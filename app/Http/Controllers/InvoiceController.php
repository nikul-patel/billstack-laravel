<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Controller for managing invoices.
 * SSR alignment: tenant-scoped invoices with validated CRUD, numbering, and PDF/email hooks per SSR specs.
 */
class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index()
    {
        $business = $this->requireBusiness();
        $invoices = Invoice::where('business_id', $business->id)->orderByDesc('invoice_date')->paginate(20);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        $business = $this->requireBusiness();
        $customers = Customer::where('business_id', $business->id)->get();
        $items = \App\Models\Item::where('business_id', $business->id)->orderBy('name')->get();
        $products = \App\Models\Product::where('business_id', $business->id)->where('is_active', true)->orderBy('name')->get();

        return view('invoices.create', compact('customers', 'items', 'products'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $business = $this->requireBusiness();
        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.item_id' => ['nullable', 'integer'],
            'items.*.name' => ['required_with:items', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.rate' => ['required_with:items', 'numeric'],
            'items.*.quantity' => ['required_with:items', 'numeric'],
            'items.*.tax_percent' => ['nullable', 'numeric'],
            'items.*.hsn_code' => ['nullable', 'string', 'max:50'],
            'items.*.sort_order' => ['nullable', 'integer'],
        ]);

        $data['business_id'] = $business->id;
        $data['currency'] = $business->currency ?? 'INR';
        $data['invoice_number'] = ($business->invoice_prefix ?? '').str_pad($business->invoice_start_no ?? 1, 4, '0', STR_PAD_LEFT);
        $data['public_hash'] = Str::random(40);
        $data['created_by'] = Auth::id();
        $invoice = Invoice::create($data);
        // Create invoice items and compute totals
        [$subtotal, $taxTotal] = $this->syncInvoiceItems($invoice, $request->input('items', []));
        $invoice->subtotal = $subtotal;
        $invoice->tax_total = $taxTotal;
        $invoice->tax_amount = $taxTotal;
        $invoice->grand_total = $subtotal + $taxTotal;
        $invoice->amount_due = $subtotal + $taxTotal;
        $invoice->save();

        // increment invoice number
        $business->invoice_start_no = ($business->invoice_start_no ?? 1) + 1;
        $business->save();

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created successfully');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $invoice->load(['business', 'customer', 'items', 'payments']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $business = $this->requireBusiness();
        $customers = Customer::where('business_id', $business->id)->get();
        $items = \App\Models\Item::where('business_id', $business->id)->orderBy('name')->get();
        $products = \App\Models\Product::where('business_id', $business->id)->where('is_active', true)->orderBy('name')->get();

        return view('invoices.create', compact('invoice', 'customers', 'items', 'products'));
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.item_id' => ['nullable', 'integer'],
            'items.*.name' => ['required_with:items', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.rate' => ['required_with:items', 'numeric'],
            'items.*.quantity' => ['required_with:items', 'numeric'],
            'items.*.tax_percent' => ['nullable', 'numeric'],
            'items.*.hsn_code' => ['nullable', 'string', 'max:50'],
            'items.*.sort_order' => ['nullable', 'integer'],
        ]);

        $invoice->update($data);
        // Update items (delete and recreate)
        $invoice->items()->delete();
        [$subtotal, $taxTotal] = $this->syncInvoiceItems($invoice, $request->input('items', []));
        $invoice->subtotal = $subtotal;
        $invoice->tax_total = $taxTotal;
        $invoice->tax_amount = $taxTotal;
        $invoice->grand_total = $subtotal + $taxTotal;
        $invoice->amount_due = max(0, $subtotal + $taxTotal - ($invoice->amount_paid ?? 0));
        $invoice->save();

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully');
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully');
    }

    /**
     * Download the invoice as a PDF using the selected template.
     */
    public function downloadPdf(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $invoice->load(['business', 'customer', 'items', 'payments']);
        $view = $this->resolveInvoiceView($invoice);
        $pdf = app('dompdf.wrapper')->loadView($view, compact('invoice'));

        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Stream the invoice PDF in-browser for preview.
     */
    public function previewPdf(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $invoice->load(['business', 'customer', 'items', 'payments']);
        $view = $this->resolveInvoiceView($invoice);
        $pdf = app('dompdf.wrapper')->loadView($view, compact('invoice'));

        return $pdf->stream("Invoice-{$invoice->invoice_number}.pdf", ['Attachment' => false]);
    }

    /**
     * Resolve the Blade view for invoice PDF rendering.
     */
    protected function resolveInvoiceView(Invoice $invoice): string
    {
        return match ($invoice->template_key) {
            'formal_black' => 'invoices.templates.formal_black',
            'classic_detailed' => 'invoices.templates.classic_detailed',
            default => 'invoices.pdf',
        };
    }

    /**
     * Send the invoice via email (simple placeholder).
     */
    public function sendEmail(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        // send email logic here
        return redirect()->back()->with('success', 'Email sent successfully');
    }

    /**
     * Sync invoice line items and return [subtotal, taxTotal].
     */
    protected function syncInvoiceItems(Invoice $invoice, array $items): array
    {
        $subtotal = 0;
        $taxTotal = 0;
        foreach ($items as $index => $item) {
            $lineTotal = ($item['rate'] ?? 0) * ($item['quantity'] ?? 1);
            $taxPercent = (float) ($item['tax_percent'] ?? 0);
            $taxAmount = round($lineTotal * $taxPercent / 100, 2);
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_id' => $item['item_id'] ?? null,
                'name' => $item['name'],
                'description' => $item['description'] ?? null,
                'rate' => $item['rate'] ?? 0,
                'quantity' => $item['quantity'] ?? 1,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'hsn_code' => $item['hsn_code'] ?? null,
                'line_total' => $lineTotal,
                'sort_order' => $item['sort_order'] ?? $index,
            ]);
            $subtotal += $lineTotal;
            $taxTotal += $taxAmount;
        }

        return [$subtotal, $taxTotal];
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
