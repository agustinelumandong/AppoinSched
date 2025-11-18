<div class="px-5 py-2 mt-5">
    <div class="flex flex-col gap-4">
        <div>
            <div class="header mb-4">
                <h3 class="text-xl font-semibold text-base-content">Requester's Details Information</h3>
                <div class="flex items-center gap-2 text-sm text-base-content/70">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Please confirm your contact information</span>
                </div>
            </div>

            <!-- Loading State -->
            <div wire:loading.delay class="text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                <p class="text-gray-600">Loading...</p>
            </div>

            <div class="flex flex-col gap-2 w-full">
                <p class="text-sm text-base-content/70 mb-2">Please review and confirm your contact details:
                </p>

                @if ($to_whom === 'someone_else')
                    <div class="alert alert-info flex items-center gap-2 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>This contact info is for reaching you about this request - it can be different from the
                            document
                            beneficiary's info.</span>
                    </div>
                @endif

                @if ($this->service->title !== 'Death Certificate')
                    <div class="bg-base-200 p-4 rounded-lg">
                        <div class="flex flex-row md:flex-col gap-4 mb-4">
                            <div class="w-full md:w-1/3">
                                <label for="contact_last_name" class="block text-xs font-medium mb-1">Requester's Last
                                    Name</label>
                                <input class="flux-form-control md:col-span-3 w-full" type="text"
                                    wire:model="contact_last_name" placeholder="Requester's Last Name"
                                    name="contact_last_name" id="contact_last_name" required
                                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                @error('contact_last_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="w-full md:w-1/3">
                                <label for="contact_first_name" class="block text-xs font-medium mb-1">Requester's First
                                    Name</label>
                                <input class="flux-form-control md:col-span-3 w-full" type="text"
                                    wire:model="contact_first_name" placeholder="Requester's First Name"
                                    name="contact_first_name" id="contact_first_name" required
                                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                @error('contact_first_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="w-full md:w-1/3">
                                <label for="contact_middle_name" class="block text-xs font-medium mb-1">Requester's
                                    Middle
                                    Name</label>
                                <input class="flux-form-control md:col-span-3 w-full" type="text"
                                    wire:model="contact_middle_name" placeholder="Requester's Middle Name"
                                    name="contact_middle_name" id="contact_middle_name"
                                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                @error('contact_middle_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class=" grid grid-cols-1 gap-2">
                            <div>
                                <label class="font-medium text-sm">Email Address:</label>
                                <input class="flux-form-control " wire:model="contact_email" placeholder="Email Address"
                                    name="contact_email" id="contact_email" required>
                                @error('contact_email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="font-medium text-sm">Phone Number:</label>
                                <input class="flux-form-control" wire:model="contact_phone" type="tel"
                                    inputmode="numeric" pattern="[0-9]*"
                                    x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')"
                                    placeholder="Phone Number" name="contact_phone" id="contact_phone" required>
                                @error('contact_phone')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-base-200 p-4 rounded-lg">
                        <div class="flex flex-row md:flex-col gap-4 mb-4">
                            <div class="w-full md:w-1/3">
                                <label for="contact_last_name" class="block text-xs font-medium mb-1">Requester's Last
                                    Name</label>
                                <input class="flux-form-control md:col-span-3 w-full" type="text"
                                    wire:model.live="contact_last_name" placeholder="Requester's Last Name"
                                    name="contact_last_name" id="contact_last_name" required
                                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                @error('contact_last_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="w-full md:w-1/3">
                                <label for="contact_first_name" class="block text-xs font-medium mb-1">Requester's First
                                    Name</label>
                                <input class="flux-form-control md:col-span-3 w-full" type="text"
                                    wire:model.live="contact_first_name" placeholder="Requester's First Name"
                                    name="contact_first_name" id="contact_first_name" required
                                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                @error('contact_first_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="w-full md:w-1/3">
                                <label for="contact_middle_name" class="block text-xs font-medium mb-1">Requester's
                                    Middle
                                    Name</label>
                                <input class="flux-form-control md:col-span-3 w-full" type="text"
                                    wire:model.live="contact_middle_name" placeholder="Requester's Middle Name"
                                    name="contact_middle_name" id="contact_middle_name"
                                    x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })">
                                @error('contact_middle_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class=" grid grid-cols-1 gap-2">
                            <div>
                                <label class="font-medium text-sm">Email Address:</label>
                                <input class="flux-form-control " wire:model.live="contact_email"
                                    placeholder="Email Address" name="contact_email" id="contact_email" required>
                                @error('contact_email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="font-medium text-sm">Phone Number:</label>
                                <input class="flux-form-control" wire:model.live="contact_phone" type="tel"
                                    inputmode="numeric" pattern="[0-9]*"
                                    x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')"
                                    placeholder="Phone Number" name="contact_phone" id="contact_phone" required>
                                @error('contact_phone')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif
                <p class="text-sm text-base-content/70 mt-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    We will use this information to contact you about your document request.
                </p>
            </div>

            <footer class="my-6 flex justify-end gap-2">
                <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
                <button class="flux-btn btn-sm flux-btn-primary" type="button"
                    wire:click="validateContactInfo" x-on:click="$dispatch('open-modal-consent-confirmation')" title="Validate" >
                    <i class="bi bi-check"></i>Next
                </button>
                {{-- <button class="btn btn-primary" wire:click="nextStep">Next</button> --}}
            </footer>
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
            <div class="gap-2">
                <button type="button" class="flux-btn flux-btn-outline" x-data
                    x-on:click="$dispatch('close-modal-consent-confirmation')">
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
