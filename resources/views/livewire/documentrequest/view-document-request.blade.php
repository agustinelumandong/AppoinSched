<?php

use App\Models\DocumentRequest;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use App\Models\PersonalInformation;
use App\Models\UserFamily;
use App\Models\UserAddresses;

new class extends Component {
    public $id;
    public $documentRequest;
    public $remarks = '';
    public $confirmApproved = false;
    public $confirmRejected = false;
    public $confirmPending = false;
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
        // dd($this->confirmApproved, $this->confirmRejected, $this->confirmPending);
        try {
            if ($this->confirmApproved == true) {
                $this->documentRequest->update([
                    'status' => 'approved',
                    'remarks' => $this->remarks,
                ]);
                session()->flash('success', 'Document request approved successfully.');
            } elseif ($this->confirmRejected == true) {
                $this->documentRequest->update([
                    'status' => 'rejected',
                    'remarks' => $this->remarks,
                ]);
                session()->flash('error', 'Document request rejected successfully.');
            } elseif ($this->confirmPending == true) {
                $this->documentRequest->update([
                    'status' => 'pending',
                    'remarks' => $this->remarks,
                ]);
                session()->flash('error', 'Document request set to pending successfully.');
            }

            $this->resetConfirmationStates();
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating the document request.');
        }
    }

    public function resetConfirmationStates()
    {
        $this->confirmApproved = false;
        $this->confirmRejected = false;
        $this->confirmPending = false;
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
