<div class="mt-8">
  <h4 class="text-lg font-bold mb-4">Consent/Advice Section (If applicable)</h4>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div>
      <label class="block text-xs font-medium mb-1">Person(s) who gave consent/advice</label>
      <input class="flux-form-control" type="text" wire:model="consent_person" placeholder="Name(s)">
      @error('consent_person')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      <span class="text-xs text-gray-500 mt-1">Required</span>
    </div>
    <div>
      <label class="block text-xs font-medium mb-1">Relationship</label>
      <input class="flux-form-control" type="text" wire:model="consent_relationship" placeholder="Relationship">
      @error('consent_relationship')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      <span class="text-xs text-gray-500 mt-1">Required</span>
    </div>
    <div>
      <label class="block text-xs font-medium mb-1">Citizenship</label>
      <input class="flux-form-control" type="text" wire:model="consent_citizenship" placeholder="Citizenship">
      @error('consent_citizenship')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
    </div>
    <div>
      <label class="block text-xs font-medium mb-1">Residence</label>
      <input class="flux-form-control" type="text" wire:model="consent_residence" placeholder="Residence">
      @error('consent_residence')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
      <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
    </div>
  </div>
</div>