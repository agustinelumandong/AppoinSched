<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\DocumentRequest;

new class extends Component {

    public $selectedPaymentMethod = null;
    public $selectedDocumentRequest = null;
    public function with()
    {
        return [
            'requests' => DocumentRequest::where('user_id', auth()->user()->id)
                ->with('office')
                ->latest()
                ->paginate(10),
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
        $method = $this->selectedPaymentMethod; 
        if($method === 'online') {
            $request->update([
                'payment_status' => 'paid',
                'payment_reference' => $request->reference_number,
            ]);
        } elseif($method === 'walkIn') { 
            $office_slug = 'municipal-treasurers-office'; 
            $service_slug = $request->service->slug;
            $reference_number = $request->reference_number;
            $this->redirect(route('offices.service.appointment', 
            [
                'office' => $office_slug, 
                'services' => $service_slug, 
                'reference_number' => $reference_number]));
        }
        $this->dispatch('close-modal-payment-method');
        $this->reset(); 
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
}; ?>

<div>
    <div class="flux-card mb-4">
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
                                                        'approved' => 'success',
                                                        'rejected' => 'danger',
                                                        'completed' => 'success',
                                                        default => 'light',
                                                    } }}">
                                                    {{ ucfirst($request->status) }}
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
                                                {{--  --}}
                                                <button
                                                    class="flux-btn btn-sm flux-btn-primary text-decoration-none {{ $request->status === 'approved' && $request->payment_status === 'unpaid' ? '' : 'opacity-50 cursor-not-allowed' }}"
                                                    wire:click="$dispatch('open-modal-payment-method'); $wire.setSelectedDocumentRequest({{ $request->id }})"
                                                    @if(!($request->status === 'approved' && $request->payment_status === 'unpaid')) disabled aria-disabled="true" @endif
                                                >
                                                    Pay Now
                                                </button>
                                                
                                                <div class="d-flex gap-2">
                                                    {{-- <a href="#" class="flux-btn flux-btn-outline btn-sm" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a> --}}
                                                </div>
                                            </td>
                                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
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
        {{-- <div class="mt-3">
            {{ $requests->links(data: ['scrollTo' => false]) }}
        </div> --}}
    </div>

    

    @include('livewire.client.components.modal.payment-method-modal')

</div>