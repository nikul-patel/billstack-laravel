@extends('layouts.app')

@section('title', 'Edit Business')
@section('page_title', 'Edit Business Profile')

@section('content')
    <div class="bg-white shadow rounded p-6 max-w-3xl mx-auto">
        <form method="POST" action="{{ route('business.profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Business Name *</label>
                    <input type="text" name="name" value="{{ old('name', $business->name ?? '') }}" required class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Owner Name</label>
                    <input type="text" name="owner_name" value="{{ old('owner_name', $business->owner_name ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email', $business->email ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $business->phone ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">GST / Tax ID</label>
                    <input type="text" name="gst_number" value="{{ old('gst_number', $business->gst_number ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Invoice Prefix</label>
                    <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $business->invoice_prefix ?? '') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="Optional">
                </div>
                <div>
                    <label class="block text-sm font-medium">Invoice Start No</label>
                    <input type="number" name="invoice_start_no" value="{{ old('invoice_start_no', $business->invoice_start_no ?? 1) }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Currency</label>
                    <input type="text" name="currency" value="{{ old('currency', $business->currency ?? 'INR') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Date Format</label>
                    <input type="text" name="date_format" value="{{ old('date_format', $business->date_format ?? 'd-m-Y') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Timezone</label>
                    <input type="text" name="timezone" value="{{ old('timezone', $business->timezone ?? config('app.timezone')) }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium">Address</label>
                <textarea name="address" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('address', $business->address ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium">Address Line 2</label>
                <input type="text" name="address_line_2" value="{{ old('address_line_2', $business->address_line_2 ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium">Terms & Conditions</label>
                <textarea name="terms" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('terms', $business->terms ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium">Default Notes</label>
                <textarea name="notes" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('notes', $business->notes ?? '') }}</textarea>
            </div>
            <div class="flex justify-between">
                <a href="{{ route('dashboard') }}" class="text-blue-600">Back</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
@endsection
