@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
    <div class="grid gap-6 md:grid-cols-3">
        <div class="bg-white shadow rounded p-6 md:col-span-2">
            <h2 class="text-xl font-semibold">Welcome, {{ auth()->user()->name }}</h2>
            @if($business)
                <p class="mt-2 text-gray-700">Business: {{ $business->name }} ({{ $business->currency ?? 'INR' }})</p>
                <p class="text-sm text-gray-500">Invoice prefix: {{ $business->invoice_prefix ?? 'None' }}</p>
            @else
                <p class="mt-2 text-red-600">Please complete onboarding to set up your business.</p>
            @endif
            @if(auth()->user()->isSuperAdmin())
                <div class="mt-4 flex flex-wrap gap-3 text-sm">
                    <a href="{{ route('admin.businesses.index') }}" class="text-blue-600">Manage Businesses</a>
                    <a href="{{ route('admin.users.index') }}" class="text-blue-600">Manage Users</a>
                </div>
            @endif
        </div>

        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold mb-2">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('customers.create') }}" class="block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Customer</a>
                <a href="{{ route('items.create') }}" class="block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Add Item</a>
                <a href="{{ route('invoices.create') }}" class="block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Create Invoice</a>
                <a href="{{ route('recurring-profiles.create') }}" class="block bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">New Recurring Profile</a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-3 mt-6">
        <div class="bg-white shadow rounded p-6">
            <h4 class="text-md font-semibold mb-2">Customers</h4>
            <p class="text-sm text-gray-600 mb-3">Manage customer contact details, GST/tax IDs, and billing addresses.</p>
            <a href="{{ route('customers.index') }}" class="text-blue-600 hover:underline">View customers</a>
        </div>
        <div class="bg-white shadow rounded p-6">
            <h4 class="text-md font-semibold mb-2">Items</h4>
            <p class="text-sm text-gray-600 mb-3">Maintain your products/services with prices and tax rates.</p>
            <a href="{{ route('items.index') }}" class="text-blue-600 hover:underline">View items</a>
        </div>
        <div class="bg-white shadow rounded p-6">
            <h4 class="text-md font-semibold mb-2">Invoices</h4>
            <p class="text-sm text-gray-600 mb-3">Create, email, and download PDF invoices for your customers.</p>
            <a href="{{ route('invoices.index') }}" class="text-blue-600 hover:underline">View invoices</a>
        </div>
        <div class="bg-white shadow rounded p-6">
            <h4 class="text-md font-semibold mb-2">Recurring Billing</h4>
            <p class="text-sm text-gray-600 mb-3">Automate monthly billing with recurring profiles.</p>
            <a href="{{ route('recurring-profiles.index') }}" class="text-blue-600 hover:underline">View recurring profiles</a>
        </div>
    </div>
@endsection
