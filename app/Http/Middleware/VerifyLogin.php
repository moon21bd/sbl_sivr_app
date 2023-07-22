<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class VerifyLogin
{
    public function handle($request, Closure $next)
    {
        // Whitelist of routes to exclude from the login check
        $whitelist = ['send-otp', 'verify-otp', 'otp-wrap', 'verify-wrap'];

        if (in_array($request->path(), $whitelist)) {
            return $next($request);
        }

        if (!Session::has('logInfo')) {
            // User is not logged in, redirect to complete the OTP verification cycle
            return redirect('/send-otp');
        }

        return $next($request);
    }

}
