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
use App\Models\Services;
use App\Models\Staff;
use App\Models\AppointmentDetails;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema as FacadesSchema;
use Illuminate\Support\Str;
use App\Models\DocumentRequest;
use App\Notifications\RequestEventNotification;
use App\Enums\RequestNotificationEvent;
use App\Notifications\AdminEventNotification;
use App\Enums\AdminNotificationEvent;
use App\Services\PhilippineLocationsService;

new #[Title('Appointment')] class extends Component {
    public int $step = 1;
    public string $to_whom = 'myself';
    public string $purpose = 'consultation';
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';

    public string $message = '';
    public ?int $appointmentId = null;
    public bool $isLoading = false;
    public ?string $reference_number = null;

    public bool $editPersonDetails = false;

    public ?int $id = null;

    public ?string $selectedDate = null;
    public ?string $selectedTime = null;

    public Offices $office;
    public Services $service;
    public User $staff;

    public ?Appointments $appointment = null;

    public string $address = '';

    public string $street = '';
    public string $zip_code = '';

    public string $region = '';
    public string $province = '';
    public string $city = '';
    public string $barangay = '';

    public array $regions = [];
    public array $provinces = [];
    public array $cities = [];
    public array $barangays = [];


    public function mount(Offices $office, Services $service, PhilippineLocationsService $locations, ?string $reference_number = null): void
    {
        $this->office = $office;
        $this->service = $service;
        $this->id = auth()->user()->id;
        $this->editPersonDetails = false;
        // Set the reference_number if provided
        if ($reference_number) {
            $this->reference_number = $reference_number;
        }

        // Pre-populate dependent dropdowns if values exist
        if ($this->region) {
            $this->provinces = $locations->getProvinces($this->region);
        }
        if ($this->region && $this->province) {
            $this->cities = $locations->getMunicipalities($this->region, $this->province);
        }
        if ($this->region && $this->province && $this->city) {
            $this->barangays = $locations->getBarangays($this->region, $this->province, $this->city);
        }

        $this->regions = $locations->getRegions();

        // Check if user has complete profile
        if (!auth()->user()->hasCompleteProfile()) {
            session()->flash('warning', 'Please complete your profile information before making an appointment.');
            $this->redirect(route('userinfo'));
        }

        // Only populate user data if "myself" is selected
        if ($this->to_whom === 'myself') {
            $this->populateUserData();
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

    public function updatedToWhom(): void
    {
        if ($this->to_whom === 'myself') {
            $this->populateUserData();
        } else {
            $this->clearPersonalData();
        }
    }

    public function clearPersonalData(): void
    {
        // Clear all personal information fields
        $this->first_name = '';
        $this->last_name = '';
        $this->middle_name = '';
        $this->email = '';
        $this->phone = '';
        $this->address = '';
        $this->region = '';
        $this->province = '';
        $this->city = '';
        $this->barangay = '';
        $this->street = '';
        $this->zip_code = '';
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
            case 1:
                $this->isLoading = true;
                $this->validate([
                    'to_whom' => 'required',
                ]);
                break;
            case 2:
                $this->isLoading = true;
                $this->validate([
                    'purpose' => 'required',
                ]);
                break;
            case 3:
                $this->isLoading = true;
                $this->validate([
                    'first_name' => 'string|required',
                    'last_name' => 'string|required',
                    'middle_name' => 'string|nullable',
                    'email' => 'string|required|email',
                    'phone' => 'string|required|max:25|regex:/^[\+]?[0-9\s\-\(\)]+$/',
                    'address' => 'string|required',
                    'region' => 'string|required',
                    'province' => 'string|required',
                    'city' => 'string|required',
                    'barangay' => 'string|required',
                    'street' => 'string|required',
                    'zip_code' => 'string|required|max:10|regex:/^[0-9\-]+$/',
                ]);
                break;
            case 4:
                $this->isLoading = true;
                $this->validate([
                    'selectedDate' => 'required|date|after_or_equal:today',
                    'selectedTime' => 'required|string',
                ]);
                break;
            case 5:
                $this->isLoading = true;
                // No additional validation needed - just confirmation step
                break;
            case 6:
                $this->isLoading = true;
                break;
        }
        $this->step++;
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
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
                session()->flash('error', 'This time slot is already booked. Please select a different time.');
                $this->isLoading = false;
                return;
            }

            // Validate the data
            $validated = $this->validate([
                'to_whom' => 'required|string',
                'purpose' => 'required|string',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:25|regex:/^[\+]?[0-9\s\-\(\)]+$/',
                'address' => 'required|string|max:255',
                'region' => 'required|string|max:100',
                'province' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'barangay' => 'required|string|max:100',
                'street' => 'required|string|max:100',
                'zip_code' => 'required|string|max:10|regex:/^[0-9\-]+$/',
                'selectedDate' => 'required|date|after_or_equal:today',
                'selectedTime' => 'required|string',
            ]);

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
                'service_id' => $this->service->id,
                'booking_date' => $this->selectedDate,
                'booking_time' => $this->selectedTime,
                'to_whom' => $this->to_whom,
                'purpose' => $this->purpose,
                'notes' => "Appointment for {$this->to_whom} - {$this->purpose}",
                'reference_number' => $reference_number,
                'status' => 'on-going',
            ]);

            // Only create details if appointment was created and the details table has the correct FK
            if ($appointment && FacadesSchema::hasColumn('appointment_details', 'appointment_id')) {
                AppointmentDetails::create([
                    'appointment_id' => $appointment->id,
                    'request_for' => $this->to_whom,
                    'first_name' => $this->first_name,
                    'middle_name' => $this->middle_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'region' => $this->region,
                    'province' => $this->province,
                    'city' => $this->city,
                    'barangay' => $this->barangay,
                    'street' => $this->street,
                    'zip_code' => $this->zip_code,
                    'purpose' => $this->purpose,
                    'notes' => $appointment->notes,
                ]);
            }


            if (str_starts_with($reference_number, 'DOC')) {
                $request = DocumentRequest::where('reference_number', $reference_number)->first();
                if ($request) {
                    $request->update([
                        'payment_status' => 'walk-in',
                        'payment_reference' => $reference_number,
                    ]);
                }
            }

            DB::commit();

            // Send notification to user
            auth()->user()->notify(new RequestEventNotification(
                RequestNotificationEvent::AppointmentScheduled,
                [
                    'date' => $this->selectedDate,
                    'time' => $this->selectedTime,
                    'location' => $this->office->name,
                    'service' => $this->service->title,
                    'reference_no' => $reference_number
                ]
            ));

            // Send notification to staff
            $staffs = User::getStaffsByOfficeId($this->office->id);
            if ($staffs->count() > 0) {
                foreach ($staffs as $staff) {
                    $staff->notify(new AdminEventNotification(
                        AdminNotificationEvent::UserAppointmentScheduled,
                        [
                            'reference_no' => $reference_number,
                            'date' => $this->selectedDate,
                            'time' => $this->selectedTime,
                            'location' => $this->office->name,
                            'service' => $this->service->title,
                            'user' => auth()->user()->name,
                            'user_email' => auth()->user()->email,
                        ]
                    ));
                }
            }
            // Send appointment slip email
            auth()->user()->notify(new \App\Notifications\AppointmentSlipNotification($appointment));

            $cacheKey = "time_slots_{$this->office->id}_{$this->service->id}_{$this->selectedDate}";
            Cache::forget($cacheKey);

            if ($appointment) {
                Log::info('Appointment created successfully', ['appointment_id' => $appointment->id]);

                // Store the reference number for use in step 7
                $this->reference_number = $reference_number;

                // Move to step 7 instead of resetting
                $this->step = 7;

                // Clear cache and update UI
                $cacheKey = "time_slots_{$this->office->id}_{$this->service->id}_{$this->selectedDate}";
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
                    'service_id' => $this->service->id ?? null,
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
                'service_id' => $this->service->id,
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
            // Convert the selected time to 24-hour format for comparison
            $timeToCheck = Carbon::parse($this->selectedTime)->format('H:i');

            $conflict = Appointments::where('booking_date', $this->selectedDate)
                ->where(function ($query) use ($timeToCheck) {
                    $query->whereRaw("TIME_FORMAT(booking_time, '%H:%i') = ?", [$timeToCheck]);
                })
                ->where('office_id', $this->office->id)
                ->where('service_id', $this->service->id)
                ->exists();

            Log::info('Checking for conflicts in stepper:', [
                'date' => $this->selectedDate,
                'time' => $this->selectedTime,
                'time_to_check' => $timeToCheck,
                'office_id' => $this->office->id,
                'service_id' => $this->service->id,
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

    public function updatedRegion(PhilippineLocationsService $locations)
    {
        $this->provinces = $locations->getProvinces($this->region);
        $this->province = '';
        $this->cities = [];
        $this->city = '';
        $this->barangays = [];
        $this->barangay = '';
    }

    public function updatedProvince(PhilippineLocationsService $locations)
    {
        $this->cities = $locations->getMunicipalities($this->region, $this->province);
        $this->city = '';
        $this->barangays = [];
        $this->barangay = '';
    }

    public function updatedCity(PhilippineLocationsService $locations)
    {
        $this->barangays = $locations->getBarangays($this->region, $this->province, $this->city);
        $this->barangay = '';
    }
}; ?>

<div class="card shadow-xl border-none border-gray-200" style="border-radius: 1rem;">



    @include('components.alert')

    <h1 class="text-2xl font-semibold text-base-content mt-3 py-2 text-center">Request an Appointment</h1>

    {{-- Stepper Header --}}
    <div class="px-5 py-2 mt-5">
        <ul class="steps steps-horizontal w-full">
            <li class="step {{ $step >= 1 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">To Whom?</div>
                    <div class="step-description text-sm text-gray-500">For Yourself or Someone Else?</div>
                </div>
            </li>
            <li class="step {{ $step >= 2 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Purpose Request</div>
                    <div class="step-description text-sm text-gray-500">State your purpose</div>
                </div>
            </li>
            <li class="step {{ $step >= 3 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Personal Information</div>
                    <div class="step-description text-sm text-gray-500">Your/Someone details</div>
                </div>
            </li>
            <li class="step {{ $step >= 4 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Date & Time</div>
                    <div class="step-description text-sm text-gray-500">Schedule</div>
                </div>
            </li>
            <li class="step {{ $step >= 5 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Contact Information</div>
                    <div class="step-description text-sm text-gray-500">How to reach you</div>
                </div>
            </li>
            <li class="step {{ $step >= 6 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Confirmation</div>
                    <div class="step-description text-sm text-gray-500">Review & Submit</div>
                </div>
            </li>
            <li class="step {{ $step >= 7 ? 'step-info' : '' }}">
                <div class="step-content">
                    <div class="step-title">Appointment Slip</div>
                    <div class="step-description text-sm text-gray-500">Save Your Details</div>
                </div>
            </li>
        </ul>
    </div>

    {{-- Stepper Content --}}
    @if ($step == 1)
        @include('livewire.appointments.components.appointment-steps.step1')
    @elseif($step == 2)
        @include('livewire.appointments.components.appointment-steps.step2')
    @elseif($step == 3)
        @include('livewire.appointments.components.appointment-steps.step3')
    @elseif($step == 4)
        @include('livewire.appointments.components.appointment-steps.step4')
    @elseif($step == 5)
        @include('livewire.appointments.components.appointment-steps.step5')
    @elseif($step == 6)
        @include('livewire.appointments.components.appointment-steps.step6')
    @elseif($step == 7)
        @include('livewire.appointments.components.appointment-steps.step7')
    @endif
</div>