<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
  <div class="col-span-2">
    <label class="block text-xs font-medium mb-1">Last Name</label>
    <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_last_name" placeholder="Last Name">
    @error($prefix . '_last_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    <span class="text-xs text-gray-500 mt-1">Required</span>
  </div>
  <div class="col-span-2">
    <label class="block text-xs font-medium mb-1">First Name</label>
    <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_first_name" placeholder="First Name">
    @error($prefix . '_first_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    <span class="text-xs text-gray-500 mt-1">Required</span>
  </div>
  <div class="col-span-2">
    <label class="block text-xs font-medium mb-1">Middle Name</label>
    <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_middle_name" placeholder="Middle Name">
    @error($prefix . '_middle_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
  </div>
  <div class="col-span-2">
    <label class="block text-xs font-medium mb-1">Suffix</label>
    <select class="flux-form-control" wire:model="{{ $prefix }}_suffix">
      <option value="">Suffix</option>
      <option value="N/A">N/A</option>
      <option value="Jr.">Jr.</option>
      <option value="Sr.">Sr.</option>
      <option value="I">I</option>
      <option value="II">II</option>
      <option value="III">III</option>
    </select>
    @error($prefix . '_suffix')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
  </div>

  <div>
    <label class="block text-xs font-medium mb-1">Age</label>
    <input class="flux-form-control" type="number" wire:model="{{ $prefix }}_age" placeholder="Age">
    @error($prefix . '_age')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    <span class="text-xs text-gray-500 mt-1">Required</span>
  </div>
  <div>
    <label class="block text-xs font-medium mb-1">Date of Birth</label>
    <input class="flux-form-control" type="date" wire:model="{{ $prefix }}_date_of_birth" placeholder="Date of Birth">
    @error($prefix . '_date_of_birth')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    <span class="text-xs text-gray-500 mt-1">Required</span>
  </div>
  <div>
    <label class="block text-xs font-medium mb-1">Place of Birth</label>
    <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_place_of_birth" placeholder="Place of Birth">
    @error($prefix . '_place_of_birth')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    <span class="text-xs text-gray-500 mt-1">Required</span>
  </div>
  <div>
    <label class="block text-xs font-medium mb-1">Sex</label>
    <select class="flux-form-control" wire:model="{{ $prefix }}_sex">
      <option value="">Select Sex</option>
      <option value="Male">Male</option>
      <option value="Female">Female</option>
    </select>
    @error($prefix . '_sex')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    <span class="text-xs text-gray-500 mt-1">Required</span>
  </div>
  <div>
    <label class="block text-xs font-medium mb-1">Citizenship</label>
    <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_citizenship" placeholder="Citizenship">
    @error($prefix . '_citizenship')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    <span class="text-xs text-gray-500 mt-1">Required</span>
  </div>
  <div>
    <label class="block text-xs font-medium mb-1">Residence</label>
    <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_residence" placeholder="Residence">
    @error($prefix . '_residence')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    <span class="text-xs text-gray-500 mt-1">Required</span>
  </div>
  <div>
    <label class="block text-xs font-medium mb-1">Religion</label>
    <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_religion" placeholder="Religion">
    @error($prefix . '_religion')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
  </div>
  <div>
    <label class="block text-xs font-medium mb-1">Civil Status</label>
    <select class="flux-form-control" wire:model="{{ $prefix }}_civil_status">
      <option value="">Civil Status</option>
      <option value="Single">Single</option>
      <option value="Previously Married">Previously Married</option>
      <option value="Widowed">Widowed</option>
      <option value="Divorced">Divorced</option>
      <option value="Separated">Separated</option>
    </select>
    @error($prefix . '_civil_status')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    <span class="text-xs text-gray-500 mt-1">Required</span>
  </div>
</div>