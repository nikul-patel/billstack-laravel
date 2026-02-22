<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

/**
 * Controller for managing the product/service catalog.
 */
class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $business = $this->requireBusiness();
        $products = Product::where('business_id', $business->id)
            ->orderBy('name')
            ->paginate(20);

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $business = $this->requireBusiness();

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'unit'         => ['required', 'string', 'max:50'],
            'default_rate' => ['required', 'numeric', 'min:0'],
            'tax_rate'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'hsn_code'     => ['nullable', 'string', 'max:50'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        $data['business_id'] = $business->id;
        $data['is_active']   = $request->boolean('is_active', true);
        $data['tax_rate']    = $data['tax_rate'] ?? 0;

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $this->authorizeProduct($product);

        return view('products.create', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($product);

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'unit'         => ['required', 'string', 'max:50'],
            'default_rate' => ['required', 'numeric', 'min:0'],
            'tax_rate'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'hsn_code'     => ['nullable', 'string', 'max:50'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['tax_rate']  = $data['tax_rate'] ?? 0;

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $this->authorizeProduct($product);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    protected function authorizeProduct(Product $product): void
    {
        if ($this->userIsSuperAdmin()) {
            return;
        }

        $business = $this->currentBusiness();

        if ($product->business_id !== $business?->id) {
            abort(403);
        }
    }
}
