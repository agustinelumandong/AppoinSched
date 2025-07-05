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

new #[Title('Appointment')] class extends Component {
    public int $step = 1;
    public string $to_whom = 'myself';
    public string $purpose = 'consultation';
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $city = '';
    public string $state = '';
    public string $zip_code = '';
    public string $message = '';
    public ?int $appointmentId = null;
    public bool $isLoading = false;

    public ?int $id = null;

    public ?string $selectedDate = null;
    public ?string $selectedTime = null;

    public Offices $office;
    public Services $service;
    public User $staff;

    public ?Appointments $appointment = null;

    public function mount(Offices $office, Services $service, User $staff): void
    {
        $this->office = $office;
        $this->service = $service;
        $this->staff = $staff;
        $this->id = auth()->user()->id;

        // Check if user has complete profile
        if (!auth()->user()->hasCompleteProfile()) {
            session()->flash('warning', 'Please complete your profile information before making an appointment.');
            $this->redirect(route('userinfo'));
        }
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
                    'city' => 'string|required',
                    'state' => 'string|required',
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
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'zip_code' => 'required|string|max:10|regex:/^[0-9\-]+$/',
                'selectedDate' => 'required|date|after_or_equal:today',
                'selectedTime' => 'required|string',
            ]);

            DB::beginTransaction();

            // Create the appointment
            $appointment = Appointments::create([
                'user_id' => $userId,
                'office_id' => $this->office->id,
                'service_id' => $this->service->id,
                'staff_id' => $this->staff->id,
                'booking_date' => $this->selectedDate,
                'booking_time' => $this->selectedTime,
                'status' => 'pending',
                'to_whom' => $this->to_whom,
                'purpose' => $this->purpose,
                'notes' => "Appointment for {$this->to_whom} - {$this->purpose}",
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
                    'city' => $this->city,
                    'state' => $this->state,
                    'zip_code' => $this->zip_code,
                    'purpose' => $this->purpose,
                    'notes' => $appointment->notes,
                ]);
            }

            DB::commit();

            $cacheKey = "time_slots_{$this->office->id}_{$this->staff->id}_{$this->selectedDate}";
            Cache::forget($cacheKey);

            if ($appointment) {
                Log::info('Appointment created successfully', ['appointment_id' => $appointment->id]);
                session()->flash('success', 'Appointment created successfully! We will contact you soon to confirm.');

                // Reset the form
                $this->reset(['step', 'to_whom', 'purpose', 'first_name', 'last_name', 'middle_name', 'email', 'phone', 'address', 'city', 'state', 'zip_code', 'selectedDate', 'selectedTime']);
                $this->step = 1;

                $this->dispatch('refresh-slots');
                $this->isLoading = true;
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
                    'staff_id' => $this->staff->id ?? null,
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
                'staff_id' => $this->staff->id,
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
                ->where('staff_id', $this->staff->id)
                ->whereNotIn('status', ['cancelled', 'no-show'])
                ->exists();

            Log::info('Checking for conflicts in stepper:', [
                'date' => $this->selectedDate,
                'time' => $this->selectedTime,
                'time_to_check' => $timeToCheck,
                'office_id' => $this->office->id,
                'staff_id' => $this->staff->id,
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
    @endif
</div>
