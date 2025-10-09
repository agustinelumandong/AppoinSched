<div class="mb-4">
    <h6 class="font-semibold mb-2">{{ $label ?? 'Parent' }} {{ isset($maiden) && $maiden ? '(Maiden Name)' : '' }}</h6>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="col-span-2">
            <label class="block text-xs font-medium mb-1">Last Name</label>
            <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_last_name"
                placeholder="Last Name"
                x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
            @error($prefix . '_last_name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div class="col-span-2">
            <label class="block text-xs font-medium mb-1">First Name</label>
            <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_first_name"
                placeholder="First Name"
                x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
            @error($prefix . '_first_name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div class="col-span-2">
            <label class="block text-xs font-medium mb-1">Middle Name</label>
            <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_middle_name"
                placeholder="Middle Name"
                x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
            @error($prefix . '_middle_name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
        </div>
        @if (!isset($maiden) || !$maiden)
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
                @error($prefix . '_suffix')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
        @endif
        <div>
            <label class="block text-xs font-medium mb-1">Citizenship</label>
            <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_citizenship"
                placeholder="Citizenship"
                x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
            @error($prefix . '_citizenship')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Residence</label>
            <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_residence"
                placeholder="Residence"
                x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
            @error($prefix . '_residence')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
        </div>
    </div>
</div>
