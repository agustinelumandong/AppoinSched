<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
  public function handle(Request $request, Closure $next, ...$roles): Response
  {
    $user = $request->user();

    if (!$user) {
      return redirect()->route('login');
    }

    // Check if user has any of the required roles
    if (!$user->hasAnyRole($roles)) {
      abort(403, 'Unauthorized access. You do not have permission to access this page.');
    }

    return $next($request);
  }
}