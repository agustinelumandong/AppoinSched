<div class="px-5 py-2 mt-5">
    <div class="header mb-4">
        <h3 class="text-xl font-semibold text-base-content">Appointment Slip</h3>
        <div class="alert alert-success flex items-center gap-2 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Your appointment has been submitted successfully! A copy has been sent to your email. Please save your
                appointment slip.</span>
        </div>
    </div>

    <div id="appointment-slip" class="flux-card p-6 border border-gray-300">
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }

                #appointment-slip,
                #appointment-slip * {
                    visibility: visible;
                }

                #appointment-slip {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                }

                .btn,
                button {
                    display: none !important;
                }
            }
        </style>
        <!-- Header with Logo -->
        <div class="flex justify-between items-center border-b pb-4 mb-4">
            <div>
                <h2 class="text-xl font-bold text-primary">Appointment Confirmation</h2>
                <p class="text-sm text-gray-600">Transaction ID: {{ $reference_number }}</p>
            </div>
            <div>
                <img src="{{ asset('images/LGU_logo.png') }}" alt="Logo" class="h-16 w-auto">
            </div>
        </div>

        <!-- Appointment Details -->
        <div class="space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <h3 class="text-md font-semibold text-gray-700">Appointment Details</h3>
                    <div class="mt-2 space-y-1">
                        <p><span class="font-medium">{{ label_with_bisaya('Office', 'office') }}:</span> {{ $office->name }}</p>
                        <p><span class="font-medium">{{ label_with_bisaya('Purpose', 'purpose') }}:</span> {{ $purpose }}</p>
                        <p><span class="font-medium">{{ label_with_bisaya('Date', 'date') }}:</span>
                            {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}
                        </p>
                        <p><span class="font-medium">{{ label_with_bisaya('Time', 'time') }}:</span>
                            {{ \Carbon\Carbon::parse($selectedTime)->format('h:i A') }}
                        </p>
                    </div>
                </div>

                <div>
                    <h3 class="text-md font-semibold text-gray-700">{{ label_with_bisaya('Client Information', 'client_information') }}</h3>
                    <div class="mt-2 space-y-1">
                        <p><span class="font-medium">{{ label_with_bisaya('Name', 'name') }}:</span>
                            @php
                                $middleName = strtolower($middle_name ?? '') !== 'n/a' && !empty(trim($middle_name ?? '')) ? $middle_name : null;
                                $nameParts = array_filter([$first_name, $middleName], function ($part) {
                                    return !empty($part) && strtolower(trim($part)) !== 'n/a';
                                });
                                $name = $last_name . ', ' . implode(' ', $nameParts);
                            @endphp
                            {{ $name }}
                        </p>
                        <p><span class="font-medium">{{ label_with_bisaya('Email', 'email') }}:</span> {{ $email }}</p>
                        <p><span class="font-medium">{{ label_with_bisaya('Phone', 'phone') }}:</span> {{ $phone }}</p>
                    </div>
                </div>
            </div>

            @if ($this->includeCertificates && count($certificates) > 0)
                <!-- Certificate Requests -->
                <div class="border-t pt-4">
                    <h3 class="text-md font-semibold text-gray-700 mb-3">{{ label_with_bisaya('Certificate Requests', 'certificate_requests') }}</h3>
                    <div class="space-y-2">
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
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded border">
                                <span class="font-medium text-sm">Certificate #{{ $index + 1 }}</span>
                                <span class="font-mono text-sm font-bold text-gray-700">{{ $certificateCode }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- <div class="flex justify-center my-4">
                <div class="text-center" x-data x-init="
                    const qrValue = '{{ $reference_number ?? 'appointment-request' }}';
                    const qr = qrcode(0, 'H');
                    qr.addData(qrValue);
                    qr.make();
                    $refs.qr.innerHTML = qr.createImgTag(4, 8);
                ">
                    <div x-ref="qr" class="mx-auto" wire:ignore></div>
                    <p class="text-xs text-gray-500 mt-2">Scan this QR code at the office</p>
                </div>
            </div>

            @push('scripts')
            <script src="{{ asset('js/qrcode.min.js') }}"></script>
            @endpush --}}



            <!-- Important Notes -->
            <div class="bg-gray-50 p-4 rounded-lg text-sm">
                <h4 class="font-semibold mb-2">Important Notes:</h4>
                <ul class="list-disc list-inside space-y-1">
                    <li>Please arrive 15 minutes before your scheduled appointment.</li>
                    <li>Bring a valid ID for verification.</li>
                    <li>Your appointment is subject to approval. You will receive a confirmation email/SMS.</li>
                    <li>For cancellations, please contact us at least 24 hours in advance.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="flex justify-center mt-6 gap-4">
        <a href="{{ route('appointment-slip.download', $reference_number) }}" class="btn btn-primary flex items-center"
            style="display:flex; align-items:center;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Download Slip</span>
        </a>
        <button wire:click="sendEmailSlip" wire:loading.attr="disabled" class="btn btn-accent flex items-center"
            style="display:flex; align-items:center;">
            <svg wire:loading.remove wire:target="sendEmailSlip" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <div wire:loading wire:target="sendEmailSlip" class="loading loading-spinner loading-sm mr-2"></div>
            <span wire:loading.remove wire:target="sendEmailSlip">Send to Email</span>
            <span wire:loading wire:target="sendEmailSlip">Sending...</span>
        </button>
        <a href="{{ route('dashboard') }}" class="btn btn-outline flex items-center"
            style="display:flex; align-items:center;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Go to Dashboard
        </a>
    </div>


</div>

