<?php

namespace App\Http\Controllers;

use App\Models\RecurringProfile;
use App\Models\RecurringProfileItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Controller for managing recurring billing profiles.
 */
class RecurringProfileController extends Controller
{
    /**
     * Display a listing of recurring profiles.
     */
    public function index()
    {
        $business = Auth::user()->businesses->first();
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
        $business = Auth::user()->businesses->first();
        $data = $request->all();
        $data['business_id'] = $business->id;
        $profile = RecurringProfile::create($data);
        // Items creation (if present)
        if ($request->has('items')) {
            foreach ($request->input('items') as $item) {
                $item['recurring_profile_id'] = $profile->id;
                RecurringProfileItem::create($item);
            }
        }
        return redirect()->route('recurring-profiles.index')->with('success', 'Recurring profile created successfully');
    }

    /**
     * Show the form for editing the specified recurring profile.
     */
    public function edit(RecurringProfile $recurring_profile)
    {
        return view('recurring-profiles.edit', ['profile' => $recurring_profile]);
    }

    /**
     * Update the specified recurring profile in storage.
     */
    public function update(Request $request, RecurringProfile $recurring_profile)
    {
        $recurring_profile->update($request->all());
        // Update items (simplified: delete and recreate)
        $recurring_profile->items()->delete();
        if ($request->has('items')) {
            foreach ($request->input('items') as $item) {
                $item['recurring_profile_id'] = $recurring_profile->id;
                RecurringProfileItem::create($item);
            }
        }
        return redirect()->route('recurring-profiles.index')->with('success', 'Recurring profile updated successfully');
    }

    /**
     * Remove the specified recurring profile from storage.
     */
    public function destroy(RecurringProfile $recurring_profile)
    {
        $recurring_profile->delete();
        return redirect()->route('recurring-profiles.index')->with('success', 'Recurring profile deleted successfully');
    }

    /**
     * Generate invoices from a recurring profile.
     */
    public function generateInvoices(RecurringProfile $profile, Request $request)
    {
        $business = Auth::user()->businesses->first();
        // Example: create a single invoice for this profile's customer
        $invoice = Invoice::create([
            'business_id'    => $business->id,
            'customer_id'    => $profile->customer_id,
            'invoice_number' => $business->default_invoice_prefix . str_pad($business->next_invoice_number, 4, '0', STR_PAD_LEFT),
            'invoice_date'   => now(),
            'due_date'       => now()->addDays($business->default_due_days),
            'status'         => 'draft',
            'currency'       => $business->default_currency,
            'subtotal'       => 0,
            'discount_type'  => 'none',
            'discount_value' => 0,
            'tax_total'      => 0,
            'grand_total'    => 0,
            'amount_paid'    => 0,
            'amount_due'     => 0,
            'public_hash'    => Str::random(40),
            'created_by'     => Auth::id(),
        ]);
        $lineTotal = 0;
        foreach ($profile->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_id'    => $item->item_id,
                'name'       => $item->name,
                'description'=> $item->description,
                'rate'       => $item->rate,
                'quantity'   => $item->quantity,
                'tax_percent'=> $item->tax_percent,
                'tax_amount' => 0,
                'line_total' => $item->rate * $item->quantity,
                'sort_order' => $item->sort_order,
            ]);
            $lineTotal += $item->rate * $item->quantity;
        }
        // update totals
        $invoice->subtotal  = $lineTotal;
        $invoice->grand_total = $lineTotal;
        $invoice->amount_due  = $lineTotal;
        $invoice->save();
        // increment business invoice number
        $business->next_invoice_number += 1;
        $business->save();
        return redirect()->route('invoices.edit', $invoice)->with('success', 'Invoice generated from recurring profile');
    }
}
