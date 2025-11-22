<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * RecurringProfileItem represents a line item in a recurring profile.
 */
class RecurringProfileItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'recurring_profile_id',
        'item_id',
        'name',
        'description',
        'rate',
        'quantity',
        'tax_percent',
        'sort_order',
    ];

    public function recurringProfile()
    {
        return $this->belongsTo(RecurringProfile::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
