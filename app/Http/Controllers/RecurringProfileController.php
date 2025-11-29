<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\RecurringProfile;
use App\Models\RecurringProfileItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Controller for managing recurring billing profiles.
 * SSR alignment: tenant-scoped recurring billing, validated inputs, and invoice generation hooks.
 */
class RecurringProfileController extends Controller
{
    /**
     * Display a listing of recurring profiles.
     */
    public function index()
    {
        $business = $this->requireBusiness();
        $profiles = RecurringProfile::where('business_id', $business->id)->paginate(20);

        return view('recurring-profiles.index', compact('profiles'));
    }

    /**
     * Show the form for creating a new recurring profile.
     */
    public function create()
    {
        return view('recurring-profiles.create');
    }

    /**
     * Store a newly created recurring profile in storage.
     */
    public function store(Request $request)
    {
        $business = $this->requireBusiness();

        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'name' => ['nullable', 'string', 'max:255'],
            'frequency' => ['required', 'string', 'max:50'],
            'next_run_date' => ['nullable', 'date'],
            'day_of_month' => ['nullable', 'integer', 'between:1,31'],
            'amount' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
        ]);
        $data['business_id'] = $business->id;
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

        return view('recurring-profiles.edit', ['profile' => $recurring_profile]);
    }

    /**
     * Update the specified recurring profile in storage.
     */
    public function update(Request $request, RecurringProfile $recurring_profile)
    {
        $this->authorizeProfile($recurring_profile);

        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'name' => ['nullable', 'string', 'max:255'],
            'frequency' => ['required', 'string', 'max:50'],
            'next_run_date' => ['nullable', 'date'],
            'day_of_month' => ['nullable', 'integer', 'between:1,31'],
            'amount' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
        ]);

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
     * Generate invoices from a recurring profile.
     */
    public function generateInvoices(RecurringProfile $profile, Request $request)
    {
        $this->authorizeProfile($profile);
        $business = $this->requireBusiness();
        // Example: create a single invoice for this profile's customer
        $invoice = Invoice::create([
            'business_id' => $business->id,
            'customer_id' => $profile->customer_id,
            'invoice_number' => ($business->invoice_prefix ?? '').str_pad($business->invoice_start_no ?? 1, 4, '0', STR_PAD_LEFT),
            'invoice_date' => now(),
            'due_date' => now()->addDays(7),
            'status' => 'draft',
            'currency' => $business->currency ?? 'INR',
            'subtotal' => 0,
            'discount_type' => 'none',
            'discount_value' => 0,
            'tax_total' => 0,
            'grand_total' => 0,
            'amount_paid' => 0,
            'amount_due' => 0,
            'public_hash' => Str::random(40),
            'created_by' => Auth::id(),
        ]);
        $lineTotal = 0;
        foreach ($profile->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_id' => $item->item_id,
                'name' => $item->name,
                'description' => $item->description,
                'rate' => $item->rate,
                'quantity' => $item->quantity,
                'tax_percent' => $item->tax_percent,
                'tax_amount' => 0,
                'line_total' => $item->rate * $item->quantity,
                'sort_order' => $item->sort_order,
            ]);
            $lineTotal += $item->rate * $item->quantity;
        }
        // update totals
        $invoice->subtotal = $lineTotal;
        $invoice->grand_total = $lineTotal;
        $invoice->amount_due = $lineTotal;
        $invoice->save();

        // increment business invoice numbering
        $business->invoice_start_no = ($business->invoice_start_no ?? 1) + 1;
        $business->save();

        return redirect()->route('invoices.edit', $invoice)->with('success', 'Invoice generated from recurring profile');
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
}
