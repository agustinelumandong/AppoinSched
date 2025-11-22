<?php

declare(strict_types=1);

use Livewire\WithPagination;

use Livewire\Volt\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $firstName = '';
    public string $middleName = '';
    public string $lastName = '';
    public string $email = '';
    public mixed $role = null;
    public mixed $roleFilter = null;
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public ?string $createdFrom = null;
    public ?string $createdTo = null;
    public mixed $user = null;
    public bool $confirmDeletion = false;

    public function mount()
    {
        if (
            !auth()
                ->user()
                ->hasAnyRole(['admin', 'super-admin'])
        ) {
            abort(403, 'Unauthorized to manage users');
        }

        // Update last viewed timestamp when admin visits users management page
        auth()->user()->update(['last_viewed_users_at' => now()]);
    }

    public function saveUser()
    {
        $validated = $this->validate([
            'firstName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|exists:roles,id',
        ]);
        if ($validated) {
            $user = User::create([
                'first_name' => $this->firstName,
                'middle_name' => $this->middleName ?? null,
                'last_name' => $this->lastName,
                'email' => $this->email,
                'password' => null, // User will set password on first login
            ]);
            if ($user) {
                $role = Role::where('id', $this->role)->first();
                $user->assignRole($role->name);

                // Generate login code and send email (using same method as OTP email)
                $loginCode = $user->generateLoginCode();
                try {
                    $loginUrl = route('login', absolute: true);
                    $emailBody = "Hello {$user->first_name} {$user->last_name}!\n\n";
                    $emailBody .= "Your account has been created. Please use the login code below to access your account for the first time.\n\n";
                    $emailBody .= "Your Login Code: {$loginCode}\n\n";
                    $emailBody .= "How to use your login code:\n";
                    $emailBody .= "1. Go to the login page: {$loginUrl}\n";
                    $emailBody .= "2. Enter your email address: {$user->email}\n";
                    $emailBody .= "3. Enter the login code shown above\n";
                    $emailBody .= "4. After logging in, you will be asked to set your password\n\n";
                    $emailBody .= "Important: This login code will expire in 7 days. After you set your password, you can use your email and password to log in.\n\n";
                    $emailBody .= "This is an automated message from " . config('app.name') . ". Please do not reply to this email.\n";
                    $emailBody .= "If you did not request this account, please contact support immediately.";

                    Mail::raw($emailBody, function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject('Your Login Code - ' . config('app.name'));
                    });
                } catch (\Exception $e) {
                    // Log error but don't fail user creation
                    \Log::error('Failed to send login code email: ' . $e->getMessage());
                }

                $this->reset();
                $this->dispatch('close-modal-add-user');
                session()->flash('success', 'User created successfully. Login code has been sent to the user\'s email.');
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

    public function clearFilters(): void
    {
        $this->search = '';
        $this->roleFilter = null;
        $this->createdFrom = null;
        $this->createdTo = null;
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCreatedFrom(): void
    {
        $this->resetPage();
    }

    public function updatedCreatedTo(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function updatedSortDirection(): void
    {
        $this->resetPage();
    }

    public function with()
    {
        $query = User::query();

        // Search functionality
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('middle_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Role filter
        if (!empty($this->roleFilter)) {
            $query->whereHas('roles', function ($q) {
                $q->where('id', $this->roleFilter);
            });
        }

        // Date range filter
        if (!empty($this->createdFrom)) {
            $query->whereDate('created_at', '>=', $this->createdFrom);
        }
        if (!empty($this->createdTo)) {
            $query->whereDate('created_at', '<=', $this->createdTo);
        }

        // Sorting
        if ($this->sortBy === 'name') {
            // Sort by last name first, then first name
            $query->orderBy('last_name', $this->sortDirection);
            $query->orderBy('first_name', $this->sortDirection);
        } else {
            $sortField = match($this->sortBy) {
                'email' => 'email',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
                default => 'created_at',
            };
            $query->orderBy($sortField, $this->sortDirection);
        }

        return [
            'users' => $query->paginate(5),
            'roles' => Role::all(),
        ];
    }
}; ?>

<div>


    {{-- Flash Messages --}}
    @include('components.alert')

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
