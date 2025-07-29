<div class="px-5 py-2 mt-5">
    <div class="header mb-4">
        <h3 class="text-xl font-semibold text-base-content">Confirmation</h3>

        @if ($error)
            <div class="alert alert-error flex items-center gap-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ $error }}</span>
            </div>
        @endif

        <div class="alert alert-info flex items-center gap-2 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Please review your appointment details before submitting.</span>
        </div>
    </div>

    <!-- Loading State -->
    <div wire:loading.delay.class="flex" wire:loading.remove.class="hidden"
        class="hidden justify-center items-center w-full h-64">
        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-500"></div>
    </div>

    <div class="space-y-6" wire:loading.remove>
        <!-- Appointment Summary -->
        <div class="flex flex-row gap-4 w-full">
            
            <div class="flux-card p-6 w-1/2">
                <div>
                    <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Appointment Summary</h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Office</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $office->name }}</p>
                        </div> 
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Purpose</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $purpose }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Date</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Time</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ \Carbon\Carbon::parse($selectedTime)->format('h:i A') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Personal Information</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $first_name }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $middle_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $last_name }}
                                </p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $email }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Phone</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $phone }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Request Details</h3>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Purpose</label>
                                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $purpose }}
                                        </p>
                                    </div>
                                </div>
                                @if ($message)
                                    <div class="grid grid-cols-1 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600 mb-1">Additional Notes</label>
                                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                                {{ $message }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                </div>
            </div>

            @if ($includeCertificates && count($certificates) > 0)
                <!-- Certificate Requests -->
                 <div class="flux-card p-6 w-1/2">
                    <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Certificate Requests
                    </h3>

                    @foreach ($certificates as $index => $certificate)
                        @php
                            $toWhom = [
                                'myself' => 'SF',
                                'spouse' => 'SP',
                                'child' => 'CH',
                                'parent' => 'PA',
                                'sibling' => 'SI',
                                'other' => 'OT',
                            ];

                            $certificateTypeCodes = [
                                'birth_certificate' => 'BC',
                                'marriage_certificate' => 'MC',
                                'death_certificate' => 'DC',
                            ];

                            $certificateCode = $certificateTypeCodes[$certificate['certificate_type']] . ':' . $toWhom[$certificate['relationship']] . ' - ' . $first_name . ' ' . $last_name;
                        @endphp
                        <div class="flex flex-row mb-4 p-3 border border-gray-200 rounded-lg">
                            <h5 class="font-medium text-gray-700 mb-2" style="margin-right: 12px;"> #{{ $index + 1 }}</h5>
                            <h5 class="font-bold text-gray-400 mb-2">
                                {{ $certificateCode }}
                            </h5>
                              
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flux-card p-6 w-1/2">
                    <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">No Certificate Requests</h3>
                </div>
            @endif

        </div>
        
 
    </div>

    <footer class="my-6 flex justify-between" wire:loading.remove>
        <button class="flux-btn flux-btn-secondary" wire:click="previousStep"
            wire:loading.attr="disabled">Previous</button>
        <button class="flux-btn flux-btn-primary" wire:click="submitAppointment" wire:loading.attr="disabled">
            <span wire:loading wire:target="submitAppointment" class="loading loading-spinner"></span>
            Submit
        </button>
    </footer>

</div>
