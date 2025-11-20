<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip middleware for:
        // 1. Livewire requests (check header or path)
        // 2. AJAX/JSON requests (API calls, etc.)
        // 3. Non-authenticated requests
        if (
            $request->header('X-Livewire') ||
            $request->is('livewire/*') ||
            $request->expectsJson() ||
            $request->ajax()
        ) {
            return $next($request);
        }

        if (auth()->check()) {
            $user = auth()->user();

            // Allow access to password setup page and logout
            if ($request->routeIs('password.setup') || $request->routeIs('logout')) {
                return $next($request);
            }

            // Redirect to password setup if password is not set
            if (!$user->hasPasswordSet()) {
                return redirect()->route('password.setup');
            }
        }

        return $next($request);
    }
}
