@extends('layouts.app')

@section('title', 'Edit Business')
@section('page_title', 'Business Profile Settings')

@section('content')
    <div class="bg-white shadow rounded p-6 max-w-3xl mx-auto">
        <form method="POST" action="{{ route('business.profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Basic Info --}}
            <div>
                <h3 class="text-md font-semibold mb-3 pb-1 border-b border-gray-200">Basic Information</h3>
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
                </div>
            </div>

            {{-- GST / Tax --}}
            <div>
                <h3 class="text-md font-semibold mb-3 pb-1 border-b border-gray-200">GST &amp; Tax Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">GSTIN</label>
                        <input type="text" name="gstin" value="{{ old('gstin', $business->gstin ?? '') }}"
                               placeholder="e.g. 29ABCDE1234F1Z5"
                               class="mt-1 w-full border rounded px-3 py-2 font-mono">
                        <p class="text-xs mt-1" style="color:var(--brand-subtext)">15-character GST Identification Number</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">PAN</label>
                        <input type="text" name="pan" value="{{ old('pan', $business->pan ?? '') }}"
                               placeholder="e.g. ABCDE1234F"
                               class="mt-1 w-full border rounded px-3 py-2 font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Legacy GST / Tax ID</label>
                        <input type="text" name="gst_number" value="{{ old('gst_number', $business->gst_number ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Default Tax Type</label>
                        <select name="default_tax_type" class="mt-1 w-full border rounded px-3 py-2">
                            @foreach(['none' => 'None', 'gst' => 'GST (India)', 'vat' => 'VAT'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('default_tax_type', $business->default_tax_type ?? 'none') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Default GST Rate (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="default_gst_rate"
                               value="{{ old('default_gst_rate', $business->default_gst_rate ?? 18) }}"
                               class="mt-1 w-full border rounded px-3 py-2">
                        <p class="text-xs mt-1" style="color:var(--brand-subtext)">Used as default on new invoices (e.g. 18 for 18%)</p>
                    </div>
                </div>
            </div>

            {{-- Address --}}
            <div>
                <h3 class="text-md font-semibold mb-3 pb-1 border-b border-gray-200">Address</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium">Address Line 1</label>
                        <textarea name="address" rows="2" class="mt-1 w-full border rounded px-3 py-2">{{ old('address', $business->address ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Address Line 2</label>
                        <input type="text" name="address_line_2" value="{{ old('address_line_2', $business->address_line_2 ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium">City</label>
                            <input type="text" name="city" value="{{ old('city', $business->city ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">State</label>
                            <input type="text" name="state" value="{{ old('state', $business->state ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Pincode</label>
                            <input type="text" name="pincode" value="{{ old('pincode', $business->pincode ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Country</label>
                        <input type="text" name="country" value="{{ old('country', $business->country ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
                    </div>
                </div>
            </div>

            {{-- Invoice Settings --}}
            <div>
                <h3 class="text-md font-semibold mb-3 pb-1 border-b border-gray-200">Invoice Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Invoice Prefix</label>
                        <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $business->invoice_prefix ?? '') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="e.g. INV-">
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
            </div>

            {{-- Terms & Notes --}}
            <div>
                <h3 class="text-md font-semibold mb-3 pb-1 border-b border-gray-200">Defaults</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium">Default Terms &amp; Conditions</label>
                        <textarea name="terms" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('terms', $business->terms ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Default Notes</label>
                        <textarea name="notes" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('notes', $business->notes ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-between pt-2">
                <a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline text-sm">Back to Dashboard</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-medium">Save Settings</button>
            </div>
        </form>
    </div>
@endsection
