<div class="px-5 py-2 mt-5">
  <div class="flex flex-col gap-4">
    <div>
      <div class="header mb-4">
        <h3 class="text-xl font-semibold text-base-content">Your/Someone details</h3>
        <div class="flex items-center gap-2 text-sm text-base-content/70">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Please provide your/someone details</span>
        </div>
      </div>

      <!-- Loading State -->
      <div wire:loading.delay class="text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2">
        </div>
        <p class="text-gray-600">Loading...</p>
      </div>

      @if($to_whom === 'someone_else')
      <div class="alert alert-info flex items-center gap-2 mb-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
      </svg>
      <span>Please bring a valid ID and ensure the requester is present.</span>
      </div>
    @endif

      <div class="flex flex-row gap-2 w-full mb-4">
        @if($editPersonDetails === false)
      <button type="button" class="flux-btn flux-btn-primary" wire:click="editPersonDetailsBtn">Edit</button>
    @else
      <button type="button" class="flux-btn flux-btn-primary" wire:click="lockPersonDetailsBtn">Lock</button>
    @endif
      </div>

      <div class="flex flex-col gap-2 w-full" wire:loading.remove>
        <div class="flex flex-row gap-2 w-full">
          <div class="w-full">
            <label for="last_name" class="text-xs font-medium mb-1">Last Name</label>
            <input type="text" placeholder="Last Name"
              class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="last_name"
              @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif />
            @error('last_name') <span class="text-red-500">{{ $message }}</span> @enderror
          </div>

          <div class="w-full">
            <label for="first_name" class="text-xs font-medium mb-1">First Name</label>
            <input type="text" placeholder="First Name"
              class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="first_name"
              @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif />
            @error('first_name') <span class="text-red-500">{{ $message }}</span> @enderror
          </div>

          <div class="w-full">
            <label for="middle_name" class="text-xs font-medium mb-1">Middle Name</label>
            <input type="text" placeholder="Middle Name"
              class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="middle_name"
              @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif />
            @error('middle_name') <span class="text-red-500">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="flex flex-row gap-2 w-full">
          <div class=" w-full">
            <label for="email" class="text-xs font-medium mb-1">Email</label>
            <input type="email" placeholder="Email"
              class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="email"
              @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif />
            @error('email')
        <span class="text-red-500">{{ $message }}</span>
      @enderror
          </div>

          <div class="w-full">
            <label for="phone" class="text-xs font-medium mb-1">Phone</label>
            <input type="tel" placeholder="Phone"
              class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="phone"
              @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif />
            @error('phone')
        <span class="text-red-500">{{ $message }}</span>
      @enderror
          </div>
        </div>


        {{-- <div class="w-full">
          <input type="text" placeholder="City"
            class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="city"
            @if($to_whom==='myself' && $editPersonDetails===false) disabled @endif />
          @error('city')
          <span class="text-red-500">{{ $message }}</span>
          @enderror
        </div>

        <div class="w-full">
          <input type="text" placeholder="State"
            class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="state"
            @if($to_whom==='myself' ) disabled @elseif($editPersonDetails===false) @endif />
          @error('state')
          <span class="text-red-500">{{ $message }}</span>
          @enderror
        </div> --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="flex flex-col">
            <label for="region" class="text-xs font-medium mb-1">Region</label>
            <select id="region" class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif"
              wire:model.live="region" @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
              <option value="">Select Region</option>
              @foreach ($regions as $region)
          <option value="{{ $region['code'] }}">{{ $region['name'] }}</option>
        @endforeach
            </select>
          </div>
          <div class="flex flex-col">
            <label for="province" class="text-xs font-medium mb-1">Province</label>
            <select id="province" class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif"
              wire:model.live="province" @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
              <option value="">Select Province</option>
              @foreach ($provinces as $provinceKey => $provinceName)
          <option value="{{ $provinceKey }}">{{ $provinceName }}</option>
        @endforeach
            </select>
          </div>
          <div class="flex flex-col">
            <label for="city" class="text-xs font-medium mb-1">City</label>
            <select id="city" class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif"
              wire:model.live="city" @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
              <option value="">Select City</option>
              @foreach ($cities as $cityKey => $cityName)
          <option value="{{ $cityKey }}">{{ $cityName }}</option>
        @endforeach
            </select>
          </div>
          <div class="flex flex-col">
            <label for="barangay" class="text-xs font-medium mb-1">Barangay</label>
            <select id="barangay" class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif"
              wire:model.live="barangay" @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
              <option value="">Select Barangay</option>
              @foreach ($barangays as $barangay)
          <option value="{{ $barangay }}">{{ $barangay }}</option>
        @endforeach
            </select>
          </div>
          <div class="flex flex-col">
            <label for="street" class="text-xs font-medium mb-1">Street</label>
            <input id="street" class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif"
              type="text" wire:model="street" placeholder="Street" @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
            <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
          </div>
          <div class="flex flex-col">
            <label for="zip_code" class="text-xs font-medium mb-1">Zip Code</label>
            <input id="zip_code" class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif"
              type="text" wire:model="zip_code" placeholder="Zip Code" @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
            <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
          </div>
        </div>

        {{-- <div class="w-full">
          <input type="text" placeholder="Zip Code"
            class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="zip_code"
            @if($to_whom==='myself' && $editPersonDetails===false) disabled @endif>
          @error('zip_code')
          <span class="text-red-500">{{ $message }}</span>
          @enderror
        </div> --}}

        <div class="w-full">
          <label for="address" class="text-xs font-medium mb-1">Address</label>
          <input type="text" placeholder="Address"
            class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="address"
            @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif />
          @error('address')
        <span class="text-red-500">{{ $message }}</span>
      @enderror
        </div>

        <footer class="my-6 flex justify-end gap-2">
          <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
          <button class="btn btn-primary" wire:click="nextStep" wire:loading.disable>Next</button>
        </footer>
      </div>
    </div>
  </div>
</div>