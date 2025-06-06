<x-modal id="add-user" title="Add New User" size="max-w-2xl">
  <form wire:submit.prevent="saveUser">
      <div class="modal-body">
          <div class="mb-3">
              <label for="firstName" class="form-label fw-semibold">First Name</label>
              <input type="text" class="form-control @error('firstName') is-invalid @enderror" id="firstName"
                  wire:model.defer="firstName" placeholder="Enter first name" required>
              @error('firstName')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
          </div>
          <div class="mb-3">
              <label for="middleName" class="form-label fw-semibold">Middle Name</label>
              <input type="text" class="form-control @error('middleName') is-invalid @enderror" id="middleName"
                  wire:model.defer="middleName" placeholder="Enter middle name" required>
              @error('middleName')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
          </div>
          <div class="mb-3">
              <label for="lastName" class="form-label fw-semibold">Last Name</label>
              <input type="text" class="form-control @error('lastName') is-invalid @enderror" id="lastName"
                  wire:model.defer="lastName" placeholder="Enter last name" required>
              @error('lastName')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
          </div>
          <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                  wire:model.defer="email" placeholder="Enter email" required>
              @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
          </div>
          <div class="mb-3">
              <label for="password" class="form-label fw-semibold">Password</label>
              <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                  wire:model.defer="password" placeholder="Enter password" required>
              @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
          </div>
          <div class="mb-3">
              <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
              <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation"
                  wire:model.defer="password_confirmation" placeholder="Confirm password" required>
              @error('password_confirmation')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
          </div>
          <div class="mb-3">  
              <label for="role" class="form-label fw-semibold">Role</label>
              <select class="form-select @error('role') is-invalid @enderror" id="role" wire:model.defer="role" required>
                  <option value="">Select Role</option>
                  @foreach ($roles as $role)
                      <option value="{{ $role->id }}">{{ $role->name }}</option>
                  @endforeach
              </select>
              @error('role')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
          </div>
      </div>
  </form>

  <x-slot name="footer">
      <div class="gap-2 ">
          <button type="button" class="btn btn-outline-secondary" x-data
              x-on:click="$dispatch('close-modal-add-user')">
              <i class="bi bi-x-lg me-1"></i>Cancel
          </button>

          <button type="submit" class="btn btn-primary ml-2" wire:click="saveUser" wire:loading.attr="disabled">
              <span wire:loading.remove>
                  <i class="bi bi-save me-1"></i>
                  Save
              </span>
              <span wire:loading>
                  <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                  Loading...
              </span>
          </button>
      </div>
  </x-slot>
</x-modal>
