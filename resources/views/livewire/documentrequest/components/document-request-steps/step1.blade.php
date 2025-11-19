<div class="px-5 py-2 mt-5" wire:key="document-request-step-1">
    <div class="flex flex-col gap-4">
        <div>
            <div class="header mb-4">
                <h3 class="text-xl font-semibold text-base-content">For Yourself or Someone Else?</h3>
                <div class="flex items-center gap-2 text-sm text-base-content/70">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Please select who this document request is for</span>
                </div>
            </div>


            <!-- Loading State -->
            <div wire:loading.delay class="text-center ">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                <p class="text-gray-600">Loading...</p>
            </div>

            @error('relationship')
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

            <div class="flex flex-col gap-2 w-full" wire:loading.remove>
                @if ($this->service->title === 'Death Certificate')
                    <?php
                    $to_whom = 'someone_else';
                    ?>
                    <input type="radio" id="myself" name="to_whom" value="myself" wire:model.live="to_whom" hidden
                        disabled />
                    <label for="myself"
                        class="flux-input-primary flux-btn cursor-pointer opacity-50 cursor-not-allowed p-2">Myself
                        (Not available for Death Certificate)</label>
                    <input type="radio" id="someone_else" name="to_whom" value="someone_else"
                        wire:model.live="to_whom" hidden />
                    <label for="someone_else"
                        class="flux-input-primary flux-btn cursor-pointer {{ $to_whom === 'someone_else' ? 'flux-btn-active-primary' : '' }} p-2">Someone
                        Else</label>
                @else
                    <input type="radio" id="myself" name="to_whom" value="myself" wire:model.live="to_whom"
                        hidden />
                    <label for="myself"
                        class="flux-input-primary flux-btn cursor-pointer {{ $to_whom === 'myself' ? 'flux-btn-active-primary' : '' }} p-2">Myself</label>
                    <input type="radio" id="someone_else" name="to_whom" value="someone_else"
                        wire:model.live="to_whom" hidden />
                    <label for="someone_else"
                        class="flux-input-primary flux-btn cursor-pointer {{ $to_whom === 'someone_else' ? 'flux-btn-active-primary' : '' }} p-2">Someone
                        Else</label>
                @endif
            </div>

            @if ($to_whom === 'someone_else')
                <div class="mt-4" wire:loading.remove>
                    <div class="header mb-4">
                        <h3 class="text-lg font-semibold text-base-content">Select Relationship</h3>
                        <div class="flex items-center gap-2 text-sm text-base-content/70">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Please select your relationship to the person</span>
                        </div>
                    </div>

                    <?php $relationship = $relationship ?? ''; ?>
                    <div class="flex flex-col gap-2 w-full">
                        <input type="radio" id="my_father" name="relationship" value="my_father"
                            wire:model.live="relationship" hidden />
                        <label for="my_father"
                            class="flux-input-primary flux-btn cursor-pointer {{ $relationship === 'my_father' ? 'flux-btn-active-primary' : '' }} p-2">My
                            Father</label>

                        <input type="radio" id="my_mother" name="relationship" value="my_mother"
                            wire:model.live="relationship" hidden />
                        <label for="my_mother"
                            class="flux-input-primary flux-btn cursor-pointer {{ $relationship === 'my_mother' ? 'flux-btn-active-primary' : '' }} p-2">My
                            Mother</label>

                        <input type="radio" id="my_son" name="relationship" value="my_son"
                            wire:model.live="relationship" hidden />
                        <label for="my_son"
                            class="flux-input-primary flux-btn cursor-pointer {{ $relationship === 'my_son' ? 'flux-btn-active-primary' : '' }} p-2">My
                            Son</label>

                        <input type="radio" id="my_daughter" name="relationship" value="my_daughter"
                            wire:model.live="relationship" hidden />
                        <label for="my_daughter"
                            class="flux-input-primary flux-btn cursor-pointer {{ $relationship === 'my_daughter' ? 'flux-btn-active-primary' : '' }} p-2">My
                            Daughter</label>

                        @if($this->service->title !== 'Death Certificate')
                            <input type="radio" id="others_relationship" name="relationship" value="others"
                                wire:model.live="relationship" hidden />
                            <label for="others_relationship"
                                class="flux-input-primary flux-btn cursor-pointer {{ $relationship === 'others' ? 'flux-btn-active-primary' : '' }} p-2">Others</label>
                        @else
                            <input type="radio" id="my_wife" name="relationship" value="my_wife"
                                wire:model.live="relationship" hidden />
                            <label for="my_wife"
                                class="flux-input-primary flux-btn cursor-pointer {{ $relationship === 'my_wife' ? 'flux-btn-active-primary' : '' }} p-2">My Wife</label>
                        @endif
                    </div>

                    @if ($relationship === 'others')
                        <div class="mt-4">
                            <input type="text" placeholder="Please specify relationship" class="flux-form-control"
                                wire:model="relationship_others" />
                            @error('relationship_others')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </div>
            @endif

            <footer class="my-6 flex justify-end gap-2">
                <button class="btn btn-ghost" type="button" wire:click="previousStep">Previous</button>
                <button class="btn btn-primary" type="button" wire:click="nextStep">Next</button>
            </footer>
        </div>
    </div>
</div>
