<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Name *</label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="mt-1 w-full border rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm font-medium">Email *</label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="mt-1 w-full border rounded px-3 py-2" required>
    </div>
    <div>
        <label class="block text-sm font-medium">{{ isset($user) ? 'New Password' : 'Password' }}</label>
        <input type="password" name="password" class="mt-1 w-full border rounded px-3 py-2" {{ isset($user) ? '' : 'required' }}>
        @if(isset($user))
            <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password.</p>
        @endif
    </div>
    <div>
        <label class="block text-sm font-medium">Business</label>
        <select name="business_id" class="mt-1 w-full border rounded px-3 py-2">
            <option value="">Unassigned</option>
            @foreach($businesses as $businessOption)
                <option value="{{ $businessOption->id }}" @selected(old('business_id', $user->business_id ?? '') == $businessOption->id)>
                    {{ $businessOption->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
