@extends('layouts.app')

@php
    $isEdit = isset($customer);
@endphp

@section('title', $isEdit ? 'Edit Customer' : 'Create Customer')
@section('page_title', $isEdit ? 'Edit Customer' : 'Create Customer')

@section('content')
    <div class="bg-white shadow rounded p-6 max-w-3xl mx-auto">
        <form method="POST" action="{{ $isEdit ? route('customers.update', $customer) : route('customers.store') }}" class="space-y-4">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}" required class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $customer->contact_person ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">GSTIN / Tax ID</label>
                    <input type="text" name="gstin" value="{{ old('gstin', $customer->gstin ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Opening Balance</label>
                    <input type="number" step="0.01" name="opening_balance" value="{{ old('opening_balance', $customer->opening_balance ?? 0) }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Address Line 1</label>
                <input type="text" name="billing_address_line_1" value="{{ old('billing_address_line_1', $customer->billing_address_line_1 ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium">Address Line 2</label>
                <input type="text" name="billing_address_line_2" value="{{ old('billing_address_line_2', $customer->billing_address_line_2 ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium">City</label>
                    <input type="text" name="city" value="{{ old('city', $customer->city ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">State</label>
                    <input type="text" name="state" value="{{ old('state', $customer->state ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Country</label>
                    <input type="text" name="country" value="{{ old('country', $customer->country ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Pincode</label>
                    <input type="text" name="pincode" value="{{ old('pincode', $customer->pincode ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Notes</label>
                <textarea name="notes" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('notes', $customer->notes ?? '') }}</textarea>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('customers.index') }}" class="text-blue-600">Back</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    {{ $isEdit ? 'Update Customer' : 'Create Customer' }}
                </button>
            </div>
        </form>
    </div>
@endsection
