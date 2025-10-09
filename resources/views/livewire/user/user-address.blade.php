<div>
    <!-- Permanent Address Information -->
    <div class="flux-card p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">Permanent Address</h2>
        <div class="flex flex-col mb-4">
            <label for="address_type" class="text-xs font-medium mb-1">Address Type</label>
            <select id="address_type" class="flux-form-control bg-gray-100" wire:model="address_type" disabled>
                <option value="Permanent" selected>Permanent</option>
            </select>
        </div>
        <div class="flex flex-col mb-4">
            <label for="address_line_1" class="text-xs font-medium mb-1">Address Line 1</label>
            <input id="address_line_1" class="flux-form-control" type="text" wire:model="address_line_1"
                placeholder="Address Line 1">
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div class="flex flex-col mb-4">
            <label for="address_line_2" class="text-xs font-medium mb-1">Address Line 2</label>
            <input id="address_line_2" class="flux-form-control" type="text" wire:model="address_line_2"
                placeholder="Address Line 2">
            <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex flex-col">
                <label for="region" class="text-xs font-medium mb-1">Region</label>
                <select id="region" class="flux-form-control" wire:model.live="region">
                    <option value="">Select Region</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region['code'] }}">{{ $region['name'] }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="province" class="text-xs font-medium mb-1">Province</label>
                <select id="province" class="flux-form-control" wire:model.live="province">
                    <option value="">Select Province</option>
                    @foreach ($provinces as $provinceKey => $provinceName)
                        <option value="{{ $provinceKey }}">{{ $provinceName }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="city" class="text-xs font-medium mb-1">City</label>
                <select id="city" class="flux-form-control" wire:model.live="city">
                    <option value="">Select City</option>
                    @foreach ($cities as $cityKey => $cityName)
                        <option value="{{ $cityKey }}">{{ $cityName }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="barangay" class="text-xs font-medium mb-1">Barangay</label>
                <select id="barangay" class="flux-form-control" wire:model.live="barangay">
                    <option value="">Select Barangay</option>
                    @foreach ($barangays as $barangay)
                        <option value="{{ $barangay }}">{{ $barangay }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="street" class="text-xs font-medium mb-1">Street</label>
                <input id="street" class="flux-form-control" type="text" wire:model="street" placeholder="Street">
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
            <div class="flex flex-col">
                <label for="zip_code" class="text-xs font-medium mb-1">Zip Code</label>
                <input id="zip_code" class="flux-form-control" type="tel" 
                        inputmode="numeric" 
                        pattern="[0-9]*"
                        x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')" wire:model="zip_code" placeholder="Zip Code">
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
        </div>
    </div>

    <!-- Present Address Information -->
    <div class="flux-card p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold">Present Address</h2>
            </div>
            <div>
                <label class="label cursor-pointer">
                    <span class="label-text text-sm">Same as Permanent Address</span>
                    <input type="checkbox" wire:model.live="same_as_permanent" class="checkbox checkbox-primary" />
                </label>
            </div>
        </div>

        <div class="flex flex-col mb-4">
            <label for="temp_address_type" class="text-xs font-medium mb-1">Address Type</label>
            <select id="temp_address_type" class="flux-form-control bg-gray-100" wire:model="temporary_address_type"
                disabled>
                <option value="Temporary" selected>Temporary</option>
            </select>
        </div>
        <div class="flex flex-col mb-4">
            <label for="temp_address_line_1" class="text-xs font-medium mb-1">Address Line 1</label>
            <input id="temp_address_line_1" class="flux-form-control" type="text" wire:model="temporary_address_line_1"
                placeholder="Address Line 1" {{ $same_as_permanent ? 'disabled' : '' }}>
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div class="flex flex-col mb-4">
            <label for="temp_address_line_2" class="text-xs font-medium mb-1">Address Line 2</label>
            <input id="temp_address_line_2" class="flux-form-control" type="text" wire:model="temporary_address_line_2"
                placeholder="Address Line 2" {{ $same_as_permanent ? 'disabled' : '' }}>
            <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex flex-col">
                <label for="temp_region" class="text-xs font-medium mb-1">Region</label>
                <select id="temp_region" class="flux-form-control" wire:model.live="temporary_region" {{ $same_as_permanent ? 'disabled' : '' }}>
                    <option value="">Select Region</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region['code'] }}">{{ $region['name'] }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="temp_province" class="text-xs font-medium mb-1">Province</label>
                <select id="temp_province" class="flux-form-control" wire:model.live="temporary_province" {{ $same_as_permanent ? 'disabled' : '' }}>
                    <option value="">Select Province</option>
                    @foreach ($provinces as $provinceKey => $provinceName)
                        <option value="{{ $provinceKey }}">{{ $provinceName }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="temp_city" class="text-xs font-medium mb-1">City</label>
                <select id="temp_city" class="flux-form-control" wire:model.live="temporary_city" {{ $same_as_permanent ? 'disabled' : '' }}>
                    <option value="">Select City</option>
                    @foreach ($cities as $cityKey => $cityName)
                        <option value="{{ $cityKey }}">{{ $cityName }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="temp_barangay" class="text-xs font-medium mb-1">Barangay</label>
                <select id="temp_barangay" class="flux-form-control" wire:model.live="temporary_barangay" {{ $same_as_permanent ? 'disabled' : '' }}>
                    <option value="">Select Barangay</option>
                    @foreach ($barangays as $barangay)
                        <option value="{{ $barangay }}">{{ $barangay }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="temp_street" class="text-xs font-medium mb-1">Street</label>
                <input id="temp_street" class="flux-form-control" type="text" wire:model="temporary_street"
                    placeholder="Street" {{ $same_as_permanent ? 'disabled' : '' }}>
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
            <div class="flex flex-col">
                <label for="temp_zip_code" class="text-xs font-medium mb-1">Zip Code</label>
                <input id="temp_zip_code" class="flux-form-control" type="tel" 
                        inputmode="numeric" 
                        pattern="[0-9]*"
                        x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')" wire:model="temporary_zip_code"
                    placeholder="Zip Code" {{ $same_as_permanent ? 'disabled' : '' }}>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
        </div>
    </div>
</div>