<div class="px-5 py-2 mt-5">
    <div class="header mb-4">
                <h3 class="text-xl font-semibold text-base-content">{{ label_with_bisaya('Confirmation', 'confirmation') }}</h3>

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

            <div class="flux-card p-6 {{ $office->slug === 'municipal-treasurers-office' ? 'w-full' : 'w-1/2' }}">
                <div>
                    <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">{{ label_with_bisaya('Appointment Summary', 'appointment_details') }}</h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">{{ label_with_bisaya('Office', 'office') }}</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $office->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">{{ label_with_bisaya('Purpose', 'purpose') }}</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $purpose }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">{{ label_with_bisaya('Date', 'date') }}</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">{{ label_with_bisaya('Time', 'time') }}</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ \Carbon\Carbon::parse($selectedTime)->format('h:i A') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">{{ label_with_bisaya('Personal Information', 'personal_information') }}</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">{{ label_with_bisaya('First Name', 'first_name') }}</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $first_name }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">{{ label_with_bisaya('Middle Name', 'middle_name') }}</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                    {{ $middle_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">{{ label_with_bisaya('Last Name', 'last_name') }}</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $last_name }}
                                </p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">{{ label_with_bisaya('Email', 'email') }}</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $email }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">{{ label_with_bisaya('Phone', 'phone') }}</label>
                                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $phone }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">{{ label_with_bisaya('Request Details', 'request_details') }}</h3>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">{{ label_with_bisaya('Purpose', 'purpose') }}</label>
                                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">{{ $purpose }}
                                        </p>
                                    </div>
                                </div>
                                @if ($message)
                                    <div class="grid grid-cols-1 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-600 mb-1">{{ label_with_bisaya('Additional Notes', 'additional_notes') }}</label>
                                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                                {{ $message }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                </div>
            </div>

            @if ($this->includeCertificates && count($certificates) > 0)
                <!-- Certificate Requests -->
                <div class="flux-card p-6 w-1/2">
                    <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">{{ label_with_bisaya('Certificate Requests', 'certificate_requests') }}
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
                                'special-permit' => 'SP',
                            ];

                            $certificateCode = $certificateTypeCodes[$certificate['certificate_type']]
                                . ':' . $toWhom[$certificate['relationship']]
                                . ' - ' . $certificate['first_name']
                                . (!empty($certificate['middle_name']) ? ' ' . $certificate['middle_name'] : '')
                                . ' ' . $certificate['last_name'];
                        @endphp
                        <div class="flex flex-row mb-4 p-3 border border-gray-200 rounded-lg">
                            <h5 class="font-medium text-gray-700 mb-2" style="margin-right: 12px;"> #{{ $index + 1 }}</h5>
                            <h5 class="font-bold text-gray-400 mb-2">
                                {{ $certificateCode }}
                            </h5>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>


    </div>

    <footer class="my-6 flex justify-between" wire:loading.remove>
        <button class="flux-btn flux-btn-secondary" wire:click="previousStep"
            wire:loading.attr="disabled">Previous</button>
        <button class="flux-btn btn-sm flux-btn-primary" type="button" x-data="{}"
            x-on:click="$dispatch('open-modal-submit-confirmation')" title="Submission Confirmation">
            <i class="bi bi-check"></i>Submit
        </button>
        {{-- <button class="flux-btn flux-btn-primary" wire:click="submitAppointment" wire:loading.attr="disabled">
            <span wire:loading wire:target="submitAppointment" class="loading loading-spinner"></span>
            Submit
        </button> --}}
    </footer>
<x-modal id="submit-confirmation" title="Submission Confirmation" size="max-w-2xl">
    <div class="modal-body">
        <div class="text-sm space-y-4 max-h-[60vh] overflow-y-auto p-2">

            <div class="bg-base-200 p-3 rounded-lg">
            <p>Please confirm that you have reviewed all appointment details and information provided is accurate.</p>
            <p class="mt-2">Once submitted, changes may require contacting the office directly.</p>
            </div>

            <div class="flex items-center alert alert-warning mt-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <span>By proceeding, you acknowledge that all provided information is complete and accurate.</span>
            </div>
        </div>
    </div>

    <x-slot name="footer">
        <div class="gap-2">
            <button type="button" class="flux-btn flux-btn-outline" x-data
                x-on:click="$dispatch('close-modal-submit-confirmation')">
                <i class="bi bi-x-lg me-1"></i>Cancel
            </button>
            <button type="button" class="flux-btn btn-sm flux-btn-success" x-data="{}"
                x-on:click="
        $dispatch('close-modal-submit-confirmation');
        $wire.submitAppointment();
    ">
                <span wire:loading.remove>
                    <i class="bi bi-check-circle me-1"></i>
                    Confirm
                </span>
                <span wire:loading>
                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                    Loading...
                </span>
            </button>
        </div>
    </x-slot>
</x-modal>
</div>
