<?php

use Livewire\Volt\Component;
use App\Models\DocumentRequest;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $id;
    public $documentRequest;

    public function mount(): void
    {
        // Update last viewed timestamp when admin visits document requests page
        if (auth()->check() && auth()->user()->hasAnyRole(['admin', 'super-admin'])) {
            auth()->user()->update(['last_viewed_document_requests_at' => now()]);
        }
    }

    public function with()
    {
        return [
            'documentRequests' => DocumentRequest::query()
                ->with(['user', 'staff', 'office', 'service', 'details'])
                ->when($this->search, function ($query) {
                    $query->whereHas('user', function ($q) {
                        $q->where('first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
                })
                ->latest()
                ->paginate(10),
        ];
    }

    public function viewDocumentRequest($id)
    {
        $this->dispatch('viewDocumentRequest', $id);
        $this->id = $id;
    }
}; ?>

<div>

    @include('components.alert')

    @include('livewire.documentrequest.components.header')
    @include('livewire.documentrequest.components.table')
</div>
