<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\Appointments;
use App\Models\Offices;

new class extends Component {
    public function mount()
    {
        if (!auth()->user()->hasRole('client')) {
            abort(403, 'Unauthorized to access client dashboard');
        }
    }

    public function with()
    {
        $user = auth()->user();
        return [
            'myAppointments' => Appointments::where('user_id', $user->id)
                ->with(['office', 'service'])
                ->latest()
                ->take(5)
                ->get(),
            'upcomingAppointments' => Appointments::where('user_id', $user->id)->where('booking_date', '>=', today())->where('status', 'approved')->count(),
            'totalAppointments' => Appointments::where('user_id', $user->id)->count(),
            'offices' => Offices::take(6)->get(),
        ];
    }
}; ?>

<div title="Client Dashboard">
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Welcome, {{ auth()->user()->first_name }}!</h1>
                <p class="text-gray-600">Book appointments and manage your requests</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Appointments</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalAppointments }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Upcoming Appointments</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $upcomingAppointments }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Available Offices</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $offices->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Available Offices</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($offices as $office)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <h4 class="font-semibold text-gray-900">{{ $office->name }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $office->description }}</p>
                            <a href="{{ route('offices.show', $office->slug) }}" class="btn btn-sm btn-primary mt-3">
                                Book Appointment
                            </a>
                        </div>
                    @empty
                        <p class="text-gray-500 col-span-3 text-center">No offices available</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Appointments</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($myAppointments as $appointment)
                        <div class="flex items-center justify-between p-4 border rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $appointment->service->name ?? 'N/A' }}</h4>
                                <p class="text-sm text-gray-600">{{ $appointment->office->name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $appointment->booking_date }} at
                                    {{ $appointment->booking_time }}</p>
                            </div>
                            <div class="text-right">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if ($appointment->status === 'pending') bg-yellow-100 text-yellow-800
            @elseif($appointment->status === 'approved') bg-green-100 text-green-800
            @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
            @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                                @if ($appointment->status === 'pending')
                                    <button class="btn btn-sm btn-outline mt-2">Cancel</button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center">No appointments found. <a
                                href="{{ route('admin.offices') }}" class="text-blue-600 hover:underline">Book your
                                first appointment!</a></p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
