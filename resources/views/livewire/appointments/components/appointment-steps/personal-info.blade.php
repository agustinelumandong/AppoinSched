<div class="space-y-6">
  <div class="text-center">
    <h2 class="text-xl font-semibold text-gray-900">{{ label_with_bisaya('Personal Information', 'personal_information') }}</h2>
    <p class="text-gray-600 mt-1">Please provide your basic information</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="form-control">
      <label class="label">
        <span class="label-text">{{ label_with_bisaya('First Name', 'first_name') }} <span class="text-red-500">*</span></span>
      </label>
      <input type="text" wire:model="first_name" class="input input-bordered" required>
      @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="form-control">
      <label class="label">
        <span class="label-text">{{ label_with_bisaya('Last Name', 'last_name') }} <span class="text-red-500">*</span></span>
      </label>
      <input type="text" wire:model="last_name" class="input input-bordered" required>
      @error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="form-control">
      <label class="label">
        <span class="label-text">{{ label_with_bisaya('Middle Name', 'middle_name') }}</span>
      </label>
      <input type="text" wire:model="middle_name" class="input input-bordered">
    </div>

    <div class="form-control">
      <label class="label">
        <span class="label-text">{{ label_with_bisaya('Email', 'email') }} <span class="text-red-500">*</span></span>
      </label>
      <input type="email" wire:model="email" class="input input-bordered" required>
      @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="form-control md:col-span-2">
      <label class="label">
        <span class="label-text">{{ label_with_bisaya('Mobile Number', 'mobile_number') }} <span class="text-red-500">*</span></span>
      </label>
      <input type="tel" wire:model="phone" class="input input-bordered" inputmode="numeric" pattern="[0-9]*"
        minlength="11" maxlength="11"
        x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '').slice(0, 11)" required>
      @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
  </div>

  <div class="flex justify-between pt-4">
    <button type="button" class="btn btn-outline" disabled>Previous</button>
    <button type="button" wire:click="nextStep" class="btn btn-primary">Next</button>
  </div>
</div>