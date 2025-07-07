<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileCompletion
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    $user = $request->user();

    if (!$user) {
      return redirect()->route('login');
    }

    // Check if user has complete profile
    if (!$user->hasCompleteProfile()) {
      session()->flash('warning', 'Please complete your profile information before proceeding.');
      return redirect()->route('userinfo');
    }

    return $next($request);
  }
}