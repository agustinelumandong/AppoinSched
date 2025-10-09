<div class="px-5 py-2 mt-5">
    <div class="flex flex-col gap-4">
        <div>
            <div class="header mb-4">
                <h3 class="text-xl font-semibold text-base-content">State your purpose</h3>
                <div class="flex items-center gap-2 text-sm text-base-content/70">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Please select the purpose of your document request</span>
                </div>
            </div>

            <!-- Loading State -->
            <div wire:loading.delay class="text-center ">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                <p class="text-gray-600">Loading...</p>
            </div>

            @error('purpose')
                <div class="alert alert-danger flex items-center gap-2 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                        </path>
                    </svg>
                    <span>{{ $message }}</span>
                </div>
            @enderror


            @if ($this->service->title !== 'Death Certificate')
                <div class="flex flex-col gap-2 w-full" wire:loading.remove>
                    <input type="radio" id="school_requirement" name="purpose" value="school_requirement"
                        wire:model.live="purpose" hidden />
                    <label for="school_requirement"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'school_requirement' ? 'flux-btn-active-primary' : '' }} p-2">School
                        requirement</label>

                    <input type="radio" id="employment" name="purpose" value="employment" wire:model.live="purpose"
                        hidden />
                    <label for="employment"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'employment' ? 'flux-btn-active-primary' : '' }} p-2">Employment</label>

                    <input type="radio" id="government_id" name="purpose" value="government_id"
                        wire:model.live="purpose" hidden />
                    <label for="government_id"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'government_id' ? 'flux-btn-active-primary' : '' }} p-2">Government
                        ID</label>

                    <input type="radio" id="hospital_use" name="purpose" value="hospital_use"
                        wire:model.live="purpose" hidden />
                    <label for="hospital_use"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'hospital_use' ? 'flux-btn-active-primary' : '' }} p-2">Hospital
                        use</label>

                    <input type="radio" id="others" name="purpose" value="others" wire:model.live="purpose"
                        hidden />
                    <label for="others"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'others' ? 'flux-btn-active-primary' : '' }} p-2">Others</label>
                </div>

                @if ($purpose === 'others')
                    <div class="mt-4">
                        <input type="text" placeholder="Please specify purpose" class="flux-form-control"
                            wire:model="purpose_others"
                            x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })" />
                        @error('purpose_others')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
            @else
                <!-- Purpose for Requesting -->
                <div class="mb-4" wire:loading.remove>
                    <h5 class="text-md font-semibold mb-3">Purpose for Requesting the Certificate</h5>
                    <div class="flex flex-col gap-2 w-full">
                        <input type="radio" id="legal_proceedings" name="purpose" value="Legal Proceedings"
                            wire:model.live="purpose" hidden />
                        <label for="legal_proceedings"
                            class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'Legal Proceedings' ? 'flux-btn-active-primary' : '' }} p-2">Legal
                            Proceedings</label>

                        <input type="radio" id="insurance_claims" name="purpose" value="Insurance Claims"
                            wire:model.live="purpose" hidden />
                        <label for="insurance_claims"
                            class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'Insurance Claims' ? 'flux-btn-active-primary' : '' }} p-2">Insurance
                            Claims</label>

                        <input type="radio" id="property_transfer" name="purpose" value="Property Transfer"
                            wire:model.live="purpose" hidden />
                        <label for="property_transfer"
                            class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'Property Transfer' ? 'flux-btn-active-primary' : '' }} p-2">Property
                            Transfer</label>

                        <input type="radio" id="social_security" name="purpose" value="Social Security Benefits"
                            wire:model.live="purpose" hidden />
                        <label for="social_security"
                            class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'Social Security Benefits' ? 'flux-btn-active-primary' : '' }} p-2">Social
                            Security Benefits</label>

                        <input type="radio" id="veterans_benefits" name="purpose" value="Veterans Benefits"
                            wire:model.live="purpose" hidden />
                        <label for="veterans_benefits"
                            class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'Veterans Benefits' ? 'flux-btn-active-primary' : '' }} p-2">Veterans
                            Benefits</label>

                        <input type="radio" id="genealogy_research" name="purpose" value="Genealogy Research"
                            wire:model.live="purpose" hidden />
                        <label for="genealogy_research"
                            class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'Genealogy Research' ? 'flux-btn-active-primary' : '' }} p-2">Genealogy
                            Research</label>

                        <input type="radio" id="personal_records" name="purpose" value="Personal Records"
                            wire:model.live="purpose" hidden />
                        <label for="personal_records"
                            class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'Personal Records' ? 'flux-btn-active-primary' : '' }} p-2">Personal
                            Records</label>

                        <input type="radio" id="other_purpose" name="purpose" value="Other"
                            wire:model.live="purpose" hidden />
                        <label for="other_purpose"
                            class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'Other' ? 'flux-btn-active-primary' : '' }} p-2">Other</label>
                    </div>

                    @if ($purpose === 'Other')
                        <div class="mt-4">
                            <input type="text" placeholder="Please specify purpose" class="flux-form-control"
                                wire:model="purpose_details"
                                x-on:input="$event.target.value = $event.target.value.toLowerCase().replace(/(^|\s)\S/g, function(letter) { return letter.toUpperCase(); })" />
                            @error('purpose_details')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif


                </div>
            @endif

            <footer class="my-6 flex justify-end gap-2">
                <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
                <button class="btn btn-primary" wire:click="nextStep">Next</button>
            </footer>
        </div>
    </div>
</div>
