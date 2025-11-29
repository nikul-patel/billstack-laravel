@extends('layouts.app')

@section('title', 'Onboarding - Review')
@section('page_title', 'Review & Complete')

@section('content')
    <div class="bg-white shadow rounded p-6 max-w-3xl mx-auto space-y-4">
        <div>
            <h2 class="text-lg font-semibold mb-2">Business Details</h2>
            <ul class="text-sm space-y-1">
                <li><strong>Name:</strong> {{ $data['name'] ?? '' }}</li>
                <li><strong>Owner:</strong> {{ $data['owner_name'] ?? '' }}</li>
                <li><strong>Email:</strong> {{ $data['email'] ?? '' }}</li>
                <li><strong>Phone:</strong> {{ $data['phone'] ?? '' }}</li>
                <li><strong>GST / Tax ID:</strong> {{ $data['gst_number'] ?? '' }}</li>
                <li><strong>Address:</strong> {{ $data['address'] ?? '' }}</li>
                <li><strong>Location:</strong> {{ implode(', ', array_filter([$data['city'] ?? '', $data['state'] ?? '', $data['country'] ?? '', $data['pincode'] ?? ''])) }}</li>
                <li><strong>Invoice Prefix:</strong> {{ $data['invoice_prefix'] ?? 'None' }} / <strong>Start No:</strong> {{ $data['invoice_start_no'] ?? 1 }}</li>
            </ul>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-2">Preferences</h2>
            <ul class="text-sm space-y-1">
                <li><strong>Currency:</strong> {{ $data['currency'] ?? 'INR' }}</li>
                <li><strong>Date Format:</strong> {{ $data['date_format'] ?? 'd-m-Y' }}</li>
                <li><strong>Timezone:</strong> {{ $data['timezone'] ?? config('app.timezone') }}</li>
                <li><strong>Terms:</strong> {{ $data['terms'] ?? '' }}</li>
                <li><strong>Notes:</strong> {{ $data['notes'] ?? '' }}</li>
            </ul>
        </div>

        <form action="{{ route('onboarding.complete') }}" method="POST" class="flex justify-between">
            @csrf
            <a href="{{ route('onboarding.step2') }}" class="text-blue-600">Back</a>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Complete</button>
        </form>
    </div>
@endsection
