@extends('layouts.app')

@section('title', 'Invoice #' . $invoice->invoice_number)
@section('page_title', 'Invoice Details')

@section('page_actions')
    <div class="space-x-2 text-sm flex flex-wrap gap-2">
        <a href="{{ route('invoices.preview', $invoice) }}" target="_blank"
           class="bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300">Preview PDF</a>
        <a href="{{ route('invoices.pdf', $invoice) }}"
           class="bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300">Download PDF</a>
        <a href="{{ route('invoices.edit', $invoice) }}"
           class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Edit</a>
        <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" onsubmit="return confirm('Delete this invoice?')">
            @csrf @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Delete</button>
        </form>
    </div>
@endsection

@section('content')
    {{-- Header summary --}}
    <div class="bg-white shadow rounded p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <div>
                <p class="text-xs uppercase tracking-widest" style="color:var(--brand-subtext)">Invoice</p>
                <p class="text-2xl font-bold">{{ $invoice->invoice_number }}</p>
                <p class="text-sm" style="color:var(--brand-subtext)">{{ $invoice->customer?->name ?? 'Unknown Customer' }}</p>
            </div>
            <div class="text-right">
                @php
                    $statusColors = [
                        'draft'     => 'bg-gray-200 text-gray-700',
                        'sent'      => 'bg-blue-100 text-blue-700',
                        'partial'   => 'bg-yellow-100 text-yellow-800',
                        'paid'      => 'bg-green-100 text-green-700',
                        'overdue'   => 'bg-red-100 text-red-700',
                        'cancelled' => 'bg-gray-400 text-white',
                    ];
                    $statusClass = $statusColors[$invoice->status ?? 'draft'] ?? 'bg-gray-200 text-gray-700';
                @endphp
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold uppercase {{ $statusClass }}">
                    {{ ucfirst($invoice->status ?? 'draft') }}
                </span>
                <p class="text-xs mt-1" style="color:var(--brand-subtext)">
                    Due: {{ $invoice->due_date?->format('d M Y') ?? '—' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p style="color:var(--brand-subtext)" class="text-xs uppercase">Invoice Date</p>
                <p class="font-medium">{{ $invoice->invoice_date?->format('d M Y') }}</p>
            </div>
            <div>
                <p style="color:var(--brand-subtext)" class="text-xs uppercase">Due Date</p>
                <p class="font-medium">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</p>
            </div>
            <div>
                <p style="color:var(--brand-subtext)" class="text-xs uppercase">Grand Total</p>
                <p class="font-semibold text-lg">{{ $invoice->currency ?? '₹' }} {{ number_format($invoice->grand_total ?? 0, 2) }}</p>
            </div>
            <div>
                <p style="color:var(--brand-subtext)" class="text-xs uppercase">Balance Due</p>
                <p class="font-semibold text-lg text-red-500">{{ $invoice->currency ?? '₹' }} {{ number_format($invoice->amount_due ?? 0, 2) }}</p>
            </div>
        </div>
    </div>

    {{-- Line Items --}}
    <div class="bg-white shadow rounded p-6 mb-6">
        <h3 class="text-lg font-semibold mb-3">Line Items</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium uppercase">#</th>
                        <th class="px-3 py-2 text-left text-xs font-medium uppercase">Item</th>
                        <th class="px-3 py-2 text-right text-xs font-medium uppercase">Rate</th>
                        <th class="px-3 py-2 text-right text-xs font-medium uppercase">Qty</th>
                        <th class="px-3 py-2 text-right text-xs font-medium uppercase">Tax %</th>
                        <th class="px-3 py-2 text-right text-xs font-medium uppercase">Tax Amt</th>
                        <th class="px-3 py-2 text-right text-xs font-medium uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($invoice->items as $idx => $item)
                        <tr>
                            <td class="px-3 py-2" style="color:var(--brand-subtext)">{{ $idx + 1 }}</td>
                            <td class="px-3 py-2">
                                <div class="font-medium">{{ $item->name }}</div>
                                @if($item->description)
                                    <div class="text-xs" style="color:var(--brand-subtext)">{{ $item->description }}</div>
                                @endif
                                @if($item->hsn_code ?? null)
                                    <div class="text-xs text-blue-400">HSN: {{ $item->hsn_code }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right">{{ number_format($item->rate, 2) }}</td>
                            <td class="px-3 py-2 text-right">{{ $item->quantity }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($item->tax_percent ?? 0, 2) }}%</td>
                            <td class="px-3 py-2 text-right">{{ number_format($item->tax_amount ?? 0, 2) }}</td>
                            <td class="px-3 py-2 text-right font-medium">{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-4 text-center" style="color:var(--brand-subtext)">No line items.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Totals + Notes --}}
    <div class="bg-white shadow rounded p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="text-sm">
                @if($invoice->notes)
                    <p class="text-xs uppercase tracking-widest mb-1" style="color:var(--brand-subtext)">Notes</p>
                    <p class="whitespace-pre-line">{{ $invoice->notes }}</p>
                @endif
                @if($invoice->terms)
                    <p class="text-xs uppercase tracking-widest mb-1 mt-4" style="color:var(--brand-subtext)">Terms</p>
                    <p class="whitespace-pre-line">{{ $invoice->terms }}</p>
                @endif
                @if(!$invoice->notes && !$invoice->terms)
                    <p style="color:var(--brand-subtext)">—</p>
                @endif
            </div>
            <div>
                @php
                    $taxAmt = $invoice->tax_amount ?? $invoice->tax_total ?? 0;
                    $businessGstin = $invoice->business->gstin ?? $invoice->business->gst_number ?? null;
                    $customerGstin = $invoice->customer->gstin ?? null;
                    $isInterState = ($businessGstin && $customerGstin)
                        ? substr($businessGstin, 0, 2) !== substr($customerGstin, 0, 2)
                        : false;
                @endphp
                <div class="flex justify-between text-sm py-1">
                    <span style="color:var(--brand-subtext)">Subtotal</span>
                    <span class="font-medium">{{ number_format($invoice->subtotal ?? 0, 2) }}</span>
                </div>
                @if(($invoice->discount_value ?? 0) > 0)
                <div class="flex justify-between text-sm py-1">
                    <span style="color:var(--brand-subtext)">Discount</span>
                    <span class="font-medium">- {{ number_format($invoice->discount_value, 2) }}</span>
                </div>
                @endif
                @if($taxAmt > 0)
                    @if($isInterState)
                    <div class="flex justify-between text-sm py-1">
                        <span style="color:var(--brand-subtext)">IGST</span>
                        <span class="font-medium">{{ number_format($taxAmt, 2) }}</span>
                    </div>
                    @else
                    <div class="flex justify-between text-sm py-1">
                        <span style="color:var(--brand-subtext)">CGST</span>
                        <span class="font-medium">{{ number_format($taxAmt / 2, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm py-1">
                        <span style="color:var(--brand-subtext)">SGST</span>
                        <span class="font-medium">{{ number_format($taxAmt / 2, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-sm py-1">
                        <span style="color:var(--brand-subtext)">Total Tax</span>
                        <span class="font-medium">{{ number_format($taxAmt, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-sm py-1 border-t border-gray-200 mt-1 pt-2">
                    <span class="font-semibold">Grand Total</span>
                    <span class="font-semibold">{{ number_format($invoice->grand_total ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm py-1">
                    <span class="text-green-500">Amount Paid</span>
                    <span class="font-medium text-green-500">{{ number_format($invoice->amount_paid ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm py-1 font-bold">
                    <span class="text-red-400">Balance Due</span>
                    <span class="text-red-400">{{ number_format($invoice->amount_due ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment History + Record Payment --}}
    <div class="bg-white shadow rounded p-6 mb-6">
        <h3 class="text-lg font-semibold mb-3">Payments</h3>

        @if($invoice->payments->isNotEmpty())
            <div class="overflow-x-auto mb-4">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium uppercase">Date</th>
                            <th class="px-3 py-2 text-left text-xs font-medium uppercase">Method</th>
                            <th class="px-3 py-2 text-left text-xs font-medium uppercase">Reference</th>
                            <th class="px-3 py-2 text-left text-xs font-medium uppercase">Notes</th>
                            <th class="px-3 py-2 text-right text-xs font-medium uppercase">Amount</th>
                            <th class="px-3 py-2 text-center text-xs font-medium uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($invoice->payments->sortByDesc('paid_at') as $payment)
                            <tr>
                                <td class="px-3 py-2">{{ $payment->paid_at?->format('d M Y') ?? '—' }}</td>
                                <td class="px-3 py-2 capitalize">{{ str_replace('_', ' ', $payment->method ?? '—') }}</td>
                                <td class="px-3 py-2">{{ $payment->reference ?? '—' }}</td>
                                <td class="px-3 py-2 text-xs" style="color:var(--brand-subtext)">{{ $payment->notes ?? '—' }}</td>
                                <td class="px-3 py-2 text-right font-medium text-green-500">
                                    {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <form method="POST" action="{{ route('payments.destroy', $payment) }}"
                                          onsubmit="return confirm('Remove this payment?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 text-xs hover:underline">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm mb-4" style="color:var(--brand-subtext)">No payments recorded yet.</p>
        @endif

        @if(($invoice->amount_due ?? 0) > 0)
        <div class="border-t border-gray-200 pt-4">
            <h4 class="text-sm font-semibold mb-3">Record a Payment</h4>
            <form method="POST" action="{{ route('invoices.payments.store', $invoice) }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium mb-1">Amount *</label>
                        <input type="number" step="0.01" name="amount"
                               value="{{ old('amount', number_format($invoice->amount_due ?? 0, 2, '.', '')) }}"
                               min="0.01" required
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Payment Date</label>
                        <input type="date" name="paid_at"
                               value="{{ old('paid_at', now()->format('Y-m-d')) }}"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Method *</label>
                        <select name="method" required class="w-full border rounded px-3 py-2 text-sm">
                            @foreach(['cash' => 'Cash', 'upi' => 'UPI', 'bank_transfer' => 'Bank Transfer', 'cheque' => 'Cheque', 'other' => 'Other'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('method') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Reference / Txn ID</label>
                        <input type="text" name="reference" value="{{ old('reference') }}"
                               placeholder="UTR / Cheque no. / ref"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium mb-1">Notes</label>
                        <input type="text" name="notes" value="{{ old('notes') }}"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <button type="submit"
                            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm font-medium">
                        Record Payment
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
@endsection
