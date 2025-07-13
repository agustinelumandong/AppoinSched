<?php

use Livewire\Volt\Component;
use App\Models\Offices;
use App\Models\Services;
use Livewire\Attributes\{Title};

new #[Title('Request')] class extends Component {
    public Offices $office;
    public Services $service;
    public ?string $reference_number = '';

    public function mount(Offices $office, Services $service, ?string $reference_number = null)
    {
        if ($service->office_id !== $office->id) {
            abort(404);
        }
        $this->office = $office;
        $this->service = $service;
        $this->reference_number = $reference_number;
    }
}; ?>

<div>
    @include('components.alert')
    <livewire:components.documentrequeststepper :office="$office" :service="$service"
        :reference_number="$reference_number" />
</div>