<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeathCertificateDetails extends Model
{
    protected $fillable = [
        'document_request_detail_id',
        'deceased_last_name',
        'deceased_first_name',
        'deceased_middle_name',
        'deceased_sex',
        'deceased_religion',
        'deceased_age',
        'deceased_place_of_birth',
        'deceased_date_of_birth',
        'deceased_civil_status',
        'deceased_residence',
        'deceased_occupation',
        'death_date',
        'death_time',
        'death_place',
        'cause_of_death',
        'death_certificate_no',
        'relationship_to_deceased',
        'deceased_father_last_name',
        'deceased_father_first_name',
        'deceased_father_middle_name',
        'deceased_mother_last_name',
        'deceased_mother_first_name',
        'deceased_mother_middle_name',
        'burial_cemetery_name',
        'burial_cemetery_address',
        'burial_date',
        'burial_time',
        'informant_name',
        'informant_address',
        'informant_relationship',
        'informant_contact_no',
        'informant_id_type',
        'informant_id_number',
        'attending_physician',
        'physician_license_no',
        'physician_address',
        'certification_date',
    ];

    protected $casts = [
        'deceased_age' => 'integer',
        'deceased_date_of_birth' => 'date',
        'death_date' => 'date',
        'death_time' => 'datetime:H:i',
        'burial_date' => 'date',
        'burial_time' => 'datetime:H:i',
        'certification_date' => 'date',
    ];

    public function documentRequestDetail(): BelongsTo
    {
        return $this->belongsTo(DocumentRequestDetails::class, 'document_request_detail_id');
    }

    /**
     * Get the full name of the deceased person
     */
    public function getDeceasedFullNameAttribute(): string
    {
        return trim("{$this->deceased_first_name} {$this->deceased_middle_name} {$this->deceased_last_name}");
    }

    /**
     * Get the full name of the deceased's father
     */
    public function getDeceasedFatherFullNameAttribute(): string
    {
        return trim("{$this->deceased_father_first_name} {$this->deceased_father_middle_name} {$this->deceased_father_last_name}");
    }

    /**
     * Get the full name of the deceased's mother
     */
    public function getDeceasedMotherFullNameAttribute(): string
    {
        return trim("{$this->deceased_mother_first_name} {$this->deceased_mother_middle_name} {$this->deceased_mother_last_name}");
    }

    /**
     * Get the full name of the informant
     */
    public function getInformantFullNameAttribute(): string
    {
        return trim($this->informant_name ?? '');
    }

    /**
     * Get formatted death date and time
     */
    public function getFormattedDeathDateTimeAttribute(): string
    {
        if (!$this->death_date) {
            return 'N/A';
        }

        $date = $this->death_date->format('F j, Y');
        $time = $this->death_time ? $this->death_time->format('g:i A') : '';

        return $time ? "{$date} at {$time}" : $date;
    }

    /**
     * Get formatted burial date and time
     */
    public function getFormattedBurialDateTimeAttribute(): string
    {
        if (!$this->burial_date) {
            return 'N/A';
        }

        $date = $this->burial_date->format('F j, Y');
        $time = $this->burial_time ? $this->burial_time->format('g:i A') : '';

        return $time ? "{$date} at {$time}" : $date;
    }

    /**
     * Check if the deceased was married at time of death
     */
    public function wasMarried(): bool
    {
        return $this->deceased_civil_status === 'Married';
    }

    /**
     * Check if the deceased was single at time of death
     */
    public function wasSingle(): bool
    {
        return $this->deceased_civil_status === 'Single';
    }
}
