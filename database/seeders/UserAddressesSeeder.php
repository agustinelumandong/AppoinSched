<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\PersonalInformation;
use App\Models\UserAddresses;
use Illuminate\Database\Seeder;

class UserAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereHas('personalInformation')->get();

        $regions = [
            'Region I - Ilocos Region',
            'Region II - Cagayan Valley',
            'Region III - Central Luzon',
            'Region IV-A - CALABARZON',
            'Region IV-B - MIMAROPA',
            'Region V - Bicol Region',
            'Region VI - Western Visayas',
            'Region VII - Central Visayas',
            'Region VIII - Eastern Visayas',
            'Region IX - Zamboanga Peninsula',
            'Region X - Northern Mindanao',
            'Region XI - Davao Region',
            'Region XII - SOCCSKSARGEN',
            'NCR - National Capital Region',
        ];

        $provinces = [
            'Metro Manila', 'Batangas', 'Laguna', 'Cavite', 'Rizal', 'Quezon',
            'Bulacan', 'Pampanga', 'Nueva Ecija', 'Tarlac', 'Zambales',
            'Bataan', 'Aurora', 'Oriental Mindoro', 'Occidental Mindoro',
        ];

        $cities = [
            'Manila', 'Quezon City', 'Makati', 'Taguig', 'Pasig', 'Mandaluyong',
            'San Juan', 'Caloocan', 'Las Piñas', 'Parañaque', 'Muntinlupa',
            'Marikina', 'Valenzuela', 'Malabon', 'Navotas', 'Pasay',
            'Batangas City', 'Lipa City', 'Tagaytay', 'Calamba', 'San Pablo',
        ];

        $barangays = [
            'Barangay 1', 'Barangay 2', 'Barangay 3', 'Barangay 4', 'Barangay 5',
            'Barangay Poblacion', 'Barangay San Jose', 'Barangay San Isidro',
            'Barangay San Antonio', 'Barangay San Miguel', 'Barangay San Roque',
        ];

        foreach ($users as $user) {
            $personalInfo = $user->personalInformation;
            if (!$personalInfo) {
                continue;
            }

            // Create permanent address
            UserAddresses::create([
                'personal_information_id' => $personalInfo->id,
                'address_type' => 'Permanent',
                'address_line_1' => fake()->buildingNumber() . ' ' . fake()->streetName() . ' Street',
                'address_line_2' => fake()->optional(0.6)->secondaryAddress(),
                'region' => fake()->randomElement($regions),
                'province' => fake()->randomElement($provinces),
                'city' => fake()->randomElement($cities),
                'barangay' => fake()->randomElement($barangays),
                'street' => fake()->streetName() . ' Street',
                'zip_code' => fake()->numerify('####'),
            ]);

            // Create temporary address for some users (30% chance)
            if (fake()->boolean(30)) {
                UserAddresses::create([
                    'personal_information_id' => $personalInfo->id,
                    'address_type' => 'Temporary',
                    'address_line_1' => fake()->buildingNumber() . ' ' . fake()->streetName() . ' Avenue',
                    'address_line_2' => fake()->optional(0.6)->secondaryAddress(),
                    'region' => fake()->randomElement($regions),
                    'province' => fake()->randomElement($provinces),
                    'city' => fake()->randomElement($cities),
                    'barangay' => fake()->randomElement($barangays),
                    'street' => fake()->streetName() . ' Avenue',
                    'zip_code' => fake()->numerify('####'),
                ]);
            }
        }

        $this->command->info('User addresses seeded successfully!');
    }
}

