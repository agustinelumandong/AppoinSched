<div class="px-5 py-2 mt-5">
    <div class="flex flex-col gap-4">


        {{-- Personal Information Section --}}
        @if (
            $this->service->slug !== 'marriage-certificate' &&
                $this->service->slug !== 'death-certificate' &&
                $this->service->slug !== 'special-permit')
            @include('livewire.documentrequest.components.document-request-steps.document-request-forms.personal-information')

            {{-- Address Information Section --}}
            @include('livewire.documentrequest.components.document-request-steps.document-request-forms.address-information')

            {{-- Family Information Section (only if not Death Certificate) --}}
            @if (($this->to_whom === 'someone_else' || $this->requiresFamilyInfo()) && $this->service->slug !== 'death-certificate')
                @include('livewire.documentrequest.components.document-request-steps.document-request-forms.family-information')
            @endif
        @endif
        {{-- Death Information Section (only if Death Certificate) --}}
        @if ($this->service->slug === 'death-certificate')
            @include('livewire.documentrequest.components.document-request-steps.document-request-forms.death-information')
        @endif

        {{-- Marriage Certificate Section (only if Marriage Certificate) --}}
        @if ($this->service->slug === 'marriage-certificate')
            @include('livewire.documentrequest.components.document-request-steps.document-request-forms.mirrage-form')
        @endif

        {{-- Special Permit Section (only if Special Permit) --}}
        @if ($this->service->slug === 'special-permit')
            @include('livewire.documentrequest.components.document-request-steps.document-request-forms.special-permit')
        @endif


        <footer class="my-6 flex justify-end gap-2">
            <button class="flux-btn btn-ghost" wire:click="previousStep">Previous</button>
            <button class="flux-btn flux-btn-primary" x-data x-on:click="$dispatch('open-modal-info-confirmation-modal')"
                title="Information Confirmation" wire:loading.disabled>
                Next
            </button>
        </footer>
    </div>

    <x-modal id="info-confirmation-modal" title="Information Confirmation" size="max-w-2xl">
        <div class="modal-body">
            <div class="text-sm space-y-4 p-2">
                <h3>Have you double-checked all your information to ensure it's accurate?</h3>
                <p>Please review all the details you have provided in the previous steps. If everything is correct,
                    click "Confirm" to proceed with your document request. If you need to make any changes, click
                    "Cancel" to return to the form.</p>
                <p class="font-bold">Note: Once you confirm, you will not be able to edit your information.</p>
            </div>
        </div>


        <x-slot name="footer">
            <div class="flex gap-2">
                <button type="button" class="flux-btn flux-btn-outline" x-data
                    x-on:click="$dispatch('close-modal-info-confirmation-modal')">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" class="flux-btn flux-btn-success" wire:click="nextStep" x-data
                    x-on:click="$dispatch('close-modal-info-confirmation-modal')">
                    <span wire:loading.remove>
                        <i class="bi bi-check-circle me-1"></i>
                        Confirm
                    </span>
                    <span wire:loading>
                        <span class="loading loading-spinner me-1"></span>
                        Processing...
                    </span>
                </button>
            </div>
        </x-slot>
    </x-modal>
</div>
