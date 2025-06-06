<div>
    <div class="stats-grid mb-4">
        <div class="flux-card stat-card">
            <div class="stat-number">{{ count($allroles) }}</div>
            <div class="stat-label">Total Roles</div>
        </div>
        <div class="flux-card stat-card">
            <div class="stat-number">{{ count($allpermissions) }}</div>
            <div class="stat-label">Total Permissions</div>
        </div>
        <div class="flux-card stat-card">
            <div class="stat-number">{{ $allroles->sum(fn($role) => $role->permissions?->count() ?? 0) }}</div>
            <div class="stat-label">Total Assignments</div>
        </div>
    </div>
</div>
