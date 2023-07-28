<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

/*class VerifyLogin
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

}*/

class VerifyLogin
{
    public function handle($request, Closure $next)
    {
        // Whitelist of routes to exclude from the login check
        $whitelist = ['send-otp', 'verify-otp', 'otp-wrap', 'verify-wrap'];

        if (in_array($request->path(), $whitelist)) {
            return $next($request);
        }

        // Check if the user is logged in
        if (!Session::has('logInfo')) {
            // User is not logged in, redirect to complete the OTP verification cycle
            return redirect('/send-otp');
        }

        // Check for logout request
        if ($request->path() === 'logout') {
            // Perform the logout action
            $this->logoutUser();

            // Redirect the user to the desired location after logout
            // For example, you can redirect them to the login page
            return redirect('/login');
        }

        return $next($request);
    }

    // Function to perform user logout action
    private function logoutUser()
    {
        // Add any necessary cleanup or logout procedures here
        // For example, clear the user session data or perform any other actions

        // Set 'is_logged_in' to false in the session
        Session::put('logInfo', null);
    }
}
