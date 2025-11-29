<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function currentBusiness(): ?Business
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        return $user->activeBusiness();
    }

    protected function requireBusiness(): Business
    {
        $business = $this->currentBusiness();

        if (! $business) {
            abort(400, 'Please select a business to continue.');
        }

        return $business;
    }

    protected function userIsSuperAdmin(): bool
    {
        return Auth::user()?->isSuperAdmin() === true;
    }
}
