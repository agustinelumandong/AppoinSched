<?php

use Livewire\Volt\Component;
use App\Models\Offices;
use Livewire\Attributes\{Title};

new #[Title('Services')] class extends Component {
    public Offices $office;

    public function mount(Offices $office)
    {
        $this->office = $office;
    }
}; ?>

<div>


    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">{{ $office->name }}</h1>
            <p class="text-muted mb-0">Available Services</p>
        </div>
        <div>
            <a href="{{ route('admin.offices') }}" class="flux-btn flux-btn-outline">
                <i class="bi bi-arrow-left me-2"></i>Back to Offices
            </a>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="row g-4">
        @forelse($office->services as $service)
            <div class="col-md-4">
                <div class="flux-card h-100">
                    <div class="p-4">
                        <h5 class="fw-bold mb-2">{{ $service->title }}</h5>
                        <p class="text-muted mb-3">{{ $service->description }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0">₱{{ number_format($service->price, 2) }}</span>
                            <a href="{{ route('offices.service.request', ['office' => $office->slug, 'service' => $service->slug]) }}"
                                class="flux-btn flux-btn-primary">
                                <i class="bi bi-calendar-check me-2"></i>Book Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="flux-card">
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x display-4 text-muted mb-3"></i>
                        <h5 class="text-muted">No services available</h5>
                        <p class="text-muted mb-0">This office hasn't added any services yet.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
