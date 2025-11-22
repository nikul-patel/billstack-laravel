<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Controller for managing invoices.
 */
class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index()
    {
        $business = Auth::user()->businesses->first();
        $invoices = Invoice::where('business_id', $business->id)->orderByDesc('invoice_date')->paginate(20);
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        $business = Auth::user()->businesses->first();
        $customers = Customer::where('business_id', $business->id)->get();
        return view('invoices.create', compact('customers'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $business = Auth::user()->businesses->first();
        $data = $request->all();
        $data['business_id'] = $business->id;
        $data['invoice_number'] = $business->default_invoice_prefix . str_pad($business->next_invoice_number, 4, '0', STR_PAD_LEFT);
        $data['public_hash'] = Str::random(40);
        $data['created_by'] = Auth::id();
        $invoice = Invoice::create($data);
        // Create invoice items if provided
        $subtotal = 0;
        if ($request->has('items')) {
            foreach ($request->input('items') as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_id'    => $item['item_id'] ?? null,
                    'name'       => $item['name'],
                    'description'=> $item['description'] ?? null,
                    'rate'       => $item['rate'],
                    'quantity'   => $item['quantity'],
                    'tax_percent'=> $item['tax_percent'] ?? null,
                    'tax_amount' => 0,
                    'line_total' => $item['rate'] * $item['quantity'],
                    'sort_order' => $item['sort_order'] ?? 0,
                ]);
                $subtotal += $item['rate'] * $item['quantity'];
            }
        }
        // update totals
        $invoice->subtotal    = $subtotal;
        $invoice->grand_total = $subtotal;
        $invoice->amount_due  = $subtotal;
        $invoice->save();
        // increment invoice number
        $business->next_invoice_number += 1;
        $business->save();
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created successfully');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        $business = Auth::user()->businesses->first();
        $customers = Customer::where('business_id', $business->id)->get();
        return view('invoices.edit', compact('invoice', 'customers'));
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $invoice->update($request->all());
        // update items (simple: delete and recreate)
        $invoice->items()->delete();
        $subtotal = 0;
        if ($request->has('items')) {
            foreach ($request->input('items') as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_id'    => $item['item_id'] ?? null,
                    'name'       => $item['name'],
                    'description'=> $item['description'] ?? null,
                    'rate'       => $item['rate'],
                    'quantity'   => $item['quantity'],
                    'tax_percent'=> $item['tax_percent'] ?? null,
                    'tax_amount' => 0,
                    'line_total' => $item['rate'] * $item['quantity'],
                    'sort_order' => $item['sort_order'] ?? 0,
                ]);
                $subtotal += $item['rate'] * $item['quantity'];
            }
        }
        $invoice->subtotal    = $subtotal;
        $invoice->grand_total = $subtotal;
        $invoice->amount_due  = $subtotal - $invoice->amount_paid;
        $invoice->save();
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully');
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully');
    }

    /**
     * Download the invoice as a PDF using the selected template.
     */
    public function downloadPdf(Invoice $invoice)
    {
        $template = $invoice->template_key ?? $invoice->business->default_template_key ?? 'classic_detailed';
        $view = match ($template) {
            'classic_detailed' => 'invoices.templates.classic_detailed',
            default           => 'invoices.templates.classic_detailed',
        };
        $pdf = app('dompdf.wrapper')->loadView($view, compact('invoice'));
        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Send the invoice via email (simple placeholder).
     */
    public function sendEmail(Invoice $invoice)
    {
        // send email logic here
        return redirect()->back()->with('success', 'Email sent successfully');
    }
}
