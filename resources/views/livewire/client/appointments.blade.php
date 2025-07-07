<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\Appointments;
use Livewire\Attributes\On;

new class extends Component {

  public $selectedAppointment = null;
  public $selectedDate = null;
  public $selectedTime = null;

  public function with()
  {
    return [
      'appointments' => Appointments::where('user_id', auth()->user()->id)
        ->with(['office', 'service', 'staff', 'appointmentDetails'])
        ->latest()
        ->paginate(10),
    ];
  }

  public function cancelAppointment($id)
  {
    $appointment = Appointments::findOrFail($id);

    // Only allow cancellation if appointment is pending or approved
    if (in_array($appointment->status, ['pending', 'approved'])) {
      $appointment->update(['status' => 'cancelled']);
      session()->flash('success', 'Appointment cancelled successfully.');
    } else {
      session()->flash('error', 'This appointment cannot be cancelled.');
    }
  }

  public function openRescheduleModal($id)
  {
    $this->selectedAppointment = Appointments::with(['office', 'service', 'staff'])
      ->findOrFail($id);
    $this->selectedDate = null;
    $this->selectedTime = null;
    $this->dispatch('open-modal-reschedule-appointment');
  }

  public function closeRescheduleModal()
  {
    $this->selectedAppointment = null;
    $this->selectedDate = null;
    $this->selectedTime = null;
    $this->dispatch('close-modal-reschedule-appointment');
  }

  public function confirmReschedule()
  {
    if (!$this->selectedAppointment || !$this->selectedDate || !$this->selectedTime) {
      session()->flash('error', 'Please select a valid date and time.');
      return;
    }

    try {
      $this->selectedAppointment->update([
        'booking_date' => $this->selectedDate,
        'booking_time' => $this->selectedTime,
        'status' => 'pending', // Reset to pending for approval
      ]);

      session()->flash('success', 'Appointment rescheduled successfully. Please wait for approval.');
      $this->closeRescheduleModal();
    } catch (\Exception $e) {
      session()->flash('error', 'Failed to reschedule appointment. Please try again.');
    }
  }

  #[On('slot-picker-updated')]
  public function handleSlotUpdate($data)
  {
    $this->selectedDate = $data['date'] ?? null;
    $this->selectedTime = $data['time'] ?? null;
  }

  public function showAppointmentDetails($id)
  {
    $this->selectedAppointment = Appointments::with(['office', 'service', 'staff', 'appointmentDetails'])
      ->findOrFail($id);
    $this->dispatch('open-modal-show-appointment');
  }
}; ?>

<div>
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
              Office
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Service
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Staff
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
              {{ $appointment->staff ? $appointment->staff->first_name . ' ' . $appointment->staff->last_name : 'Not Assigned' }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">
              {{ $appointment->booking_date->format('M d, Y') }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">
              {{ \Carbon\Carbon::parse($appointment->booking_time)->format('h:i A') }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="flux-badge flux-badge-{{ match ($appointment->status) {
        'pending' => 'warning',
        'approved' => 'success',
        'cancelled' => 'danger',
        'completed' => 'success',
        'no-show' => 'secondary',
        default => 'light',
        } }}">
              {{ ucfirst($appointment->status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="d-flex gap-2">
              @if(in_array($appointment->status, ['pending', 'approved']))
            <button class="flux-btn btn-sm flux-btn-outline flux-btn-danger"
            wire:click="cancelAppointment({{ $appointment->id }})"
            wire:confirm="Are you sure you want to cancel this appointment?">
            Cancel
            </button>
          @endif

              @if($appointment->status === 'pending')
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
        <td colspan="7" class="text-center py-5">
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
  @include('livewire.client.components.modal.reschedule-appointment-modal')
</div>