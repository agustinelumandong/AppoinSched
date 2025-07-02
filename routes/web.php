<?php

use App\Models\Offices;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Dashboard redirector
Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    Volt::route('userinfo', 'user.userinfo')->name('userinfo');

    // SuperAdmin only routes
    Route::middleware(['role:super-admin'])->group(function () {
        Volt::route('admin/roles-permissions', 'admin.roles-permissions')->name('admin.roles-permissions');
        Volt::route('admin/system-management', 'admin.system-management')->name('admin.system-management');
    });

    // Admin and SuperAdmin routes
    Route::middleware(['role:admin,super-admin'])->group(function () {
        Volt::route('admin/dashboard', 'admin.dashboard')->name('admin.dashboard');
        Volt::route('admin/users-management', 'admin.users.users-management')->name('admin.users.users-management');
        Volt::route('admin/offices', 'offices.offices')->name('admin.offices');
        Volt::route('admin/services', 'services.services-management')->name('admin.services');
        Volt::route('admin/appointments-management', 'appointments.appointments-management')->name('admin.appointments-management');
        Volt::route('admin/document-request', 'documentrequest.document-request')->name('admin.document-request');
    });

    // Staff, Admin, and SuperAdmin routes
    Route::middleware(['role:MCR-staff,MTO-staff,BPLS-staff,admin,super-admin'])->group(function () {
        Volt::route('staff/dashboard', 'staff.dashboard')->name('staff.dashboard');
        Volt::route('staff/appointments', 'staff.appointments')->name('staff.appointments');
        Volt::route('staff/documents', 'staff.documents')->name('staff.documents');
        Volt::route('admin/view/document-request/{id}', 'documentrequest.view-document-request')->name('admin.view-document-request');
    });

    // Client routes (all authenticated users with complete profile)
    Route::middleware(['auth', 'verified', 'profile.complete'])->group(function () {
        Volt::route('client/dashboard', 'client.dashboard')->name('client.dashboard');
        Volt::route('client/appointments', 'client.appointments')->name('client.appointments');
        Volt::route('client/documents', 'client.documents')->name('client.documents');
        Volt::route('admin/offices/{office:slug}', 'offices.show')->name('offices.show');
        Volt::route('admin/offices/{office:slug}/services', 'offices.services')->name('offices.services');
        Volt::route('admin/offices/{office:slug}/services/{service:slug}', 'offices.request')->name('offices.service.request');
        Volt::route('offices/{office:slug}/services/appointments', 'offices.appointment')->name('offices.service.appointment');
    });

    // Testing route (remove in production)
    Volt::route('slot-picker', 'slot-picker')->name('slot-picker');
});

require __DIR__ . '/auth.php';
