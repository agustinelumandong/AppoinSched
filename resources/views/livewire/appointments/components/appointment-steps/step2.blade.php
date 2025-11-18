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
                    <input type="radio" id="request-follow-up" name="purpose" value="request-follow-up" wire:model.live="purpose"
                        hidden />
                    <label for="request-follow-up"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'request-follow-up' ? 'flux-btn-active-primary' : '' }} p-2">Request follow up
                    </label>

                    <input type="radio" id="school-requirement" name="purpose" value="school-requirement" wire:model.live="purpose"
                        hidden />
                    <label for="school-requirement"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'school-requirement' ? 'flux-btn-active-primary' : '' }} p-2">School Requirement
                    </label>

                    <input type="radio" id="employment" name="purpose" value="employment" wire:model.live="purpose"
                        hidden />
                    <label for="employment"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'employment' ? 'flux-btn-active-primary' : '' }} p-2">Employment
                    </label>

                    <input type="radio" id="live-birth-registration" name="purpose" value="live-birth-registration" wire:model.live="purpose"
                        hidden />
                    <label for="live-birth-registration"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'live-birth-registration' ? 'flux-btn-active-primary' : '' }} p-2">Live Birth Registration
                    </label>

                    <input type="radio" id="delayed-registration-of-birth" name="purpose" value="delayed-registration-of-birth" wire:model.live="purpose"
                        hidden />
                    <label for="delayed-registration-of-birth"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'delayed-registration-of-birth' ? 'flux-btn-active-primary' : '' }} p-2">Delayed Registration of Birth
                    </label>

                    <input type="radio" id="marriage-certificate-registration" name="purpose" value="marriage-certificate-registration" wire:model.live="purpose"
                        hidden />
                    <label for="marriage-certificate-registration"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'marriage-certificate-registration' ? 'flux-btn-active-primary' : '' }} p-2">Marriage Certificate Registration
                    </label>

                    <input type="radio" id="delayed-registration-of-marriage" name="purpose" value="delayed-registration-of-marriage" wire:model.live="purpose"
                        hidden />
                    <label for="delayed-registration-of-marriage"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'delayed-registration-of-marriage' ? 'flux-btn-active-primary' : '' }} p-2">Delayed Registration of Marriage
                    </label>

                    <input type="radio" id="death-certificate-registration" name="purpose" value="death-certificate-registration" wire:model.live="purpose"
                        hidden />
                    <label for="death-certificate-registration"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'death-certificate-registration' ? 'flux-btn-active-primary' : '' }} p-2">Death Certificate Registration
                    </label>

                    <input type="radio" id="delayed-registration-of-death" name="purpose" value="delayed-registration-of-death" wire:model.live="purpose"
                        hidden />
                    <label for="delayed-registration-of-death"
                        class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'delayed-registration-of-death' ? 'flux-btn-active-primary' : '' }} p-2">Delayed Registration of Death
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
