<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Collection;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'login_code',
        'login_code_expires_at',
        'password_set_at',
        'last_viewed_users_at',
        'email_otp',
        'email_otp_expires_at',
        'notification_settings',
        'email_verified_at',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_otp_expires_at' => 'datetime',
            'login_code_expires_at' => 'datetime',
            'password_set_at' => 'datetime',
            'last_viewed_users_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_settings' => 'array',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->first_name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1)->upper())
            ->implode('');
    }

    /**
     * Get the user's full name
     */
    public function getNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            strtolower($this->middle_name ?? '') !== 'n/a' ? $this->middle_name : null,
            $this->last_name,
        ], function ($part) {
            return !empty($part) && strtolower(trim($part)) !== 'n/a';
        });

        return trim(implode(' ', $parts));
    }

    public function personalInformation()
    {
        return $this->hasOne(PersonalInformation::class, 'user_id');
    }

    public function userAddresses()
    {
        return $this->hasManyThrough(
            UserAddresses::class,
            PersonalInformation::class,
            'user_id',
            'personal_information_id',
            'id',
            'id'
        );
    }

    public function userFamilies()
    {
        return $this->hasManyThrough(
            UserFamily::class,
            PersonalInformation::class,
            'user_id',
            'personal_information_id',
            'id',
            'id'
        );
    }

    /**
     * Check if user has completed their basic profile information
     */
    public function hasCompleteBasicProfile(): bool
    {
        return !empty($this->first_name) &&
            !empty($this->last_name) &&
            !empty($this->email) &&
            !empty($this->personalInformation?->contact_no);
    }

    /**
     * Check if user has completed their personal information
     */
    public function hasCompletePersonalInfo(): bool
    {
        if (!$this->personalInformation) {
            return false;
        }

        return !empty($this->personalInformation->sex_at_birth) &&
            !empty($this->personalInformation->date_of_birth) &&
            !empty($this->personalInformation->place_of_birth) &&
            !empty($this->personalInformation->civil_status) &&
            !empty($this->personalInformation->nationality) &&
            !empty($this->personalInformation->government_id_type) &&
            !empty($this->personalInformation->government_id_image_path);
    }

    /**
     * Check if user has completed their address information
     * Only checks permanent address as it's required
     */
    public function hasCompleteAddress(): bool
    {
        $permanentAddress = $this->userAddresses->where('address_type', 'Permanent')->first();
        if (!$permanentAddress) {
            return false;
        }

        return !empty($permanentAddress->address_line_1) &&
            !empty($permanentAddress->region) &&
            !empty($permanentAddress->province) &&
            !empty($permanentAddress->city) &&
            !empty($permanentAddress->barangay) &&
            !empty($permanentAddress->zip_code);
    }

    /**
     * Check if user has completed their family information
     * At minimum, requires father's or mother's first and last name (not "N/A")
     */
    public function hasCompleteFamilyInfo(): bool
    {
        $family = $this->userFamilies->first();
        if (!$family) {
            return false;
        }

        // Check if father info is provided (first and last name exist and are not "N/A")
        $hasFatherInfo = !empty($family->father_first_name) &&
                        !empty($family->father_last_name) &&
                        strtoupper(trim($family->father_first_name)) !== 'N/A' &&
                        strtoupper(trim($family->father_last_name)) !== 'N/A';

        // Check if mother info is provided (first and last name exist and are not "N/A")
        $hasMotherInfo = !empty($family->mother_first_name) &&
                        !empty($family->mother_last_name) &&
                        strtoupper(trim($family->mother_first_name)) !== 'N/A' &&
                        strtoupper(trim($family->mother_last_name)) !== 'N/A';

        // At minimum, require father's or mother's information (with valid names, not just "N/A")
        return $hasFatherInfo || $hasMotherInfo;
    }

    /**
     * Check if user has completed their full profile
     */
    public function hasCompleteProfile(): bool
    {
        return $this->hasCompleteBasicProfile() &&
            $this->hasCompletePersonalInfo() &&
            $this->hasCompleteAddress() &&
            $this->hasCompleteFamilyInfo();
    }

    /**
     * Get profile completion percentage
     */
    public function getProfileCompletionPercentage(): int
    {
        $totalFields = 4; // basic, personal, address, family
        $completedFields = 0;

        if ($this->hasCompleteBasicProfile())
            $completedFields++;
        if ($this->hasCompletePersonalInfo())
            $completedFields++;
        if ($this->hasCompleteAddress())
            $completedFields++;
        if ($this->hasCompleteFamilyInfo())
            $completedFields++;

        return (int) round(($completedFields / $totalFields) * 100);
    }


    public function getDocumentRequestFormData(): array
    {
        $personalInfo = $this->personalInformation;
        $permanentAddress = $this->userAddresses->where('address_type', 'Permanent')->first();
        $temporaryAddress = $this->userAddresses->where('address_type', 'Temporary')->first();
        $userFamily = $this->userFamilies->first();

        return [
            // Basic info
            'last_name' => $this->last_name ?? '',
            'first_name' => $this->first_name ?? '',
            'middle_name' => $this->middle_name ?? '',
            'email' => $this->email ?? '',

            // Personal Information
            'suffix' => $personalInfo?->suffix ?? 'N/A',
            'phone' => $personalInfo?->contact_no ?? '',
            'sex_at_birth' => $personalInfo?->sex_at_birth ?? '',
            'date_of_birth' => $personalInfo?->date_of_birth instanceof \Carbon\Carbon ? $personalInfo->date_of_birth->format('Y-m-d') : ($personalInfo?->date_of_birth ?? ''),
            'place_of_birth' => $personalInfo?->place_of_birth ?? '',
            'civil_status' => $personalInfo?->civil_status ?? 'Single',
            'religion' => $personalInfo?->religion ?? '',
            'nationality' => $personalInfo?->nationality ?? 'Filipino',
            'government_id_type' => $personalInfo?->government_id_type ?? '',
            'government_id_image_path' => $personalInfo?->government_id_image_path ?? '',

            // Permanent Address Information
            'address_type' => $permanentAddress?->address_type ?? 'Permanent',
            'address_line_1' => $permanentAddress?->address_line_1 ?? '',
            'address_line_2' => $permanentAddress?->address_line_2 ?? '',
            'region' => $permanentAddress?->region ?? '',
            'province' => $permanentAddress?->province ?? '',
            'city' => $permanentAddress?->city ?? '',
            'barangay' => $permanentAddress?->barangay ?? '',
            'street' => $permanentAddress?->street ?? '',
            'zip_code' => $permanentAddress?->zip_code ?? '',

            // Temporary Address Information (if exists)
            'temporary_address_type' => $temporaryAddress?->address_type ?? 'Temporary',
            'temporary_address_line_1' => $temporaryAddress?->address_line_1 ?? '',
            'temporary_address_line_2' => $temporaryAddress?->address_line_2 ?? '',
            'temporary_region' => $temporaryAddress?->region ?? '',
            'temporary_province' => $temporaryAddress?->province ?? '',
            'temporary_city' => $temporaryAddress?->city ?? '',
            'temporary_barangay' => $temporaryAddress?->barangay ?? '',
            'temporary_street' => $temporaryAddress?->street ?? '',
            'temporary_zip_code' => $temporaryAddress?->zip_code ?? '',

            // Family Information
            'father_last_name' => $userFamily?->father_last_name ?? '',
            'father_first_name' => $userFamily?->father_first_name ?? '',
            'father_middle_name' => $userFamily?->father_middle_name ?? '',
            'father_suffix' => $userFamily?->father_suffix ?? 'N/A',
            'father_birthdate' => $userFamily?->father_birthdate instanceof \Carbon\Carbon ? $userFamily->father_birthdate->format('Y-m-d') : ($userFamily?->father_birthdate ?? ''),
            'father_nationality' => $userFamily?->father_nationality ?? '',
            'father_religion' => $userFamily?->father_religion ?? '',
            'father_contact_no' => $userFamily?->father_contact_no ?? '',

            'mother_last_name' => $userFamily?->mother_last_name ?? '',
            'mother_first_name' => $userFamily?->mother_first_name ?? '',
            'mother_middle_name' => $userFamily?->mother_middle_name ?? '',
            'mother_suffix' => $userFamily?->mother_suffix ?? 'N/A',
            'mother_birthdate' => $userFamily?->mother_birthdate instanceof \Carbon\Carbon ? $userFamily->mother_birthdate->format('Y-m-d') : ($userFamily?->mother_birthdate ?? ''),
            'mother_nationality' => $userFamily?->mother_nationality ?? '',
            'mother_religion' => $userFamily?->mother_religion ?? '',
            'mother_contact_no' => $userFamily?->mother_contact_no ?? '',
        ];
    }

    /**
     * Get user data for appointment form (basic info only)
     */
    public function getAppointmentFormData(): array
    {
        $personalInfo = $this->personalInformation;
        $userAddress = $this->userAddresses->where('address_type', 'Permanent')->first();

        return [
            // Basic info
            'first_name' => $this->first_name ?? '',
            'last_name' => $this->last_name ?? '',
            'middle_name' => $this->middle_name ?? '',
            'email' => $this->email ?? '',
            'phone' => $personalInfo?->contact_no ?? '',

            // Address Information (basic)
            'address' => $userAddress?->address_line_1 ?? '',
            'region' => $userAddress?->region ?? '',
            'province' => $userAddress?->province ?? '',
            'city' => $userAddress?->city ?? '',
            'barangay' => $userAddress?->barangay ?? '',
            'street' => $userAddress?->street ?? '',
            'zip_code' => $userAddress?->zip_code ?? '',
        ];
    }

    /**
     * Get all document requests made by this user
     */
    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    /**
     * Get all document requests where this user is the assigned staff
     */
    public function assignedDocumentRequests()
    {
        return $this->hasMany(DocumentRequest::class, 'staff_id');
    }


    public function getOfficeIdForStaff(): ?int
    {
        // Check staff roles
        if ($this->hasRole('MCR-staff')) {
            return Offices::where('slug', 'municipal-civil-registrar')->value('id');
        }
        if ($this->hasRole('MTO-staff')) {
            return Offices::where('slug', 'municipal-treasurers-office')->value('id');
        }
        if ($this->hasRole('BPLS-staff')) {
            return Offices::where('slug', 'business-permits-and-licensing-section')->value('id');
        }

        // Check office-specific admin roles
        if ($this->hasRole('MCR-admin')) {
            return Offices::where('slug', 'municipal-civil-registrar')->value('id');
        }
        if ($this->hasRole('MTO-admin')) {
            return Offices::where('slug', 'municipal-treasurers-office')->value('id');
        }
        if ($this->hasRole('BPLS-admin')) {
            return Offices::where('slug', 'business-permits-and-licensing-section')->value('id');
        }

        return null;
    }

    /**
     * Check if user is assigned to a specific office (role-based)
     */
    public function isAssignedToOffice(int $officeId): bool
    {
        return $this->getOfficeIdForStaff() === $officeId;
    }

    /**
     * Get assigned office IDs for this staff (role-based)
     */
    public function getAssignedOfficeIds(): array
    {
        $officeId = $this->getOfficeIdForStaff();
        return $officeId ? [$officeId] : [];
    }

    /**
     * Get the office for this staff based on their role
     */
    public function getAssignedOffice()
    {
        $officeId = $this->getOfficeIdForStaff();
        return $officeId ? Offices::find($officeId) : null;
    }
    /**
     * Get assigned offices for this staff (role-based)
     */
    public function getAssignedOffices()
    {
        $office = $this->getAssignedOffice();
        return $office ? collect([$office]) : collect();
    }

    /**
     * Get all staff users assigned to a specific office based on roles
     *
     * @param int $officeId The ID of the office to find staff for
     * @return \Illuminate\Database\Eloquent\Collection Collection of User models
     */
    public static function getStaffsByOfficeId(int $officeId): Collection
    {
        return User::query()
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['MCR-staff', 'MTO-staff', 'BPLS-staff']);
            })
            ->where(function ($query) use ($officeId) {
                // Check if user's role-based office matches the requested office
                $query->whereHas('roles', function ($roleQuery) use ($officeId) {
                    // Match users whose role corresponds to this office
                    $roleQuery->where(function ($q) use ($officeId) {
                        if ($officeId === Offices::where('slug', 'municipal-civil-registrar')->value('id')) {
                            $q->where('name', 'MCR-staff');
                        } elseif ($officeId === Offices::where('slug', 'municipal-treasurers-office')->value('id')) {
                            $q->where('name', 'MTO-staff');
                        } elseif ($officeId === Offices::where('slug', 'business-permits-and-licensing-section')->value('id')) {
                            $q->where('name', 'BPLS-staff');
                        }
                    });
                });
            })
            ->get();
    }

    /**
     * Check if user can make document requests
     */
    public function canMakeDocumentRequests(): bool
    {
        // User must have complete profile to make document requests
        return $this->hasCompleteProfile();
    }

    /**
     * Get the user's most recent document request
     */
    public function latestDocumentRequest()
    {
        return $this->documentRequests()->latest()->first();
    }

    /**
     * Get pending document requests count
     */
    public function getPendingDocumentRequestsCount(): int
    {
        return $this->documentRequests()->where('status', 'pending')->count();
    }

    /**
     * Get approved document requests count
     */
    public function getApprovedDocumentRequestsCount(): int
    {
        return $this->documentRequests()->where('status', 'approved')->count();
    }

    /**
     * Check if user has any pending document requests
     */
    public function hasPendingDocumentRequests(): bool
    {
        return $this->getPendingDocumentRequestsCount() > 0;
    }

    /**
     * Populate form data for document request without modifying user profile
     *
     * @param array $overrides Additional data to override or add to the form
     * @return array
     */
    public function getDocumentRequestFormDataWithOverrides(array $overrides = []): array
    {
        $defaultData = $this->getDocumentRequestFormData();
        return array_merge($defaultData, $overrides);
    }

    /**
     * Get user's permanent address as a formatted string
     */
    public function getFormattedAddress(): string
    {
        $permanentAddress = $this->userAddresses->where('address_type', 'Permanent')->first();
        if (!$permanentAddress) {
            return 'No address on file';
        }

        $addressParts = array_filter([
            $permanentAddress->address_line_1,
            $permanentAddress->address_line_2,
            $permanentAddress->street,
            $permanentAddress->barangay,
            $permanentAddress->city,
            $permanentAddress->province,
            $permanentAddress->region,
            $permanentAddress->zip_code,
        ]);

        return implode(', ', $addressParts);
    }

    /**
     * Get user's permanent address record
     */
    public function getPermanentAddress(): ?UserAddresses
    {
        return $this->userAddresses->where('address_type', 'Permanent')->first();
    }

    /**
     * Get user's temporary address record
     */
    public function getTemporaryAddress(): ?UserAddresses
    {
        return $this->userAddresses->where('address_type', 'Temporary')->first();
    }

    /**
     * Get user's profile completion details
     */
    public function getProfileCompletionDetails(): array
    {
        return [
            'basic_profile' => [
                'completed' => $this->hasCompleteBasicProfile(),
                'percentage' => 25,
                'fields' => ['first_name', 'last_name', 'email', 'contact_no']
            ],
            'personal_info' => [
                'completed' => $this->hasCompletePersonalInfo(),
                'percentage' => 25,
                'fields' => ['sex_at_birth', 'date_of_birth', 'place_of_birth', 'civil_status', 'nationality']
            ],
            'address' => [
                'completed' => $this->hasCompleteAddress(),
                'percentage' => 25,
                'fields' => ['address_line_1', 'region', 'province', 'city', 'barangay', 'street', 'zip_code']
            ],
            'family_info' => [
                'completed' => $this->hasCompleteFamilyInfo(),
                'percentage' => 25,
                'fields' => ['father_info_or_mother_info']
            ],
            'overall_percentage' => $this->getProfileCompletionPercentage()
        ];
    }

    /**
     * Get default notification settings
     */
    public function getDefaultNotificationSettings(): array
    {
        return [
            'submitted' => true,
            'approved' => true,
            'rejected' => true,
            'payment_uploaded' => true,
            'payment_verified' => true,
            'appointment_scheduled' => true,
            'appointment_approved' => true,
            'appointment_cancelled' => true,
            'appointment_rescheduled' => true,
            'completed' => true,
            'appointment_reminder' => true,
        ];
    }

    /**
     * Get notification settings with defaults
     */
    public function getNotificationSettings(): array
    {
        return $this->notification_settings ?? $this->getDefaultNotificationSettings();
    }

    /**
     * Update notification settings
     */
    public function updateNotificationSettings(array $settings): void
    {
        $this->update(['notification_settings' => $settings]);
    }

    /**
     * Generate and store a login code for the user
     */
    public function generateLoginCode(): string
    {
        do {
            $code = Str::upper(Str::random(6));
        } while (self::where('login_code', $code)->where('login_code_expires_at', '>', now())->exists());

        $this->update([
            'login_code' => $code,
            'login_code_expires_at' => now()->addDays(7),
        ]);

        return $code;
    }

    /**
     * Check if user has set a password
     */
    public function hasPasswordSet(): bool
    {
        return !empty($this->password);
    }

    /**
     * Validate if the provided login code is valid
     */
    public function isLoginCodeValid(string $code): bool
    {
        if (empty($this->login_code) || empty($this->login_code_expires_at)) {
            return false;
        }

        if (now()->isAfter($this->login_code_expires_at)) {
            return false;
        }

        return strtoupper(trim($code)) === strtoupper(trim($this->login_code));
    }

    /**
     * Clear the login code after password setup
     */
    public function clearLoginCode(): void
    {
        $this->update([
            'login_code' => null,
            'login_code_expires_at' => null,
        ]);
    }

    protected static function boot()
    {
        parent::boot();
        static::created(function ($user) {

            // Set default notification settings
            $user->updateNotificationSettings($user->getDefaultNotificationSettings());
        });
    }
}
