<div class="px-5 py-2 mt-5" wire:key="step-4-container">
    <div class="flex flex-col gap-4">
        <div>
            {{-- Header --}}
            <div class="header mb-4">
                <h3 class="text-xl font-semibold text-base-content">{{ label_with_bisaya('Schedule', 'schedule') }}</h3>
                <div class="flex items-center gap-2 text-sm text-base-content/70">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Please select a date and time for your appointment</span>
                </div>
            </div>

            {{-- Slot Picker --}}
            <div class="mb-4" wire:key="slot-picker-wrapper">
                <label class="form-label fw-semibold">{{ label_with_bisaya('Select Date and Time', 'select_schedule') }}</label>
                <div id="slot-picker-container-stepper" wire:ignore>
                    <livewire:slot-picker :office-id="$office->id" :pre-selected-date="$selectedDate"
                        :pre-selected-time="$selectedTime" wire:key="stepper-slot-picker-{{ $office->id }}" />
                </div>
            </div>

            {{-- Footer --}}
            <footer class="my-6 flex justify-between" wire:loading.remove>
                <button class="flux-btn flux-btn-secondary" wire:click="previousStep">Previous</button>
                <button class="flux-btn flux-btn-primary" wire:click="nextStep" {{ !$selectedDate || !$selectedTime ? 'disabled' : '' }}>
                    Next
                </button>
            </footer>

        </div>
    </div>
</div>