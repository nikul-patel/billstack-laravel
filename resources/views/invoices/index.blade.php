@extends('layouts.app')

@section('title', 'Invoices')
@section('page_title', 'Invoices')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('invoices.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">Generate Invoice</a>
    </div>

    <div class="bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount Due</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($invoices as $invoice)
                    <tr>
                        <td class="px-4 py-2">{{ $invoice->invoice_number }}</td>
                        <td class="px-4 py-2">{{ $invoice->customer?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ optional($invoice->invoice_date)->format('Y-m-d') }}</td>
                        <td class="px-4 py-2">{{ number_format($invoice->amount_due ?? 0, 2) }}</td>
                        <td class="px-4 py-2 capitalize">{{ $invoice->status ?? 'draft' }}</td>
                        <td class="px-4 py-2 space-x-2 text-sm">
                            <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600">View</a>
                            <a href="{{ route('invoices.edit', $invoice) }}" class="text-indigo-600">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">No invoices found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
@endsection
