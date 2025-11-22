<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Business model represents a tenant in the system. Each business
 * may have multiple users, customers, items, invoices and payments.
 */
class Business extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'owner_id',
        'phone',
        'email',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'pincode',
        'country',
        'su_number',
        'village',
        'taluka',
        'district',
        'default_invoice_prefix',
        'next_invoice_number',
        'default_currency',
        'default_due_days',
        'default_template_key',
        'date_format',
        'timezone',
        'bank_name',
        'bank_account_no',
        'bank_ifsc',
        'upi_id',
        'footer_note',
    ];

    /**
     * Users belonging to the business.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }

    /**
     * Customers for this business.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Items (products or services) that belong to the business.
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Invoices issued by the business.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Payments recorded for this business.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
