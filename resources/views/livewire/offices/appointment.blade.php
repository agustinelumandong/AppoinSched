<?php

use Livewire\Volt\Component;
use App\Models\Offices;
use App\Models\Services;
use Livewire\Attributes\{Title};

new #[Title('Appointment')]
  class extends Component {
  public Offices $office;
  public Services $service;

  public function mount(Offices $office, Services $service)
  {
    if ($service->office_id !== $office->id) {
      abort(404);
    }
    $this->office = $office;
  }

}; ?>

<div>
  <livewire:components.appointmentstepper :office="$office" />
</div>