<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Handles business settings and dashboard.
 */
class BusinessController extends Controller
{
    /**
     * Show a basic dashboard for the current business.
     */
    public function dashboard()
    {
        // For now, return a simple view or JSON placeholder
        return response()->json(['message' => 'Dashboard coming soon']);
    }

    /**
     * Show the form for editing the business profile.
     */
    public function edit()
    {
        $business = Auth::user()->businesses->first();
        return view('business.edit', compact('business'));
    }

    /**
     * Update the business profile.
     */
    public function update(Request $request)
    {
        $business = Auth::user()->businesses->first();
        $business->update($request->all());
        return redirect()->route('business.profile.edit')->with('success', 'Profile updated successfully');
    }
}
