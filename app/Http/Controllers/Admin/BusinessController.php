<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminBusinessRequest;
use App\Models\Business;
use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class BusinessController extends Controller
{
    public function index(): View
    {
        $this->authorizeSuperAdmin();
        $businesses = Business::query()->orderBy('name')->paginate(20);

        return view('admin.businesses.index', compact('businesses'));
    }

    public function create(): View
    {
        $this->authorizeSuperAdmin();

        return view('admin.businesses.create');
    }

    public function store(AdminBusinessRequest $request): RedirectResponse
    {
        $this->authorizeSuperAdmin();
        $business = Business::create($request->validated());

        return redirect()->route('admin.businesses.index')->with('success', 'Business created successfully');
    }

    public function edit(Business $business): View
    {
        $this->authorizeSuperAdmin();

        return view('admin.businesses.edit', compact('business'));
    }

    public function update(AdminBusinessRequest $request, Business $business): RedirectResponse
    {
        $this->authorizeSuperAdmin();
        $oldPrefix = $business->invoice_prefix;
        $business->update($request->validated());
        $this->refreshInvoicePrefixes($business, $oldPrefix);

        return redirect()->route('admin.businesses.index')->with('success', 'Business updated successfully');
    }

    public function destroy(Business $business): RedirectResponse
    {
        $this->authorizeSuperAdmin();
        $business->delete();

        return redirect()->route('admin.businesses.index')->with('success', 'Business deleted successfully');
    }

    protected function authorizeSuperAdmin(): void
    {
        if (! $this->userIsSuperAdmin()) {
            abort(403);
        }
    }

    protected function refreshInvoicePrefixes(Business $business, ?string $oldPrefix): void
    {
        if ($oldPrefix === $business->invoice_prefix) {
            return;
        }

        $newPrefix = $business->invoice_prefix ?? '';
        Invoice::where('business_id', $business->id)->chunkById(200, function ($invoices) use ($oldPrefix, $newPrefix) {
            foreach ($invoices as $invoice) {
                $number = $invoice->invoice_number ?? '';
                if ($oldPrefix && str_starts_with($number, $oldPrefix)) {
                    $number = $newPrefix.$this->stripPrefix($number, $oldPrefix);
                } elseif (! $oldPrefix && $newPrefix && ! str_starts_with($number, $newPrefix)) {
                    $number = $newPrefix.$number;
                }
                $invoice->invoice_number = $number;
                $invoice->save();
            }
        });
    }

    protected function stripPrefix(string $value, string $prefix): string
    {
        return str_starts_with($value, $prefix) ? substr($value, strlen($prefix)) : $value;
    }
}
