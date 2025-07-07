<x-modal id="reschedule-appointment" title="Reschedule Appointment"  size="max-w-2xl"  >
    <div class="modal-body">
        @if ($selectedAppointment)
            <div class="mb-4">
                <label class="form-label fw-semibold">Select New Date and Time</label>
                <div wire:key="reschedule-slot-picker-{{ $selectedAppointment->id }}-{{ now() }}">
                    @livewire(
                        'slot-picker',
                        [
                            'officeId' => $selectedAppointment->office_id,
                            'serviceId' => $selectedAppointment->service_id,
                            'staffId' => $selectedAppointment->staff_id,
                            'excludeAppointmentId' => $selectedAppointment->id,
                            'preSelectedDate' => $selectedDate,
                            'preSelectedTime' => $selectedTime,
                        ],
                        key('reschedule-slot-picker-' . $selectedAppointment->id . '-' . now())
                    )
                </div>
            </div>

            <!-- Display current selection -->
            @if ($selectedDate && $selectedTime)
                <div class="mb-4">
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="bi bi-calendar-check me-2"></i>
                        <div>
                            <strong>Selected:</strong>
                            {{ Carbon\Carbon::parse($selectedDate)->format('F j, Y') }} at
                            {{ Carbon\Carbon::parse($selectedTime)->format('g:i A') }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <p class="text-muted">Loading appointment details...</p>
            </div>
        @endif
    </div>

    <x-slot name="footer">
        <div class="d-flex justify-content-between">
            <button type="button" class="flux-btn flux-btn-outline" wire:click="closeRescheduleModal">
                Cancel
            </button>
            <button type="button" class="flux-btn flux-btn-primary" wire:click="confirmReschedule" 
                    @if(!$selectedDate || !$selectedTime) disabled @endif>
                Confirm Reschedule
            </button>
        </div>
    </x-slot>
</x-modal> 