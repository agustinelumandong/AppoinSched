<div class="space-y-4">
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Person(s) who gave consent/advice</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details->consent_person ?? 'N/A' }}
    </p>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Relationship</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details->consent_relationship ?? 'N/A' }}
    </p>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Citizenship</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details->consent_citizenship ?? 'N/A' }}
    </p>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-600 mb-1">Residence</label>
    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
      {{ $details->consent_residence ?? 'N/A' }}
    </p>
  </div>
</div>