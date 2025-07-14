<x-modal id="edit-appointment" title="Edit Appointment" size="max-w-4xl" x-data="{ show: false }" 
    x-show="show" 
    x-on:open-modal-edit-appointment.window="show = true"
    x-on:close-modal-edit-appointment.window="show = false"
    x-on:keydown.escape.window="show = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-95"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-95">
    <form wire:submit.prevent="updateAppointment">
        <div class="modal-body">
            @if ($appointment)
                <div class="mb-4">
                    <label class="form-label fw-semibold">Select Date and Time</label>
                    <div wire:key="slot-picker-{{ $appointment->id }}-{{ now() }}">
                        @livewire(
                            'slot-picker',
                            [
                                'officeId' => $appointment->office_id,
                                'serviceId' => $appointment->service_id,
                                'staffId' => $appointment->staff_id,
                                'excludeAppointmentId' => $appointment->id,
                                'preSelectedDate' => $selectedDate,
                                'preSelectedTime' => $selectedTime,
                            ],
                            key('edit-slot-picker-' . $appointment->id . '-' . now())
                        )
                    </div>
                </div>

                <!-- Debug to Display current selection -->
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
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" rows="3"
                            wire:model="notes" placeholder="Enter any additional notes..."></textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary"
                    x-on:click="$dispatch('close-modal-edit-appointment')">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>

                <button type="button" class="btn btn-primary" wire:loading.attr="disabled"
                    wire:click="updateAppointment" :disabled="!$wire.selectedDate || !$wire.selectedTime">
                    <span wire:loading.remove>
                        <i class="bi bi-save me-1"></i>Save Changes
                    </span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Saving...
                    </span>
                </button>
            </div>
        </x-slot>
    </form>
</x-modal>
