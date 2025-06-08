<?php

use Livewire\Volt\Component;
use App\Models\Offices;
use App\Models\Services;

new class extends Component {
    public Offices $office;
    public Services $service;

    public function mount(Offices $office, Services $service)
    {
        if ($service->office_id !== $office->id) {
            abort(404);
        }
        $this->office = $office;
        $this->service = $service;
    }

}; ?>

<div>
     <h1>Request</h1>
</div> 