<div class="space-y-6">
  <div class="text-center">
    <h2 class="text-xl font-semibold text-gray-900">{{ label_with_bisaya('Select Schedule', 'select_schedule') }}</h2>
    <p class="text-gray-600 mt-1">Choose your preferred date and time</p>
  </div>

  <div class="form-control">
    <label class="label">
      <span class="label-text">{{ label_with_bisaya('Purpose of Visit', 'purpose_of_visit') }} <span class="text-red-500">*</span></span>
    </label>
    <textarea wire:model="purpose" class="textarea textarea-bordered"
      placeholder="Please describe the purpose of your visit..." required></textarea>
    @error('purpose') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
  </div>

  <div>
    <livewire:slot-picker :office-id="$office->id" :service-id="$service->id" :selected-date="$selectedDate"
      :selected-time="$selectedTime" wire:key="appointment-slot-picker" />
  </div>

  <div class="form-control">
    <label class="label">
      <span class="label-text">{{ label_with_bisaya('Additional Notes', 'additional_notes') }}</span>
    </label>
    <textarea wire:model="notes" class="textarea textarea-bordered"
      placeholder="Any additional information..."></textarea>
  </div>

  <div class="flex justify-between pt-4">
    <button type="button" wire:click="previousStep" class="btn btn-outline">Previous</button>
    <button type="button" wire:click="submitAppointment" class="btn btn-primary" {{ !$selectedDate || !$selectedTime ? 'disabled' : '' }}>
      @if($isLoading)
      <span class="loading loading-spinner loading-sm"></span>
      Creating...
    @else
      Create Appointment
    @endif
    </button>
  </div>
</div>