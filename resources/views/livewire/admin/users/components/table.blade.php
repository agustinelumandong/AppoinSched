<div>
  <div class="flux-card mb-4">
    <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
        <h5 class="mb-0 fw-semibold">Users</h5>
        <input type="text" class="flux-form-control search" placeholder="Search users" wire:model.live="search">
    </div>
      <div class="table-responsive">
          <table class="table flux-table mb-0">
              <thead>
                  <tr>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Role</th>
                      <th>Created</th>
                      <th>Updated</th>
                      <th>Actions</th>
                  </tr>
              </thead>
              <tbody>
                  @forelse($users as $user)
                      <tr>
                          <td>
                              <p class="fw-semibold">{{ $user->name ?? 'N/A' }}</p>
                          </td>
                          <td>
                              <p class="fw-semibold ">{{ $user->email ?? 'N/A' }}</p>
                          </td>
                          <td>
                              <div class="d-flex flex-wrap gap-1">
                                  @forelse($user->roles as $role)
                                      <span class="flux-badge flux-badge-primary">{{ $role->name }}</span>
                                  @empty
                                      <span class="text-muted">No roles assigned</span>
                                  @endforelse
                              </div>
                          </td>
                          <td>
                              <span class="text-muted">{{ $user->created_at?->format('M d, Y') }}</span>
                          </td>
                          <td>
                              <span class="text-muted">{{ $user->updated_at?->format('M d, Y') }}</span>
                          </td>
                          <td>
                              <div class="action-buttons">
                                  <button class="flux-btn flux-btn-primary btn-sm"
                                      wire:click="openEditUserModal({{ $user->id }})">
                                      <i class="bi bi-pencil"></i>

                                  </button>
                                  <button class="flux-btn flux-btn-danger btn-sm"
                                      wire:click="openDeleteUserModal({{ $user->id }})"
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
                                  <div>No users found. Create your first user to get started.</div>
                              </div>
                          </td>
                      </tr>
                  @endforelse
              </tbody>
          </table>
      </div>
      <!-- Pagination links -->
      <div class="mt-3">
          {{ $users->links(data: ['scrollTo' => false]) }}
      </div>
  </div>

</div>
