<?php

namespace App\Http\Controllers;

use App\Http\Requests\SwitchBusinessRequest;
use App\Models\Business;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Handles business settings and dashboard.
 * SSR alignment: enforces single-tenant context (business_id) and editable business preferences per SSR docs.
 */
class BusinessController extends Controller
{
    /**
     * Show the dashboard with live KPI data.
     */
    public function dashboard()
    {
        $business = $this->currentBusiness();

        if (! $business) {
            return view('dashboard', compact('business'));
        }

        $businessId = $business->id;
        $now        = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth   = $now->copy()->endOfMonth();

        // Total revenue this month (sum of grand_total for paid invoices)
        $totalRevenueThisMonth = Invoice::where('business_id', $businessId)
            ->where('status', 'paid')
            ->whereBetween('invoice_date', [$startOfMonth, $endOfMonth])
            ->sum('grand_total');

        // Outstanding amount (unpaid/partial invoice balances)
        // Include 'partially_paid' for backward compatibility with invoices created before the
        // status was normalised to 'partial'.
        $outstandingAmount = Invoice::where('business_id', $businessId)
            ->whereIn('status', ['sent', 'partial', 'partially_paid', 'draft'])
            ->sum('amount_due');

        // Overdue invoices (past due_date, not paid/cancelled)
        $overdueCount = Invoice::where('business_id', $businessId)
            ->whereNotNull('due_date')
            ->where('due_date', '<', $now->toDateString())
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->count();

        // Recent 5 invoices with customer name and status
        $recentInvoices = Invoice::with('customer')
            ->where('business_id', $businessId)
            ->orderByDesc('invoice_date')
            ->limit(5)
            ->get();

        // Monthly revenue for last 6 months
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month      = $now->copy()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd   = $month->copy()->endOfMonth();
            $revenue    = Invoice::where('business_id', $businessId)
                ->where('status', 'paid')
                ->whereBetween('invoice_date', [$monthStart, $monthEnd])
                ->sum('grand_total');
            $monthlyRevenue[] = [
                'month'   => $month->format('M Y'),
                'revenue' => (float) $revenue,
            ];
        }

        return view('dashboard', compact(
            'business',
            'totalRevenueThisMonth',
            'outstandingAmount',
            'overdueCount',
            'recentInvoices',
            'monthlyRevenue'
        ));
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
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['nullable', 'email', 'max:255'],
            'phone'            => ['nullable', 'string', 'max:50'],
            'owner_name'       => ['nullable', 'string', 'max:255'],
            'gst_number'       => ['nullable', 'string', 'max:50'],
            'gstin'            => ['nullable', 'string', 'max:20'],
            'pan'              => ['nullable', 'string', 'max:20'],
            'default_tax_type' => ['nullable', 'in:none,gst,vat'],
            'default_gst_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'address'          => ['nullable', 'string'],
            'address_line_2'   => ['nullable', 'string', 'max:255'],
            'city'             => ['nullable', 'string', 'max:255'],
            'state'            => ['nullable', 'string', 'max:255'],
            'country'          => ['nullable', 'string', 'max:255'],
            'pincode'          => ['nullable', 'string', 'max:20'],
            'invoice_prefix'   => ['nullable', 'string', 'max:50'],
            'invoice_start_no' => ['nullable', 'integer', 'min:1'],
            'currency'         => ['nullable', 'string', 'max:10'],
            'date_format'      => ['nullable', 'string', 'max:20'],
            'timezone'         => ['nullable', 'string', 'max:100'],
            'terms'            => ['nullable', 'string'],
            'notes'            => ['nullable', 'string'],
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
