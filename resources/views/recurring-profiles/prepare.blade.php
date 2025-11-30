@extends('layouts.app')

@section('title', 'Prepare Draft Invoice')
@section('page_title', 'Prepare Draft Invoice')

@section('content')
    <div class="bg-white shadow rounded-lg p-6 space-y-6">
        <div class="border border-blue-100 rounded-lg p-4 bg-blue-50 text-sm text-blue-900">
            <p class="font-semibold">Profile: {{ $profile->name }}</p>
            <p>Customer: {{ $profile->customer?->name ?? 'N/A' }}</p>
            <p class="text-xs mt-2 text-blue-700">
                Select the catalog items to include in this month's draft. Only rows with a quantity greater than zero will be added to the invoice.
            </p>
        </div>

        @if($items->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded">
                No catalog items found for this business. <a href="{{ route('items.create') }}" class="underline">Add items</a> before preparing a draft invoice.
            </div>
        @else
            <form method="POST" action="{{ route('recurring-profiles.generate-invoices', $profile) }}">
                @csrf
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-gray-600">Item</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-600">Quantity</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-600">Rate</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-600">Tax %</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($items as $index => $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-gray-900">{{ $item->name }}</div>
                                        <div class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($item->description, 60) }}</div>
                                        <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            name="items[{{ $index }}][quantity]"
                                            value="{{ old('items.'.$index.'.quantity') }}"
                                            class="w-28 border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                                        >
                                    </td>
                                    <td class="px-4 py-3">
                                        <input
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            name="items[{{ $index }}][rate]"
                                            value="{{ old('items.'.$index.'.rate', $item->price) }}"
                                            class="w-28 border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                                        >
                                    </td>
                                    <td class="px-4 py-3 text-gray-800">
                                        {{ number_format($item->tax_rate ?? 0, 2) }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex items-center gap-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-semibold">
                        Draft Invoice
                    </button>
                    <a href="{{ route('recurring-profiles.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                </div>
            </form>
        @endif
    </div>
@endsection
