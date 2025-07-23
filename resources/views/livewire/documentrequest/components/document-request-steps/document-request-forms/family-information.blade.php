<div class="flux-card p-6">
  <h4 class="text-lg font-bold mb-4">Family Information</h4>
  {{-- Father --}}
  <div>
    <h5 class="text-md font-bold mb-4">Father</h5>
    <div class="form-control">
      <label class="label cursor-pointer">
        <span class="label-text">Father is Unknown</span>
        <input type="checkbox" wire:model.live="father_is_unknown" class="checkbox" />
      </label>
    </div>
    <div class="flex flex-row md:flex-col gap-4 mb-4">
      <div class="w-full md:w-1/3">
        <label for="father_last_name" class="block text-xs font-medium mb-1">Last Name</label>
        <input class="flux-form-control md:col-span-3 w-full" type="text" wire:model="father_last_name"
          placeholder="Last Name" name="father_last_name" id="father_last_name" required {{ $father_is_unknown ? 'disabled' : '' }}>
        @error('father_last_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div class="w-full md:w-1/3">
        <label for="father_first_name" class="block text-xs font-medium mb-1">First Name</label>
        <input class="flux-form-control md:col-span-3 w-full" type="text" wire:model="father_first_name"
          placeholder="First Name" name="father_first_name" id="father_first_name" required {{ $father_is_unknown ? 'disabled' : '' }}>
        @error('father_first_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div class="w-full md:w-1/3">
        <label for="father_middle_name" class="block text-xs font-medium mb-1">Middle Name</label>
        <input class="flux-form-control md:col-span-3 w-full" type="text" wire:model="father_middle_name"
          placeholder="Middle Name" name="father_middle_name" id="father_middle_name" required {{ $father_is_unknown ? 'disabled' : '' }}>
        @error('father_middle_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div class="w-1/7">
        <label for="father_suffix" class="block text-xs font-medium mb-1">Suffix</label>
        <select class="flux-form-control md:col-span-1 w-full" wire:model="father_suffix" name="father_suffix"
          id="father_suffix" {{ $father_is_unknown ? 'disabled' : '' }}>
          <option value="">Suffix</option>
          <option value="N/A">N/A</option>
          <option value="Jr.">Jr.</option>
          <option value="Sr.">Sr.</option>
        </select>
        @error('father_suffix')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
      <div>
        <label for="father_birthdate" class="block text-xs font-medium mb-1">Date of Birth</label>
        <input class="flux-form-control" type="date" wire:model="father_birthdate" name="father_birthdate"
          id="father_birthdate" {{ $father_is_unknown ? 'disabled' : '' }}>
        @error('father_birthdate')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div>
        <label for="father_nationality" class="block text-xs font-medium mb-1">Nationality</label>
        <input class="flux-form-control" type="text" wire:model="father_nationality" placeholder="Nationality"
          name="father_nationality" id="father_nationality" required {{ $father_is_unknown ? 'disabled' : '' }}>
        @error('father_nationality')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div>
        <label for="father_religion" class="block text-xs font-medium mb-1">Religion</label>
        <input class="flux-form-control" type="text" wire:model="father_religion" placeholder="Religion"
          name="father_religion" id="father_religion" required {{ $father_is_unknown ? 'disabled' : '' }}>
        @error('father_religion')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div>
        <label for="father_contact_no" class="block text-xs font-medium mb-1">Contact No.</label>
        <input class="flux-form-control" type="text" wire:model="father_contact_no" placeholder="Contact Number"
          name="father_contact_no" id="father_contact_no" required {{ $father_is_unknown ? 'disabled' : '' }}>
        @error('father_contact_no')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
    </div>
  </div>
  {{-- Mother --}}
  <div>
    <h5 class="text-md font-bold mb-4">Mother</h5>
    <div class="flex flex-row md:flex-col gap-4 mb-4">
      <div class="w-full md:w-1/3">
        <label for="mother_last_name" class="block text-xs font-medium mb-1">Last Name</label>
        <input class="flux-form-control md:col-span-3 w-full" type="text" wire:model="mother_last_name"
          placeholder="Last Name" name="mother_last_name" id="mother_last_name">
        @error('mother_last_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div class="w-full md:w-1/3">
        <label for="mother_first_name" class="block text-xs font-medium mb-1">First Name</label>
        <input class="flux-form-control md:col-span-3 w-full" type="text" wire:model="mother_first_name"
          placeholder="First Name" name="mother_first_name" id="mother_first_name">
        @error('mother_first_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div class="w-full md:w-1/3">
        <label for="mother_middle_name" class="block text-xs font-medium mb-1">Middle Name</label>
        <input class="flux-form-control md:col-span-3 w-full" type="text" wire:model="mother_middle_name"
          placeholder="Middle Name" name="mother_middle_name" id="mother_middle_name">
        @error('mother_middle_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
    </div>
    <div class="flex flex-row md:flex-col gap-4 mb-4">
      <div class="w-full md:w-1/3">
        <label for="mother_birthdate" class="block text-xs font-medium mb-1">Mother's Birthdate</label>
        <input class="flux-form-control" type="date" wire:model="mother_birthdate" placeholder="Mother's Birthdate"
          name="mother_birthdate" id="mother_birthdate">
        @error('mother_birthdate')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div class="w-full md:w-1/3">
        <label for="mother_nationality" class="block text-xs font-medium mb-1">Mother's Nationality</label>
        <input class="flux-form-control" type="text" wire:model="mother_nationality" placeholder="Mother's Nationality"
          name="mother_nationality" id="mother_nationality">
        @error('mother_nationality')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div class="w-full md:w-1/3">
        <label for="mother_religion" class="block text-xs font-medium mb-1">Mother's Religion</label>
        <input class="flux-form-control" type="text" wire:model="mother_religion" placeholder="Mother's Religion"
          name="mother_religion" id="mother_religion">
        @error('mother_religion')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div class="w-full md:w-1/3">
        <label for="mother_contact_no" class="block text-xs font-medium mb-1">Mother's Contact No</label>
        <input class="flux-form-control" type="text" wire:model="mother_contact_no" placeholder="Mother's Contact No"
          name="mother_contact_no" id="mother_contact_no">
        @error('mother_contact_no')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
    </div>
  </div>

</div>