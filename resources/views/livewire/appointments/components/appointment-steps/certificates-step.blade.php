@php
    // Dynamically set certificate types based on office slug
    $officeSlug = $this->office->slug ?? null;
    if ($officeSlug === 'municipal-civil-registrar') {
        $certificateTypes = [
            'birth_certificate' => 'Birth Certificate',
            'death_certificate' => 'Death Certificate',
            'marriage_certificate' => 'Marriage Certificate',
        ];
    } elseif ($officeSlug === 'business-permits-and-licensing-section') {
        $certificateTypes = [
            'special-permit' => 'Special Permit',
        ];
    } else {
        $certificateTypes = [
            'birth_certificate' => 'Birth Certificate',
            'death_certificate' => 'Death Certificate',
            'marriage_certificate' => 'Marriage Certificate',
            'special-permit' => 'Special Permit',
        ];
    }

    $relationshipTypes = [
        'myself' => 'Myself',
        'spouse' => 'Spouse',
        'child' => 'Child',
        'parent' => 'Parent',
        'sibling' => 'Sibling',
        'other' => 'Other',
    ];
@endphp

<div class="px-5 py-2 mt-5">
    <div class="flex flex-col gap-4">
        <div>
            <div class="header mb-4">
                <h3 class="text-xl font-semibold text-base-content">{{ label_with_bisaya('Certificate Requests', 'certificate_requests') }}</h3>
                <div class="flex items-center gap-2 text-sm text-base-content/70">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Select the certificates you need for this appointment</span>
                </div>
            </div>

            <!-- Loading State -->
            <div wire:loading.delay class="text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                <p class="text-gray-600">Loading...</p>
            </div>

            <div wire:loading.remove>
                <div class="space-y-4">
                    @foreach ($certificates as $index => $certificate)
                        <div class="card bg-base-100 border border-gray-200">
                            <div class="card-body p-4">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="font-semibold">Certificate #{{ $index + 1 }}</h3>
                                    @if (count($certificates) > 1)
                                        <button type="button" wire:click="removeCertificate({{ $index }})"
                                            class="btn btn-error btn-sm btn-outline">
                                            Remove
                                        </button>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="label">
                                            <span class="label-text">{{ label_with_bisaya('Certificate Type', 'certificate_type') }} <span
                                                    class="text-red-500">*</span></span>
                                        </label>
                                        <select wire:model="certificates.{{ $index }}.certificate_type"
                                            class="flux-form-control cursor-pointer" required>
                                            <option value="">-- Select Certificate Type --</option>
                                            @foreach ($certificateTypes as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error("certificates.{$index}.certificate_type")
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="label">
                                            <span class="label-text">{{ label_with_bisaya('Relationship', 'relationship') }} <span
                                                    class="text-red-500">*</span></span>
                                        </label>
                                        <select wire:model="certificates.{{ $index }}.relationship"
                                            class="flux-form-control cursor-pointer" required>
                                            <option value="">-- Select Relationship --</option>
                                            @foreach ($relationshipTypes as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error("certificates.{$index}.relationship")
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="label">
                                            <span class="label-text">{{ label_with_bisaya('First Name', 'first_name') }} <span
                                                    class="text-red-500">*</span></span>
                                        </label>
                                        <input type="text" wire:model="certificates.{{ $index }}.first_name"
                                            class="flux-form-control" required x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })" >
                                        @error("certificates.{$index}.first_name")
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="label">
                                            <span class="label-text">{{ label_with_bisaya('Last Name', 'last_name') }} <span
                                                    class="text-red-500">*</span></span>
                                        </label>
                                        <input type="text" wire:model="certificates.{{ $index }}.last_name"
                                            class="flux-form-control" required x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })" >
                                        @error("certificates.{$index}.last_name")
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="label">
                                            <span class="label-text">{{ label_with_bisaya('Middle Name', 'middle_name') }}</span>
                                        </label>
                                        <input type="text" wire:model="certificates.{{ $index }}.middle_name"
                                            class="flux-form-control" x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })" >
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-4">
                    <button type="button" wire:click="addCertificate" class="btn btn-outline btn-primary">
                        Add Another Certificate
                    </button>
                </div>

                <footer class="my-6 flex justify-between" wire:loading.remove>
                    <button class="flux-btn flux-btn-secondary" wire:click="previousStep">Previous</button>
                    <button class="flux-btn flux-btn-primary" x-data x-on:click="$dispatch('open-modal-info-confirmation-modal')" title="Confirmation" wire:loading.disabled>
                Next
            </button>
                </footer>
            </div>
        </div>
    </div>

    <x-modal id="info-confirmation-modal" title="Information Confirmation" size="max-w-2xl">
        <div class="modal-body">
            <div class="text-sm space-y-4 p-2">
                <h3>Have you double-checked all your information to ensure it's accurate?</h3>
                <p>Please review all the details you have provided in the previous steps. If everything is correct, click "Confirm" to proceed with your document request. If you need to make any changes, click "Cancel" to return to the form.</p>
                <p class="font-bold">Note: Once you confirm, you will not be able to edit your information.</p>
            </div>
        </div>


        <x-slot name="footer">
            <div class="flex gap-2">
                <button type="button" class="flux-btn flux-btn-outline" x-data
                    x-on:click="$dispatch('close-modal-info-confirmation-modal')">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" class="flux-btn flux-btn-success"
                    wire:click="nextStep" x-data
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
