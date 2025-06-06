<?php

declare(strict_types=1);

use Livewire\WithPagination;

use Livewire\Volt\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $firstName = '';
    public string $middleName = '';
    public string $lastName = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public mixed $role = null;
    public mixed $user = null;
    public bool $confirmDeletion = false;

    public function saveUser()
    {
        $validated = $this->validate([
            'firstName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|min:8|same:password',
            'role' => 'required|exists:roles,id',
        ]);
        if ($validated) {
            $user = User::create([
                'first_name' => $this->firstName,
                'middle_name' => $this->middleName ?? null,
                'last_name' => $this->lastName,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
            if ($user) {
                $role = Role::where('id', $this->role)->first();
                $user->assignRole($role->name);
                $this->reset();
                $this->dispatch('close-modal-add-user');
                session()->flash('success', 'User created successfully');
            } else {
                session()->flash('error', 'User creation failed');
            }
        } else {
            session()->flash('error', 'User creation failed');
        }
    }

    public function openEditUserModal($id)
    {
        $user = User::findOrFail($id);
        if ($user) {
            $this->user = $user;
            $this->firstName = $user->first_name;
            $this->middleName = $user->middle_name ?? '';
            $this->lastName = $user->last_name;
            $this->email = $user->email;
            $this->role = $user->roles->first()->id ?? null;
            $this->dispatch('open-modal-edit-user');
        }
    }

    public function updateUser()
    {
        $validated = $this->validate([
            'firstName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'role' => 'required|exists:roles,id',
        ]);
        if ($validated) {
            $user = User::find($this->user->id);
            $user->first_name = $this->firstName;
            $user->middle_name = $this->middleName ?? null;
            $user->last_name = $this->lastName;
            $user->email = $this->email;
            $user->save();
            $role = Role::where('id', $this->role)->first();
            $user->syncRoles($role->name);
            $this->reset(['firstName', 'middleName', 'lastName', 'email', 'role']);
            $this->dispatch('close-modal-edit-user');
            session()->flash('success', 'User updated successfully');
        } else {
            session()->flash('error', 'User update failed');
        }
    }

    public function openDeleteUserModal($id)
    {
        $user = User::findOrFail($id);
        if ($user) {
            $this->user = $user;
            $this->confirmDeletion = false;
            $this->dispatch('open-modal-delete-user');
        }
    }

    public function confirmDeleteUser()
    {
        if ($this->confirmDeletion) {
            $this->user->delete();
            $this->dispatch('close-modal-delete-user');
            session()->flash('success', 'User deleted successfully');
        } else {
            session()->flash('error', 'User deletion failed');
        }
    }

    public function with()
    {
        return [
            'users' => User::where('first_name', 'like', '%' . $this->search . '%')
                ->orWhere('middle_name', 'like', '%' . $this->search . '%')
                ->orWhere('last_name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%')
                ->paginate(5),
            'roles' => Role::all(),
        ];
    }
}; ?>

<div>
    <link rel="stylesheet" href="{{ asset('css/fluxUI.css') }}">

    <div>
        @if (session()->has('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- Header --}}
    @include('livewire.admin.users.components.header')

    {{-- Table --}}
    @include('livewire.admin.users.components.table')

    {{-- Add User Modal --}}
    @include('livewire.admin.users.components.modal.add-user-modal')

    {{-- Edit User Modal --}}
    @include('livewire.admin.users.components.modal.edit-user-modal')

    {{-- Delete User Modal --}}
    @include('livewire.admin.users.components.modal.delete-user-modal')
</div>
