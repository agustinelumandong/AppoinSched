<div>
    <!-- Family Information -->
    <div class="flux-card p-6">
        <h2 class="text-xl font-bold mb-4">{{ label_with_bisaya('Family Information', 'family_information') }}</h2>
        <div>
            <h5 class="text-md font-bold mb-4">{{ label_with_bisaya('Father', 'father') }}</h5>
            <div class="form-control ">
                <label class="label cursor-pointer">
                    <span class="label-text">{{ label_with_bisaya('Father is Unknown', 'father_is_unknown') }}</span>
                    <input type="checkbox" wire:model.live="father_is_unknown" class="checkbox" />
                </label>

            </div>
            <div class="flex flex-row md:flex-col gap-4 mb-4">
                <div class="w-full md:w-1/3">
                    <label for="father_last_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Last Name', 'father_last_name') }}</label>
                    <input class="flux-form-control md:col-span-3 w-full" type="text" wire:model="father_last_name"
                        placeholder="Last Name" name="father_last_name" id="father_last_name" required
                        {{ $father_is_unknown ? 'disabled' : '' }}
                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                    @error('father_last_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
                <div class="w-full md:w-1/3">
                    <label for="father_first_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('First Name', 'father_first_name') }}</label>
                    <input class="flux-form-control md:col-span-3 w-full" type="text" wire:model="father_first_name"
                        placeholder="First Name" name="father_first_name" id="father_first_name" required
                        {{ $father_is_unknown ? 'disabled' : '' }}
                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                    @error('father_first_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <span class="text-xs text-gray-500 mt-1">Required</span>
                </div>
                <div class="w-full md:w-1/3">
                    <label for="father_middle_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Middle Name', 'father_middle_name') }}</label>
                    <input class="flux-form-control md:col-span-3 w-full" type="text" wire:model="father_middle_name"
                        placeholder="Middle Name" name="father_middle_name" id="father_middle_name" required
                        {{ $father_is_unknown ? 'disabled' : '' }}
                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                    @error('father_middle_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
                <div class="w-1/7">
                    <label for="father_suffix" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Suffix', 'father_suffix') }}</label>
                    <select class="flux-form-control md:col-span-1 w-full" wire:model="father_suffix"
                        name="father_suffix" id="father_suffix" {{ $father_is_unknown ? 'disabled' : '' }}>
                        <option value="">Suffix</option>
                        <option value="N/A">N/A</option>
                        <option value="Jr.">Jr.</option>
                        <option value="Sr.">Sr.</option>
                        <option value="I">I</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                    </select>
                    @error('father_suffix')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
            </div>
            <div class="flex flex-row md:flex-col gap-4 mb-4">
                <div class="w-full md:w-1/3">
                    <label for="father_birthdate" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Date of Birth', 'father_birthdate') }}</label>
                    <input class="flux-form-control" type="date" wire:model="father_birthdate"
                        name="father_birthdate" id="father_birthdate" {{ $father_is_unknown ? 'disabled' : '' }}>
                    @error('father_birthdate')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <span class="text-xs text-gray-500 mt-1">Required</span>
                </div>
                <div class="w-full md:w-1/3">
                    <label for="father_nationality" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Nationality', 'father_nationality') }}</label>
                    <input class="flux-form-control" type="text" wire:model="father_nationality"
                        placeholder="Nationality" name="father_nationality" id="father_nationality" required
                        {{ $father_is_unknown ? 'disabled' : '' }}
                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                    @error('father_nationality')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
                <div class="w-full md:w-1/3">
                    <label for="father_religion" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Religion', 'father_religion') }}</label>
                    <input class="flux-form-control" type="text" wire:model="father_religion" placeholder="Religion"
                        name="father_religion" id="father_religion" required {{ $father_is_unknown ? 'disabled' : '' }}
                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                    @error('father_religion')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
                <div class="w-full md:w-1/3">
                    <label for="father_contact_no" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Contact No.', 'father_contact_no') }}</label>
                    <input class="flux-form-control" type="tel" inputmode="numeric" pattern="[0-9]*"
                        maxlength="11"
                        x-on:input="$event.target.value = $event.target.value === 'N/A' ? 'N/A' : $event.target.value.replace(/[^0-9]/g, '').slice(0, 11)"
                        wire:model="father_contact_no" placeholder="Contact Number" name="father_contact_no"
                        id="father_contact_no" required {{ $father_is_unknown ? 'disabled' : '' }}>
                    @error('father_contact_no')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
            </div>
        </div>
        <div>
            <h5 class="text-md font-bold mb-4">{{ label_with_bisaya('Mother', 'mother') }}</h5>
            <div class="form-control">
                <label class="label cursor-pointer">
                    <span class="label-text">{{ label_with_bisaya('Mother is Unknown', 'mother_is_unknown') }}</span>
                    <input type="checkbox" wire:model.live="mother_is_unknown" class="checkbox" />
                </label>
            </div>
            <div class="flex flex-row md:flex-col gap-4 mb-4">
                <div class="w-full md:w-1/3">
                    <label for="mother_last_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Mother\'s Maiden Last Name', 'last_maiden_name') }}</label>
                    <input class="flux-form-control md:col-span-3 w-full " type="text"
                        wire:model="mother_last_name" placeholder="Last Name" name="mother_last_name"
                        id="mother_last_name" {{ $mother_is_unknown ? 'disabled' : '' }}
                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                    @error('mother_last_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
                <div class="w-full md:w-1/3">
                    <label for="mother_first_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Mother\'s Maiden First Name', 'first_maiden_name') }}</label>
                    <input class="flux-form-control md:col-span-3 w-full " type="text"
                        wire:model="mother_first_name" placeholder="First Name" name="mother_first_name"
                        id="mother_first_name" {{ $mother_is_unknown ? 'disabled' : '' }}
                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                    @error('mother_first_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
                <div class="w-full md:w-1/3">
                    <label for="mother_middle_name" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Mother\'s Maiden Middle Name', 'middle_maiden_name') }}</label>
                    <input class="flux-form-control md:col-span-3 w-full " type="text"
                        wire:model="mother_middle_name" placeholder="Middle Name" name="mother_middle_name"
                        id="mother_middle_name" {{ $mother_is_unknown ? 'disabled' : '' }}
                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                    @error('mother_middle_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
            </div>
        </div>
        <div class="flex flex-row md:flex-col gap-4 mb-4">
            <div class="w-full md:w-1/3">
                <label for="mother_birthdate" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Mother\'s Birthdate', 'mother_birthdate') }}</label>
                <input class="flux-form-control " type="date" wire:model="mother_birthdate"
                    placeholder="Mother's Birthdate" name="mother_birthdate" id="mother_birthdate"
                    {{ $mother_is_unknown ? 'disabled' : '' }}>
                @error('mother_birthdate')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
            <div class="w-full md:w-1/3">
                <label for="mother_nationality" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Mother\'s Nationality', 'mother_nationality') }}</label>
                <input class="flux-form-control " type="text" wire:model="mother_nationality"
                    placeholder="Mother's Nationality" name="mother_nationality" id="mother_nationality"
                    {{ $mother_is_unknown ? 'disabled' : '' }}
                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                @error('mother_nationality')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>

            <div class="w-full md:w-1/3">
                <label for="mother_religion" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Mother\'s Religion', 'mother_religion') }}</label>
                <input class="flux-form-control " type="text" wire:model="mother_religion"
                    placeholder="Mother's Religion" name="mother_religion" id="mother_religion"
                    {{ $mother_is_unknown ? 'disabled' : '' }}
                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                @error('mother_religion')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>

            </div>
            <div class="w-full md:w-1/3">
                <label for="mother_contact_no" class="block text-xs font-medium mb-1">{{ label_with_bisaya('Mother\'s Contact No', 'mother_contact_no') }}</label>
                <input class="flux-form-control" type="tel" inputmode="numeric" pattern="[0-9]*"
                    maxlength="11"
                    x-on:input="$event.target.value = $event.target.value === 'N/A' ? 'N/A' : $event.target.value.replace(/[^0-9]/g, '').slice(0, 11)"
                    wire:model="mother_contact_no" placeholder="Mother's Contact No" name="mother_contact_no"
                    id="mother_contact_no" {{ $mother_is_unknown ? 'disabled' : '' }}>
                @error('mother_contact_no')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
        </div>
    </div>
</div>
