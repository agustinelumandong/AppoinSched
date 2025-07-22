<div class="w-full">
  <div class="flux-card p-6 mb-6">
    <h4 class="text-lg font-bold mb-4">Bride Information</h4>
    @include('livewire.documentrequest.components.document-request-steps.document-request-forms.readonly.partials.person-info', [
      'prefix' => 'bride',
      'details' => $documentRequest->details
    ])
    <h5 class="text-md font-bold mt-6 mb-2">Father's Information</h5>
    @include('livewire.documentrequest.components.document-request-steps.document-request-forms.readonly.partials.parent-info', [
      'prefix' => 'bride_father',
      'details' => $documentRequest->details
    ])
    <h5 class="text-md font-bold mt-6 mb-2">Mother's Information</h5>
    @include('livewire.documentrequest.components.document-request-steps.document-request-forms.readonly.partials.parent-info', [
      'prefix' => 'bride_mother',
      'details' => $documentRequest->details
    ])
  </div>
</div>