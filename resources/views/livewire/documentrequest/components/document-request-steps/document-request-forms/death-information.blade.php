<div class="flux-card p-6">
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
      </div>
      <div class="w-full md:w-1/3">
        <label for="deceased_first_name" class="block text-xs font-medium mb-1">First Name</label>
        <input class="flux-form-control" type="text" wire:model="deceased_first_name" placeholder="First Name"
          name="deceased_first_name" id="deceased_first_name">
        @error('deceased_first_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div class="w-full md:w-1/3">
        <label for="deceased_middle_name" class="block text-xs font-medium mb-1">Middle Name</label>
        <input class="flux-form-control" type="text" wire:model="deceased_middle_name" placeholder="Middle Name"
          name="deceased_middle_name" id="deceased_middle_name">
        @error('deceased_middle_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
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
      </div>
      <div class="w-full md:w-1/3">
        <label for="death_time" class="block text-xs font-medium mb-1">Death Time</label>
        <input class="flux-form-control" type="time" wire:model="death_time" placeholder="Death Time" name="death_time"
          id="death_time">
        @error('death_time')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
      <div class="w-full md:w-1/3">
        <label for="death_place" class="block text-xs font-medium mb-1">Place of Death</label>
        <input class="flux-form-control" type="text" wire:model="death_place" placeholder="Place of Death"
          name="death_place" id="death_place">
        @error('death_place')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
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
      </div>
    </div>
  </div>
</div>