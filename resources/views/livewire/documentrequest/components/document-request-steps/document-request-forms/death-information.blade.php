<div class="flux-card p-6">

    @if ($to_whom === 'someone_else')
        <div class="alert alert-info flex items-center gap-2 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none"
                viewBox="0 0 24 24">
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
                    name="deceased_last_name" id="deceased_last_name"
                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                @error('deceased_last_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="w-full md:w-1/3">
                <label for="deceased_first_name" class="block text-xs font-medium mb-1">First Name</label>
                <input class="flux-form-control" type="text" wire:model="deceased_first_name"
                    placeholder="First Name" name="deceased_first_name" id="deceased_first_name"
                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                @error('deceased_first_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="w-full md:w-1/3">
                <label for="deceased_middle_name" class="block text-xs font-medium mb-1">Middle Name</label>
                <input class="flux-form-control" type="text" wire:model="deceased_middle_name"
                    placeholder="Middle Name" name="deceased_middle_name" id="deceased_middle_name"
                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                @error('deceased_middle_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
        </div>
    </div>

    <!-- Parental Information -->
    <div class="mb-4">
        <h5 class="text-md font-semibold mb-3">Father of the Deceased</h5>
        <div class="flex flex-row md:flex-col gap-4 mb-4">
            <div class="w-full md:w-1/3">
                <label for="deceased_father_last_name" class="block text-xs font-medium mb-1">Last Name</label>
                <input class="flux-form-control" type="text" wire:model="deceased_father_last_name"
                    id="deceased_father_last_name" name="deceased_father_last_name" placeholder="Last Name"
                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                @error('deceased_father_last_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
            <div class="w-full md:w-1/3">
                <label for="deceased_father_first_name" class="block text-xs font-medium mb-1">First Name</label>
                <input class="flux-form-control" type="text" wire:model="deceased_father_first_name"
                    id="deceased_father_first_name" name="deceased_father_first_name" placeholder="First Name"
                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                @error('deceased_father_first_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
            <div class="w-full md:w-1/3">
                <label for="deceased_father_middle_name" class="block text-xs font-medium mb-1">Middle Name</label>
                <input class="flux-form-control" type="text" wire:model="deceased_father_middle_name"
                    id="deceased_father_middle_name" name="deceased_father_middle_name" placeholder="Middle Name"
                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                @error('deceased_father_middle_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
        </div>
        <h5 class="text-md font-semibold mb-3">Mother of the Deceased</h5>
        <div class="flex flex-row md:flex-col gap-4 mb-4">
            <div class="w-full md:w-1/3">
                <label for="deceased_mother_last_name" class="block text-xs font-medium mb-1">Last Maiden Name</label>
                <input class="flux-form-control" type="text" wire:model="deceased_mother_last_name"
                    id="deceased_mother_last_name" name="deceased_mother_last_name" placeholder="Last Name"
                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                @error('deceased_mother_last_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
            <div class="w-full md:w-1/3">
                <label for="deceased_mother_first_name" class="block text-xs font-medium mb-1">First Maiden
                    Name</label>
                <input class="flux-form-control" type="text" wire:model="deceased_mother_first_name"
                    id="deceased_mother_first_name" name="deceased_mother_first_name" placeholder="First Name"
                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                @error('deceased_mother_first_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
            <div class="w-full md:w-1/3">
                <label for="deceased_mother_middle_name" class="block text-xs font-medium mb-1">Middle Maiden
                    Name</label>
                <input class="flux-form-control" type="text" wire:model="deceased_mother_middle_name"
                    id="deceased_mother_middle_name" name="deceased_mother_middle_name" placeholder="Middle Name"
                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                @error('deceased_mother_middle_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
        </div>
    </div>

</div>
