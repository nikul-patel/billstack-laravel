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
        'frequency',
        'next_run_date',
        'day_of_month',
        'amount',
        'notes',
    ];

    protected $casts = [
        'next_run_date' => 'date',
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
