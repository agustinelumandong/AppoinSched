<?php

use Livewire\Volt\Component;
use App\Models\Offices;
use App\Models\Services;
use Livewire\Attributes\{Title};

new #[Title('Request')] class extends Component {
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
    @include('components.alert')
    <livewire:components.documentrequeststepper :office="$office" :service="$service" />
</div>
