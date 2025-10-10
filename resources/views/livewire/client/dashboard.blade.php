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
                ->with(['office'])
                ->latest()
                ->take(5)
                ->get(),
            'upcomingAppointments' => Appointments::where('user_id', $user->id)->where('booking_date', '>=', today())->count(),
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

    @include('components.alert')

    <div class="space-y-6">
        {{-- <div class="mb-1 flex flex-row gap-4">
            <div class="w-full">
                <h4 class="text-lg font-bold text-gray-900">Available Offices</h4>
            </div>
        </div> --}}

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
            <div class="flex flex-col w-full gap-4">
                {{-- Offices --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @forelse ($offices as $office)
                        <a href="{{ route('offices.show', $office->slug) }}"
                            class="flux-office-card relative p-4 border-blue-200 hover:bg-blue-50 hover:-translate-y-2 transition-all duration-300 shadow-lg rounded-lg cursor-pointer overflow-hidden block text-black no-underline"
                            id="office-{{ $office->id }}">
                            <div class="absolute" style="top: -50px; right: -80px;">
                                @php
                                    $logo = $office->logo;
                                    if (!empty($logo)) {
                                        $logo = asset('storage/offices/' . $office->logo);
                                    }
                                    if ($office->slug === 'municipal-civil-registrar') {
                                        $logo = asset('images/MCR_logo.jpg');
                                    } elseif ($office->slug === 'municipal-treasurers-office') {
                                        $logo = asset('images/MTO_logo.jpg');
                                    } elseif ($office->slug === 'business-permits-and-licensing-section') {
                                        $logo = asset('images/BPLS_logo.jpg');
                                    }
                                @endphp
                                <img src="{{ $logo }}" alt="{{ $office->name }} Logo" class="rounded-full"
                                    style="width: 300px; height: 300px; filter: drop-shadow(0 0 10px rgba(0, 0, 0, 0.5)); opacity: 0.1;">
                            </div>
                            {{-- Header --}}
                            <header class="flex flex-col">
                                <div class="flex items-center justify-between">
                                    <h5 class="text-lg font-bold text-black">{{ $office->name }}</h5>
                                </div>
                                <p class="text-sm text-gray-500 mt-2 text-black">
                                    {{ $office->description }}
                                </p>
                            </header>
                            {{-- Footer --}}
                            <footer class="flex items-center justify-between pt-2">
                                <span class="text-blue-50 hover:text-blue-700 flux-btn flux-btn-primary">View Office</span>
                            </footer>
                        </a>
                    @empty
                        <div class="flux-card p-4 bg-red-50 border-red-200 shadow-lg rounded-lg col-span-full">
                            <p class="text-gray-500">No offices found</p>
                            <p class="text-sm text-gray-500">You don't have any offices yet. Please create one to get started.
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- Appointments Calendar --}}
                {{-- <div class="w-full">
                    <h4 class="text-lg font-bold text-gray-900">Appointments Calendar</h4>
                </div> --}}
                <div class="w-full">
                    <livewire:components.full-calendar :calendartitle="'Appointments Calendar'" />
                </div>
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
    
<style>
    .flux-office-card {
        background: linear-gradient(90deg, #C4D5FF 4%, #fff 100%);
        /* height: 230px; */
        height: fit-content;
        text-decoration: none;
    }
</style>
</div>
