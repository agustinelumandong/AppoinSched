<div>
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
        @if ($documentRequest->service->title !== 'Death Certificate')
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
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="mb-8">
                <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Contact Information
                </h3>
                <div class="space-y-4">
                    <!-- Contact Person Information -->
                    @if ($documentRequest->details && ($documentRequest->details->contact_first_name || $documentRequest->details->contact_last_name))
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
                <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Family Information</h3>
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
                                <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->mother_last_name ?? ($documentRequest->user->mother_last_name ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->mother_first_name ?? ($documentRequest->user->mother_first_name ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
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

                    <!-- Spouse Information -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Spouse's Information</h4>
                        <div class="grid md:grid-cols-4 gap-6 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->spouse_last_name ?? ($documentRequest->user->spouse_last_name ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->spouse_first_name ?? ($documentRequest->user->spouse_first_name ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->spouse_middle_name ?? ($documentRequest->user->spouse_middle_name ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Suffix</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->spouse_suffix ?? ($documentRequest->user->spouse_suffix ?? 'N/A') }}
                                </p>
                            </div>
                        </div>
                        <div class="grid md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Birth Date</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ optional($documentRequest->details)->spouse_birthdate
            ? \Carbon\Carbon::parse($documentRequest->details->spouse_birthdate)->format('M d, Y')
            : (optional($documentRequest->user)->spouse_birthdate
                ? \Carbon\Carbon::parse($documentRequest->user->spouse_birthdate)->format('M d, Y')
                : 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Nationality</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->spouse_nationality ?? ($documentRequest->user->spouse_nationality ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Religion</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->spouse_religion ?? ($documentRequest->user->spouse_religion ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Contact Number</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $documentRequest->details->spouse_contact_no ?? ($documentRequest->user->spouse_contact_no ?? 'N/A') }}
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
                    Information
                </h3>
                <div class="space-y-4">
                    <div class="grid md:grid-cols-4 gap-6">
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
                        {{-- <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Full Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details ? $documentRequest->details->deceased_full_name : 'N/A' }}
                            </p>
                        </div> --}}
                    </div>
                </div>
            </div>

            <!-- Death Information -->
            <div class="mb-8">
                <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Death Information</h3>
                <div class="space-y-4">
                    <div class="grid md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Date of Death</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details && $documentRequest->details->death_date ? \Carbon\Carbon::parse($documentRequest->details->death_date)->format('M d, Y') : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Time of Death</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details && $documentRequest->details->death_time ? \Carbon\Carbon::parse($documentRequest->details->death_time)->format('h:i A') : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Place of Death</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->death_place ?? 'N/A' }}
                            </p>
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
                            <label class="block text-sm font-medium text-gray-600 mb-1">Relationship to
                                Deceased</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->details->relationship_to_deceased ?? 'N/A' }}
                            </p>
                        </div>
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
                    @if ($documentRequest->details && ($documentRequest->details->contact_first_name || $documentRequest->details->contact_last_name))
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Staff</label>
                <p class="text-gray-900">
                    @if ($documentRequest->staff)
                        {{ $documentRequest->staff->first_name }} {{ $documentRequest->staff->last_name }}
                    @else
                        <span class="text-gray-500 italic">No staff assigned</span>
                    @endif
                </p>
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
    {{-- <div class="flux-card p-6">
        <div class="flex items-center space-x-2 mb-4">
            <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Uploaded Attachments
            </h4>
        </div>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path
                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <p class="mt-2 text-sm text-gray-600">No attachments uploaded yet</p>
            <p class="text-xs text-gray-500">(Feature coming soon)</p>
        </div>
    </div> --}}

    <!-- Payment Status -->
    {{-- <div class="flux-card p-6">
        <div class="flex items-center space-x-2 mb-4">
            <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Payment Status</h4>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">Payment Required</p>
                    <p class="text-xs text-gray-500">Payment processing feature coming soon</p>
                </div>
                <span class="flux-badge flux-badge-warning">Pending</span>
            </div>
        </div>
    </div> --}}

    <!-- Remarks Input -->
    <div class="flux-card p-6 mb-6">
        <div class="flex items-center space-x-2 mb-4">
            <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Remarks</h4>
            <span class="text-sm text-gray-500">(Optional)</span>
        </div>
        <textarea wire:model="remarks" class="flux-form-control w-full h-24 resize-none"
            placeholder="Add any remarks or notes about this request..."></textarea>
    </div>

    <!-- Action Buttons -->
    <div class="flux-card p-6 mb-6">
        <div class="flex items-center space-x-2 mb-4">
            <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Actions</h4>
        </div>
        <div class="action-buttons">
            <button wire:click="$set('confirmApproved', true); $dispatch('open-modal-confirm-document-request')"
                class="flux-btn flux-btn-success flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Approve Request
            </button>

            <button wire:click="$set('confirmRejected', true); $dispatch('open-modal-confirm-document-request')"
                class="flux-btn flux-btn-danger flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
                Reject Request
            </button>

            <button wire:click="$set('confirmPending', true); $dispatch('open-modal-confirm-document-request')"
                class="flux-btn flux-btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Set to Pending
            </button>

            <a href="{{ route('admin.document-request') }}" class="flux-btn flux-btn-outline flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>
                    Back to List</span>
            </a>
        </div>
    </div>
</div>