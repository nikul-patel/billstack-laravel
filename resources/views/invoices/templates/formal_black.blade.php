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
        @page { margin: 20mm; }
        body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 0; background: #fff; }
        .page { width: 100%; max-width: 720px; margin: 0 auto; }
        .bordered { border: 2px solid #000; padding: 18px; box-sizing: border-box; }
        .header-title { text-align: center; font-size: 18px; font-weight: bold; border: 2px solid #000; padding: 10px 0; margin-bottom: 6px; }
        .sub-title { text-align: center; font-weight: bold; background: #000; color: #fff; padding: 8px 0; margin-bottom: 14px; }
        .info-row { width: 100%; display: table; margin-bottom: 14px; }
        .col { display: table-cell; vertical-align: top; }
        .col-60 { width: 55%; padding-right: 12px; }
        .col-40 { width: 45%; text-align: left; padding-left: 12px; }
        .box { border: 1px solid #000; padding: 0px 12px; font-size: 11px; width: 90%; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #000; padding: 8px 6px; font-size: 12px; vertical-align: top; }
        th { background: #000; color: #fff; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .no-border td { border: none; padding: 4px 0; }
        .totals-row td { font-weight: bold; }
    </style>
</head>
<body>
    <div class="page">
        <div class="bordered">
            <div class="header-title">{{ strtoupper($business->name) }}</div>
            <div class="sub-title">Invoice</div>

            <div class="info-row">
                <div class="col col-60">
                    <p style="margin:0 0 6px 0;">
                        <strong>Name:</strong> {{ $business->owner_name ?? $business->name }}<br>
                        <strong>Address:</strong>
                        @php
                            $addressParts = array_filter([
                                $business->address,
                                $business->address_line_2,
                                $business->city,
                            ]);
                        @endphp
                        @if(!empty($addressParts))
                            @foreach($addressParts as $part)
                                {{ $part }}@if($part !== end($addressParts)), @endif
                                <br>
                            @endforeach
                        @endif
                        @if($business->state || $business->pincode)
                            {{ $business->state }}@if($business->pincode) - {{ $business->pincode }}@endif
                        @endif
                    </p>
                </div>
                <div class="col col-40">
                    <table class="no-border" style="margin: 0 auto;">
                        <tr>
                            <td><strong>INVOICE NO:</strong></td>
                            <td>{{ $invoice->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dated:</strong></td>
                            <td>{{ $invoice->invoice_date ? $invoice->invoice_date->format('d/m/Y') : '' }}</td>
                        </tr>
                    </table>
                    <div class="box" style="margin-top:12px; text-align:left;">
                        <strong>Duration of Invoice:</strong><br>
                        <span style="display:inline-block; margin-top:4px;">{{ $invoice->duration_text ?? '' }}</span>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 14px;">
                <strong>To,</strong><br>
                <strong>Company Name:</strong> {{ $customer->name }}<br>
                @if($customer->billing_address_line_1)
                    <strong>Address:</strong> {{ $customer->billing_address_line_1 }}@if($customer->billing_address_line_2),<br> {{ $customer->billing_address_line_2 }}@endif
                    <br>
                @endif
                @if($customer->city || $customer->state || $customer->pincode)
                    {{ $customer->city }}<br>
                    {{ $customer->state }}@if($customer->pincode) - {{ $customer->pincode }}@endif
                    <br>
                    <!-- @if($customer->pincode)
                        <br>
                    @endif -->
                @endif
                @if($customer->country)
                    {{ $customer->country }}
                @endif
                <br><br>
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="8%">Sr. No.</th>
                        <th>Particular</th>
                        <th width="15%">Rate</th>
                        <th width="10%">Qty</th>
                        <th width="17%">Amount {{$business->currency??''}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                    @if($invoice->items->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">No items</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="totals-row">
                        <td colspan="4" class="text-right">Total</td>
                        <td class="text-right">{{ number_format($invoice->grand_total ?? $invoice->subtotal ?? 0, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>
</html>
