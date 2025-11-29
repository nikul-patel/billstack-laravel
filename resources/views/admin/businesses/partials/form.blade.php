<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Name *</label>
        <input type="text" name="name" value="{{ old('name', $business->name ?? '') }}" class="mt-1 w-full border rounded px-3 py-2" required>
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
        <label class="block text-sm font-medium">GST Number</label>
        <input type="text" name="gst_number" value="{{ old('gst_number', $business->gst_number ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Invoice Prefix</label>
        <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $business->invoice_prefix ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Invoice Start No</label>
        <input type="number" name="invoice_start_no" value="{{ old('invoice_start_no', $business->invoice_start_no ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Currency</label>
        <input type="text" name="currency" value="{{ old('currency', $business->currency ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
    </div>
</div>
<div>
    <label class="block text-sm font-medium">Address</label>
    <textarea name="address" rows="2" class="mt-1 w-full border rounded px-3 py-2">{{ old('address', $business->address ?? '') }}</textarea>
</div>
<div>
    <label class="block text-sm font-medium">Address Line 2</label>
    <input type="text" name="address_line_2" value="{{ old('address_line_2', $business->address_line_2 ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-medium">City</label>
        <input type="text" name="city" value="{{ old('city', $business->city ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium">State</label>
        <input type="text" name="state" value="{{ old('state', $business->state ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Country</label>
        <input type="text" name="country" value="{{ old('country', $business->country ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Pincode</label>
        <input type="text" name="pincode" value="{{ old('pincode', $business->pincode ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
    </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Timezone</label>
        <input type="text" name="timezone" value="{{ old('timezone', $business->timezone ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium">Date Format</label>
        <input type="text" name="date_format" value="{{ old('date_format', $business->date_format ?? '') }}" class="mt-1 w-full border rounded px-3 py-2">
    </div>
</div>
<div>
    <label class="block text-sm font-medium">Terms</label>
    <textarea name="terms" rows="2" class="mt-1 w-full border rounded px-3 py-2">{{ old('terms', $business->terms ?? '') }}</textarea>
</div>
<div>
    <label class="block text-sm font-medium">Notes</label>
    <textarea name="notes" rows="2" class="mt-1 w-full border rounded px-3 py-2">{{ old('notes', $business->notes ?? '') }}</textarea>
</div>
