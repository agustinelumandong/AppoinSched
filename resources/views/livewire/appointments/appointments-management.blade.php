<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\Appointments;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Livewire\Attributes\On;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public ?string $selectedDate = null;
    public ?string $selectedTime = null;
    public string $notes = '';

    public ?Appointments $appointment = null;
    public $user = null;
    public $office = null;
    public bool $showAppointmentModal = false;

    public function mount(): void
    {
        $this->resetAppointmentData();
    }

    #[On('appointmentUpdated')]
    public function refreshComponent(): void
    {
        $this->resetPage();
    }

    #[On('slot-picker-updated')]
    public function handleSlotUpdate($data): void
    {
        try {
            $this->selectedDate = $data['date'] ?? null;
            $this->selectedTime = $data['time'] ?? null;

            Log::info('Slot picker updated', [
                'date' => $this->selectedDate,
                'time' => $this->selectedTime,
                'appointment_id' => $this->appointment?->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error handling slot update: ' . $e->getMessage());
            session()->flash('error', 'Failed to update date and time selection');
        }
    }

    public function openEditAppointmentModal(int $id): void
    {
        try {
            $appointment = Appointments::findOrFail($id);

            $this->appointment = $appointment;
            $this->selectedDate = $appointment->booking_date?->format('Y-m-d');
            $this->selectedTime = $appointment->booking_time;
            $this->notes = $appointment->notes ?? '';

            $this->dispatch('open-modal-edit-appointment');
        } catch (\Exception $e) {
            Log::error('Error opening edit modal: ' . $e->getMessage());
            session()->flash('error', 'Failed to open edit modal');
        }
    }

    public function updateAppointment(): void
    {
        if (!$this->appointment) {
            session()->flash('error', 'Appointment not found');
            return;
        }

        try {
            $validated = $this->validate([
                'selectedDate' => 'required|date|after_or_equal:today',
                'selectedTime' => 'required|string',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Check for scheduling conflicts
            $conflict = $this->checkForConflicts();
            if ($conflict) {
                session()->flash('error', 'This time slot is already booked');
                return;
            }

            $updateData = [
                'booking_date' => $this->selectedDate,
                'booking_time' => $this->selectedTime,
                'notes' => $this->notes,
            ];

            $updated = $this->appointment->update($updateData);

            if ($updated) {
                $this->resetAppointmentData();
                $this->dispatch('appointmentUpdated');
                $this->dispatch('close-modal-edit-appointment');
                session()->flash('success', 'Appointment updated successfully');
            } else {
                session()->flash('error', 'Failed to update appointment');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = collect($e->errors())->flatten();
            session()->flash('error', 'Validation failed: ' . $errors->first());
        } catch (\Exception $e) {
            Log::error('Appointment update failed: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while updating the appointment');
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
                ->where('office_id', $this->appointment->office_id)
                ->where('id', '!=', $this->appointment->id)
                ->exists();

            Log::info('Checking for conflicts:', [
                'date' => $this->selectedDate,
                'time' => $this->selectedTime,
                'time_to_check' => $timeToCheck,
                'office_id' => $this->appointment->office_id,
                'appointment_id' => $this->appointment->id,
                'has_conflict' => $conflict,
            ]);

            return $conflict;
        } catch (\Exception $e) {
            Log::error('Error checking for conflicts: ' . $e->getMessage(), [
                'date' => $this->selectedDate,
                'time' => $this->selectedTime,
                'appointment_id' => $this->appointment->id,
            ]);
            return true; // Return true to prevent booking in case of error
        }
    }

    public function searchs(): void
    {
        $this->resetPage();
    }

    public function openShowAppointmentModal(int $id): void
    {
        $appointment = Appointments::with(['user', 'office'])->findOrFail($id);

        $this->appointment = $appointment;
        $this->user = $appointment->user;
        $this->office = $appointment->office;
        $this->showAppointmentModal = true;
        $this->dispatch('open-modal-show-appointment');
    }

    private function resetAppointmentData(): void
    {
        $this->appointment = null;
        $this->selectedDate = null;
        $this->selectedTime = null;
        $this->notes = '';
    }

    public function with(): array
    {
        return [
            'appointments' => Appointments::with(['user', 'office'])
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->whereHas('user', function ($userQuery) {
                            $userQuery
                                ->where('first_name', 'like', '%' . $this->search . '%')
                                ->orWhere('last_name', 'like', '%' . $this->search . '%')
                                ->orWhere('reference_number', 'like', '%' . $this->search . '%');
                        });
                    });
                })
                ->latest()
                ->paginate(10),
        ];
    }
}; ?>

<div>


    <!-- Flash Messages -->
    @include('components.alert')

    @include('livewire.appointments.components.header')
    @include('livewire.appointments.components.table')
    @include('livewire.appointments.components.modal.edit-appointments-modal')
    @include('livewire.appointments.components.modal.show-appointments-modal')
</div>