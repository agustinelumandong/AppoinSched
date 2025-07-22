<div class="px-5 py-2 mt-5">
    <div class="header mb-4">
        <h3 class="text-xl font-semibold text-base-content">Confirmation</h3>
        <div class="alert alert-info flex items-center gap-2 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Please review your document request details before submitting.</span>
        </div>
    </div>

    <div class="alert alert-info flex items-center gap-2 mb-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>Present your actual valid government-issued ID. Photocopies or digital images are not accepted.</span>
    </div>

    <!-- Loading State -->
    <div wire:loading.delay.class="flex" wire:loading.remove.class="hidden"
        class="hidden justify-center items-center w-full h-64">
        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-500"></div>
    </div>

    <div class="space-y-6" wire:loading.remove>
        <!-- Request Summary -->
        <div class="flux-card p-6">
            <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Request Summary</h3>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Office</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $office->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Service</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $service->title }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Purpose</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                        {{ $purpose === 'others' ? $purpose_others : $purpose }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Price</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                        ₱{{ number_format($service->price, 2) }}</p>
                </div>
            </div>
        </div>

        @if ($service->slug === 'marriage-certificate')
            @include('livewire.documentrequest.components.document-request-steps.document-request-forms.mirrage-form')
        @elseif ($service->slug === 'death-certificate')
            @include('livewire.documentrequest.components.document-request-steps.document-request-forms.death-information')
        @else
            @include('livewire.documentrequest.components.document-request-steps.document-request-forms.personal-information')
            @include('livewire.documentrequest.components.document-request-steps.document-request-forms.address-information')
            @if (($to_whom === 'someone_else' || $this->requiresFamilyInfo()) && $service->slug !== 'death-certificate')
                @include('livewire.documentrequest.components.document-request-steps.document-request-forms.family-information')
            @endif
        @endif

        <!-- Contact Person Information -->
        <div class="flux-card p-6">
            <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Contact Person Information
            </h3>
            <div class="space-y-4">
                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $contact_last_name }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $contact_first_name }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $contact_middle_name ?? 'N/A' }}
                        </p>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $contact_email }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Phone</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $contact_phone ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="my-6 flex justify-end gap-2">
        <button class="btn btn-ghost" wire:click="previousStep" wire:loading.attr="disabled">Previous</button>
        <button class="btn btn-primary" wire:click="submitDocumentRequest" wire:loading.attr="disabled">
            <span wire:loading wire:target="submitDocumentRequest" class="loading loading-spinner"></span>
            Submit
        </button>
    </footer>
</div>