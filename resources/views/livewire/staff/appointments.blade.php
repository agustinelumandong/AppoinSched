<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\Appointments;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Livewire\Attributes\On;
use App\Models\Offices;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public ?string $selectedDate = null;
    public ?string $selectedTime = null;
    public string $notes = '';
    public ?int $selectedOfficeId = null;

    public ?Appointments $appointment = null;
    public $user = null;
    public $office = null;
    public $service = null;
    public bool $showAppointmentModal = false;

    public function mount(): void
    {
        $this->resetAppointmentData();

        // Check if user has staff role
        if (
            !auth()
                ->user()
                ->hasAnyRole(['MCR-staff', 'MTO-staff', 'BPLS-staff', 'admin', 'super-admin'])
        ) {
            abort(403, 'Unauthorized access');
        }
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

            // Check if staff is assigned to this appointment's office
            if (!auth()->user()->isAssignedToOffice($appointment->office_id)) {
                session()->flash('error', 'You are not authorized to edit this appointment');
                return;
            }

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

        // Check if staff is assigned to this appointment's office
        if (!auth()->user()->isAssignedToOffice($this->appointment->office_id)) {
            session()->flash('error', 'You are not authorized to update this appointment');
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
        $appointment = Appointments::with(['user', 'office', 'service'])->findOrFail($id);

        // Check if staff is assigned to this appointment's office
        if (!auth()->user()->isAssignedToOffice($appointment->office_id)) {
            session()->flash('error', 'You are not authorized to view this appointment');
            return;
        }

        $this->appointment = $appointment;
        $this->user = $appointment->user;
        $this->office = $appointment->office;
        $this->service = $appointment->service;
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

    public function getOfficeIdForStaff(): ?int
    {
        if (auth()->user()->hasRole('MCR-staff')) {
            return Offices::where('slug', 'municipal-civil-registrar')->value('id');
        }
        if (auth()->user()->hasRole('MTO-staff')) {
            return Offices::where('slug', 'municipal-treasurers-office')->value('id');
        }
        if (auth()->user()->hasRole('BPLS-staff')) {
            return Offices::where('slug', 'business-permits-and-licensing-section')->value('id');
        }
        return null;
    }

    public function with(): array
    {
        $query = Appointments::with(['user', 'office', 'service'])
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
            ->when($this->selectedOfficeId, function ($query) {
                $query->where('office_id', $this->selectedOfficeId);
            });

        // Filter by staff's assigned office if not admin/super-admin
        if (
            !auth()
                ->user()
                ->hasAnyRole(['admin', 'super-admin'])
        ) {
            $officeId = $this->getOfficeIdForStaff();
            if ($officeId) {
                $query->where('office_id', $officeId);
            }
        }

        return [
            'appointments' => $query->latest()->paginate(10),
            'offices' => Offices::all(),
        ];
    }
}; ?>

<div>


    <!-- Flash Messages -->
    @include('components.alert')

    <!-- Header -->
    <div class="flux-card p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manage Appointments</h1>
                <p class="text-gray-600 mt-1">View and manage appointments for your assigned offices</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Assigned Offices:</span>
                <div class="flex flex-wrap gap-1">


                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ auth()->user()->getAssignedOffice()->name ?? 'No assigned office' }}
                    </span>


                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="flux-card p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="search" wire:model.live="search" class="form-control"
                    placeholder="Search by status, notes, or names...">
            </div>
            <div class="flex gap-2">
                <button wire:click="searchs" class="btn btn-primary">
                    <i class="bi bi-search me-2"></i>Search
                </button>
            </div>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="flux-card ">
        <div class="overflow-x-auto">
            <table class="table flux-table w-full">
                <thead>
                    <tr> 
                        <th>Reference Number</th>
                        <th>Client</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Date & Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->reference_number }}</td>
                            <td>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div
                                            class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ $appointment->user->initials() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $appointment->user->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $appointment->user->email }}
                                        </div>
                                    </div>
                                </div>  
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">{{ $appointment->service->title ?? '' }}</div>
                            </td>
                            <td>
                                <span
                                    class="flux-badge flux-badge-{{ match ($appointment->status) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'completed' => 'success',
                                        default => 'light',
                                    } }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">
                                    {{  Carbon::parse($appointment->booking_date)->format('M d, Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{  Carbon::parse($appointment->booking_time)->format('h:i A') }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    
                                    <button wire:click="openShowAppointmentModal({{ $appointment->id }})"
                                        class="flux-btn flux-btn-outline btn-sm">
                                        <i class="bi bi-eye me-1"></i>View
                                    </button>
                                    <button wire:click="openEditAppointmentModal({{ $appointment->id }})"
                                        class="flux-btn flux-btn-primary flux-btn-outline btn-sm">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </button>
                                </div> 
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8">
                                <div class="text-gray-500">
                                    <i class="bi bi-file-earmark-text text-4xl mb-2"></i>
                                    <p>No appointments found for your assigned offices.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="mt-6">
            {{ $appointments->links() }}
        </div>
    </div>

    @include('livewire.appointments.components.modal.edit-appointments-modal')
    @include('livewire.appointments.components.modal.show-appointments-modal')
</div>
