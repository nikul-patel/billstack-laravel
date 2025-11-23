<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for managing products/services (items).
 * SSR alignment: tenant-scoped CRUD with validation, respecting business_id per SSR requirements.
 */
class ItemController extends Controller
{
    /**
     * Display a listing of the items.
     */
    public function index()
    {
        $business = Auth::user()->business;
        $items = Item::where('business_id', $business->id)->paginate(20);
        return view('items.index', compact('items'));
    }

    /**
     * Show the form for creating a new item.
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created item in storage.
     */
    public function store(Request $request)
    {
        $business = Auth::user()->business;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric'],
            'tax_rate' => ['nullable', 'numeric'],
            'unit' => ['nullable', 'string', 'max:50'],
        ]);

        $data['business_id'] = $business->id;
        Item::create($data);
        return redirect()->route('items.index')->with('success', 'Item created successfully');
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    /**
     * Update the specified item in storage.
     */
    public function update(Request $request, Item $item)
    {
        $this->authorizeItem($item);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric'],
            'tax_rate' => ['nullable', 'numeric'],
            'unit' => ['nullable', 'string', 'max:50'],
        ]);

        $item->update($data);
        return redirect()->route('items.index')->with('success', 'Item updated successfully');
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item)
    {
        $this->authorizeItem($item);
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted successfully');
    }

    protected function authorizeItem(Item $item): void
    {
        $business = Auth::user()->business;

        if ($item->business_id !== $business?->id) {
            abort(403);
        }
    }
}
