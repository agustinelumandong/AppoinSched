<div class="px-5 py-2 mt-5">
    <div class="flex flex-col gap-4">


        {{-- Personal Information Section --}}
        @if($this->service->slug !== 'marriage-certificate' && $this->service->slug !== 'death-certificate')
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

        <footer class="my-6 flex justify-end gap-2">
            <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
            <button class="btn btn-primary" wire:click="nextStep" wire:loading.disable>Next</button>
        </footer>
    </div>
</div>