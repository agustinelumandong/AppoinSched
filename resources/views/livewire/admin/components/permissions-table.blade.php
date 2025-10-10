<div>
    <div class="flux-card" style="padding: 12px;">
        <div class="p-4 border-bottom">
            <h5 class="mb-0 fw-semibold">Permissions Management</h5>
        </div>
        <div class="table-responsive">
            <table class="table flux-table mb-0">
                <thead>
                    <tr>
                        <th>Permission Name</th>
                        <th>Guard</th>
                        <th>Used in Roles</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $permission->name }}</div>
                            </td>
                            <td>
                                <span class="flux-badge flux-badge-success">{{ $permission->guard_name }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @php
                                        $rolesWithPermission = $roles->filter(
                                            fn($role) => $role->permissions->contains('id', $permission->id),
                                        );
                                    @endphp
                                    @forelse($rolesWithPermission as $role)
                                        <span class="flux-badge flux-badge-primary">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-muted">Not assigned</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">{{ $permission->created_at?->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <div class="action-buttons justify-content-end">
                                    <button class="flux-btn flux-btn-primary btn-sm"
                                        wire:click="openEditPermissionModal({{ $permission->id }})"
                                        wire:loading.attr="disabled">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="flux-btn flux-btn-danger btn-sm"
                                        wire:click="openDeletePermissionModal({{ $permission->id }})"
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
                                    <i class="bi bi-shield-check display-4 mb-3"></i>
                                    <div>No permissions found. Create your first permission to get started.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination links -->
        <div class="mt-3">
            {{ $permissions->links(data: ['scrollTo' => false]) }}
        </div>
    </div>
</div>
