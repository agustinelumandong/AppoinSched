<div class="px-5 py-2 mt-5">
  <div class="flex flex-col gap-4">
    <div>
      <div class="header mb-4">
        <h3 class="text-xl font-semibold text-base-content">Confirmation</h3>
        <div class="flex items-center gap-2 text-sm text-base-content/70">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Please review your appointment details</span>
        </div>
      </div>

      <!-- Loading State -->
      <div wire:loading.delay class="text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
        <p class="text-gray-600">Loading...</p>
      </div>

      <div class="flex flex-col gap-2 w-full md:w-2/3 lg:w-1/2 text-sm text-base-content/70" wire:loading.remove>
        <div class="grid grid-cols-2 gap-4">
          <div class="flex flex-col gap-2">
            <p class="font-medium">Appointment Details</p>
            <p>Who User Id: {{ $id }}</p>
            <p>Office Id: {{ $office->id }}</p>
            <p>Service Id: {{ $service->id }}</p>
            <p>Staff Id: {{ $staff->id }}</p>

            <p>To Whom: <span class="text-base-content">{{ $to_whom }}</span></p>
            <p>Purpose: <span class="text-base-content">{{ $purpose }}</span></p>
            <p>Date: <span class=" text-base-content">{{ $selectedDate }}</span></p>
            <p>Time: <span class="text-base-content">{{ $selectedTime }}</span></p>
          </div>
          <div class=" flex flex-col gap-2">
            <p class="font-medium">Personal Information</p>
            <p>Name: <span class="text-base-content">{{ $first_name }}
                {{ $last_name }}</span></p>
            <p>Email: <span class=" text-base-content">{{ $email }}</span>
            </p>
            <p>Phone: <span class="text-base-content">{{ $phone }}
              </span>
            </p>
            <p>Address: <span class="text-base-content">{{ $address }}</span></p>
            <p>Location: <span class="text-base-content">{{ $city }}, {{ $state }}
                {{ $zip_code }}
              </span></p>
          </div>
        </div>
      </div>

      <footer class="my-6 flex justify-end gap-2">
        <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
        <button class="btn btn-primary" wire:click="submitAppointment">Submit</button>
      </footer>
    </div>
  </div>
</div>