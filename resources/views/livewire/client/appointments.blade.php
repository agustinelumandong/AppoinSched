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

  public function openRescheduleModal($id)
  {
    $this->selectedAppointment = Appointments::with(['office', 'service', 'staff'])
      ->findOrFail($id);
    $this->selectedDate = null;
    $this->selectedTime = null;
    $this->dispatch('open-modal-reschedule-appointment');
  }

  public function rescheduleAppointment()
  {
    if (!$this->selectedAppointment) {
      session()->flash('error', 'Appointment not found');
      return;
    }

    try {
      $validated = $this->validate([
        'selectedDate' => 'required|date|after_or_equal:today',
        'selectedTime' => 'required|string',
      ]);

      // Check for scheduling conflicts
      $conflict = Appointments::where('booking_date', $this->selectedDate)
        ->where('booking_time', $this->selectedTime)
        ->where('office_id', $this->selectedAppointment->office_id)
        ->where('staff_id', $this->selectedAppointment->staff_id)
        ->where('id', '!=', $this->selectedAppointment->id)
        ->exists();

      if ($conflict) {
        session()->flash('error', 'This time slot is already booked. Please select a different time.');
        return;
      }

      $this->selectedAppointment->update([
        'booking_date' => $this->selectedDate,
        'booking_time' => $this->selectedTime,
      ]);

      session()->flash('success', 'Appointment rescheduled successfully.');
      $this->dispatch('close-modal-reschedule-appointment');
    } catch (\Exception $e) {
      session()->flash('error', 'Failed to reschedule appointment.');
    }
  }

  public function showAppointmentDetails($id)
  {
    $this->selectedAppointment = Appointments::with(['office', 'service', 'staff'])
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
          {{ \Carbon\Carbon::parse($appointment->booking_time)->format('h:i A') }}
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
          <div class="d-flex gap-2">
          <button class="flux-btn btn-sm flux-btn-outline flux-btn-primary"
            wire:click="openRescheduleModal({{ $appointment->id }})">
            Reschedule
          </button>

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
  @include('livewire.client.components.modal.reschedule-appointment-modal')
</div>