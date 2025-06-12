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
                        <th>User</th>
                        <th>Staff</th>
                        <th>Office</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentRequests as $documentRequest)
                        <tr>
                            <td>
                                <p class="fw-semibold">
                                    {{ $documentRequest->user->first_name . ' ' . $documentRequest->user->last_name ?? 'N/A' }}
                                </p>
                            </td>
                            <td>
                                <p class="fw-semibold">
                                    {{ $documentRequest->staff->first_name . ' ' . $documentRequest->staff->last_name ?? 'N/A' }}
                                </p>
                            </td>
                            <td>
                                <p class="fw-semibold ">{{ $documentRequest->office->name ?? 'N/A' }}</p>
                            </td>
                            <td>
                                <p class="fw-semibold">{{ $documentRequest->service->title ?? 'N/A' }}</p>
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
                                <p class="fw-semibold">{{ $documentRequest->remarks ?? 'N/A' }}</p>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.view-document-request', Crypt::encryptString($documentRequest->id)) }}"
                                        wire:navigate 
                                        class="flux-btn flux-btn-outline btn-sm"
                                        title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    {{-- <button class="flux-btn flux-btn-outline btn-sm"
                                        wire:click="openEditDocumentRequestModal({{ $documentRequest->id }})"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button> --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
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
            {{ $documentRequests->links(data: ['scrollTo' => false]) }}
        </div>
    </div>

</div>
