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
                'certificate_type' => '',
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
                if (!$this->office->slug === 'municipal-treasurers-office') {
                    if (!$this->includeCertificates) {
                        $this->step++; // Skip to next step
                    }
                }
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
                        'phone' => 'required|numeric|digits_between:7,25',
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
                'phone' => 'required|numeric|digits_between:7,25',
                'metadata' => 'nullable|array',
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
                                'certificate_code' => $certificateTypeCodes[$certificate['certificate_type']] . ':' . $toWhom[$certificate['relationship']] . ' - ' . $this->first_name . ' ' . $this->last_name,
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
                if (!$this->office->slug === 'municipal-treasurers-office') {
                    $this->step = 7;
                } else {
                    $this->step = 6;
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
            'certificate_type' => '',
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

    <h1 class="text-2xl font-semibold text-base-content mt-3 py-2 text-center">Request an Appointment on <span class="font-bold">{{ $this->office->name }}</span> Office</h1>

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
            @if($this->includeCertificates)
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
                <div class="flex justify-center flex-col text-center">
                    <div class="header mb-4 ">
                        <h3 class="text-xl font-semibold text-base-content">Terms and Conditions</h3>
                        <div class="flex justify-center items-center gap-2 text-sm text-base-content/70 ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Please read and accept the terms and conditions.</span>
                        </div>
                    </div>

                    <div class="terms-conditions-content mb-6">
                        <p class="mb-4">By submitting this appointment request, you agree to the following terms and
                            conditions:</p>
                        <ul class="list-disc list-inside space-y-2 text-sm text-base-content/80">
                            <li>You are requesting an appointment for yourself.</li>
                            <li>You understand that the appointment is subject to availability and confirmation.</li>
                            <li>You agree to provide accurate and truthful information.</li>
                            <li>You acknowledge that the appointment details will be sent to the relevant staff.</li>
                            <li>You agree to the terms of service and privacy policy of this platform.</li>
                            <li>You understand that appointment times may be subject to change based on staff
                                availability.
                            </li>
                            <li>You agree to arrive on time for your scheduled appointment.</li>
                            <li>You acknowledge that failure to show up may affect future appointment scheduling.</li>
                        </ul>
                    </div>

                    <div class=" mb-6">
                        <label for="termsAccepted" class="flex items-center cursor-pointer">
                            <input type="checkbox" id="termsAccepted" wire:model.live="termsAccepted"
                                class="checkbox checkbox-sm">
                            I accept the <a href="TERMS" class="text-blue-500">Terms and Conditions</a>
                        </label>
                    </div>

                </div>
                <footer class="my-6 flex justify-end gap-2" wire:loading.remove>
                    <button class="flux-btn flux-btn-primary" wire:click="nextStep" @if (!$termsAccepted) disabled
                    @endif>Next</button>
                </footer>
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