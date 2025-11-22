<?php

declare(strict_types=1);

use Livewire\Attributes\On;
use Livewire\Volt\Component;
use App\Models\Appointments;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Models\Offices;
use App\Models\AppointmentDetails;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\DocumentRequest;
use App\Notifications\RequestEventNotification;
use App\Enums\RequestNotificationEvent;
use App\Notifications\AdminEventNotification;
use App\Enums\AdminNotificationEvent;

new #[Title('Appointment')] class extends Component {
    public int $step = 1;
    public string $purpose = 'consultation';
    public string $otherPurpose = '';
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';

    // Certificate request properties
    public array $certificates = [];
    public bool $includeCertificates = true;

    public string $message = '';
    public ?int $appointmentId = null;
    public bool $isLoading = false;
    public ?string $reference_number = null;
    public ?string $error = null;

    public bool $editPersonDetails = false;
    public string $metadata = '';

    public ?int $id = null;

    public ?string $selectedDate = null;
    public ?string $selectedTime = null;

    public Offices $office;
    public User $staff;
    public User $user;

    public ?Appointments $appointment = null;

    public bool $termsAccepted = false;

    public function mount(Offices $office, ?string $reference_number = null): void
    {
        $this->office = $office;
        $this->id = auth()->user()->id;
        $this->editPersonDetails = false;
        // Set the reference_number if provided
        if ($reference_number) {
            $this->reference_number = $reference_number;
        }

        // Initialize user and userAddresses like userinfo.blade.php
        $this->user = auth()->user();

        // Initialize certificates array with one empty certificate
        $this->certificates = [
            [
                'certificate_type' => 'special-permit',
                'relationship' => '',
                'first_name' => '',
                'last_name' => '',
                'middle_name' => '',
            ],
        ];

        // Skip certificates step for MTO office
        if ($this->office->slug === 'municipal-treasurers-office') {
            $this->includeCertificates = false;
        }

        // Check if user has complete profile
        if (!auth()->user()->hasCompleteProfile()) {
            session()->flash('warning', 'Please complete your profile information before making an appointment.');
            $this->redirect(route('userinfo'));
        }

        // Always populate user data since we're removing the "to_whom" step
        $this->populateUserData();
    }

    public function termsAccepted()
    {
        if ($this->termsAccepted) {
            $this->step = 2;
            $this->isLoading = true;
            $this->dispatch('refresh-slots');
        } else {
            session()->flash('error', 'You must accept the terms and conditions to proceed.');
        }
    }

    public function populateUserData(): void
    {
        $userData = auth()->user()->getAppointmentFormData();
        // Populate form fields with user data (temporary data for this request only)
        foreach ($userData as $field => $value) {
            if (property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }
    }

    public function editPersonDetailsBtn()
    {
        $this->editPersonDetails = true;
    }

    public function lockPersonDetailsBtn()
    {
        $this->editPersonDetails = false;
    }

    public function nextStep()
    {
        switch ($this->step) {
            case 2:
                $this->isLoading = true;
                $this->validate([
                    'purpose' => 'required',
                ]);
                // Skip the certificates step if includeCertificates is false
                // if (!$this->includeCertificates) {
                //     $this->step++; // Skip to next step
                //     // Default to locked state (shows Edit button)
                //     $this->editPersonDetails = false;
                // }
                break;
            case 3:
                $this->isLoading = true;
                // Validate certificates if they are included
                if ($this->includeCertificates) {
                    foreach ($this->certificates as $index => $certificate) {
                        $this->validate([
                            "certificates.{$index}.certificate_type" => 'required',
                            "certificates.{$index}.relationship" => 'required',
                            "certificates.{$index}.first_name" => 'required|string',
                            "certificates.{$index}.last_name" => 'required|string',
                            "certificates.{$index}.middle_name" => 'nullable|string',
                        ]);
                    }
                    // Default to locked state (shows Edit button) when reaching personal information step (step 4 with certificates)
                    $this->editPersonDetails = false;
                } else {
                    // This is the personal information step when certificates are skipped
                    $this->validate([
                        'first_name' => 'string|required',
                        'last_name' => 'string|required',
                        'middle_name' => 'string|nullable',
                        'email' => 'string|required|email',
                        'phone' => 'required|numeric|digits:11',
                    ], [
                        'phone.digits' => 'The mobile number must be exactly 11 digits.',
                        'phone.required' => 'The mobile number field is required.',
                        'phone.numeric' => 'The mobile number must contain only numbers.',
                    ]);
                    // Default to locked state (shows Edit button)
                    $this->editPersonDetails = false;
                }
                break;
            case 4:
                $this->isLoading = true;
                if ($this->includeCertificates) {
                    // This is the personal information step when certificates are included
                    $this->validate([
                        'first_name' => 'string|required',
                        'last_name' => 'string|required',
                        'middle_name' => 'string|nullable',
                        'email' => 'string|required|email',
                        'phone' => 'required|numeric|digits:11',
                    ], [
                        'phone.digits' => 'The mobile number must be exactly 11 digits.',
                        'phone.required' => 'The mobile number field is required.',
                        'phone.numeric' => 'The mobile number must contain only numbers.',
                    ]);

                    // Add info redundancy validation directly here
                    if (!empty($this->certificates)) {
                        $currentUser = auth()->user();
                        $userData = $currentUser->getAppointmentFormData();

                        foreach ($this->certificates as $index => $certificate) {
                            if ($certificate['relationship'] === 'myself') {
                                continue;
                            }

                            $isRedundant = true;

                            if (strtolower(trim($certificate['first_name'])) !== strtolower(trim($userData['first_name'] ?? ''))) {
                                $isRedundant = false;
                            }
                            if (strtolower(trim($certificate['last_name'])) !== strtolower(trim($userData['last_name'] ?? ''))) {
                                $isRedundant = false;
                            }
                            if (!empty($certificate['middle_name']) && strtolower(trim($certificate['middle_name'])) !== strtolower(trim($userData['middle_name'] ?? ''))) {
                                $isRedundant = false;
                            }

                            if ($isRedundant) {
                                $relationshipName = ucfirst($certificate['relationship']);
                                $this->addError('info_redundancy', "INFO REDUNDANCY: You are entering your own information for a certificate that is for your {$relationshipName}. Please enter the correct information for your {$relationshipName} instead of your own details.");
                                return;
                            }
                        }
                    }
                } else {
                    // This is the date/time step when certificates are skipped
                    $this->validate([
                        'selectedTime' => 'required|string',
                    ]);
                }
                break;
            case 5:
                $this->isLoading = true;
                if ($this->includeCertificates) {
                    // This is the date/time step when certificates are included
                    $this->validate([
                        'selectedTime' => 'required|string',
                    ]);
                } else {
                    // This is the confirmation step when certificates are skipped
                    // No additional validation needed - just confirmation step
                }
                break;
            case 6:
                $this->isLoading = true;
                if ($this->includeCertificates) {
                    // This is the confirmation step when certificates are included
                    // No additional validation needed - just confirmation step
                } else {
                    // This is the appointment slip step when certificates are skipped
                    // No additional validation needed
                }
                break;
            case 7:
                $this->isLoading = true;
                // This is the appointment slip step when certificates are included
                // No additional validation needed
                break;
        }
        $this->step++;
    }

    public function updatedStep($value): void
    {
        // Default to locked state (shows Edit button) when reaching personal information step
        $personalInfoStep = $this->includeCertificates ? 4 : 3;
        if ($value == $personalInfoStep) {
            $this->editPersonDetails = false;
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            if ($this->step == 3 && $this->includeCertificates) {
                // If we are on the certificates step, go back to step 2
                $this->step = 2;
            } elseif ($this->step == 3 && !$this->includeCertificates) {
                // If we are on the personal details step without certificates, go back to step 2
                $this->step = 2;
            } elseif ($this->step == 4 && $this->includeCertificates) {
                // If we are on the personal details step with certificates, go back to step 3
                $this->step = 3;
            } elseif ($this->step == 4 && !$this->includeCertificates) {
                // If we are on the date/time step without certificates, go back to step 3
                $this->step = 3;
            } elseif ($this->step == 5 && $this->includeCertificates) {
                // If we are on the date/time step with certificates, go back to step 4
                $this->step = 4;
            } elseif ($this->step == 5 && !$this->includeCertificates) {
                // If we are on the confirmation step without certificates, go back to step 4
                $this->step = 4;
            } elseif ($this->step == 6 && $this->includeCertificates) {
                // If we are on the confirmation step with certificates, go back to step 5
                $this->step = 5;
            } elseif ($this->step == 6 && !$this->includeCertificates) {
                // If we are on the appointment slip step without certificates, go back to step 5
                $this->step = 5;
            } elseif ($this->step == 7 && $this->includeCertificates) {
                // If we are on the appointment slip step with certificates, go back to step 6
                $this->step = 6;
            } else {
                // Otherwise, just go back one step
                $this->step--;
            }
        } else {
            $this->step = 1;
            // session()->flash('error', 'Cannot go back further');
        }
        $this->isLoading = true;
    }

    public function submitAppointment(): void
    {
        $this->isLoading = true;

        try {
            $userId = auth()->id();

            // Check for scheduling conflicts
            if ($this->checkForConflicts()) {
                Log::info('Conflict found, appointment not saved');
                $this->error = 'This hour has reached the maximum of 5 appointments. Please select a different time.';
                $this->isLoading = false;
                return;
            }

            // Validate the data
            $validated = $this->validate([
                'purpose' => 'required|string',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|numeric|digits:11',
                'metadata' => 'nullable|array',
            ], [
                'phone.digits' => 'The mobile number must be exactly 11 digits.',
                'phone.required' => 'The mobile number field is required.',
                'phone.numeric' => 'The mobile number must contain only numbers.',
                'selectedDate' => 'required|date|after_or_equal:today',
                'selectedTime' => 'required|string',
            ]);

            // Validate certificates if included
            if ($this->includeCertificates && count($this->certificates) > 0) {
                foreach ($this->certificates as $index => $certificate) {
                    $this->validate([
                        "certificates.{$index}.certificate_type" => 'required',
                        "certificates.{$index}.relationship" => 'required',
                        "certificates.{$index}.first_name" => 'required|string',
                        "certificates.{$index}.last_name" => 'required|string',
                        "certificates.{$index}.middle_name" => 'nullable|string|max:255',
                    ]);
                }
            }

            DB::beginTransaction();

            if (!empty($this->reference_number)) {
                $reference_number = $this->reference_number;
            } else {
                do {
                    $reference_number = 'APPOINTMENT-' . strtoupper(Str::random(10));
                } while (Appointments::where('reference_number', $reference_number)->exists());
            }

            // Create the appointment
            $appointment = Appointments::create([
                'user_id' => $userId,
                'office_id' => $this->office->id,
                'booking_date' => $this->selectedDate,
                'booking_time' => $this->selectedTime,
                'to_whom' => 'myself', // Always set to 'myself' since we removed the step
                'purpose' => $this->purpose,
                'notes' => "Appointment for myself - {$this->purpose}",
                'reference_number' => $reference_number,
                'status' => 'on-going',
            ]);

            if (str_starts_with($reference_number, 'DOC')) {
                $request = DocumentRequest::where('reference_number', $reference_number)->first();
                if ($request) {
                    $request->update([
                        'payment_status' => 'walk-in',
                        'payment_reference' => $reference_number,
                    ]);
                }
            }

            // Process certificate requests if included
            if ($this->includeCertificates && count($this->certificates) > 0) {
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
                // MC:OT - John Doe
                AppointmentDetails::create([
                    'appointment_id' => $appointment->id,
                    'request_for' => 'myself', // Always set to 'myself' since we removed the step
                    'first_name' => $this->first_name,
                    'middle_name' => $this->middle_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'metadata' => collect($this->certificates)
                        ->map(function ($certificate, $index) use ($certificateTypeCodes, $toWhom) {
                            return [
                                'number' => $index + 1,
                                'certificate_request' => $certificate,
                                'certificate_code' => $certificateTypeCodes[$certificate['certificate_type']]
                                . ':' . $toWhom[$certificate['relationship']]
                                . ' - ' . $certificate['first_name']
                                . (!empty($certificate['middle_name']) ? ' ' . $certificate['middle_name'] : '')
                                . ' ' . $certificate['last_name'],
                            ];
                        })
                        ->all(),
                    'purpose' => $this->purpose,
                    'notes' => $appointment->notes,
                ]);
            } else {
                AppointmentDetails::create([
                    'appointment_id' => $appointment->id,
                    'request_for' => 'myself', // Always set to 'myself' since we removed the step
                    'first_name' => $this->first_name,
                    'middle_name' => $this->middle_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'metadata' => null,
                    'purpose' => $this->purpose,
                    'notes' => $appointment->notes,
                ]);
            }

            DB::commit();

            auth()
                ->user()
                ->notify(
                    new RequestEventNotification(RequestNotificationEvent::AppointmentScheduled, [
                        'date' => $this->selectedDate,
                        'time' => $this->selectedTime,
                        'location' => $this->office->name,
                        'reference_no' => $reference_number,
                    ]),
                );

            $staffs = User::getStaffsByOfficeId($this->office->id);
            if ($staffs->count() > 0) {
                foreach ($staffs as $staff) {
                    $staff->notify(
                        new AdminEventNotification(AdminNotificationEvent::UserAppointmentScheduled, [
                            'reference_no' => $reference_number,
                            'date' => $this->selectedDate,
                            'time' => $this->selectedTime,
                            'location' => $this->office->name,
                            'user' => auth()->user()->name,
                            'user_email' => auth()->user()->email,
                        ]),
                    );
                }
            }
            auth()->user()->notify(new \App\Notifications\AppointmentSlipNotification($appointment));

            $cacheKey = "time_slots_{$this->office->id}_{$this->selectedDate}";
            Cache::forget($cacheKey);

            if ($appointment) {
                Log::info('Appointment created successfully', ['appointment_id' => $appointment->id]);

                $this->reference_number = $reference_number;
                if (!$this->includeCertificates) {
                    $this->step = 6;  // MTO office - no certificates, appointment slip at step 6
                } else {
                    $this->step = 7;  // Other offices - with certificates, appointment slip at step 7
                }

                $cacheKey = "time_slots_{$this->office->id}_{$this->selectedDate}";
                Cache::forget($cacheKey);
                $this->dispatch('refresh-slots');
            } else {
                Log::error('Appointment creation failed - no appointment object returned');
                session()->flash('error', 'Failed to create appointment. Please try again.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exception during appointment creation: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => [
                    'user_id' => auth()->id() ?? null,
                    'office_id' => $this->office->id ?? null,
                ],
            ]);
            session()->flash('error', 'Error: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    #[On('slot-picker-updated')]
    public function handleSlotUpdate(array $data): void
    {
        try {
            Log::info('Slot update received in stepper', [
                'data' => $data,
                'current_step' => $this->step,
            ]);

            if (!isset($data['date']) || !isset($data['time'])) {
                Log::warning('Invalid slot update data', ['data' => $data]);
                return;
            }

            $this->selectedDate = $data['date'];
            $this->selectedTime = $data['time'];

            Log::info('Slot picker updated in stepper', [
                'date' => $this->selectedDate,
                'time' => $this->selectedTime,
                'office_id' => $this->office->id,
            ]);

            // Force Livewire to update the UI
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            Log::error('Error updating slot in stepper: ' . $e->getMessage(), [
                'data' => $data ?? 'null',
                'exception' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Failed to update appointment time. Please try again.');
        }
    }

    private function checkForConflicts(): bool
    {
        try {
            // Get hour from selected time
            $hour = Carbon::parse($this->selectedTime)->format('H');

            // Count appointments in the same hour (hourly limit is 5)
            $hourlyLimit = 5; // Maximum appointments per hour

            $hourlyCount = Appointments::where('booking_date', $this->selectedDate)
                ->where('office_id', $this->office->id)
                ->whereRaw('HOUR(booking_time) = ?', [$hour])
                ->whereIn('status', ['on-going', 'completed'])
                ->count();

            $conflict = $hourlyCount >= $hourlyLimit;

            Log::info('Checking for hourly conflicts in stepper:', [
                'date' => $this->selectedDate,
                'time' => $this->selectedTime,
                'hour' => $hour,
                'office_id' => $this->office->id,
                'hourly_count' => $hourlyCount,
                'hourly_limit' => $hourlyLimit,
                'has_conflict' => $conflict,
            ]);

            return $conflict;
        } catch (\Exception $e) {
            Log::error('Error checking for conflicts in stepper: ' . $e->getMessage(), [
                'date' => $this->selectedDate,
                'time' => $this->selectedTime,
            ]);
            return true; // Return true to prevent booking in case of error
        }
    }

    public function sendEmailSlip(): void
    {
        try {
            // Find the appointment by reference number
            $appointment = Appointments::where('reference_number', $this->reference_number)->first();

            if (!$appointment) {
                session()->flash('error', 'Appointment not found.');
                return;
            }

            // Send the appointment slip notification
            auth()->user()->notify(new \App\Notifications\AppointmentSlipNotification($appointment));

            session()->flash('success', 'Appointment slip has been sent to your email address.');
        } catch (\Exception $e) {
            Log::error('Error sending appointment slip email: ' . $e->getMessage());
            session()->flash('error', 'Failed to send appointment slip. Please try again.');
        }
    }

    // Certificate management methods
    public function addCertificate(): void
    {
        $this->certificates[] = [
            'certificate_type' => 'special-permit',
            'relationship' => '',
            'first_name' => '',
            'last_name' => '',
            'middle_name' => '',
        ];
    }

    public function removeCertificate(int $index): void
    {
        if (count($this->certificates) > 1) {
            unset($this->certificates[$index]);
            $this->certificates = array_values($this->certificates);
        }
    }

    public function toggleCertificates(): void
    {
        $this->includeCertificates = !$this->includeCertificates;

        if ($this->includeCertificates && empty($this->certificates)) {
            $this->addCertificate();
        }
    }
}; ?>

<div class="card shadow-xl border-none border-gray-200" style="border-radius: 1rem;">

    @include('components.alert')

    <h1 class="text-2xl font-semibold text-base-content mt-3 py-2 text-center">Request an Appointment on <span
            class="font-bold">{{ $this->office->name }}</span> Office</h1>

    {{-- Stepper Header --}}
    <div class="px-5 py-2 mt-5">
        <ul class="steps steps-horizontal w-full">
            <li class="step {{ $step >= 1 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Accept Terms</div>
                    <div class="step-description text-sm text-gray-500">Accept Terms and Conditions</div>
                </div>
            </li>
            <li class="step {{ $step >= 2 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Purpose Request</div>
                    <div class="step-description text-sm text-gray-500">State your purpose</div>
                </div>
            </li>
            @if ($this->includeCertificates)
                <li class="step {{ $step >= 3 ? 'step-info' : '' }}">
                    <div class="step-content">
                        <div class="step-title">Certificate Requests</div>
                        <div class="step-description text-sm text-gray-500">Required documents</div>
                    </div>
                </li>
            @endif
            <li class="step {{ $step >= ($this->includeCertificates ? 4 : 3) ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Personal Information</div>
                    <div class="step-description text-sm text-gray-500">Your details</div>
                </div>
            </li>
            <li class="step {{ $step >= ($this->includeCertificates ? 5 : 4) ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Date & Time</div>
                    <div class="step-description text-sm text-gray-500">Schedule</div>
                </div>
            </li>
            <li class="step {{ $step >= ($this->includeCertificates ? 6 : 5) ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Confirmation</div>
                    <div class="step-description text-sm text-gray-500">Review & Submit</div>
                </div>
            </li>
            <li class="step {{ $step >= ($this->includeCertificates ? 7 : 6) ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Appointment Slip</div>
                    <div class="step-description text-sm text-gray-500">Save Your Details</div>
                </div>
            </li>
        </ul>
    </div>

    {{-- Stepper Content --}}
    @if ($step == 1)
        <div class="px-5 py-2 mt-5">
            <div class="flex flex-col gap-4">
                <div>
                    <div class="modal-body">
                        <div class="text-sm space-y-4 max-h-92 overflow-y-auto p-4">
                            <h4 class="font-bold text-xl mb-2">Terms and Conditions</h4>
                            <p>By using this system, you acknowledge that you have read, understood, and agreed to the
                                following Terms and Disclaimer. These terms govern your use of our online document
                                request and appointment scheduling system. Please read them carefully before proceeding.
                            </p>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">1. Scope of Services</h5>
                                <p>This system facilitates the <strong>online request and appointment
                                        scheduling</strong> for official documents. The actual processing, verification,
                                    and release of the requested documents are conducted <strong>solely by the
                                        designated Municipal or Local Civil Registry Office (LCRO)</strong>.</p>
                                <p class="mt-2">Our system <strong>does not provide delivery, mailing, or courier
                                        services</strong>. All requested documents must be <strong>personally claimed by
                                        the document owner or authorized representative</strong> at the designated
                                    office during the appointed schedule.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">2. User Responsibilities</h5>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li>You affirm that all information provided in your request is <strong>true,
                                            complete, and accurate</strong>.</li>
                                    <li>You understand that any errors, omissions, or false declarations may result in
                                        processing delays or cancellation of your request.</li>
                                    <li>You are required to bring <strong>valid government-issued
                                            identification</strong> for verification upon claiming your document.</li>
                                    <li>If you are an authorized representative, you must present an
                                        <strong>authorization letter</strong> and valid IDs of both the requester and
                                        the owner of the document.
                                    </li>
                                </ul>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">3. Payment and Proof of Transaction</h5>
                                <p>All payments must be made accurately to the <strong>official account details provided
                                        by our system</strong>. You are required to upload a <strong>valid and clear
                                        screenshot of your GCash payment receipt</strong> as proof of transaction.</p>
                                <p class="mt-2 text-justify">Please note that we <strong>do not process refunds</strong>
                                    for incorrect, incomplete, or misdirected payments. Any errors in the amount sent or
                                    transfers made to the wrong account are the sole responsibility of the sender.
                                    Payments will only be considered valid upon successful verification of the uploaded
                                    receipt.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">4. Document Processing and Release</h5>
                                <p>The processing of requested documents is performed by the authorized municipal
                                    offices. Processing time may vary depending on the completeness of your requirements
                                    and the verification procedures of the issuing office.</p>
                                <p class="mt-2">You will be notified through the system or official communication
                                    channels once your document is ready for pickup. Documents not claimed within the
                                    prescribed period may be subject to cancellation or revalidation, depending on
                                    office policy.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">5. Disclaimer of Liability</h5>
                                <p>While we exercise reasonable care in maintaining this system, we make no warranty
                                    that its operation will be uninterrupted, error-free, or secure at all times. We
                                    shall not be held liable for any losses, damages, or inconveniences caused by:</p>
                                <ul class="list-disc pl-6 mt-2 space-y-1">
                                    <li>User's failure to provide accurate or complete information;</li>
                                    <li>Improper or failed payment transactions;</li>
                                    <li>System downtime, network issues, or other unforeseen technical problems;</li>
                                    <li>Delays, errors, or decisions made by the municipal office during document
                                        processing.</li>
                                </ul>
                                <p class="mt-2">The system serves only as an <strong>online facilitation and
                                        appointment tool</strong>. The issuing government office retains full authority
                                    and responsibility over the processing and release of all official documents.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">6. Data Privacy and Confidentiality</h5>
                                <p>All personal information collected through this system is handled in accordance with
                                    the <strong>Data Privacy Act of 2012 (RA 10173)</strong> and our <a
                                        href="/privacy-policy" class="text-primary hover:underline">Data Privacy
                                        Notice</a>. We ensure that your personal data is used only for legitimate
                                    purposes and is protected against unauthorized access or disclosure.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">7. Policy Updates</h5>
                                <p>We reserve the right to update or modify these Terms and Disclaimer at any time
                                    without prior notice. Any changes will take effect immediately upon posting to this
                                    page. Continued use of the system constitutes acceptance of the revised terms.</p>
                            </div>
                            <br />
                            <br />
                            <br />
                            <br />
                            <h4 class="font-bold text-xl mb-2">Data Privacy Notice</h4>
                            <p>We respect your right to privacy and are committed to protecting the personal information
                                you provide through this system. This notice explains how we collect, use, store, and
                                secure your data in compliance with the <strong>Data Privacy Act of 2012 (Republic Act
                                    No. 10173)</strong> and its Implementing Rules and Regulations (IRR).</p>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">1. Purpose of Data Collection</h5>
                                <p>This system collects and processes personal information solely for legitimate
                                    purposes related to the <strong>online request, scheduling, and processing of
                                        official documents</strong>.</p>
                                <ul class="list-disc pl-6 mt-2 space-y-1">
                                    <li>Verify your identity and eligibility to request official documents;</li>
                                    <li>Schedule appointments and manage request records;</li>
                                    <li>Process and confirm payments;</li>
                                    <li>Notify you of request or appointment updates; and</li>
                                    <li>Comply with legal, auditing, and administrative requirements.</li>
                                </ul>
                                <p class="mt-2">We <strong>do not engage in courier or delivery services</strong>. All
                                    requested documents must be <strong>personally claimed at the designated
                                        office</strong> upon presentation of a valid government-issued ID and proof of
                                    payment.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">2. Information We Collect</h5>
                                <ul class="list-disc pl-6 space-y-1">
                                    <li><strong>Personal information:</strong> Full name, date of birth, contact number,
                                        email address, and other data required for document processing;</li>
                                    <li><strong>Supporting documents:</strong> Valid identification cards and payment
                                        receipts uploaded as proof of transaction;</li>
                                    <li><strong>Technical information:</strong> IP address, browser type, access time,
                                        and system logs for security and verification purposes.</li>
                                </ul>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">3. Data Usage and Processing</h5>
                                <p>Your personal information shall be used <strong>only for official and lawful
                                        purposes</strong>, including verification, appointment confirmation, and record
                                    management. We ensure that your data is processed <strong>fairly, accurately, and
                                        securely</strong>, and is retained <strong>only for as long as
                                        necessary</strong> to fulfill its intended purpose.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">4. Data Storage and Security</h5>
                                <p>All collected information is stored in <strong>secured databases</strong> accessible
                                    only to authorized personnel. We implement organizational, physical, and technical
                                    measures to protect your data against unauthorized access, alteration, disclosure,
                                    or destruction.</p>
                                <p class="mt-2">Uploaded files such as IDs and receipts are <strong>encrypted</strong>
                                    and automatically deleted after completion of the document request or after the
                                    legally required retention period.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">5. Data Sharing and Disclosure</h5>
                                <p>Your information will <strong>not be shared</strong> with any third party outside of
                                    our organization unless:</p>
                                <ul class="list-disc pl-6 mt-2 space-y-1">
                                    <li>Required by law, court order, or government regulation;</li>
                                    <li>Necessary to comply with legal obligations; or</li>
                                    <li>Authorized by you in writing.</li>
                                </ul>
                                <p class="mt-2">We do <strong>not sell, rent, or trade</strong> personal information
                                    to any external entity.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">6. Your Rights Under the Data Privacy Act
                                </h5>
                                <p>As a data subject, you have the following rights under Republic Act No. 10173:</p>
                                <ul class="list-disc pl-6 mt-2 space-y-1">
                                    <li><strong>Right to be informed</strong> – to know how your personal data is
                                        collected, processed, and stored;</li>
                                    <li><strong>Right to access</strong> – to request a copy of your personal data in
                                        our records;</li>
                                    <li><strong>Right to rectification</strong> – to correct any inaccurate or outdated
                                        information;</li>
                                    <li><strong>Right to erasure or blocking</strong> – to request deletion or blocking
                                        of data that is no longer necessary;</li>
                                    <li><strong>Right to object</strong> – to refuse processing of your data under
                                        certain conditions; and</li>
                                    <li><strong>Right to file a complaint</strong> – to the <strong>National Privacy
                                            Commission (NPC)</strong> in case of any privacy violation.</li>
                                </ul>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">7. Data Retention Policy</h5>
                                <p>Personal information shall be retained only for as long as necessary to complete the
                                    service or as required by applicable laws and government regulations. After the
                                    retention period, all personal data shall be <strong>securely disposed of or
                                        anonymized</strong> to prevent unauthorized access.</p>
                            </div>

                            <div>
                                <h5 class="font-semibold text-base mt-4 mb-2">Contact Information</h5>
                                <p>For concerns or clarifications regarding these terms, you may contact:</p>
                                <div class="bg-base-200 p-3 rounded-lg mt-2 text-sm">
                                    <p><strong>Support Team</strong></p>
                                    <p>📧 <a href="mailto:santotomasdavnor@gov.ph"
                                            class="text-primary hover:underline">santotomasdavnor@gov.ph</a></p>
                                    <p>📞 +6392-232-2332</p>
                                    <p>🏢 santotomasdavnor@gov.ph</p>
                                </div>
                            </div>

                            <p class="italic text-xs text-gray-500 mt-4">Last Updated: October 2025</p>
                        </div>
                    </div>


                    <div class="flex justify-center mt-6">
                        <label for="termsAccepted"
                            class="flex items-center justify-center gap-2 cursor-pointer text-sm sm:text-base">
                            <input type="checkbox" wire:model.live="termsAccepted" id="termsAccepted"
                                class="checkbox checkbox-primary w-5 h-5 shrink-0" />
                            <span class="text-lg font-medium">
                                I accept the
                                <a href="/terms-and-disclaimer" class="text-blue-500 hover:underline ml-1">
                                    Terms and Conditions
                                </a>
                            </span>
                        </label>
                    </div>



                    <footer class="my-6 flex justify-end gap-2">
                        <button class="flux-btn flux-btn-primary" wire:click="nextStep"
                            @if (!$termsAccepted) disabled @endif>Next</button>
                    </footer>
                </div>
            </div>
        </div>
    @elseif($step == 2)
        @include('livewire.appointments.components.appointment-steps.step2')
    @elseif($step == 3 && $this->includeCertificates)
        @include('livewire.appointments.components.appointment-steps.certificates-step')
    @elseif($step == 3 && !$this->includeCertificates)
        @include('livewire.appointments.components.appointment-steps.step3')
    @elseif($step == 4 && $this->includeCertificates)
        @include('livewire.appointments.components.appointment-steps.step3')
    @elseif($step == 4 && !$this->includeCertificates)
        @include('livewire.appointments.components.appointment-steps.step4')
    @elseif($step == 5 && $this->includeCertificates)
        @include('livewire.appointments.components.appointment-steps.step4')
    @elseif($step == 5 && !$this->includeCertificates)
        @include('livewire.appointments.components.appointment-steps.step6')
    @elseif($step == 6 && $this->includeCertificates)
        @include('livewire.appointments.components.appointment-steps.step6')
    @elseif($step == 6 && !$this->includeCertificates)
        @include('livewire.appointments.components.appointment-steps.step7')
    @elseif($step == 7 && $this->includeCertificates)
        @include('livewire.appointments.components.appointment-steps.step7')
    @endif
</div>
