<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

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
        $business = $this->requireBusiness();
        $this->authorizeInvoice($invoice);

        $data = $request->validate([
            'amount'    => ['required', 'numeric', 'min:0.01'],
            'paid_at'   => ['nullable', 'date'],
            'method'    => ['required', 'in:cash,upi,bank_transfer,cheque,other'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes'     => ['nullable', 'string'],
        ]);

        $data['business_id'] = $business->id;
        $data['invoice_id']  = $invoice->id;
        $data['paid_at']     = $data['paid_at'] ?? now();

        $payment = Payment::create($data);

        // Recalculate invoice amounts
        $invoice->amount_paid = (float) $invoice->amount_paid + (float) $payment->amount;
        $invoice->amount_due  = max(0, (float) $invoice->grand_total - (float) $invoice->amount_paid);
        $invoice->status      = $this->resolveInvoiceStatus($invoice);
        $invoice->save();

        return redirect()->route('invoices.show', $invoice)->with('success', 'Payment recorded successfully.');
    }

    /**
     * Remove the specified payment and adjust invoice amounts.
     */
    public function destroy(Payment $payment)
    {
        $invoice = $payment->invoice;
        $this->authorizeInvoice($invoice);

        $invoice->amount_paid = max(0, (float) $invoice->amount_paid - (float) $payment->amount);
        $invoice->amount_due  = max(0, (float) $invoice->grand_total - (float) $invoice->amount_paid);
        $invoice->status      = $this->resolveInvoiceStatus($invoice);
        $invoice->save();

        $payment->delete();

        return redirect()->route('invoices.show', $invoice)->with('success', 'Payment removed successfully.');
    }

    /**
     * Resolve the correct invoice status after a payment change.
     */
    protected function resolveInvoiceStatus(Invoice $invoice): string
    {
        if ((float) $invoice->amount_due <= 0) {
            return 'paid';
        }

        if ((float) $invoice->amount_paid > 0) {
            return 'partial';
        }

        // Keep existing status (sent, draft, overdue, etc.) if nothing paid yet
        return in_array($invoice->status, ['paid', 'partial']) ? 'sent' : ($invoice->status ?? 'sent');
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
