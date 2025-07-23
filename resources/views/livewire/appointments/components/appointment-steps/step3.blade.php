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

      <div class="flex flex-row gap-2 w-full mb-4">
        @if($editPersonDetails === false)
      <button type="button" class="btn-flux btn-flux-primary" wire:click="editPersonDetailsBtn">Edit</button>
    @else
      <button type="button" class="btn-flux btn-flux-primary" wire:click="lockPersonDetailsBtn">Lock</button>
    @endif
      </div>

      <div class="flex flex-col gap-2 w-full" wire:loading.remove>
        <div class="flex flex-row gap-2 w-full">
          <div class="w-full">
            <input type="text" placeholder="Last Name" class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="last_name"
              @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif />
            @error('last_name') <span class="text-red-500">{{ $message }}</span> @enderror
          </div>

          <div class="w-full">
            <input type="text" placeholder="First Name" class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="first_name"
              @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif />
            @error('first_name') <span class="text-red-500">{{ $message }}</span> @enderror
          </div>

          <div class="w-full">
            <input type="text" placeholder="Middle Name" class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="middle_name"
              @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif />
            @error('middle_name') <span class="text-red-500">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="flex flex-row gap-2 w-full">
          <div class=" w-full">
            <input type="email" placeholder="Email" class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="email" @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif />
            @error('email')
        <span class="text-red-500">{{ $message }}</span>
      @enderror
          </div>

          <div class="w-full">
            <input type="tel" placeholder="Phone" class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="phone" @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif />
            @error('phone')
        <span class="text-red-500">{{ $message }}</span>
      @enderror
          </div>
        </div>

        <div class="w-full">
          <input type="text" placeholder="Address" class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="address"
            @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif />
          @error('address')
        <span class="text-red-500">{{ $message }}</span>
      @enderror
        </div>

        <div class="w-full">
          <input type="text" placeholder="City" class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="city" @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif />
          @error('city')
        <span class="text-red-500">{{ $message }}</span>
      @enderror
        </div>

        <div class="w-full">
            <input type="text" placeholder="State" class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="state" @if($to_whom === 'myself')
    disabled @elseif($editPersonDetails === false) @endif />
          @error('state')
        <span class="text-red-500">{{ $message }}</span>
      @enderror
        </div>

        <div class="w-full">
          <input type="text" placeholder="Zip Code" class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" wire:model="zip_code"
            @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
          @error('zip_code')
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