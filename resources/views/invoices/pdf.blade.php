@php
    $business = $invoice->business;
    $customer = $invoice->customer;
    $currency = $invoice->currency ?? $business->currency ?? 'INR';

    // GST breakup
    $taxAmount   = $invoice->tax_amount ?? $invoice->tax_total ?? 0;
    $businessGstin = $business->gstin ?? $business->gst_number ?? null;
    $customerGstin = $customer->gstin ?? null;

    // Determine intra-state (CGST + SGST) vs inter-state (IGST) based on GSTIN state codes
    $isInterState = false;
    if ($businessGstin && $customerGstin) {
        $isInterState = substr($businessGstin, 0, 2) !== substr($customerGstin, 0, 2);
    }
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page { margin: 18mm; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #1a1a2e;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .page { width: 100%; }

        /* Header */
        .header { width: 100%; margin-bottom: 20px; }
        .header-left { display: inline-block; width: 58%; vertical-align: top; }
        .header-right { display: inline-block; width: 40%; vertical-align: top; text-align: right; }
        .business-name { font-size: 22px; font-weight: 700; color: #1a237e; margin: 0 0 4px; }
        .business-meta { font-size: 10px; color: #555; line-height: 1.5; }
        .invoice-badge {
            display: inline-block;
            background: #1a237e;
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            padding: 6px 18px;
            border-radius: 4px;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .invoice-meta { font-size: 11px; color: #333; line-height: 1.8; }
        .invoice-meta td:first-child { color: #777; padding-right: 10px; }

        /* Divider */
        .divider { border: none; border-top: 2px solid #1a237e; margin: 14px 0; }

        /* Parties */
        .parties { width: 100%; margin-bottom: 18px; }
        .party-box { display: inline-block; width: 48%; vertical-align: top; }
        .party-box-right { display: inline-block; width: 48%; vertical-align: top; text-align: right; }
        .party-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #1a237e; font-weight: 700; margin-bottom: 4px; }
        .party-name { font-size: 14px; font-weight: 700; margin: 0 0 3px; }
        .party-detail { font-size: 10px; color: #555; line-height: 1.5; }
        .gstin-badge { display: inline-block; background: #e8eaf6; color: #1a237e; font-size: 9px; font-weight: 700; padding: 2px 6px; border-radius: 2px; margin-top: 4px; }

        /* Line items */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .items-table th {
            background: #1a237e;
            color: #fff;
            padding: 7px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .items-table th.text-right { text-align: right; }
        .items-table td { padding: 7px 8px; border-bottom: 1px solid #e8eaf6; font-size: 11px; vertical-align: top; }
        .items-table td.text-right { text-align: right; }
        .items-table tbody tr:nth-child(even) td { background: #f5f5ff; }
        .item-name { font-weight: 600; }
        .item-desc { font-size: 9px; color: #777; margin-top: 2px; }
        .item-hsn { font-size: 9px; color: #1a237e; }

        /* Totals */
        .totals-wrap { width: 100%; }
        .totals-left { display: inline-block; width: 55%; vertical-align: top; }
        .totals-right { display: inline-block; width: 43%; vertical-align: top; }
        .totals-table { width: 100%; }
        .totals-table td { padding: 4px 6px; font-size: 11px; }
        .totals-table td:last-child { text-align: right; font-weight: 600; }
        .totals-table .subtotal-row td { color: #444; }
        .totals-table .divider-row td { border-top: 1px solid #c5cae9; padding-top: 6px; }
        .totals-table .grand-row td { font-size: 14px; font-weight: 700; color: #1a237e; padding-top: 6px; }
        .totals-table .paid-row td { color: #388e3c; }
        .totals-table .due-row td { font-size: 13px; font-weight: 700; color: #c62828; }
        .totals-table .gst-label { font-size: 10px; color: #777; padding-left: 12px; }

        /* Notes */
        .notes-section { margin-top: 18px; padding: 10px; background: #f5f5ff; border-radius: 4px; font-size: 10px; color: #555; }
        .notes-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #1a237e; font-weight: 700; margin-bottom: 4px; }

        /* Footer */
        .footer { margin-top: 20px; border-top: 1px solid #e8eaf6; padding-top: 8px; text-align: center; font-size: 9px; color: #999; }
        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-paid { background: #e8f5e9; color: #2e7d32; }
        .status-draft { background: #eeeeee; color: #616161; }
        .status-overdue { background: #ffebee; color: #c62828; }
        .status-partial { background: #fff3e0; color: #e65100; }
        .status-sent { background: #e3f2fd; color: #1565c0; }
    </style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <div class="business-name">{{ $business->name }}</div>
            <div class="business-meta">
                @if($business->address){{ $business->address }}@if($business->city || $business->state), @endif@endif
                @if($business->city){{ $business->city }}@if($business->state), @endif@endif
                @if($business->state){{ $business->state }}@if($business->pincode) - {{ $business->pincode }}@endif@endif
                @if($business->country)<br>{{ $business->country }}@endif
                @if($business->phone)<br>Ph: {{ $business->phone }}@endif
                @if($business->email)<br>{{ $business->email }}@endif
                @if($businessGstin)<br><strong>GSTIN:</strong> {{ $businessGstin }}@endif
                @if($business->pan ?? null)<br><strong>PAN:</strong> {{ $business->pan }}@endif
            </div>
        </div>
        <div class="header-right">
            <div class="invoice-badge">INVOICE</div>
            <table class="invoice-meta" style="margin-left:auto;">
                <tr>
                    <td>Invoice #</td>
                    <td><strong>{{ $invoice->invoice_number }}</strong></td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td>{{ $invoice->invoice_date?->format('d M Y') }}</td>
                </tr>
                @if($invoice->due_date)
                <tr>
                    <td>Due Date</td>
                    <td>{{ $invoice->due_date->format('d M Y') }}</td>
                </tr>
                @endif
                <tr>
                    <td>Status</td>
                    <td>
                        <span class="status-badge status-{{ $invoice->status ?? 'draft' }}">
                            {{ ucfirst($invoice->status ?? 'draft') }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <hr class="divider">

    {{-- Parties --}}
    <div class="parties">
        <div class="party-box">
            <div class="party-label">From</div>
            <div class="party-name">{{ $business->owner_name ?? $business->name }}</div>
            <div class="party-detail">
                @if($businessGstin)<span class="gstin-badge">GSTIN: {{ $businessGstin }}</span>@endif
            </div>
        </div>
        <div class="party-box-right">
            <div class="party-label">Bill To</div>
            <div class="party-name">{{ $customer->name }}</div>
            <div class="party-detail">
                @if($customer->billing_address_line_1){{ $customer->billing_address_line_1 }}<br>@endif
                @if($customer->billing_address_line_2){{ $customer->billing_address_line_2 }}<br>@endif
                @if($customer->city){{ $customer->city }}@if($customer->state), @endif@endif
                @if($customer->state){{ $customer->state }}@if($customer->pincode) - {{ $customer->pincode }}@endif@endif
                @if($customer->email)<br>{{ $customer->email }}@endif
                @if($customer->phone)<br>{{ $customer->phone }}@endif
                @if($customerGstin)<br><span class="gstin-badge">GSTIN: {{ $customerGstin }}</span>@endif
            </div>
        </div>
    </div>

    {{-- Line Items --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:35%">Item / Description</th>
                @if($invoice->items->whereNotNull('hsn_code')->isNotEmpty())
                <th style="width:10%">HSN</th>
                @endif
                <th class="text-right" style="width:10%">Rate</th>
                <th class="text-right" style="width:8%">Qty</th>
                @if($invoice->items->where('tax_percent', '>', 0)->isNotEmpty())
                <th class="text-right" style="width:10%">Tax %</th>
                <th class="text-right" style="width:10%">Tax Amt</th>
                @endif
                <th class="text-right" style="width:12%">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $hasHsn = $invoice->items->whereNotNull('hsn_code')->isNotEmpty(); @endphp
            @php $hasTax = $invoice->items->where('tax_percent', '>', 0)->isNotEmpty(); @endphp
            @foreach($invoice->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <div class="item-name">{{ $item->name }}</div>
                    @if($item->description)
                        <div class="item-desc">{{ $item->description }}</div>
                    @endif
                </td>
                @if($hasHsn)
                <td><span class="item-hsn">{{ $item->hsn_code ?? '—' }}</span></td>
                @endif
                <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                @if($hasTax)
                <td class="text-right">{{ number_format($item->tax_percent ?? 0, 2) }}%</td>
                <td class="text-right">{{ number_format($item->tax_amount ?? 0, 2) }}</td>
                @endif
                <td class="text-right">{{ number_format($item->line_total, 2) }}</td>
            </tr>
            @endforeach
            @if($invoice->items->isEmpty())
            <tr>
                <td colspan="7" style="text-align:center; color:#999; padding: 20px;">No line items on this invoice.</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- Totals + Notes --}}
    <div class="totals-wrap">
        <div class="totals-left">
            @if($invoice->notes)
            <div class="notes-section">
                <div class="notes-label">Notes</div>
                {{ $invoice->notes }}
            </div>
            @endif
            @if($invoice->terms)
            <div class="notes-section" style="margin-top:8px;">
                <div class="notes-label">Terms &amp; Conditions</div>
                {{ $invoice->terms }}
            </div>
            @endif
        </div>
        <div class="totals-right">
            <table class="totals-table">
                <tr class="subtotal-row">
                    <td>Subtotal</td>
                    <td>{{ $currency }} {{ number_format($invoice->subtotal ?? 0, 2) }}</td>
                </tr>
                @if(($invoice->discount_value ?? 0) > 0)
                <tr class="subtotal-row">
                    <td>Discount</td>
                    <td>- {{ $currency }} {{ number_format($invoice->discount_value, 2) }}</td>
                </tr>
                @endif

                {{-- GST Breakup --}}
                @if($taxAmount > 0)
                    @if($isInterState)
                    <tr class="subtotal-row">
                        <td class="gst-label">IGST</td>
                        <td>{{ $currency }} {{ number_format($taxAmount, 2) }}</td>
                    </tr>
                    @else
                    <tr class="subtotal-row">
                        <td class="gst-label">CGST</td>
                        <td>{{ $currency }} {{ number_format($taxAmount / 2, 2) }}</td>
                    </tr>
                    <tr class="subtotal-row">
                        <td class="gst-label">SGST</td>
                        <td>{{ $currency }} {{ number_format($taxAmount / 2, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="subtotal-row">
                        <td><strong>Total Tax</strong></td>
                        <td><strong>{{ $currency }} {{ number_format($taxAmount, 2) }}</strong></td>
                    </tr>
                @endif

                <tr class="divider-row grand-row">
                    <td>Grand Total</td>
                    <td>{{ $currency }} {{ number_format($invoice->grand_total ?? 0, 2) }}</td>
                </tr>
                @if(($invoice->amount_paid ?? 0) > 0)
                <tr class="paid-row">
                    <td>Amount Paid</td>
                    <td>- {{ $currency }} {{ number_format($invoice->amount_paid, 2) }}</td>
                </tr>
                @endif
                <tr class="due-row">
                    <td>Balance Due</td>
                    <td>{{ $currency }} {{ number_format($invoice->amount_due ?? $invoice->grand_total ?? 0, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Payments history --}}
    @if($invoice->payments && $invoice->payments->isNotEmpty())
    <div style="margin-top: 18px;">
        <div class="party-label" style="margin-bottom: 6px;">Payment History</div>
        <table class="items-table" style="margin-bottom:0;">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Reference</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $payment)
                <tr>
                    <td>{{ $payment->paid_at?->format('d M Y') ?? '—' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $payment->method ?? '')) ?: '—' }}</td>
                    <td>{{ $payment->reference ?? '—' }}</td>
                    <td class="text-right">{{ $currency }} {{ number_format($payment->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Generated by Billstack &bull; {{ now()->format('d M Y') }}
    </div>

</div>
</body>
</html>
