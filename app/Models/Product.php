<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Product represents a product or service in the business catalog.
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'unit',
        'default_rate',
        'tax_rate',
        'hsn_code',
        'is_active',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'default_rate' => 'decimal:2',
        'tax_rate'     => 'decimal:2',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Scope to only active products for the given business.
     */
    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
