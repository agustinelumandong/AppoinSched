<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\DocumentRequest;

new class extends Component {

    public $selectedPaymentMethod = null;
    public $selectedDocumentRequest = null;
    public $selectedRemarksRequest = null;

    public function with()
    {
        return [
            'requests' => DocumentRequest::where('user_id', auth()->user()->id)
                ->with('office')
                ->latest()
                ->paginate(5),
        ];
    }

    public function selectPaymentMethod($method)
    {
        $this->selectedPaymentMethod = $method;
    }

    public function payNow()
    {
        $id = $this->selectedDocumentRequest->id;
        $request = DocumentRequest::findOrFail($id);
        $office = $request->office;
        $service = $request->service;
        $referenceNumber = $request->reference_number;
        // Redirect to the document request stepper for payment
        return redirect()->route('offices.service.request', [
            'office' => $office->slug,
            'service' => $service->slug,
            'reference_number' => $referenceNumber,
        ]);
    }

    public function setSelectedDocumentRequest($id)
    {
        $this->selectedDocumentRequest = DocumentRequest::findOrFail($id);
    }

    public function resetPaymentMethodStates()
    {
        $this->dispatch('close-modal-payment-method');
        $this->reset();
    }

    public function openRemarksModal($id)
    {
        $this->selectedRemarksRequest = DocumentRequest::findOrFail($id);
        $this->dispatch('open-modal-view-remarks');
    }

    public function closeRemarksModal()
    {
        $this->dispatch('close-modal-view-remarks');
        $this->selectedRemarksRequest = null;
    }
}; ?>

<div>
    <div class="flux-card mb-4">
        <div style="padding: 12px;">

            <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                <h5 class="mb-0 fw-semibold">Document Requests</h5>
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="flux-form-control search" placeholder="Search document requests"
                            wire:model.live="search" wire:keyup="searchs" wire:debounce.300ms>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table flux-table mb-0">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Reference Number
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Office
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Document
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Payment Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Request Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $request->reference_number ?? 'N/A' }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $request->office->name }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $request->service->title }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                            ₱ {{ number_format($request->service->price, 2) }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="flux-badge flux-badge-{{ match ($request->status) {
                                                            'pending' => 'warning',
                                                            'in-progress' => 'info',
                                                            'ready-for-pickup' => 'success',
                                                            'complete' => 'success',
                                                            'cancelled' => 'danger',
                                                            default => 'light',
                                                        } }}">
                                                        {{ ucfirst(str_replace('-', ' ', $request->status)) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="flux-badge flux-badge-{{ match ($request->payment_status) {
                                                            'unpaid' => 'warning',
                                                            'paid' => 'success',
                                                            'pending' => 'danger',
                                                            'completed' => 'success',
                                                            default => 'light',
                                                        } }}">
                                                        {{ ucfirst($request->payment_status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <p class="fw-semibold">
                                                        {{ $request->created_at->format('M d, Y') }}
                                                    </p>
                                                </td>
                                                <td>
                                                    @if ($request->status === 'cancelled' || $request->payment_status === 'failed')
                                                        <button
                                                            class="flux-btn btn-sm flux-btn-outline flux-btn-info text-decoration-none"
                                                            wire:click="openRemarksModal({{ $request->id }})"
                                                        >
                                                            View Remarks from staff
                                                        </button>
                                                    @else
                                                        <button
                                                            class="flux-btn btn-sm flux-btn-primary text-decoration-none {{   $request->payment_status === 'unpaid' ? '' : 'opacity-50 cursor-not-allowed' }}"
                                                            wire:click="$dispatch('open-modal-payment-method'); $wire.setSelectedDocumentRequest({{ $request->id }})"
                                                        >
                                                            Pay Now
                                                        </button>
                                                    @endif

                                                    <div class="d-flex gap-2">
                                                        {{-- <a href="#" class="flux-btn flux-btn-outline btn-sm" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a> --}}
                                                    </div>
                                                </td>
                                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-person-badge display-4 mb-3"></i>
                                        <div>No document requests found. Create your first document request to get started.
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
            <!-- Pagination links -->
            <div class="mt-3">
                {{ $requests->links(data: ['scrollTo' => false]) }}
            </div>
        </div>
    </div>



    @include('livewire.client.components.modal.payment-method-modal')
    @include('livewire.client.components.modal.view-remarks-modal')

</div>
