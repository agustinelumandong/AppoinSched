<div class="space-y-6">
  <div class="text-center">
    <h2 class="text-xl font-semibold text-gray-900">Certificate Requests</h2>
    <p class="text-gray-600 mt-1">Select the certificates you need</p>
  </div>

  <div class="space-y-4">
    @foreach($certificates as $index => $certificate)
    <div class="card bg-base-100 border border-gray-200">
      <div class="card-body p-4">
      <div class="flex justify-between items-center mb-4">
        <h3 class="font-semibold">Certificate #{{ $index + 1 }}</h3>
        @if(count($certificates) > 1)
      <button type="button" wire:click="removeCertificate({{ $index }})" class="btn btn-error btn-sm btn-outline">
      Remove
      </button>
      @endif
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="form-control">
        <label class="label">
          <span class="label-text">Certificate Type <span class="text-red-500">*</span></span>
        </label>
        <select wire:model="certificates.{{ $index }}.certificate_type" class="select select-bordered" required>
          <option value="">-- Select Certificate Type --</option>
          <option value="birth_certificate">Birth Certificate</option>
          <option value="death_certificate">Death Certificate</option>
          <option value="marriage_certificate">Marriage Certificate</option>
          <option value="cenomar">CENOMAR</option>
        </select>
        @error("certificates.{$index}.certificate_type")
      <span class="text-red-500 text-sm">{{ $message }}</span>
      @enderror
        </div>

        <div class="form-control">
        <label class="label">
          <span class="label-text">Relationship <span class="text-red-500">*</span></span>
        </label>
        <select wire:model="certificates.{{ $index }}.relationship" class="select select-bordered" required>
          <option value="">-- Select Relationship --</option>
          <option value="myself">Myself</option>
          <option value="spouse">Spouse</option>
          <option value="child">Child</option>
          <option value="parent">Parent</option>
          <option value="sibling">Sibling</option>
          <option value="other">Other</option>
        </select>
        @error("certificates.{$index}.relationship")
      <span class="text-red-500 text-sm">{{ $message }}</span>
      @enderror
        </div>

        <div class="form-control">
        <label class="label">
          <span class="label-text">First Name <span class="text-red-500">*</span></span>
        </label>
        <input type="text" wire:model="certificates.{{ $index }}.first_name" class="input input-bordered" required>
        @error("certificates.{$index}.first_name")
      <span class="text-red-500 text-sm">{{ $message }}</span>
      @enderror
        </div>

        <div class="form-control">
        <label class="label">
          <span class="label-text">Last Name <span class="text-red-500">*</span></span>
        </label>
        <input type="text" wire:model="certificates.{{ $index }}.last_name" class="input input-bordered" required>
        @error("certificates.{$index}.last_name")
      <span class="text-red-500 text-sm">{{ $message }}</span>
      @enderror
        </div>

        <div class="form-control md:col-span-2">
        <label class="label">
          <span class="label-text">Middle Name</span>
        </label>
        <input type="text" wire:model="certificates.{{ $index }}.middle_name" class="input input-bordered">
        </div>
      </div>
      </div>
    </div>
  @endforeach
  </div>

  <div class="text-center">
    <button type="button" wire:click="addCertificate" class="btn btn-outline btn-primary">
      Add Another Certificate
    </button>
  </div>

  <div class="flex justify-between pt-4">
    <button type="button" wire:click="previousStep" class="btn btn-outline">Previous</button>
    <button type="button" wire:click="nextStep" class="btn btn-primary">Next</button>
  </div>
</div>