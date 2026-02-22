<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * InvoiceItem represents a line item on an invoice.
 */
class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'item_id',
        'name',
        'description',
        'hsn_code',
        'rate',
        'quantity',
        'tax_percent',
        'tax_rate',
        'tax_amount',
        'line_total',
        'sort_order',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
