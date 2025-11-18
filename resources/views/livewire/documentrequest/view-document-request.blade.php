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

    public function approve()
    {
        $this->documentRequest->update([
            'status' => 'approved',
            'remarks' => $this->remarks,
        ]);

        session()->flash('success', 'Document request approved successfully.');
    }

    public function reject()
    {
        $this->documentRequest->update([
            'status' => 'rejected',
            'remarks' => $this->remarks,
        ]);

        session()->flash('error', 'Document request rejected.');
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
            // Handle document status confirmations
            if ($this->confirmApproved == true) {
                $this->updateDocumentStatusModal('approved');
                session()->flash('success', 'Document request approved successfully.');
            } elseif ($this->confirmRejected == true) {
                $this->updateDocumentStatusModal('rejected');
                session()->flash('error', 'Document request rejected successfully.');
            } elseif ($this->confirmPending == true) {
                $this->updateDocumentStatusModal('pending');
                session()->flash('error', 'Document request set to pending successfully.');
            }
            // Handle payment status confirmations
            elseif ($this->confirmPaymentStatus == true && !empty($this->paymentStatusToUpdate)) {
                $this->updatePaymentStatus($this->paymentStatusToUpdate);
                session()->flash('success', 'Payment status updated to ' . ucfirst($this->paymentStatusToUpdate) . ' successfully.');
            }

            $this->resetConfirmationStates();
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating the request: ' . $e->getMessage());
        }
    }

    public function resetConfirmationStates()
    {
        $this->confirmApproved = false;
        $this->confirmRejected = false;
        $this->confirmPending = false;
        $this->confirmPaymentStatus = false;
        $this->paymentStatusToUpdate = '';
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
            $this->documentRequest->update([
                'payment_status' => $status,
            ]);

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

            // Set completed_date if status is completed
            if ($status === 'completed') {
                $updateData['completed_date'] = now();
            }

            $this->documentRequest->update($updateData);

            if ($status === 'approved') {
                $this->documentRequest->user->notify(
                    new RequestEventNotification(RequestNotificationEvent::DocumentApproved, [
                        'reference_no' => $this->documentRequest->reference_number,
                    ]),
                );
            } elseif ($status === 'rejected') {
                $this->documentRequest->user->notify(
                    new RequestEventNotification(RequestNotificationEvent::DocumentRejected, [
                        'reference_no' => $this->documentRequest->reference_number,
                    ]),
                );
            } elseif ($status === 'cancelled') {
                $this->documentRequest->user->notify(
                    new RequestEventNotification(RequestNotificationEvent::DocumentCancelled, [
                        'reference_no' => $this->documentRequest->reference_number,
                    ]),
                );
            } elseif ($status === 'completed') {
                $this->documentRequest->user->notify(
                    new RequestEventNotification(RequestNotificationEvent::DocumentCompleted, [
                        'reference_no' => $this->documentRequest->reference_number,
                    ]),
                );
            } elseif ($status === 'in-progress') {
                $this->documentRequest->user->notify(
                    new RequestEventNotification(RequestNotificationEvent::DocumentInProgress, [
                        'reference_no' => $this->documentRequest->reference_number,
                    ]),
                );
            } elseif ($status === 'ready-for-pickup') {
                $this->documentRequest->user->notify(
                    new RequestEventNotification(RequestNotificationEvent::DocumentReadyForPickup, [
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
        if ($status === 'approved') {
            $this->confirmApproved = true;
        } elseif ($status === 'rejected') {
            $this->confirmRejected = true;
        } elseif ($status === 'pending') {
            $this->confirmPending = true;
        } else {
            // For other statuses, we'll use a generic confirmation
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
            if ($triggerType === 'payment_status') {
                if ($newStatus === 'paid') {
                    // When payment is marked as paid, update document status to in-progress
                    if ($this->documentRequest->status !== 'in-progress' && $this->documentRequest->status !== 'completed' && $this->documentRequest->status !== 'ready-for-pickup') {
                        $this->documentRequest->update([
                            'status' => 'in-progress',
                            'remarks' => $this->remarks ? $this->remarks : 'Automatically set to in-progress after payment verification',
                            'staff_id' => auth()->id(),
                        ]);

                        // Send notification for automatic status change
                        $this->documentRequest->user->notify(
                            new RequestEventNotification(RequestNotificationEvent::DocumentInProgress, [
                                'reference_no' => $this->documentRequest->reference_number,
                            ]),
                        );

                        session()->flash('success', 'Payment verified. Document request status automatically updated to In Progress.');
                    }
                } elseif ($newStatus === 'failed') {
                    // When payment is marked as failed, update document status to pending
                    if ($this->documentRequest->status !== 'pending') {
                        $this->documentRequest->update([
                            'status' => 'pending',
                            'remarks' => $this->remarks ? $this->remarks : 'Set to pending due to payment failure',
                        ]);

                        session()->flash('info', 'Payment failed. Document request status set to Pending.');
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
                    case 'ready-for-pickup':
                        // Set pickup_ready_date if not already set
                        if (!$this->documentRequest->pickup_ready_date) {
                            $this->documentRequest->update([
                                'pickup_ready_date' => now(),
                            ]);
                        }

                        // Update payment status if not already paid
                        if ($this->documentRequest->payment_status !== 'paid') {
                            $this->documentRequest->update([
                                'payment_status' => 'paid',
                            ]);

                            // Send payment notification
                            $this->documentRequest->user->notify(
                                new RequestEventNotification(RequestNotificationEvent::PaymentVerified, [
                                    'reference_no' => $this->documentRequest->reference_number,
                                ]),
                            );

                            session()->flash('info', 'Payment status automatically updated to Paid.');
                        }
                        break;

                    case 'completed':
                        // Set completed_date if not already set
                        if (!$this->documentRequest->completed_date) {
                            $this->documentRequest->update([
                                'completed_date' => now(),
                            ]);
                        }

                        // Ensure payment is marked as paid
                        if ($this->documentRequest->payment_status !== 'paid') {
                            $this->documentRequest->update([
                                'payment_status' => 'paid',
                            ]);

                            // Send payment notification
                            $this->documentRequest->user->notify(
                                new RequestEventNotification(RequestNotificationEvent::PaymentVerified, [
                                    'reference_no' => $this->documentRequest->reference_number,
                                ]),
                            );

                            session()->flash('info', 'Payment status automatically updated to Paid.');
                        }
                        break;

                    case 'approved':
                        // If approved, ensure staff_id is set
                        if (!$this->documentRequest->staff_id) {
                            $this->documentRequest->update([
                                'staff_id' => auth()->id(),
                            ]);
                        }

                        // If payment is already verified, move to in-progress
                        if ($this->documentRequest->payment_status === 'paid') {
                            $this->documentRequest->update([
                                'status' => 'in-progress',
                                'remarks' => $this->remarks ? $this->remarks : 'Automatically set to in-progress after approval',
                            ]);

                            // Send notification for automatic status change
                            $this->documentRequest->user->notify(
                                new RequestEventNotification(RequestNotificationEvent::DocumentInProgress, [
                                    'reference_no' => $this->documentRequest->reference_number,
                                ]),
                            );

                            session()->flash('info', 'Document request automatically moved to In Progress.');
                        }
                        break;

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

                    case 'rejected':
                        // If rejected, ensure staff_id is set
                        if (!$this->documentRequest->staff_id) {
                            $this->documentRequest->update([
                                'staff_id' => auth()->id(),
                            ]);
                        }

                        // If payment is not failed, set it to failed
                        if ($this->documentRequest->payment_status !== 'failed' && $this->documentRequest->payment_status !== 'unpaid') {
                            $this->documentRequest->update([
                                'payment_status' => 'failed',
                            ]);

                            // Send payment notification
                            $this->documentRequest->user->notify(
                                new RequestEventNotification(RequestNotificationEvent::PaymentFailed, [
                                    'reference_no' => $this->documentRequest->reference_number,
                                ]),
                            );

                            session()->flash('info', 'Payment status automatically updated to Failed.');
                        }
                        break;

                    case 'cancelled':
                        // If cancelled, ensure staff_id is set
                        if (!$this->documentRequest->staff_id) {
                            $this->documentRequest->update([
                                'staff_id' => auth()->id(),
                            ]);
                        }

                        // If payment is not unpaid, set it to unpaid
                        if ($this->documentRequest->payment_status !== 'unpaid') {
                            $this->documentRequest->update([
                                'payment_status' => 'unpaid',
                            ]);

                            session()->flash('info', 'Payment status automatically updated to Unpaid.');
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
                'remarks' => $this->remarks,
            ];

            // Set staff_id if not already set
            if (!$this->documentRequest->staff_id) {
                $updateData['staff_id'] = auth()->id();
            }

            // Set completed_date if status is completed
            if ($status === 'completed') {
                $updateData['completed_date'] = now();
            }

            // Set pickup_ready_date if status is ready-for-pickup
            if ($status === 'ready-for-pickup') {
                $updateData['pickup_ready_date'] = now();
            }

            $this->documentRequest->update($updateData);

            // Send appropriate notification based on status
            switch ($status) {
                case 'approved':
                    $this->documentRequest->user->notify(
                        new RequestEventNotification(RequestNotificationEvent::DocumentApproved, [
                            'reference_no' => $this->documentRequest->reference_number,
                        ]),
                    );
                    break;

                case 'rejected':
                    $this->documentRequest->user->notify(
                        new RequestEventNotification(RequestNotificationEvent::DocumentRejected, [
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

                case 'completed':
                    $this->documentRequest->user->notify(
                        new RequestEventNotification(RequestNotificationEvent::DocumentCompleted, [
                            'reference_no' => $this->documentRequest->reference_number,
                        ]),
                    );
                    break;

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
                    class="flux-badge flux-badge-{{ $documentRequest->status === 'pending' ? 'warning' : ($documentRequest->status === 'approved' ? 'success' : 'danger') }}">
                    {{ ucfirst($documentRequest->status) }}
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
