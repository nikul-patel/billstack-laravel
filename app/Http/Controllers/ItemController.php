<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for managing products/services (items).
 */
class ItemController extends Controller
{
    /**
     * Display a listing of the items.
     */
    public function index()
    {
        $business = Auth::user()->businesses->first();
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
        $business = Auth::user()->businesses->first();
        $data = $request->all();
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
        $item->update($request->all());
        return redirect()->route('items.index')->with('success', 'Item updated successfully');
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted successfully');
    }
}
