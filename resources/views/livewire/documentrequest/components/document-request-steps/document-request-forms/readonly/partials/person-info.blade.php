<div class="space-y-4">
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details[$prefix . '_last_name'] ?? 'N/A' }}
    </p>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details[$prefix . '_first_name'] ?? 'N/A' }}
    </p>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details[$prefix . '_middle_name'] ?? 'N/A' }}
    </p>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Suffix</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details[$prefix . '_suffix'] ?? 'N/A' }}
    </p>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Age</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details[$prefix . '_age'] ?? 'N/A' }}
    </p>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Date of Birth</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details[$prefix . '_date_of_birth'] ?? 'N/A' }}
    </p>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Place of Birth</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details[$prefix . '_place_of_birth'] ?? 'N/A' }}
    </p>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Sex</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details[$prefix . '_sex'] ?? 'N/A' }}
    </p>
  </div>
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
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Religion</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details[$prefix . '_religion'] ?? 'N/A' }}
    </p>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Civil Status</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details[$prefix . '_civil_status'] ?? 'N/A' }}
    </p>
  </div>
</div>