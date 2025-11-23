<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Handles business settings and dashboard.
 * SSR alignment: enforces single-tenant context (business_id) and editable business preferences per SSR docs.
 */
class BusinessController extends Controller
{
    /**
     * Show a basic dashboard for the current business.
     */
    public function dashboard()
    {
        $business = Auth::user()->business;

        return view('dashboard', compact('business'));
    }

    /**
     * Show the form for editing the business profile.
     */
    public function edit()
    {
        $business = Auth::user()->business;
        return view('business.edit', compact('business'));
    }

    /**
     * Update the business profile.
     */
    public function update(Request $request)
    {
        $business = Auth::user()->business;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'gst_number' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'invoice_prefix' => ['nullable', 'string', 'max:50'],
            'invoice_start_no' => ['nullable', 'integer', 'min:1'],
            'currency' => ['nullable', 'string', 'max:10'],
            'date_format' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'terms' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $business->update($validated);
        return redirect()->route('business.profile.edit')->with('success', 'Profile updated successfully');
    }
}
