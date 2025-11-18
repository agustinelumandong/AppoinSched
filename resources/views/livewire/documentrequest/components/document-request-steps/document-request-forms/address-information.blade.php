<div class="flux-card p-6"
    x-data="{
        scrollPosition: 0,
        init() {
            // Save scroll position before Livewire updates
            if (typeof Livewire !== 'undefined') {
                Livewire.hook('morph.updating', () => {
                    this.scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
                });

                // Restore scroll position after Livewire updates
                Livewire.hook('morph.updated', () => {
                    setTimeout(() => {
                        window.scrollTo({
                            top: this.scrollPosition,
                            behavior: 'instant'
                        });
                    }, 10);
                });
            }

            // Fallback: also listen for Livewire initialization
            document.addEventListener('livewire:init', () => {
                Livewire.hook('morph.updating', () => {
                    this.scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
                });

                Livewire.hook('morph.updated', () => {
                    setTimeout(() => {
                        window.scrollTo({
                            top: this.scrollPosition,
                            behavior: 'instant'
                        });
                    }, 10);
                });
            });
        }
    }">
    @if ($to_whom === 'someone_else')
        <div class="alert alert-info flex items-center gap-2 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Please provide the complete address for the person you're requesting the document for.</span>
        </div>
    @endif
    {{-- Permanent Address --}}
    <div>
        <h4 class="text-lg font-bold mb-4">Permanent Address</h4>
        <div>
            <label for="address_type" class="block text-xs font-medium mb-1">Address Type</label>
            <select class="flux-form-control mb-4 @if ($editPersonDetails === false) bg-gray-100 @endif"
                wire:model="address_type" name="address_type" id="address_type"
                @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif @if ($same_as_personal_address) disabled @endif>
                <option value="">Address Type</option>
                <option value="Permanent">Permanent</option>
                <option value="Temporary">Temporary</option>
            </select>
            @error('address_type')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div>
            <label for="address_line_1" class="block text-xs font-medium mb-1">Address Line 1</label>
            <input class="flux-form-control mb-4 @if ($editPersonDetails === false) bg-gray-100 @endif" type="text"
                wire:model="address_line_1" name="address_line_1" placeholder="Address Line 1" id="address_line_1"
                @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif @if ($same_as_personal_address) disabled @endif
                x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
            @error('address_line_1')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div>
            <label for="address_line_2" class="block text-xs font-medium mb-1">Address Line 2</label>
            <input class="flux-form-control mb-4 @if ($editPersonDetails === false) bg-gray-100 @endif" type="text"
                wire:model="address_line_2" name="address_line_2" placeholder="Address Line 2" id="address_line_2"
                @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif @if ($same_as_personal_address) disabled @endif
                x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
            @error('address_line_2')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
        </div>
        <div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex flex-col">
                    <label for="region" class="text-xs font-medium mb-1">Region</label>
                    <select id="region"
                        class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                        wire:model.live="region" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                        @if ($same_as_personal_address) disabled @endif
                        x-on:change="$el.closest('[x-data]').__x.$data.scrollPosition = window.pageYOffset || document.documentElement.scrollTop">
                        <option value="">Select Region</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region['code'] }}">{{ $region['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="province" class="text-xs font-medium mb-1">Province</label>
                    <select id="province"
                        class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                        wire:model.live="province" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                        @if ($same_as_personal_address) disabled @endif
                        x-on:change="$el.closest('[x-data]').__x.$data.scrollPosition = window.pageYOffset || document.documentElement.scrollTop">
                        <option value="">Select Province</option>
                        @foreach ($provinces as $provinceKey => $provinceName)
                            <option value="{{ $provinceKey }}">{{ $provinceName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="city" class="text-xs font-medium mb-1">City</label>
                    <select id="city"
                        class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                        wire:model.live="city" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                        @if ($same_as_personal_address) disabled @endif
                        x-on:change="$el.closest('[x-data]').__x.$data.scrollPosition = window.pageYOffset || document.documentElement.scrollTop">
                        <option value="">Select City</option>
                        @foreach ($cities as $cityKey => $cityName)
                            <option value="{{ $cityKey }}">{{ $cityName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="barangay" class="text-xs font-medium mb-1">Barangay</label>
                    <select id="barangay"
                        class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                        wire:model.live="barangay" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                        @if ($same_as_personal_address) disabled @endif
                        x-on:change="$el.closest('[x-data]').__x.$data.scrollPosition = window.pageYOffset || document.documentElement.scrollTop">
                        <option value="">Select Barangay</option>
                        @foreach ($barangays as $barangay)
                            <option value="{{ $barangay }}">{{ $barangay }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="street" class="text-xs font-medium mb-1">Street</label>
                    <input id="street"
                        class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif" type="text"
                        wire:model="street" placeholder="Street" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                        @if ($same_as_personal_address) disabled @endif
                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                    <span class="text-xs text-gray-500 mt-1">Required</span>
                </div>
                <div class="flex flex-col">
                    <label for="zip_code" class="text-xs font-medium mb-1">Zip Code</label>
                    <input id="zip_code"
                        class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif" type="tel"
                        inputmode="numeric" pattern="[0-9]*"
                        x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')"
                        wire:model="zip_code" placeholder="Zip Code" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                        @if ($same_as_personal_address) disabled @endif>
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                    <span class="text-xs text-gray-500 mt-1">Required</span>
                </div>
            </div>
        </div>
    </div>
    {{-- Present Address --}}
    <div>
        <h4 class="text-lg font-bold mb-4">Present Address</h4>
        <div class="flex items-center mb-4">
            <input type="checkbox" id="same_as_personal_address" wire:model="same_as_personal_address"
                class="checkbox checkbox-primary mr-2" />
            <label for="same_as_personal_address" class="text-sm">Same as personal address</label>
        </div>
        <div>
            <label for="address_type" class="block text-xs font-medium mb-1">Address Type</label>
            <select class="flux-form-control mb-4 @if ($editPersonDetails === false) bg-gray-100 @endif"
                wire:model="address_type" name="address_type" id="address_type"
                @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif @if ($same_as_personal_address) disabled @endif>
                <option value="">Address Type</option>
                <option value="Permanent">Permanent</option>
                <option value="Temporary">Temporary</option>
            </select>
            @error('address_type')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div>
            <label for="address_line_1" class="block text-xs font-medium mb-1">Address Line 1</label>
            <input class="flux-form-control mb-4 @if ($editPersonDetails === false) bg-gray-100 @endif"
                type="text" wire:model="address_line_1" name="address_line_1" placeholder="Address Line 1"
                id="address_line_1" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                @if ($same_as_personal_address) disabled @endif
                x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
            @error('address_line_1')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div>
            <label for="address_line_2" class="block text-xs font-medium mb-1">Address Line 2</label>
            <input class="flux-form-control mb-4 @if ($editPersonDetails === false) bg-gray-100 @endif"
                type="text" wire:model="address_line_2" name="address_line_2" placeholder="Address Line 2"
                id="address_line_2" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                @if ($same_as_personal_address) disabled @endif
                x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
            @error('address_line_2')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
        </div>
        <div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex flex-col">
                    <label for="region" class="text-xs font-medium mb-1">Region</label>
                    <select id="region"
                        class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                        wire:model.live="region" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                        @if ($same_as_personal_address) disabled @endif
                        x-on:change="$el.closest('[x-data]').__x.$data.scrollPosition = window.pageYOffset || document.documentElement.scrollTop">
                        <option value="">Select Region</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region['code'] }}">{{ $region['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="province" class="text-xs font-medium mb-1">Province</label>
                    <select id="province"
                        class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                        wire:model.live="province" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                        @if ($same_as_personal_address) disabled @endif
                        x-on:change="$el.closest('[x-data]').__x.$data.scrollPosition = window.pageYOffset || document.documentElement.scrollTop">
                        <option value="">Select Province</option>
                        @foreach ($provinces as $provinceKey => $provinceName)
                            <option value="{{ $provinceKey }}">{{ $provinceName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="city" class="text-xs font-medium mb-1">City</label>
                    <select id="city"
                        class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                        wire:model.live="city" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                        @if ($same_as_personal_address) disabled @endif
                        x-on:change="$el.closest('[x-data]').__x.$data.scrollPosition = window.pageYOffset || document.documentElement.scrollTop">
                        <option value="">Select City</option>
                        @foreach ($cities as $cityKey => $cityName)
                            <option value="{{ $cityKey }}">{{ $cityName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="barangay" class="text-xs font-medium mb-1">Barangay</label>
                    <select id="barangay"
                        class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                        wire:model.live="barangay" @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                        @if ($same_as_personal_address) disabled @endif
                        x-on:change="$el.closest('[x-data]').__x.$data.scrollPosition = window.pageYOffset || document.documentElement.scrollTop">
                        <option value="">Select Barangay</option>
                        @foreach ($barangays as $barangay)
                            <option value="{{ $barangay }}">{{ $barangay }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="street" class="text-xs font-medium mb-1">Street</label>
                    <input id="street"
                        class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                        type="text" wire:model="street" placeholder="Street"
                        @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                        @if ($same_as_personal_address) disabled @endif
                        x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                    <span class="text-xs text-gray-500 mt-1">Required</span>
                </div>
                <div class="flex flex-col">
                    <label for="zip_code" class="text-xs font-medium mb-1">Zip Code</label>
                    <input id="zip_code"
                        class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                        type="tel" inputmode="numeric" pattern="[0-9]*"
                        x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')"
                        wire:model="zip_code" placeholder="Zip Code"
                        @if ($to_whom === 'myself' && $editPersonDetails === false) disabled @endif
                        @if ($same_as_personal_address) disabled @endif>
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                    <span class="text-xs text-gray-500 mt-1">Required</span>
                </div>
            </div>
        </div>
    </div>
</div>
