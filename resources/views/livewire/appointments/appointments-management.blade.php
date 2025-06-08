<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\Appointments;
use Livewire\WithPagination;

new class extends Component {

    use WithPagination;

    public string $search = '';
    public mixed $booking_date;
    public mixed $booking_time;
    public  $status = '';
    public string $notes = '';
    public string $user_id = '';
    public string $staff_id = '';
    public string $office_id = '';
    public string $service_id = '';
    public  $user = null;
    public  $staff = null;
    public  $office = null;
    public  $service = null;
    public  $showAppointmentModal = false;
    public ?Appointments $appointment = null;

    public function mount(Appointments $appointment)
    {
        $this->appointment = $appointment; 
        $this->status = $appointment->status;
        $this->resetPage();
        $this->reset();
    }

    public function searchs()
    {
        $this->resetPage();
    }

    public function with()
    {
        return [
            'appointments' =>Appointments::with('user', 'staff', 'office', 'service')
            ->where('status', 'like', '%' . $this->search . '%')
            ->orWhere('notes', 'like', '%' . $this->search . '%')
            ->orWhere('user_id', 'like', '%' . $this->search . '%')
            ->orWhere('staff_id', 'like', '%' . $this->search . '%')
            ->orWhere('office_id', 'like', '%' . $this->search . '%')
            ->orWhere('service_id', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')->paginate(10),
        ];
    }

    public function openShowAppointmentModal(int $id)
    {
        $appointment = Appointments::findOrFail($id);
        if($appointment){
            $this->appointment = $appointment;
            $this->user = $appointment->user()->where('id', $appointment->user_id)->first();
            $this->staff = $appointment->staff()->where('id', $appointment->staff_id)->first();
            $this->office = $appointment->office()->where('id', $appointment->office_id)->first();
            $this->service = $appointment->service()->where('id', $appointment->service_id)->first();
            $this->showAppointmentModal = true;
            $this->dispatch('open-modal-show-appointment');
        }
    }

    public function openEditAppointmentModal(int $id)
    {
        $appointment = Appointments::findOrFail($id);
        if($appointment){
            $this->appointment = $appointment;
            $this->booking_date = $appointment->booking_date;
            $this->booking_time = $appointment->booking_time;
            $this->status = $appointment->status;
            $this->notes = $appointment->notes;
            $this->dispatch('open-modal-edit-appointment');
        }
    }

    public function updateAppointment()
    {
        $validated =$this->validate([
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);
        if($validated){
         $updated =   $this->appointment->update([
            'booking_date' => $this->booking_date,
            'booking_time' => $this->booking_time,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);
        if($updated){
            $this->reset();
            $this->resetPage();
            $this->dispatch('close-modal-edit-appointment');
            $this->dispatch('close-modal-show-appointment');
            session()->flash('success', 'Appointment updated successfully');    
        }else{
            session()->flash('error', 'Appointment update failed');
        }
    }
    }

}; ?>

<div>

    <link rel="stylesheet" href="{{ asset('css/fluxUI.css') }}">

    <!-- Flash Messages -->
    <div>
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    @include('livewire.appointments.components.header')
    @include('livewire.appointments.components.table')
    @include('livewire.appointments.components.modal.edit-appointments-modal')
    @include('livewire.appointments.components.modal.show-appointments-modal')
</div>
