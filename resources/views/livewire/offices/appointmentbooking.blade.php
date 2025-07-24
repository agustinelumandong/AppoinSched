<?php

use Livewire\Volt\Component;
use App\Models\Offices;
use App\Models\Services;
use Livewire\Attributes\{Title};

new #[Title('Appointment Booking')] class extends Component {
    public Offices $office;
    public ?string $reference_number = null;

    public function mount(Offices $office)
    {
        $this->office = $office;
        $this->reference_number = request()->query('reference_number');
    }

    public function with()
    {
        return [
            'office' => $this->office,
        ];
    }
}; ?>

<div>
    @include('components.alert')
    <livewire:components.appointmentstepper :office="$office" :reference_number="$reference_number" />
</div>