<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
  public function index(Request $request): RedirectResponse
  {
    $user = $request->user();

    if ($user->hasRole('super-admin')) {
      return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('admin')) {
      return redirect()->route('admin.dashboard');
    }

    if ($user->hasAnyRole(['MCR-staff', 'MTO-staff', 'BPLS-staff'])) {
      return redirect()->route('staff.dashboard');
    }

    if ($user->hasRole('client')) {
      return redirect()->route('client.dashboard');
    }

    // Fallback for users without roles
    $user->assignRole('client');
    return redirect()->route('client.dashboard');
  }
}