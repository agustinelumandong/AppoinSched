<div class="flux-card p-6">
  <h4 class="text-lg font-bold mb-4">Address Information</h4>
  <div>
    <label for="address_type" class="block text-xs font-medium mb-1">Address Type</label>
    <select class="flux-form-control mb-4" wire:model="address_type" name="address_type" id="address_type">
      <option value="">Address Type</option>
      <option value="Permanent">Permanent</option>
      <option value="Temporary">Temporary</option>
    </select>
    @error('address_type')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
  </div>
  <div>
    <label for="address_line_1" class="block text-xs font-medium mb-1">Address Line 1</label>
    <input class="flux-form-control mb-4" type="text" wire:model="address_line_1" name="address_line_1"
      placeholder="Address Line 1" id="address_line_1">
    @error('address_line_1')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
  </div>
  <div>
    <label for="address_line_2" class="block text-xs font-medium mb-1">Address Line 2</label>
    <input class="flux-form-control mb-4" type="text" wire:model="address_line_2" name="address_line_2"
      placeholder="Address Line 2" id="address_line_2">
    @error('address_line_2')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
  </div>
  <div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label for="region" class="block text-xs font-medium mb-1">Region</label>
        <input class="flux-form-control" type="text" wire:model="region" name="region" placeholder="Region" id="region">
        @error('region')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div>
        <label for="province" class="block text-xs font-medium mb-1">Province</label>
        <input class="flux-form-control" type="text" wire:model="province" name="province" placeholder="Province"
          id="province">
        @error('province')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div>
        <label for="city" class="block text-xs font-medium mb-1">City</label>
        <input class="flux-form-control" type="text" wire:model="city" placeholder="City" name="city" id="city">
        @error('city')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div>
        <label for="barangay" class="block text-xs font-medium mb-1">Barangay</label>
        <input class="flux-form-control" type="text" wire:model="barangay" name="barangay" placeholder="Barangay"
          id="barangay">
        @error('barangay')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div>
        <label for="street" class="block text-xs font-medium mb-1">Street</label>
        <input class="flux-form-control" type="text" wire:model="street" placeholder="Street" name="street" id="street">
        @error('street')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div>
        <label for="zip_code" class="block text-xs font-medium mb-1">Zip Code</label>
        <input class="flux-form-control" type="text" wire:model="zip_code" placeholder="Zip Code" name="zip_code"
          id="zip_code">
        @error('zip_code')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
    </div>
  </div>
</div>