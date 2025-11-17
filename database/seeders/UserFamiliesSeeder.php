<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\PersonalInformation;
use App\Models\UserFamily;
use Illuminate\Database\Seeder;

class UserFamiliesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereHas('personalInformation')->get();

        $firstNames = [
            'Maria', 'Jose', 'Juan', 'Ana', 'Carlos', 'Rosa', 'Pedro', 'Carmen',
            'Antonio', 'Elena', 'Manuel', 'Rita', 'Francisco', 'Lourdes', 'Miguel', 'Teresa',
            'Ricardo', 'Sofia', 'Fernando', 'Isabel', 'Roberto', 'Patricia', 'Eduardo', 'Monica',
        ];

        $lastNames = [
            'Santos', 'Reyes', 'Cruz', 'Bautista', 'Ocampo', 'Garcia', 'Mendoza', 'Torres',
            'Flores', 'Rivera', 'Gonzales', 'Villanueva', 'Fernandez', 'Ramos', 'Lopez', 'Aquino',
        ];

        $suffixes = ['N/A', 'Jr.', 'Sr.'];
        $religions = ['Catholic', 'Protestant', 'Islam', 'Buddhist', 'Iglesia ni Cristo', 'Born Again'];

        foreach ($users as $user) {
            $personalInfo = $user->personalInformation;
            if (!$personalInfo) {
                continue;
            }

            // Get user's birthdate to calculate parent birthdates
            $userBirthDate = $personalInfo->date_of_birth ? new \DateTime($personalInfo->date_of_birth) : new \DateTime('-30 years');
            $userBirthYear = (int) $userBirthDate->format('Y');

            // Parents should be 20-40 years older than the user
            $fatherBirthYear = $userBirthYear - fake()->numberBetween(20, 40);
            $motherBirthYear = $userBirthYear - fake()->numberBetween(20, 40);

            $fatherBirthDate = fake()->dateTimeBetween(
                $fatherBirthYear . '-01-01',
                $fatherBirthYear . '-12-31'
            );
            $motherBirthDate = fake()->dateTimeBetween(
                $motherBirthYear . '-01-01',
                $motherBirthYear . '-12-31'
            );

            UserFamily::create([
                'personal_information_id' => $personalInfo->id,
                // Father's information
                'father_last_name' => fake()->randomElement($lastNames),
                'father_first_name' => fake()->randomElement($firstNames),
                'father_middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                'father_suffix' => fake()->randomElement($suffixes),
                'father_birthdate' => $fatherBirthDate->format('Y-m-d'),
                'father_nationality' => 'Filipino',
                'father_religion' => fake()->randomElement($religions),
                'father_contact_no' => fake()->optional(0.6)->numerify('09#########'),
                // Mother's information
                'mother_last_name' => fake()->randomElement($lastNames),
                'mother_first_name' => fake()->randomElement($firstNames),
                'mother_middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                'mother_suffix' => fake()->randomElement($suffixes),
                'mother_birthdate' => $motherBirthDate->format('Y-m-d'),
                'mother_nationality' => 'Filipino',
                'mother_religion' => fake()->randomElement($religions),
                'mother_contact_no' => fake()->optional(0.6)->numerify('09#########'),
            ]);
        }

        $this->command->info('User families seeded successfully!');
    }
}

