<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

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
        $business = $this->requireBusiness();
        $this->authorizeInvoice($invoice);

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
        $invoice->amount_due = max(0, $invoice->grand_total - $invoice->amount_paid);
        $invoice->status = $invoice->amount_due <= 0 ? 'paid' : 'partially_paid';
        $invoice->save();

        return redirect()->route('invoices.show', $invoice)->with('success', 'Payment recorded successfully');
    }

    /**
     * Remove the specified payment (and adjust invoice amounts).
     */
    public function destroy(Payment $payment)
    {
        $this->authorizeInvoice($payment->invoice);
        $invoice = $payment->invoice;
        $invoice->amount_paid -= $payment->amount;
        $invoice->amount_due = max(0, $invoice->grand_total - $invoice->amount_paid);
        $invoice->status = $invoice->amount_due <= 0 ? 'paid' : 'partially_paid';
        $invoice->save();
        $payment->delete();

        return redirect()->route('invoices.show', $invoice)->with('success', 'Payment deleted successfully');
    }

    protected function authorizeInvoice(Invoice $invoice): void
    {
        if ($this->userIsSuperAdmin()) {
            return;
        }

        $business = $this->currentBusiness();

        if ($invoice->business_id !== $business?->id) {
            abort(403);
        }
    }
}
