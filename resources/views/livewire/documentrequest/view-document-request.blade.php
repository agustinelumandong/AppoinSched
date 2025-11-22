<?php

use App\Models\DocumentRequest;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\PersonalInformation;
use App\Models\UserFamily;
use App\Models\UserAddresses;
use App\Notifications\RequestEventNotification;
use App\Enums\RequestNotificationEvent;

new class extends Component {
    public $id;
    public $documentRequest;
    public $remarks = '';
    public $confirmApproved = false;
    public $confirmRejected = false;
    public $confirmPending = false;
    public $confirmPaymentStatus = false;
    public $paymentStatusToUpdate = '';
    public $documentStatusToUpdate = '';
    public $personalInformation;
    public $userFamily;
    public $userAddresses;

    public function mount($id)
    {
        $this->id = Crypt::decryptString($id);
        $this->documentRequest = DocumentRequest::with(['user', 'staff', 'office', 'service', 'details'])->findOrFail($this->id);
        $this->remarks = $this->documentRequest->remarks;

        if ($this->documentRequest->details) {
            $this->personalInformation = $this->documentRequest->details;
            $this->userAddresses = $this->documentRequest->details;
            $this->userFamily = $this->documentRequest->details;
        } else {
            // Legacy fallback
            $user = $this->documentRequest->user;
            $this->personalInformation = $user->personalInformation;
            $this->userAddresses = $user->userAddresses->first();
            $this->userFamily = $user->userFamilies->first();
        }
    }

    // Deprecated: Use updateDocumentStatusModal('in-progress') instead
    public function approve()
    {
        $this->documentRequest->update([
            'status' => 'in-progress',
            'remarks' => $this->remarks,
        ]);

        session()->flash('success', 'Document request set to in-progress successfully.');
    }

    // Deprecated: Use updateDocumentStatusModal('cancelled') instead
    public function reject()
    {
        $this->documentRequest->update([
            'status' => 'cancelled',
            'remarks' => $this->remarks,
        ]);

        session()->flash('error', 'Document request cancelled.');
    }

    public function pending()
    {
        $this->documentRequest->update([
            'status' => 'pending',
            'remarks' => $this->remarks,
        ]);

        session()->flash('success', 'Document request set to pending.');
    }

    public function confirmDocumentRequest()
    {
        try {
            // Validate remarks for required actions
            if ($this->confirmPaymentStatus && $this->paymentStatusToUpdate === 'failed') {
                if (empty(trim($this->remarks))) {
                    session()->flash('error', 'Remarks are required when marking payment as failed.');
                    $this->dispatch('keep-modal-open');
                    return;
                }
            }

            if ($this->documentStatusToUpdate === 'cancelled') {
                if (empty(trim($this->remarks))) {
                    session()->flash('error', 'Remarks are required when cancelling a document request.');
                    $this->dispatch('keep-modal-open');
                    return;
                }
            }

            // Handle document status confirmations
            if (!empty($this->documentStatusToUpdate)) {
                $this->updateDocumentStatusModal($this->documentStatusToUpdate);
                session()->flash('success', 'Document request status updated to ' . ucfirst(str_replace('-', ' ', $this->documentStatusToUpdate)) . ' successfully. Client has been notified via email.');
            }
            // Handle payment status confirmations
            elseif ($this->confirmPaymentStatus == true && !empty($this->paymentStatusToUpdate)) {
                $this->updatePaymentStatus($this->paymentStatusToUpdate);
                session()->flash('success', 'Payment status updated to ' . ucfirst($this->paymentStatusToUpdate) . ' successfully.');
            }

            // Only close modal and reset if we got here (validation passed)
            $this->dispatch('close-modal-confirm-document-request');
            $this->resetConfirmationStates();
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating the request: ' . $e->getMessage());
            $this->dispatch('keep-modal-open');
        }
    }

    public function resetConfirmationStates()
    {
        $this->confirmApproved = false;
        $this->confirmRejected = false;
        $this->confirmPending = false;
        $this->confirmPaymentStatus = false;
        $this->paymentStatusToUpdate = '';
        $this->documentStatusToUpdate = '';
    }

    public function setPaymentStatusToUpdate(string $status): void
    {
        $this->confirmPaymentStatus = true;
        $this->paymentStatusToUpdate = $status;
        $this->dispatch('open-modal-confirm-document-request');
    }

    public function updatePaymentStatus(string $status): void
    {
        try {
            // Get the current payment status before updating
            $currentPaymentStatus = $this->documentRequest->payment_status;

            $updateData = [
                'payment_status' => $status,
            ];

            // If changing from failed to paid or unpaid, clear remarks
            if ($currentPaymentStatus === 'failed' && ($status === 'paid' || $status === 'unpaid')) {
                $updateData['remarks'] = null;
                $this->remarks = ''; // Clear the component's remarks property as well
            } elseif (!empty(trim($this->remarks))) {
                // Include remarks in update if provided (and not clearing them)
                $updateData['remarks'] = $this->remarks;
            }

            $this->documentRequest->update($updateData);

            // Send appropriate notification based on payment status
            switch ($status) {
                case 'paid':
                    $this->documentRequest->user->notify(
                        new RequestEventNotification(RequestNotificationEvent::PaymentVerified, [
                            'reference_no' => $this->documentRequest->reference_number,
                        ]),
                    );
                    break;

                case 'failed':
                    $this->documentRequest->user->notify(
                        new RequestEventNotification(RequestNotificationEvent::PaymentFailed, [
                            'reference_no' => $this->documentRequest->reference_number,
                        ]),
                    );
                    break;

                case 'unpaid':
                    // No specific notification for unpaid status
                    break;
            }

            // Apply automatic transitions for all payment status changes
            $this->handleStatusTransitions('payment_status', $status);

            // Refresh the document request to get updated data
            $this->documentRequest = DocumentRequest::with(['user', 'staff', 'office', 'service', 'details'])->findOrFail($this->documentRequest->id);

            // Update remarks property to match the database
            $this->remarks = $this->documentRequest->remarks ?? '';

            session()->flash('success', 'Payment status updated to ' . ucfirst($status) . ' successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update payment status.');
        }
    }

    public function updateDocumentStatus(string $status): void
    {
        try {
            $updateData = [
                'status' => $status,
                'remarks' => $this->remarks,
            ];

            // Set staff_id if not already set
            if (!$this->documentRequest->staff_id) {
                $updateData['staff_id'] = auth()->id();
            }

            // Validate status
            if (!in_array($status, \App\Models\DocumentRequest::VALID_STATUSES)) {
                session()->flash('error', 'Invalid status value');
                return;
            }

            $this->documentRequest->update($updateData);

            if ($status === 'in-progress') {
                $this->documentRequest->user->notify(
                    new RequestEventNotification(RequestNotificationEvent::DocumentInProgress, [
                        'reference_no' => $this->documentRequest->reference_number,
                    ]),
                );
            } elseif ($status === 'cancelled') {
                $this->documentRequest->user->notify(
                    new RequestEventNotification(RequestNotificationEvent::DocumentCancelled, [
                        'reference_no' => $this->documentRequest->reference_number,
                    ]),
                );
            }

            session()->flash('success', 'Document request status updated to ' . ucfirst($status) . ' successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update document request status.');
        }
    }

    public function setDocumentStatusToUpdate(string $status): void
    {
        // Validate status
        if (!in_array($status, \App\Models\DocumentRequest::VALID_STATUSES)) {
            session()->flash('error', 'Invalid status value');
            return;
        }

        $this->documentStatusToUpdate = $status;

        if ($status === 'in-progress') {
            $this->confirmApproved = true; // Reuse confirmApproved for in-progress
        } elseif ($status === 'cancelled') {
            $this->confirmRejected = true; // Reuse confirmRejected for cancelled
        } elseif ($status === 'pending') {
            $this->confirmPending = true;
        } else {
            // For other statuses, reset confirmation flags
            $this->confirmApproved = false;
            $this->confirmRejected = false;
            $this->confirmPending = false;
        }

        $this->paymentStatusToUpdate = '';
        $this->confirmPaymentStatus = false;
        $this->dispatch('open-modal-confirm-document-request');
    }

    public function handleStatusTransitions(string $triggerType, string $newStatus): void
    {
        try {
            // Refresh the document request to get the latest data
            $this->documentRequest = DocumentRequest::with(['user', 'staff', 'office', 'service', 'details'])->findOrFail($this->documentRequest->id);

            // Handle payment status transitions
            // Note: Automatic status transition from pending to in-progress when payment_status changes to paid
            // is now handled by DocumentRequest model's boot() method
            if ($triggerType === 'payment_status') {
                if ($newStatus === 'paid') {
                    // The model boot() method will automatically set status to 'in-progress' if currently 'pending'
                    // Send notification if status was automatically changed
                    if ($this->documentRequest->status === 'in-progress' && $this->documentRequest->getOriginal('status') === 'pending') {
                        $this->documentRequest->user->notify(
                            new RequestEventNotification(RequestNotificationEvent::DocumentInProgress, [
                                'reference_no' => $this->documentRequest->reference_number,
                            ]),
                        );

                        session()->flash('success', 'Payment verified. Document request status automatically updated to In Progress.');
                    }
                } elseif ($newStatus === 'failed') {
                    // When payment is marked as failed, automatically set document status to cancelled
                    if ($this->documentRequest->status !== 'cancelled') {
                        $updateData = [
                            'status' => 'cancelled',
                        ];

                        // Set staff_id if not already set
                        if (!$this->documentRequest->staff_id) {
                            $updateData['staff_id'] = auth()->id();
                        }

                        // Include remarks if provided
                        if (!empty(trim($this->remarks))) {
                            $updateData['remarks'] = $this->remarks;
                        }

                        $this->documentRequest->update($updateData);

                        // Send cancellation notification
                        $this->documentRequest->user->notify(
                            new RequestEventNotification(RequestNotificationEvent::DocumentCancelled, [
                                'reference_no' => $this->documentRequest->reference_number,
                            ]),
                        );

                        session()->flash('info', 'Payment failed. Document request status automatically set to Cancelled.');
                    }
                } elseif ($newStatus === 'unpaid') {
                    // When payment is marked as unpaid, ensure document status reflects this
                    if ($this->documentRequest->status !== 'pending') {
                        $this->documentRequest->update([
                            'status' => 'pending',
                            'remarks' => $this->remarks ? $this->remarks : 'Set to pending due to unpaid status',
                        ]);

                        session()->flash('info', 'Payment marked as unpaid. Document request status set to Pending.');
                    }
                }
            }
            // Handle document status transitions
            elseif ($triggerType === 'document_status') {
                switch ($newStatus) {
                    case 'in-progress':
                        // If moving to in-progress, ensure staff_id is set
                        if (!$this->documentRequest->staff_id) {
                            $this->documentRequest->update([
                                'staff_id' => auth()->id(),
                            ]);
                        }

                        // Payment status is managed independently by staff during verification
                        // No automatic payment status update when document moves to in-progress
                        break;

                    case 'cancelled':
                        // If cancelled, ensure staff_id is set
                        if (!$this->documentRequest->staff_id) {
                            $this->documentRequest->update([
                                'staff_id' => auth()->id(),
                            ]);
                        }

                        // When document status is cancelled, automatically set payment_status to failed
                        if ($this->documentRequest->payment_status !== 'failed') {
                            $this->documentRequest->update([
                                'payment_status' => 'failed',
                            ]);

                            // Send payment failed notification
                            $this->documentRequest->user->notify(
                                new RequestEventNotification(RequestNotificationEvent::PaymentFailed, [
                                    'reference_no' => $this->documentRequest->reference_number,
                                ]),
                            );

                            session()->flash('info', 'Payment status automatically updated to Failed.');
                        }
                        break;

                    case 'pending':
                        // Reset certain fields when moving back to pending
                        $this->documentRequest->update([
                            'pickup_ready_date' => null,
                        ]);

                        // Set payment to unpaid if it's not already paid
                        if ($this->documentRequest->payment_status !== 'paid') {
                            $this->documentRequest->update([
                                'payment_status' => 'unpaid',
                            ]);

                            session()->flash('info', 'Payment status automatically updated to Unpaid.');
                        }
                        break;
                }
            }

            // Refresh the document request after all changes
            $this->documentRequest = DocumentRequest::with(['user', 'staff', 'office', 'service', 'details'])->findOrFail($this->documentRequest->id);
        } catch (\Exception $e) {
            // Log error but don't show to user as this is an automatic process
            \Illuminate\Support\Facades\Log::error('Error in automatic status transition: ' . $e->getMessage());
        }
    }

    public function updateDocumentStatusModal(string $status): void
    {
        try {
            $updateData = [
                'status' => $status,
            ];

            // Include remarks in update if provided
            if (!empty(trim($this->remarks))) {
                $updateData['remarks'] = $this->remarks;
            }

            // Set staff_id if not already set
            if (!$this->documentRequest->staff_id) {
                $updateData['staff_id'] = auth()->id();
            }

            // When document status is cancelled, automatically set payment_status to failed
            if ($status === 'cancelled' && $this->documentRequest->payment_status !== 'failed') {
                $updateData['payment_status'] = 'failed';
            }

            // Validate status
            if (!in_array($status, \App\Models\DocumentRequest::VALID_STATUSES)) {
                session()->flash('error', 'Invalid status value');
                return;
            }

            $this->documentRequest->update($updateData);

            // Send payment failed notification if payment status was automatically set to failed
            if ($status === 'cancelled' && isset($updateData['payment_status']) && $updateData['payment_status'] === 'failed') {
                $this->documentRequest->user->notify(
                    new RequestEventNotification(RequestNotificationEvent::PaymentFailed, [
                        'reference_no' => $this->documentRequest->reference_number,
                    ]),
                );
            }

            // Set completed_date when status is complete
            if ($status === 'complete') {
                $this->documentRequest->update(['completed_date' => now()]);
            }

            // Send appropriate notification based on status
            switch ($status) {
                case 'in-progress':
                    $this->documentRequest->user->notify(
                        new RequestEventNotification(RequestNotificationEvent::DocumentInProgress, [
                            'reference_no' => $this->documentRequest->reference_number,
                        ]),
                    );
                    break;

                case 'ready-for-pickup':
                    $this->documentRequest->user->notify(
                        new RequestEventNotification(RequestNotificationEvent::DocumentReadyForPickup, [
                            'reference_no' => $this->documentRequest->reference_number,
                        ]),
                    );
                    // Send claim slip notification
                    $this->documentRequest->user->notify(
                        new \App\Notifications\DocumentClaimSlipNotification($this->documentRequest)
                    );
                    break;

                case 'complete':
                    $this->documentRequest->user->notify(
                        new RequestEventNotification(RequestNotificationEvent::DocumentCompleted, [
                            'reference_no' => $this->documentRequest->reference_number,
                        ]),
                    );
                    break;

                case 'cancelled':
                    $this->documentRequest->user->notify(
                        new RequestEventNotification(RequestNotificationEvent::DocumentCancelled, [
                            'reference_no' => $this->documentRequest->reference_number,
                        ]),
                    );
                    break;

                case 'pending':
                    // No specific notification for pending status
                    break;
            }

            // Handle automatic transitions for all document status changes
            $this->handleStatusTransitions('document_status', $status);

            session()->flash('success', 'Document request status updated to ' . ucfirst($status) . ' successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update document request status.');
        }
    }
}; ?>

<div class="max-w-4xl mx-auto p-6 space-y-6">

    @include('components.alert')

    <!-- Header -->
    <div class="flux-card p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Request #{{ $documentRequest->id }}</h1>
                    <p class="text-sm text-gray-500">Document Request Details</p>

                    <!-- NEW: Add request type indicator -->
                    @if ($documentRequest->details)
                        <div class="mt-2 flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Requested by:</span>
                            <span class="font-medium text-gray-900">{{ $documentRequest->user->name }}</span>

                            <!-- Request type badge -->
                            <span
                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                                                                                                                                                                                                                                {{ $documentRequest->details->request_for === 'myself' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $documentRequest->details->request_for)) }}
                            </span>
                        </div>

                        <!-- Show beneficiary name if different from requester -->
                        @if ($documentRequest->details->request_for === 'someone_else')
                            <div class="mt-1">
                                <span class="text-sm text-gray-600">Document for:</span>
                                <span class="font-medium text-gray-900">
                                    {{ $documentRequest->details->first_name }}
                                    {{ $documentRequest->details->last_name }}
                                </span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            <div class="text-right">
                <span
                    class="flux-badge flux-badge-{{ match ($documentRequest->status) {
                        'pending' => 'warning',
                        'in-progress' => 'info',
                        'ready-for-pickup' => 'success',
                        'complete' => 'success',
                        'cancelled' => 'danger',
                        default => 'light',
                    } }}">
                    {{ ucfirst(str_replace('-', ' ', $documentRequest->status)) }}
                </span>
                <p class="text-sm text-gray-500 mt-1">{{ $documentRequest->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- NEW: Add warning banner for third-party requests -->
    @if ($documentRequest->details && $documentRequest->details->request_for === 'someone_else')
        <div class="flux-card p-4 bg-amber-50 border-l-4 border-amber-400 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-amber-800">Third-Party Document Request</h3>
                    <p class="text-sm text-amber-700 mt-1">
                        This document was requested by <strong>{{ $documentRequest->user->name }}</strong>
                        ({{ $documentRequest->user->email }}) for
                        <strong>{{ $documentRequest->details->first_name }}
                            {{ $documentRequest->details->last_name }}</strong>.
                        The information below shows the beneficiary's details as submitted with this request.
                    </p>
                </div>
            </div>
        </div>
    @endif

    @include('livewire.documentrequest.components.view-document-request-contents')

    @include('livewire.documentrequest.components.modal.confirmation-document-request-modal')

</div>
