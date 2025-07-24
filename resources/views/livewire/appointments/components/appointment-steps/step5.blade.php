<div class="px-5 py-2 mt-5">
  <div class="flex flex-col gap-4">
    <div>
      <div class="header mb-4">
        <h3 class="text-xl font-semibold text-base-content">Contact Information</h3>
        <div class="flex items-center gap-2 text-sm text-base-content/70">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Please confirm your contact information</span>
        </div>
      </div>

      <!-- Loading State -->
      <div wire:loading.delay class="text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
        <p class="text-gray-600">Loading...</p>
      </div>

      <div class="flex flex-col gap-2 w-1/2" wire:loading.remove>
        <p class="text-sm text-base-content/70 mb-2">Please review and confirm your contact details:</p>

        <div class="bg-base-200 p-4 rounded-lg">
          <div class=" grid grid-cols-1 gap-2">
            <div>
              <label class="font-medium text-sm">Email:</label>
              <p class="text-base-content">{{ $email }}</p>
            </div>
            <div>
              <label class=" font-medium text-sm">Phone:</label>
              <p class="text-base-content">{{ $phone }}</p>
            </div>
            <div>
              <label class=" font-medium text-sm">Address:</label>
              <p class="text-base-content">{{ $address }}</p>
            </div>
            <div>
              <label class=" font-medium text-sm">Location:</label>
              <p class="text-base-content">{{ $city }}, {{ $province }}
                {{ $zip_code }}
              </p>
            </div>
          </div>
        </div>

        <p class="text-sm text-base-content/70 mt-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          We will use this information to contact you about your appointment.
        </p>
      </div>

      <footer class="my-6 flex justify-end gap-2">
        <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
        <button class="btn btn-primary" wire:click="nextStep">Next</button>
      </footer>
    </div>
  </div>
</div>