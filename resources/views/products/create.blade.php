@extends('layouts.app')

@php $isEdit = isset($product); @endphp

@section('title', $isEdit ? 'Edit Product' : 'Add Product')
@section('page_title', $isEdit ? 'Edit Product' : 'Add Product')

@section('content')
    <div class="bg-white shadow rounded p-6 max-w-2xl mx-auto">
        <form method="POST"
              action="{{ $isEdit ? route('products.update', $product) : route('products.store') }}"
              class="space-y-4">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
                       required class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border rounded px-3 py-2">{{ old('description', $product->description ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Unit *</label>
                    <input type="text" name="unit" value="{{ old('unit', $product->unit ?? 'pcs') }}"
                           placeholder="e.g. pcs, kg, hr, box" required
                           class="w-full border rounded px-3 py-2">
                    <p class="text-xs mt-1" style="color:var(--brand-subtext)">Common: pcs, kg, hr, box, ltr, nos</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Default Rate *</label>
                    <input type="number" step="0.01" min="0" name="default_rate"
                           value="{{ old('default_rate', $product->default_rate ?? 0) }}"
                           required class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tax Rate (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="tax_rate"
                           value="{{ old('tax_rate', $product->tax_rate ?? 0) }}"
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">HSN Code</label>
                    <input type="text" name="hsn_code"
                           value="{{ old('hsn_code', $product->hsn_code ?? '') }}"
                           placeholder="e.g. 998314"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       @checked(old('is_active', $product->is_active ?? true))
                       class="rounded border-gray-300">
                <label for="is_active" class="text-sm font-medium cursor-pointer">Active (visible in invoice item selector)</label>
            </div>

            <div class="flex justify-between pt-2">
                <a href="{{ route('products.index') }}" class="text-blue-500 hover:underline text-sm">Back to Products</a>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-medium">
                    {{ $isEdit ? 'Update Product' : 'Create Product' }}
                </button>
            </div>
        </form>
    </div>
@endsection
