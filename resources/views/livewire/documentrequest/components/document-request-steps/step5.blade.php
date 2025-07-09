<div class="px-5 py-2 mt-5">
    <div class="header mb-4">
        <h3 class="text-xl font-semibold text-base-content">Confirmation</h3>
        <div class="alert alert-info flex items-center gap-2 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Please review your document request details before submitting.</span>
        </div>
    </div>

    <div class="alert alert-info flex items-center gap-2 mb-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 w-5 h-5" fill="none"
            viewBox="0 0 24 24">
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
                        {{ $purpose === 'others' ? $purpose_others : $purpose }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Price</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                        ₱{{ number_format($service->price, 2) }}</p>
                </div>
            </div>
        </div>

        @if ($service->title !== 'Death Certificate')
            <!-- Beneficiary Information -->
            <div class="flux-card p-6">
                <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Beneficiary Information
                </h3>
                <div class="space-y-4">
                    <div class="grid md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $last_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $first_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $middle_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Suffix</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $suffix ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Date of Birth</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ \Carbon\Carbon::parse($date_of_birth)->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Place of Birth</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $place_of_birth }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Sex at Birth</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $sex_at_birth }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Civil Status</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $civil_status }}</p>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Religion</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $religion }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Nationality</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $nationality }}</p>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Government ID Type</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $government_id_type }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Government ID Image</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $government_id_image_path ? 'Uploaded' : 'Not uploaded' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="flux-card p-6">
                <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Address Information
                </h3>
                <div class="space-y-4">
                    <div class="grid md:grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Address</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $address_line_1 }} {{ $address_line_2 }} {{ $street }},
                                {{ $barangay }}, {{ $city }}, {{ $province }}, {{ $region }}
                                {{ $zip_code }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Family Information -->
            <div class="flux-card p-6">
                <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Family Information</h3>
                <!-- Father's Information -->
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Father's Information</h4>
                    <div class="grid md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $father_last_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $father_first_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $father_middle_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Suffix</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $father_suffix ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-4 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Birthdate</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $father_birthdate ? \Carbon\Carbon::parse($father_birthdate)->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Nationality</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $father_nationality ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Religion</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $father_religion ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Contact No.</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $father_contact_no ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <!-- Mother's Information -->
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Mother's Information</h4>
                    <div class="grid md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $mother_last_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $mother_first_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $mother_middle_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Suffix</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $mother_suffix ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-4 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Birthdate</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $mother_birthdate ? \Carbon\Carbon::parse($mother_birthdate)->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Nationality</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $mother_nationality ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Religion</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $mother_religion ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Contact No.</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $mother_contact_no ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <!-- Spouse's Information -->
                @if ($civil_status === 'Married' || $civil_status === 'Widowed')
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Spouse's Information</h4>
                        <div class="grid md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $spouse_last_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $spouse_first_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $spouse_middle_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Suffix</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $spouse_suffix ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="grid md:grid-cols-4 gap-6 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Birthdate</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $spouse_birthdate ? \Carbon\Carbon::parse($spouse_birthdate)->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Nationality</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $spouse_nationality ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Religion</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $spouse_religion ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Contact No.</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $spouse_contact_no ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <!-- Deceased Person Information -->
            <div class="flux-card p-6">
                <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Deceased Person
                    Information</h3>
                <div class="space-y-4">
                    <div class="grid md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $deceased_last_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $deceased_first_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $deceased_middle_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Date of Death</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ \Carbon\Carbon::parse($death_date)->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Time of Death</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $death_time ? \Carbon\Carbon::parse($death_time)->format('h:i A') : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Place of Death</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $death_place }}</p>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Relationship to
                                Deceased</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $relationship_to_deceased }}</p>
                        </div>
                    </div>
                </div>
            </div>
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
                            {{ $contact_last_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $contact_first_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $contact_middle_name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $contact_email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Phone</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $contact_phone ?? 'N/A' }}</p>
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
