<div class="mt-8">
  <h4 class="text-lg font-bold mb-4">Consent/Advice Section (If applicable)</h4>
  @include('livewire.documentrequest.components.document-request-steps.document-request-forms.readonly.partials.consent-info', [
    'details' => $documentRequest->details
])
</div> 