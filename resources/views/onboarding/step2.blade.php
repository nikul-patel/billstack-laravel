@extends('layouts.app')

@section('title', 'Onboarding - Preferences')
@section('page_title', 'Business Preferences')

@section('content')
    <div class="bg-white shadow rounded p-6 max-w-3xl mx-auto">
        <form action="{{ route('onboarding.step2.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Currency</label>
                    <input type="text" name="currency" value="{{ old('currency', $data['currency'] ?? 'INR') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Date Format</label>
                    <input type="text" name="date_format" value="{{ old('date_format', $data['date_format'] ?? 'd-m-Y') }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Timezone</label>
                    <input type="text" name="timezone" value="{{ old('timezone', $data['timezone'] ?? config('app.timezone')) }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium">Terms & Conditions</label>
                <textarea name="terms" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('terms', $data['terms'] ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium">Default Notes</label>
                <textarea name="notes" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('notes', $data['notes'] ?? '') }}</textarea>
            </div>
            <div class="flex justify-between">
                <a href="{{ route('onboarding.step1') }}" class="text-blue-600">Back</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Continue</button>
            </div>
        </form>
    </div>
@endsection
