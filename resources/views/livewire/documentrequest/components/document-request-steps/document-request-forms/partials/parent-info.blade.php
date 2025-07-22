<div class="mb-4">
  <h6 class="font-semibold mb-2">{{ $label ?? 'Parent' }} {{ isset($maiden) && $maiden ? '(Maiden Name)' : '' }}</h6>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="col-span-2">
      <label class="block text-xs font-medium mb-1">Last Name</label>
      <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_last_name" placeholder="Last Name">
      @error($prefix . '_last_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    </div>
    <div class="col-span-2">
      <label class="block text-xs font-medium mb-1">First Name</label>
      <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_first_name" placeholder="First Name">
      @error($prefix . '_first_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    </div>
    <div class="col-span-2">
      <label class="block text-xs font-medium mb-1">Middle Name</label>
      <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_middle_name" placeholder="Middle Name">
      @error($prefix . '_middle_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    </div>
    @if(!isset($maiden) || !$maiden)
      <div class="col-span-2">
        <label class="block text-xs font-medium mb-1">Suffix</label>
        <select class="flux-form-control" wire:model="{{ $prefix }}_suffix">
          <option value="">Suffix</option>
          <option value="N/A">N/A</option>
          <option value="Jr.">Jr.</option>
          <option value="Sr.">Sr.</option>
        </select>
        @error($prefix . '_suffix')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      </div>
    @endif
    <div>
      <label class="block text-xs font-medium mb-1">Citizenship</label>
      <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_citizenship" placeholder="Citizenship">
      @error($prefix . '_citizenship')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    </div>
    <div>
      <label class="block text-xs font-medium mb-1">Residence</label>
      <input class="flux-form-control" type="text" wire:model="{{ $prefix }}_residence" placeholder="Residence">
      @error($prefix . '_residence')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
    </div>
  </div>
</div>