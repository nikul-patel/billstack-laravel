<?php

namespace App\Http\Controllers;

use App\Http\Requests\SwitchBusinessRequest;
use App\Models\Business;
use App\Models\Invoice;
use Illuminate\Http\Request;

/**
 * Handles business settings and dashboard.
 * SSR alignment: enforces single-tenant context (business_id) and editable business preferences per SSR docs.
 */
class BusinessController extends Controller
{
    /**
     * Show a basic dashboard for the current business.
     */
    public function dashboard()
    {
        $business = $this->currentBusiness();

        return view('dashboard', compact('business'));
    }

    /**
     * Show the form for editing the business profile.
     */
    public function edit()
    {
        $business = $this->requireBusiness();

        return view('business.edit', compact('business'));
    }

    /**
     * Update the business profile.
     */
    public function update(Request $request)
    {
        $business = $this->requireBusiness();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'gst_number' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'invoice_prefix' => ['nullable', 'string', 'max:50'],
            'invoice_start_no' => ['nullable', 'integer', 'min:1'],
            'currency' => ['nullable', 'string', 'max:10'],
            'date_format' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'terms' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldPrefix = $business->invoice_prefix;
        $business->update($validated);
        $this->refreshInvoicePrefixes($business, $oldPrefix);

        return redirect()->route('business.profile.edit')->with('success', 'Profile updated successfully');
    }

    /**
     * Switch the active business context for a super admin.
     */
    public function switch(SwitchBusinessRequest $request)
    {
        $businessId = $request->validated()['business_id'];
        $business = Business::find($businessId);

        session(['active_business_id' => $businessId]);

        return redirect()->back()->with('success', 'Switched to '.$business?->name.' successfully');
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
