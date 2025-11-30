@extends('layouts.app')

@php
    $isEdit = $isEdit ?? false;
    $formAction = $isEdit ? route('recurring-profiles.update', $profile) : route('recurring-profiles.store');
    $pageTitle = $isEdit ? 'Edit Recurring Profile' : 'Create Recurring Profile';
@endphp

@section('title', $pageTitle)
@section('page_title', $pageTitle)

@section('content')
    <div class="bg-white shadow rounded-lg p-6 space-y-6">
        <form method="POST" action="{{ $formAction }}" class="space-y-6">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div>
                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer <span class="text-red-500">*</span></label>
                <select
                    id="customer_id"
                    name="customer_id"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                >
                    <option value="">Select customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('customer_id', $profile->customer_id ?? null) == $customer->id)>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
                @if($customers->isEmpty())
                    <p class="text-xs text-orange-600 mt-1">No customers found for this business. Please create a customer before creating a recurring profile.</p>
                @endif
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Profile Name <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        required
                        maxlength="255"
                        value="{{ old('name', $profile->name ?? '') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                    >
                </div>
                <div>
                    <label for="frequency" class="block text-sm font-medium text-gray-700 mb-1">Frequency <span class="text-red-500">*</span></label>
                    <select
                        id="frequency"
                        name="frequency"
                        required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                    >
                        @foreach($frequencies as $value => $label)
                            <option value="{{ $value }}" @selected(old('frequency', $profile->frequency ?? 'monthly') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label for="next_run_date" class="block text-sm font-medium text-gray-700 mb-1">Next Run Date</label>
                    <input
                        type="date"
                        id="next_run_date"
                        name="next_run_date"
                        value="{{ old('next_run_date', optional($profile->next_run_date)->format('Y-m-d')) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                    >
                </div>
                <div>
                    <label for="day_of_month" class="block text-sm font-medium text-gray-700 mb-1">Day of Month</label>
                    <input
                        type="number"
                        id="day_of_month"
                        name="day_of_month"
                        min="1"
                        max="31"
                        value="{{ old('day_of_month', $profile->day_of_month ?? '') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                    >
                    <p class="text-xs text-gray-500 mt-1">Optional. Helpful for monthly, quarterly, or yearly profiles.</p>
                </div>
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Reference Amount</label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        id="amount"
                        name="amount"
                        value="{{ old('amount', $profile->amount ?? '') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                    >
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea
                    id="notes"
                    name="notes"
                    rows="4"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                >{{ old('notes', $profile->notes ?? '') }}</textarea>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-semibold">
                    {{ $isEdit ? 'Update Profile' : 'Create Profile' }}
                </button>
                <a href="{{ route('recurring-profiles.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>
@endsection
