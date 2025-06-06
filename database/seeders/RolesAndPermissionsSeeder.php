<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for document/permit management
        $permissions = [
            // Document/Permit Management
            'create-documents',
            'edit-documents',
            'delete-documents',
            'view-documents',
            'request-documents',
            'approve-documents',
            'reject-documents',

            // User Management
            'create-users',
            'edit-users',
            'delete-users',
            'view-users',

            // Appointment Management
            'create-appointments',
            'edit-appointments',
            'delete-appointments',
            'view-appointments',
            'approve-appointments',

            // System Management
            'manage-settings',
            'view-reports',
            'manage-system',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'assign-permissions'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Create roles
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web'
        ]);

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        $staffRole = Role::firstOrCreate([
            'name' => 'staff',
            'guard_name' => 'web'
        ]);

        $clientRole = Role::firstOrCreate([
            'name' => 'client',
            'guard_name' => 'web'
        ]);

        // Assign permissions to roles

        // SuperAdmin - Full system access for maintenance and upgrades
        $superAdminRole->givePermissionTo(Permission::all());

        // Admin - System administrators with broad access
        $adminRole->givePermissionTo([
            'create-documents',
            'edit-documents',
            'delete-documents',
            'view-documents',
            'request-documents',
            'approve-documents',
            'reject-documents',
            'create-users',
            'edit-users',
            'view-users',
            'create-appointments',
            'edit-appointments',
            'delete-appointments',
            'view-appointments',
            'approve-appointments',
            'manage-settings',
            'view-reports',
            'create-roles',
            'edit-roles',
            'assign-permissions'
        ]);

        // Staff - MCR, BPLS, MTO personnel
        $staffRole->givePermissionTo([
            'create-documents',
            'edit-documents',
            'view-documents',
            'request-documents',
            'approve-documents',
            'reject-documents',
            'create-appointments',
            'view-appointments',
            'edit-appointments',
            'approve-appointments',
            'view-users',
            'view-reports'
        ]);

        // Client - Citizens requesting documents or permits
        $clientRole->givePermissionTo([
            'view-documents',
            'request-documents',
            'create-appointments',
            'edit-appointments',
            'view-appointments'
        ]);

        // Create example users and assign roles
        $this->createExampleUsers($superAdminRole, $adminRole, $staffRole, $clientRole);

        $this->command->info('Roles and permissions seeded successfully!');
    }

    private function createExampleUsers(Role $superAdminRole, Role $adminRole, Role $staffRole, Role $clientRole): void
    {
        // Create SuperAdmin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // Create Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        // Create Staff user (MCR personnel example)
        $staff = User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'first_name' => 'MCR',
                'last_name' => 'Staff Member',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $staff->assignRole($staffRole);

        // Create Client user
        $client = User::firstOrCreate(
            ['email' => 'client@example.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Citizen',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $client->assignRole($clientRole);

        $this->command->info('Example users created and roles assigned successfully!');
    }
}
