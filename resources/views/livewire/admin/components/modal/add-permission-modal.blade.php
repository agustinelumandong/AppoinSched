<x-modal id="add-permission" title="Add Permission" size="max-w-lg">
    <form wire:submit.prevent="savePermission">
        <div class="modal-body">
            <div class="mb-3">
                <label for="permissionName" class="form-label fw-semibold">Permission Name</label>
                <input type="text" class="form-control @error('permissionName') is-invalid @enderror"
                    id="permissionName" wire:model.defer="permissionName"
                    placeholder="Enter permission name (e.g., create_users, edit_posts)" required>
                @error('permissionName')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    Use <strong>lowercase</strong> letters, <strong>numbers</strong>, and <strong>underscores</strong>
                    only. <br>
                    <strong>Example:</strong> manage_users, view_reports
                </div>
            </div>

        </div>
    </form>
    <x-slot name="footer">
        <div class="gap-2 ">
            <button type="button" class="btn btn-outline-secondary" x-data
                x-on:click="$dispatch('close-modal-add-permission')">
                <i class="bi bi-x-lg me-1"></i>Cancel
            </button>

            <button type="submit" class="btn btn-primary ml-2" wire:click="savePermission"
                wire:loading.attr="disabled">
                <span wire:loading.remove>
                    <i class="bi bi-check"></i>
                    Submit
                </span>
                <span wire:loading>
                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                    Loading...
                </span>
            </button>
        </div>
    </x-slot>
</x-modal>
