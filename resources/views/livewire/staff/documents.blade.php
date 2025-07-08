<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\DocumentRequest;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use App\Models\Offices;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public ?int $selectedOfficeId = null;
    public ?int $documentRequestId = null;
    public ?DocumentRequest $documentRequest = null;

    public function mount(): void
    {
        // Check if user has staff role
        if (
            !auth()
                ->user()
                ->hasAnyRole(['MCR-staff', 'MTO-staff', 'BPLS-staff', 'admin', 'super-admin'])
        ) {
            abort(403, 'Unauthorized access');
        }
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

    public function updateDocumentStatus(int $id, string $status): void
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            // Check if staff is assigned to this document request's office
            if (!auth()->user()->isAssignedToOffice($documentRequest->office_id)) {
                session()->flash('error', 'You are not authorized to update this document request');
                return;
            }

            $validated = $this->validate([
                'status' => 'required|string|in:pending,approved,rejected,completed',
            ]);

            $updateData = [
                'status' => $status,
                'staff_id' => auth()->id(),
            ];

            if ($status === 'completed') {
                $updateData['completed_date'] = now();
            }

            $updated = $documentRequest->update($updateData);

            if ($updated) {
                $this->dispatch('documentRequestUpdated');
                session()->flash('success', 'Document request status updated successfully');
            } else {
                session()->flash('error', 'Failed to update document request status');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = collect($e->errors())->flatten();
            session()->flash('error', 'Validation failed: ' . $errors->first());
        } catch (\Exception $e) {
            Log::error('Document request status update failed: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while updating the document request status');
        }
    }
    public function getOfficeIdForStaff(): ?int
    {
        if (auth()->user()->hasRole('MCR-staff')) {
            return Offices::where('slug', 'municipal-civil-registrar')->value('id');
        }
        if (auth()->user()->hasRole('MTO-staff')) {
            return Offices::where('slug', 'municipal-treasurers-office')->value('id');
        }
        if (auth()->user()->hasRole('BPLS-staff')) {
            return Offices::where('slug', 'business-permits-and-licensing-section')->value('id');
        }
        return null;
    }
    public function with(): array
    {
        return [
            'documentRequests' => DocumentRequest::with(['user', 'staff', 'office', 'service', 'details'])
                ->where('office_id', auth()->user()->getOfficeIdForStaff())
                ->when($this->search, function ($query) {
                    $query->whereHas('user', function ($q) {
                        $q->where('first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->status, function ($query) {
                    $query->where('status', $this->status);
                })
                ->latest()
                ->paginate(10), 
        ];
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
            <div class="flex-1">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" wire:model.live="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="completed">Completed</option>
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
    <div class="flux-card p-6">
        <div class="overflow-x-auto">
            <table class="table flux-table w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Requestor</th>
                        <th>Document For</th>
                        <th>Service</th>
                        <th>Office</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentRequests as $documentRequest)
                        <tr>
                            <td>{{ $documentRequest->id }}</td>
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
                                    {{ $documentRequest->details->first_name }}
                                    {{ $documentRequest->details->last_name }}
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
                                <p class="fw-semibold">{{ $documentRequest->office?->name ?? 'N/A' }}</p>
                            </td>
                            <td>
                                <span
                                    class="flux-badge flux-badge-{{ match ($documentRequest->status) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'completed' => 'success',
                                        default => 'light',
                                    } }}">
                                    {{ ucfirst($documentRequest->status) }}
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
                                        wire:navigate class="flux-btn flux-btn-outline btn-sm" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
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
                                <button wire:click="updateDocumentStatus({{ $documentRequest->id }}, 'approved')"
                                    class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i>Approve
                                </button>
                                <button wire:click="updateDocumentStatus({{ $documentRequest->id }}, 'rejected')"
                                    class="btn btn-danger">
                                    <i class="bi bi-x-circle me-1"></i>Reject
                                </button>
                                <button wire:click="updateDocumentStatus({{ $documentRequest->id }}, 'completed')"
                                    class="btn btn-primary">
                                    <i class="bi bi-check2-all me-1"></i>Complete
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('viewDocumentRequest', (id) => {
            const modal = new bootstrap.Modal(document.getElementById('documentRequestModal'));
            modal.show();
        });
    });
</script>
