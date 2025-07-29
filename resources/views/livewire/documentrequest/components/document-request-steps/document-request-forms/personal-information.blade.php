<div class="PersonalInformation">
  <div class="header mb-4 flex flex-row justify-between items-center">
    <div>
      <h3 class="text-xl font-semibold text-base-content">Personal Information</h3>
      <div class="flex items-center gap-2 text-sm text-base-content/70">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Please provide complete personal information</span>
      </div>

    </div>
    <div class="flex flex-row gap-2 mb-4">
      @if($editPersonDetails === false)
      <button type="button" class="flux-btn flux-btn-primary" wire:click="editPersonDetailsBtn">Edit</button>
    @else
      <button type="button" class="flux-btn flux-btn-primary" wire:click="lockPersonDetailsBtn">Lock</button>
    @endif
    </div>

  </div>

  <!-- Info Redundancy Error -->
  @error('info_redundancy')
    <div class="alert alert-error mb-4">
    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <span>{{ $message }}</span>
    </div>
  @enderror

  <!-- Info: N/A Guidance -->
  <div class="alert alert-info flex items-center gap-2 mb-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <span>If a field is not applicable, you may enter <span class="font-semibold">"N/A"</span>.</span>
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
  <!-- Auto-fill Information Message -->
  @if ($to_whom === 'myself')
    <div class="alert alert-info flex items-center gap-2 mb-2">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <span>Your profile information has been automatically filled. You can edit any field if needed.</span>
    </div>
  @else
    <div class="alert alert-warning flex items-center gap-2 mb-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
      </path>
    </svg>
    <span>Please enter the complete information for the person you're requesting the document for.</span>
    </div>
  @endif
  <!-- Loading State -->
  <div wire:loading.delay class="text-center">
    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
    <p class="text-gray-600">Loading...</p>
  </div>



  <div class="flex flex-col gap-6 w-full" wire:loading.remove>
    <div class="flux-card p-6">
      <h4 class="text-lg font-bold mb-4">Basic Information</h4>
      <div class="flex flex-row md:flex-col gap-4 mb-4">
        <div class="w-full md:w-1/3">
          <label for="last_name" class="block text-xs font-medium mb-1">Last Name</label>
          <input class="flux-form-control md:col-span-3 w-full @if($editPersonDetails === false) bg-gray-100 @endif"
            type="text" wire:model="last_name" placeholder="Last Name" name="last_name" id="last_name"
            @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
          @error('last_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
          <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div class="w-full md:w-1/3">
          <label for="first_name" class="block text-xs font-medium mb-1">First Name</label>
          <input class="flux-form-control md:col-span-3 w-full @if($editPersonDetails === false) bg-gray-100 @endif"
            type="text" wire:model="first_name" placeholder="First Name" name="first_name" id="first_name"
            @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
          @error('first_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
          <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div class="w-full md:w-1/3">
          <label for="middle_name" class="block text-xs font-medium mb-1">Middle Name</label>
          <input class="flux-form-control md:col-span-3 w-full @if($editPersonDetails === false) bg-gray-100 @endif"
            type="text" wire:model="middle_name" placeholder="Middle Name" name="middle_name" id="middle_name"
            @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
          @error('middle_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
          <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
        </div>
        <div class="w-1/7">
          <label for="suffix" class="block text-xs font-medium mb-1">Suffix</label>
          <select class="flux-form-control md:col-span-1 w-full @if($editPersonDetails === false) bg-gray-100 @endif"
            wire:model="suffix" name="suffix" id="suffix" @if($to_whom === 'myself' && $editPersonDetails === false)
      disabled @endif>
            <option value="">Suffix</option>
            <option value="N/A">N/A</option>
            <option value="Jr.">Jr.</option>
            <option value="Sr.">Sr.</option>
            <option value="I">I</option>
            <option value="II">II</option>
            <option value="III">III</option>
          </select>
          @error('suffix')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
          <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
          <label for="email" class="block text-xs font-medium mb-1">Email</label>
          <input class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" name="email"
            type="email" wire:model="email" placeholder="Email" id="email" @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
          @error('email')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
          <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div>
          <label for="phone" class="block text-xs font-medium mb-1">Contact No</label>
          <input class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" type="text" name="phone"
            wire:model="phone" placeholder="Contact No" id="phone" @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
          @error('phone')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
          <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div>
          <label for="sex_at_birth" class="block text-xs font-medium mb-1">Sex at Birth</label>
          <select class="flux-form-control w-full @if($editPersonDetails === false) bg-gray-100 @endif"
            wire:model="sex_at_birth" name="sex_at_birth" id="sex_at_birth" aria-label="Sex at Birth"
            @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
            <option value="">Select Sex at Birth</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
          @error('sex_at_birth')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
          <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
          <label for="date_of_birth" class="block text-xs font-medium mb-1">Date of Birth</label>
          <input class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" type="date"
            wire:model="date_of_birth" placeholder="Date of Birth" name="date_of_birth" id="date_of_birth"
            @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
          @error('date_of_birth')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
          <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div>
          <label for="place_of_birth" class="block text-xs font-medium mb-1">Place of Birth</label>
          <input class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" type="text"
            wire:model="place_of_birth" placeholder="Place of Birth" name="place_of_birth" id="place_of_birth"
            @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
          @error('place_of_birth')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
          <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div>
          <label for="civil_status" class="block text-xs font-medium mb-1">Civil Status</label>
          <select class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif"
            wire:model="civil_status" name="civil_status" id="civil_status" @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
            <option value="">Civil Status</option>
            <option value="Single">Single</option>
            <option value="Married">Married</option>
            <option value="Widowed">Widowed</option>
            <option value="Divorced">Divorced</option>
            <option value="Separated">Separated</option>
          </select>
          @error('civil_status')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
          <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="religion" class="block text-xs font-medium mb-1">Religion</label>
          <input class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" type="text"
            wire:model="religion" placeholder="Religion" name="religion" id="religion" @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
          @error('religion')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
          <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
        </div>
        <div>
          <label for="nationality" class="block text-xs font-medium mb-1">Nationality</label>
          <input class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" type="text"
            wire:model="nationality" placeholder="Nationality" name="nationality" id="nationality"
            @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
          @error('nationality')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
          <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
          <label for="government_id_type" class="block text-xs font-medium mb-1">Government ID Type</label>
          <select class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif"
            wire:model="government_id_type" name="government_id_type" id="government_id_type" @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
            <option value="">Select Government ID Type *</option>
            <option value="SSS">SSS</option>
            <option value="GSIS">GSIS</option>
            <option value="TIN">TIN</option>
            <option value="PhilHealth">PhilHealth</option>
            <option value="Passport">Passport</option>
            <option value="Driver's License">Driver's License</option>
            <option value="UMID">UMID</option>
            <option value="Postal ID">Postal ID</option>
            <option value="Voter's ID">Voter's ID</option>
            <option value="Senior Citizen ID">Senior Citizen ID</option>
            <option value="Other">Other</option>
          </select>
          @error('government_id_type')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
          <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div>
          <label for="government_id_image_file" class="block text-xs font-medium mb-1">Government ID PDF</label>
          <input class="flux-form-control @if($editPersonDetails === false) bg-gray-100 @endif" type="file"
            wire:model="government_id_image_file" name="government_id_image_file" id="government_id_image_file"
            accept=".pdf" @if($to_whom === 'myself' && $editPersonDetails === false) disabled @endif>
          <span class="text-xs text-gray-500 mt-1">Required</span>
          <span class="text-xs text-gray-500 mt-1">Upload a PDF copy of your government ID (Max 5MB)</span>
          @error('government_id_image_file')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror

        </div>
      </div>
    </div>
  </div>
</div>