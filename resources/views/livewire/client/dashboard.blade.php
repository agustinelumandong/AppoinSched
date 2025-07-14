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
            'upcomingAppointments' => Appointments::where('user_id', $user->id)
                ->where('booking_date', '>=', today())
                ->count(),
            'totalAppointments' => Appointments::where('user_id', $user->id)->count(),
            'offices' => Offices::take(6)->get(),
            // 'offices' => Offices::orderBy('created_at', 'DESC')->get(),
            'profileCompletion' => $user->getProfileCompletionPercentage(),
            'hasCompleteProfile' => $user->hasCompleteProfile(),
            'hasCompleteBasicProfile' => $user->hasCompleteBasicProfile(),
            'hasCompletePersonalInfo' => $user->hasCompletePersonalInfo(),
            'hasCompleteAddress' => $user->hasCompleteAddress(),
            'hasCompleteFamilyInfo' => $user->hasCompleteFamilyInfo(),
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

        <div class="mb-6">
            <h4 class="text-3xl font-bold text-gray-900">Available Offices</h4>
        </div>

        @if (!$hasCompleteProfile)
            <div id="alert" class="opacity-100 transition-all" x-data="{ show: true }" x-show="show">
                <div class="flux-card p-4 bg-yellow-50 border-yellow-200 shadow-lg rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            <div>
                                <span class="text-yellow-600 font-medium">Please complete your user information</span>
                                <div class="text-sm text-yellow-700 mt-1">
                                    Profile completion: {{ $profileCompletion }}% -
                                    <a href="{{ route('userinfo') }}" class="underline hover:no-underline">Complete
                                        Profile</a>
                                </div>
                            </div>
                        </div>
                        <button x-on:click="show = false" class="text-yellow-600 hover:text-yellow-800 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @else
            {{-- <div class="flux-card p-4 bg-green-50 border-green-200 shadow-lg rounded-lg mb-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-green-800 font-medium">Profile complete! You can now access appointments and document
                        requests.</span>
                </div>
            </div>
            --}}

            {{-- Offices --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                @forelse ($offices as $office)
                    <a href="{{ route('offices.show', $office->slug) }}"
                        class="relative flux-card p-4 border-blue-200 hover:bg-blue-50 hover:translate-y-[-10px] transition-all duration-300 shadow-lg rounded-lg cursor-pointer overflow-hidden block text-decoration-none text-black"
                        id="office-{{ $office->id }}">
                        <div class="absolute" style="top: -50px; right: -80px;">
                            <img src="{{ asset('storage/offices/' . $office->logo) }}" alt="{{ $office->name }} Logo"
                                class="rounded-full"
                                style="width: 300px; height: 300px; filter: drop-shadow(0 0 10px rgba(0, 0, 0, 0.5)); opacity: 0.1;">
                        </div>
                        {{-- Header --}}
                        <header class="flex flex-col ">
                            <div class="flex items-center justify-between">
                                <h5 class="text-lg font-bold   text-decoration-none text-black">{{ $office->name }}</h5>
                                {{-- <p class="text-lg   text-decoration-none text-black">{{ $office->services->count() }}
                                    Services
                                </p> --}}
                            </div>
                            <p class="text-sm text-gray-500 mt-2   text-decoration-none text-black">
                                {{ $office->description }}
                            </p>
                        </header>
                        {{-- Footer --}}
                        <footer class="flex items-center justify-between pt-10">
                            <span class="text-blue-50 hover:text-blue-700  text-decoration-none flux-btn flux-btn-primary">View
                                Office</span>
                        </footer>
                    </a>
                @empty
                    <div class="flux-card p-4 bg-red-50 border-red-200 shadow-lg rounded-lg">
                        <p class="text-gray-500">No offices found</p>
                        <p class="text-sm text-gray-500">You don't have any offices yet. Please create one to get
                            started.</p>
                    </div>
                @endforelse
            </div>

            {{-- Appointments Calendar --}}
            <div class="container-fluid mx-auto px-4 py-8">
                <div class="mb-6">
                    <h4 class="text-3xl font-bold text-gray-900">Appointments Calendar</h1>
                        <p class="text-gray-600 mt-2">View and filter appointments across different offices and staff
                        </p>
                </div>
                <livewire:components.full-calendar />
            </div>
        @endif

        {{-- Recent Appointments --}}
        {{-- <div class="bg-white rounded-lg shadow">
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
                                {{ $appointment->booking_time }}
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($appointment->booking_time)->format('h:i A') }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center">No appointments found. <a href="{{ route('admin.offices') }}"
                            class="text-blue-600 hover:underline">Book your
                            first appointment!</a></p>
                    @endforelse
                </div>
            </div>
        </div> --}}
    </div>
</div>