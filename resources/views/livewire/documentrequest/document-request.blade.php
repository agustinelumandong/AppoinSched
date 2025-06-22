<?php

use Livewire\Volt\Component;
use App\Models\DocumentRequest;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $id;
    public $documentRequest;

    public function with()
    {
        return [
            'documentRequests' => DocumentRequest::with(['user', 'staff', 'office', 'service'])
            ->where('user_id', auth()->user()->id)
            ->orWhere('staff_id', auth()->user()->id)
            ->orWhere('office_id', auth()->user()->id)
            ->orWhereHas('user', fn($q) => $q->where('first_name', 'like', '%' . $this->search . '%'))
            ->orWhereHas('staff', fn($q) => $q->where('first_name', 'like', '%' . $this->search . '%'))
            ->orWhereHas('office', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->orWhereHas('service', fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->orderBy('created_at', 'desc')
            ->paginate(5),
        ];
    }

    public function viewDocumentRequest($id)
    {
        // $this->emit('viewDocumentRequest', $id);
        $this->dispatch('viewDocumentRequest', $id);
        $this->id = $id;
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

    @include('livewire.documentrequest.components.header')
    @include('livewire.documentrequest.components.table')
</div>
