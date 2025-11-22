<?php

declare(strict_types=1);

use App\Enums\RequestNotificationEvent;
use App\Models\DocumentRequest;
use App\Models\Offices;
use App\Notifications\RequestEventNotification;
use App\Notifications\DocumentClaimSlipNotification;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public string $status = 'in-progress';

    public string $sortDirection = 'desc';

    public ?int $selectedOfficeId = null;

    public ?int $documentRequestId = null;

    public ?DocumentRequest $documentRequest = null;

    public string $remarks = '';

    public bool $confirmApproved = false;

    public bool $confirmRejected = false;

    public bool $confirmPending = false;

    public bool $confirmPaymentStatus = false;

    public string $paymentStatusToUpdate = '';

    public string $documentStatusToUpdate = '';

    public function mount(): void
    {
        // Check if user has staff or admin role
        if (
            !auth()
                ->user()
                ->hasAnyRole(['MCR-staff', 'MTO-staff', 'BPLS-staff', 'MCR-admin', 'MTO-admin', 'BPLS-admin', 'admin', 'super-admin'])
        ) {
            abort(403, 'Unauthorized access');
        }

        // Update last viewed timestamp when staff visits document requests page
        auth()->user()->update(['last_viewed_document_requests_at' => now()]);
    }

    #[On('documentRequestUpdated')]
    public function refreshComponent(): void
    {
        $this->resetPage();
    }

    public function searchs(): void
    {
        $this->resetPage();
    }

    /**
     * Mark a status as seen and filter by that status (called when user clicks on complete or cancelled filter)
     */
    public function markStatusAsSeen(string $status): void
    {
        if (!in_array($status, ['complete', 'cancelled'])) {
            return;
        }

        // Mark current count as seen
        $currentCount = $this->getStatusCount($status);
        $seenCounts = session('document_status_seen_counts', []);
        $seenCounts[$status] = $currentCount;
        session(['document_status_seen_counts' => $seenCounts]);

        // Set the status filter
        $this->status = $status;
        $this->resetPage(); // Reset pagination when filtering
    }

    /**
     * Get unseen count for a status (for complete and cancelled only)
     */
    private function getUnseenCount(string $status): int
    {
        if (!in_array($status, ['complete', 'cancelled'])) {
            return 0;
        }

        $currentCount = $this->getStatusCount($status);
        $seenCounts = session('document_status_seen_counts', []);
        $seenCount = $seenCounts[$status] ?? 0;

        return max(0, $currentCount - $seenCount);
    }

    public function viewDocumentRequest(int $id): void
    {
        try {
            $documentRequest = DocumentRequest::with(['user', 'staff', 'office', 'service', 'details'])->findOrFail($id);

            // Check if staff is assigned to this document request's office
            if (!auth()->user()->isAssignedToOffice($documentRequest->office_id)) {
                session()->flash('error', 'You are not authorized to view this document request');

                return;
            }

            $this->documentRequest = $documentRequest;
            $this->documentRequestId = $id;
            $this->dispatch('viewDocumentRequest', $id);
        } catch (\Exception $e) {
            Log::error('Error viewing document request: ' . $e->getMessage());
            session()->flash('error', 'Failed to load document request');
        }
    }

    public function setDocumentStatusToUpdate(int $id, string $status): void
    {
        // Validate status
        if (!in_array($status, \App\Models\DocumentRequest::VALID_STATUSES)) {
            session()->flash('error', 'Invalid status value');
            return;
        }

        $this->documentRequestId = $id;
        $this->documentStatusToUpdate = $status;

        if ($status === 'cancelled') {
            $this->confirmRejected = true;
        }

        $this->remarks = ''; // Reset remarks when opening modal
        $this->dispatch('open-modal-confirm-document-request');
    }

    public function confirmDocumentRequest(): void
    {
        try {
            // Validate remarks for required actions
            if ($this->documentStatusToUpdate === 'cancelled') {
                if (empty(trim($this->remarks))) {
                    session()->flash('error', 'Remarks are required when cancelling a document request.');
                    return;
                }
            }

            if (!$this->documentRequestId) {
                session()->flash('error', 'No document request selected.');
                return;
            }

            $this->updateDocumentStatus($this->documentRequestId, $this->documentStatusToUpdate);
            $this->resetConfirmationStates();
            $this->dispatch('close-modal-confirm-document-request');
        } catch (\Exception $e) {
            Log::error('Error confirming document request: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while updating the document request.');
        }
    }

    public function resetConfirmationStates(): void
    {
        $this->confirmApproved = false;
        $this->confirmRejected = false;
        $this->confirmPending = false;
        $this->confirmPaymentStatus = false;
        $this->paymentStatusToUpdate = '';
        $this->documentStatusToUpdate = '';
    }

    public function updateDocumentStatus(int $id, string $status): void
    {
        try {
            $documentRequest = DocumentRequest::with('user')->findOrFail($id);

            // Check if staff is assigned to this document request's office
            if (!auth()->user()->isAssignedToOffice($documentRequest->office_id)) {
                session()->flash('error', 'You are not authorized to update this document request');

                return;
            }

            // Validate the status parameter directly
            if (!in_array($status, \App\Models\DocumentRequest::VALID_STATUSES)) {
                session()->flash('error', 'Invalid status value');
                return;
            }

            $updateData = [
                'status' => $status,
                'staff_id' => auth()->id(),
            ];

            // Include remarks if provided
            if (!empty(trim($this->remarks))) {
                $updateData['remarks'] = $this->remarks;
            }

            // When document status is cancelled, automatically set payment_status to failed
            if ($status === 'cancelled' && $documentRequest->payment_status !== 'failed') {
                $updateData['payment_status'] = 'failed';
            }

            $updated = $documentRequest->update($updateData);

            if ($updated) {
                // Set completed_date when status is complete
                if ($status === 'complete') {
                    $documentRequest->update(['completed_date' => now()]);
                }

                // Send payment failed notification if payment status was automatically set to failed
                if ($status === 'cancelled' && isset($updateData['payment_status']) && $updateData['payment_status'] === 'failed') {
                    $documentRequest->user->notify(
                        new RequestEventNotification(RequestNotificationEvent::PaymentFailed, [
                            'reference_no' => $documentRequest->reference_number,
                        ])
                    );
                }

                // Send notification to client for status changes
                if ($status === 'in-progress') {
                    $documentRequest->user->notify(
                        new RequestEventNotification(RequestNotificationEvent::DocumentInProgress, [
                            'reference_no' => $documentRequest->reference_number,
                        ])
                    );
                } elseif ($status === 'ready-for-pickup') {
                    $documentRequest->user->notify(
                        new RequestEventNotification(RequestNotificationEvent::DocumentReadyForPickup, [
                            'reference_no' => $documentRequest->reference_number,
                        ])
                    );
                    // Send claim slip notification
                    $documentRequest->user->notify(
                        new DocumentClaimSlipNotification($documentRequest)
                    );
                } elseif ($status === 'complete') {
                    $documentRequest->user->notify(
                        new RequestEventNotification(RequestNotificationEvent::DocumentCompleted, [
                            'reference_no' => $documentRequest->reference_number,
                        ])
                    );
                } elseif ($status === 'cancelled') {
                    $documentRequest->user->notify(
                        new RequestEventNotification(RequestNotificationEvent::DocumentCancelled, [
                            'reference_no' => $documentRequest->reference_number,
                        ])
                    );
                }

                // Refresh the page data without losing the flash message
                $this->resetPage();
                session()->flash('success', 'Document request status updated to ' . ucfirst($status) . ' successfully. Client has been notified via email.');
            } else {
                session()->flash('error', 'Failed to update document request status');
            }
        } catch (\Exception $e) {
            Log::error('Document request status update failed: ' . $e->getMessage(), [
                'exception' => $e,
                'status' => $status,
                'document_request_id' => $id,
            ]);
            session()->flash('error', 'An error occurred while updating the document request status: ' . $e->getMessage());
        }
    }

    public function updateDocumentStatusModal(string $status): void
    {
        try {
            if (!$this->documentRequest) {
                session()->flash('error', 'No document request selected.');

                return;
            }

            // Check if staff is assigned to this document request's office
            if (!auth()->user()->isAssignedToOffice($this->documentRequest->office_id)) {
                session()->flash('error', 'You are not authorized to update this document request');

                return;
            }

            $updateData = [
                'status' => $status,
                'staff_id' => auth()->id(),
            ];

            // Note: completed_date is no longer automatically set based on status
            // Note: Remarks should be handled via the confirmation modal in the detailed view

            $this->documentRequest->update($updateData);

            session()->flash('success', 'Document request status updated to ' . ucfirst($status) . ' successfully.');
        } catch (\Exception $e) {
            Log::error('Document status update failed: ' . $e->getMessage());
            session()->flash('error', 'Failed to update document request status.');
        }
    }

    public function getOfficeIdForStaff(): ?int
    {
        return auth()->user()->getOfficeIdForStaff();
    }

    /**
     * Get base query with office filtering applied
     */
    private function getBaseQuery()
    {
        $user = auth()->user();
        $officeId = $user->getOfficeIdForStaff();

        $query = DocumentRequest::query();

        // Super-admin sees all offices, others see only their assigned office
        if (!$user->hasRole('super-admin') && $officeId) {
            $query->where('office_id', $officeId);
        }

        return $query;
    }

    /**
     * Get count of document requests by status
     */
    private function getStatusCount(string $status): int
    {
        return $this->getBaseQuery()
            ->where('status', $status)
            ->count();
    }

    public function with(): array
    {
        $user = auth()->user();
        $officeId = $user->getOfficeIdForStaff();

        $query = DocumentRequest::with(['user', 'staff', 'office', 'service', 'details']);

        // Super-admin sees all offices, others see only their assigned office
        if (!$user->hasRole('super-admin') && $officeId) {
            $query->where('office_id', $officeId);
        }

        return [
            'documentRequests' => $query
                ->when($this->search, function ($q) {
                    $q->whereHas('user', function ($userQuery) {
                        $userQuery->where('first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%')
                            ->orWhere('reference_number', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->status, function ($q) {
                    $q->where('status', $this->status);
                })
                ->orderBy('created_at', $this->sortDirection)
                ->paginate(10),
            'statusCounts' => [
                'pending' => $this->getStatusCount('pending'),
                'in-progress' => $this->getStatusCount('in-progress'),
                'ready-for-pickup' => $this->getStatusCount('ready-for-pickup'),
                // 'complete' => $this->getStatusCount('complete'),
                // 'cancelled' => $this->getStatusCount('cancelled'),
            ],
            'unseenCounts' => [
                'complete' => $this->getUnseenCount('complete'),
                'cancelled' => $this->getUnseenCount('cancelled'),
            ],
        ];
    }

    public function updatePaymentStatus(string $status): void
    {
        try {
            if (!$this->documentRequest) {
                session()->flash('error', 'No document request selected.');

                return;
            }

            // Check if staff is assigned to this document request's office
            if (!auth()->user()->isAssignedToOffice($this->documentRequest->office_id)) {
                session()->flash('error', 'You are not authorized to update this document request');

                return;
            }

            // Note: For failed payment status, remarks should be provided via the detailed view modal
            // This direct update method is kept for quick actions, but staff should use the detailed view for failed status
            $this->documentRequest->update([
                'payment_status' => $status,
            ]);

            session()->flash('success', 'Payment status updated to ' . ucfirst($status) . ' successfully.');
        } catch (\Exception $e) {
            Log::error('Payment status update failed: ' . $e->getMessage());
            session()->flash('error', 'Failed to update payment status.');
        }
    }

    public function updatePaymentStatusFromTable(int $id, string $status): void
    {
        try {
            $documentRequest = DocumentRequest::with('user')->findOrFail($id);

            // Check if staff is assigned to this document request's office
            if (!auth()->user()->isAssignedToOffice($documentRequest->office_id)) {
                session()->flash('error', 'You are not authorized to update this document request');
                return;
            }

            $updateData = [
                'payment_status' => $status,
            ];

            // When payment status is failed, automatically set document status to cancelled
            if ($status === 'failed' && $documentRequest->status !== 'cancelled') {
                $updateData['status'] = 'cancelled';
                // Set staff_id if not already set
                if (!$documentRequest->staff_id) {
                    $updateData['staff_id'] = auth()->id();
                }
            }

            $documentRequest->update($updateData);

            // If payment is marked as paid and status is pending, automatically transition to in-progress
            if ($status === 'paid' && $documentRequest->status === 'pending') {
                $documentRequest->update(['status' => 'in-progress']);
                $documentRequest->user->notify(
                    new RequestEventNotification(RequestNotificationEvent::PaymentVerified, [
                        'reference_no' => $documentRequest->reference_number,
                    ])
                );
                $documentRequest->user->notify(
                    new RequestEventNotification(RequestNotificationEvent::DocumentInProgress, [
                        'reference_no' => $documentRequest->reference_number,
                    ])
                );
            } elseif ($status === 'paid') {
                $documentRequest->user->notify(
                    new RequestEventNotification(RequestNotificationEvent::PaymentVerified, [
                        'reference_no' => $documentRequest->reference_number,
                    ])
                );
            } elseif ($status === 'failed') {
                // Send payment failed notification
                $documentRequest->user->notify(
                    new RequestEventNotification(RequestNotificationEvent::PaymentFailed, [
                        'reference_no' => $documentRequest->reference_number,
                    ])
                );
                // Send cancellation notification if document status was automatically set to cancelled
                if (isset($updateData['status']) && $updateData['status'] === 'cancelled') {
                    $documentRequest->user->notify(
                        new RequestEventNotification(RequestNotificationEvent::DocumentCancelled, [
                            'reference_no' => $documentRequest->reference_number,
                        ])
                    );
                }
            }

            $this->resetPage();
            session()->flash('success', 'Payment status updated to ' . ucfirst($status) . ' successfully.');
        } catch (\Exception $e) {
            Log::error('Payment status update failed: ' . $e->getMessage());
            session()->flash('error', 'Failed to update payment status.');
        }
    }
}; ?>

<div>


    <!-- Flash Messages -->
    @include('components.alert')

    <!-- Header -->
    <div class="flux-card p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Process Documents</h1>
                <p class="text-gray-600 mt-1">View and process document requests for your assigned offices</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Assigned Offices:</span>
                <div class="flex flex-wrap gap-1">

                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ auth()->user()->getAssignedOffice()->name ?? 'No assigned office' }}
                    </span>

                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="flux-card p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="search" wire:model.live="search" class="form-control"
                    placeholder="Search by client name or email...">
            </div>
            <div class="flex-1" x-data="{ open: false }">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <div class="relative">
                    <button type="button"
                            @click="open = !open"
                            class="form-control w-full text-left flex items-center justify-between"
                            :class="{ 'border-blue-500': open }">
                            <div class="flex items-center justify-between">
                        <span>
                            @if($status === '')
                                All Status
                            @else
                                {{ ucfirst(str_replace('-', ' ', $status)) }}
                                @if(isset($statusCounts[$status]))
                                    <span class="inline-flex items-center justify-center w-5 h-5 ml-2 text-xs font-bold text-white bg-red-500 rounded-full">
                                        {{ $statusCounts[$status] }}
                                    </span>
                                @endif
                            @endif
                        </span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                        </div>
                    </button>

                    <div x-show="open"
                         @click.away="open = false"
                         x-transition
                         class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                        <div wire:click="$set('status', '')"
                             @click="open = false"
                             class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex items-center justify-between">
                            <span>All Status</span>
                        </div>
                        <div wire:click="$set('status', 'pending')"
                             @click="open = false"
                             class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex items-center justify-between">
                            <span>Pending</span>
                            @if(isset($statusCounts['pending']) && $statusCounts['pending'] > 0)
                                <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full">
                                    {{ $statusCounts['pending'] }}
                                </span>
                            @endif
                        </div>
                        <div wire:click="$set('status', 'in-progress')"
                             @click="open = false"
                             class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex items-center justify-between">
                            <span>In Progress</span>
                            @if(isset($statusCounts['in-progress']) && $statusCounts['in-progress'] > 0)
                                <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-blue-500 rounded-full">
                                    {{ $statusCounts['in-progress'] }}
                                </span>
                            @endif
                        </div>
                        <div wire:click="$set('status', 'ready-for-pickup')"
                             @click="open = false"
                             class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex items-center justify-between">
                            <span>Ready for Pickup</span>
                            @if(isset($statusCounts['ready-for-pickup']) && $statusCounts['ready-for-pickup'] > 0)
                                <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-green-500 rounded-full">
                                    {{ $statusCounts['ready-for-pickup'] }}
                                </span>
                            @endif
                        </div>
                        <div wire:click="markStatusAsSeen('complete')"
                             @click="open = false"
                             class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex items-center justify-between">
                            <span>Complete</span>
                            @if(isset($unseenCounts['complete']) && $unseenCounts['complete'] > 0)
                                <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-green-500 rounded-full">
                                    {{ $unseenCounts['complete'] }}
                                </span>
                            @endif
                        </div>
                        <div wire:click="markStatusAsSeen('cancelled')"
                             @click="open = false"
                             class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex items-center justify-between">
                            <span>Cancelled</span>
                            @if(isset($unseenCounts['cancelled']) && $unseenCounts['cancelled'] > 0)
                                <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full">
                                    {{ $unseenCounts['cancelled'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Hidden select for Livewire binding -->
                <select wire:model.live="status" class="hidden">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in-progress">In Progress</option>
                    <option value="ready-for-pickup">Ready for Pickup</option>
                    <option value="complete">Complete</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="flex-1">
                <label for="sortDirection" class="block text-sm font-medium text-gray-700 mb-1">Sort</label>
                <select id="sortDirection" wire:model.live="sortDirection" class="form-control">
                    <option value="desc">Newest First</option>
                    <option value="asc">Oldest First</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button wire:click="searchs" class="btn btn-primary">
                    <i class="bi bi-search me-2"></i>Search
                </button>
            </div>
        </div>
    </div>

    <!-- Document Requests Table -->
    <div class="flux-card" style="padding: 12px;">
        <div class="overflow-x-auto">
            <table class="table flux-table w-full">
                <thead>
                    <tr>
                        <th>Reference Number</th>
                        <th>Requestor</th>
                        <th>Document For</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentRequests as $documentRequest)
                                        <tr>
                                            <td>{{ $documentRequest->reference_number }}</td>
                                            <td>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $documentRequest->user?->first_name . ' ' . $documentRequest->user?->last_name ?? 'N/A' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $documentRequest->user?->email ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td>
                                                @if ($documentRequest->details)
                                                    @if (
                                                            ($documentRequest->details->first_name === 'N/A' && $documentRequest->details->last_name === 'N/A') ||
                                                            (empty($documentRequest->details->first_name) && empty($documentRequest->details->last_name))
                                                        )
                                                        {{-- Empty string if both are N/A or empty --}}
                                                    @else
                                                        {{ $documentRequest->details->first_name }}
                                                        {{ $documentRequest->details->last_name }}
                                                    @endif
                                                    <span
                                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $documentRequest->details->request_for === 'myself' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $documentRequest->details->request_for)) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">No details</span>
                                                @endif
                                            </td>
                                            <td>
                                                <p class="fw-semibold">{{ $documentRequest->service?->title ?? 'N/A' }}</p>
                                            </td>

                                            <td>
                                                <span class="flux-badge flux-badge-{{ match ($documentRequest->status) {
                            'pending' => 'warning',
                            'in-progress' => 'info',
                            'ready-for-pickup' => 'success',
                            'complete' => 'success',
                            'cancelled' => 'danger',
                            default => 'light',
                        } }}">
                                                    {{ ucfirst(str_replace('-', ' ', $documentRequest->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <p class="fw-semibold">
                                                    {{ $documentRequest->created_at?->format('M d, Y') ?? 'N/A' }}
                                                </p>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.view-document-request', Crypt::encryptString($documentRequest->id)) }}"
                                                        wire:navigate class="flux-btn btn-sm flux-btn-primary" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if ($documentRequest->status === 'pending')
                                                        <button wire:click="updatePaymentStatusFromTable({{ $documentRequest->id }}, 'paid')"
                                                            class="flux-btn btn-sm flux-btn-success" title="Mark as Paid"
                                                            wire:loading.attr="disabled">
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                        <button wire:click="setDocumentStatusToUpdate({{ $documentRequest->id }}, 'cancelled')"
                                                            class="flux-btn btn-sm flux-btn-danger" title="Cancel Request"
                                                            wire:loading.attr="disabled">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    @elseif ($documentRequest->status === 'in-progress')
                                                        <button wire:click="updateDocumentStatus({{ $documentRequest->id }}, 'ready-for-pickup')"
                                                            class="flux-btn btn-sm flux-btn-success" title="Ready for Pick Up"
                                                            wire:loading.attr="disabled">
                                                            <i class="bi bi-box-arrow-up"></i>
                                                        </button>
                                                        <button wire:click="setDocumentStatusToUpdate({{ $documentRequest->id }}, 'cancelled')"
                                                            class="flux-btn btn-sm flux-btn-danger" title="Cancel Request"
                                                            wire:loading.attr="disabled">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    @elseif ($documentRequest->status === 'ready-for-pickup')
                                                        <button wire:click="updateDocumentStatus({{ $documentRequest->id }}, 'complete')"
                                                            class="flux-btn btn-sm flux-btn-success" title="Mark as Complete"
                                                            wire:loading.attr="disabled">
                                                            <i class="bi bi-check2-circle"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8">
                                <div class="text-gray-500">
                                    <i class="bi bi-file-earmark-text text-4xl mb-2"></i>
                                    <p>No document requests found for your assigned offices.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="mt-6">
            {{ $documentRequests->links() }}
        </div>
    </div>

    <!-- Document Request Details Modal -->
    @if ($documentRequest)
        <div class="modal fade" id="documentRequestModal" tabindex="-1" aria-labelledby="documentRequestModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="documentRequestModalLabel">Document Request Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @include('livewire.documentrequest.components.view-document-request-contents')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        @if ($documentRequest->status === 'pending')
                            <div class="btn-group">
                                <button wire:click="updateDocumentStatus({{ $documentRequest->id }}, 'in-progress')"
                                    class="btn btn-info">
                                    <i class="bi bi-arrow-right-circle me-1"></i>Mark as In Progress
                                </button>
                                <button wire:click="updateDocumentStatus({{ $documentRequest->id }}, 'cancelled')"
                                    class="btn btn-danger">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </button>
                            </div>
                        @elseif ($documentRequest->status === 'in-progress')
                            <div class="btn-group">
                                <button wire:click="updateDocumentStatus({{ $documentRequest->id }}, 'cancelled')"
                                    class="btn btn-danger">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Document Request Confirmation Modal -->
    @include('livewire.documentrequest.components.modal.confirmation-document-request-modal')

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('viewDocumentRequest', (id) => {
                const modal = new bootstrap.Modal(document.getElementById('documentRequestModal'));
                modal.show();
            });
        });
    </script>
</div>
