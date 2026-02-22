@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
    @if($business)
        {{-- KPI Cards --}}
        <div class="grid gap-4 md:grid-cols-3 mb-6">
            <div class="bg-white shadow rounded p-5">
                <p class="text-xs uppercase tracking-widest mb-1" style="color:var(--brand-subtext)">Revenue This Month</p>
                <p class="text-2xl font-bold text-green-400">
                    {{ $business->currency ?? '₹' }} {{ number_format($totalRevenueThisMonth ?? 0, 2) }}
                </p>
                <p class="text-xs mt-1" style="color:var(--brand-subtext)">Paid invoices in {{ now()->format('F Y') }}</p>
            </div>
            <div class="bg-white shadow rounded p-5">
                <p class="text-xs uppercase tracking-widest mb-1" style="color:var(--brand-subtext)">Outstanding Amount</p>
                <p class="text-2xl font-bold text-yellow-400">
                    {{ $business->currency ?? '₹' }} {{ number_format($outstandingAmount ?? 0, 2) }}
                </p>
                <p class="text-xs mt-1" style="color:var(--brand-subtext)">Unpaid &amp; partially paid invoices</p>
            </div>
            <div class="bg-white shadow rounded p-5">
                <p class="text-xs uppercase tracking-widest mb-1" style="color:var(--brand-subtext)">Overdue Invoices</p>
                <p class="text-2xl font-bold {{ ($overdueCount ?? 0) > 0 ? 'text-red-400' : 'text-green-400' }}">
                    {{ $overdueCount ?? 0 }}
                </p>
                <p class="text-xs mt-1" style="color:var(--brand-subtext)">Past due date, not yet paid</p>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 mb-6">
            {{-- Monthly Revenue Bar Chart --}}
            <div class="bg-white shadow rounded p-5">
                <h3 class="text-md font-semibold mb-4">Revenue — Last 6 Months</h3>
                @if(!empty($monthlyRevenue))
                    @php
                        $maxRevenue = max(array_column($monthlyRevenue, 'revenue'));
                        $maxRevenue = $maxRevenue > 0 ? $maxRevenue : 1;
                    @endphp
                    <div class="flex items-end gap-2" style="height: 120px;">
                        @foreach($monthlyRevenue as $month)
                            @php
                                $pct = ($month['revenue'] / $maxRevenue) * 100;
                                $pct = max($pct, 2);
                            @endphp
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <span class="text-xs font-medium" style="color:var(--brand-subtext); font-size:10px;">
                                    @if($month['revenue'] > 0)
                                        @if($month['revenue'] >= 1000)
                                            {{ number_format($month['revenue'] / 1000, 0) }}k
                                        @else
                                            {{ number_format($month['revenue'], 0) }}
                                        @endif
                                    @endif
                                </span>
                                <div class="w-full rounded-t flex-1"
                                     style="height: {{ $pct }}%; background: linear-gradient(to top, #4338ca, #06b6d4); min-height: 3px; max-height: 90px;">
                                </div>
                                <span style="color:var(--brand-subtext); font-size:10px;">{{ explode(' ', $month['month'])[0] }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color:var(--brand-subtext)" class="text-sm">No revenue data available yet.</p>
                @endif
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white shadow rounded p-5">
                <h3 class="text-md font-semibold mb-3">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('invoices.create') }}"
                       class="block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-medium text-center">
                        + Create Invoice
                    </a>
                    <a href="{{ route('customers.create') }}"
                       class="block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm font-medium text-center">
                        + Add Customer
                    </a>
                    <a href="{{ route('products.create') }}"
                       class="block bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700 text-sm font-medium text-center">
                        + Add Product
                    </a>
                    <a href="{{ route('recurring-profiles.create') }}"
                       class="block bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 text-sm font-medium text-center">
                        + New Recurring Profile
                    </a>
                    <a href="{{ route('business.profile.edit') }}"
                       class="block text-center border border-gray-400 px-4 py-2 rounded text-sm"
                       style="color:var(--text-color)">
                        Business Settings
                    </a>
                </div>
                @if(auth()->user()->isSuperAdmin())
                    <div class="mt-4 pt-3 border-t border-gray-200 space-y-1 text-sm">
                        <a href="{{ route('admin.businesses.index') }}" class="block text-blue-400 hover:underline">Manage Businesses</a>
                        <a href="{{ route('admin.users.index') }}" class="block text-blue-400 hover:underline">Manage Users</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent Invoices --}}
        <div class="bg-white shadow rounded p-5">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-md font-semibold">Recent Invoices</h3>
                <a href="{{ route('invoices.index') }}" class="text-blue-400 text-sm hover:underline">View all</a>
            </div>
            @if(isset($recentInvoices) && $recentInvoices->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium uppercase">Invoice #</th>
                                <th class="px-3 py-2 text-left text-xs font-medium uppercase">Customer</th>
                                <th class="px-3 py-2 text-left text-xs font-medium uppercase">Date</th>
                                <th class="px-3 py-2 text-right text-xs font-medium uppercase">Total</th>
                                <th class="px-3 py-2 text-right text-xs font-medium uppercase">Balance</th>
                                <th class="px-3 py-2 text-center text-xs font-medium uppercase">Status</th>
                                <th class="px-3 py-2 text-center text-xs font-medium uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($recentInvoices as $inv)
                                @php
                                    $statusColors = [
                                        'draft'     => 'bg-gray-200 text-gray-700',
                                        'sent'      => 'bg-blue-100 text-blue-700',
                                        'partial'   => 'bg-yellow-100 text-yellow-800',
                                        'paid'      => 'bg-green-100 text-green-700',
                                        'overdue'   => 'bg-red-100 text-red-700',
                                        'cancelled' => 'bg-gray-400 text-white',
                                    ];
                                    $sc = $statusColors[$inv->status ?? 'draft'] ?? 'bg-gray-200 text-gray-700';
                                @endphp
                                <tr>
                                    <td class="px-3 py-2 font-medium">{{ $inv->invoice_number }}</td>
                                    <td class="px-3 py-2">{{ $inv->customer?->name ?? '—' }}</td>
                                    <td class="px-3 py-2" style="color:var(--brand-subtext)">
                                        {{ $inv->invoice_date?->format('d M Y') }}
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        {{ number_format($inv->grand_total ?? 0, 2) }}
                                    </td>
                                    <td class="px-3 py-2 text-right text-red-400 font-medium">
                                        {{ number_format($inv->amount_due ?? 0, 2) }}
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc }}">
                                            {{ ucfirst($inv->status ?? 'draft') }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <a href="{{ route('invoices.show', $inv) }}" class="text-blue-400 text-xs hover:underline">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color:var(--brand-subtext)" class="text-sm">
                    No invoices yet.
                    <a href="{{ route('invoices.create') }}" class="text-blue-400 hover:underline">Create your first invoice.</a>
                </p>
            @endif
        </div>

        {{-- Nav cards --}}
        <div class="grid gap-4 md:grid-cols-4 mt-6">
            <div class="bg-white shadow rounded p-4">
                <h4 class="text-sm font-semibold mb-1">Customers</h4>
                <p class="text-xs mb-2" style="color:var(--brand-subtext)">Manage contacts, GST IDs, and billing.</p>
                <a href="{{ route('customers.index') }}" class="text-blue-400 text-xs hover:underline">View all</a>
            </div>
            <div class="bg-white shadow rounded p-4">
                <h4 class="text-sm font-semibold mb-1">Products</h4>
                <p class="text-xs mb-2" style="color:var(--brand-subtext)">Catalog of products &amp; services.</p>
                <a href="{{ route('products.index') }}" class="text-blue-400 text-xs hover:underline">View catalog</a>
            </div>
            <div class="bg-white shadow rounded p-4">
                <h4 class="text-sm font-semibold mb-1">Items</h4>
                <p class="text-xs mb-2" style="color:var(--brand-subtext)">Legacy items with prices and tax rates.</p>
                <a href="{{ route('items.index') }}" class="text-blue-400 text-xs hover:underline">View items</a>
            </div>
            <div class="bg-white shadow rounded p-4">
                <h4 class="text-sm font-semibold mb-1">Reports</h4>
                <p class="text-xs mb-2" style="color:var(--brand-subtext)">Invoice reports and summaries.</p>
                <a href="{{ route('reports.invoices') }}" class="text-blue-400 text-xs hover:underline">View reports</a>
            </div>
        </div>
    @else
        <div class="bg-white shadow rounded p-6">
            <h2 class="text-xl font-semibold">Welcome, {{ auth()->user()->name }}</h2>
            <p class="mt-2 text-red-400">Please complete onboarding to set up your business.</p>
            <a href="{{ route('onboarding.step1') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                Start Onboarding
            </a>
        </div>
    @endif
@endsection
