<div>
    <div class="flux-card mb-4">
        <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
            <h5 class="mb-0 fw-semibold">Offices</h5>
            <div class="d-flex align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <input type="text" class="flux-form-control search" placeholder="Search offices"
                        wire:model.live="search" wire:keyup="searchs" wire:debounce.300ms>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table flux-table mb-0">
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offices as $office)
                        <tr>
                            <td>
                                <img src="{{ asset('storage/offices/' . $office->logo) }}" alt="Logo"
                                    class="h-12 w-12 object-cover rounded">
                            </td>
                            <td>
                                <p class="fw-semibold">{{ $office->name ?? 'N/A' }}</p>
                            </td>
                            <td>
                                <p class="fw-semibold ">{{ $office->slug ?? 'N/A' }}</p>
                            </td>
                            <td>
                                <p class="fw-semibold">{{ $office->description ?? 'N/A' }}</p>
                            </td>
                            <td>
                                <p class="fw-semibold">{{ $office->created_at?->format('M d, Y') }}</p>
                            </td>
                            <td>
                                <p class="fw-semibold">{{ $office->updated_at?->format('M d, Y') }}</p>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="flux-btn flux-btn-outline btn-sm"
                                        wire:click="openEditOfficeModal({{ $office->id }})">
                                        <i class="bi bi-pencil"></i>

                                    </button>
                                    <button class="flux-btn flux-btn-danger btn-sm"
                                        wire:click="openDeleteOfficeModal({{ $office->id }})"
                                        wire:loading.attr="disabled">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-person-badge display-4 mb-3"></i>
                                    <div>No offices found. Create your first office to get started.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination links -->
        <div class="mt-3">
            {{ $offices->links(data: ['scrollTo' => false]) }}
        </div>
    </div>

</div>
