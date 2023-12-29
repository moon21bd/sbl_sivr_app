<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckWalletAccess
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $accountVerificationStatus = Session::get('account_verification_status', false);

        if ($accountVerificationStatus === true) {
            // Allow access to the route
            return $next($request);
        }

        return redirect(url('/'))
            ->with('status', 'error')
            ->with('message', __('messages.service-access-denied'));

    }
}
