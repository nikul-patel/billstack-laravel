<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\RecurringProfile;
use App\Models\RecurringProfileItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Controller for managing recurring billing profiles.
 * SSR alignment: tenant-scoped recurring billing, validated inputs, and invoice generation hooks.
 */
class RecurringProfileController extends Controller
{
    /**
     * Display a listing of recurring profiles.
     */
    public function index(Request $request)
    {
        $business = $this->requireBusiness();
        $search = trim((string) $request->input('search', ''));

        $profiles = RecurringProfile::with('customer')
            ->where('business_id', $business->id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('next_run_date')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('recurring-profiles.index', [
            'profiles' => $profiles,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new recurring profile.
     */
    public function create()
    {
        $business = $this->requireBusiness();

        return view('recurring-profiles.create', [
            'profile' => new RecurringProfile([
                'frequency' => 'monthly',
                'next_run_date' => now()->toDateString(),
                'day_of_month' => now()->day,
            ]),
            'customers' => $this->customersForBusiness($business->id),
            'frequencies' => $this->frequencyOptions(),
            'isEdit' => false,
        ]);
    }

    /**
     * Store a newly created recurring profile in storage.
     */
    public function store(Request $request)
    {
        $business = $this->requireBusiness();

        $data = $request->validate([
            'customer_id' => [
                'required',
                Rule::exists('customers', 'id')->where('business_id', $business->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'frequency' => ['required', Rule::in(array_keys($this->frequencyOptions()))],
            'next_run_date' => ['nullable', 'date'],
            'day_of_month' => ['nullable', 'integer', 'between:1,31'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);
        $data['business_id'] = $business->id;
        $data['next_run_date'] = $data['next_run_date'] ?? now()->toDateString();
        $data['amount'] = $data['amount'] ?? 0;
        $profile = RecurringProfile::create($data);
        // Items creation (if present)
        if ($request->has('items')) {
            foreach ($request->input('items') as $item) {
                RecurringProfileItem::create([
                    'recurring_profile_id' => $profile->id,
                    'name' => $item['name'] ?? null,
                    'description' => $item['description'] ?? null,
                    'rate' => $item['rate'] ?? 0,
                    'quantity' => $item['quantity'] ?? 1,
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'sort_order' => $item['sort_order'] ?? 0,
                ]);
            }
        }

        return redirect()->route('recurring-profiles.index')->with('success', 'Recurring profile created successfully');
    }

    /**
     * Show the form for editing the specified recurring profile.
     */
    public function edit(RecurringProfile $recurring_profile)
    {
        $this->authorizeProfile($recurring_profile);
        $business = $this->requireBusiness();

        return view('recurring-profiles.create', [
            'profile' => $recurring_profile,
            'customers' => $this->customersForBusiness($business->id),
            'frequencies' => $this->frequencyOptions(),
            'isEdit' => true,
        ]);
    }

    /**
     * Update the specified recurring profile in storage.
     */
    public function update(Request $request, RecurringProfile $recurring_profile)
    {
        $this->authorizeProfile($recurring_profile);

        $business = $this->requireBusiness();

        $data = $request->validate([
            'customer_id' => [
                'required',
                Rule::exists('customers', 'id')->where('business_id', $business->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'frequency' => ['required', Rule::in(array_keys($this->frequencyOptions()))],
            'next_run_date' => ['nullable', 'date'],
            'day_of_month' => ['nullable', 'integer', 'between:1,31'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['next_run_date'] = $data['next_run_date'] ?? now()->toDateString();
        $data['amount'] = $data['amount'] ?? 0;

        $recurring_profile->update($data);
        // Update items (simplified: delete and recreate)
        $recurring_profile->items()->delete();
        if ($request->has('items')) {
            foreach ($request->input('items') as $item) {
                RecurringProfileItem::create([
                    'recurring_profile_id' => $recurring_profile->id,
                    'name' => $item['name'] ?? null,
                    'description' => $item['description'] ?? null,
                    'rate' => $item['rate'] ?? 0,
                    'quantity' => $item['quantity'] ?? 1,
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'sort_order' => $item['sort_order'] ?? 0,
                ]);
            }
        }

        return redirect()->route('recurring-profiles.index')->with('success', 'Recurring profile updated successfully');
    }

    /**
     * Remove the specified recurring profile from storage.
     */
    public function destroy(RecurringProfile $recurring_profile)
    {
        $this->authorizeProfile($recurring_profile);
        $recurring_profile->delete();

        return redirect()->route('recurring-profiles.index')->with('success', 'Recurring profile deleted successfully');
    }

    /**
     * Allow the user to pick items prior to generating an invoice.
     */
    public function prepare(RecurringProfile $profile)
    {
        $this->authorizeProfile($profile);
        $business = $this->requireBusiness();

        return view('recurring-profiles.prepare', [
            'profile' => $profile->load('customer'),
            'items' => $this->itemsForBusiness($business->id),
        ]);
    }

    /**
     * Generate invoices from a recurring profile.
     */
    public function generateInvoices(Request $request, RecurringProfile $profile)
    {
        $this->authorizeProfile($profile);
        $business = $this->requireBusiness();

        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.item_id' => [
                'required',
                Rule::exists('items', 'id')->where('business_id', $business->id),
            ],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.rate' => ['nullable', 'numeric', 'min:0'],
        ], [
            'items.required' => 'Select at least one item to build the invoice.',
        ]);

        $itemsCollection = $this->itemsForBusiness($business->id)->keyBy('id');
        $selectedItems = collect($validated['items'])
            ->filter(function ($item) {
                return (float) ($item['quantity'] ?? 0) > 0;
            })
            ->map(function ($item, $index) use ($itemsCollection) {
                $catalogItem = $itemsCollection->get((int) $item['item_id']);

                $rate = $item['rate'] ?? $catalogItem?->price ?? 0;

                return [
                    'item_id' => $catalogItem?->id,
                    'name' => $catalogItem?->name,
                    'description' => $catalogItem?->description,
                    'rate' => (float) $rate,
                    'quantity' => (float) $item['quantity'],
                    'tax_percent' => $catalogItem?->tax_rate ?? 0,
                    'sort_order' => $index,
                ];
            })
            ->filter(function ($line) {
                return $line['item_id'] !== null;
            })
            ->values();

        if ($selectedItems->isEmpty()) {
            return back()
                ->withErrors(['items' => 'Select at least one catalog item with a quantity greater than zero.'])
                ->withInput();
        }

        try {
            $invoice = $profile->generateInvoice(Auth::id(), $selectedItems->all());
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withErrors(['items' => 'Unable to draft invoice: '.$exception->getMessage()])
                ->withInput();
        }

        $profile->advanceToNextRun();

        return redirect()
            ->route('invoices.edit', $invoice)
            ->with('success', 'Invoice generated from recurring profile');
    }

    protected function authorizeProfile(RecurringProfile $profile): void
    {
        if ($this->userIsSuperAdmin()) {
            return;
        }

        $business = $this->currentBusiness();

        if ($profile->business_id !== $business?->id) {
            abort(403);
        }
    }

    private function customersForBusiness(int $businessId)
    {
        return Customer::select('id', 'name')
            ->where('business_id', $businessId)
            ->orderBy('name')
            ->get();
    }

    private function frequencyOptions(): array
    {
        return [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'biweekly' => 'Every 2 Weeks',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
        ];
    }

    private function itemsForBusiness(int $businessId)
    {
        return Item::select('id', 'name', 'price', 'tax_rate', 'description')
            ->where('business_id', $businessId)
            ->orderBy('name')
            ->get();
    }
}
