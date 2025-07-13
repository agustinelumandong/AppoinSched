<div class="px-5 py-2 mt-5">
    <div class="flex flex-col gap-4">
        <div>
            <div class="header mb-4">
                <h3 class="text-xl font-semibold text-base-content">Personal Information</h3>
                <div class="flex items-center gap-2 text-sm text-base-content/70">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Please provide complete personal information</span>
                </div>
            </div>

            <!-- Info: N/A Guidance -->
            <div class="alert alert-info flex items-center gap-2 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>If a field is not applicable, you may enter <span class="font-semibold">"N/A"</span>.</span>
            </div>

            <!-- Auto-fill Information Message -->
            @if ($to_whom === 'myself')
                <div class="alert alert-info flex items-center gap-2 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Your profile information has been automatically filled. You can edit any field if
                        needed.</span>
                </div>
            @else
                <div class="alert alert-warning flex items-center gap-2 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                        </path>
                    </svg>
                    <span>Please enter the complete information for the person you're requesting the document
                        for.</span>
                </div>
            @endif

            <!-- Loading State -->
            <div wire:loading.delay class="text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2">
                </div>
                <p class="text-gray-600">Loading...</p>
            </div>

            <div class="flex flex-col gap-6 w-full" wire:loading.remove>
                @if ($this->service->title !== 'Death Certificate')
                    <div class="flux-card p-6">
                        <h4 class="text-lg font-bold mb-4">Basic Information</h4>
                        <div class="flex flex-row md:flex-col gap-4 mb-4">
                            <div class="w-full md:w-1/3">
                                <label for="last_name" class="block text-xs font-medium mb-1">Last Name</label>
                                <input class="flux-form-control md:col-span-3 w-full" type="text" wire:model="last_name"
                                    placeholder="Last Name" name="last_name" id="last_name">
                                @error('last_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="w-full md:w-1/3">
                                <label for="first_name" class="block text-xs font-medium mb-1">First Name</label>
                                <input class="flux-form-control md:col-span-3 w-full" type="text" wire:model="first_name"
                                    placeholder="First Name" name="first_name" id="first_name">
                                @error('first_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="w-full md:w-1/3">
                                <label for="middle_name" class="block text-xs font-medium mb-1">Middle
                                    Name</label>
                                <input class="flux-form-control md:col-span-3 w-full" type="text" wire:model="middle_name"
                                    placeholder="Middle Name" name="middle_name" id="middle_name">
                                @error('middle_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="w-1/7">
                                <label for="suffix" class="block text-xs font-medium mb-1">Suffix</label>
                                <select class="flux-form-control md:col-span-1 w-full" wire:model="suffix" name="suffix"
                                    id="suffix">
                                    <option value="">Suffix</option>
                                    <option value="N/A">N/A</option>
                                    <option value="Jr.">Jr.</option>
                                    <option value="Sr.">Sr.</option>
                                </select>
                                @error('suffix')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="email" class="block text-xs font-medium mb-1">Email</label>
                                <input class="flux-form-control" name="email" type="email" wire:model="email"
                                    placeholder="Email" id="email">
                                @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="phone" class="block text-xs font-medium mb-1">Contact No</label>
                                <input class="flux-form-control" type="text" name="phone" wire:model="phone"
                                    placeholder="Contact No" id="phone">
                                @error('phone')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="sex_at_birth" class="block text-xs font-medium mb-1">Sex at Birth</label>
                                <select class="flux-form-control w-full" wire:model="sex_at_birth" name="sex_at_birth"
                                    id="sex_at_birth" aria-label="Sex at Birth">
                                    <option value="">Select Sex at Birth</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                @error('sex_at_birth')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label for="date_of_birth" class="block text-xs font-medium mb-1">Date of
                                    Birth</label>
                                <input class="flux-form-control" type="date" wire:model="date_of_birth"
                                    placeholder="Date of Birth" name="date_of_birth" id="date_of_birth">
                                @error('date_of_birth')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="place_of_birth" class="block text-xs font-medium mb-1">Place of
                                    Birth</label>
                                <input class="flux-form-control" type="text" wire:model="place_of_birth"
                                    placeholder="Place of Birth" name="place_of_birth" id="place_of_birth">
                                @error('place_of_birth')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="civil_status" class="block text-xs font-medium mb-1">Civil
                                    Status</label>
                                <select class="flux-form-control" wire:model="civil_status" name="civil_status"
                                    id="civil_status">
                                    <option value="">Civil Status</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Divorced">Divorced</option>
                                    <option value="Separated">Separated</option>
                                </select>
                                @error('civil_status')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="religion" class="block text-xs font-medium mb-1">Religion</label>
                                <input class="flux-form-control" type="text" wire:model="religion" placeholder="Religion"
                                    name="religion" id="religion">
                                @error('religion')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="nationality" class="block text-xs font-medium mb-1">Nationality</label>
                                <input class="flux-form-control" type="text" wire:model="nationality"
                                    placeholder="Nationality" name="nationality" id="nationality">
                                @error('nationality')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="government_id_type" class="block text-xs font-medium mb-1">Government ID
                                    Type</label>
                                <select class="flux-form-control" wire:model="government_id_type" name="government_id_type"
                                    id="government_id_type">
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
                                @error('government_id_type')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="government_id_image_file" class="block text-xs font-medium mb-1">Government ID Image</label>
                                <input class="flux-form-control" type="file" wire:model="government_id_image_file"
                                    name="government_id_image_file" id="government_id_image_file" accept="image/*">
                                <span class="text-xs text-gray-500 mt-1">Upload a clear image of your government ID (Max 2MB)</span>
                                @error('government_id_image_file')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror

                                <!-- Show existing file if available -->
                                @if($this->government_id_image_path && !$this->government_id_image_file)
                                    <div class="mt-2">
                                        <p class="text-xs text-gray-600 mb-1">Current ID Image:</p>
                                        <img src="{{ asset('storage/' . $this->government_id_image_path) }}" alt="Government ID"
                                            class="w-32 h-20 object-cover border rounded">
                                        <p class="text-xs text-gray-500 mt-1">File: {{ basename($this->government_id_image_path) }}</p>
                                    </div>
                                @endif

                                <!-- Show new uploaded file preview -->
                                @if($this->government_id_image_file)
                                    <div class="mt-2">
                                        <p class="text-xs text-gray-600 mb-1">New Upload:</p>
                                        <img src="{{ $this->government_id_image_file->temporaryUrl() }}" alt="New Government ID"
                                            class="w-32 h-20 object-cover border rounded">
                                        <p class="text-xs text-gray-500 mt-1">File: {{ $this->government_id_image_file->getClientOriginalName() }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="flux-card p-6">
                        <h4 class="text-lg font-bold mb-4">Address Information</h4>
                        <div>
                            <label for="address_type" class="block text-xs font-medium mb-1">Address Type</label>
                            <select class="flux-form-control mb-4" wire:model="address_type" name="address_type"
                                id="address_type">
                                <option value="">Address Type</option>
                                <option value="Permanent">Permanent</option>
                                <option value="Temporary">Temporary</option>
                            </select>
                            @error('address_type')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="address_line_1" class="block text-xs font-medium mb-1">Address Line
                                1</label>
                            <input class="flux-form-control mb-4" type="text" wire:model="address_line_1"
                                name="address_line_1" placeholder="Address Line 1" id="address_line_1">
                            @error('address_line_1')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="address_line_2" class="block text-xs font-medium mb-1">Address Line
                                2</label>
                            <input class="flux-form-control mb-4" type="text" wire:model="address_line_2"
                                name="address_line_2" placeholder="Address Line 2" id="address_line_2">
                            @error('address_line_2')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="region" class="block text-xs font-medium mb-1">Region</label>
                                    <input class="flux-form-control" type="text" wire:model="region" name="region"
                                        placeholder="Region" id="region">
                                    @error('region')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="province" class="block text-xs font-medium mb-1">Province</label>
                                    <input class="flux-form-control" type="text" wire:model="province" name="province"
                                        placeholder="Province" id="province">
                                    @error('province')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="city" class="block text-xs font-medium mb-1">City</label>
                                    <input class="flux-form-control" type="text" wire:model="city" placeholder="City"
                                        name="city" id="city">
                                    @error('city')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="barangay" class="block text-xs font-medium mb-1">Barangay</label>
                                    <input class="flux-form-control" type="text" wire:model="barangay" name="barangay"
                                        placeholder="Barangay" id="barangay">
                                    @error('barangay')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="street" class="block text-xs font-medium mb-1">Street</label>
                                    <input class="flux-form-control" type="text" wire:model="street" placeholder="Street"
                                        name="street" id="street">
                                    @error('street')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label for="zip_code" class="block text-xs font-medium mb-1">Zip Code</label>
                                    <input class="flux-form-control" type="text" wire:model="zip_code"
                                        placeholder="Zip Code" name="zip_code" id="zip_code">
                                    @error('zip_code')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Family Information -->
                    @if (($this->to_whom === 'someone_else' || $this->requiresFamilyInfo()) && $this->service->title !== 'Death Certificate')
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
                                        <label for="father_last_name" class="block text-xs font-medium mb-1">Last
                                            Name</label>
                                        <input class="flux-form-control md:col-span-3 w-full" type="text"
                                            wire:model="father_last_name" placeholder="Last Name" name="father_last_name"
                                            id="father_last_name" required {{ $father_is_unknown ? 'disabled' : '' }}>
                                        @error('father_last_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="father_first_name" class="block text-xs font-medium mb-1">First
                                            Name</label>
                                        <input class="flux-form-control md:col-span-3 w-full" type="text"
                                            wire:model="father_first_name" placeholder="First Name" name="father_first_name"
                                            id="father_first_name" required {{ $father_is_unknown ? 'disabled' : '' }}>
                                        @error('father_first_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="father_middle_name" class="block text-xs font-medium mb-1">Middle
                                            Name</label>
                                        <input class="flux-form-control md:col-span-3 w-full" type="text"
                                            wire:model="father_middle_name" placeholder="Middle Name" name="father_middle_name"
                                            id="father_middle_name" required {{ $father_is_unknown ? 'disabled' : '' }}>
                                        @error('father_middle_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="w-1/7">
                                        <label for="father_suffix" class="block text-xs font-medium mb-1">Suffix</label>
                                        <select class="flux-form-control md:col-span-1 w-full" wire:model="father_suffix"
                                            name="father_suffix" id="father_suffix" {{ $father_is_unknown ? 'disabled' : '' }}>
                                            <option value="">Suffix</option>
                                            <option value="N/A">N/A</option>
                                            <option value="Jr.">Jr.</option>
                                            <option value="Sr.">Sr.</option>
                                        </select>
                                        @error('father_suffix')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label for="father_birthdate" class="block text-xs font-medium mb-1">Date of
                                            Birth</label>
                                        <input class="flux-form-control" type="date" wire:model="father_birthdate"
                                            name="father_birthdate" id="father_birthdate" {{ $father_is_unknown ? 'disabled' : '' }}>
                                        @error('father_birthdate')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="father_nationality"
                                            class="block text-xs font-medium mb-1">Nationality</label>
                                        <input class="flux-form-control" type="text" wire:model="father_nationality"
                                            placeholder="Nationality" name="father_nationality" id="father_nationality" required
                                            {{ $father_is_unknown ? 'disabled' : '' }}>
                                        @error('father_nationality')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="father_religion" class="block text-xs font-medium mb-1">Religion</label>
                                        <input class="flux-form-control" type="text" wire:model="father_religion"
                                            placeholder="Religion" name="father_religion" id="father_religion" required {{ $father_is_unknown ? 'disabled' : '' }}>
                                        @error('father_religion')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="father_contact_no" class="block text-xs font-medium mb-1">Contact
                                            No.</label>
                                        <input class="flux-form-control" type="text" wire:model="father_contact_no"
                                            placeholder="Contact Number" name="father_contact_no" id="father_contact_no"
                                            required {{ $father_is_unknown ? 'disabled' : '' }}>
                                        @error('father_contact_no')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Mother --}}
                            <div>
                                <h5 class="text-md font-bold mb-4">Mother</h5>
                                <div class="flex flex-row md:flex-col gap-4 mb-4">
                                    <div class="w-full md:w-1/3">
                                        <label for="mother_last_name" class="block text-xs font-medium mb-1">Last
                                            Name</label>
                                        <input class="flux-form-control md:col-span-3 w-full" type="text"
                                            wire:model="mother_last_name" placeholder="Last Name" name="mother_last_name"
                                            id="mother_last_name">
                                        @error('mother_last_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="mother_first_name" class="block text-xs font-medium mb-1">First
                                            Name</label>
                                        <input class="flux-form-control md:col-span-3 w-full" type="text"
                                            wire:model="mother_first_name" placeholder="First Name" name="mother_first_name"
                                            id="mother_first_name">
                                        @error('mother_first_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="mother_middle_name" class="block text-xs font-medium mb-1">Middle
                                            Name</label>
                                        <input class="flux-form-control md:col-span-3 w-full" type="text"
                                            wire:model="mother_middle_name" placeholder="Middle Name" name="mother_middle_name"
                                            id="mother_middle_name">
                                        @error('mother_middle_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="w-1/7">
                                        <label for="mother_suffix" class="block text-xs font-medium mb-1">Suffix</label>
                                        <select class="flux-form-control md:col-span-1 w-full" wire:model="mother_suffix"
                                            name="mother_suffix" id="mother_suffix">
                                            <option value="">Suffix</option>
                                            <option value="N/A">N/A</option>
                                            <option value="Jr.">Jr.</option>
                                            <option value="Sr.">Sr.</option>
                                        </select>
                                        @error('mother_suffix')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="flex flex-row md:flex-col gap-4 mb-4">
                                    <div class="w-full md:w-1/3">
                                        <label for="mother_birthdate" class="block text-xs font-medium mb-1">Mother's
                                            Birthdate</label>
                                        <input class="flux-form-control" type="date" wire:model="mother_birthdate"
                                            placeholder="Mother's Birthdate" name="mother_birthdate" id="mother_birthdate">
                                        @error('mother_birthdate')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="mother_nationality" class="block text-xs font-medium mb-1">Mother's
                                            Nationality</label>
                                        <input class="flux-form-control" type="text" wire:model="mother_nationality"
                                            placeholder="Mother's Nationality" name="mother_nationality"
                                            id="mother_nationality">
                                        @error('mother_nationality')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="mother_religion" class="block text-xs font-medium mb-1">Mother's
                                            Religion</label>
                                        <input class="flux-form-control" type="text" wire:model="mother_religion"
                                            placeholder="Mother's Religion" name="mother_religion" id="mother_religion">
                                        @error('mother_religion')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="w-full md:w-1/3">
                                        <label for="mother_contact_no" class="block text-xs font-medium mb-1">Mother's
                                            Contact
                                            No</label>
                                        <input class="flux-form-control" type="text" wire:model="mother_contact_no"
                                            placeholder="Mother's Contact No" name="mother_contact_no" id="mother_contact_no">
                                        @error('mother_contact_no')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Spouse -->
                                @if ($this->service->title === 'Certificate of No Marriage (CENOMAR)' || $this->service->title === 'Marriage Certificate' && $this->to_whom !== 'myself')
                                    <div>
                                        <h5 class="text-md font-bold mb-4">Spouse</h5>
                                        <div class="form-control">
                                            <label class="label cursor-pointer">
                                                <span class="label-text">Spouse is Unknown</span>
                                                <input type="checkbox" wire:model.live="spouse_is_unknown" class="checkbox" />
                                            </label>
                                        </div>
                                        <div class="flex flex-row md:flex-col gap-4 mb-4">
                                            <div class="w-full md:w-1/3">
                                                <label for="spouse_last_name" class="block text-xs font-medium mb-1">Last
                                                    Name</label>
                                                <input class="flux-form-control md:col-span-3 w-full" type="text"
                                                    wire:model="spouse_last_name" placeholder="Last Name" name="spouse_last_name"
                                                    id="spouse_last_name" required {{ $spouse_is_unknown ? 'disabled' : '' }}>
                                                @error('spouse_last_name')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="w-full md:w-1/3">
                                                <label for="spouse_first_name" class="block text-xs font-medium mb-1">First
                                                    Name</label>
                                                <input class="flux-form-control md:col-span-3 w-full" type="text"
                                                    wire:model="spouse_first_name" placeholder="First Name" name="spouse_first_name"
                                                    id="spouse_first_name" required {{ $spouse_is_unknown ? 'disabled' : '' }}>
                                                @error('spouse_first_name')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="w-full md:w-1/3">
                                                <label for="spouse_middle_name" class="block text-xs font-medium mb-1">Middle
                                                    Name</label>
                                                <input class="flux-form-control md:col-span-3 w-full" type="text"
                                                    wire:model="spouse_middle_name" placeholder="Middle Name"
                                                    name="spouse_middle_name" id="spouse_middle_name" required {{ $spouse_is_unknown ? 'disabled' : '' }}>
                                                @error('spouse_middle_name')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="w-1/7">
                                                <label for="spouse_suffix" class="block text-xs font-medium mb-1">Suffix</label>
                                                <select class="flux-form-control md:col-span-1 w-full" wire:model="spouse_suffix"
                                                    name="spouse_suffix" id="spouse_suffix" {{ $spouse_is_unknown ? 'disabled' : '' }}>
                                                    <option value="">Suffix</option>
                                                    <option value="N/A">N/A</option>
                                                    <option value="Jr.">Jr.</option>
                                                    <option value="Sr.">Sr.</option>
                                                </select>
                                                @error('spouse_suffix')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                            <div>
                                                <label for="spouse_birthdate" class="block text-xs font-medium mb-1">Date of
                                                    Birth</label>
                                                <input class="flux-form-control" type="date" wire:model="spouse_birthdate"
                                                    name="spouse_birthdate" id="spouse_birthdate" {{ $spouse_is_unknown ? 'disabled' : '' }}>
                                                @error('spouse_birthdate')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="spouse_nationality"
                                                    class="block text-xs font-medium mb-1">Nationality</label>
                                                <input class="flux-form-control" type="text" wire:model="spouse_nationality"
                                                    placeholder="Nationality" name="spouse_nationality" id="spouse_nationality"
                                                    required {{ $spouse_is_unknown ? 'disabled' : '' }}>
                                                @error('spouse_nationality')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="spouse_religion" class="block text-xs font-medium mb-1">Religion</label>
                                                <input class="flux-form-control" type="text" wire:model="spouse_religion"
                                                    placeholder="Religion" name="spouse_religion" id="spouse_religion" required {{ $spouse_is_unknown ? 'disabled' : '' }}>
                                                @error('spouse_religion')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="spouse_contact_no" class="block text-xs font-medium mb-1">Contact
                                                    No.</label>
                                                <input class="flux-form-control" type="text" wire:model="spouse_contact_no"
                                                    placeholder="Contact Number" name="spouse_contact_no" id="spouse_contact_no"
                                                    required {{ $spouse_is_unknown ? 'disabled' : '' }}>
                                                @error('spouse_contact_no')
                                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                    @else
                            <div class="flux-card p-6">
                                <h4 class="text-lg font-bold mb-4">Death Information</h4>

                                <!-- Full Name of the Deceased -->
                                <div class="mb-4">
                                    <h5 class="text-md font-semibold mb-3">Full Name of the Deceased</h5>
                                    <div class="flex flex-row md:flex-col gap-4 mb-4">
                                        <div class="w-full md:w-1/3">
                                            <label for="deceased_last_name" class="block text-xs font-medium mb-1">Last
                                                Name</label>
                                            <input class="flux-form-control" type="text" wire:model="deceased_last_name"
                                                placeholder="Last Name" name="deceased_last_name" id="deceased_last_name">
                                            @error('deceased_last_name')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="w-full md:w-1/3">
                                            <label for="deceased_first_name" class="block text-xs font-medium mb-1">First
                                                Name</label>
                                            <input class="flux-form-control" type="text" wire:model="deceased_first_name"
                                                placeholder="First Name" name="deceased_first_name" id="deceased_first_name">
                                            @error('deceased_first_name')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="w-full md:w-1/3">
                                            <label for="deceased_middle_name" class="block text-xs font-medium mb-1">Middle
                                                Name</label>
                                            <input class="flux-form-control" type="text" wire:model="deceased_middle_name"
                                                placeholder="Middle Name" name="deceased_middle_name" id="deceased_middle_name">
                                            @error('deceased_middle_name')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Date and Place of Death -->
                                <div class="mb-4">
                                    <h5 class="text-md font-semibold mb-3">Date and Place of Death</h5>
                                    <div class="flex flex-row md:flex-col gap-4 mb-4">
                                        <div class="w-full md:w-1/3">
                                            <label for="death_date" class="block text-xs font-medium mb-1">Death
                                                Date</label>
                                            <input class="flux-form-control" type="date" wire:model="death_date"
                                                placeholder="Death Date" name="death_date" id="death_date">
                                            @error('death_date')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="w-full md:w-1/3">
                                            <label for="death_time" class="block text-xs font-medium mb-1">Death
                                                Time</label>
                                            <input class="flux-form-control" type="time" wire:model="death_time"
                                                placeholder="Death Time" name="death_time" id="death_time">
                                            @error('death_time')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="w-full md:w-1/3">
                                            <label for="death_place" class="block text-xs font-medium mb-1">Place of
                                                Death</label>
                                            <input class="flux-form-control" type="text" wire:model="death_place"
                                                placeholder="Place of Death" name="death_place" id="death_place">
                                            @error('death_place')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Relationship to the Deceased -->
                                <div class="mb-4">
                                    <h5 class="text-md font-semibold mb-3">Your Relationship to the Deceased</h5>
                                    <div class="flex flex-row md:flex-col gap-4 mb-4">
                                        <div class="w-1/2">
                                            <label for="relationship_to_deceased"
                                                class="block text-xs font-medium mb-1">Relationship</label>
                                            <select class="flux-form-control" wire:model="relationship_to_deceased"
                                                name="relationship_to_deceased" id="relationship_to_deceased">
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
                                            @error('relationship_to_deceased')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <footer class="my-6 flex justify-end gap-2">
            <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
            <button class="btn btn-primary" wire:click="nextStep" wire:loading.disable>Next</button>
        </footer>
    </div>
</div>