<?php

use Livewire\Volt\Component;
use App\Models\Offices;
use App\Models\Services;
use Illuminate\Support\Str;
use Livewire\Attributes\{Title};

new #[Title('Office Details')] class extends Component {
    public Offices $office;
    public $services;

    public function mount(Offices $office)
    {
        $this->office = $office;
        $this->services = $office->services()->where('is_active', 1)->get();
    }
}; ?>

<div>


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

                @php
                    // Get the first active service for this office, or null if none
                    $service = $services->first();
                @endphp

                @if($service)
                    @php
                        $isAppointment = in_array($service->slug, [
                            'appointment-mto',
                            'appointment-mcr',
                            'appointment-bpls'
                        ]);
                        $isDocumentRequest = in_array($service->slug, [
                            'document-request-mto',
                            'document-request-mcr',
                            'document-request-bpls'
                        ]);
                        $requestRoute = $isAppointment ? 'offices.service.appointment' : 'offices.service.request';

                        $logo = $service->office->logo;
                        if (!empty($logo)) {
                            $logo = asset('storage/offices/' . $service->office->logo);
                        }
                        if ($service->office->slug === 'municipal-civil-registrar') {
                            $logo = asset('images/MCR_logo.jpg');
                        } elseif ($service->office->slug === 'municipal-treasurers-office') {
                            $logo = asset('images/MTO_logo.jpg');
                        } elseif ($service->office->slug === 'business-permits-and-licensing-section') {
                            $logo = asset('images/BPLS_logo.jpg');
                        }
                    @endphp

                    <a href="{{ route('offices.service.request', ['office' => $office->slug]) }}"
                        class="relative flux-card p-4 border-blue-200 hover:bg-blue-50 hover:translate-y-[-10px] transition-all duration-300 shadow-lg rounded-lg cursor-pointer overflow-hidden block text-decoration-none text-black"
                        id="office-{{ $office->id }}">
                        <div class="absolute" style="top: -50px; right: -80px;">
                            <img src="{{ $logo }}" alt="{{ $office->name }} Logo" class="rounded-full"
                                style="width: 300px; height: 300px; filter: drop-shadow(0 0 10px rgba(0, 0, 0, 0.5)); opacity: 0.1;">
                        </div>
                        {{-- Header --}}
                        <header class="flex flex-col ">
                            <h3 class="text-lg font-bold  text-decoration-none text-black ">
                                Document Request
                            </h3>
                            <p class="text-sm text-gray-500  text-decoration-none text-black ">
                                Request a document from this office
                            </p>
                        </header>
                        {{-- Footer --}}
                        <footer class="flex items-center justify-between pt-10">
                            <span
                                class="text-blue-50 hover:text-blue-700  text-decoration-none  flux-btn flux-btn-primary">Request
                                Document</span>
                        </footer>
                    </a>
                    <a href="{{ route('offices.service.appointment', ['office' => $office->slug]) }}"
                        class="relative flux-card p-4 border-blue-200 hover:bg-blue-50 hover:translate-y-[-10px] transition-all duration-300 shadow-lg rounded-lg cursor-pointer overflow-hidden block text-decoration-none text-black"
                        id="office-{{ $office->id }}">
                        <div class="absolute" style="top: -50px; right: -80px;">
                            <img src="{{ $logo }}" alt="{{ $office->name }} Logo" class="rounded-full"
                                style="width: 300px; height: 300px; filter: drop-shadow(0 0 10px rgba(0, 0, 0, 0.5)); opacity: 0.1;">
                        </div>
                        {{-- Header --}}
                        <header class="flex flex-col ">
                            <h3 class="text-lg font-bold  text-decoration-none text-black ">
                                Appointment Booking
                            </h3>
                            <p class="text-sm text-gray-500  text-decoration-none text-black ">
                                Book an appointment with this office
                            </p>
                        </header>
                        {{-- Footer --}}
                        <footer class="flex items-center justify-between pt-10">
                            <span
                                class="text-blue-50 hover:text-blue-700  text-decoration-none  flux-btn flux-btn-primary">Request
                                Appointment</span>
                        </footer>
                    </a>
                @else
                    <div class="col-span-1 md:col-span-2">
                        <div class="flux-card text-center py-8">
                            <h5 class="text-muted">No services available</h5>
                            <p class="text-muted mb-0">This office hasn't added any services yet.</p>
                        </div>
                    </div>
                @endif


                @forelse($services as $service)
                    @php
                        $isAppointment = $service->slug === 'appointment-mto' || $service->slug === 'appointment-mcr' || $service->slug === 'appointment-bpls';
                        $isDocumentRequest = $service->slug === 'document-request-mto' || $service->slug === 'document-request-mcr' || $service->slug === 'document-request-bpls' || $service->slug === 'document-request-bpls';

                        $requestRoute = $isAppointment ? 'offices.service.appointment' : 'offices.service.request';
                    @endphp
                    {{-- Service Card --}}

                    <a href="{{ route($requestRoute, ['office' => $office->slug, 'service' => $service->slug]) }}"
                        class="relative flux-card p-4 border-blue-200 hover:bg-blue-50 hover:translate-y-[-10px] transition-all duration-300 shadow-lg rounded-lg cursor-pointer overflow-hidden block text-decoration-none text-black"
                        id="office-{{ $service->id }}">
                        <div class="absolute" style="top: -50px; right: -80px;">
                            @php
                                $logo = $service->office->logo;
                                if (!empty($logo)) {
                                    $logo = asset('storage/offices/' . $service->office->logo);
                                }
                                if ($service->office->slug === 'municipal-civil-registrar') {
                                    $logo = asset('images/MCR_logo.jpg');
                                } elseif ($service->office->slug === 'municipal-treasurers-office') {
                                    $logo = asset('images/MTO_logo.jpg');
                                } elseif ($service->office->slug === 'business-permits-and-licensing-section') {
                                    $logo = asset('images/BPLS_logo.jpg');
                                }
                            @endphp
                            <img src="{{ $logo }}" alt="{{ $service->title }} Logo" class="rounded-full"
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