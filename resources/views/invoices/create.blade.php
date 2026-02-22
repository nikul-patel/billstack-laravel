@extends('layouts.app')

@php
    $isEdit = isset($invoice);
    $products = $products ?? collect();
@endphp

@section('title', $isEdit ? 'Edit Invoice' : 'Create Invoice')
@section('page_title', $isEdit ? 'Edit Invoice' : 'Create Invoice')

@section('content')
    {{-- Product catalog data for JS auto-fill --}}
    <script id="products-data" type="application/json">
        @json($products->map(fn($p) => [
            'id'          => $p->id,
            'name'        => $p->name,
            'description' => $p->description ?? '',
            'unit'        => $p->unit,
            'rate'        => $p->default_rate,
            'tax_rate'    => $p->tax_rate,
            'hsn_code'    => $p->hsn_code ?? '',
        ]))
    </script>

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
                <textarea name="notes" rows="2" class="mt-1 w-full border rounded px-3 py-2">{{ old('notes', $invoice->notes ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium">Terms</label>
                <textarea name="terms" rows="2" class="mt-1 w-full border rounded px-3 py-2">{{ old('terms', $invoice->terms ?? '') }}</textarea>
            </div>

            {{-- Product quick-add selector --}}
            @if($products->isNotEmpty())
            <div class="flex items-center gap-3 bg-gray-100 rounded px-3 py-2" style="background:rgba(99,102,241,0.08)">
                <label class="text-sm font-medium whitespace-nowrap">Add from Catalog:</label>
                <select id="product-quick-add" class="flex-1 border rounded px-2 py-1 text-sm">
                    <option value="">— Select a product to add —</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->unit }} @ {{ number_format($p->default_rate, 2) }})</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-md font-semibold">Line Items</h3>
                    <button type="button" id="add-item-row" class="bg-gray-200 text-gray-800 px-3 py-1 rounded text-sm hover:bg-gray-300">+ Add Row</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm" id="items-table">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium uppercase">Item / Name</th>
                                <th class="px-3 py-2 text-left text-xs font-medium uppercase">Description</th>
                                <th class="px-3 py-2 text-right text-xs font-medium uppercase" style="width:90px">Rate</th>
                                <th class="px-3 py-2 text-right text-xs font-medium uppercase" style="width:70px">Qty</th>
                                <th class="px-3 py-2 text-right text-xs font-medium uppercase" style="width:80px">Tax %</th>
                                <th class="px-3 py-2 text-left text-xs font-medium uppercase" style="width:90px">HSN</th>
                                <th class="px-3 py-2 text-center text-xs font-medium uppercase" style="width:70px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $existingItems = old('items', $invoice->items ?? []);
                            @endphp
                            @forelse($existingItems as $index => $item)
                                <tr>
                                    <td class="px-3 py-2">
                                        <select name="items[{{ $index }}][item_id]" class="w-full border rounded px-2 py-1 item-select mb-1">
                                            <option value="">— catalog item —</option>
                                            @foreach($items as $itemOption)
                                                <option value="{{ $itemOption->id }}"
                                                    data-rate="{{ $itemOption->price }}"
                                                    data-name="{{ $itemOption->name }}"
                                                    data-description="{{ $itemOption->description ?? '' }}"
                                                    @selected(($item['item_id'] ?? $item->item_id ?? null) == $itemOption->id)>
                                                    {{ $itemOption->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="items[{{ $index }}][name]"
                                               value="{{ $item['name'] ?? $item->name ?? '' }}"
                                               placeholder="Name *" required
                                               class="w-full border rounded px-2 py-1">
                                    </td>
                                    <td class="px-3 py-2">
                                        <textarea name="items[{{ $index }}][description]" rows="2" class="w-full border rounded px-2 py-1">{{ $item['description'] ?? $item->description ?? '' }}</textarea>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][rate]"
                                               value="{{ $item['rate'] ?? $item->rate ?? 0 }}"
                                               class="w-full border rounded px-2 py-1 text-right">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][quantity]"
                                               value="{{ $item['quantity'] ?? $item->quantity ?? 1 }}"
                                               class="w-full border rounded px-2 py-1 text-right">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" min="0" max="100" name="items[{{ $index }}][tax_percent]"
                                               value="{{ $item['tax_percent'] ?? $item->tax_percent ?? 0 }}"
                                               class="w-full border rounded px-2 py-1 text-right">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" name="items[{{ $index }}][hsn_code]"
                                               value="{{ $item['hsn_code'] ?? $item->hsn_code ?? '' }}"
                                               placeholder="HSN"
                                               class="w-full border rounded px-2 py-1">
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" class="text-red-500 remove-item-row text-xs hover:underline">Remove</button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-row">
                                    <td colspan="7" class="px-3 py-4 text-center" style="color:var(--brand-subtext)">No items added yet. Click "+ Add Row" or pick from catalog above.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-between pt-2">
                <a href="{{ route('invoices.index') }}" class="text-blue-500 hover:underline text-sm">Back</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-medium">
                    {{ $isEdit ? 'Update Invoice' : 'Create Invoice' }}
                </button>
            </div>
        </form>
    </div>

    <template id="item-row-template">
        <tr>
            <td class="px-3 py-2">
                <select name="__NAME__[item_id]" class="w-full border rounded px-2 py-1 item-select mb-1">
                    <option value="">— catalog item —</option>
                    @foreach($items as $itemOption)
                        <option value="{{ $itemOption->id }}"
                                data-rate="{{ $itemOption->price }}"
                                data-name="{{ $itemOption->name }}"
                                data-description="{{ $itemOption->description ?? '' }}">
                            {{ $itemOption->name }}
                        </option>
                    @endforeach
                </select>
                <input type="text" name="__NAME__[name]" placeholder="Name *" required class="w-full border rounded px-2 py-1">
            </td>
            <td class="px-3 py-2">
                <textarea name="__NAME__[description]" rows="2" class="w-full border rounded px-2 py-1"></textarea>
            </td>
            <td class="px-3 py-2">
                <input type="number" step="0.01" min="0" name="__NAME__[rate]" value="0" class="w-full border rounded px-2 py-1 text-right">
            </td>
            <td class="px-3 py-2">
                <input type="number" step="0.01" min="0" name="__NAME__[quantity]" value="1" class="w-full border rounded px-2 py-1 text-right">
            </td>
            <td class="px-3 py-2">
                <input type="number" step="0.01" min="0" max="100" name="__NAME__[tax_percent]" value="0" class="w-full border rounded px-2 py-1 text-right">
            </td>
            <td class="px-3 py-2">
                <input type="text" name="__NAME__[hsn_code]" placeholder="HSN" class="w-full border rounded px-2 py-1">
            </td>
            <td class="px-3 py-2 text-center">
                <button type="button" class="text-red-500 remove-item-row text-xs hover:underline">Remove</button>
            </td>
        </tr>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tableBody = document.querySelector('#items-table tbody');
            const addBtn    = document.getElementById('add-item-row');
            const template  = document.getElementById('item-row-template').innerHTML;

            // Products catalog data injected from server
            const productsData = JSON.parse(
                document.getElementById('products-data')?.textContent || '[]'
            );
            const productsMap = {};
            productsData.forEach(p => { productsMap[String(p.id)] = p; });

            let rowIndex = tableBody.querySelectorAll('tr:not(.empty-row)').length;

            const removeEmptyRow = () => {
                const emptyRow = tableBody.querySelector('.empty-row');
                if (emptyRow) emptyRow.remove();
            };

            /**
             * Fill a row's fields from a product object.
             */
            const fillRowFromProduct = (row, product) => {
                const nameInput = row.querySelector('input[name$="[name]"]');
                const descInput = row.querySelector('textarea[name$="[description]"]');
                const rateInput = row.querySelector('input[name$="[rate]"]');
                const taxInput  = row.querySelector('input[name$="[tax_percent]"]');
                const hsnInput  = row.querySelector('input[name$="[hsn_code]"]');

                if (nameInput) nameInput.value = product.name ?? '';
                if (descInput) descInput.value = product.description ?? '';
                if (rateInput) rateInput.value = product.rate ?? 0;
                if (taxInput)  taxInput.value  = product.tax_rate ?? 0;
                if (hsnInput)  hsnInput.value  = product.hsn_code ?? '';
            };

            /**
             * Bind the legacy catalog item dropdown (items table).
             */
            const bindItemSelect = (row) => {
                const select = row.querySelector('.item-select');
                if (!select) return;
                select.addEventListener('change', () => {
                    const option = select.selectedOptions[0];
                    if (!option || !option.value) return;
                    const nameInput = row.querySelector('input[name$="[name]"]');
                    const descInput = row.querySelector('textarea[name$="[description]"]');
                    const rateInput = row.querySelector('input[name$="[rate]"]');
                    if (option.dataset.rate) rateInput.value   = option.dataset.rate;
                    if (option.dataset.name) nameInput.value   = option.dataset.name;
                    if (option.dataset.description !== undefined) descInput.value = option.dataset.description;
                });
            };

            /**
             * Create and append a new row, return it.
             */
            const addRow = (prefillProduct = null) => {
                removeEmptyRow();
                const nameBase = `items[${rowIndex}]`;
                const html     = template.replace(/__NAME__/g, nameBase);
                const tmp      = document.createElement('tbody');
                tmp.innerHTML  = html.trim();
                const row      = tmp.firstElementChild;
                tableBody.appendChild(row);
                row.querySelector('.remove-item-row').addEventListener('click', () => row.remove());
                bindItemSelect(row);
                if (prefillProduct) fillRowFromProduct(row, prefillProduct);
                rowIndex++;
                return row;
            };

            addBtn?.addEventListener('click', () => addRow());

            // Bind existing rows
            tableBody.querySelectorAll('tr:not(.empty-row)').forEach(row => {
                row.querySelector('.remove-item-row')?.addEventListener('click', () => row.remove());
                bindItemSelect(row);
            });

            // Product quick-add selector
            const quickAdd = document.getElementById('product-quick-add');
            quickAdd?.addEventListener('change', () => {
                const pid = quickAdd.value;
                if (!pid) return;
                const product = productsMap[pid];
                if (product) addRow(product);
                quickAdd.value = '';
            });
        });
    </script>
@endsection
