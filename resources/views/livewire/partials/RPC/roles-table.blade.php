<div>
    <div class="flux-card mb-4">
        <div class="p-4 border-bottom">
            <h5 class="mb-0 fw-semibold">Roles Management</h5>
        </div>
        <div class="table-responsive">
            <table class="table flux-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 150px;">Role Name</th>
                        <th>Guard</th>
                        <th style="width: 1000px;">Permissions</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $role->name }}</div>
                                @if ($role->description)
                                    <small class="text-muted">{{ $role->description }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="flux-badge flux-badge-success">{{ $role->guard_name }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse($role->permissions as $permission)
                                        <span class="flux-badge flux-badge-primary">{{ $permission->name }}</span>
                                    @empty
                                        <span class="text-muted">No permissions assigned</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">{{ $role->created_at?->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <div class="action-buttons justify-content-end">
                                    <button class="flux-btn flux-btn-outline btn-sm"
                                        wire:click="openEditRoleModal({{ $role->id }})">
                                        <i class="bi bi-pencil"></i>

                                    </button>
                                    <button class="flux-btn flux-btn-danger btn-sm"
                                        wire:click="openDeleteRoleModal({{ $role->id }})"
                                        wire:loading.attr="disabled">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-person-badge display-4 mb-3"></i>
                                    <div>No roles found. Create your first role to get started.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination links -->
        <div class="mt-3">
            {{ $roles->links(data: ['scrollTo' => false]) }}
        </div>
    </div>

</div>
