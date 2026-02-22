@extends('layouts.app')

@section('title', 'Products & Services')
@section('page_title', 'Products & Services')

@section('page_actions')
    <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-medium">
        + Add Product
    </a>
@endsection

@section('content')
    <div class="bg-white shadow rounded p-4">
        @if($products->isEmpty())
            <div class="text-center py-12" style="color:var(--brand-subtext)">
                <p class="text-lg mb-2">No products yet.</p>
                <p class="text-sm mb-4">Add products and services to your catalog to speed up invoice creation.</p>
                <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    Add your first product
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase">Unit</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase">Rate</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase">Tax %</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase">HSN Code</th>
                            <th class="px-4 py-3 text-center text-xs font-medium uppercase">Active</th>
                            <th class="px-4 py-3 text-center text-xs font-medium uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($products as $product)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $product->name }}</div>
                                    @if($product->description)
                                        <div class="text-xs" style="color:var(--brand-subtext)">{{ Str::limit($product->description, 60) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $product->unit }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($product->default_rate, 2) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($product->tax_rate, 2) }}%</td>
                                <td class="px-4 py-3">{{ $product->hsn_code ?? '—' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($product->is_active)
                                        <span class="inline-block px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-medium">Active</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded-full bg-gray-200 text-gray-600 text-xs font-medium">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center space-x-2">
                                    <a href="{{ route('products.edit', $product) }}" class="text-blue-500 hover:underline text-xs">Edit</a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline"
                                          onsubmit="return confirm('Delete this product?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline text-xs">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
