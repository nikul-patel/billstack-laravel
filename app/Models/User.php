<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'business_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected ?Business $resolvedActiveBusiness = null;

    protected bool $hasResolvedActiveBusiness = false;

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function activeBusiness(): ?Business
    {
        if ($this->hasResolvedActiveBusiness) {
            return $this->resolvedActiveBusiness;
        }

        $business = $this->business;

        if ($this->isSuperAdmin()) {
            $businessId = session('active_business_id');
            $business = $businessId
                ? Business::find($businessId)
                : Business::query()->orderBy('name')->first();
        }

        $this->resolvedActiveBusiness = $business;
        $this->hasResolvedActiveBusiness = true;

        return $this->resolvedActiveBusiness;
    }
}
