<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function step1(): View
    {
        return view('onboarding.step1', [
            'data' => session('onboarding', []),
        ]);
    }

    public function step1Store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'gst_number' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'pincode' => ['nullable', 'string', 'max:20'],
            'invoice_prefix' => ['nullable', 'string', 'max:50'],
            'invoice_start_no' => ['nullable', 'integer', 'min:1'],
        ]);

        session(['onboarding' => array_merge(session('onboarding', []), $data)]);

        return redirect()->route('onboarding.step2');
    }

    public function step2(): View
    {
        return view('onboarding.step2', [
            'data' => session('onboarding', []),
        ]);
    }

    public function step2Store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'currency' => ['nullable', 'string', 'max:10'],
            'date_format' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'terms' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        session(['onboarding' => array_merge(session('onboarding', []), $data)]);

        return redirect()->route('onboarding.review');
    }

    public function review(): View
    {
        return view('onboarding.review', [
            'data' => session('onboarding', []),
        ]);
    }

    public function complete(Request $request): RedirectResponse
    {
        $data = session('onboarding', []);

        if (empty($data)) {
            return redirect()->route('onboarding.step1');
        }

        $user = Auth::user();

        $business = Business::create(array_merge([
            'invoice_prefix' => $data['invoice_prefix'] ?? null,
            'invoice_start_no' => $data['invoice_start_no'] ?? 1,
            'currency' => $data['currency'] ?? 'INR',
            'date_format' => $data['date_format'] ?? 'd-m-Y',
            'timezone' => $data['timezone'] ?? config('app.timezone'),
        ], $data, [
            'owner_id' => $user->id,
        ]));

        $user->business()->associate($business);
        $user->save();

        session()->forget('onboarding');

        return redirect()->route('dashboard')->with('success', 'Business created successfully.');
    }
}
