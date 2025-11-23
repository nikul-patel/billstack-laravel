@extends('layouts.app')

@php
    $isEdit = isset($item);
@endphp

@section('title', $isEdit ? 'Edit Item' : 'Create Item')
@section('page_title', $isEdit ? 'Edit Item' : 'Create Item')

@section('content')
    <div class="bg-white shadow rounded p-6 max-w-2xl mx-auto">
        <form method="POST" action="{{ $isEdit ? route('items.update', $item) : route('items.store') }}" class="space-y-4">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif
            <div>
                <label class="block text-sm font-medium">Name *</label>
                <input type="text" name="name" value="{{ old('name', $item->name ?? '') }}" required class="mt-1 w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium">Description</label>
                <textarea name="description" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('description', $item->description ?? '') }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium">Price *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $item->price ?? '') }}" required class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Tax Rate (%)</label>
                    <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', $item->tax_rate ?? 0) }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Unit</label>
                    <input type="text" name="unit" value="{{ old('unit', $item->unit ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
            </div>
            <div class="flex justify-between">
                <a href="{{ route('items.index') }}" class="text-blue-600">Back</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    {{ $isEdit ? 'Update Item' : 'Create Item' }}
                </button>
            </div>
        </form>
    </div>
@endsection
