{{-- Marriage License (Read-Only) --}}
<div class="mirrage-license">
    @if($to_whom === 'someone_else')
        <div class="alert alert-info flex items-center gap-2 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Please provide the complete information for the person you're requesting the document for.</span>
        </div>
    @endif
    <div class="header mb-4">
        <h3 class="text-xl font-semibold text-base-content">Marriage License</h3>
        <div class="flex items-center gap-2 text-sm text-base-content/70">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Marriage license information as submitted</span>
        </div>
    </div>
    <div class="flex flex-col md:flex-row gap-4">
        {{-- Groom Section (Read-Only) --}}
        @include('livewire.documentrequest.components.document-request-steps.document-request-forms.mirrage-form-groom')
        {{-- Bride Section (Read-Only) --}}
        @include('livewire.documentrequest.components.document-request-steps.document-request-forms.mirrage-form-bride')
    </div>
    {{-- Consent Section (Read-Only, if applicable) --}}
    @include('livewire.documentrequest.components.document-request-steps.document-request-forms.mirrage-form-consent')
    {{-- Requirements Checklist (Read-Only) --}}
    {{--
    @include('livewire.documentrequest.components.document-request-steps.document-request-forms.readonly.mirrage-form-requirements',
    ['documentRequest' => $documentRequest]) --}}
</div>