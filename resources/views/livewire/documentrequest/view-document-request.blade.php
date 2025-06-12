<?php

use App\Models\DocumentRequest;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Crypt;

new class extends Component {
    public $id;
    public $documentRequest;
    public $remarks = '';
    public $confirmApproved = false;
    public $confirmRejected = false;
    public $confirmPending = false;

    public function mount($id)
    {
        $this->id = Crypt::decryptString($id);
        $this->documentRequest = DocumentRequest::with(['user', 'staff', 'office', 'service'])->findOrFail($this->id);
        $this->remarks = $this->documentRequest->remarks;
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
    <link rel="stylesheet" href="{{ asset('css/fluxUI.css') }}">

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

    <!-- Client Info -->
    <div class="flux-card p-6">
        <div class="flex items-center space-x-2 mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Client Information</h2>
        </div>

        <!-- Personal Identity Section -->
        <div class="mb-8">
            <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Personal Identity</h3>
            <div class="space-y-4">
                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->user->last_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->user->first_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->user->middle_name ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Birth Date</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->user->birthdate ? \Carbon\Carbon::parse($documentRequest->user->birthdate)->format('M d, Y') : 'January 15, 1990' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Birth Place</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->user->birthplace ?? 'Manila, Philippines' }}</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Gender</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->user->gender ? ucfirst($documentRequest->user->gender) : 'Male' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Civil Status</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->user->civil_status ? ucfirst($documentRequest->user->civil_status) : 'Single' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nationality</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->user->nationality ?? 'Filipino' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="mb-8">
            <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Contact Information</h3>
            <div class="space-y-4">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Email Address</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Phone Number</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->user->phone ?? '+63 912 345 6789' }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Complete Address</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                        {{ $documentRequest->user->address ?? '123 Sample Street, Sample City, Metro Manila' }}</p>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Barangay</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->user->barangay ?? 'Barangay Sample' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Occupation</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->user->occupation ?? 'Software Developer' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Family Information Section -->
        <div class="mb-8">
            <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Family Information</h3>
            <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Mother's Full Name</label>
                <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                    {{ $documentRequest->user->last_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                    {{ $documentRequest->user->first_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                    {{ $documentRequest->user->middle_name ?? 'N/A' }}</p>
                </div>
                </div>
                <div class="grid md:grid-cols-2 gap-6 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Birth Date</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                    {{ $documentRequest->user->mother_birthdate ? \Carbon\Carbon::parse($documentRequest->user->mother_birthdate)->format('M d, Y') : 'N/A' }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Place of Birth</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                    {{ $documentRequest->user->mother_birthplace ?? 'N/A' }}
                    </p>
                </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Father's Full Name</label>
                <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                    {{ $documentRequest->user->last_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                    {{ $documentRequest->user->first_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                    {{ $documentRequest->user->middle_name ?? 'N/A' }}</p>
                </div>
                </div>
                <div class="grid md:grid-cols-2 gap-6 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Birth Date</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                    {{ $documentRequest->user->father_birthdate ? \Carbon\Carbon::parse($documentRequest->user->father_birthdate)->format('M d, Y') : 'N/A' }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Place of Birth</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                    {{ $documentRequest->user->father_birthplace ?? 'N/A' }}
                    </p>
                </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Spouse's Full Name</label>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                    {{ $documentRequest->user->last_name }}</p>
                </div>
                <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                    {{ $documentRequest->user->first_name }}</p>
                </div>
                <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                    {{ $documentRequest->user->middle_name ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-6 mt-4">
                <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Birth Date</label>
                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                    {{ $documentRequest->user->spouse_birthdate ? \Carbon\Carbon::parse($documentRequest->user->spouse_birthdate)->format('M d, Y') : 'N/A' }}
                </p>
                </div>
                <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Place of Birth</label>
                <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                    {{ $documentRequest->user->spouse_birthplace ?? 'N/A' }}
                </p>
                </div>
            </div>
            </div>
        </div>

        <!-- Partner Information Section -->
        <div class="mb-8">
            <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Partner Information</h3>
            <div class="space-y-4">
                <div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Partner's Full Name</label>
                    </div>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Last Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->user->last_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">First Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->user->first_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Middle Name</label>
                            <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                                {{ $documentRequest->user->middle_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Date of Marriage</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->user->marriage_date ? \Carbon\Carbon::parse($documentRequest->user->marriage_date)->format('M d, Y') : 'Not applicable' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Place of Marriage</label>
                        <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                            {{ $documentRequest->user->marriage_place ?? 'Not applicable' }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Partner's Occupation</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                        {{ $documentRequest->user->partner_occupation ?? 'Not applicable' }}</p>
                </div>
            </div>
        </div>

        <!-- Identification Documents Section -->
        <div class="mb-6">
            <h3 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Identification Documents
            </h3>
            <div class="grid md:grid-cols-3 gap-6 items-start">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Valid ID Type</label>
                    <p class="text-gray-900 pb-2 border-b border-gray-300 form-field-underline">
                        {{ $documentRequest->user->valid_id_type ?? 'Driver\'s License' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">ID Number</label>
                    <p class="text-gray-900 font-mono text-sm pb-2 border-b border-gray-300 form-field-underline">
                        {{ $documentRequest->user->valid_id_number ?? 'DL-12-345678-90' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">Photo</label>
                    @if ($documentRequest->user->photo_path)
                        <img src="{{ asset('storage/' . $documentRequest->user->photo_path) }}" alt="Client Photo"
                            class="w-20 h-20 rounded-lg object-cover border-2 border-gray-200 shadow-sm">
                    @else
                        <div
                            class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-gray-200">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Requested Service -->
    <div class="flux-card p-6">
        <div class="flex items-center space-x-2 mb-4">
            <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Requested Service</h4>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service Title</label>
                <p class="text-gray-900 font-medium">{{ $documentRequest->service->title }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Office</label>
                <p class="text-gray-900">{{ $documentRequest->office->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assigned Staff</label>
                <p class="text-gray-900">{{ $documentRequest->staff->first_name }}
                    {{ $documentRequest->staff->last_name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service Description</label>
                <p class="text-gray-600 text-sm">
                    {{ $documentRequest->service->description ?? 'No description available' }}</p>
            </div>
        </div>
    </div>

    <!-- Uploaded Attachments -->
    <div class="flux-card p-6">
        <div class="flex items-center space-x-2 mb-4">
            <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Uploaded Attachments</h4>
        </div>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path
                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <p class="mt-2 text-sm text-gray-600">No attachments uploaded yet</p>
            <p class="text-xs text-gray-500">(Feature coming soon)</p>
        </div>
    </div>

    <!-- Payment Status -->
    <div class="flux-card p-6">
        <div class="flex items-center space-x-2 mb-4">
            <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Payment Status</h4>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">Payment Required</p>
                    <p class="text-xs text-gray-500">Payment processing feature coming soon</p>
                </div>
                <span class="flux-badge flux-badge-warning">Pending</span>
            </div>
        </div>
    </div>

    <!-- Remarks Input -->
    <div class="flux-card p-6">
        <div class="flex items-center space-x-2 mb-4">
            <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Remarks</h4>
            <span class="text-sm text-gray-500">(Optional)</span>
        </div>
        <textarea wire:model="remarks" class="flux-form-control w-full h-24 resize-none"
            placeholder="Add any remarks or notes about this request..."></textarea>
    </div>

    <!-- Action Buttons -->
    <div class="flux-card p-6">
        <div class="flex items-center space-x-2 mb-4">
            <h4 class="text-md font-medium text-gray-800 mb-4 pb-2 border-b border-gray-200">Actions</h4>
        </div>
        <div class="action-buttons">
            <button wire:click="$set('confirmApproved', true); $dispatch('open-modal-confirm-document-request')"
                class="flux-btn flux-btn-success flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Approve Request
            </button>

            <button wire:click="$set('confirmRejected', true); $dispatch('open-modal-confirm-document-request')"
                class="flux-btn flux-btn-danger flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
                Reject Request
            </button>

            <button wire:click="$set('confirmPending', true); $dispatch('open-modal-confirm-document-request')"
                class="flux-btn flux-btn-secondary flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Set to Pending
            </button>

            <a href="{{ route('admin.document-request') }}" class="flux-btn flux-btn-outline flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>
                    Back to List</span>
            </a>
        </div>
    </div>

    @if (session()->has('success'))
    <div id="success-notification" class="fixed top-4 right-4 z-50 max-w-md opacity-100 transition-all duration-300" x-init="setTimeout(() => closeNotification('success-notification'), 5000)">
        <div class="flux-card p-4 bg-green-50 border-green-200 shadow-lg rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-green-800 font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="closeNotification('success-notification')" class="text-green-600 hover:text-green-800 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @elseif (session()->has('error'))
    <div id="error-notification" class="fixed top-4 right-4 z-50 max-w-md opacity-100 transition-all duration-300" x-init="setTimeout(() => closeNotification('error-notification'), 5000)">
        <div class="flux-card p-4 bg-red-50 border-red-200 shadow-lg rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="text-red-800 font-medium">{{ session('error') }}</span>
                </div>
                <button onclick="closeNotification('error-notification')" class="text-red-600 hover:text-red-800 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    <script>
        function closeNotification(id) {
            const notification = document.getElementById(id);
            if (notification) {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }
        }
    </script>

    <style>
        #success-notification, #error-notification {
            transition: all 0.3s ease;
            transform: translateX(0);
        }
    </style>
    @include('livewire.documentrequest.components.modal.confirmation-document-request-modal')

</div>



