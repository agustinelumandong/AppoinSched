<?php

use Livewire\Volt\Component;
use App\Models\Offices;
use App\Models\Services;
use Illuminate\Support\Str;
use Livewire\Attributes\{Title};

new #[Title('Office Details')]
    class extends Component {
    public Offices $office;
    public $services;

    public function mount(Offices $office)
    {
        $this->office = $office;
        $this->services = $office->services()->where('is_active', 1)->get();
    }

}; ?>

<div>
    <link rel="stylesheet" href="{{ asset('css/fluxUI.css') }}">

    <!-- Flash Messages -->
    @include('components.alert')

    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">{{ $office->name }}</h1>
            <p class="mb-0 text-muted text-sm">{{ $office->description ?? 'No description available' }}</p>
        </div>
        <div class=" ml-3">
            <a href="{{ route('dashboard') }}" class="flux-btn flux-btn-outline text-decoration-none text-black">
                <i class="bi bi-arrow-left me-1"></i>
            </a>
        </div>
    </div>

    <!-- Office Details Card -->


    <div class="row mt-4">
        <!-- Office Information -->
        <div class="col-md-12">
            <h4 class="mb-3">Services Offered</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <a href="{{ route('offices.service.appointment', ['office' => $office->slug]) }}"
                    class="relative flux-card p-4 border-blue-200 hover:bg-blue-50 hover:translate-y-[-10px] transition-all duration-300 shadow-lg rounded-lg cursor-pointer overflow-hidden block text-decoration-none text-black">
                    {{-- Header --}}
                    <header class="flex flex-col ">
                        <h3 class="text-lg font-bold  text-decoration-none text-black ">
                            Appointment Booking
                        </h3>
                        <p class="text-sm text-gray-500  text-decoration-none text-black ">
                            Book an appointment with the office
                        </p>
                        <p class="text-sm text-gray-800 font-bold  text-decoration-none text-black">
                            Free
                        </p>
                    </header>
                    {{-- Footer --}}
                    <footer class="flex items-center justify-between pt-10">
                        <span
                            class="text-blue-50 hover:text-blue-700  text-decoration-none  flux-btn flux-btn-primary">Request
                            Book Appointment</span>
                    </footer>
                </a>

                @forelse($services as $service)
                    <a href="{{ route('offices.service.request', ['office' => $office->slug, 'service' => $service->slug]) }}"
                        class="relative flux-card p-4 border-blue-200 hover:bg-blue-50 hover:translate-y-[-10px] transition-all duration-300 shadow-lg rounded-lg cursor-pointer overflow-hidden block text-decoration-none text-black"
                        id="office-{{ $service->id }}">
                        <div class="absolute" style="top: -50px; right: -80px;">
                            <img src="{{ asset('storage/offices/' . $service->office->logo) }}"
                                alt="{{ $service->title }} Logo" class="rounded-full"
                                style="width: 300px; height: 300px; filter: drop-shadow(0 0 10px rgba(0, 0, 0, 0.5)); opacity: 0.1;">
                        </div>
                        {{-- Header --}}
                        <header class="flex flex-col ">
                            <h3 class="text-lg font-bold  text-decoration-none text-black ">
                                {{ $service->title }}
                            </h3>
                            <p class="text-sm text-gray-500  text-decoration-none text-black ">
                                {{ Str::limit($service->description, 100, '...') }}
                            </p>
                            <p class="text-sm text-gray-800 font-bold  text-decoration-none text-black">
                                ₱{{ number_format($service->price, 2) }}
                            </p>
                        </header>
                        {{-- Footer --}}
                        <footer class="flex items-center justify-between pt-10">
                            <span
                                class="text-blue-50 hover:text-blue-700  text-decoration-none  flux-btn flux-btn-primary">Request
                                Document</span>
                        </footer>
                    </a>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No services available for this office yet.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>



    @push('scripts')
        <script>
            // Auto-hide alerts after 5 seconds
            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(function () {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(alert => {
                        if (alert) {
                            alert.style.transition = 'opacity 0.5s';
                            alert.style.opacity = '0';
                            setTimeout(() => alert.remove(), 500);
                        }
                    });
                }, 5000);
            });
        </script>
    @endpush
</div>