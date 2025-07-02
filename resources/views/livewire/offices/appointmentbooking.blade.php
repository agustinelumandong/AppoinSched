<?php

use Livewire\Volt\Component;
use App\Models\Offices;
use App\Models\Services;
use Livewire\Attributes\{Title};

new #[Title('Appointment Booking')] class extends Component {
    public Offices $office;
    public Services $service;

    public function mount(Offices $office, Services $service)
    {
        $this->office = $office;
        $this->service = $service;
    }

    public function with()
    {
        return [
            'office' => $this->office,
            'service' => $this->service,
        ];
    }
}; ?>

<div>
    @include('components.alert')
    <livewire:components.appointmentstepper :office="$office" />
</div>
