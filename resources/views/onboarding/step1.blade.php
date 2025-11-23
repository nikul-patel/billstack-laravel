@extends('layouts.app')

@section('title', 'Onboarding - Business Info')
@section('page_title', 'Business Information')

@section('content')
    <div class="bg-white shadow rounded p-6 max-w-3xl mx-auto">
        <form action="{{ route('onboarding.step1.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Business Name *</label>
                    <input type="text" name="name" value="{{ old('name', $data['name'] ?? '') }}" required class="mt-1 w-full border rounded px-3 py-2">
                    @error('name')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Owner Name</label>
                    <input type="text" name="owner_name" value="{{ old('owner_name', $data['owner_name'] ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email', $data['email'] ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $data['phone'] ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">GST / Tax ID</label>
                    <input type="text" name="gst_number" value="{{ old('gst_number', $data['gst_number'] ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Pincode</label>
                    <input type="text" name="pincode" value="{{ old('pincode', $data['pincode'] ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">City</label>
                    <input type="text" name="city" value="{{ old('city', $data['city'] ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">State</label>
                    <input type="text" name="state" value="{{ old('state', $data['state'] ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Country</label>
                    <input type="text" name="country" value="{{ old('country', $data['country'] ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Invoice Prefix</label>
                    <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $data['invoice_prefix'] ?? 'INV-') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Invoice Start Number</label>
                    <input type="number" name="invoice_start_no" value="{{ old('invoice_start_no', $data['invoice_start_no'] ?? 1) }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium">Address</label>
                <textarea name="address" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('address', $data['address'] ?? '') }}</textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Continue</button>
            </div>
        </form>
    </div>
@endsection
