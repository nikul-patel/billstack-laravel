<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for recording payments against invoices.
 */
class PaymentController extends Controller
{
    /**
     * Store a newly created payment for an invoice.
     */
    public function store(Request $request, Invoice $invoice)
    {
        $business = Auth::user()->businesses->first();
        $data = $request->all();
        $data['business_id'] = $business->id;
        $data['invoice_id'] = $invoice->id;
        $data['created_by'] = Auth::id();
        $payment = Payment::create($data);
        // update invoice amounts
        $invoice->amount_paid += $payment->amount;
        $invoice->amount_due  = max(0, $invoice->grand_total - $invoice->amount_paid);
        $invoice->status      = $invoice->amount_due <= 0 ? 'paid' : 'partially_paid';
        $invoice->save();
        return redirect()->route('invoices.show', $invoice)->with('success', 'Payment recorded successfully');
    }

    /**
     * Remove the specified payment (and adjust invoice amounts).
     */
    public function destroy(Payment $payment)
    {
        $invoice = $payment->invoice;
        $invoice->amount_paid -= $payment->amount;
        $invoice->amount_due  = max(0, $invoice->grand_total - $invoice->amount_paid);
        $invoice->status      = $invoice->amount_due <= 0 ? 'paid' : 'partially_paid';
        $invoice->save();
        $payment->delete();
        return redirect()->route('invoices.show', $invoice)->with('success', 'Payment deleted successfully');
    }
}
