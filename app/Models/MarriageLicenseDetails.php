<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarriageLicenseDetails extends Model
{
    protected $fillable = [
        'document_request_detail_id',
        'marriage_date',
        'marriage_time',
        'marriage_place',
        'marriage_venue',
        'marriage_venue_address',
        'groom_first_name',
        'groom_middle_name',
        'groom_last_name',
        'groom_suffix',
        'groom_age',
        'groom_date_of_birth',
        'groom_place_of_birth',
        'groom_sex',
        'groom_citizenship',
        'groom_residence',
        'groom_religion',
        'groom_civil_status',
        'groom_father_first_name',
        'groom_father_middle_name',
        'groom_father_last_name',
        'groom_father_suffix',
        'groom_father_citizenship',
        'groom_father_residence',
        'groom_mother_first_name',
        'groom_mother_middle_name',
        'groom_mother_last_name',
        'groom_mother_citizenship',
        'groom_mother_residence',
        'bride_first_name',
        'bride_middle_name',
        'bride_last_name',
        'bride_suffix',
        'bride_age',
        'bride_date_of_birth',
        'bride_place_of_birth',
        'bride_sex',
        'bride_citizenship',
        'bride_residence',
        'bride_religion',
        'bride_civil_status',
        'bride_father_first_name',
        'bride_father_middle_name',
        'bride_father_last_name',
        'bride_father_suffix',
        'bride_father_citizenship',
        'bride_father_residence',
        'bride_mother_first_name',
        'bride_mother_middle_name',
        'bride_mother_last_name',
        'bride_mother_citizenship',
        'bride_mother_residence',
        'establishment_name',
        'establishment_address',
        'establishment_purpose',
        'consent_person',
        'consent_relationship',
        'consent_citizenship',
        'consent_residence',
        'license_number',
        'license_issued_date',
        'license_issued_place',
        'witness1_name',
        'witness1_address',
        'witness2_name',
        'witness2_address',
        'officiant_name',
        'officiant_title',
        'officiant_license_no',
        'officiant_address',
    ];

    protected $casts = [
        'marriage_date' => 'date',
        'marriage_time' => 'datetime:H:i',
        'groom_age' => 'integer',
        'groom_date_of_birth' => 'date',
        'bride_age' => 'integer',
        'bride_date_of_birth' => 'date',
        'license_issued_date' => 'date',
    ];

    public function documentRequestDetail(): BelongsTo
    {
        return $this->belongsTo(DocumentRequestDetails::class, 'document_request_detail_id');
    }

    /**
     * Get the full name of the groom
     */
    public function getGroomFullNameAttribute(): string
    {
        return trim("{$this->groom_first_name} {$this->groom_middle_name} {$this->groom_last_name}");
    }

    /**
     * Get the full name of the bride
     */
    public function getBrideFullNameAttribute(): string
    {
        return trim("{$this->bride_first_name} {$this->bride_middle_name} {$this->bride_last_name}");
    }

    /**
     * Get the full name of the groom's father
     */
    public function getGroomFatherFullNameAttribute(): string
    {
        return trim("{$this->groom_father_first_name} {$this->groom_father_middle_name} {$this->groom_father_last_name}");
    }

    /**
     * Get the full name of the groom's mother
     */
    public function getGroomMotherFullNameAttribute(): string
    {
        return trim("{$this->groom_mother_first_name} {$this->groom_mother_middle_name} {$this->groom_mother_last_name}");
    }

    /**
     * Get the full name of the bride's father
     */
    public function getBrideFatherFullNameAttribute(): string
    {
        return trim("{$this->bride_father_first_name} {$this->bride_father_middle_name} {$this->bride_father_last_name}");
    }

    /**
     * Get the full name of the bride's mother
     */
    public function getBrideMotherFullNameAttribute(): string
    {
        return trim("{$this->bride_mother_first_name} {$this->bride_mother_middle_name} {$this->bride_mother_last_name}");
    }

    /**
     * Get the full name of the consent person
     */
    public function getConsentPersonFullNameAttribute(): string
    {
        return trim($this->consent_person ?? '');
    }

    /**
     * Get the full name of the officiant
     */
    public function getOfficiantFullNameAttribute(): string
    {
        return trim($this->officiant_name ?? '');
    }

    /**
     * Get formatted marriage date and time
     */
    public function getFormattedMarriageDateTimeAttribute(): string
    {
        if (!$this->marriage_date) {
            return 'N/A';
        }

        $date = $this->marriage_date->format('F j, Y');
        $time = $this->marriage_time ? $this->marriage_time->format('g:i A') : '';

        return $time ? "{$date} at {$time}" : $date;
    }

    /**
     * Check if this is a minor marriage (requires special permit)
     */
    public function isMinorMarriage(): bool
    {
        return $this->groom_age < 18 || $this->bride_age < 18;
    }

    /**
     * Check if both parties are of legal age
     */
    public function isLegalAgeMarriage(): bool
    {
        return $this->groom_age >= 18 && $this->bride_age >= 18;
    }

    /**
     * Get the age difference between groom and bride
     */
    public function getAgeDifferenceAttribute(): int
    {
        if (!$this->groom_age || !$this->bride_age) {
            return 0;
        }

        return abs($this->groom_age - $this->bride_age);
    }
}
