<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">Roles & Permissions</h1>
            <p class="text-muted mb-0">Manage user roles and their associated permissions</p>
        </div>
        <div class="d-flex gap-2">
            <button class="flux-btn flux-btn-secondary" x-on:click="$dispatch('open-modal-add-permission')">
                <i class="bi bi-plus-lg me-2"></i>Add Permission
            </button>
            <button class="flux-btn flux-btn-primary" type="button" x-on:click="$dispatch('open-modal-add-role')">
                <i class="bi bi-plus-lg me-2"></i>Add Role
            </button>
        </div>
    </div>
</div>
