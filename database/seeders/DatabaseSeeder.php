<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Step 1: Seed roles and permissions (must be first)
        $this->command->info('Seeding roles and permissions...');
        $this->call(RolesAndPermissionsSeeder::class);

        // Step 2: Seed offices and services (needed before appointments/document requests)
        $this->command->info('Seeding offices and services...');
        $this->call(OfficeSeeder::class);

        // Step 3: Seed users (needed before personal info, addresses, families, appointments, document requests)
        $this->command->info('Seeding users...');
        $this->call(UsersSeeder::class);

        // Step 4: Seed personal information (needed before addresses and families)
        $this->command->info('Seeding personal information...');
        $this->call(PersonalInformationSeeder::class);

        // Step 5: Seed user addresses (depends on personal information)
        $this->command->info('Seeding user addresses...');
        $this->call(UserAddressesSeeder::class);

        // Step 6: Seed user families (depends on personal information)
        $this->command->info('Seeding user families...');
        $this->call(UserFamiliesSeeder::class);

        // Step 7: Seed appointments (depends on users and offices)
        $this->command->info('Seeding appointments...');
        $this->call(AppointmentsSeeder::class);

        // Step 8: Seed appointment details (depends on appointments)
        $this->command->info('Seeding appointment details...');
        $this->call(AppointmentDetailsSeeder::class);

        // Step 9: Seed document requests (depends on users, offices, and services)
        $this->command->info('Seeding document requests...');
        $this->call(DocumentRequestsSeeder::class);

        // Step 10: Seed document request details (depends on document requests)
        $this->command->info('Seeding document request details...');
        $this->call(DocumentRequestDetailsSeeder::class);

        // Step 11: Seed notifications (depends on users)
        $this->command->info('Seeding notifications...');
        $this->call(NotificationsSeeder::class);

        $this->command->info('Database seeding completed successfully!');
    }
}
