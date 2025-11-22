<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DocumentRequest;
use App\Models\DocumentRequestDetails;
use App\Models\Services;
use Illuminate\Database\Seeder;

class DocumentRequestDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentRequests = DocumentRequest::whereDoesntHave('details')->get();

        $firstNames = [
            'Maria', 'Jose', 'Juan', 'Ana', 'Carlos', 'Rosa', 'Pedro', 'Carmen',
            'Antonio', 'Elena', 'Manuel', 'Rita', 'Francisco', 'Lourdes', 'Miguel', 'Teresa',
            'Ricardo', 'Sofia', 'Fernando', 'Isabel', 'Roberto', 'Patricia', 'Eduardo', 'Monica',
        ];

        $lastNames = [
            'Santos', 'Reyes', 'Cruz', 'Bautista', 'Ocampo', 'Garcia', 'Mendoza', 'Torres',
            'Flores', 'Rivera', 'Gonzales', 'Villanueva', 'Fernandez', 'Ramos', 'Lopez', 'Aquino',
        ];

        $suffixes = ['N/A', 'Jr.', 'Sr.', 'I', 'II', 'III'];
        $sexOptions = ['Male', 'Female'];
        $civilStatuses = ['Single', 'Married', 'Widowed', 'Divorced', 'Separated'];
        $religions = ['Catholic', 'Protestant', 'Islam', 'Buddhist', 'Iglesia ni Cristo', 'Born Again'];
        $governmentIdTypes = ['SSS', 'TIN', 'PhilHealth', 'Driver\'s License', 'Passport', 'National ID'];

        $regions = [
            'Region I - Ilocos Region',
            'Region II - Cagayan Valley',
            'Region III - Central Luzon',
            'Region IV-A - CALABARZON',
            'Region IV-B - MIMAROPA',
            'Region V - Bicol Region',
            'NCR - National Capital Region',
        ];

        $provinces = [
            'Metro Manila', 'Batangas', 'Laguna', 'Cavite', 'Rizal', 'Quezon',
            'Bulacan', 'Pampanga', 'Nueva Ecija', 'Tarlac', 'Zambales',
        ];

        $cities = [
            'Manila', 'Quezon City', 'Makati', 'Taguig', 'Pasig', 'Mandaluyong',
            'Batangas City', 'Lipa City', 'Tagaytay', 'Calamba', 'San Pablo',
        ];

        $barangays = [
            'Barangay 1', 'Barangay 2', 'Barangay 3', 'Barangay Poblacion',
            'Barangay San Jose', 'Barangay San Isidro', 'Barangay San Antonio',
        ];

        foreach ($documentRequests as $request) {
            $user = $request->user;
            $service = $request->service;
            $serviceTitle = strtolower($service->title ?? '');

            $baseData = [
                'document_request_id' => $request->id,
                'request_for' => fake()->randomElement(['myself', 'someone_else']),
                // Personal Information
                'last_name' => $user->last_name ?? fake()->randomElement($lastNames),
                'first_name' => $user->first_name ?? fake()->randomElement($firstNames),
                'middle_name' => $user->middle_name ?? fake()->optional(0.7)->randomElement($firstNames),
                'suffix' => fake()->randomElement($suffixes),
                'email' => $user->email ?? fake()->safeEmail(),
                'contact_no' => $user->personalInformation?->contact_no ?? fake()->numerify('09#########'),
                'sex_at_birth' => fake()->randomElement($sexOptions),
                'date_of_birth' => fake()->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
                'place_of_birth' => fake()->randomElement($cities) . ', Philippines',
                'civil_status' => fake()->randomElement($civilStatuses),
                'religion' => fake()->randomElement($religions),
                'nationality' => 'Filipino',
                'government_id_type' => fake()->randomElement($governmentIdTypes),
                'government_id_image_path' => 'ids/' . strtolower($user->first_name ?? 'user') . '_id.pdf',
                // Address Information
                'address_type' => 'Permanent',
                'address_line_1' => fake()->buildingNumber() . ' ' . fake()->streetName() . ' Street',
                'address_line_2' => fake()->optional(0.6)->secondaryAddress(),
                'region' => fake()->randomElement($regions),
                'province' => fake()->randomElement($provinces),
                'city' => fake()->randomElement($cities),
                'barangay' => fake()->randomElement($barangays),
                'street' => fake()->streetName() . ' Street',
                'zip_code' => fake()->numerify('####'),
                // Family Information
                'father_last_name' => fake()->randomElement($lastNames),
                'father_first_name' => fake()->randomElement($firstNames),
                'father_middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                'father_suffix' => fake()->randomElement($suffixes),
                'father_birthdate' => fake()->dateTimeBetween('-80 years', '-40 years')->format('Y-m-d'),
                'father_nationality' => 'Filipino',
                'father_religion' => fake()->randomElement($religions),
                'father_contact_no' => fake()->optional(0.6)->numerify('09#########'),
                'mother_last_name' => fake()->randomElement($lastNames),
                'mother_first_name' => fake()->randomElement($firstNames),
                'mother_middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                'mother_suffix' => fake()->randomElement($suffixes),
                'mother_birthdate' => fake()->dateTimeBetween('-80 years', '-40 years')->format('Y-m-d'),
                'mother_nationality' => 'Filipino',
                'mother_religion' => fake()->randomElement($religions),
                'mother_contact_no' => fake()->optional(0.6)->numerify('09#########'),
            ];

            // Add document-specific fields based on service type
            if (str_contains($serviceTitle, 'birth')) {
                // Birth Certificate - no additional fields needed beyond base data
            } elseif (str_contains($serviceTitle, 'marriage')) {
                // Marriage Certificate - add groom and bride information
                $groomAge = fake()->numberBetween(20, 50);
                $brideAge = fake()->numberBetween(20, 50);
                $baseData = array_merge($baseData, [
                    'groom_first_name' => fake()->randomElement($firstNames),
                    'groom_middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                    'groom_last_name' => fake()->randomElement($lastNames),
                    'groom_suffix' => fake()->randomElement($suffixes),
                    'groom_age' => $groomAge,
                    'groom_date_of_birth' => fake()->dateTimeBetween('-' . ($groomAge + 5) . ' years', '-' . $groomAge . ' years')->format('Y-m-d'),
                    'groom_place_of_birth' => fake()->randomElement($cities) . ', Philippines',
                    'groom_sex' => 'Male',
                    'groom_citizenship' => 'Filipino',
                    'groom_residence' => fake()->randomElement($cities) . ', ' . fake()->randomElement($provinces),
                    'groom_religion' => fake()->randomElement($religions),
                    'groom_civil_status' => 'Single',
                    'groom_father_first_name' => fake()->randomElement($firstNames),
                    'groom_father_middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                    'groom_father_last_name' => fake()->randomElement($lastNames),
                    'groom_father_suffix' => fake()->randomElement($suffixes),
                    'groom_father_citizenship' => 'Filipino',
                    'groom_father_residence' => fake()->randomElement($cities) . ', ' . fake()->randomElement($provinces),
                    'groom_mother_first_name' => fake()->randomElement($firstNames),
                    'groom_mother_middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                    'groom_mother_last_name' => fake()->randomElement($lastNames),
                    'groom_mother_citizenship' => 'Filipino',
                    'groom_mother_residence' => fake()->randomElement($cities) . ', ' . fake()->randomElement($provinces),
                    'bride_first_name' => fake()->randomElement($firstNames),
                    'bride_middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                    'bride_last_name' => fake()->randomElement($lastNames),
                    'bride_suffix' => fake()->randomElement($suffixes),
                    'bride_age' => $brideAge,
                    'bride_date_of_birth' => fake()->dateTimeBetween('-' . ($brideAge + 5) . ' years', '-' . $brideAge . ' years')->format('Y-m-d'),
                    'bride_place_of_birth' => fake()->randomElement($cities) . ', Philippines',
                    'bride_sex' => 'Female',
                    'bride_citizenship' => 'Filipino',
                    'bride_residence' => fake()->randomElement($cities) . ', ' . fake()->randomElement($provinces),
                    'bride_religion' => fake()->randomElement($religions),
                    'bride_civil_status' => 'Single',
                    'bride_father_first_name' => fake()->randomElement($firstNames),
                    'bride_father_middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                    'bride_father_last_name' => fake()->randomElement($lastNames),
                    'bride_father_suffix' => fake()->randomElement($suffixes),
                    'bride_father_citizenship' => 'Filipino',
                    'bride_father_residence' => fake()->randomElement($cities) . ', ' . fake()->randomElement($provinces),
                    'bride_mother_first_name' => fake()->randomElement($firstNames),
                    'bride_mother_middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                    'bride_mother_last_name' => fake()->randomElement($lastNames),
                    'bride_mother_citizenship' => 'Filipino',
                    'bride_mother_residence' => fake()->randomElement($cities) . ', ' . fake()->randomElement($provinces),
                ]);
            } elseif (str_contains($serviceTitle, 'death')) {
                // Death Certificate - add deceased information
                $baseData = array_merge($baseData, [
                    'deceased_last_name' => fake()->randomElement($lastNames),
                    'deceased_first_name' => fake()->randomElement($firstNames),
                    'deceased_middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                    'deceased_father_last_name' => fake()->randomElement($lastNames),
                    'deceased_father_first_name' => fake()->randomElement($firstNames),
                    'deceased_father_middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                    'deceased_mother_last_name' => fake()->randomElement($lastNames),
                    'deceased_mother_first_name' => fake()->randomElement($firstNames),
                    'deceased_mother_middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                ]);
            } elseif (str_contains($serviceTitle, 'special permit') || str_contains($serviceTitle, 'permit')) {
                // Special Permit - add establishment information
                $baseData = array_merge($baseData, [
                    'establishment_name' => fake()->company() . ' ' . fake()->randomElement(['Restaurant', 'Store', 'Shop', 'Cafe', 'Bakery']),
                    'establishment_address' => fake()->randomElement($cities) . ', ' . fake()->randomElement($provinces),
                    'establishment_purpose' => fake()->randomElement([
                        'Business Operation',
                        'Special Event',
                        'Temporary Business',
                        'Food Service',
                        'Retail Operation',
                    ]),
                ]);
            }

            // Add contact person information if request is for someone else
            if ($baseData['request_for'] === 'someone_else') {
                $baseData = array_merge($baseData, [
                    'contact_first_name' => fake()->randomElement($firstNames),
                    'contact_last_name' => fake()->randomElement($lastNames),
                    'contact_middle_name' => fake()->optional(0.7)->randomElement($firstNames),
                    'contact_email' => fake()->safeEmail(),
                    'contact_phone' => fake()->numerify('09#########'),
                    'relationship' => fake()->randomElement(['Self', 'Spouse', 'Parent', 'Child', 'Sibling', 'Relative', 'Friend']),
                ]);
            }

            DocumentRequestDetails::create($baseData);
        }

        $this->command->info('Document request details seeded successfully!');
    }
}

