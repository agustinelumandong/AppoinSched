<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\PersonalInformation;
use App\Models\UserAddresses;
use App\Models\UserFamily;
use Illuminate\Support\Facades\DB;

class UserProfileService
{
    public function __construct(
        private AddressService $addressService
    ) {}

    /**
     * Get complete user profile with all related data
     */
    public function getCompleteProfile(User $user): array
    {
        $user->load([
            'personalInformation',
            'userAddresses',
            'userFamilies'
        ]);

        return [
            'user' => $user,
            'personal_information' => $user->personalInformation,
            'addresses' => $user->userAddresses,
            'families' => $user->userFamilies,
            'completion_status' => $this->getProfileCompletionStatus($user),
        ];
    }

    /**
     * Update user profile across multiple tables
     */
    public function updateProfile(User $user, array $profileData): User
    {
        return DB::transaction(function () use ($user, $profileData) {
            // Update basic user information
            if (isset($profileData['user'])) {
                $user->update($profileData['user']);
            }

            // Update personal information
            if (isset($profileData['personal_information'])) {
                $this->updatePersonalInformation($user, $profileData['personal_information']);
            }

            // Update addresses
            if (isset($profileData['addresses'])) {
                $this->updateAddresses($user, $profileData['addresses']);
            }

            // Update family information
            if (isset($profileData['families'])) {
                $this->updateFamilyInformation($user, $profileData['families']);
            }

            return $user->fresh();
        });
    }

    /**
     * Validate profile completion
     */
    public function validateProfileCompletion(User $user): array
    {
        $completion = [
            'basic_profile' => $user->hasCompleteBasicProfile(),
            'personal_info' => $user->hasCompletePersonalInfo(),
            'address' => $user->hasCompleteAddress(),
            'family_info' => $user->hasCompleteFamilyInfo(),
        ];

        $completion['overall'] = $user->hasCompleteProfile();
        $completion['percentage'] = $user->getProfileCompletionPercentage();

        return $completion;
    }

    /**
     * Get profile completion status with details
     */
    public function getProfileCompletionStatus(User $user): array
    {
        return [
            'basic_profile' => [
                'completed' => $user->hasCompleteBasicProfile(),
                'percentage' => 25,
                'fields' => ['first_name', 'last_name', 'email', 'contact_no']
            ],
            'personal_info' => [
                'completed' => $user->hasCompletePersonalInfo(),
                'percentage' => 25,
                'fields' => ['sex_at_birth', 'date_of_birth', 'place_of_birth', 'civil_status', 'nationality']
            ],
            'address' => [
                'completed' => $user->hasCompleteAddress(),
                'percentage' => 25,
                'fields' => ['address_line_1', 'region', 'province', 'city', 'barangay', 'street', 'zip_code']
            ],
            'family_info' => [
                'completed' => $user->hasCompleteFamilyInfo(),
                'percentage' => 25,
                'fields' => ['father_info_or_mother_info']
            ],
            'overall_percentage' => $user->getProfileCompletionPercentage()
        ];
    }

    /**
     * Update personal information
     */
    private function updatePersonalInformation(User $user, array $personalData): void
    {
        if ($user->personalInformation) {
            $user->personalInformation->update($personalData);
        } else {
            $personalData['user_id'] = $user->id;
            PersonalInformation::create($personalData);
        }
    }

    /**
     * Update addresses
     */
    private function updateAddresses(User $user, array $addressesData): void
    {
        if (!$user->personalInformation) {
            return;
        }

        foreach ($addressesData as $addressData) {
            if (isset($addressData['id'])) {
                // Update existing address
                $this->addressService->createOrUpdateAddress(
                    $user->personalInformation->id,
                    $addressData,
                    $addressData['id']
                );
            } else {
                // Create new address
                $this->addressService->createOrUpdateAddress(
                    $user->personalInformation->id,
                    $addressData
                );
            }
        }
    }

    /**
     * Update family information
     */
    private function updateFamilyInformation(User $user, array $familyData): void
    {
        if (!$user->personalInformation) {
            return;
        }

        if ($user->userFamilies->isNotEmpty()) {
            $family = $user->userFamilies->first();
            $family->update($familyData);
        } else {
            $familyData['user_id'] = $user->id;
            $familyData['personal_information_id'] = $user->personalInformation->id;
            UserFamily::create($familyData);
        }
    }

    /**
     * Get user data for document request forms
     */
    public function getDocumentRequestFormData(User $user): array
    {
        return $user->getDocumentRequestFormData();
    }

    /**
     * Get user data for appointment forms
     */
    public function getAppointmentFormData(User $user): array
    {
        return $user->getAppointmentFormData();
    }

    /**
     * Check if user can make document requests
     */
    public function canMakeDocumentRequests(User $user): bool
    {
        return $user->canMakeDocumentRequests();
    }

    /**
     * Get user's formatted address
     */
    public function getFormattedAddress(User $user): string
    {
        return $user->getFormattedAddress();
    }

    /**
     * Get user's profile completion details
     */
    public function getProfileCompletionDetails(User $user): array
    {
        return $user->getProfileCompletionDetails();
    }
}
