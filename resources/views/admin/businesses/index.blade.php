@extends('layouts.app')

@section('title', 'Businesses')
@section('page_title', 'Businesses')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('admin.businesses.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Add Business</a>
    </div>
    <div class="bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($businesses as $business)
                    <tr>
                        <td class="px-4 py-2">{{ $business->name }}</td>
                        <td class="px-4 py-2">{{ $business->email }}</td>
                        <td class="px-4 py-2">{{ $business->phone }}</td>
                        <td class="px-4 py-2 space-x-2">
                            <a href="{{ route('admin.businesses.edit', $business) }}" class="text-blue-600 text-sm">Edit</a>
                            <form action="{{ route('admin.businesses.destroy', $business) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 text-sm" onclick="return confirm('Delete this business?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $businesses->links() }}
    </div>
@endsection
