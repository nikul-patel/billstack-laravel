<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Payment records a payment against an invoice.
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'invoice_id',
        'amount',
        'paid_at',
        'method',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
