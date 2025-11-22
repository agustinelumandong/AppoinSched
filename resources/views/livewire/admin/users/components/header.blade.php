<div>
  <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
          <h1 class="h3 mb-1 fw-bold text-dark">Users Management</h1>
          <p class="text-muted mb-0">Manage users and their associated roles</p>
      </div>
      <div class="d-flex gap-2">
          <button class="flux-btn flux-btn-primary" type="button" x-on:click="$dispatch('open-modal-add-user')">
              <i class="bi bi-plus-lg me-2"></i>Add User
          </button>
      </div>
  </div>

  {{-- Filters Section --}}
  <div class="flux-card mb-4" style="padding: 16px;">
      <div class="row g-3">
          {{-- Search --}}
          <div class="col-md-3">
              <label class="form-label fw-semibold">Search</label>
              <input type="text" class="flux-form-control" placeholder="Search by name or email" wire:model.live.debounce.300ms="search">
          </div>

          {{-- Role Filter --}}
          <div class="col-md-2">
              <label class="form-label fw-semibold">Role</label>
              <select class="form-select" wire:model.live="roleFilter">
                  <option value="">All Roles</option>
                  @foreach ($roles as $role)
                      <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                  @endforeach
              </select>
          </div>

          {{-- Created From Date --}}
          <div class="col-md-2">
              <label class="form-label fw-semibold">Created From</label>
              <input type="date" class="flux-form-control" wire:model.live="createdFrom">
          </div>

          {{-- Created To Date --}}
          <div class="col-md-2">
              <label class="form-label fw-semibold">Created To</label>
              <input type="date" class="flux-form-control" wire:model.live="createdTo">
          </div>

          {{-- Sort By --}}
          <div class="col-md-2">
              <label class="form-label fw-semibold">Sort By</label>
              <select class="form-select" wire:model.live="sortBy">
                  <option value="created_at">Date Created</option>
                  <option value="name">Name</option>
                  <option value="email">Email</option>
                  <option value="updated_at">Last Updated</option>
              </select>
          </div>

          {{-- Sort Direction --}}
          <div class="col-md-1">
              <label class="form-label fw-semibold">Order</label>
              <select class="form-select" wire:model.live="sortDirection">
                  <option value="desc">Oldest First</option>
                  <option value="asc">Latest First</option>
              </select>
          </div>
      </div>

      {{-- Clear Filters Button --}}
      @if($search || $roleFilter || $createdFrom || $createdTo)
      <div class="mt-3">
          <button type="button" class="flux-btn flux-btn-outline-secondary btn-sm" wire:click="clearFilters">
              <i class="bi bi-x-circle me-1"></i>Clear Filters
          </button>
      </div>
      @endif
  </div>
</div>
