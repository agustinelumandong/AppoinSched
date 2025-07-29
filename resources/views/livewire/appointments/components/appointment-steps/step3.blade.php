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
                            wire:model="last_name" @if ($editPersonDetails === false) disabled @endif />
                        @error('last_name')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                        <span class="text-xs text-gray-500 mt-1">Required</span>
                    </div>

                    <div class="w-full">
                        <label for="first_name" class="text-xs font-medium mb-1">First Name</label>
                        <input type="text" placeholder="First Name"
                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                            wire:model="first_name" @if ($editPersonDetails === false) disabled @endif />
                        @error('first_name')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                        <span class="text-xs text-gray-500 mt-1">Required</span>
                    </div>

                    <div class="w-full">
                        <label for="middle_name" class="text-xs font-medium mb-1">Middle Name</label>
                        <input type="text" placeholder="Middle Name"
                            class="flux-form-control @if ($editPersonDetails === false) bg-gray-100 @endif"
                            wire:model="middle_name" @if ($editPersonDetails === false) disabled @endif />
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
                        <input type="tel" placeholder="Phone"
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
                    <button class="flux-btn flux-btn-primary" wire:click="nextStep">Next</button>
                </footer>
            </div>
        </div>
    </div>
</div>