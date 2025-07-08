<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\PersonalInformation;
use App\Models\UserAddresses;
use App\Models\UserFamily;

new class extends Component {
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
    public string $address_type = '';
    public string $address_line_1 = '';
    public string $address_line_2 = '';
    public string $region = '';
    public string $province = '';
    public string $city = '';
    public string $barangay = '';
    public string $street = '';
    public string $zip_code = '';
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
    public string $spouse_last_name = '';
    public string $spouse_first_name = '';
    public string $spouse_middle_name = '';
    public string $spouse_suffix = 'N/A';
    public string $spouse_birthdate = '';
    public string $spouse_nationality = '';
    public string $spouse_religion = '';
    public string $spouse_contact_no = '';
    public bool $father_is_unknown = false;
    public bool $spouse_is_unknown = false;

    public function mount()
    {
        $this->user = auth()->user();
        $this->personalInformation = $this->user->personalInformation ?? new PersonalInformation(['user_id' => $this->user->id]);
        $this->userAddresses = $this->user->userAddresses->first() ?? new UserAddresses(['personal_information_id' => $this->personalInformation->id]);
        $this->userFamily = $this->user->userFamilies->first() ?? new UserFamily(['user_id' => $this->user->id]);

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
        // Family - Spouse
        $this->spouse_last_name = $this->userFamily->spouse_last_name ?? '';
        $this->spouse_first_name = $this->userFamily->spouse_first_name ?? '';
        $this->spouse_middle_name = $this->userFamily->spouse_middle_name ?? '';
        $this->spouse_suffix = $this->userFamily->spouse_suffix ?? 'N/A';
        $this->spouse_birthdate = $this->userFamily->spouse_birthdate ?? '';
        $this->spouse_nationality = $this->userFamily->spouse_nationality ?? '';
        $this->spouse_religion = $this->userFamily->spouse_religion ?? '';
        $this->spouse_contact_no = $this->userFamily->spouse_contact_no ?? '';

        // Set father_is_unknown if all father fields are N/A
        $this->father_is_unknown = (
            $this->father_last_name === 'N/A' &&
            $this->father_first_name === 'N/A' &&
            $this->father_middle_name === 'N/A' &&
            $this->father_suffix === 'N/A' &&
            $this->father_nationality === 'N/A' &&
            $this->father_religion === 'N/A' &&
            $this->father_contact_no === 'N/A'
        );
        // Set spouse_is_unknown if all spouse fields are N/A
        $this->spouse_is_unknown = (
            $this->spouse_last_name === 'N/A' &&
            $this->spouse_first_name === 'N/A' &&
            $this->spouse_middle_name === 'N/A' &&
            $this->spouse_suffix === 'N/A' &&
            $this->spouse_nationality === 'N/A' &&
            $this->spouse_religion === 'N/A' &&
            $this->spouse_contact_no === 'N/A'
        );
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
        // Validate input using Laravel's validator
        $validated = $this->validate([
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:10'],
            'email' => ['required', 'email', 'max:255'],
            'contact_no' => ['nullable', 'string', 'max:20'],
            'sex_at_birth' => ['nullable', 'in:Male,Female'],
            'date_of_birth' => ['nullable', 'date'],
            'place_of_birth' => ['nullable', 'string', 'max:255'],
            'civil_status' => ['nullable', 'string', 'max:50'],
            'religion' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'address_type' => ['nullable', 'string', 'max:50'],
            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'barangay' => ['nullable', 'string', 'max:100'],
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
            'mother_last_name' => ['required', 'string', 'max:255'],
            'mother_first_name' => ['required', 'string', 'max:255'],
            'mother_middle_name' => ['nullable', 'string', 'max:255'],
            'mother_suffix' => ['nullable', 'string', 'max:10'],
            'mother_birthdate' => ['nullable', 'date'],
            'mother_nationality' => ['required', 'string', 'max:100'],
            'mother_religion' => ['required', 'string', 'max:100'],
            'mother_contact_no' => ['required', 'string', 'max:20'],
            'spouse_last_name' => [$this->spouse_is_unknown ? 'nullable' : 'string', 'max:255'],
            'spouse_first_name' => [$this->spouse_is_unknown ? 'nullable' : 'string', 'max:255'],
            'spouse_middle_name' => ['nullable', 'string', 'max:255'],
            'spouse_suffix' => ['nullable', 'string', 'max:10'],
            'spouse_birthdate' => ['nullable', 'date'],
            'spouse_nationality' => [$this->spouse_is_unknown ? 'nullable' : 'string', 'max:100'],
            'spouse_religion' => [$this->spouse_is_unknown ? 'nullable' : 'string', 'max:100'],
            'spouse_contact_no' => [$this->spouse_is_unknown ? 'nullable' : 'string', 'max:20'],
        ]);

        // Update User
        $this->user
            ->fill([
                'last_name' => $this->last_name,
                'first_name' => $this->first_name,
                'middle_name' => $this->middle_name ?? 'N/A',
                'email' => $this->email,
            ])
            ->save();

        // Update Personal Information
        $this->personalInformation
            ->fill([
                'user_id' => $this->user->id,
                'suffix' => $this->suffix ?? 'N/A',
                'contact_no' => $this->contact_no ?: null,
                'sex_at_birth' => $this->sex_at_birth ?: null,
                'date_of_birth' => $this->date_of_birth ?: null,
                'place_of_birth' => $this->place_of_birth ?: null,
                'civil_status' => $this->civil_status ?? 'Single',
                'religion' => $this->religion ?: null,
                'nationality' => $this->nationality ?? 'Filipino',
            ])
            ->save();

        // Update User Addresses
        $this->userAddresses
            ->fill([
                // 'user_id' => $this->user->id,
                'personal_information_id' => $this->personalInformation->id,
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
                'user_id' => $this->user->id,
                'personal_information_id' => $this->personalInformation->id,
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
                'spouse_last_name' => $this->spouse_last_name ?: null,
                'spouse_first_name' => $this->spouse_first_name ?: null,
                'spouse_middle_name' => $this->spouse_middle_name ?: null,
                'spouse_suffix' => $this->spouse_suffix ?? 'N/A',
                'spouse_birthdate' => $this->spouse_birthdate ?: null,
                'spouse_nationality' => $this->spouse_nationality ?: null,
                'spouse_religion' => $this->spouse_religion ?: null,
                'spouse_contact_no' => $this->spouse_contact_no ?: null,
            ])
            ->save();

        session()->flash('success', 'All information updated!');
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
            $this->father_birthdate = '';
            $this->father_nationality = 'N/A';
            $this->father_religion = 'N/A';
            $this->father_contact_no = 'N/A';
        } else {
            $this->father_last_name = '';
            $this->father_first_name = '';
            $this->father_middle_name = '';
            $this->father_suffix = 'N/A';
            $this->father_birthdate = '';
            $this->father_nationality = '';
            $this->father_religion = '';
            $this->father_contact_no = '';
        }
    }

    public function updatedSpouseIsUnknown($value)
    {
        if ($value) {
            $this->spouse_last_name = 'N/A';
            $this->spouse_first_name = 'N/A';
            $this->spouse_middle_name = 'N/A';
            $this->spouse_suffix = 'N/A';
            $this->spouse_birthdate = '';
            $this->spouse_nationality = 'N/A';
            $this->spouse_religion = 'N/A';
            $this->spouse_contact_no = 'N/A';
        } else {
            $this->spouse_last_name = '';
            $this->spouse_first_name = '';
            $this->spouse_middle_name = '';
            $this->spouse_suffix = 'N/A';
            $this->spouse_birthdate = '';
            $this->spouse_nationality = '';
            $this->spouse_religion = '';
            $this->spouse_contact_no = '';
        }
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
            </div>
            <div class="flex flex-col md:col-span-3">
                <label for="first_name" class="text-xs font-medium mb-1">First Name</label>
                <input id="first_name" class="flux-form-control w-full" type="text" wire:model="first_name"
                    placeholder="First Name">
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
                </select>
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="flex flex-col">
                <label for="email" class="text-xs font-medium mb-1">Email</label>
                <input id="email" class="flux-form-control" type="email" wire:model="email" placeholder="Email">
            </div>
            <div class="flex flex-col">
                <label for="contact_no" class="text-xs font-medium mb-1">Contact No</label>
                <input id="contact_no" class="flux-form-control" type="text" wire:model="contact_no"
                    placeholder="Contact No">
            </div>
            <div class="flex flex-col">
                <label for="sex_at_birth" class="text-xs font-medium mb-1">Sex at Birth</label>
                <select id="sex_at_birth" class="flux-form-control" wire:model="sex_at_birth">
                    <option value="">Select Sex at Birth</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="flex flex-col">
                <label for="date_of_birth" class="text-xs font-medium mb-1">Date of Birth</label>
                <input id="date_of_birth" class="flux-form-control" type="date" wire:model="date_of_birth"
                    placeholder="Date of Birth">
            </div>
            <div class="flex flex-col">
                <label for="place_of_birth" class="text-xs font-medium mb-1">Place of Birth</label>
                <input id="place_of_birth" class="flux-form-control" type="text" wire:model="place_of_birth"
                    placeholder="Place of Birth">
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
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col">
                <label for="religion" class="text-xs font-medium mb-1">Religion</label>
                <input id="religion" class="flux-form-control" type="text" wire:model="religion" placeholder="Religion">
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
            <div class="flex flex-col">
                <label for="nationality" class="text-xs font-medium mb-1">Nationality</label>
                <input id="nationality" class="flux-form-control" type="text" wire:model="nationality"
                    placeholder="Nationality">
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
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
                <input id="region" class="flux-form-control" type="text" wire:model="region" placeholder="Region">
            </div>
            <div class="flex flex-col">
                <label for="province" class="text-xs font-medium mb-1">Province</label>
                <input id="province" class="flux-form-control" type="text" wire:model="province" placeholder="Province">
            </div>
            <div class="flex flex-col">
                <label for="city" class="text-xs font-medium mb-1">City</label>
                <input id="city" class="flux-form-control" type="text" wire:model="city" placeholder="City">
            </div>
            <div class="flex flex-col">
                <label for="barangay" class="text-xs font-medium mb-1">Barangay</label>
                <input id="barangay" class="flux-form-control" type="text" wire:model="barangay" placeholder="Barangay">
            </div>
            <div class="flex flex-col">
                <label for="street" class="text-xs font-medium mb-1">Street</label>
                <input id="street" class="flux-form-control" type="text" wire:model="street" placeholder="Street">
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
            <div class="flex flex-col">
                <label for="zip_code" class="text-xs font-medium mb-1">Zip Code</label>
                <input id="zip_code" class="flux-form-control" type="text" wire:model="zip_code" placeholder="Zip Code">
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
        </div>
    </div>

    <!-- Family Information -->
    <div class="flux-card p-6">
        <h2 class="text-xl font-bold mb-4">Family Information</h2>
        <h4 class="text-lg font-bold mb-4">Father</h4>
        <div class="form-control mb-2">
            <label class="label cursor-pointer">
                <span class="label-text">Father is Unknown</span>
                <input type="checkbox" wire:model.live="father_is_unknown" class="checkbox" />
            </label>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-10 gap-4 mb-4">
            @if($father_is_unknown)
                <div class="flex flex-col md:col-span-3">
                    <label class="text-xs font-medium mb-1">Last Name</label>
                    <span class="flux-form-control w-full bg-gray-100">N/A</span>
                </div>
                <div class="flex flex-col md:col-span-3">
                    <label class="text-xs font-medium mb-1">First Name</label>
                    <span class="flux-form-control w-full bg-gray-100">N/A</span>
                </div>
                <div class="flex flex-col md:col-span-3">
                    <label class="text-xs font-medium mb-1">Middle Name</label>
                    <span class="flux-form-control w-full bg-gray-100">N/A</span>
                </div>
                <div class="flex flex-col md:col-span-1">
                    <label class="text-xs font-medium mb-1">Suffix</label>
                    <span class="flux-form-control w-full bg-gray-100">N/A</span>
                </div>
            @else
                <div class="flex flex-col md:col-span-3">
                    <label for="father_last_name" class="text-xs font-medium mb-1">Last Name</label>
                    <input id="father_last_name" class="flux-form-control w-full" type="text"
                        wire:model="father_last_name" placeholder="Last Name">
                </div>
                <div class="flex flex-col md:col-span-3">
                    <label for="father_first_name" class="text-xs font-medium mb-1">First Name</label>
                    <input id="father_first_name" class="flux-form-control w-full" type="text"
                        wire:model="father_first_name" placeholder="First Name">
                </div>
                <div class="flex flex-col md:col-span-3">
                    <label for="father_middle_name" class="text-xs font-medium mb-1">Middle Name</label>
                    <input id="father_middle_name" class="flux-form-control w-full" type="text"
                        wire:model="father_middle_name" placeholder="Middle Name">
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
                <div class="flex flex-col md:col-span-1">
                    <label for="father_suffix" class="text-xs font-medium mb-1">Suffix</label>
                    <select id="father_suffix" class="flux-form-control w-full" wire:model="father_suffix">
                        <option value="">Suffix</option>
                        <option value="N/A">N/A</option>
                        <option value="Jr.">Jr.</option>
                        <option value="Sr.">Sr.</option>
                    </select>
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
            @endif
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            @if($father_is_unknown)
                <div class="flex flex-col">
                    <label class="text-xs font-medium mb-1">Father's Birthdate</label>
                    <span class="flux-form-control bg-gray-100">N/A</span>
                </div>
                <div class="flex flex-col">
                    <label class="text-xs font-medium mb-1">Father's Nationality</label>
                    <span class="flux-form-control bg-gray-100">N/A</span>
                </div>
                <div class="flex flex-col">
                    <label class="text-xs font-medium mb-1">Father's Religion</label>
                    <span class="flux-form-control bg-gray-100">N/A</span>
                </div>
                <div class="flex flex-col">
                    <label class="text-xs font-medium mb-1">Father's Contact No</label>
                    <span class="flux-form-control bg-gray-100">N/A</span>
                </div>
            @else
                <div class="flex flex-col">
                    <label for="father_birthdate" class="text-xs font-medium mb-1">Father's Birthdate</label>
                    <input id="father_birthdate" class="flux-form-control" type="date" wire:model="father_birthdate"
                        placeholder="Father's Birthdate">
                </div>
                <div class="flex flex-col">
                    <label for="father_nationality" class="text-xs font-medium mb-1">Father's Nationality</label>
                    <input id="father_nationality" class="flux-form-control" type="text"
                        wire:model="father_nationality" placeholder="Father's Nationality">
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
                <div class="flex flex-col">
                    <label for="father_religion" class="text-xs font-medium mb-1">Father's Religion</label>
                    <input id="father_religion" class="flux-form-control" type="text" wire:model="father_religion"
                        placeholder="Father's Religion">
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
                <div class="flex flex-col">
                    <label for="father_contact_no" class="text-xs font-medium mb-1">Father's Contact No</label>
                    <input id="father_contact_no" class="flux-form-control" type="text"
                        wire:model="father_contact_no" placeholder="Father's Contact No">
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
            @endif
        </div>
        <h4 class="text-lg font-bold mb-4">Mother</h4>
        <div class="grid grid-cols-1 md:grid-cols-10 gap-4 mb-4">
            <div class="flex flex-col md:col-span-3">
                <label for="mother_last_name" class="text-xs font-medium mb-1">Last Name</label>
                <input id="mother_last_name" class="flux-form-control w-full" type="text" wire:model="mother_last_name"
                    placeholder="Last Name">
            </div>
            <div class="flex flex-col md:col-span-3">
                <label for="mother_first_name" class="text-xs font-medium mb-1">First Name</label>
                <input id="mother_first_name" class="flux-form-control w-full" type="text"
                    wire:model="mother_first_name" placeholder="First Name">
            </div>
            <div class="flex flex-col md:col-span-3">
                <label for="mother_middle_name" class="text-xs font-medium mb-1">Middle Name</label>
                <input id="mother_middle_name" class="flux-form-control w-full" type="text"
                    wire:model="mother_middle_name" placeholder="Middle Name">
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
            <div class="flex flex-col md:col-span-1">
                <label for="mother_suffix" class="text-xs font-medium mb-1">Suffix</label>
                <select id="mother_suffix" class="flux-form-control w-full" wire:model="mother_suffix">
                    <option value="">Suffix</option>
                    <option value="N/A">N/A</option>
                    <option value="Jr.">Jr.</option>
                    <option value="Sr.">Sr.</option>
                </select>
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="flex flex-col">
                <label for="mother_birthdate" class="text-xs font-medium mb-1">Mother's Birthdate</label>
                <input id="mother_birthdate" class="flux-form-control" type="date" wire:model="mother_birthdate"
                    placeholder="Mother's Birthdate">
            </div>
            <div class="flex flex-col">
                <label for="mother_nationality" class="text-xs font-medium mb-1">Mother's Nationality</label>
                <input id="mother_nationality" class="flux-form-control" type="text" wire:model="mother_nationality"
                    placeholder="Mother's Nationality">
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
            <div class="flex flex-col">
                <label for="mother_religion" class="text-xs font-medium mb-1">Mother's Religion</label>
                <input id="mother_religion" class="flux-form-control" type="text" wire:model="mother_religion"
                    placeholder="Mother's Religion">
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
            <div class="flex flex-col">
                <label for="mother_contact_no" class="text-xs font-medium mb-1">Mother's Contact No</label>
                <input id="mother_contact_no" class="flux-form-control" type="text" wire:model="mother_contact_no"
                    placeholder="Mother's Contact No">
                <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
            </div>
        </div>
        <h4 class="text-lg font-bold mb-4">Spouse</h4>
        <div class="form-control mb-2">
            <label class="label cursor-pointer">
                <span class="label-text">Spouse is Unknown</span>
                <input type="checkbox" wire:model.live="spouse_is_unknown" class="checkbox" />
            </label>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-10 gap-4 mb-4">
            @if($spouse_is_unknown)
                <div class="flex flex-col md:col-span-3">
                    <label class="text-xs font-medium mb-1">Last Name</label>
                    <span class="flux-form-control w-full bg-gray-100">N/A</span>
                </div>
                <div class="flex flex-col md:col-span-3">
                    <label class="text-xs font-medium mb-1">First Name</label>
                    <span class="flux-form-control w-full bg-gray-100">N/A</span>
                </div>
                <div class="flex flex-col md:col-span-3">
                    <label class="text-xs font-medium mb-1">Middle Name</label>
                    <span class="flux-form-control w-full bg-gray-100">N/A</span>
                </div>
                <div class="flex flex-col md:col-span-1">
                    <label class="text-xs font-medium mb-1">Suffix</label>
                    <span class="flux-form-control w-full bg-gray-100">N/A</span>
                </div>
            @else
                <div class="flex flex-col md:col-span-3">
                    <label for="spouse_last_name" class="text-xs font-medium mb-1">Last Name</label>
                    <input id="spouse_last_name" class="flux-form-control w-full" type="text"
                        wire:model="spouse_last_name" placeholder="Last Name">
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
                <div class="flex flex-col md:col-span-3">
                    <label for="spouse_first_name" class="text-xs font-medium mb-1">First Name</label>
                    <input id="spouse_first_name" class="flux-form-control w-full" type="text"
                        wire:model="spouse_first_name" placeholder="First Name">
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
                <div class="flex flex-col md:col-span-3">
                    <label for="spouse_middle_name" class="text-xs font-medium mb-1">Middle Name</label>
                    <input id="spouse_middle_name" class="flux-form-control w-full" type="text"
                        wire:model="spouse_middle_name" placeholder="Middle Name">
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
                <div class="flex flex-col md:col-span-1">
                    <label for="spouse_suffix" class="text-xs font-medium mb-1">Suffix</label>
                    <select id="spouse_suffix" class="flux-form-control w-full" wire:model="spouse_suffix">
                        <option value="">Suffix</option>
                        <option value="N/A">N/A</option>
                        <option value="Jr.">Jr.</option>
                        <option value="Sr.">Sr.</option>
                    </select>
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
            @endif
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            @if($spouse_is_unknown)
                <div class="flex flex-col">
                    <label class="text-xs font-medium mb-1">Spouse's Birthdate</label>
                    <span class="flux-form-control bg-gray-100">N/A</span>
                </div>
                <div class="flex flex-col">
                    <label class="text-xs font-medium mb-1">Spouse's Nationality</label>
                    <span class="flux-form-control bg-gray-100">N/A</span>
                </div>
                <div class="flex flex-col">
                    <label class="text-xs font-medium mb-1">Spouse's Religion</label>
                    <span class="flux-form-control bg-gray-100">N/A</span>
                </div>
                <div class="flex flex-col">
                    <label class="text-xs font-medium mb-1">Spouse's Contact No</label>
                    <span class="flux-form-control bg-gray-100">N/A</span>
                </div>
            @else
                <div class="flex flex-col">
                    <label for="spouse_birthdate" class="text-xs font-medium mb-1">Spouse's Birthdate</label>
                    <input id="spouse_birthdate" class="flux-form-control" type="date" wire:model="spouse_birthdate"
                        placeholder="Spouse's Birthdate">
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
                <div class="flex flex-col">
                    <label for="spouse_nationality" class="text-xs font-medium mb-1">Spouse's Nationality</label>
                    <input id="spouse_nationality" class="flux-form-control" type="text"
                        wire:model="spouse_nationality" placeholder="Spouse's Nationality">
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
                <div class="flex flex-col">
                    <label for="spouse_religion" class="text-xs font-medium mb-1">Spouse's Religion</label>
                    <input id="spouse_religion" class="flux-form-control" type="text" wire:model="spouse_religion"
                        placeholder="Spouse's Religion">
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
                <div class="flex flex-col">
                    <label for="spouse_contact_no" class="text-xs font-medium mb-1">Spouse's Contact No</label>
                    <input id="spouse_contact_no" class="flux-form-control" type="text"
                        wire:model="spouse_contact_no" placeholder="Spouse's Contact No">
                    <span class="text-xs text-gray-500 mt-1">Put N/A if not applicable</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Save Button --}}
    <div class="flex justify-end">
        <button class="flux-btn flux-btn-primary mt-4" wire:click="updateAll">Save All</button>
    </div>
</div>