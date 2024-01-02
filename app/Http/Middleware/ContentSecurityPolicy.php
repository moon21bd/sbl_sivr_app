<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Set Content Security Policy headers
        // $response->header('Content-Security-Policy', "default-src 'self'");
        $response->header('Content-Security-Policy', "default-src 'self'; style-src 'self' 'unsafe-inline'");
        return $response;
    }
}
