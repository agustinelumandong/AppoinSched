{{-- Special Permit Information Section --}}
<div class="space-y-6">
    <div class="header mb-4">
        <h3 class="text-xl font-semibold text-base-content">Special Permit Information</h3>
        <div class="flex items-center gap-2 text-sm text-base-content/70">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Please provide the establishment details for the special permit</span>
        </div>
    </div>

    {{-- Establishment/Office/Person Name --}}
    
        <label for="establishment_name" class="label mt-2">
            <span class="label-text font-medium">Name of Establishment/Office/Person *</span>
        </label>
        <input type="text" id="establishment_name" wire:model="establishment_name" class="flux-form-control"
            placeholder="Enter the name of establishment, office, or person" required
            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })" />
        @error('establishment_name')
            <div class="text-error text-sm mt-1">{{ $message }}</div>
        @enderror
    

    {{-- Establishment Address --}}
     
        <label for="establishment_address" class="label">
            <span class="label-text font-medium">Address of the Establishment *</span>
        </label>
        <textarea id="establishment_address" wire:model="establishment_address" class="flux-form-control min-h-[100px]"
            placeholder="Enter the complete address of the establishment" required
            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })"></textarea>
        @error('establishment_address')
            <div class="text-error text-sm mt-1">{{ $message }}</div>
        @enderror
     

    {{-- Purpose --}}
     
        <label for="establishment_purpose" class="label">
            <span class="label-text font-medium">Purpose *</span>
        </label>
        <textarea id="establishment_purpose" wire:model="establishment_purpose" class="flux-form-control min-h-[100px]"
            placeholder="Describe the purpose of the special permit" required
            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })"></textarea>
        @error('establishment_purpose')
            <div class="text-error text-sm mt-1">{{ $message }}</div>
        @enderror
     
</div>
