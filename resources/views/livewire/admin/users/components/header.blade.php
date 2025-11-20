<div>
  <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
          <h1 class="h3 mb-1 fw-bold text-dark">Users Management</h1>
          <p class="text-muted mb-0">Manage users and their associated roles</p>
      </div>
      <div class="d-flex gap-2">
          <select class="form-select" wire:model.live="roleFilter" style="min-width: 200px;">
              <option value="">All Roles</option>
              @foreach ($roles as $role)
                  <option value="{{ $role->id }}">{{ $role->name }}</option>
              @endforeach
          </select>
          <button class="flux-btn flux-btn-primary" type="button" x-on:click="$dispatch('open-modal-add-user')">
              <i class="bi bi-plus-lg me-2"></i>Add User
          </button>
      </div>
  </div>
</div>
