<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\Appointments;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\AdminEventNotification;
use App\Enums\AdminNotificationEvent;

new class extends Component {
  public ?Appointments $appointment = null;
  public ?string $selectedDate = null;
  public ?string $selectedTime = null;
  public function mount(): void
  {
    $this->resetRescheduleData();
  }

  #[On('slot-picker-updated')]
  public function updateRescheduleSlot($data): void
  {
    $this->selectedDate = $data['date'] ?? null;
    $this->selectedTime = $data['time'] ?? null;
  }

  public function with(): array
  {
    return [
      'appointments' => Appointments::where('user_id', auth()->user()->id)
        ->with(['office', 'service', 'appointmentDetails'])
        ->latest()
        ->paginate(10),
    ];
  }

  public function openRescheduleModal(int $id): void
  {
    $appointment = Appointments::with(['office', 'service'])->findOrFail($id);
    $this->appointment = $appointment;
    $this->selectedDate = $appointment->booking_date?->format('Y-m-d');
    $this->selectedTime = $appointment->booking_time;
    $this->dispatch('open-modal-reschedule-appointment');
  }

  public function rescheduleAppointment(): void
  {
    if (!$this->appointment) {
      session()->flash('error', 'Appointment not found');
      return;
    }

    try {
      $validated = $this->validate([
        'selectedDate' => 'required|date|after_or_equal:today',
        'selectedTime' => 'required|string',
      ]);

      // Check for scheduling conflicts
      $conflict = $this->checkForConflicts();
      if ($conflict) {
        session()->flash('error', 'This time slot is already booked. Please select a different time.');
        return;
      }

      $updated = $this->appointment->update([
        'booking_date' => $this->selectedDate,
        'booking_time' => $this->selectedTime,
      ]);

      // Send notification to staff
      $staffs = User::getStaffsByOfficeId($this->appointment->office_id);
      if ($staffs->count() > 0) {
        foreach ($staffs as $staff) {
          $staff->notify(new AdminEventNotification(
            AdminNotificationEvent::UserAppointmentRescheduled,
            [
              'reference_no' => $this->appointment->reference_number,
              'date' => $this->selectedDate,
              'time' => $this->selectedTime,
              'location' => $this->appointment->office->name,
              'service' => $this->appointment->service->title,
              'user' => auth()->user()->name,
              'user_email' => auth()->user()->email,
            ]
          ));
        }
      }

      if ($updated) {
        $this->resetRescheduleData();
        $this->dispatch('close-modal-reschedule-appointment');
        session()->flash('success', 'Appointment rescheduled successfully.');
      } else {
        session()->flash('error', 'Failed to reschedule appointment.');
      }
    } catch (\Illuminate\Validation\ValidationException $e) {
      $errors = collect($e->errors())->flatten();
      session()->flash('error', 'Validation failed: ' . $errors->first());
    } catch (\Exception $e) {
      Log::error('Failed to reschedule appointment: ' . $e->getMessage());
      session()->flash('error', 'Failed to reschedule appointment.');
    }
  }

  private function checkForConflicts(): bool
  {
    try {
      $timeToCheck = Carbon::parse($this->selectedTime)->format('H:i');
      $conflict = Appointments::where('booking_date', $this->selectedDate)
        ->where(function ($query) use ($timeToCheck) {
          $query->whereRaw("TIME_FORMAT(booking_time, '%H:%i') = ?", [$timeToCheck]);
        })
        ->where('office_id', $this->appointment->office_id)
        ->where('service_id', $this->appointment->service_id)
        ->where('id', '!=', $this->appointment->id)
        ->exists();
      return $conflict;
    } catch (\Exception $e) {
      Log::error('Error checking for conflicts: ' . $e->getMessage());
      return true;
    }
  }

  private function resetRescheduleData(): void
  {
    $this->appointment = null;
    $this->selectedDate = null;
    $this->selectedTime = null;
  }

  public function showAppointmentDetails(int $id): void
  {
    $this->appointment = Appointments::with(['office', 'service'])->findOrFail($id);
    $this->dispatch('open-modal-show-appointment');
  }

  public function searchs(): void
  {
    $this->resetPage();
  }

  public function cancelAppointment(int $id): void
  {
    $this->appointment = Appointments::findOrFail($id);
    $this->dispatch('open-modal-confirm-modal');
  }

  public function confirmCancelAppointment(int $id): void
  {
    $appointment = Appointments::findOrFail($id);
    $appointment->update(['status' => 'cancelled']);
    $staffs = User::getStaffsByOfficeId($appointment->office_id);
    if ($staffs->count() > 0) {
      foreach ($staffs as $staff) {
        $staff->notify(new AdminEventNotification(
          AdminNotificationEvent::UserAppointmentCancelled,
          [
            'reference_no' => $appointment->reference_number,
            'date' => $appointment->booking_date,
            'time' => $appointment->booking_time,
            'location' => $appointment->office->name,
            'service' => $appointment->service->title,
            'user' => auth()->user()->name,
            'user_email' => auth()->user()->email,
          ]
        ));
      }
    }
    $this->dispatch('close-modal-confirm-modal');
    session()->flash('success', 'Appointment cancelled successfully.');
  }


}; ?>

<div>

  @include('components.alert')

  <div class="flux-card mb-4">
    <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
      <h5 class="mb-0 fw-semibold">My Appointments</h5>
      <div class="d-flex align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
          <input type="text" class="flux-form-control search" placeholder="Search appointments" wire:model.live="search"
            wire:keyup="searchs" wire:debounce.300ms>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table flux-table mb-0">
        <thead>
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Reference Number
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Office
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Service
            </th>

            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Date
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Time
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Status
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody>
          @forelse($appointments as $appointment)
        <tr>
        <td class="px-6 py-4 whitespace-nowrap">
          <div class="text-sm font-medium text-gray-900">
          {{ $appointment->reference_number ?? 'N/A' }}
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
          <div class="text-sm font-medium text-gray-900">
          {{ $appointment->office?->name ?? 'N/A' }}
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
          <div class="text-sm font-medium text-gray-900">
          {{ $appointment->service?->title ?? 'N/A' }}
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
          <div class="text-sm font-medium text-gray-900">
          {{ $appointment->booking_date->format('M d, Y') }}
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
          <div class="text-sm font-medium text-gray-900">
          {{  Carbon::parse($appointment->booking_time)->format('h:i A') }}
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
          <div class="text-sm font-medium text-gray-900">
          @php
        $status = $appointment->status ?? 'N/A';
        $badgeColor = match ($status) {
        'on-going' => 'flux-badge-warning',
        'completed' => 'flux-badge-success',
        'cancelled' => 'flux-badge-danger',
        default => 'flux-badge-light',
        };
      @endphp
          <span class="flux-badge {{ $badgeColor }} capitalize">
            {{ ucfirst($status) }}
          </span>
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
          <div class="d-flex gap-2">
          @if($appointment->status == 'on-going')
        <button class="flux-btn btn-sm flux-btn-outline flux-btn-primary"
        wire:click="cancelAppointment({{ $appointment->id }})">
        Cancel
        </button>
      @endif
          @if($appointment->status == 'on-going')
        <button class="flux-btn btn-sm flux-btn-outline flux-btn-primary"
        wire:click="openRescheduleModal({{ $appointment->id }})">
        Reschedule
        </button>
      @endif
          <button class="flux-btn btn-sm flux-btn-outline"
            wire:click="showAppointmentDetails({{ $appointment->id }})">
            View Details
          </button>
          </div>
        </td>
        </tr>
      @empty
        <tr>
        <td colspan="6" class="text-center py-5">
          <div class="text-muted">
          <i class="bi bi-calendar-check display-4 mb-3"></i>
          <div>No appointments found. Book your first appointment to get started.
          </div>
          </div>
        </td>
        </tr>
      @endforelse
        </tbody>
      </table>
    </div>
    <!-- Pagination links -->
    <div class="mt-3">
      {{ $appointments->links(data: ['scrollTo' => false]) }}
    </div>
  </div>

  @include('livewire.client.components.modal.show-appointment-modal')
  @include('livewire.client.components.modal.confirmation-modal')
  @include('livewire.client.components.modal.reschedule-appointment-modal')
</div>