<div>
    <div class="flux-card mb-4">
        <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
            <h5 class="mb-0 fw-semibold">Services</h5>
            <div class="d-flex align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <input type="text" class="flux-form-control search" placeholder="Search services"
                        wire:model.live="search" wire:keyup="searchServices" wire:debounce.300ms>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table flux-table mb-0">
                <thead>
                    <tr>
                        <th>Office</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                        <tr>
                            <td>
                                <p class="fw-semibold">{{ $service->office->name ?? 'N/A' }}</p>
                            </td>
                            <td>
                                <p class="fw-semibold ">{{ $service->title ?? 'N/A' }}</p>
                            </td>
                            <td>
                                <p class="fw-semibold">{{ $service->slug ?? 'N/A' }}</p>
                            </td>
                            <td>
                                <p class="fw-semibold">{{ $service->description ?? 'N/A' }}</p>
                            </td>
                            <td>
                                <p class="fw-semibold">₱ {{ $service->price ?? 'N/A' }}</p>
                            </td>
                            <td>
                                <span class="flux-badge {{ $service->is_active ? 'flux-badge-success' : 'flux-badge-danger' }}  fw-semibold">
                                    {{ $service->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <p class="fw-semibold">{{ $service->created_at?->format('M d, Y') }}</p>
                            </td>
                            <td>
                                <p class="fw-semibold">{{ $service->updated_at?->format('M d, Y') }}</p>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="flux-btn flux-btn-outline btn-sm"
                                        wire:click="openEditServiceModal({{ $service->id }})">
                                        <i class="bi bi-pencil"></i>

                                    </button>
                                    <button class="flux-btn flux-btn-danger btn-sm"
                                        wire:click="openDeleteServiceModal({{ $service->id }})"
                                        wire:loading.attr="disabled">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-person-badge display-4 mb-3"></i>
                                    <div>No services found. Create your first service to get started.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination links -->
        <div class="mt-3">
            {{ $services->links(data: ['scrollTo' => false]) }}
        </div>
    </div>

</div>
