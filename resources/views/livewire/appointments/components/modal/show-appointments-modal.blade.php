<x-modal id="show-appointment" title="Show Appointment" size="max-w-2xl" footerClass="justify-between">
    <div class="modal-body">
        <div class="row g-4">
            <!-- User Information -->
            <div class="col-md-6">
                <div class="info-group">
                    <h6 class="text-muted mb-2">User Information</h6>
                    <div class="p-3 rounded bg-light">
                        <p class="mb-0 text-dark">
                            @if($appointment->user->middle_name ?? '')
                                {{$appointment->user->middle_name ?? ''}},
                            @endif
                            {{ $appointment->user->first_name ?? '' }}
                            {{ $appointment->user->last_name ?? '' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Staff Information -->
            <div class="col-md-6">
                <div class="info-group">
                    <h6 class="text-muted mb-2">Staff Information</h6>
                    <div class="p-3 rounded bg-light">
                        <p class="mb-0 text-dark">
                            @if($appointment->staff->middle_name ?? '')
                                {{ $appointment->staff->middle_name ?? '' }},
                            @endif
                            {{ $appointment->staff->first_name ?? '' }}
                            {{ $appointment->staff->last_name ?? '' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Service Information -->
            <div class="col-md-6">
                <div class="info-group">
                    <h6 class="text-muted mb-2">Service Details</h6>
                    <div class="p-3 rounded bg-light">
                        <p class="mb-0 text-dark">{{ $appointment->service->title ?? '' }}</p>
                    </div>
                </div>
            </div>

            <!-- Office Information -->
            <div class="col-md-6">
                <div class="info-group">
                    <h6 class="text-muted mb-2">Office Details</h6>
                    <div class="p-3 rounded bg-light">
                        <p class="mb-0 text-dark">{{ $appointment->office->name ?? '' }}</p>
                    </div>
                </div>
            </div>

            <!-- Booking Information -->
            <div class="col-md-6">
                <div class="info-group">
                    <h6 class="text-muted mb-2">Booking Date</h6>
                    <div class="p-3 rounded bg-light">
                        <p class="mb-0 text-dark">{{ $appointment->booking_date ?? '' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-group">
                    <h6 class="text-muted mb-2">Booking Time</h6>
                    <div class="p-3 rounded bg-light">
                        <p class="mb-0 text-dark">{{ $appointment->booking_time ?? '' }}</p>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="col-md-12">
                <div class="info-group">
                    <h6 class="text-muted mb-2">Additional Notes</h6>
                    <div class="p-3 rounded bg-light">
                        <p class="mb-0 text-dark">{{ $appointment->notes ?? '' }}</p>
                    </div>
                </div>
            </div>

            <!-- Status Information -->
            <div class="col-md-12">
                <div class="info-group">
                    <h6 class="text-muted mb-2">Appointment Status</h6>
                    <div class=" rounded ">
                        <span class="flux-badge flux-badge-{{ $appointment && $appointment->status ? ($appointment->status === 'pending' ? 'warning' : ($appointment->status === 'approved' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'secondary'))) : 'secondary' }}">
                            {{ $appointment && $appointment->status ? ucfirst($appointment->status) : 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="footer">
        <p class="text-muted">Created: {{ $appointment?->created_at?->format('M d, Y g:i A') ?? 'N/A' }}</p>
        <button type="button" class="btn btn-outline-secondary" x-data x-on:click="$dispatch('close-modal-show-appointment')">
            <i class="bi bi-x-lg me-1"></i>Back
        </button>

    </x-slot>
</x-modal>
