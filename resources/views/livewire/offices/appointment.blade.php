<?php

use Livewire\Volt\Component;
use App\Models\Offices;
use Livewire\Attributes\{Title};

new #[Title('Appointment')] class extends Component {
    public Offices $office;

    public function mount(Offices $office)
    {
        $this->office = $office;
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
    <livewire:components.appointmentstepper :office="$office" />
</div>