<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $business = $this->requireBusiness();

        return view('items.index', compact('business'));
    }

    public function datatable(): JsonResponse
    {
        $business = $this->requireBusiness();
        $items = Item::query()
            ->where('business_id', $business->id)
            ->select(['id', 'name', 'price', 'tax_rate', 'unit'])
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $items]);
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
        $business = $this->requireBusiness();

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
        $this->authorizeItem($item);

        return view('items.create', compact('item'));
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
        if ($this->userIsSuperAdmin()) {
            return;
        }

        $business = $this->currentBusiness();

        if ($item->business_id !== $business?->id) {
            abort(403);
        }
    }
}
