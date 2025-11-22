<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Item represents a product or service that can appear on an invoice.
 */
class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'code',
        'type',
        'unit',
        'rate',
        'tax_percent',
        'description',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
