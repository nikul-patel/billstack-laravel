<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBusinessSetup
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && is_null($user->business_id) && ! $request->routeIs('onboarding.*')) {
            return redirect()->route('onboarding.step1');
        }

        return $next($request);
    }
}
