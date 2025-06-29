<div class="px-5 py-2 mt-5">
    <div class="flex flex-col gap-4">
        <div>
            <div class="header mb-4">
                <h3 class="text-xl font-semibold text-base-content">Contact Information</h3>
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

            <div class="flex flex-col gap-2 w-1/2" wire:loading.remove>
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
                                <label for="contact_last_name" class="block text-xs font-medium mb-1">Last Name</label>
                                <input class="flux-form-control md:col-span-3 w-full" type="text"
                                    wire:model="contact_last_name" placeholder="Last Name" name="contact_last_name"
                                    id="contact_last_name">
                                @error('contact_last_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="w-full md:w-1/3">
                                <label for="contact_first_name" class="block text-xs font-medium mb-1">First
                                    Name</label>
                                <input class="flux-form-control md:col-span-3 w-full" type="text"
                                    wire:model="contact_first_name" placeholder="First Name" name="contact_first_name"
                                    id="contact_first_name">
                                @error('contact_first_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="w-full md:w-1/3">
                                <label for="contact_middle_name" class="block text-xs font-medium mb-1">Middle
                                    Name</label>
                                <input class="flux-form-control md:col-span-3 w-full" type="text"
                                    wire:model="contact_middle_name" placeholder="Middle Name" name="contact_middle_name"
                                    id="contact_middle_name">
                                @error('contact_middle_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class=" grid grid-cols-1 gap-2">
                            <div>
                                <label class="font-medium text-sm">Contact Email:</label>
                                <input class="flux-form-control " wire:model="contact_email" placeholder="Contact Email"
                                    name="contact_email" id="contact_email">
                                @error('contact_email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="font-medium text-sm">Contact Phone:</label>
                                <input class="flux-form-control" wire:model="contact_phone" placeholder="Contact Phone"
                                    name="contact_phone" id="contact_phone">
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
                                <label for="contact_last_name" class="block text-xs font-medium mb-1">Last Name</label>
                                <input class="flux-form-control md:col-span-3 w-full" type="text"
                                    wire:model="contact_last_name" placeholder="Last Name" name="contact_last_name"
                                    id="contact_last_name">
                                @error('contact_last_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="w-full md:w-1/3">
                                <label for="contact_first_name" class="block text-xs font-medium mb-1">First
                                    Name</label>
                                <input class="flux-form-control md:col-span-3 w-full" type="text"
                                    wire:model="contact_first_name" placeholder="First Name" name="contact_first_name"
                                    id="contact_first_name">
                                @error('contact_first_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="w-full md:w-1/3">
                                <label for="contact_middle_name" class="block text-xs font-medium mb-1">Middle
                                    Name</label>
                                <input class="flux-form-control md:col-span-3 w-full" type="text"
                                    wire:model="contact_middle_name" placeholder="Middle Name" name="contact_middle_name"
                                    id="contact_middle_name">
                                @error('contact_middle_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class=" grid grid-cols-1 gap-2">
                            <div>
                                <label class="font-medium text-sm">Contact Email:</label>
                                <input class="flux-form-control " wire:model="contact_email" placeholder="Contact Email"
                                    name="contact_email" id="contact_email">
                                @error('contact_email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="font-medium text-sm">Contact Phone:</label>
                                <input class="flux-form-control" wire:model="contact_phone" placeholder="Contact Phone"
                                    name="contact_phone" id="contact_phone">
                                @error('contact_phone')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif
                <p class="text-sm text-base-content/70 mt-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    We will use this information to contact you about your document request.
                </p>
            </div>

            <footer class="my-6 flex justify-end gap-2">
                <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
                <button class="btn btn-primary" wire:click="nextStep">Next</button>
            </footer>
        </div>
    </div>
</div>