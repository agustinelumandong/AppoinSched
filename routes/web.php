<?php

use App\Models\Offices;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Volt::route('admin/roles-permissions', 'admin.roles-permissions')->name('admin.roles-permissions');

    Volt::route('admin/users-management', 'admin.users.users-management')->name('admin.users.users-management');

    Volt::route('admin/offices', 'offices.offices')->name('admin.offices');

    Volt::route('admin/offices/{office:slug}', 'offices.show')->name('offices.show');

    Volt::route('admin/offices/{office:slug}/services', 'offices.services')->name('offices.services');

    Volt::route('admin/offices/{office:slug}/services/{service:slug}', 'offices.request')->name('offices.service.request');

    Volt::route('admin/services', 'services.services-management')->name('admin.services');

    Volt::route('admin/appointments-management', 'appointments.appointments-management')->name('admin.appointments-management');

    Volt::route('slot-picker', 'slot-picker')->name('slot-picker');



});





require __DIR__ . '/auth.php';
