<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

new class extends Component {
    use WithPagination;

    public bool $confirmDeletion;
    public string $roleName = '';
    public string $roleDescription = '';
    public array $selectedPermissions = [];
    public string $permissionName = '';
    public mixed $role = null;

    public function mount()
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'Unauthorized access. Only super administrators can manage roles and permissions.');
        }
    }

    // Save Permission
    public function savePermission()
    {
        $validated = $this->validate([
            'permissionName' => 'required|string|max:255|unique:permissions,name',
        ]);
        if ($validated) {
            $permission = Permission::create([
                'name' => $this->permissionName,
                'guard_name' => 'web',
            ]);

            if ($permission) {
                session()->flash('message', 'Permission created successfully.');
                $this->reset(['permissionName']);
                $this->dispatch('close-modal-add-permission');
            }
        }
    }
    // Save Role with selected permissions
    public function saveRole()
    {
        $validated = $this->validate([
            'roleName' => 'required|string|max:255|unique:roles,name',
        ]);
        if ($validated) {
            $role = Role::create([
                'name' => $this->roleName,
                'guard_name' => 'web',
            ]);

            if ($role) {
                $role = Role::find($role->id);
                $permisionsNames = Permission::whereIn('id', $this->selectedPermissions)->pluck('name')->toArray();
                $role->syncPermissions($permisionsNames);
                session()->flash('message', 'Role created successfully with the selected permissions.');
                $this->reset(['roleName', 'selectedPermissions']);
                $this->dispatch('close-modal-add-role');
            }
        }
    }

    public function openEditRoleModal($id)
    {
        $role = Role::findOrFail($id);
        if ($role) {
            $this->role = $role;
            $this->roleName = $role->name;
            $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
            $this->dispatch('open-modal-edit-role');
        }
    }

    public function saveEditRole()
    {
        $validated = $this->validate([
            'roleName' => 'required|string|max:255|unique:roles,name,' . $this->role->id,
        ]);
        if ($validated) {
            $role = Role::where('name', $this->roleName)->first();
            if ($role) {
                $role->name = $this->roleName;
                $role->save();

                // Sync permissions
                $permisionsNames = Permission::whereIn('id', $this->selectedPermissions)->pluck('name')->toArray();
                $role->syncPermissions($permisionsNames);

                session()->flash('message', 'Role updated successfully with the selected permissions.');
                $this->reset(['roleName', 'selectedPermissions']);
                $this->dispatch('close-modal-edit-role');
            }
        }
    }

    public function openDeleteRoleModal($id)
    {
        $role = Role::findOrFail($id);
        if ($role) {
            $this->roleName = $role->name;
            $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
            $this->confirmDeletion = false; // Reset confirmation checkbox
            $this->dispatch('open-modal-delete-role');
        }
    }

    public function confirmDeleteRole()
    {
        if ($this->confirmDeletion) {
            $role = Role::where('name', $this->roleName)->first();
            if ($role) {
                $role->delete();
                session()->flash('message', 'Role deleted successfully.');
                $this->reset(['roleName', 'selectedPermissions', 'confirmDeletion']);
                $this->dispatch('close-modal-delete-role');
            } else {
                session()->flash('error', 'Role not found.');
            }
        } else {
            session()->flash('error', 'You must confirm the deletion.');
        }
    }

    public function openEditPermissionModal($id)
    {
        $permission = Permission::findOrFail($id);
        if ($permission) {
            $this->permissionName = $permission->name;
            $this->dispatch('open-modal-edit-permission');
        }
    }

    public function saveEditPermission()
    {
        $validated = $this->validate([
            'permissionName' => 'required|string|max:255',
        ]);
        if ($validated) {
            $permission = Permission::where('name', $this->permissionName)->first();
            if ($permission) {
                $permission->name = $this->permissionName;
                $permission->save();

                session()->flash('message', 'Permission updated successfully .');
                $this->reset(['permissionName']);
                $this->dispatch('close-modal-edit-permission');
            }
        }
    }

    public function openDeletePermissionModal($id)
    {
        $permission = Permission::findOrFail($id);
        if ($permission) {
            $this->permissionName = $permission->name;
            $this->confirmDeletion = false; // Reset confirmation checkbox
            $this->dispatch('open-modal-delete-permission');
        }
    }

    public function confirmDeletePermission()
    {
        if ($this->confirmDeletion) {
            $permission = Permission::where('name', $this->permissionName)->first();
            if ($permission) {
                $permission->delete();
                session()->flash('message', 'Permission deleted successfully.');
                $this->reset(['permissionName', 'confirmDeletion']);
                $this->dispatch('close-modal-delete-permission');
            } else {
                session()->flash('error', 'Permission not found.');
            }
        } else {
            session()->flash('error', 'You must confirm the deletion.');
        }
    }

    //  Render the component
    public function with()
    {
        return [
            'roles' => Role::paginate(5, pageName: 'roles'),
            'permissions' => Permission::paginate(5, pageName: 'permissions'),
            'allpermissions' => Permission::all(),
            'allroles' => Role::all(),
        ];
    }
}; ?>

<div>



    <!-- Flash Messages -->
    @include('components.alert')

    <!-- Header Section -->
    @include('livewire.admin.components.header')

    <!-- Statistic -->
    @include('livewire.admin.components.statistic')

    <!-- Roles Table -->
    @include('livewire.admin.components.roles-table')

    <!-- Permissions Table -->
    @include('livewire.admin.components.permissions-table')

    <!-- Modals -->
    <!-- Add Role Modal -->
    @include('livewire.admin.components.modal.add-role-modal')

    <!-- Edit Role Modal -->
    @include('livewire.admin.components.modal.edit-role-modal')

    <!-- Delete Role Modal -->
    @include('livewire.admin.components.modal.delete-role-modal')


    <!-- Add Permission Modal -->
    @include('livewire.admin.components.modal.add-permission-modal')

    <!-- Edit Permission Modal -->
    @include('livewire.admin.components.modal.edit-permission-modal')

    <!-- Delete Permission Modal -->
    @include('livewire.admin.components.modal.delete-permission-modal')

    @push('scripts')
        <script>
            // Auto-hide alerts after 5 seconds
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(alert => {
                        if (alert) {
                            alert.style.transition = 'opacity 0.5s';
                            alert.style.opacity = '0';
                            setTimeout(() => alert.remove(), 500);
                        }
                    });
                }, 5000);
            });
        </script>
    @endpush

</div>
