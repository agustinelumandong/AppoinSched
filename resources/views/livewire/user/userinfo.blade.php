<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\PersonalInformation;
use App\Models\UserAddresses;
use App\Models\UserFamily;
use App\Services\PhilippineLocationsService;

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

        // Create PersonalInformation if it doesn't exist yet
        if (!$this->user->personalInformation) {
            $personalInfo = new PersonalInformation();
            $personalInfo->user_id = $this->user->getKey();
            $personalInfo->save();
            $this->user->refresh();
        }

        $this->personalInformation = $this->user->personalInformation;

        // Create UserAddresses if it doesn't exist yet
        if (!$this->user->userAddresses || $this->user->userAddresses->isEmpty()) {
            $userAddress = new UserAddresses();
            $userAddress->personal_information_id = $this->personalInformation->getKey();
            $userAddress->save();
            $this->user->refresh();
        }

        $this->userAddresses = $this->user->userAddresses->first();

        // Create UserFamily if it doesn't exist yet
        if (!$this->user->userFamilies || $this->user->userFamilies->isEmpty()) {
            $userFamily = new UserFamily();
            $userFamily->user_id = $this->user->getKey();
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
        // Note: government_id_image_path is handled as a file upload, not a string
        // Address
        $this->address_type = $this->userAddresses->address_type ?? 'Permanent';
        $this->address_line_1 = $this->userAddresses->address_line_1 ?? '';
        $this->address_line_2 = $this->userAddresses->address_line_2 ?? '';
        $this->region = $this->userAddresses->region ?? '';
        $this->province = $this->userAddresses->province ?? '';
        $this->city = $this->userAddresses->city ?? '';
        $this->barangay = $this->userAddresses->barangay ?? '';
        // $this->street = $this->userAddresses->street ?? '';
        $this->zip_code = $this->userAddresses->zip_code ?? '';
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
        // Note: government_id_image_path is handled as a file upload, not a string
        // Address
        $this->address_type = $this->userAddresses->address_type ?? 'Permanent';
        $this->address_line_1 = $this->userAddresses->address_line_1 ?? '';
        $this->address_line_2 = $this->userAddresses->address_line_2 ?? '';
        $this->region = $this->userAddresses->region ?? '';
        $this->province = $this->userAddresses->province ?? '';
        $this->city = $this->userAddresses->city ?? '';
        $this->barangay = $this->userAddresses->barangay ?? '';
        // $this->street = $this->userAddresses->street ?? '';
        $this->zip_code = $this->userAddresses->zip_code ?? '';
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
        try {
            // Validate input using Laravel's validator
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
                'government_id_image_path' => ['nullable', 'image', 'max:2048'], // 2MB max
                'address_type' => ['required', 'string', 'max:50'],
                'address_line_1' => ['required', 'string', 'max:255'],
                'address_line_2' => ['required', 'string', 'max:255'],
                'region' => ['required', 'string', 'max:100'],
                'province' => ['required', 'string', 'max:100'],
                'city' => ['required', 'string', 'max:100'],
                'barangay' => ['required', 'string', 'max:100'],
                // 'street' => ['nullable', 'string', 'max:255'],
                'zip_code' => ['nullable', 'string', 'max:20'],
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
                session()->flash('error', 'Please fill up the following personal information fields: ' . implode(', ', $missingFields));
                return;
            }

            // Update User
            $this->user
                ->fill([
                    'last_name' => $this->last_name,
                    'first_name' => $this->first_name,
                    'middle_name' => $this->middle_name ?? 'N/A',
                    'email' => $this->email,
                ])
                ->save();

            // Handle government ID image upload
            $governmentIdImagePath = null;
            $currentImagePath = $this->personalInformation->getAttribute('government_id_image_path') ?? null;

            if (!empty($currentImagePath) && !is_object($this->government_id_image_path)) {
                // Keep the existing image
                $governmentIdImagePath = $currentImagePath;
            } elseif (is_object($this->government_id_image_path)) {
                // Upload the new image
                $governmentIdImagePath = $this->government_id_image_path->storeAs('government-ids', $this->user->getKey() . '_' . $this->government_id_image_path->getClientOriginalName(), 'public');
            }

            // Update Personal Information
            $this->personalInformation
                ->fill([
                    'user_id' => $this->user->getKey(),
                    'suffix' => $this->suffix ?? 'N/A',
                    'contact_no' => $this->contact_no ?: null,
                    'sex_at_birth' => $this->sex_at_birth ?: null,
                    'date_of_birth' => $this->date_of_birth ?: null,
                    'place_of_birth' => $this->place_of_birth ?: null,
                    'civil_status' => $this->civil_status ?? 'Single',
                    'religion' => $this->religion ?: null,
                    'nationality' => $this->nationality ?? 'Filipino',
                    'government_id_type' => $this->government_id_type ?: null,
                    'government_id_image_path' => $governmentIdImagePath,
                ])
                ->save();

            // Update User Addresses
            $this->userAddresses
                ->fill([
                    'personal_information_id' => $this->personalInformation->getKey(),
                    'address_type' => $this->address_type ?? 'Permanent',
                    'address_line_1' => $this->address_line_1 ?: null,
                    'address_line_2' => $this->address_line_2 ?: null,
                    'region' => $this->region ?: null,
                    'province' => $this->province ?: null,
                    'city' => $this->city ?: null,
                    'barangay' => $this->barangay ?: null,
                    // 'street' => $this->street ?: null,
                    'zip_code' => $this->zip_code ?: null,
                ])
                ->save();

            // Update User Family
            $this->userFamily
                ->fill([
                    'user_id' => $this->user->getKey(),
                    'personal_information_id' => $this->personalInformation->getKey(),
                    'father_last_name' => $this->father_last_name ?: null,
                    'father_first_name' => $this->father_first_name ?: null,
                    'father_middle_name' => $this->father_middle_name ?: null,
                    'father_suffix' => $this->father_suffix ?? 'N/A',
                    'father_birthdate' => $this->father_birthdate ?: null,
                    'father_nationality' => $this->father_nationality ?: null,
                    'father_religion' => $this->father_religion ?: null,
                    'father_contact_no' => $this->father_contact_no ?: null,
                    'mother_last_name' => $this->mother_last_name ?: null,
                    'mother_first_name' => $this->mother_first_name ?: null,
                    'mother_middle_name' => $this->mother_middle_name ?: null,
                    'mother_suffix' => $this->mother_suffix ?? 'N/A',
                    'mother_birthdate' => $this->mother_birthdate ?: null,
                    'mother_nationality' => $this->mother_nationality ?: null,
                    'mother_religion' => $this->mother_religion ?: null,
                    'mother_contact_no' => $this->mother_contact_no ?: null,
                ])
                ->save();


            if ($this->user->hasCompleteProfile()) {
                session()->flash('success', 'All information updated!');
                redirect()->route('client.dashboard');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            $messages = $e->validator->errors()->all();
            session()->flash('error', 'Validation failed: ' . implode(' ', $messages));
            return;
        } catch (\Exception $e) {
            session()->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
            return;
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

    <!-- User Info -->
    <div class="flux-card p-6">
        <h2 class="text-xl font-bold mb-4">Personal Information</h2>

        <div class="grid grid-cols-1 md:grid-cols-10 gap-4 mb-4">
            <div class="flex flex-col md:col-span-3">
                <label for="last_name" class="text-xs font-medium mb-1">Last Name</label>
                <input id="last_name" class="flux-form-control w-full" type="text" wire:model="last_name"
                    placeholder="Last Name">
                    <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col md:col-span-3">
                <label for="first_name" class="text-xs font-medium mb-1">First Name</label>
                <input id="first_name" class="flux-form-control w-full" type="text" wire:model="first_name"
                    placeholder="First Name">
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col md:col-span-3">
                <label for="middle_name" class="text-xs font-medium mb-1">Middle Name</label>
                <input id="middle_name" class="flux-form-control w-full" type="text" wire:model="middle_name"
                    placeholder="Middle Name">
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
            <div class="flex flex-col md:col-span-1">
                <label for="suffix" class="text-xs font-medium mb-1">Suffix</label>
                <select id="suffix" class="flux-form-control w-full" wire:model="suffix">
                    <option value="">Suffix</option>
                    <option value="N/A">N/A</option>
                    <option value="Jr.">Jr.</option>
                    <option value="Sr.">Sr.</option>
                    <option value="I">I</option>
                    <option value="II">II</option>
                    <option value="III">III</option>
                </select>
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="flex flex-col">
                <label for="email" class="text-xs font-medium mb-1">Email</label>
                <input id="email" class="flux-form-control" type="email" wire:model="email" placeholder="Email">
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="contact_no" class="text-xs font-medium mb-1">Contact No</label>
                <input id="contact_no" class="flux-form-control" type="text" wire:model="contact_no"
                    placeholder="Contact No">
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="sex_at_birth" class="text-xs font-medium mb-1">Sex at Birth</label>
                <select id="sex_at_birth" class="flux-form-control" wire:model="sex_at_birth">
                    <option value="">Select Sex at Birth</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="date_of_birth" class="text-xs font-medium mb-1">Date of Birth</label>
                <input id="date_of_birth" class="flux-form-control" type="date" wire:model="date_of_birth"
                    placeholder="Date of Birth">
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="place_of_birth" class="text-xs font-medium mb-1">Place of Birth</label>
                <input id="place_of_birth" class="flux-form-control" type="text" wire:model="place_of_birth"
                    placeholder="Place of Birth">
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="civil_status" class="text-xs font-medium mb-1">Civil Status</label>
                <select id="civil_status" class="flux-form-control" wire:model="civil_status">
                    <option value="">Civil Status</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Widowed">Widowed</option>
                    <option value="Divorced">Divorced</option>
                    <option value="Separated">Separated</option>
                </select>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col">
                <label for="religion" class="text-xs font-medium mb-1">Religion</label>
                <input id="religion" class="flux-form-control" type="text" wire:model="religion" placeholder="Religion">
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="nationality" class="text-xs font-medium mb-1">Nationality</label>
                <input id="nationality" class="flux-form-control" type="text" wire:model="nationality"
                    placeholder="Nationality">
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div class="flex flex-col">
                <label for="government_id_type" class="text-xs font-medium mb-1">Government ID Type</label>
                <select id="government_id_type" class="flux-form-control" wire:model="government_id_type">
                    <option value="">Select Government ID Type</option>
                    <option value="SSS">SSS</option>
                    <option value="GSIS">GSIS</option>
                    <option value="TIN">TIN</option>
                    <option value="PhilHealth">PhilHealth</option>
                    <option value="Passport">Passport</option>
                    <option value="Driver's License">Driver's License</option>
                    <option value="UMID">UMID</option>
                    <option value="Postal ID">Postal ID</option>
                    <option value="Voter's ID">Voter's ID</option>
                    <option value="Senior Citizen ID">Senior Citizen ID</option>
                    <option value="Other">Other</option>
                </select>
                <span class="text-xs text-gray-500 mt-1">Select your primary government ID</span>
                @error('government_id_type')
                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>

            <div class="flex flex-col">
                <label for="government_id_image_path" class="text-xs font-medium mb-1">Government ID Image</label>
                <input id="government_id_image_path" class="flux-form-control" type="file"
                    wire:model="government_id_image_path" accept="image/*" required>
                <span class="text-xs text-gray-500 mt-1">Upload a clear image of your government ID (Max 2MB)</span>
                @error('government_id_image_path')
                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                @enderror
                @if ($this->getGovernmentIdImageUrl())
                    <div class="mt-2">
                        <p class="text-xs text-gray-600 mb-1">Current ID Image:</p>
                        <img src="{{ $this->getGovernmentIdImageUrl() }}" alt="Government ID"
                            class="w-32 h-20 object-cover border rounded">
                    </div>
                @endif
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
        </div>
    </div>

    <!-- Address Information -->
    <div class="flux-card p-6">
        <h2 class="text-xl font-bold mb-4">Address</h2>
        <div class="flex flex-col mb-4">
            <label for="address_type" class="text-xs font-medium mb-1">Address Type</label>
            <select id="address_type" class="flux-form-control" wire:model="address_type">
                <option value="">Address Type</option>
                <option value="Permanent">Permanent</option>
                <option value="Temporary">Temporary</option>
            </select>
        </div>
        <div class="flex flex-col mb-4">
            <label for="address_line_1" class="text-xs font-medium mb-1">Address Line 1</label>
            <input id="address_line_1" class="flux-form-control" type="text" wire:model="address_line_1"
                placeholder="Address Line 1">
            <span class="text-xs text-gray-500 mt-1">Required</span>
        </div>
        <div class="flex flex-col mb-4">
            <label for="address_line_2" class="text-xs font-medium mb-1">Address Line 2</label>
            <input id="address_line_2" class="flux-form-control" type="text" wire:model="address_line_2"
                placeholder="Address Line 2">
            <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex flex-col">
                <label for="region" class="text-xs font-medium mb-1">Region</label>
                <select id="region" class="flux-form-control" wire:model.live="region">
                    <option value="">Select Region</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region['code'] }}">{{ $region['name'] }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="province" class="text-xs font-medium mb-1">Province</label>
                <select id="province" class="flux-form-control" wire:model.live="province">
                    <option value="">Select Province</option>
                    @foreach ($provinces as $provinceKey => $provinceName)
                        <option value="{{ $provinceKey }}">{{ $provinceName }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="city" class="text-xs font-medium mb-1">City</label>
                <select id="city" class="flux-form-control" wire:model.live="city">
                    <option value="">Select City</option>
                    @foreach ($cities as $cityKey => $cityName)
                        <option value="{{ $cityKey }}">{{ $cityName }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="barangay" class="text-xs font-medium mb-1">Barangay</label>
                <select id="barangay" class="flux-form-control" wire:model.live="barangay">
                    <option value="">Select Barangay</option>
                    @foreach ($barangays as $barangay)
                        <option value="{{ $barangay }}">{{ $barangay }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
            <div class="flex flex-col">
                <label for="street" class="text-xs font-medium mb-1">Street</label>
                <input id="street" class="flux-form-control" type="text" wire:model="street" placeholder="Street">
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
            <div class="flex flex-col">
                <label for="zip_code" class="text-xs font-medium mb-1">Zip Code</label>
                <input id="zip_code" class="flux-form-control" type="text" wire:model="zip_code" placeholder="Zip Code">
                <span class="text-xs text-gray-500 mt-1">Required</span>
            </div>
        </div>
    </div>

    <!-- Family Information -->
    <div class="flux-card p-6">
        <h2 class="text-xl font-bold mb-4">Family Information</h2>
        <div>
            <h5 class="text-md font-bold mb-4">Father</h5>
            <div class="form-control ">
              <label class="label cursor-pointer">
                <span class="label-text">Father is Unknown</span>
                <input type="checkbox" wire:model.live="father_is_unknown"
                  class="checkbox" />
              </label>
              
            </div>
            <div class="flex flex-row md:flex-col gap-4 mb-4">
              <div class="w-full md:w-1/3">
                <label for="father_last_name" class="block text-xs font-medium mb-1">Last Name</label>
                <input class="flux-form-control md:col-span-3 w-full"
                  type="text" wire:model="father_last_name" placeholder="Last Name" name="father_last_name"
                  id="father_last_name" required {{ $father_is_unknown ? 'disabled' : '' }} >
                @error('father_last_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
              </div>
              <div class="w-full md:w-1/3">
                <label for="father_first_name" class="block text-xs font-medium mb-1">First Name</label>
                <input class="flux-form-control md:col-span-3 w-full"
                  type="text" wire:model="father_first_name" placeholder="First Name" name="father_first_name"
                  id="father_first_name" required {{ $father_is_unknown ? 'disabled' : '' }} >
                @error('father_first_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                <span class="text-xs text-gray-500 mt-1">Required</span>
              </div>
              <div class="w-full md:w-1/3">
                <label for="father_middle_name" class="block text-xs font-medium mb-1">Middle Name</label>
                <input class="flux-form-control md:col-span-3 w-full"
                  type="text" wire:model="father_middle_name" placeholder="Middle Name" name="father_middle_name"
                  id="father_middle_name" required {{ $father_is_unknown ? 'disabled' : '' }} >
                @error('father_middle_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
              </div>
              <div class="w-1/7">
                <label for="father_suffix" class="block text-xs font-medium mb-1">Suffix</label>
                <select class="flux-form-control md:col-span-1 w-full"
                  wire:model="father_suffix" name="father_suffix" id="father_suffix" {{ $father_is_unknown ? 'disabled' : '' }}
                  >
                  <option value="">Suffix</option>
                  <option value="N/A">N/A</option>
                  <option value="Jr.">Jr.</option>
                  <option value="Sr.">Sr.</option>
                  <option value="I">I</option>
                  <option value="II">II</option>
                  <option value="III">III</option>
                </select>
                @error('father_suffix')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
              </div>
            </div>
            <div class="flex flex-row md:flex-col gap-4 mb-4">
              <div class="w-full md:w-1/3">
                <label for="father_birthdate" class="block text-xs font-medium mb-1">Date of Birth</label>
                <input class="flux-form-control" type="date"
                  wire:model="father_birthdate" name="father_birthdate" id="father_birthdate" {{ $father_is_unknown ? 'disabled' : '' }} >
                @error('father_birthdate')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                <span class="text-xs text-gray-500 mt-1">Required</span>
              </div>
              <div class="w-full md:w-1/3">
                <label for="father_nationality" class="block text-xs font-medium mb-1">Nationality</label>
                <input class="flux-form-control" type="text"
                  wire:model="father_nationality" placeholder="Nationality" name="father_nationality" id="father_nationality"
                  required {{ $father_is_unknown ? 'disabled' : '' }} >
                @error('father_nationality')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
              </div>
              <div class="w-full md:w-1/3">
                <label for="father_religion" class="block text-xs font-medium mb-1">Religion</label>
                <input class="flux-form-control" type="text"
                  wire:model="father_religion" placeholder="Religion" name="father_religion" id="father_religion" required {{ $father_is_unknown ? 'disabled' : '' }} >
                @error('father_religion')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
              </div>
              <div class="w-full md:w-1/3">
                <label for="father_contact_no" class="block text-xs font-medium mb-1">Contact No.</label>
                    <input class="flux-form-control" type="text"
                  wire:model="father_contact_no" placeholder="Contact Number" name="father_contact_no" id="father_contact_no"
                  required {{ $father_is_unknown ? 'disabled' : '' }} >
                @error('father_contact_no')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>              </div>
            </div>
          </div>
        <div>
            <h5 class="text-md font-bold mb-4">Mother</h5>
            <div class="form-control">
                <label class="label cursor-pointer">
                    <span class="label-text">Mother is Unknown</span>
                    <input type="checkbox" wire:model.live="mother_is_unknown" class="checkbox" />
                </label>    
            </div>
            <div class="flex flex-row md:flex-col gap-4 mb-4">
                <div class="w-full md:w-1/3">
                    <label for="mother_last_name" class="block text-xs font-medium mb-1">Last Name</label>
                    <input class="flux-form-control md:col-span-3 w-full " type="text" wire:model="mother_last_name"
                        placeholder="Last Name" name="mother_last_name" id="mother_last_name" {{ $mother_is_unknown ? 'disabled' : '' }}>
                    @error('mother_last_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>                </div>
                <div class="w-full md:w-1/3">
                    <label for="mother_first_name" class="block text-xs font-medium mb-1">First Name</label>
                    <input class="flux-form-control md:col-span-3 w-full " type="text" wire:model="mother_first_name"
                        placeholder="First Name" name="mother_first_name" id="mother_first_name" {{ $mother_is_unknown ? 'disabled' : '' }}>
                    @error('mother_first_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>                </div>
                <div class="w-full md:w-1/3">
                    <label for="mother_middle_name" class="block text-xs font-medium mb-1">Middle Name</label>
                    <input class="flux-form-control md:col-span-3 w-full " type="text" wire:model="mother_middle_name"
                        placeholder="Middle Name" name="mother_middle_name" id="mother_middle_name" {{ $mother_is_unknown ? 'disabled' : '' }}>
                    @error('mother_middle_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>                </div>
                </div>
            </div>
            <div class="flex flex-row md:flex-col gap-4 mb-4">
                <div class="w-full md:w-1/3">
                    <label for="mother_birthdate" class="block text-xs font-medium mb-1">Mother's Birthdate</label>
                    <input class="flux-form-control " type="date" wire:model="mother_birthdate"
                        placeholder="Mother's Birthdate" name="mother_birthdate" id="mother_birthdate" {{ $mother_is_unknown ? 'disabled' : '' }}>
                    @error('mother_birthdate')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>                </div>
                <div class="w-full md:w-1/3">
                    <label for="mother_nationality" class="block text-xs font-medium mb-1">Mother's Nationality</label>
                    <input class="flux-form-control " type="text" wire:model="mother_nationality"
                        placeholder="Mother's Nationality" name="mother_nationality" id="mother_nationality" {{ $mother_is_unknown ? 'disabled' : '' }}>
                    @error('mother_nationality')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>                </div>
                </div>
                <div class="w-full md:w-1/3">
                    <label for="mother_religion" class="block text-xs font-medium mb-1">Mother's Religion</label>
                    <input class="flux-form-control " type="text" wire:model="mother_religion"
                        placeholder="Mother's Religion" name="mother_religion" id="mother_religion" {{ $mother_is_unknown ? 'disabled' : '' }}>
                    @error('mother_religion')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>                </div>
                </div>
                <div class="w-full md:w-1/3">
                    <label for="mother_contact_no" class="block text-xs font-medium mb-1">Mother's Contact No</label>
                    <input class="flux-form-control     " type="text" wire:model="mother_contact_no"
                        placeholder="Mother's Contact No" name="mother_contact_no" id="mother_contact_no" {{ $mother_is_unknown ? 'disabled' : '' }}>
                    @error('mother_contact_no')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>                </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Save Button --}}
    <div class="flex justify-end">
        <button class="flux-btn flux-btn-primary mt-4" wire:click="updateAll">Save All</button>
    </div>
</div>