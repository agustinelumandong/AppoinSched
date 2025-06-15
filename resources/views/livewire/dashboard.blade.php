<?php

use App\Models\Offices;
use App\Models\Services;
use Livewire\Volt\Component;
use Livewire\Attributes\{Title};

new #[Title('Dashboard')]
    class extends Component {


    public function with()
    {
        return [
            'offices' => Offices::orderBy('created_at', 'DESC')->get(),
        ];
    }
}; ?>

<div>

    <link rel="stylesheet" href="{{ asset('css/fluxUI.css') }}">
    <h1>Dashboard</h1>
    <p class="text-gray-600 text-sm">Welcome to your dashboard. Here you can manage your offices and services.</p>
    <div id="alert" class="opacity-100 transition-all" x-data="{ show: true }" x-show="show">
        <div class="flux-card p-4 bg-yellow-50 border-yellow-200 shadow-lg rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <span class="text-yellow-600 font-medium">Please complete your user information</span>
                </div>
                <button x-on:click="show = false" class="text-yellow-600 hover:text-yellow-800 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

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
                        <p class="text-lg   text-decoration-none text-black">{{ $office->services->count() }} Services</p>
                    </div>
                    <p class="text-sm text-gray-500 mt-2   text-decoration-none text-black">{{ $office->description }}</p>
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
                <p class="text-sm text-gray-500">You don't have any offices yet. Please create one to get started.</p>
            </div>
        @endforelse
    </div>
</div>