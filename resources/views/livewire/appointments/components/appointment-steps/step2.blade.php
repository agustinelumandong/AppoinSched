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
                    <span>Please select the purpose of your appointment</span>
                </div>
            </div>

            <!-- Loading State -->
            <div wire:loading.delay class="text-center ">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                <p class="text-gray-600">Loading...</p>
            </div>

            <div class="flex flex-col gap-2 w-full">
                @if($office->slug === 'business-permits-and-licensing-section')
                    <input type="radio" id="special-permit" name="purpose" value="special-permit" wire:model.live="purpose"
                        hidden />
                    <label for="special-permit"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'special-permit' ? 'flux-btn-active-primary' : '' }} p-2">Special
                        Permit
                    </label>
                @elseif($office->slug === 'municipal-treasurers-office')
                    <input type="radio" id="payment" name="purpose" value="payment"
                        wire:model.live="purpose" hidden />
                    <label for="payment"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'payment' ? 'flux-btn-active-primary' : '' }} p-2">Payment
                    </label>
                @else
                    <input type="radio" id="get-copy-of-civil-registry-documents-and-certifications" name="purpose" value="get-copy-of-civil-registry-documents-and-certifications" wire:model.live="purpose"
                        hidden />
                    <label for="get-copy-of-civil-registry-documents-and-certifications"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'get-copy-of-civil-registry-documents-and-certifications' ? 'flux-btn-active-primary' : '' }} p-2">Get copy/ies of civil registry document/s and certification/s
                    </label>

                    <input type="radio" id="for-delayed-registration" name="purpose" value="for-delayed-registration" wire:model.live="purpose"
                        hidden />
                    <label for="for-delayed-registration"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'for-delayed-registration' ? 'flux-btn-active-primary' : '' }} p-2">For delayed Registration
                    </label>

                    <input type="radio" id="registration" name="purpose" value="registration" wire:model.live="purpose"
                        hidden />
                    <label for="registration"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'registration' ? 'flux-btn-active-primary' : '' }} p-2">Registration
                    </label>

                    <input type="radio" id="others" name="purpose" value="others" wire:model.live="purpose"
                        hidden />
                    <label for="others"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'others' ? 'flux-btn-active-primary' : '' }} p-2">Others
                    </label>

                    @if($purpose === 'others')
                        <div class="mt-2">
                            <input type="text"
                                wire:model="otherPurpose"
                                placeholder="Please specify your purpose..."
                                class="flux-input-primary w-full p-2" />
                        </div>
                    @endif
                @endif

                @if(!$office->slug === 'municipal-treasurers-office')
                    <input type="radio" id="follow_up" name="purpose" value="follow_up" wire:model.live="purpose" hidden />
                    <label for="follow_up"
                        class="flux-input-primary  flux-btn cursor-pointer {{ $purpose === 'follow_up' ? 'flux-btn-active-primary' : '' }} p-2">Follow
                        Up
                    </label>
                @endif
            </div>



            <footer class="my-6 flex justify-between" wire:loading.remove>
                <button class="flux-btn flux-btn-secondary" wire:click="previousStep">Previous</button>
                <button class="flux-btn flux-btn-primary" wire:click="nextStep">Next</button>
            </footer>
        </div>
    </div>
</div>
