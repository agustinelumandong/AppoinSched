<?php

use Livewire\Volt\Component;
use App\Models\Offices;
use App\Models\Services;
use Illuminate\Support\Str;

new class extends Component {
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
    <div>
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">{{ $office->name }}</h1>
            <p class="text-muted mb-0">Office Details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.offices') }}" class="flux-btn flux-btn-outline">
                <i class="bi bi-arrow-left me-2"></i>Back to Offices
            </a>
            <button class="flux-btn flux-btn-primary" wire:click="$dispatch('open-modal-edit-office')">
                <i class="bi bi-pencil me-2"></i>Edit Office
            </button>
        </div>
    </div>

    <!-- Office Details Card -->
    <div class="flux-card mb-4">
        <div class="card-body p-4">
            <div class="row">
                <!-- Logo Section -->
                <div class="col-md-4 text-center mb-4 mb-md-0">
                    <div class="flux-card p-4">
                        <img src="{{ asset('storage/offices/' . $office->logo) }}" 
                             alt="{{ $office->name }} Logo" 
                             class="img-fluid rounded"
                             style="max-height: 200px;">
                    </div>
                </div>

                <!-- Office Information -->
                <div class="col-md-8">
                    <div class="flux-card p-4">
                        <h4 class="mb-3">Office Information</h4>
                        
                        <div class="mb-4">
                            <h5 class="text-muted mb-2">Description</h5>
                            <p class="mb-0">{{ $office->description ?? 'No description available' }}</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h5 class="text-muted mb-2">Details</h5>
                                    <p class="mb-1"><strong>Slug:</strong> {{ $office->slug }}</p>
                                    <p class="mb-1"><strong>Created:</strong> {{ $office->created_at->format('M d, Y') }}</p>
                                    <p class="mb-0"><strong>Last Updated:</strong> {{ $office->updated_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Services Section -->
                        <div class="mt-4">
                            <h4 class="mb-3">Services Offered</h4>
                            <div class="row">
                                    @forelse($services as $service)
                                        <div class="col-md-6 mb-3">
                                            <div class="flux-card p-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h5 class="mb-1">{{ $service->title }}</h5>
                                                        <p class="text-muted mb-2">{{ Str::limit($service->description, 100) }}</p>
                                                        <p class="mb-0 fw-bold">₱{{ number_format($service->price, 2) }}</p>
                                                    </div>
                                                         <a href="{{ route('offices.service.request', ['office' => $office->slug, 'service' => $service->slug]) }}" 
                                                    class="flux-btn flux-btn-primary">
                                                        Book Now
                                                    </a>
                                            </div>
                                        </div>
                                    </div>
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

                        <!-- Action Buttons -->
                        <div class="mt-4">
                            <button class="flux-btn flux-btn-danger" wire:click="openDeleteOfficeModal({{ $office->id }})">
                                <i class="bi bi-trash me-2"></i>Delete Office
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Auto-hide alerts after 5 seconds
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
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
