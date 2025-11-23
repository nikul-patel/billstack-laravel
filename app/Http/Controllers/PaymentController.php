<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for recording payments against invoices.
 * SSR alignment: tenant-scoped payments adjusting invoice balances per SSR requirements.
 */
class PaymentController extends Controller
{
    /**
     * Store a newly created payment for an invoice.
     */
    public function store(Request $request, Invoice $invoice)
    {
        $business = Auth::user()->business;
        $this->authorizeInvoice($invoice, $business->id);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_at' => ['nullable', 'date'],
            'method' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);
        $data['business_id'] = $business->id;
        $data['invoice_id'] = $invoice->id;
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
        $this->authorizeInvoice($payment->invoice, $payment->business_id);
        $invoice = $payment->invoice;
        $invoice->amount_paid -= $payment->amount;
        $invoice->amount_due  = max(0, $invoice->grand_total - $invoice->amount_paid);
        $invoice->status      = $invoice->amount_due <= 0 ? 'paid' : 'partially_paid';
        $invoice->save();
        $payment->delete();
        return redirect()->route('invoices.show', $invoice)->with('success', 'Payment deleted successfully');
    }

    protected function authorizeInvoice(Invoice $invoice, int $businessId): void
    {
        if ($invoice->business_id !== $businessId) {
            abort(403);
        }
    }
}
