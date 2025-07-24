<?php

use Livewire\Volt\Component;
use App\Models\Offices;
use App\Models\Services;
use Livewire\Attributes\{Title};

new #[Title('Request')] class extends Component {
    public Offices $office;
    public ?string $reference_number = '';

    public function mount(Offices $office, ?string $reference_number = null)
    {
        $this->office = $office;
        $this->reference_number = $reference_number;
    }
}; ?>

<div>
    @include('components.alert')
    <livewire:components.documentrequeststepper :office="$office" :reference_number="$reference_number" />
</div>