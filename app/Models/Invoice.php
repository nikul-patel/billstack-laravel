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
     * Recalculate and persist invoice totals from stored line-item values.
     *
     * This method derives subtotal, tax_total, and grand_total exclusively
     * from the immutable snapshot values stored on InvoiceItem records,
     * ensuring historical invoices remain unaffected by product price changes.
     */
    public function recalculateTotalsFromItems(): self
    {
        $this->loadMissing('items');

        $subtotal = 0;
        $taxTotal = 0;

        foreach ($this->items as $item) {
            $subtotal += (float) $item->line_total;
            $taxTotal += (float) $item->tax_amount;
        }

        $this->subtotal = $subtotal;
        $this->tax_total = $taxTotal;
        $this->tax_amount = $taxTotal;
        $this->grand_total = $subtotal + $taxTotal - (float) ($this->discount_value ?? 0);
        $this->amount_due = max(0, $this->grand_total - (float) ($this->amount_paid ?? 0));

        return $this;
    }

    /**
     * Get the calculated subtotal from stored line items.
     *
     * Returns the sum of line_total from all InvoiceItem records,
     * ensuring totals are derived from immutable snapshot values.
     */
    public function getCalculatedSubtotal(): float
    {
        $this->loadMissing('items');

        return (float) $this->items->sum('line_total');
    }

    /**
     * Get the calculated tax total from stored line items.
     *
     * Returns the sum of tax_amount from all InvoiceItem records,
     * ensuring totals are derived from immutable snapshot values.
     */
    public function getCalculatedTaxTotal(): float
    {
        $this->loadMissing('items');

        return (float) $this->items->sum('tax_amount');
    }

    /**
     * Get the calculated grand total from stored line items.
     *
     * Returns subtotal + tax - discount, derived exclusively from
     * stored InvoiceItem snapshot values.
     */
    public function getCalculatedGrandTotal(): float
    {
        return $this->getCalculatedSubtotal()
            + $this->getCalculatedTaxTotal()
            - (float) ($this->discount_value ?? 0);
    }
}
