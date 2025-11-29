@extends('layouts.app')

@php
    $isEdit = isset($invoice);
@endphp

@section('title', $isEdit ? 'Edit Invoice' : 'Create Invoice')
@section('page_title', $isEdit ? 'Edit Invoice' : 'Create Invoice')

@section('content')
    <div class="bg-white shadow rounded p-6 max-w-4xl mx-auto">
        <form method="POST" action="{{ $isEdit ? route('invoices.update', $invoice) : route('invoices.store') }}" class="space-y-4">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Customer *</label>
                    <select name="customer_id" class="mt-1 w-full border rounded px-3 py-2" required>
                        <option value="">Select customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id', $invoice->customer_id ?? '') == $customer->id)>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Invoice Date *</label>
                        <input type="date" name="invoice_date" value="{{ old('invoice_date', optional($invoice->invoice_date ?? now())->format('Y-m-d')) }}" required class="mt-1 w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Due Date</label>
                        <input type="date" name="due_date" value="{{ old('due_date', optional($invoice->due_date ?? now()->addDays(7))->format('Y-m-d')) }}" class="mt-1 w-full border rounded px-3 py-2">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Notes</label>
                <textarea name="notes" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('notes', $invoice->notes ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium">Terms</label>
                <textarea name="terms" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('terms', $invoice->terms ?? '') }}</textarea>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-md font-semibold">Line Items</h3>
                    <button type="button" id="add-item-row" class="bg-gray-200 text-gray-800 px-3 py-1 rounded text-sm">Add Item</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm" id="items-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left">Item</th>
                                <th class="px-3 py-2 text-left">Description</th>
                                <th class="px-3 py-2 text-right">Rate</th>
                                <th class="px-3 py-2 text-right">Qty</th>
                                <th class="px-3 py-2 text-right">Tax %</th>
                                <th class="px-3 py-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $existingItems = old('items', $invoice->items ?? []);
                            @endphp
                            @forelse($existingItems as $index => $item)
                                <tr>
                                    <td class="px-3 py-2">
                                        <select name="items[{{ $index }}][item_id]" class="w-full border rounded px-2 py-1 item-select">
                                            <option value="">Select item</option>
                                            @foreach($items as $itemOption)
                                                <option value="{{ $itemOption->id }}" data-rate="{{ $itemOption->price }}" @selected(($item['item_id'] ?? $item->item_id ?? null) == $itemOption->id)>
                                                    {{ $itemOption->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="items[{{ $index }}][name]" value="{{ $item['name'] ?? $item->name ?? '' }}" placeholder="Name" class="mt-1 w-full border rounded px-2 py-1">
                                    </td>
                                    <td class="px-3 py-2">
                                        <textarea name="items[{{ $index }}][description]" rows="2" class="w-full border rounded px-2 py-1">{{ $item['description'] ?? $item->description ?? '' }}</textarea>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" name="items[{{ $index }}][rate]" value="{{ $item['rate'] ?? $item->rate ?? 0 }}" class="w-full border rounded px-2 py-1 text-right">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? $item->quantity ?? 1 }}" class="w-full border rounded px-2 py-1 text-right">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" name="items[{{ $index }}][tax_percent]" value="{{ $item['tax_percent'] ?? $item->tax_percent ?? 0 }}" class="w-full border rounded px-2 py-1 text-right">
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" class="text-red-600 remove-item-row">Remove</button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-row">
                                    <td colspan="6" class="px-3 py-3 text-center text-gray-500">No items added yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('invoices.index') }}" class="text-blue-600">Back</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    {{ $isEdit ? 'Update Invoice' : 'Create Invoice' }}
                </button>
            </div>
        </form>
    </div>

    <template id="item-row-template">
        <tr>
            <td class="px-3 py-2">
                <select name="__NAME__[item_id]" class="w-full border rounded px-2 py-1 item-select">
                    <option value="">Select item</option>
                    @foreach($items as $itemOption)
                        <option value="{{ $itemOption->id }}" data-rate="{{ $itemOption->price }}">{{ $itemOption->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="__NAME__[name]" placeholder="Name" class="mt-1 w-full border rounded px-2 py-1">
            </td>
            <td class="px-3 py-2">
                <textarea name="__NAME__[description]" rows="2" class="w-full border rounded px-2 py-1"></textarea>
            </td>
            <td class="px-3 py-2">
                <input type="number" step="0.01" name="__NAME__[rate]" value="0" class="w-full border rounded px-2 py-1 text-right">
            </td>
            <td class="px-3 py-2">
                <input type="number" step="0.01" name="__NAME__[quantity]" value="1" class="w-full border rounded px-2 py-1 text-right">
            </td>
            <td class="px-3 py-2">
                <input type="number" step="0.01" name="__NAME__[tax_percent]" value="0" class="w-full border rounded px-2 py-1 text-right">
            </td>
            <td class="px-3 py-2 text-center">
                <button type="button" class="text-red-600 remove-item-row">Remove</button>
            </td>
        </tr>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tableBody = document.querySelector('#items-table tbody');
            const addBtn = document.getElementById('add-item-row');
            const template = document.getElementById('item-row-template').innerHTML;

            let rowIndex = tableBody.querySelectorAll('tr').length;

            const removeEmptyRow = () => {
                const emptyRow = tableBody.querySelector('.empty-row');
                if (emptyRow) {
                    emptyRow.remove();
                }
            };

            const bindSelect = (row) => {
                const select = row.querySelector('.item-select');
                select.addEventListener('change', (e) => {
                    const option = e.target.selectedOptions[0];
                    const rate = option?.dataset?.rate;
                    if (rate) {
                        row.querySelector('input[name$="[rate]"]').value = rate;
                        row.querySelector('input[name$="[name]"]').value = option.textContent.trim();
                    }
                });
            };

            const addRow = () => {
                removeEmptyRow();
                const nameBase = `items[${rowIndex}]`;
                const html = template.replace(/__NAME__/g, nameBase);
                const tmp = document.createElement('tbody');
                tmp.innerHTML = html.trim();
                const row = tmp.firstChild;
                tableBody.appendChild(row);
                row.querySelector('.remove-item-row').addEventListener('click', () => row.remove());
                bindSelect(row);
                rowIndex++;
            };

            addBtn?.addEventListener('click', addRow);

            tableBody.querySelectorAll('.remove-item-row').forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.currentTarget.closest('tr').remove();
                });
            });

            tableBody.querySelectorAll('tr').forEach(bindSelect);
        });
    </script>
@endsection
