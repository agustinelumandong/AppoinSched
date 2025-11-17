<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all roles
        $roles = [
            'super-admin' => Role::where('name', 'super-admin')->first(),
            'admin' => Role::where('name', 'admin')->first(),
            'client' => Role::where('name', 'client')->first(),
            'MCR-staff' => Role::where('name', 'MCR-staff')->first(),
            'MTO-staff' => Role::where('name', 'MTO-staff')->first(),
            'BPLS-staff' => Role::where('name', 'BPLS-staff')->first(),
            'MCR-admin' => Role::where('name', 'MCR-admin')->first(),
            'MTO-admin' => Role::where('name', 'MTO-admin')->first(),
            'BPLS-admin' => Role::where('name', 'BPLS-admin')->first(),
        ];

        // Philippine first names and last names for realistic data
        $firstNames = [
            'Maria', 'Jose', 'Juan', 'Ana', 'Carlos', 'Rosa', 'Pedro', 'Carmen',
            'Antonio', 'Elena', 'Manuel', 'Rita', 'Francisco', 'Lourdes', 'Miguel', 'Teresa',
            'Ricardo', 'Sofia', 'Fernando', 'Isabel', 'Roberto', 'Patricia', 'Eduardo', 'Monica',
            'Alberto', 'Andrea', 'Ramon', 'Cristina', 'Jorge', 'Angela', 'Luis', 'Beatriz',
            'Enrique', 'Diana', 'Victor', 'Gloria', 'Daniel', 'Lucia', 'Felipe', 'Rosa',
        ];

        $lastNames = [
            'Santos', 'Reyes', 'Cruz', 'Bautista', 'Ocampo', 'Garcia', 'Mendoza', 'Torres',
            'Flores', 'Rivera', 'Gonzales', 'Villanueva', 'Fernandez', 'Ramos', 'Lopez', 'Aquino',
            'Mendoza', 'Del Rosario', 'Castillo', 'Morales', 'Castro', 'Dela Cruz', 'Gutierrez', 'Perez',
            'Sanchez', 'Rodriguez', 'Martinez', 'Romero', 'Herrera', 'Jimenez', 'Navarro', 'Vargas',
        ];

        // Create Super Admin users (2)
        for ($i = 1; $i <= 2; $i++) {
            $user = User::create([
                'first_name' => fake()->randomElement($firstNames),
                'middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                'last_name' => fake()->randomElement($lastNames),
                'email' => "superadmin{$i}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'notification_settings' => [
                    'submitted' => true,
                    'approved' => true,
                    'rejected' => true,
                    'payment_uploaded' => true,
                    'payment_verified' => true,
                    'appointment_scheduled' => true,
                    'appointment_approved' => true,
                    'appointment_cancelled' => true,
                    'appointment_rescheduled' => true,
                    'completed' => true,
                    'appointment_reminder' => true,
                ],
            ]);
            if ($roles['super-admin']) {
                $user->assignRole($roles['super-admin']);
            }
        }

        // Create Admin users (3)
        for ($i = 1; $i <= 3; $i++) {
            $user = User::create([
                'first_name' => fake()->randomElement($firstNames),
                'middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                'last_name' => fake()->randomElement($lastNames),
                'email' => "admin{$i}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'notification_settings' => [
                    'submitted' => true,
                    'approved' => true,
                    'rejected' => true,
                    'payment_uploaded' => true,
                    'payment_verified' => true,
                    'appointment_scheduled' => true,
                    'appointment_approved' => true,
                    'appointment_cancelled' => true,
                    'appointment_rescheduled' => true,
                    'completed' => true,
                    'appointment_reminder' => true,
                ],
            ]);
            if ($roles['admin']) {
                $user->assignRole($roles['admin']);
            }
        }

        // Create MCR Admin users (2)
        for ($i = 1; $i <= 2; $i++) {
            $user = User::create([
                'first_name' => fake()->randomElement($firstNames),
                'middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                'last_name' => fake()->randomElement($lastNames),
                'email' => "mcradmin{$i}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            if ($roles['MCR-admin']) {
                $user->assignRole($roles['MCR-admin']);
            }
        }

        // Create MTO Admin users (2)
        for ($i = 1; $i <= 2; $i++) {
            $user = User::create([
                'first_name' => fake()->randomElement($firstNames),
                'middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                'last_name' => fake()->randomElement($lastNames),
                'email' => "mtoadmin{$i}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            if ($roles['MTO-admin']) {
                $user->assignRole($roles['MTO-admin']);
            }
        }

        // Create BPLS Admin users (2)
        for ($i = 1; $i <= 2; $i++) {
            $user = User::create([
                'first_name' => fake()->randomElement($firstNames),
                'middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                'last_name' => fake()->randomElement($lastNames),
                'email' => "bplsadmin{$i}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            if ($roles['BPLS-admin']) {
                $user->assignRole($roles['BPLS-admin']);
            }
        }

        // Create MCR Staff users (5)
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'first_name' => fake()->randomElement($firstNames),
                'middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                'last_name' => fake()->randomElement($lastNames),
                'email' => "mcrstaff{$i}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            if ($roles['MCR-staff']) {
                $user->assignRole($roles['MCR-staff']);
            }
        }

        // Create MTO Staff users (5)
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'first_name' => fake()->randomElement($firstNames),
                'middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                'last_name' => fake()->randomElement($lastNames),
                'email' => "mtostaff{$i}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            if ($roles['MTO-staff']) {
                $user->assignRole($roles['MTO-staff']);
            }
        }

        // Create BPLS Staff users (5)
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'first_name' => fake()->randomElement($firstNames),
                'middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                'last_name' => fake()->randomElement($lastNames),
                'email' => "bplsstaff{$i}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            if ($roles['BPLS-staff']) {
                $user->assignRole($roles['BPLS-staff']);
            }
        }

        // Create Client users (30) - mix of verified and unverified
        for ($i = 1; $i <= 30; $i++) {
            $isVerified = fake()->boolean(80); // 80% verified
            $user = User::create([
                'first_name' => fake()->randomElement($firstNames),
                'middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                'last_name' => fake()->randomElement($lastNames),
                'email' => "client{$i}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => $isVerified ? now() : null,
            ]);
            if ($roles['client']) {
                $user->assignRole($roles['client']);
            }
        }

        $this->command->info('Users seeded successfully!');
    }
}
