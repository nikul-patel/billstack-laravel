<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for invoice report listing.
 */
class InvoiceReportController extends Controller
{
    /**
     * Display a listing of invoices with filters.
     */
    public function index(Request $request)
    {
        $business = Auth::user()->business;
        $query = Invoice::where('business_id', $business->id);
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }
        $invoices = $query->orderByDesc('invoice_date')->paginate(20);
        return view('reports.invoices', compact('invoices'));
    }
}
