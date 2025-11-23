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

            <div class="flex justify-between">
                <a href="{{ route('invoices.index') }}" class="text-blue-600">Back</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    {{ $isEdit ? 'Update Invoice' : 'Create Invoice' }}
                </button>
            </div>
        </form>
    </div>
@endsection
