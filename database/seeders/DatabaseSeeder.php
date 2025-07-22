<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        DB::table('users')->delete();

        // Run other seeders
        $this->call([
            RolesAndPermissionsSeeder::class,
            OfficeSeeder::class,
        ]);

        // Create test users
        $user1 = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $user1->personalInformation()->create([
            'user_id' => $user1->id,
            'suffix' => 'Jr.',
            'date_of_birth' => '1990-01-01',
            'place_of_birth' => 'Anytown',
            'sex_at_birth' => 'Male',
            'civil_status' => 'Single',
            'religion' => 'Catholic',
            'nationality' => 'Filipino',
            'contact_no' => '09123456789',
            'government_id_type' => 'SSS',
            'government_id_image_path' => 'ids/sss.png',
        ]);

        $user1->userAddresses()->create([
            'personal_information_id' => $user1->personalInformation->id,
            'address_type' => 'Permanent',
            'address_line_1' => '123 Main St',
            'address_line_2' => 'Apt 4B',
            'region' => 'Region IV-A',
            'province' => 'Anytown',
            'city' => 'Anytown',
            'barangay' => 'Barangay Uno',
            'street' => 'Main St',
            'zip_code' => '12345',
        ]);

        $user1->userFamilies()->create([
            'personal_information_id' => $user1->personalInformation->id,
            'user_id' => $user1->id,
            // Father's info
            'father_last_name' => 'Doe',
            'father_first_name' => 'John',
            'father_middle_name' => 'M',
            'father_suffix' => 'Sr.',
            'father_birthdate' => '1960-01-01',
            'father_nationality' => 'Filipino',
            'father_religion' => 'Catholic',
            'father_contact_no' => '09123456780',
            // Mother's info
            'mother_last_name' => 'Smith',
            'mother_first_name' => 'Jane',
            'mother_middle_name' => 'A',
            'mother_suffix' => 'N/A',
            'mother_birthdate' => '1965-01-01',
            'mother_nationality' => 'Filipino',
            'mother_religion' => 'Catholic',
            'mother_contact_no' => '09123456781',
        ]);

        User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
