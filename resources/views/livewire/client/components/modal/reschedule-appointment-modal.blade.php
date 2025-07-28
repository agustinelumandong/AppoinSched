<x-modal id="reschedule-appointment" title="Reschedule Appointment" size="max-w-2xl" x-data="{ show: false }" x-show="show"
    x-on:open-modal-reschedule-appointment.window="show = true"
    x-on:close-modal-reschedule-appointment.window="show = false" x-on:keydown.escape.window="show = false"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
    x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
    <form wire:submit.prevent="rescheduleAppointment">
        <div class="modal-body">
            @if (session()->has('error'))
                <div class="alert alert-danger d-flex align-items-center mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if ($appointment)
                <div class="mb-4">
                    <label class="form-label fw-semibold">Select New Date and Time</label>

                    <div class="alert alert-info mb-3">
                        <div class="d-flex">
                            <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                            <div>
                                <strong>Hourly Appointment System</strong>
                                <p class="mb-0 small">Each hour can accommodate up to 5 appointments. Please select an
                                    hour with available slots.</p>
                            </div>
                        </div>
                    </div>

                    <div wire:key="reschedule-slot-picker-{{ $appointment->id }}-{{ now() }}">
                        @livewire(
                            'slot-picker',
                            [
                                'officeId' => $appointment->office_id,
                                'serviceId' => $appointment->service_id,
                                'excludeAppointmentId' => $appointment->id,
                                'preSelectedDate' => $selectedDate,
                                'preSelectedTime' => $selectedTime,
                            ],
                            key('reschedule-slot-picker-' . $appointment->id . '-' . now())
                        ) </div>
                </div>
                {{-- @if ($selectedDate && $selectedTime)
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
                @endif --}}
            @else
                <div class="text-center py-4">
                    <p class="text-muted">Loading appointment details...</p>
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary"
                    x-on:click="$dispatch('close-modal-reschedule-appointment')">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                    wire:click="rescheduleAppointment" @if (!$selectedDate || !$selectedTime) disabled @endif>
                    <span wire:loading.remove>
                        <i class="bi bi-save me-1"></i>Confirm Reschedule
                    </span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Rescheduling...
                    </span>
                </button>
            </div>
        </x-slot>
    </form>
</x-modal>
