<div class="flux-card p-6">

  @if($to_whom === 'someone_else')
    <div class="alert alert-info flex items-center gap-2 mb-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <span>Please provide the complete information for the person you're requesting the document for.</span>
    </div>
  @endif
  <h4 class="text-lg font-bold mb-4">Death Information</h4>
  <!-- Full Name of the Deceased -->
  <div class="mb-4">
    <h5 class="text-md font-semibold mb-3">Full Name of the Deceased</h5>
    <div class="flex flex-row md:flex-col gap-4 mb-4">
      <div class="w-full md:w-1/3">
        <label for="deceased_last_name" class="block text-xs font-medium mb-1">Last Name</label>
        <input class="flux-form-control" type="text" wire:model="deceased_last_name" placeholder="Last Name"
          name="deceased_last_name" id="deceased_last_name">
        @error('deceased_last_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Required</span>
      </div>
      <div class="w-full md:w-1/3">
        <label for="deceased_first_name" class="block text-xs font-medium mb-1">First Name</label>
        <input class="flux-form-control" type="text" wire:model="deceased_first_name" placeholder="First Name"
          name="deceased_first_name" id="deceased_first_name">
        @error('deceased_first_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Required</span>
      </div>
      <div class="w-full md:w-1/3">
        <label for="deceased_middle_name" class="block text-xs font-medium mb-1">Middle Name</label>
        <input class="flux-form-control" type="text" wire:model="deceased_middle_name" placeholder="Middle Name"
          name="deceased_middle_name" id="deceased_middle_name">
        @error('deceased_middle_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
      </div>
    </div>
  </div>
  <!-- Date and Place of Death -->
  <div class="mb-4">
    <h5 class="text-md font-semibold mb-3">Date and Place of Death</h5>
    <div class="flex flex-row md:flex-col gap-4 mb-4">
      <div class="w-full md:w-1/3">
        <label for="death_date" class="block text-xs font-medium mb-1">Death Date</label>
        <input class="flux-form-control" type="date" wire:model="death_date" placeholder="Death Date" name="death_date"
          id="death_date">
        @error('death_date')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Required</span>
      </div>
      <div class="w-full md:w-1/3">
        <label for="death_time" class="block text-xs font-medium mb-1">Death Time</label>
        <input class="flux-form-control" type="time" wire:model="death_time" placeholder="Death Time" name="death_time"
          id="death_time">
        @error('death_time')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Required</span>
      </div>
      <div class="w-full md:w-1/3">
        <label for="death_place" class="block text-xs font-medium mb-1">Place of Death</label>
        <input class="flux-form-control" type="text" wire:model="death_place" placeholder="Place of Death"
          name="death_place" id="death_place">
        @error('death_place')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Required</span>
      </div>
    </div>
  </div>
  <!-- Relationship to the Deceased -->
  <div class="mb-4">
    <h5 class="text-md font-semibold mb-3">Your Relationship to the Deceased</h5>
    <div class="flex flex-row md:flex-col gap-4 mb-4">
      <div class="w-1/2">
        <label for="relationship_to_deceased" class="block text-xs font-medium mb-1">Relationship</label>
        <select class="flux-form-control" wire:model="relationship_to_deceased" name="relationship_to_deceased"
          id="relationship_to_deceased">
          <option value="">Select Relationship</option>
          <option value="Spouse">Spouse</option>
          <option value="Child">Child</option>
          <option value="Parent">Parent</option>
          <option value="Sibling">Sibling</option>
          <option value="Grandchild">Grandchild</option>
          <option value="Grandparent">Grandparent</option>
          <option value="Other Relative">Other Relative</option>
          <option value="Legal Representative">Legal Representative</option>
          <option value="Other">Other</option>
        </select>
        @error('relationship_to_deceased')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Required</span>
      </div>
    </div>
  </div>
  <!-- Additional Deceased Information -->
  <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
      <label for="deceased_sex" class="block text-xs font-medium mb-1">Sex</label>
      <select class="flux-form-control" wire:model="deceased_sex" id="deceased_sex" name="deceased_sex">
        <option value="">Select Sex</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>
      @error('deceased_sex')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      <span class="text-xs text-gray-500 mt-1">Required</span>
    </div>
    <div>
      <label for="deceased_religion" class="block text-xs font-medium mb-1">Religion</label>
      <input class="flux-form-control" type="text" wire:model="deceased_religion" id="deceased_religion"
        name="deceased_religion" placeholder="Religion">
      @error('deceased_religion')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
    </div>
    <div>
      <label for="deceased_age" class="block text-xs font-medium mb-1">Age</label>
      <input class="flux-form-control" type="number" min="0" wire:model="deceased_age" id="deceased_age"
        name="deceased_age" placeholder="Age">
      @error('deceased_age')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      <span class="text-xs text-gray-500 mt-1">Required</span>
    </div>
  </div>
  <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label for="deceased_place_of_birth" class="block text-xs font-medium mb-1">Place of Birth</label>
      <input class="flux-form-control" type="text" wire:model="deceased_place_of_birth" id="deceased_place_of_birth"
        name="deceased_place_of_birth" placeholder="Place of Birth">
      @error('deceased_place_of_birth')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      <span class="text-xs text-gray-500 mt-1">Required</span>
    </div>
    <div>
      <label for="deceased_date_of_birth" class="block text-xs font-medium mb-1">Date of Birth</label>
      <input class="flux-form-control" type="date" wire:model="deceased_date_of_birth" id="deceased_date_of_birth"
        name="deceased_date_of_birth">
      @error('deceased_date_of_birth')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      <span class="text-xs text-gray-500 mt-1">Required</span>
    </div>
  </div>
  <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label for="deceased_civil_status" class="block text-xs font-medium mb-1">Civil Status</label>
      <select class="flux-form-control" wire:model="deceased_civil_status" id="deceased_civil_status"
        name="deceased_civil_status">
        <option value="">Select Civil Status</option>
        <option value="Single">Single</option>
        <option value="Married">Married</option>
        <option value="Widowed">Widowed</option>
        <option value="Divorced">Divorced</option>
        <option value="Separated">Separated</option>
      </select>
      @error('deceased_civil_status')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      <span class="text-xs text-gray-500 mt-1">Required</span>
    </div>
    <div>
      <label for="deceased_residence" class="block text-xs font-medium mb-1">Residence</label>
      <input class="flux-form-control" type="text" wire:model="deceased_residence" id="deceased_residence"
        name="deceased_residence" placeholder="Residence">
      @error('deceased_residence')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
    </div>
  </div>
  <div class="mb-4">
    <label for="deceased_occupation" class="block text-xs font-medium mb-1">Occupation</label>
    <input class="flux-form-control" type="text" wire:model="deceased_occupation" id="deceased_occupation"
      name="deceased_occupation" placeholder="Occupation">
    @error('deceased_occupation')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
  </div>
  <!-- Parental Information -->
  <div class="mb-4">
    <h5 class="text-md font-semibold mb-3">Father's Information</h5>
    <div class="flex flex-row md:flex-col gap-4 mb-4">
      <div class="w-full md:w-1/3">
        <label for="deceased_father_last_name" class="block text-xs font-medium mb-1">Last Name</label>
        <input class="flux-form-control" type="text" wire:model="deceased_father_last_name"
          id="deceased_father_last_name" name="deceased_father_last_name" placeholder="Last Name">
        @error('deceased_father_last_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
      </div>
      <div class="w-full md:w-1/3">
        <label for="deceased_father_first_name" class="block text-xs font-medium mb-1">First Name</label>
        <input class="flux-form-control" type="text" wire:model="deceased_father_first_name"
          id="deceased_father_first_name" name="deceased_father_first_name" placeholder="First Name">
        @error('deceased_father_first_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
      </div>
      <div class="w-full md:w-1/3">
        <label for="deceased_father_middle_name" class="block text-xs font-medium mb-1">Middle Name</label>
        <input class="flux-form-control" type="text" wire:model="deceased_father_middle_name"
          id="deceased_father_middle_name" name="deceased_father_middle_name" placeholder="Middle Name">
        @error('deceased_father_middle_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
      </div>
    </div>
    <h5 class="text-md font-semibold mb-3">Mother's Information</h5>
    <div class="flex flex-row md:flex-col gap-4 mb-4">
      <div class="w-full md:w-1/3">
        <label for="deceased_mother_last_name" class="block text-xs font-medium mb-1">Last Maiden Name</label>
        <input class="flux-form-control" type="text" wire:model="deceased_mother_last_name"
          id="deceased_mother_last_name" name="deceased_mother_last_name" placeholder="Last Name">
        @error('deceased_mother_last_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
      </div>
      <div class="w-full md:w-1/3">
        <label for="deceased_mother_first_name" class="block text-xs font-medium mb-1">First Maiden Name</label>
        <input class="flux-form-control" type="text" wire:model="deceased_mother_first_name"
          id="deceased_mother_first_name" name="deceased_mother_first_name" placeholder="First Name">
        @error('deceased_mother_first_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
      </div>
      <div class="w-full md:w-1/3">
        <label for="deceased_mother_middle_name" class="block text-xs font-medium mb-1">Middle Maiden Name</label>
        <input class="flux-form-control" type="text" wire:model="deceased_mother_middle_name"
          id="deceased_mother_middle_name" name="deceased_mother_middle_name" placeholder="Middle Name">
        @error('deceased_mother_middle_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
      </div>
    </div>
  </div>
  <!-- Burial Details -->
  <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label for="burial_cemetery_name" class="block text-xs font-medium mb-1">Cemetery Name</label>
      <input class="flux-form-control" type="text" wire:model="burial_cemetery_name" id="burial_cemetery_name"
        name="burial_cemetery_name" placeholder="Cemetery Name">
      @error('burial_cemetery_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
    </div>
    <div>
      <label for="burial_cemetery_address" class="block text-xs font-medium mb-1">Cemetery Address</label>
      <input class="flux-form-control" type="text" wire:model="burial_cemetery_address" id="burial_cemetery_address"
        name="burial_cemetery_address" placeholder="Cemetery Address">
      @error('burial_cemetery_address')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
    </div>
  </div>
  <!-- Informant's Declaration -->
  <div class="mb-4">
    <h5 class="text-md font-semibold mb-3">Informant's Declaration</h5>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
      <div>
        <label for="informant_name" class="block text-xs font-medium mb-1">Name of Informant</label>
        <input class="flux-form-control" type="text" wire:model="informant_name" id="informant_name"
          name="informant_name" placeholder="Name of Informant">
        @error('informant_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
      </div>
      <div>
        <label for="informant_address" class="block text-xs font-medium mb-1">Address</label>
        <input class="flux-form-control" type="text" wire:model="informant_address" id="informant_address"
          name="informant_address" placeholder="Address">
        @error('informant_address')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
      </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
      <div>
        <label for="informant_relationship" class="block text-xs font-medium mb-1">Relationship to the Deceased</label>
        <input class="flux-form-control" type="text" wire:model="informant_relationship" id="informant_relationship"
          name="informant_relationship" placeholder="Relationship">
        @error('informant_relationship')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
      </div>
      <div>
        <label for="informant_contact_no" class="block text-xs font-medium mb-1">Contact Number (CP #)</label>
        <input class="flux-form-control" type="text" wire:model="informant_contact_no" id="informant_contact_no"
          name="informant_contact_no" placeholder="Contact Number">
        @error('informant_contact_no')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
      </div>
    </div>
  </div>

</div>