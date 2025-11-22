<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RecurringProfileController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Reports\InvoiceReportController;

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

Route::middleware('auth')->group(function () {
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
