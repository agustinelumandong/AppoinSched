<x-modal id="show-appointment" title="Appointment Details" size="max-w-2xl">
    <div class="modal-body">
        @if ($appointment)
            <div class="row g-4">
                <!-- Office Information -->
                <div class="col-md-6">
                    <div class="info-group">
                        <h6 class="text-muted mb-2">Office</h6>
                        <div class="p-3 flux-card">
                            <p class="mb-0 text-dark">{{ $appointment->office?->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Service Information -->
                <div class="col-md-6">
                    <div class="info-group">
                        <h6 class="text-muted mb-2">Service</h6>
                        <div class="p-3 flux-card">
                            <p class="mb-0 text-dark">{{ $appointment->service?->title ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Date and Time -->
                <div class="col-md-6">
                    <div class="info-group">
                        <h6 class="text-muted mb-2">Date & Time</h6>
                        <div class="p-3 flux-card">
                            <p class="mb-0 text-dark">
                                {{ $appointment->booking_date->format('M d, Y') }} at
                                {{ \Carbon\Carbon::parse($appointment->booking_time)->format('h:i A') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Purpose -->
                <div class="col-md-6">
                    <div class="info-group">
                        <h6 class="text-muted mb-2">Purpose</h6>
                        <div class="p-3 flux-card">
                            <p class="mb-0 text-dark">{{ $appointment->purpose ?? 'Not specified' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if ($appointment->notes)
                    <div class="col-12">
                        <div class="info-group">
                            <h6 class="text-muted mb-2">Notes</h6>
                            <div class="p-3 flux-card">
                                <p class="mb-0 text-dark">{{ $appointment->notes }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Appointment Details -->
                @if ($appointment->appointmentDetails)
                    <div class="col-12">
                        <div class="info-group">
                            <h6 class="text-muted mb-2">Appointment Details</h6>
                            <div class="p-3 flux-card">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Request For:</strong>
                                            {{ ucfirst($appointment->appointmentDetails->request_for ?? 'N/A') }}</p>
                                        <p class="mb-1"><strong>Name:</strong>
                                            {{ $appointment->appointmentDetails->full_name ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>Email:</strong>
                                            {{ $appointment->appointmentDetails->email ?? 'N/A' }}
                                        </p>
                                        <p class="mb-1"><strong>Phone:</strong>
                                            {{ $appointment->appointmentDetails->phone ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Address:</strong>
                                            {{ $appointment->appointmentDetails->complete_address ?? 'N/A' }}</p>
                                        @if ($appointment->appointmentDetails->purpose)
                                            <p class="mb-1"><strong>Purpose:</strong>
                                                {{ $appointment->appointmentDetails->purpose }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Status -->
                <div class="col-md-6">
                    <div class="info-group">
                        <h6 class="text-muted mb-2">Status</h6>
                        <p class="mb-0 text-dark">
                            @php
                                $status = $appointment->status ?? 'N/A';
                                $badgeColor = match ($status) {
                                    'on-going' => 'flux-badge-warning',
                                    'completed' => 'flux-badge-success',
                                    'cancelled' => 'flux-badge-danger',
                                    default => 'flux-badge-light',
                                };
                            @endphp
                            <span class="flux-badge {{ $badgeColor }} capitalize">
                                {{ ucfirst($status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <p class="text-muted">Loading appointment details...</p>
            </div>
        @endif
    </div>
</x-modal>
