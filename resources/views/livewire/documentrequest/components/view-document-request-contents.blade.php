<!-- Loading State -->
<div wire:loading.delay.class="flex" wire:loading.remove.class="hidden"
    class="hidden justify-center items-center w-full h-64">
    <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-500"></div>
</div>

<div wire:loading.remove>
    <!-- Client Info -->
    <div class="flux-card p-6 mb-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-2">
                <h2 class="text-lg font-semibold text-gray-900">
                    @if ($documentRequest->details && $documentRequest->details->request_for === 'someone_else')
                        Beneficiary Information
                    @else
                        Client Information
                    @endif
                </h2>

                <!-- NEW: Add data source indicator -->
                <div class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                    @if ($documentRequest->details)
                        Data as submitted on {{ $documentRequest->created_at->format('M d, Y') }}
                    @else
                        Current profile data (legacy request)
                    @endif
                </div>
            </div>

            <!-- NEW: Add comparison button for third-party requests -->
            @if ($documentRequest->details && $documentRequest->details->request_for === 'someone_else')
                <button type="button" class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                    onclick="toggleRequesterInfo()">
                    View Requester Info
                </button>
            @endif
        </div>
        @if ($documentRequest->service->title === 'Marriage Certificate')
            @include(
                'livewire.documentrequest.components.document-request-steps.document-request-forms.readonly.mirrage-form',
                [
                    'documentRequest' => $documentRequest,
                ]
            )
        @elseif ($documentRequest->service->title === 'Special Permit')
            <div class="mb-8">
                <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Establishment
                    Information</h3>
                <div class="space-y-4">
                    <div class="grid md:grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Establishment Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->establishment_name ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Establishment Address</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->establishment_address ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Purpose</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->establishment_purpose ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif ($documentRequest->service->title === 'Birth Certificate')
            <!-- Personal Identity Section -->
            <div class="mb-8">
                <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Personal Identity</h3>
                <div class="space-y-4">
                    <div class="grid md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->last_name ?? $documentRequest->user->last_name }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->first_name ?? $documentRequest->user->first_name }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->middle_name ?? ($documentRequest->user->middle_name ?? 'N/A') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Suffix</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->suffix ?? ($documentRequest->user->suffix ?? 'N/A') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Date of Birth</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ optional($documentRequest->details)->date_of_birth
                                    ? \Carbon\Carbon::parse($documentRequest->details->date_of_birth)->format('M d, Y')
                                    : (optional($documentRequest->user)->date_of_birth
                                        ? \Carbon\Carbon::parse($documentRequest->user->date_of_birth)->format('M d, Y')
                                        : 'N/A') }}

                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Place of Birth</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->place_of_birth ?? ($documentRequest->user->place_of_birth ?? 'N/A') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Sex at Birth</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->sex_at_birth ?? ($documentRequest->user->sex_at_birth ?? 'N/A') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Civil Status</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->civil_status ?? ($documentRequest->user->civil_status ?? 'N/A') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Nationality</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->nationality ?? ($documentRequest->user->nationality ?? 'N/A') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Religion</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->religion ?? ($documentRequest->user->religion ?? 'N/A') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Contact Number</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->contact_no ?? ($documentRequest->user->contact_no ?? 'N/A') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Government ID Type</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->government_id_type ?? ($documentRequest->user->government_id_type ?? 'N/A') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Government ID PDF</label>
                            @if ($documentRequest->details && $documentRequest->details->government_id_image_path)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $documentRequest->details->government_id_image_path) }}"
                                        target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                        View PDF Document
                                    </a>
                                </div>
                            @elseif(
                                $documentRequest->user->personalInformation &&
                                    $documentRequest->user->personalInformation->government_id_image_path)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $documentRequest->user->personalInformation->government_id_image_path) }}"
                                        target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                        View PDF Document
                                    </a>
                                </div>
                            @else
                                <p class="text-gray-500 text-sm">No government ID PDF uploaded</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="mb-8">
                <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Contact Information
                </h3>
                <div class="space-y-4">
                    <!-- Contact Person Information -->
                    @if (
                        $documentRequest->details &&
                            ($documentRequest->details->contact_first_name || $documentRequest->details->contact_last_name))
                        <div class="bg-blue-50 p-4 rounded-lg mb-4">
                            <h4 class="text-sm font-medium text-blue-800 mb-3">Contact Person (for this request)</h4>
                            <div class="grid md:grid-cols-3 gap-6 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $documentRequest->details->contact_last_name ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $documentRequest->details->contact_first_name ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $documentRequest->details->contact_middle_name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Contact Email</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $documentRequest->details->contact_email ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Contact Phone</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $documentRequest->details->contact_phone ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Beneficiary Contact Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-800 mb-3">Beneficiary Contact Information</h4>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Email Address</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->email ?? $documentRequest->user->email }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Contact Number</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->contact_no ?? ($documentRequest->user->contact_no ?? 'N/A') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Permanent
                            Address</h4>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Address Type</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->address_type ?? ($documentRequest->user->address_type ?? 'N/A') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Zip Code</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->zip_code ?? ($documentRequest->user->zip_code ?? 'N/A') }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Address Line 1</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->details->address_line_1 ?? ($documentRequest->user->address_line_1 ?? 'N/A') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Address Line 2</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->details->address_line_2 ?? ($documentRequest->user->address_line_2 ?? 'N/A') }}
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Region</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->region ?? ($documentRequest->user->region ?? 'N/A') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Province</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->province ?? ($documentRequest->user->province ?? 'N/A') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">City</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->city ?? ($documentRequest->user->city ?? 'N/A') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Barangay</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->barangay ?? ($documentRequest->user->barangay ?? 'N/A') }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Street</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->details->street ?? ($documentRequest->user->street ?? 'N/A') }}
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Present
                            Address</h4>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Address Type</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->address_type ?? ($documentRequest->user->address_type ?? 'N/A') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Zip Code</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->zip_code ?? ($documentRequest->user->zip_code ?? 'N/A') }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Address Line 1</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->details->address_line_1 ?? ($documentRequest->user->address_line_1 ?? 'N/A') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Address Line 2</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->details->address_line_2 ?? ($documentRequest->user->address_line_2 ?? 'N/A') }}
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Region</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->region ?? ($documentRequest->user->region ?? 'N/A') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Province</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->province ?? ($documentRequest->user->province ?? 'N/A') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">City</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->city ?? ($documentRequest->user->city ?? 'N/A') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Barangay</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->barangay ?? ($documentRequest->user->barangay ?? 'N/A') }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Street</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->details->street ?? ($documentRequest->user->street ?? 'N/A') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Family Information Section -->
            <div class="mb-8">
                <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Family Information
                </h3>
                <div class="space-y-6">
                    <!-- Father Information -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Father's Information</h4>
                        <div class="grid md:grid-cols-4 gap-6 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->father_last_name ?? ($documentRequest->user->father_last_name ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->father_first_name ?? ($documentRequest->user->father_first_name ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->father_middle_name ?? ($documentRequest->user->father_middle_name ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Suffix</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->father_suffix ?? ($documentRequest->user->father_suffix ?? 'N/A') }}
                                </p>
                            </div>
                        </div>
                        <div class="grid md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Birth Date</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ optional($documentRequest->details)->father_birthdate
                                        ? \Carbon\Carbon::parse($documentRequest->details->father_birthdate)->format('M d, Y')
                                        : (optional($documentRequest->user)->father_birthdate
                                            ? \Carbon\Carbon::parse($documentRequest->user->father_birthdate)->format('M d, Y')
                                            : 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Nationality</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->father_nationality ?? ($documentRequest->user->father_nationality ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Religion</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->father_religion ?? ($documentRequest->user->father_religion ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Contact Number</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->father_contact_no ?? ($documentRequest->user->father_contact_no ?? 'N/A') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Mother Information -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Mother's Information</h4>
                        <div class="grid md:grid-cols-4 gap-6 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Last Maiden Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->mother_last_name ?? ($documentRequest->user->mother_last_name ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">First Maiden Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->mother_first_name ?? ($documentRequest->user->mother_first_name ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Middle Maiden Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->mother_middle_name ?? ($documentRequest->user->mother_middle_name ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Suffix</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->mother_suffix ?? ($documentRequest->user->mother_suffix ?? 'N/A') }}
                                </p>
                            </div>
                        </div>
                        <div class="grid md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Birth Date</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ optional($documentRequest->details)->mother_birthdate
                                        ? \Carbon\Carbon::parse($documentRequest->details->mother_birthdate)->format('M d, Y')
                                        : (optional($documentRequest->user)->mother_birthdate
                                            ? \Carbon\Carbon::parse($documentRequest->user->mother_birthdate)->format('M d, Y')
                                            : 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Nationality</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->mother_nationality ?? ($documentRequest->user->mother_nationality ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Religion</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->mother_religion ?? ($documentRequest->user->mother_religion ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Contact Number</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->mother_contact_no ?? ($documentRequest->user->mother_contact_no ?? 'N/A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Deceased Person Information -->
            <div class="mb-8">
                <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Deceased Person
                    Information</h3>
                <div class="space-y-4">
                    <div class="grid md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->deceased_last_name ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->deceased_first_name ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->deceased_middle_name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    <!-- Parental Information -->
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Father's Name</h4>
                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->deceased_father_last_name ?? 'N/A' }}
                            </div>
                            <div class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->deceased_father_first_name ?? 'N/A' }}
                            </div>
                            <div class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->deceased_father_middle_name ?? 'N/A' }}
                            </div>
                        </div>
                        <h4 class="text-sm font-medium text-gray-700 mt-4 mb-2">Mother's Maiden Name</h4>
                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->deceased_mother_last_name ?? 'N/A' }}
                            </div>
                            <div class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->deceased_mother_first_name ?? 'N/A' }}
                            </div>
                            <div class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->deceased_mother_middle_name ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Requester Information -->
            <div class="mb-8">
                <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Requester Information
                </h3>
                <div class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Request Type</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                @if ($documentRequest->details && $documentRequest->details->request_for === 'someone_else')
                                    Request for Someone Else
                                @else
                                    Personal Request
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Contact Person Information (if different from requester) -->
                    @if (
                        $documentRequest->details &&
                            ($documentRequest->details->contact_first_name || $documentRequest->details->contact_last_name))
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-blue-800 mb-3">Contact Person (for this request)</h4>
                            <div class="grid md:grid-cols-3 gap-6 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $documentRequest->details->contact_last_name ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $documentRequest->details->contact_first_name ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $documentRequest->details->contact_middle_name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Contact Email</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $documentRequest->details->contact_email ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Contact Phone</label>
                                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                        {{ $documentRequest->details->contact_phone ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Requester Contact Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-800 mb-3">Requester Contact Information</h4>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Requester Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->first_name ?? $documentRequest->user->first_name }}
                                    {{ $documentRequest->details->last_name ?? $documentRequest->user->last_name }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Contact Number</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->contact_no ?? ($personalInformation->contact_no ?? 'N/A') }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Email Address</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->email ?? $documentRequest->user->email }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        @endif
    </div>

    {{-- Requested Service --}}
    <div class="flux-card p-6 mb-6">
        <div class="flex items-center space-x-2 mb-4">
            <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Requested Service</h4>
        </div>

        {{-- Service Information --}}
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service Title</label>
                <p class="text-gray-900 font-medium">{{ $documentRequest->service->title }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Office</label>
                <p class="text-gray-900">{{ $documentRequest->office->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service Description</label>
                <p class="text-gray-600 text-sm">
                    {{ $documentRequest->service->description ?? 'No description available' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Uploaded Attachments -->
    <div class="flux-card p-6 mb-6">
        <div class="flex items-center space-x-2 mb-4">
            <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Uploaded Attachments</h4>
        </div>
        @if ($documentRequest->payment_proof_path)
            <div class="flex flex-col items-center space-y-4">
                <div class="">
                    {{-- w-48 h-32 for the image --}}
                    <a href="{{ asset('storage/' . $documentRequest->payment_proof_path) }}" target="_blank"
                        class="text-blue-600 hover:underline">
                        <img src="{{ asset('storage/' . $documentRequest->payment_proof_path) }}" alt="Payment Proof"
                            class="w-full h-32 object-cover border rounded mb-2">
                        <div class="text-xs text-gray-500">View Attachment</div>
                    </a>
                </div>
            </div>
        @else
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                    viewBox="0 0 48 48">
                    <path
                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="mt-2 text-sm text-gray-600">No attachments uploaded yet</p>
            </div>
        @endif
    </div>

    <!-- Payment Status -->
    <div class="flux-card p-6 mb-6">
        <div class="flex items-center space-x-2 mb-4">
            <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Payment Status</h4>
        </div>

        <!-- Current Payment Status Display -->
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">
                        {{ ucfirst($documentRequest->payment_status ?? 'Pending') }}
                    </p>
                    @if ($documentRequest->payment_method)
                        <p class="text-xs text-gray-500">Method: {{ $documentRequest->payment_method }}</p>
                    @endif
                    @if ($documentRequest->payment_reference)
                        <p class="text-xs text-gray-500">Reference: {{ $documentRequest->payment_reference }}</p>
                    @endif
                    @if ($documentRequest->payment_date)
                        <p class="text-xs text-gray-500">Paid on:
                            {{ \Carbon\Carbon::parse($documentRequest->payment_date)->format('M d, Y h:i A') }}
                        </p>
                    @endif
                </div>
                <span
                    class="flux-badge
                    @if ($documentRequest->payment_status === 'paid') flux-badge-success
                    @elseif ($documentRequest->payment_status === 'failed') flux-badge-danger
                    @else flux-badge-secondary @endif">
                    {{ ucfirst($documentRequest->payment_status ?? 'Unpaid') }}
                </span>
            </div>
        </div>

        <!-- Payment Status Update Buttons -->
        <div class="space-y-3">
            <h5 class="text-sm font-medium text-gray-700 mb-3">Update Payment Status:</h5>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                <button wire:click="setPaymentStatusToUpdate('unpaid')" wire:prevent-scroll
                    class="flex items-center justify-center flux-btn flux-btn-sm @if ($documentRequest->payment_status === 'unpaid') flux-btn-primary @else flux-btn-outline @endif">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                        </path>
                    </svg>
                    <span class="text-xs">Unpaid</span>
                </button>

                <button wire:click="setPaymentStatusToUpdate('paid')" wire:prevent-scroll
                    class="flex items-center justify-center flux-btn flux-btn-sm @if ($documentRequest->payment_status === 'paid') flux-btn-success @else flux-btn-outline @endif">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                        </path>
                    </svg>
                    <span class="text-xs">Paid</span>
                </button>

                <button wire:click="setPaymentStatusToUpdate('failed')" wire:prevent-scroll
                    class="flex items-center justify-center flux-btn flux-btn-sm @if ($documentRequest->payment_status === 'failed') flux-btn-danger @else flux-btn-outline @endif">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                    <span class="text-xs">Failed</span>
                </button>

            </div>
        </div>
    </div>

    <!-- Remarks Input -->


    <!-- Document Request Status -->
    <div class="flux-card p-6 mb-6">
        <div class="flex items-center space-x-2 mb-4">
            <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Document Request Status
            </h4>
        </div>

        <!-- Current Status Display -->
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">
                        {{ ucfirst($documentRequest->status ?? 'Pending') }}
                    </p>
                    @if ($documentRequest->staff)
                        <p class="text-xs text-gray-500">Processed by: {{ $documentRequest->staff->name }}</p>
                    @endif
                    @if ($documentRequest->completed_date)
                        <p class="text-xs text-gray-500">Completed on:
                            {{ \Carbon\Carbon::parse($documentRequest->completed_date)->format('M d, Y h:i A') }}
                        </p>
                    @endif
                </div>
                <span
                    class="flux-badge
                    @if ($documentRequest->status === 'pending') flux-badge-warning
                    @elseif ($documentRequest->status === 'in-progress') flux-badge-info
                    @elseif ($documentRequest->status === 'ready-for-pickup') flux-badge-success
                    @elseif ($documentRequest->status === 'complete') flux-badge-success
                    @elseif ($documentRequest->status === 'cancelled') flux-badge-danger
                    @else flux-badge-secondary @endif">
                    {{ ucfirst(str_replace('-', ' ', $documentRequest->status ?? 'Pending')) }}
                </span>
            </div>
        </div>

        <!-- Status Update Buttons -->
        <div class="space-y-3">
            <h5 class="text-sm font-medium text-gray-700 mb-3">Update Document Request Status:</h5>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                <button wire:click="setDocumentStatusToUpdate('pending')" wire:prevent-scroll
                    class="flex items-center justify-center flux-btn flux-btn-sm @if ($documentRequest->status === 'pending') flux-btn-warning @else flux-btn-outline @endif">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-xs">Pending</span>
                </button>

                <button wire:click="setDocumentStatusToUpdate('in-progress')" wire:prevent-scroll
                    class="flex items-center justify-center flux-btn flux-btn-sm @if ($documentRequest->status === 'in-progress') flux-btn-info @else flux-btn-outline @endif">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span class="text-xs">In Progress</span>
                </button>

                <button wire:click="setDocumentStatusToUpdate('ready-for-pickup')" wire:prevent-scroll
                    class="flex items-center justify-center flux-btn flux-btn-sm @if ($documentRequest->status === 'ready-for-pickup') flux-btn-success @else flux-btn-outline @endif">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                    <span class="text-xs">Ready for Pickup</span>
                </button>

                <button wire:click="setDocumentStatusToUpdate('complete')" wire:prevent-scroll
                    class="flex items-center justify-center flux-btn flux-btn-sm @if ($documentRequest->status === 'complete') flux-btn-success @else flux-btn-outline @endif">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-xs">Complete</span>
                </button>

                <button wire:click="setDocumentStatusToUpdate('cancelled')" wire:prevent-scroll
                    class="flex items-center justify-center flux-btn flux-btn-sm @if ($documentRequest->status === 'cancelled') flux-btn-danger @else flux-btn-outline @endif">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="text-xs">Cancelled</span>
                </button>
            </div>
        </div>


    </div>

    @if (!empty($documentRequest->remarks))
        <div class="flux-card p-6 mb-6">
            <div class="flex items-center space-x-2 mb-4">
                <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Remarks</h4>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $documentRequest->remarks }}</p>
            </div>
        </div>
    @endif

    <!-- Navigation -->
    <div class="flux-card p-6 mb-6">
        <div class="flex items-center space-x-2 mb-4">
            <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Navigation</h4>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('staff.documents') }}" class="flux-btn flux-btn-outline flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back to List</span>
            </a>

            {{-- <button onclick="printDocument()" class="flux-btn flux-btn-primary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                    </path>
                </svg>
                <span>Print Document</span>
            </button> --}}
        </div>
    </div>

    <script>
        function printDocument() {
            // Add print-specific styles
            const style = document.createElement('style');
            style.innerHTML = `
                @media print {
                    body * {
                        visibility: hidden;
                    }
                    .flux-card, .flux-card * {
                        visibility: visible;
                    }
                    .flux-card {
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 100%;
                    }
                    button, .flux-btn, .navigation-section, .modal {
                        display: none !important;
                    }
                }
            `;
            document.head.appendChild(style);

            // Trigger browser print dialog
            window.print();

            // Remove the style after printing
            setTimeout(() => {
                document.head.removeChild(style);
            }, 1000);
        }
    </script>
</div>
