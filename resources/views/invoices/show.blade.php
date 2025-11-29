@extends('layouts.app')

@section('title', 'Invoice Details')
@section('page_title', 'Invoice Details')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <div>
            <p class="text-sm text-gray-600">Invoice #{{ $invoice->invoice_number }}</p>
            <p class="text-lg font-semibold">{{ $invoice->customer?->name ?? 'Unknown Customer' }}</p>
        </div>
        <div class="space-x-2 text-sm">
            <a href="{{ route('invoices.preview', $invoice) }}" target="_blank" class="bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300">Preview PDF</a>
            <a href="{{ route('invoices.pdf', $invoice) }}" class="bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300">Download PDF</a>
            <a href="{{ route('invoices.edit', $invoice) }}" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Edit</a>
        </div>
    </div>

    <div class="bg-white shadow rounded p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Invoice Date</p>
                <p class="font-medium">{{ optional($invoice->invoice_date)->format('Y-m-d') }}</p>
            </div>
            <div>
                <p class="text-gray-500">Due Date</p>
                <p class="font-medium">{{ optional($invoice->due_date)->format('Y-m-d') ?: '—' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Status</p>
                <p class="font-medium capitalize">{{ $invoice->status ?? 'draft' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded p-6 mb-6">
        <h3 class="text-lg font-semibold mb-3">Line Items</h3>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Rate</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Line Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($invoice->items as $item)
                    <tr>
                        <td class="px-3 py-2">
                            <div class="font-medium">{{ $item->name }}</div>
                            @if($item->description)
                                <div class="text-xs text-gray-500">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-right">{{ number_format($item->rate, 2) }}</td>
                        <td class="px-3 py-2 text-right">{{ $item->quantity }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 py-3 text-center text-gray-500">No line items.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white shadow rounded p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="text-sm">
                <p class="text-gray-500">Notes</p>
                <p class="whitespace-pre-line">{{ $invoice->notes ?? '—' }}</p>
            </div>
            <div>
                <div class="flex justify-between text-sm py-1">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-medium">{{ number_format($invoice->subtotal ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm py-1">
                    <span class="text-gray-600">Tax</span>
                    <span class="font-medium">{{ number_format($invoice->tax_total ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm py-1">
                    <span class="text-gray-600">Grand Total</span>
                    <span class="font-semibold">{{ number_format($invoice->grand_total ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm py-1">
                    <span class="text-gray-600">Amount Paid</span>
                    <span class="font-medium text-green-700">{{ number_format($invoice->amount_paid ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm py-1">
                    <span class="text-gray-600">Amount Due</span>
                    <span class="font-semibold text-red-600">{{ number_format($invoice->amount_due ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
