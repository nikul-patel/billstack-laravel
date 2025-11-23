<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RecurringProfileController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Reports\InvoiceReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\OnboardingController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'business.setup'])->group(function () {
    // Onboarding
    Route::get('onboarding/step-1', [OnboardingController::class, 'step1'])->name('onboarding.step1');
    Route::post('onboarding/step-1', [OnboardingController::class, 'step1Store'])->name('onboarding.step1.store');
    Route::get('onboarding/step-2', [OnboardingController::class, 'step2'])->name('onboarding.step2');
    Route::post('onboarding/step-2', [OnboardingController::class, 'step2Store'])->name('onboarding.step2.store');
    Route::get('onboarding/review', [OnboardingController::class, 'review'])->name('onboarding.review');
    Route::post('onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');

    // Dashboard
    Route::get('/dashboard', [BusinessController::class, 'dashboard'])->name('dashboard');

    // Business profile
    Route::get('business/profile', [BusinessController::class, 'edit'])->name('business.profile.edit');
    Route::put('business/profile', [BusinessController::class, 'update'])->name('business.profile.update');

    // Resources
    Route::resource('customers', CustomerController::class);
    Route::resource('items', ItemController::class);
    Route::resource('recurring-profiles', RecurringProfileController::class);
    Route::resource('invoices', InvoiceController::class);

    // Recurring profiles: generate invoices
    Route::post('recurring-profiles/{profile}/generate-invoices', [RecurringProfileController::class, 'generateInvoices'])
        ->name('recurring-profiles.generate-invoices');

    // Invoice actions
    Route::post('invoices/{invoice}/send', [InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');

    // Payments
    Route::post('invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('invoices.payments.store');
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    // Reports
    Route::get('reports/invoices', [InvoiceReportController::class, 'index'])->name('reports.invoices');
});
