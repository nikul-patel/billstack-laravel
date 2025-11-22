<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * RecurringProfile defines monthly or periodic billable items for a customer.
 */
class RecurringProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'customer_id',
        'name',
        'billing_frequency',
        'billing_day_of_month',
        'start_date',
        'end_date',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
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
        return $this->hasMany(RecurringProfileItem::class);
    }
}
