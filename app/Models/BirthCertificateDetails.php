<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthCertificateDetails extends Model
{
    protected $fillable = [
        'document_request_detail_id',
        'hospital_name',
        'hospital_address',
        'attendant_name',
        'attendant_qualification',
        'attendant_license_no',
        'attendant_address',
        'birth_weight',
        'birth_length',
        'birth_order',
        'total_children',
        'birth_place_type',
        'birth_notes',
    ];

    protected $casts = [
        'birth_weight' => 'decimal:2',
        'birth_length' => 'decimal:2',
        'birth_order' => 'integer',
        'total_children' => 'integer',
    ];

    public function documentRequestDetail(): BelongsTo
    {
        return $this->belongsTo(DocumentRequestDetails::class, 'document_request_detail_id');
    }

    /**
     * Get the full name of the attendant
     */
    public function getAttendantFullNameAttribute(): string
    {
        return trim($this->attendant_name ?? '');
    }

    /**
     * Get formatted birth weight
     */
    public function getFormattedBirthWeightAttribute(): string
    {
        return $this->birth_weight ? $this->birth_weight . ' kg' : 'N/A';
    }

    /**
     * Get formatted birth length
     */
    public function getFormattedBirthLengthAttribute(): string
    {
        return $this->birth_length ? $this->birth_length . ' cm' : 'N/A';
    }

    /**
     * Check if birth occurred in a hospital
     */
    public function isHospitalBirth(): bool
    {
        return $this->birth_place_type === 'Hospital';
    }

    /**
     * Check if birth occurred at home
     */
    public function isHomeBirth(): bool
    {
        return $this->birth_place_type === 'Home';
    }
}
