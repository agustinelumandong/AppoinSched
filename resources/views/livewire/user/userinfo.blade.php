<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\PersonalInformation;
use App\Models\UserAddresses;
use App\Models\UserFamily;
use App\Services\PhilippineLocationsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

new class extends Component {
    use WithFileUploads;
    public User $user;
    public PersonalInformation $personalInformation;
    public UserAddresses $userAddresses;
    public UserFamily $userFamily;

    public string $last_name = '';
    public string $first_name = '';
    public string $middle_name = '';
    public string $suffix = 'N/A';
    public string $email = '';
    public string $contact_no = '';
    public string $sex_at_birth = '';
    public string $date_of_birth = '';
    public string $place_of_birth = '';
    public string $civil_status = 'Single';
    public string $religion = '';
    public string $nationality = 'Filipino';
    public string $government_id_type = '';
    public mixed $government_id_image_path = null;
    public string $address_type = '';
    public string $address_line_1 = '';
    public string $address_line_2 = '';

    // Present Address properties
    public string $temporary_address_type = 'Temporary';
    public string $temporary_address_line_1 = '';
    public string $temporary_address_line_2 = '';
    public string $temporary_region = '';
    public string $temporary_province = '';
    public string $temporary_city = '';
    public string $temporary_barangay = '';
    public string $temporary_street = '';
    public string $temporary_zip_code = '';

    // Present Address dropdowns
    public array $temporary_provinces = [];
    public array $temporary_cities = [];
    public array $temporary_barangays = [];

    // Checkbox for auto-fill
    public bool $same_as_permanent = false;

    public string $father_last_name = '';
    public string $father_first_name = '';
    public string $father_middle_name = '';
    public string $father_suffix = 'N/A';
    public string $father_birthdate = '';
    public string $father_nationality = '';
    public string $father_religion = '';
    public string $father_contact_no = '';
    public string $mother_last_name = '';
    public string $mother_first_name = '';
    public string $mother_middle_name = '';
    public string $mother_suffix = 'N/A';
    public string $mother_birthdate = '';
    public string $mother_nationality = '';
    public string $mother_religion = '';
    public string $mother_contact_no = '';

    public string $street = '';
    public string $zip_code = '';

    public string $region = '';
    public string $province = '';
    public string $city = '';
    public string $barangay = '';

    public array $regions = [];
    public array $provinces = [];
    public array $cities = [];
    public array $barangays = [];

    public bool $father_is_unknown = false;
    public bool $mother_is_unknown = false;

    protected PhilippineLocationsService $locationsService;

    public function mount(PhilippineLocationsService $locations)
    {
        $this->user = auth()->user();

        // Ensure user is authenticated and verified
        if (!$this->user || !$this->user->hasVerifiedEmail()) {
            $this->redirect(route('verification.notice'), navigate: true);
            return;
        }

        // Create PersonalInformation if it doesn't exist yet
        if (!$this->user->personalInformation) {
            $personalInfo = new PersonalInformation();
            $personalInfo->user_id = $this->user->getKey();
            $personalInfo->save();
            $this->user->refresh();
        }

        $this->personalInformation = $this->user->personalInformation;

        // Create or get Permanent UserAddresses
        $permanentAddress = $this->user->userAddresses->where('address_type', 'Permanent')->first();
        if (!$permanentAddress) {
            $permanentAddress = new UserAddresses();
            $permanentAddress->personal_information_id = $this->personalInformation->getKey();
            $permanentAddress->address_type = 'Permanent';
            $permanentAddress->save();
            $this->user->refresh();
        }
        $this->userAddresses = $permanentAddress;

        // Get Temporary Address (if exists)
        $temporaryAddress = $this->user->userAddresses->where('address_type', 'Temporary')->first();

        // Create UserFamily if it doesn't exist yet
        if (!$this->user->userFamilies || $this->user->userFamilies->isEmpty()) {
            $userFamily = new UserFamily();
            // $userFamily->user_id = $this->user->getKey();
            $userFamily->personal_information_id = $this->personalInformation->getKey();
            $userFamily->save();
            $this->user->refresh();
        }

        $this->userFamily = $this->user->userFamilies->first();

        $this->regions = $locations->getRegions();

        // User
        $this->last_name = $this->user->last_name ?? '';
        $this->first_name = $this->user->first_name ?? '';
        $this->middle_name = $this->user->middle_name ?? '';
        $this->email = $this->user->email ?? '';
        $this->contact_no = $this->personalInformation->contact_no ?? '';
        // Personal Information
        $this->suffix = $this->personalInformation->suffix ?? 'N/A';
        $this->sex_at_birth = $this->personalInformation->sex_at_birth ?? '';
        $this->date_of_birth = $this->personalInformation->date_of_birth ?? '';
        $this->place_of_birth = $this->personalInformation->place_of_birth ?? '';
        $this->civil_status = $this->personalInformation->civil_status ?? 'Single';
        $this->religion = $this->personalInformation->religion ?? '';
        $this->nationality = $this->personalInformation->nationality ?? 'Filipino';
        $this->government_id_type = $this->personalInformation->government_id_type ?? '';
        // Note: government_id_image_path is handled as a PDF file upload, not a string
        // Address
        $this->address_type = $this->userAddresses->address_type ?? 'Permanent';
        $this->address_line_1 = $this->userAddresses->address_line_1 ?? '';
        $this->address_line_2 = $this->userAddresses->address_line_2 ?? '';
        $this->region = $this->userAddresses->region ?? '';
        $this->province = $this->userAddresses->province ?? '';
        $this->city = $this->userAddresses->city ?? '';
        $this->barangay = $this->userAddresses->barangay ?? '';
        $this->street = $this->userAddresses->street ?? '';
        $this->zip_code = $this->userAddresses->zip_code ?? '';

        // Present Address - load from temporary address record
        $this->temporary_address_type = $temporaryAddress->address_type ?? 'Temporary';
        $this->temporary_address_line_1 = $temporaryAddress->address_line_1 ?? '';
        $this->temporary_address_line_2 = $temporaryAddress->address_line_2 ?? '';
        $this->temporary_region = $temporaryAddress->region ?? '';
        $this->temporary_province = $temporaryAddress->province ?? '';
        $this->temporary_city = $temporaryAddress->city ?? '';
        $this->temporary_barangay = $temporaryAddress->barangay ?? '';
        $this->temporary_street = $temporaryAddress->street ?? '';
        $this->temporary_zip_code = $temporaryAddress->zip_code ?? '';

        // Family - Father
        $this->father_last_name = $this->userFamily->father_last_name ?? '';
        $this->father_first_name = $this->userFamily->father_first_name ?? '';
        $this->father_middle_name = $this->userFamily->father_middle_name ?? '';
        $this->father_suffix = $this->userFamily->father_suffix ?? 'N/A';
        $this->father_birthdate = $this->userFamily->father_birthdate ?? '';
        $this->father_nationality = $this->userFamily->father_nationality ?? '';
        $this->father_religion = $this->userFamily->father_religion ?? '';
        $this->father_contact_no = $this->userFamily->father_contact_no ?? '';
        // Family - Mother
        $this->mother_last_name = $this->userFamily->mother_last_name ?? '';
        $this->mother_first_name = $this->userFamily->mother_first_name ?? '';
        $this->mother_middle_name = $this->userFamily->mother_middle_name ?? '';
        $this->mother_suffix = $this->userFamily->mother_suffix ?? 'N/A';
        $this->mother_birthdate = $this->userFamily->mother_birthdate ?? '';
        $this->mother_nationality = $this->userFamily->mother_nationality ?? '';
        $this->mother_religion = $this->userFamily->mother_religion ?? '';
        $this->mother_contact_no = $this->userFamily->mother_contact_no ?? '';

        // Set father_is_unknown if all father fields are N/A
        $this->father_is_unknown = $this->father_last_name === 'N/A' && $this->father_first_name === 'N/A' && $this->father_middle_name === 'N/A' && $this->father_suffix === 'N/A' && $this->father_nationality === 'N/A' && $this->father_religion === 'N/A' && $this->father_contact_no === 'N/A';
        $this->mother_is_unknown = $this->mother_last_name === 'N/A' && $this->mother_first_name === 'N/A' && $this->mother_middle_name === 'N/A' && $this->mother_suffix === 'N/A' && $this->mother_nationality === 'N/A' && $this->mother_religion === 'N/A' && $this->mother_contact_no === 'N/A';

        // Check if temporary address is same as permanent
        $this->same_as_permanent = $temporaryAddress &&
            $temporaryAddress->address_line_1 === $this->userAddresses->address_line_1 &&
            $temporaryAddress->address_line_2 === $this->userAddresses->address_line_2 &&
            $temporaryAddress->region === $this->userAddresses->region &&
            $temporaryAddress->province === $this->userAddresses->province &&
            $temporaryAddress->city === $this->userAddresses->city &&
            $temporaryAddress->barangay === $this->userAddresses->barangay &&
            $temporaryAddress->street === $this->userAddresses->street &&
            $temporaryAddress->zip_code === $this->userAddresses->zip_code;

        // Pre-populate dependent dropdowns if values exist
        if ($this->region) {
            $this->provinces = $locations->getProvinces($this->region);
        }
        if ($this->region && $this->province) {
            $this->cities = $locations->getMunicipalities($this->region, $this->province);
        }
        if ($this->region && $this->province && $this->city) {
            $this->barangays = $locations->getBarangays($this->region, $this->province, $this->city);
        }

        // Pre-populate temporary address dropdowns if values exist
        if ($this->temporary_region) {
            $this->temporary_provinces = $locations->getProvinces($this->temporary_region);
        }
        if ($this->temporary_region && $this->temporary_province) {
            $this->temporary_cities = $locations->getMunicipalities($this->temporary_region, $this->temporary_province);
        }
        if ($this->temporary_region && $this->temporary_province && $this->temporary_city) {
            $this->temporary_barangays = $locations->getBarangays($this->temporary_region, $this->temporary_province, $this->temporary_city);
        }

    }

    public function updatedRegion(PhilippineLocationsService $locations)
    {
        $this->provinces = $locations->getProvinces($this->region);
        $this->province = '';
        $this->cities = [];
        $this->city = '';
        $this->barangays = [];
        $this->barangay = '';
    }

    public function updatedProvince(PhilippineLocationsService $locations)
    {
        $this->cities = $locations->getMunicipalities($this->region, $this->province);
        $this->city = '';
        $this->barangays = [];
        $this->barangay = '';
    }

    public function updatedCity(PhilippineLocationsService $locations)
    {
        $this->barangays = $locations->getBarangays($this->region, $this->province, $this->city);
        $this->barangay = '';
    }

    // Present Address update methods
    public function updatedTempRegion(PhilippineLocationsService $locations)
    {
        $this->temporary_provinces = $locations->getProvinces($this->temporary_region);
        $this->temporary_province = '';
        $this->temporary_cities = [];
        $this->temporary_city = '';
        $this->temporary_barangays = [];
        $this->temporary_barangay = '';
    }

    public function updatedTempProvince(PhilippineLocationsService $locations)
    {
        $this->temporary_cities = $locations->getMunicipalities($this->temporary_region, $this->temporary_province);
        $this->temporary_city = '';
        $this->temporary_barangays = [];
        $this->temporary_barangay = '';
    }

    public function updatedTempCity(PhilippineLocationsService $locations)
    {
        $this->temporary_barangays = $locations->getBarangays($this->temporary_region, $this->temporary_province, $this->temporary_city);
        $this->temporary_barangay = '';
    }

    // Auto-fill Present Address from permanent address
    public function updatedSameAsPermanent($value)
    {
        if ($value) {
            $this->temporary_address_type = 'Temporary';
            $this->temporary_address_line_1 = $this->address_line_1;
            $this->temporary_address_line_2 = $this->address_line_2;
            $this->temporary_region = $this->region;
            $this->temporary_province = $this->province;
            $this->temporary_city = $this->city;
            $this->temporary_barangay = $this->barangay;
            $this->temporary_street = $this->street;
            $this->temporary_zip_code = $this->zip_code;

            // Update Present Address dropdowns
            if ($this->temporary_region) {
                $this->temporary_provinces = app(PhilippineLocationsService::class)->getProvinces($this->temporary_region);
            }
            if ($this->temporary_region && $this->temporary_province) {
                $this->temporary_cities = app(PhilippineLocationsService::class)->getMunicipalities($this->temporary_region, $this->temporary_province);
            }
            if ($this->temporary_region && $this->temporary_province && $this->temporary_city) {
                $this->temporary_barangays = app(PhilippineLocationsService::class)->getBarangays($this->temporary_region, $this->temporary_province, $this->temporary_city);
            }
        } else {
            // Clear Present Address fields
            $this->temporary_address_type = '';
            $this->temporary_address_line_1 = '';
            $this->temporary_address_line_2 = '';
            $this->temporary_region = '';
            $this->temporary_province = '';
            $this->temporary_city = '';
            $this->temporary_barangay = '';
            $this->temporary_street = '';
            $this->temporary_zip_code = '';
            $this->temporary_provinces = [];
            $this->temporary_cities = [];
            $this->temporary_barangays = [];
        }
    }

    public function updatePersonalInformation()
    {
        $this->personalInformation->save();
    }

    public function updateUserAddresses()
    {
        $this->userAddresses->save();
    }

    public function updateUserFamily()
    {
        $this->userFamily->save();
    }

    public function updateAll(): void
    {
        Log::info('=== UserInfo UpdateAll Started ===', [
            'user_id' => $this->user->getKey(),
            'permanent_address_id' => $this->userAddresses->id ?? null,
        ]);

        try {
            // Validate input using Laravel's validator
            Log::debug('Starting validation...');
            $validated = $this->validate([
                'last_name' => ['required', 'string', 'max:255'],
                'first_name' => ['required', 'string', 'max:255'],
                'middle_name' => ['nullable', 'string', 'max:255'],
                'suffix' => ['nullable', 'string', 'max:10'],
                'email' => ['required', 'email', 'max:255'],
                'contact_no' => ['required', 'string', 'max:20'],
                'sex_at_birth' => ['required', 'in:Male,Female'],
                'date_of_birth' => ['required', 'date'],
                'place_of_birth' => ['required', 'string', 'max:255'],
                'civil_status' => ['required', 'string', 'max:50'],
                'religion' => ['required', 'string', 'max:100'],
                'nationality' => ['required', 'string', 'max:100'],
                'government_id_type' => ['nullable', 'string', 'max:100'],
                'government_id_image_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'], // 5MB max for PDF

                'address_type' => ['required', 'string', 'max:50'],
                'address_line_1' => ['required', 'string', 'max:255'],
                'address_line_2' => ['required', 'string', 'max:255'],
                'region' => ['required', 'string', 'max:100'],
                'province' => ['required', 'string', 'max:100'],
                'city' => ['required', 'string', 'max:100'],
                'barangay' => ['required', 'string', 'max:100'],
                'street' => ['nullable', 'string', 'max:255'],
                'zip_code' => ['required', 'string', 'max:20'],

                'temporary_address_type' => ['nullable', 'string', 'max:50'],
                'temporary_address_line_1' => ['nullable', 'string', 'max:255'],
                'temporary_address_line_2' => ['nullable', 'string', 'max:255'],
                'temporary_region' => ['nullable', 'string', 'max:100'],
                'temporary_province' => ['nullable', 'string', 'max:100'],
                'temporary_city' => ['nullable', 'string', 'max:100'],
                'temporary_barangay' => ['nullable', 'string', 'max:100'],
                'temporary_street' => ['nullable', 'string', 'max:255'],
                'temporary_zip_code' => ['nullable', 'string', 'max:20'],

                'father_last_name' => [$this->father_is_unknown ? 'nullable' : 'required', 'string', 'max:255'],
                'father_first_name' => [$this->father_is_unknown ? 'nullable' : 'required', 'string', 'max:255'],
                'father_middle_name' => ['nullable', 'string', 'max:255'],
                'father_suffix' => ['nullable', 'string', 'max:10'],
                'father_birthdate' => ['nullable', 'date'],
                'father_nationality' => [$this->father_is_unknown ? 'nullable' : 'required', 'string', 'max:100'],
                'father_religion' => [$this->father_is_unknown ? 'nullable' : 'required', 'string', 'max:100'],
                'father_contact_no' => [$this->father_is_unknown ? 'nullable' : 'required', 'string', 'max:20'],
                'mother_last_name' => [$this->mother_is_unknown ? 'nullable' : 'required', 'string', 'max:255'],
                'mother_first_name' => [$this->mother_is_unknown ? 'nullable' : 'required', 'string', 'max:255'],
                'mother_middle_name' => ['nullable', 'string', 'max:255'],
                'mother_suffix' => ['nullable', 'string', 'max:10'],
                'mother_birthdate' => ['nullable', 'date'],
                'mother_nationality' => [$this->mother_is_unknown ? 'nullable' : 'required', 'string', 'max:100'],
                'mother_religion' => [$this->mother_is_unknown ? 'nullable' : 'required', 'string', 'max:100'],
                'mother_contact_no' => [$this->mother_is_unknown ? 'nullable' : 'required', 'string', 'max:20'],
            ]);

            Log::debug('Validation passed', ['validated_fields_count' => count($validated)]);

            // Check if required personal info fields are filled
            $missingFields = [];
            $requiredPersonalFields = ['sex_at_birth', 'date_of_birth', 'place_of_birth', 'civil_status', 'religion', 'nationality', 'government_id_type', 'government_id_image_path'];

            foreach ($requiredPersonalFields as $field) {
                // Special handling for government_id_image_path: skip if already present in DB
                if ($field === 'government_id_image_path' && !empty(optional($this->personalInformation)->government_id_image_path)) {
                    continue;
                }
                if (empty($this->{$field})) {
                    $missingFields[] = $field;
                }
            }

            if (count($missingFields) > 0) {
                Log::warning('Missing required fields', ['missing_fields' => $missingFields]);
                session()->flash('error', 'Please fill up the following personal information fields: ' . implode(', ', $missingFields));
                return;
            }

            // Start database transaction for data integrity
            DB::beginTransaction();
            Log::info('Database transaction started');

            // Update User
            Log::debug('Updating User', [
                'user_id' => $this->user->getKey(),
                'data' => [
                    'last_name' => $this->last_name,
                    'first_name' => $this->first_name,
                    'middle_name' => $this->middle_name,
                    'email' => $this->email,
                ],
            ]);
            $this->user
                ->fill([
                    'last_name' => $this->last_name,
                    'first_name' => $this->first_name,
                    'middle_name' => !empty(trim($this->middle_name)) ? $this->middle_name : 'N/A',
                    'email' => $this->email,
                ])
                ->save();
            Log::info('User updated successfully');

            // Handle government ID PDF upload
            Log::debug('Processing government ID upload', [
                'has_current_image' => !empty($this->personalInformation->getAttribute('government_id_image_path')),
                'has_new_upload' => is_object($this->government_id_image_path),
            ]);
            $governmentIdImagePath = null;
            $currentImagePath = $this->personalInformation->getAttribute('government_id_image_path') ?? null;

            if (!empty($currentImagePath) && !is_object($this->government_id_image_path)) {
                // Keep the existing PDF
                $governmentIdImagePath = $currentImagePath;
            } elseif (is_object($this->government_id_image_path)) {
                // Upload the new PDF
                $governmentIdImagePath = $this->government_id_image_path->storeAs('government-ids', $this->user->getKey() . '_' . $this->government_id_image_path->getClientOriginalName(), 'public');
            }

            // Update Personal Information
            $this->personalInformation
                ->fill([
                    'user_id' => $this->user->getKey(),
                    'suffix' => !empty(trim($this->suffix)) ? $this->suffix : 'N/A',
                    'contact_no' => !empty(trim($this->contact_no)) ? $this->contact_no : null,
                    'sex_at_birth' => $this->sex_at_birth ?: null,
                    'date_of_birth' => $this->date_of_birth ?: null,
                    'place_of_birth' => $this->place_of_birth ?: null,
                    'civil_status' => $this->civil_status ?? 'Single',
                    'religion' => !empty(trim($this->religion)) ? $this->religion : 'N/A',
                    'nationality' => $this->nationality ?? 'Filipino',
                    'government_id_type' => !empty(trim($this->government_id_type)) ? $this->government_id_type : 'N/A',
                    'government_id_image_path' => $governmentIdImagePath,
                ])
                ->save();
            Log::info('Personal Information updated successfully');

            // Update User Addresses (Permanent Address)
            Log::debug('Updating Permanent Address', [
                'address_id' => $this->userAddresses->id ?? null,
                'data' => [
                    'personal_information_id' => $this->personalInformation->getKey(),
                    'address_type' => 'Permanent',
                    'address_line_1' => $this->address_line_1,
                    'address_line_2' => $this->address_line_2,
                    'region' => $this->region,
                    'province' => $this->province,
                    'city' => $this->city,
                    'barangay' => $this->barangay,
                    'street' => $this->street,
                    'zip_code' => $this->zip_code,
                ],
            ]);
            $this->userAddresses
                ->fill([
                    'personal_information_id' => $this->personalInformation->getKey(),
                    'address_type' => 'Permanent',
                    'address_line_1' => $this->address_line_1 ?: null,
                    'address_line_2' => !empty(trim($this->address_line_2)) ? $this->address_line_2 : 'N/A',
                    'region' => $this->region ?: null,
                    'province' => $this->province ?: null,
                    'city' => $this->city ?: null,
                    'barangay' => $this->barangay ?: null,
                    'street' => !empty(trim($this->street)) ? $this->street : 'N/A',
                    'zip_code' => $this->zip_code ?: null,
                ])
                ->save();
            Log::info('Permanent Address updated successfully');

            // Save Present Address as a separate record
            $hasTemporaryAddress = $this->temporary_address_line_1 || $this->temporary_address_line_2 || $this->temporary_region || $this->temporary_province || $this->temporary_city || $this->temporary_barangay || $this->temporary_street || $this->temporary_zip_code;

            Log::debug('Checking Temporary Address', [
                'has_temporary_address' => $hasTemporaryAddress,
                'temporary_data' => [
                    'address_line_1' => $this->temporary_address_line_1,
                    'address_line_2' => $this->temporary_address_line_2,
                    'region' => $this->temporary_region,
                    'province' => $this->temporary_province,
                    'city' => $this->temporary_city,
                    'barangay' => $this->temporary_barangay,
                    'street' => $this->temporary_street,
                    'zip_code' => $this->temporary_zip_code,
                ],
            ]);

            if ($hasTemporaryAddress) {
                Log::debug('Creating/Updating Temporary Address', [
                    'personal_information_id' => $this->personalInformation->getKey(),
                ]);

                $temporaryAddress = UserAddresses::updateOrCreate(
                    [
                        'personal_information_id' => $this->personalInformation->getKey(),
                        'address_type' => 'Temporary',
                    ],
                    [
                        'address_line_1' => $this->temporary_address_line_1 ?: null,
                        'address_line_2' => $this->temporary_address_line_2 ?: null,
                        'region' => $this->temporary_region ?: null,
                        'province' => $this->temporary_province ?: null,
                        'city' => $this->temporary_city ?: null,
                        'barangay' => $this->temporary_barangay ?: null,
                        'street' => $this->temporary_street ?: null,
                        'zip_code' => $this->temporary_zip_code ?: null,
                    ],
                );

                Log::info('Temporary Address saved', [
                    'address_id' => $temporaryAddress->id,
                    'was_recently_created' => $temporaryAddress->wasRecentlyCreated,
                ]);
            } else {
                Log::debug('No temporary address data provided, skipping...');
            }

            // Update User Family
            // For required fields (when not unknown), validation ensures they have values
            // For nullable fields, save as 'N/A' if empty
            Log::debug('Updating User Family', [
                'family_id' => $this->userFamily->id ?? null,
                'father_is_unknown' => $this->father_is_unknown,
                'mother_is_unknown' => $this->mother_is_unknown,
            ]);

            $this->userFamily
                ->fill([
                    // 'user_id' => $this->user->getKey(),
                    'personal_information_id' => $this->personalInformation->getKey(),
                    'father_last_name' => $this->father_is_unknown ? 'N/A' : ($this->father_last_name ?: 'N/A'),
                    'father_first_name' => $this->father_is_unknown ? 'N/A' : ($this->father_first_name ?: 'N/A'),
                    'father_middle_name' => !empty(trim($this->father_middle_name)) ? $this->father_middle_name : 'N/A',
                    'father_suffix' => !empty(trim($this->father_suffix)) ? $this->father_suffix : 'N/A',
                    'father_birthdate' => $this->father_birthdate ?: null,
                    'father_nationality' => $this->father_is_unknown ? 'N/A' : ($this->father_nationality ?: 'N/A'),
                    'father_religion' => $this->father_is_unknown ? 'N/A' : ($this->father_religion ?: 'N/A'),
                    'father_contact_no' => $this->father_is_unknown ? 'N/A' : ($this->father_contact_no ?: 'N/A'),
                    'mother_last_name' => $this->mother_is_unknown ? 'N/A' : ($this->mother_last_name ?: 'N/A'),
                    'mother_first_name' => $this->mother_is_unknown ? 'N/A' : ($this->mother_first_name ?: 'N/A'),
                    'mother_middle_name' => !empty(trim($this->mother_middle_name)) ? $this->mother_middle_name : 'N/A',
                    'mother_suffix' => !empty(trim($this->mother_suffix)) ? $this->mother_suffix : 'N/A',
                    'mother_birthdate' => $this->mother_birthdate ?: null,
                    'mother_nationality' => $this->mother_is_unknown ? 'N/A' : ($this->mother_nationality ?: 'N/A'),
                    'mother_religion' => $this->mother_is_unknown ? 'N/A' : ($this->mother_religion ?: 'N/A'),
                    'mother_contact_no' => $this->mother_is_unknown ? 'N/A' : ($this->mother_contact_no ?: 'N/A'),
                ])
                ->save();
            Log::info('User Family updated successfully');

            // Commit transaction
            DB::commit();
            Log::info('Database transaction committed successfully');

            if ($this->user->hasCompleteProfile()) {
                Log::info('Profile is complete, redirecting to dashboard');
                session()->flash('success', 'All information updated!');
                redirect()->route('client.dashboard');
            } else {
                Log::info('Profile update completed but profile is not yet complete');
                session()->flash('success', 'Information updated successfully!');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $messages = $e->validator->errors()->all();
            $errorDetails = [
                'validation_errors' => $e->validator->errors()->toArray(),
                'user_id' => $this->user->getKey(),
            ];
            Log::error('Validation failed in updateAll', $errorDetails);
            session()->flash('error', 'Validation failed: ' . implode(' ', $messages));
            return;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $errorDetails = [
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'user_id' => $this->user->getKey(),
            ];
            Log::error('Database query error in updateAll', $errorDetails);
            session()->flash('error', 'Database error occurred. Please check the logs for details. Error: ' . $e->getMessage());
            return;
        } catch (\Exception $e) {
            DB::rollBack();
            $errorDetails = [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => $this->user->getKey(),
            ];
            Log::error('Unexpected error in updateAll', $errorDetails);
            session()->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
            return;
        } finally {
            Log::info('=== UserInfo UpdateAll Completed ===');
        }
    }

    public function with()
    {
        return [
            'profileCompletion' => $this->user->getProfileCompletionPercentage(),
            'hasCompleteProfile' => $this->user->hasCompleteProfile(),
            'hasCompleteBasicProfile' => $this->user->hasCompleteBasicProfile(),
            'hasCompletePersonalInfo' => $this->user->hasCompletePersonalInfo(),
            'hasCompleteAddress' => $this->user->hasCompleteAddress(),
            'hasCompleteFamilyInfo' => $this->user->hasCompleteFamilyInfo(),
        ];
    }

    public function updatedFatherIsUnknown($value)
    {
        if ($value) {
            $this->father_last_name = 'N/A';
            $this->father_first_name = 'N/A';
            $this->father_middle_name = 'N/A';
            $this->father_suffix = 'N/A';
            $this->father_birthdate = '0001-01-01';
            $this->father_nationality = 'N/A';
            $this->father_religion = 'N/A';
            $this->father_contact_no = 'N/A';
        } else {
            $this->father_last_name = '';
            $this->father_first_name = '';
            $this->father_middle_name = '';
            $this->father_suffix = 'N/A';
            $this->father_birthdate = '0001-01-01';
            $this->father_nationality = '';
            $this->father_religion = '';
            $this->father_contact_no = '';
        }
    }

    public function updatedMotherIsUnknown($value)
    {
        if ($value) {
            $this->mother_last_name = 'N/A';
            $this->mother_first_name = 'N/A';
            $this->mother_middle_name = 'N/A';
            $this->mother_suffix = 'N/A';
            $this->mother_birthdate = '0001-01-01';
            $this->mother_nationality = 'N/A';
            $this->mother_religion = 'N/A';
            $this->mother_contact_no = 'N/A';
        } else {
            $this->mother_last_name = '';
            $this->mother_first_name = '';
            $this->mother_middle_name = '';
            $this->mother_suffix = 'N/A';
            $this->mother_birthdate = '0001-01-01';
            $this->mother_nationality = '';
            $this->mother_religion = '';
            $this->mother_contact_no = '';
        }
    }

    public function getGovernmentIdImageUrl()
    {
        if ($this->personalInformation && !empty($this->personalInformation->getAttribute('government_id_image_path'))) {
            return asset('storage/' . $this->personalInformation->getAttribute('government_id_image_path'));
        }
        return null;
    }
}; ?>

<div class="space-y-8">

    {{-- Flash Messages --}}
    @include('components.alert')

    <!-- Profile Completion Status -->
    <div class="flux-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Profile Completion</h2>
            <div class="text-right">
                <div class="text-2xl font-bold {{ $hasCompleteProfile ? 'text-green-600' : 'text-blue-600' }}">
                    {{ $profileCompletion }}%
                </div>
                <div class="text-sm text-gray-600">
                    {{ $hasCompleteProfile ? 'Complete' : 'Incomplete' }}
                </div>
            </div>
        </div>

        <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                style="width: {{ $profileCompletion }}%"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 rounded-full {{ $hasCompleteBasicProfile ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                <span class="text-sm {{ $hasCompleteBasicProfile ? 'text-green-700' : 'text-gray-600' }}">
                    Basic Information
                </span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 rounded-full {{ $hasCompletePersonalInfo ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                <span class="text-sm {{ $hasCompletePersonalInfo ? 'text-green-700' : 'text-gray-600' }}">
                    Personal Details
                </span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 rounded-full {{ $hasCompleteAddress ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                <span class="text-sm {{ $hasCompleteAddress ? 'text-green-700' : 'text-gray-600' }}">
                    Address
                </span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 rounded-full {{ $hasCompleteFamilyInfo ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                <span class="text-sm {{ $hasCompleteFamilyInfo ? 'text-green-700' : 'text-gray-600' }}">
                    Family Information
                </span>
            </div>
        </div>

        @if (!$hasCompleteProfile)
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-yellow-800 font-medium">
                        Complete your profile to access appointments and document requests
                    </span>
                </div>
            </div>
        @else
            <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-green-800 font-medium">
                        Profile complete! You can now access appointments and document requests
                    </span>
                </div>
            </div>
        @endif
    </div>


    @include('livewire.user.user-Information')

    @include('livewire.user.user-address')

    @include('livewire.user.user-family-information')



    {{-- Save Button --}}
    <div class="flex justify-end">
        <button class="flux-btn flux-btn-primary mt-4" x-data x-on:click="$dispatch('open-modal-confirm-save-all')">
            Save All
        </button>
    </div>

    {{-- Confirmation Modal --}}
    <x-modal id="confirm-save-all" title="Confirm Save" size="max-w-2xl">
        <div class="modal-body">
            <div class="text-sm space-y-4 p-2">
                <h3 class="font-semibold text-lg">Are you sure all the details are correct and up to date?</h3>
                <p>Please review all the information you have provided. If everything is correct, click "Confirm" to save all your information. If you need to make any changes, click "Cancel" to return to the form.</p>
                <p class="font-bold text-amber-600">Note: Once you confirm, your information will be saved and updated.</p>
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex gap-2">
                <button type="button" class="flux-btn flux-btn-outline" x-data
                    x-on:click="$dispatch('close-modal-confirm-save-all')">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" class="flux-btn flux-btn-primary"
                    wire:click="updateAll" wire:loading.attr="disabled"
                    x-on:click="$dispatch('close-modal-confirm-save-all')">
                    <span wire:loading.remove>
                        <i class="bi bi-check-circle me-1"></i>
                        Confirm & Save
                    </span>
                    <span wire:loading>
                        <span class="loading loading-spinner me-1"></span>
                        Saving...
                    </span>
                </button>
            </div>
        </x-slot>
    </x-modal>
</div>
