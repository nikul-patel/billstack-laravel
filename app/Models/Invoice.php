<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Invoice represents a bill issued to a customer with multiple line items.
 */
class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'customer_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'status',
        'currency',
        'template_key',
        'duration_text',
        'period_from',
        'period_to',
        'subtotal',
        'discount_type',
        'discount_value',
        'tax_total',
        'tax_amount',
        'round_off',
        'grand_total',
        'amount_paid',
        'amount_due',
        'notes',
        'terms',
        'public_hash',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'period_from' => 'date',
        'period_to' => 'date',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Calculate the subtotal from stored line item values.
     *
     * Uses the immutable line_total stored on each invoice item,
     * ensuring product price changes don't affect historical invoices.
     */
    public function calculateSubtotal(): float
    {
        return (float) $this->items()->sum('line_total');
    }

    /**
     * Calculate the tax total from stored line item values.
     *
     * Uses the immutable tax_amount stored on each invoice item,
     * ensuring product price changes don't affect historical invoices.
     */
    public function calculateTaxTotal(): float
    {
        return (float) $this->items()->sum('tax_amount');
    }

    /**
     * Calculate the grand total from stored line item values.
     *
     * Derives total exclusively from immutable invoice line-item snapshot data.
     */
    public function calculateGrandTotal(): float
    {
        return $this->calculateSubtotal() + $this->calculateTaxTotal();
    }

    /**
     * Recalculate and persist totals from stored line item values.
     *
     * This ensures invoice totals are always derived from immutable
     * line-item snapshot data rather than current product prices.
     */
    public function recalculateTotals(): self
    {
        $this->subtotal = $this->calculateSubtotal();
        $this->tax_total = $this->calculateTaxTotal();
        $this->tax_amount = $this->tax_total;
        $this->grand_total = $this->calculateGrandTotal();
        $this->amount_due = max(0, $this->grand_total - ($this->amount_paid ?? 0));
        $this->save();

        return $this;
    }
}
