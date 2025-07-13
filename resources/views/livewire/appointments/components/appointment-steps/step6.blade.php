<div class="px-5 py-2 mt-5">
  <div class="header mb-4">
    <h3 class="text-xl font-semibold text-base-content">Confirmation</h3>
    <div class="alert alert-info flex items-center gap-2 mb-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>Please review your appointment details before submitting.</span>
    </div>
  </div>

  <!-- Loading State -->
  <div wire:loading.delay.class="flex" wire:loading.remove.class="hidden"
    class="hidden justify-center items-center w-full h-64">
    <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-500"></div>
  </div>

  <div class="space-y-6" wire:loading.remove>
    <!-- Appointment Summary -->
    <div class="flux-card p-6">
      <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Appointment Summary</h3>
      <div class="grid md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Office</label>
          <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $office->name }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Service</label>
          <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $service->title }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Staff</label>
          <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $staff->first_name }}
            {{ $staff->last_name }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Purpose</label>
          <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $purpose }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Date</label>
          <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
            {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Time</label>
          <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
            {{ \Carbon\Carbon::parse($selectedTime)->format('h:i A') }}</p>
        </div>
      </div>
    </div>

    <!-- Personal Information -->
    <div class="flux-card p-6">
      <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Personal Information</h3>
      <div class="space-y-4">
        <div class="grid md:grid-cols-3 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $first_name }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $middle_name ?? 'N/A' }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $last_name }}</p>
          </div>
        </div>
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $email }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Phone</label>
            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $phone }}</p>
          </div>
        </div>
        <div class="grid md:grid-cols-1 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Address</label>
            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $address }}</p>
          </div>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">City</label>
            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $city }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">State/Province</label>
            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $state }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">ZIP Code</label>
            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $zip_code }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Request Details -->
    <div class="flux-card p-6">
      <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Request Details</h3>
      <div class="space-y-4">
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Request For</label>
            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
              {{ $to_whom === 'myself' ? 'Myself' : 'Someone Else' }}
            </p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Purpose</label>
            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $purpose }}</p>
          </div>
        </div>
        @if($message)
      <div class="grid md:grid-cols-1 gap-6">
        <div>
        <label class="block text-sm font-medium text-gray-600 mb-1">Additional Notes</label>
        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $message }}</p>
        </div>
      </div>
    @endif
      </div>
    </div>
  </div>

  <footer class="my-6 flex justify-end gap-2">
    <button class="btn btn-ghost" wire:click="previousStep" wire:loading.attr="disabled">Previous</button>
    <button class="btn btn-primary" wire:click="submitAppointment" wire:loading.attr="disabled">
      <span wire:loading wire:target="submitAppointment" class="loading loading-spinner"></span>
      Submit
    </button>
  </footer>
</div>