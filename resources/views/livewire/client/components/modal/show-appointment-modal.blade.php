<x-modal id="show-appointment" title="Appointment Details" size="max-w-2xl">
  <div class="modal-body">
    @if($selectedAppointment)
      <div class="row g-4">
                        <!-- Office Information -->
                <div class="col-md-6">
                    <div class="info-group">
                        <h6 class="text-muted mb-2">Office</h6>
                        <div class="p-3 rounded bg-light">
                            <p class="mb-0 text-dark">{{ $selectedAppointment->office?->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Service Information -->
                <div class="col-md-6">
                    <div class="info-group">
                        <h6 class="text-muted mb-2">Service</h6>
                        <div class="p-3 rounded bg-light">
                            <p class="mb-0 text-dark">{{ $selectedAppointment->service?->title ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

        <!-- Staff Information -->
        <div class="col-md-6">
        <div class="info-group">
          <h6 class="text-muted mb-2">Assigned Staff</h6>
          <div class="p-3 rounded bg-light">
          <p class="mb-0 text-dark">
            @if($selectedAppointment->staff)
          {{ $selectedAppointment->staff->first_name }} {{ $selectedAppointment->staff->last_name }}
        @else
          Not Assigned Yet
        @endif
          </p>
          </div>
        </div>
        </div>

        <!-- Date and Time -->
        <div class="col-md-6">
        <div class="info-group">
          <h6 class="text-muted mb-2">Date & Time</h6>
          <div class="p-3 rounded bg-light">
          <p class="mb-0 text-dark">
            {{ $selectedAppointment->booking_date->format('M d, Y') }} at
            {{ \Carbon\Carbon::parse($selectedAppointment->booking_time)->format('h:i A') }}
          </p>
          </div>
        </div>
        </div>

        <!-- Status -->
        <div class="col-md-6">
        <div class="info-group">
          <h6 class="text-muted mb-2">Status</h6>
          <div class="p-3 rounded bg-light">
          <span class="flux-badge flux-badge-{{ match ($selectedAppointment->status) {
      'pending' => 'warning',
      'approved' => 'success',
      'cancelled' => 'danger',
      'completed' => 'success',
      'no-show' => 'secondary',
      default => 'light',
      } }}">
            {{ ucfirst($selectedAppointment->status) }}
          </span>
          </div>
        </div>
        </div>

        <!-- Purpose -->
        <div class="col-md-6">
        <div class="info-group">
          <h6 class="text-muted mb-2">Purpose</h6>
          <div class="p-3 rounded bg-light">
          <p class="mb-0 text-dark">{{ $selectedAppointment->purpose ?? 'Not specified' }}</p>
          </div>
        </div>
        </div>

        <!-- Notes -->
        @if($selectedAppointment->notes)
        <div class="col-12">
        <div class="info-group">
        <h6 class="text-muted mb-2">Notes</h6>
        <div class="p-3 rounded bg-light">
        <p class="mb-0 text-dark">{{ $selectedAppointment->notes }}</p>
        </div>
        </div>
        </div>
      @endif

        <!-- Appointment Details -->
        @if($selectedAppointment->appointmentDetails)
        <div class="col-12">
        <div class="info-group">
        <h6 class="text-muted mb-2">Appointment Details</h6>
        <div class="p-3 rounded bg-light">
        <div class="row">
          <div class="col-md-6">
          <p class="mb-1"><strong>Request For:</strong>
          {{ ucfirst($selectedAppointment->appointmentDetails->request_for ?? 'N/A') }}</p>
          <p class="mb-1"><strong>Name:</strong> {{ $selectedAppointment->appointmentDetails->full_name ?? 'N/A' }}</p>
          <p class="mb-1"><strong>Email:</strong> {{ $selectedAppointment->appointmentDetails->email ?? 'N/A' }}</p>
          <p class="mb-1"><strong>Phone:</strong> {{ $selectedAppointment->appointmentDetails->phone ?? 'N/A' }}</p>
          </div>
          <div class="col-md-6">
          <p class="mb-1"><strong>Address:</strong>
          {{ $selectedAppointment->appointmentDetails->complete_address ?? 'N/A' }}</p>
          @if($selectedAppointment->appointmentDetails->purpose)
        <p class="mb-1"><strong>Purpose:</strong> {{ $selectedAppointment->appointmentDetails->purpose }}</p>
        @endif
          </div>
        </div>
        </div>
        </div>
        </div>
      @endif
      </div>
  @else
    <div class="text-center py-4">
      <p class="text-muted">Loading appointment details...</p>
    </div>
  @endif
  </div>
</x-modal>