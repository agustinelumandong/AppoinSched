<div class="px-5 py-2 mt-5">
    <div class="flex flex-col gap-4">
        <div>
            <div class="header mb-4 flex flex-row justify-between items-center">
                <div>

                    <h3 class="text-xl font-semibold text-base-content">Your details</h3>
                    <div class="flex items-center gap-2 text-sm text-base-content/70">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Please provide your details</span>
                    </div>
                </div>
                <div class="flex flex-row gap-2 mb-4">
                    @if ($editPersonDetails === false)
                        <button type="button" class="flux-btn flux-btn-primary"
                            wire:click="editPersonDetailsBtn">Edit</button>
                    @else
                        <button type="button" class="flux-btn flux-btn-primary"
                            wire:click="lockPersonDetailsBtn">Lock</button>
                    @endif
                </div>
            </div>

            <!-- Info Redundancy Error -->
            @error('info_redundancy')
                <div class="alert alert-error mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $message }}</span>
                </div>
            @enderror

            <!-- Loading State -->
            <div wire:loading.delay class="text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2">
                </div>
                <p class="text-gray-600">Loading...</p>
            </div>

            <div class="flex flex-col gap-2 w-full" wire:loading.remove>
                <div class="flex flex-row gap-2 w-full">
                    <div class="w-full">
                        <label for="last_name" class="text-xs font-medium mb-1">Last Name</label>
                        <input type="text" placeholder="Last Name"
                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                            wire:model="last_name" @if ($editPersonDetails === false) disabled @endif x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })" />
                        @error('last_name')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                        <span class="text-xs text-gray-500 mt-1">Required</span>
                    </div>

                    <div class="w-full">
                        <label for="first_name" class="text-xs font-medium mb-1">First Name</label>
                        <input type="text" placeholder="First Name"
                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                            wire:model="first_name" @if ($editPersonDetails === false) disabled @endif x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })" />
                        @error('first_name')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                        <span class="text-xs text-gray-500 mt-1">Required</span>
                    </div>

                    <div class="w-full">
                        <label for="middle_name" class="text-xs font-medium mb-1">Middle Name</label>
                        <input type="text" placeholder="Middle Name"
                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                            wire:model="middle_name" @if ($editPersonDetails === false) disabled @endif x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })" />
                        @error('middle_name')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                        <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                    </div>
                </div>

                <div class="flex flex-row gap-2 w-full">
                    <div class=" w-full">
                        <label for="email" class="text-xs font-medium mb-1">Email</label>
                        <input type="email" placeholder="Email"
                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                            wire:model="email" @if ($editPersonDetails === false) disabled @endif />
                        @error('email')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                        <span class="text-xs text-gray-500 mt-1">Required</span>
                    </div>

                    <div class="w-full">
                        <label for="phone" class="text-xs font-medium mb-1">Phone</label>
                        <input type="tel" inputmode="numeric" pattern="[0-9]*"
                            x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')"
                            placeholder="Phone"
                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                            wire:model="phone" @if ($editPersonDetails === false) disabled @endif />
                        @error('phone')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                        <span class="text-xs text-gray-500 mt-1">Required</span>
                    </div>
                </div>

                <footer class="my-6 flex justify-between" wire:loading.remove>
                    <button class="flux-btn flux-btn-secondary" wire:click="previousStep">Previous</button>
                    {{-- <button class="flux-btn flux-btn-primary" x-data x-on:click="$dispatch('open-modal-appoint-info-confirmation')" title="Appointment Confirmation">
                        Next
                    </button> --}}

                    <button class="flux-btn btn-sm flux-btn-primary" type="button" x-data="{}"
                        x-on:click="$dispatch('open-modal-consent-confirmation')" title="Confirmation">
                        <i class="bi bi-check"></i>Next
                    </button>
                </footer>
            </div>
        </div>
    </div>
    <x-modal id="consent-confirmation" title="Confirmation" size="max-w-2xl">
        <div class="modal-body">
            <div class="text-sm space-y-4 max-h-[60vh] overflow-y-auto p-2">
                <h3 class="font-bold text-lg mb-2">Consent and Affirmation</h3>

                <div class="bg-base-200 p-3 rounded-lg">
                    <p>I affirm that I am the document owner or the duly authorized representative of the owner of the
                        requested PSA certificate. I attest that all information provided above is true and accurate to
                        the best of my knowledge.</p>
                    <p class="mt-2">I understand that as the authorized requester, I am required to personally appear
                        at the designated office to claim the requested document. I will present valid identification to
                        verify my identity during collection.</p>
                    <p class="mt-2">I acknowledge that my personal information will be processed in accordance with
                        the Data Privacy Act of 2012 (Republic Act No. 10173). I understand that the information
                        provided will be used solely for the purpose of processing my document request.</p>
                </div>

                <div>
                    <h4 class="font-semibold text-base mt-4 mb-2">Disclaimer</h4>
                    <p>This online system serves only as a platform to facilitate document requests and appointment
                        scheduling. It does not provide courier, mailing, or delivery services of any kind.</p>
                    <p class="mt-2">All requested documents must be personally claimed by the requester or their
                        authorized representative at the designated Municipal Office. Failure to appear or present valid
                        identification may result in delays or denial of release.</p>
                    <p class="mt-2">The processing, printing, and release of documents are conducted exclusively by
                        the authorized Municipal Offices. Our system has no involvement in or responsibility for the
                        physical handling, storage, or issuance of the requested documents.</p>
                    <p class="mt-2">For any concerns regarding the status or release of your document, please contact
                        or visit your respective Municipal Office directly.</p>
                </div>

                <div class="mt-4">
                    <h4 class="font-semibold text-base mb-2">Acceptance</h4>
                    <p>As a Requisition and Appointment System customer, I fully understand and accept the terms stated
                        above, including all requirements, processes, and applicable laws governing this application.
                    </p>
                </div>

            </div>
        </div>


        <x-slot name="footer">
            <div class="flex gap-2">
                <button type="button" class="flux-btn flux-btn-outline" x-data
                    x-on:click="$dispatch('close-modal-appoint-info-confirmation')">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" class="flux-btn btn-sm flux-btn-success" x-data="{}"
                    x-on:click="
                        $dispatch('close-modal-consent-confirmation');
                        $wire.nextStep();
                    ">
                    <span wire:loading.remove>
                        <i class="bi bi-check-circle me-1"></i>
                        Confirm
                    </span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                        Loading...
                    </span>
                </button>
            </div>
        </x-slot>
    </x-modal>
</div>
