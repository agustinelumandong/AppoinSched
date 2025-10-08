<div class="space-y-4">
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Last Maiden Name</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details[$prefix . '_last_name'] ?? 'N/A' }}
    </p>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">First Maiden Name</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details[$prefix . '_first_name'] ?? 'N/A' }}
    </p>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Middle Maiden Name</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details[$prefix . '_middle_name'] ?? 'N/A' }}
    </p>
  </div>
  @if(isset($details[$prefix . '_suffix']))
    <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Suffix</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details[$prefix . '_suffix'] ?? 'N/A' }}
    </p>
    </div>
  @endif
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Citizenship</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details[$prefix . '_citizenship'] ?? 'N/A' }}
    </p>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Residence</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details[$prefix . '_residence'] ?? 'N/A' }}
    </p>
  </div>
</div>