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
            Log::error('Error opening edit appointment modal: ' . $e->getMessage());
            session()->flash('error', 'Failed to open appointment for editing');
        }
    }

    public function updateAppointment(): void
    {
        try {
            if (!$this->appointment) {
                session()->flash('error', 'Appointment not found');
                return;
            }

            // Check for scheduling conflicts
            if ($this->checkForConflicts()) {
                Log::info('Conflict found, appointment not updated');
                session()->flash('error', 'This hour has reached the maximum of 5 appointments. Please select a different time.');
                return;
            }

            // Validate the data
            $this->validate([
                'selectedDate' => 'required|date|after_or_equal:today',
                'selectedTime' => 'required|string',
                'notes' => 'nullable|string|max:500',
            ]);

            $this->appointment->update([
                'booking_date' => $this->selectedDate,
                'booking_time' => $this->selectedTime,
                'notes' => $this->notes,
            ]);

            session()->flash('success', 'Appointment updated successfully');
            $this->dispatch('appointmentUpdated');
            $this->dispatch('close-modal-edit-appointment');
            $this->resetAppointmentData();

            // Clear cache for the updated date
            $cacheKey = "time_slots_{$this->appointment->office_id}_{$this->selectedDate}";
            Cache::forget($cacheKey);

        } catch (\Exception $e) {
            Log::error('Failed to update appointment: ' . $e->getMessage());
            session()->flash('error', 'Failed to update appointment.');
        }
    }

    private function checkForConflicts(): bool
    {
        try {
            // Get hour from selected time for hourly limit check
            $selectedTime = $this->selectedTime;
            $hour = Carbon::parse($selectedTime)->format('H');

            // Maximum appointments allowed per hour
            $hourlyLimit = 5;

            // Count appointments in the same hour
            $hourlyCount = Appointments::where('booking_date', $this->selectedDate)
                ->where('office_id', $this->appointment->office_id)
                ->where('id', '!=', $this->appointment->id)
                ->whereRaw('HOUR(booking_time) = ?', [$hour])
                ->whereIn('status', ['on-going', 'completed'])
                ->count();

            $conflict = $hourlyCount >= $hourlyLimit;

            Log::info('Checking for hourly conflicts in appointment update:', [
                'date' => $this->selectedDate,
                'time' => $selectedTime,
                'hour' => $hour,
                'office_id' => $this->appointment->office_id,
                'hourly_count' => $hourlyCount,
                'hourly_limit' => $hourlyLimit,
                'has_conflict' => $conflict,
            ]);

            return $conflict;
        } catch (\Exception $e) {
            Log::error('Error checking for conflicts: ' . $e->getMessage());
            return true;
        }
    }

    public function searchs(): void
    {
        $this->resetPage();
    }

    public function openShowAppointmentModal(int $id): void
    {
        $appointment = Appointments::with(['user', 'office'])->findOrFail($id);

        // Check if staff is assigned to this appointment's office
        if (!auth()->user()->isAssignedToOffice($appointment->office_id)) {
            session()->flash('error', 'You are not authorized to view this appointment');
            return;
        }

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

    public function completeAppointment(int $id): void
    {
        $appointment = Appointments::findOrFail($id);
        $appointment->status = 'completed';
        $appointment->save();
        session()->flash('success', 'Appointment completed successfully');
    }

    public function cancelAppointment(int $id): void
    {
        $appointment = Appointments::findOrFail($id);
        $appointment->status = 'cancelled';
        $appointment->save();
        session()->flash('success', 'Appointment cancelled successfully');
    }

    public function with(): array
    {
        $query = Appointments::with(['user', 'office'])
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
                    placeholder="Search by reference number, client, or service...">
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
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
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
                                    class="flux-badge flux-badge-{{ $appointment->status == 'completed' ? 'success' : ($appointment->status == 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-sm text-gray-900">
                                    {{ Carbon::parse($appointment->booking_date)->format('M d, Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ Carbon::parse($appointment->booking_time)->format('h:i A') }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    {{-- <button wire:click="openEditAppointmentModal({{ $appointment->id }})"
                                        class="flux-btn flux-btn-primary flux-btn-outline btn-sm">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </button> --}}
                                    <button wire:click="openShowAppointmentModal({{ $appointment->id }})"
                                        class="flux-btn btn-sm flux-btn-primary">
                                        <i class="bi bi-eye me-1"></i>
                                    </button>
                                    <button wire:click="completeAppointment({{ $appointment->id }})"
                                        class="flux-btn btn-sm flux-btn-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                    </button>
                                    <button wire:click="cancelAppointment({{ $appointment->id }})"
                                        class="flux-btn btn-sm flux-btn-danger">
                                        <i class="bi bi-trash me-1"></i>
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