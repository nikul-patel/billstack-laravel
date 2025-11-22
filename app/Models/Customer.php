<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Customer represents a party to whom invoices will be issued.
 */
class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'contact_person',
        'phone',
        'email',
        'billing_address_line_1',
        'billing_address_line_2',
        'city',
        'state',
        'pincode',
        'country',
        'gstin',
        'opening_balance',
        'notes',
        'default_template_key',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function recurringProfiles()
    {
        return $this->hasMany(RecurringProfile::class);
    }
}
