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
}
