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
use Livewire\Attributes\Title;

new #[Title('Appointment')]
    class extends Component {
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

    public ?int $id = 12;

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

    public function submitAppointment()
    {
        try {
            // Check for scheduling conflicts
            $conflict = $this->checkForConflicts();
            if ($conflict) {
                Log::info('Conflict found, appointment not saved');
                session()->flash('error', 'This time slot is already booked. Please select a different time.');
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

            Log::info('Validation passed, attempting to create appointment', $validated);

            // Create the appointment
            $appointment = Appointments::create([
                'user_id' => $this->id,
                'office_id' => $this->office->id,
                'service_id' => $this->service->id,
                'staff_id' => $this->staff->id,
                'booking_date' => $this->selectedDate,
                'booking_time' => $this->selectedTime,
                'status' => 'pending',
                'to_whom' => $this->to_whom,
                'purpose' => $this->purpose,
                'client_first_name' => $this->first_name,
                'client_last_name' => $this->last_name,
                'client_middle_name' => $this->middle_name,
                'client_email' => $this->email,
                'client_phone' => $this->phone,
                'client_address' => $this->address,
                'client_city' => $this->city,
                'client_state' => $this->state,
                'client_zip_code' => $this->zip_code,
                'notes' => "Appointment for {$this->to_whom} - {$this->purpose}",
            ]);

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
            Log::error('Exception during appointment creation: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => [
                    'user_id' => $this->id,
                    'office_id' => $this->office->id,
                    'service_id' => $this->service->id,
                    'staff_id' => $this->staff->id,
                ]
            ]);
            session()->flash('error', 'Error: ' . $e->getMessage());
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

    <link rel="stylesheet" href="{{ asset('css/fluxUI.css') }}">

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success mx-5 mt-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-error mx-5 mt-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

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
        <div class="px-5 py-2 mt-5">
            <div class="flex flex-col gap-4">
                <div>
                    <div class="header mb-4">
                        <h3 class="text-xl font-semibold text-base-content">For Yourself or Someone Else?</h3>
                        <div class="flex items-center gap-2 text-sm text-base-content/70">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Please select who this appointment is for</span>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div wire:loading.delay class="text-center ">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                        <p class="text-gray-600">Loading...</p>
                    </div>

                    <div class="flex flex-col gap-2 w-full" wire:loading.remove>
                        <input type="radio" id="myself" name="to_whom" value="myself" wire:model.live="to_whom" hidden />
                        <label for="myself"
                            class="flux-input-primary flux-btn cursor-pointer {{ $to_whom === 'myself' ? 'flux-btn-active-primary' : '' }} p-2">Myself</label>
                        <input type="radio" id="someone_else" name="to_whom" value="someone_else" wire:model.live="to_whom"
                            hidden />
                        <label for="someone_else"
                            class="flux-input-primary flux-btn cursor-pointer {{ $to_whom === 'someone_else' ? 'flux-btn-active-primary' : '' }} p-2">Someone
                            Else</label>
                    </div>


                    <footer class="my-6 flex justify-end gap-2">
                        {{-- <button class="btn btn-ghost" wire:click="previousStep">Previous</button> --}}
                        <button class="btn btn-primary" wire:click="nextStep">Next</button>
                    </footer>
                </div>
            </div>
        </div>
    @elseif($step == 2)
        <div class="px-5 py-2 mt-5">
            <div class="flex flex-col gap-4">
                <div>
                    <div class="header mb-4">
                        <h3 class="text-xl font-semibold text-base-content">State your purpose</h3>
                        <div class="flex items-center gap-2 text-sm text-base-content/70">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Please select the purpose of your appointment</span>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div wire:loading.delay class="text-center ">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                        <p class="text-gray-600">Loading...</p>
                    </div>

                    <div class="flex flex-col gap-2 w-full" wire:loading.remove>
                        <input type="radio" id="consultation" name="purpose" value="consultation" wire:model.live="purpose"
                            hidden />
                        <label for="consultation"
                            class="flux-input-primary flux-btn cursor-pointer {{ $purpose === 'consultation' ? 'flux-btn-active-primary' : '' }} p-2">Consultation</label>
                        <input type="radio" id="follow_up" name="purpose" value="follow_up" wire:model.live="purpose"
                            hidden />
                        <label for="follow_up"
                            class="flux-input-primary  flux-btn cursor-pointer {{ $purpose === 'follow_up' ? 'flux-btn-active-primary' : '' }} p-2">Follow
                            Up</label>
                    </div>


                    <footer class="my-6 flex justify-end gap-2">
                        <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
                        <button class="btn btn-primary" wire:click="nextStep">Next</button>
                    </footer>
                </div>
            </div>
        </div>
    @elseif($step == 3)
        <div class="px-5 py-2 mt-5">
            <div class="flex flex-col gap-4">
                <div>
                    <div class="header mb-4">
                        <h3 class="text-xl font-semibold text-base-content">Your/Someone details</h3>
                        <div class="flex items-center gap-2 text-sm text-base-content/70">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Please provide your/someone details</span>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div wire:loading.delay class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2">
                        </div>
                        <p class="text-gray-600">Loading...</p>
                    </div>

                    <div class="flex flex-col gap-2 w-full" wire:loading.remove>

                        <div class="flex flex-row gap-2 w-full">
                            <div class="w-full">
                                <input type="text" placeholder="Last Name" class="flux-form-control"
                                    wire:model="last_name" />
                                @error('last_name') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>


                            <div class="w-full">
                                <input type="text" placeholder="First Name" class="flux-form-control"
                                    wire:model="first_name" />
                                @error('first_name') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="w-full">
                                <input type="text" placeholder="Middle Name" class="flux-form-control"
                                    wire:model="middle_name" />
                                @error('middle_name') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex flex-row gap-2 w-full">
                            <div class=" w-full">
                                <input type="email" placeholder="Email" class="flux-form-control" wire:model="email" />
                                @error('email')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="w-full">
                                <input type="tel" placeholder="Phone" class="flux-form-control" wire:model="phone" />
                                @error('phone')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="w-full">
                            <input type="text" placeholder="Address" class="flux-form-control" wire:model="address" />
                            @error('address')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="w-full">
                            <input type="text" placeholder="City" class="flux-form-control" wire:model="city" />
                            @error('city')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="w-full">
                            <input type="text" placeholder="State" class="flux-form-control" wire:model="state" />
                            @error('state')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="w-full">
                            <input type="text" placeholder="Zip Code" class="flux-form-control" wire:model="zip_code" />
                            @error('zip_code')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>


                        <footer class="my-6 flex justify-end gap-2">
                            <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
                            <button class="btn btn-primary" wire:click="nextStep" wire:loading.disable>Next</button>

                        </footer>
                    </div>
                </div>
            </div>
    @elseif($step == 4)
            <div class="px-5 py-2 mt-5" wire:key="step-4-container">
                <div class="flex flex-col gap-4">
                    <div>
                        {{-- Header --}}
                        <div class="header mb-4">
                            <h3 class="text-xl font-semibold text-base-content">Schedule</h3>
                            <div class="flex items-center gap-2 text-sm text-base-content/70">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Please select a date and time for your appointment</span>
                            </div>
                        </div>

                        {{-- Slot Picker --}}
                        <div class="mb-4" wire:key="slot-picker-wrapper">
                            <label class="form-label fw-semibold">Select Date and Time</label>
                            <div id=" slot-picker-container-stepper" wire:ignore>
                                <livewire:slot-picker :office-id="$office->id" :service-id="$service->id"
                                    :staff-id="$staff->id" :pre-selected-date="$selectedDate"
                                    :pre-selected-time="$selectedTime"
                                    wire:key="stepper-slot-picker-{{ $office->id }}-{{ $service->id }}-{{ $staff->id }}" />
                            </div>
                        </div>

                        {{-- DEBUG: current selection --}}
                        {{-- @if ($selectedDate && $selectedTime)
                        <div class="mb-4" wire:key="selection-display">
                            <div class="alert alert-info flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <strong>Selected:</strong>
                                    {{ Carbon::parse($selectedDate)->format('F j, Y') }} at
                                    {{ Carbon::parse($selectedTime)->format('g:i A') }}
                                </div>
                            </div>
                        </div>
                        @endif --}}

                        {{-- Footer --}}
                        <footer class="my-6 flex justify-end gap-2">
                            <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
                            <button class="btn btn-primary" wire:click="nextStep" {{ !$selectedDate || !$selectedTime ? 'disabled' : '' }}>
                                Next
                            </button>

                        </footer>
                    </div>
                </div>
            </div>
        @elseif($step == 5)
            <div class="px-5 py-2 mt-5">
                <div class="flex flex-col gap-4">
                    <div>
                        <div class="header mb-4">
                            <h3 class="text-xl font-semibold text-base-content">Contact Information</h3>
                            <div class="flex items-center gap-2 text-sm text-base-content/70">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Please confirm your contact information</span>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div wire:loading.delay class="text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                            <p class="text-gray-600">Loading...</p>
                        </div>

                        <div class="flex flex-col gap-2 w-1/2" wire:loading.remove>
                            <p class="text-sm text-base-content/70 mb-2">Please review and confirm your contact details:
                            </p>



                            <div class="bg-base-200 p-4 rounded-lg">
                                <div class=" grid grid-cols-1 gap-2">
                                    <div>
                                        <label class="font-medium text-sm">Email:</label>
                                        <p class="text-base-content">{{ $email }}</p>
                                    </div>
                                    <div>
                                        <label class=" font-medium text-sm">Phone:</label>
                                        <p class="text-base-content">{{ $phone }}</p>
                                    </div>
                                    <div>
                                        <label class=" font-medium text-sm">Address:</label>
                                        <p class="text-base-content">{{ $address }}</p>
                                    </div>
                                    <div>
                                        <label class=" font-medium text-sm">Location:</label>
                                        <p class="text-base-content">{{ $city }}, {{ $state }}
                                            {{ $zip_code }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <p class="text-sm text-base-content/70 mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                We will use this information to contact you about your appointment.
                            </p>
                        </div>

                        <footer class="my-6 flex justify-end gap-2">
                            <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
                            <button class="btn btn-primary" wire:click="nextStep">Next</button>

                        </footer>
                    </div>
                </div>
            </div>
        @elseif($step == 6)
            <div class="px-5 py-2 mt-5">
                <div class="flex flex-col gap-4">
                    <div>
                        <div class="header mb-4">
                            <h3 class="text-xl font-semibold text-base-content">Confirmation</h3>
                            <div class="flex items-center gap-2 text-sm text-base-content/70">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Please review your appointment details</span>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div wire:loading.delay class="text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                            <p class="text-gray-600">Loading...</p>
                        </div>

                        <div class="flex flex-col gap-2 w-1/2 text-sm text-base-content/70" wire:loading.remove>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex flex-col gap-2">
                                    <p class="font-medium">Appointment Details</p>
                                    <p>Who User Id: {{ $id }}</p>
                                    <p>Office Id: {{ $office->id }}</p>
                                    <p>Service Id: {{ $service->id }}</p>
                                    <p>Staff Id: {{ $staff->id }}</p>

                                    <p>To Whom: <span class="text-base-content">{{ $to_whom }}</span></p>
                                    <p>Purpose: <span class="text-base-content">{{ $purpose }}</span></p>
                                    <p>Date: <span class=" text-base-content">{{ $selectedDate }}</span></p>
                                    <p>Time: <span class="text-base-content">{{ $selectedTime }}</span></p>
                                </div>
                                <div class=" flex flex-col gap-2">
                                    <p class="font-medium">Personal Information</p>
                                    <p>Name: <span class="text-base-content">{{ $first_name }}
                                            {{ $last_name }}</span></p>
                                    <p>Email: <span class=" text-base-content">{{ $email }}</span>
                                    </p>
                                    <p>Phone: <span class="text-base-content">{{ $phone }}
                                        </span>
                                    </p>
                                    <p>Address: <span class="text-base-content">{{ $address }}</span></p>
                                    <p>Location: <span class="text-base-content">{{ $city }}, {{ $state }}
                                            {{ $zip_code }}
                                        </span></p>
                                </div>
                            </div>
                        </div>

                        <footer class="my-6 flex justify-end gap-2">
                            <button class="btn btn-ghost" wire:click="previousStep">Previous</button>
                            <button class="btn btn-primary" wire:click="submitAppointment">Submit</button>

                        </footer>
                    </div>
                </div>
            </div>
        @endif
    </div>