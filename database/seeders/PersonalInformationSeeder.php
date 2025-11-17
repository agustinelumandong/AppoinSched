<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\PersonalInformation;
use Illuminate\Database\Seeder;

class PersonalInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereDoesntHave('personalInformation')->get();

        $suffixes = ['N/A', 'Jr.', 'Sr.', 'I', 'II', 'III'];
        $sexOptions = ['Male', 'Female'];
        $civilStatuses = ['Single', 'Married', 'Widowed', 'Divorced', 'Separated'];
        $religions = ['Catholic', 'Protestant', 'Islam', 'Buddhist', 'Iglesia ni Cristo', 'Born Again', 'Jehovah\'s Witness', 'Other'];
        $governmentIdTypes = ['SSS', 'TIN', 'PhilHealth', 'Driver\'s License', 'Passport', 'National ID'];

        $philippinePlaces = [
            'Manila', 'Quezon City', 'Makati', 'Taguig', 'Pasig', 'Mandaluyong', 'San Juan',
            'Caloocan', 'Las Piñas', 'Parañaque', 'Muntinlupa', 'Marikina', 'Valenzuela',
            'Malabon', 'Navotas', 'Pasay', 'Batangas City', 'Lipa City', 'Tagaytay',
            'Calamba', 'San Pablo', 'Lucena', 'Antipolo', 'Cainta', 'Taytay',
        ];

        foreach ($users as $user) {
            $sex = fake()->randomElement($sexOptions);
            $birthDate = fake()->dateTimeBetween('-70 years', '-18 years');

            PersonalInformation::create([
                'user_id' => $user->id,
                'suffix' => fake()->randomElement($suffixes),
                'date_of_birth' => $birthDate->format('Y-m-d'),
                'place_of_birth' => fake()->randomElement($philippinePlaces) . ', Philippines',
                'sex_at_birth' => $sex,
                'civil_status' => fake()->randomElement($civilStatuses),
                'religion' => fake()->randomElement($religions),
                'nationality' => 'Filipino',
                'contact_no' => '09' . fake()->numerify('#########'), // 11-digit Philippine mobile
                'government_id_type' => fake()->randomElement($governmentIdTypes),
                'government_id_image_path' => 'ids/' . strtolower($user->first_name) . '_' . strtolower($user->last_name) . '_id.pdf',
            ]);
        }

        $this->command->info('Personal information seeded successfully!');
    }
}

