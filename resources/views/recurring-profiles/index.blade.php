@extends('layouts.app')

@section('title', 'Recurring Profiles')
@section('page_title', 'Recurring Profiles')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <form method="GET" action="{{ route('recurring-profiles.index') }}" class="w-full md:w-1/2">
            <label class="sr-only" for="search">Search recurring profiles</label>
            <div class="relative">
                <input
                    type="search"
                    id="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search by profile or customer name"
                    class="w-full border border-gray-300 rounded-lg py-2 pl-10 pr-3 text-sm focus:outline-none focus:ring focus:ring-blue-200"
                >
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                </span>
            </div>
        </form>
        <a href="{{ route('recurring-profiles.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
            + New Recurring Profile
        </a>
    </div>

    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Profile</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Customer</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Frequency</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Next Run</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Amount</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($profiles as $profile)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-900">{{ $profile->name }}</div>
                            <div class="text-xs text-gray-500">ID: {{ $profile->id }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-800">{{ $profile->customer?->name ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 capitalize">
                                {{ str_replace('-', ' ', $profile->frequency) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-800">
                            {{ optional($profile->next_run_date)->format('M d, Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900">
                            {{ $profile->amount ? number_format($profile->amount, 2) : '—' }}
                        </td>
                        <td class="px-4 py-3 space-y-2">
                            <a href="{{ route('recurring-profiles.prepare', $profile) }}" class="text-green-600 hover:underline text-sm">Prepare Draft</a>
                            <a href="{{ route('recurring-profiles.edit', $profile) }}" class="text-blue-600 hover:underline text-sm">Edit</a>
                            <form method="POST" action="{{ route('recurring-profiles.destroy', $profile) }}" onsubmit="return confirm('Delete this recurring profile?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            No recurring profiles found. Create your first recurring billing profile to automate invoices.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $profiles->links() }}
    </div>
@endsection
