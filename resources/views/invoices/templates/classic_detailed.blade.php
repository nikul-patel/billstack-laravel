{{-- Classic Detailed Invoice Template --}}
@php
    $business = $invoice->business;
    $customer = $invoice->customer;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .wrapper { width: 780px; margin: 0 auto; }
        .row { width: 100%; display: table; }
        .col-6 { display: table-cell; width: 50%; vertical-align: top; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 4px 6px; }
        .no-border td, .no-border th { border: none; padding: 2px 0; }
        .title { font-size: 18px; font-weight: bold; text-align: center; margin: 10px 0; text-transform: uppercase; }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- Business Header --}}
    <div class="row">
        <div class="col-6">
            <strong>{{ strtoupper($business->name) }}</strong><br>
            @if($business->su_number)
                SU NO.: {{ $business->su_number }}<br>
            @endif
            @if($business->village)
                VILLAGE: {{ strtoupper($business->village) }}<br>
            @endif
            @if($business->taluka || $business->district)
                TA/DIST: {{ strtoupper(trim(($business->taluka ? $business->taluka.' / ' : '').$business->district)) }}<br>
            @endif
            @if($business->state || $business->pincode)
                {{ strtoupper($business->state) }}-{{ $business->pincode }}<br>
            @endif
        </div>
        <div class="col-6 text-right">
            <table class="no-border" style="float:right;">
                <tr>
                    <td><strong>INVOICE NO:</strong></td>
                    <td>{{ $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td><strong>Date:</strong></td>
                    <td>{{ $invoice->invoice_date ? $invoice->invoice_date->format('d-m-Y') : '' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="title">INVOICE</div>

    {{-- Bill To and Duration --}}
    <table class="no-border">
        <tr>
            <td width="60%">
                <strong>To,</strong><br>
                <strong>{{ $customer->name }}</strong><br>
                @if($customer->billing_address_line_1)
                    {{ $customer->billing_address_line_1 }}<br>
                @endif
                @if($customer->billing_address_line_2)
                    {{ $customer->billing_address_line_2 }}<br>
                @endif
                @if($customer->city || $customer->state || $customer->pincode)
                    {{ $customer->city }} {{ $customer->state }} {{ $customer->pincode }}<br>
                @endif
            </td>
            <td width="40%">
                <strong>Duration of Invoice:</strong><br>
                {{ $invoice->duration_text }}
            </td>
        </tr>
    </table>

    {{-- Items Table --}}
    <table style="margin-top: 10px;">
        <thead>
            <tr>
                <th width="8%">Sr. No.</th>
                <th>Particular</th>
                <th width="15%">Rate</th>
                <th width="12%">Qty</th>
                <th width="18%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $i => $item)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals & Signature --}}
    <table style="margin-top: 10px;">
        <tr>
            <td width="60%">
                <strong>Amount Rs.</strong> {{ number_format($invoice->grand_total, 2) }}
            </td>
            <td width="40%" class="text-center" style="height: 60px;">
                <br><br>
                Authorised Signatory
            </td>
        </tr>
    </table>

</div>
</body>
</html>
