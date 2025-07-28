<div class="space-y-6">
  <div class="text-center">
    <h2 class="text-xl font-semibold text-gray-900">Personal Information</h2>
    <p class="text-gray-600 mt-1">Please provide your basic information</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="form-control">
      <label class="label">
        <span class="label-text">First Name <span class="text-red-500">*</span></span>
      </label>
      <input type="text" wire:model="first_name" class="input input-bordered" required>
      @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="form-control">
      <label class="label">
        <span class="label-text">Last Name <span class="text-red-500">*</span></span>
      </label>
      <input type="text" wire:model="last_name" class="input input-bordered" required>
      @error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="form-control">
      <label class="label">
        <span class="label-text">Middle Name</span>
      </label>
      <input type="text" wire:model="middle_name" class="input input-bordered">
    </div>

    <div class="form-control">
      <label class="label">
        <span class="label-text">Email <span class="text-red-500">*</span></span>
      </label>
      <input type="email" wire:model="email" class="input input-bordered" required>
      @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="form-control md:col-span-2">
      <label class="label">
        <span class="label-text">Mobile Number <span class="text-red-500">*</span></span>
      </label>
      <input type="tel" wire:model="phone" class="input input-bordered" required>
      @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
  </div>

  <div class="flex justify-between pt-4">
    <button type="button" class="btn btn-outline" disabled>Previous</button>
    <button type="button" wire:click="nextStep" class="btn btn-primary">Next</button>
  </div>
</div>