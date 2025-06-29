<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRequestDetails extends Model
{
    protected $fillable = [
        // Basic request information
        'document_request_id',
        'request_for',

        // Personal information
        'last_name',
        'first_name',
        'middle_name',
        'suffix',
        'email',
        'contact_no',
        'sex_at_birth',
        'date_of_birth',
        'place_of_birth',
        'civil_status',
        'religion',
        'nationality',

        // Address information
        'address_type',
        'address_line_1',
        'address_line_2',
        'region',
        'province',
        'city',
        'barangay',
        'street',
        'zip_code',

        // Father's information
        'father_last_name',
        'father_first_name',
        'father_middle_name',
        'father_suffix',
        'father_birthdate',
        'father_nationality',
        'father_religion',
        'father_contact_no',

        // Mother's information
        'mother_last_name',
        'mother_first_name',
        'mother_middle_name',
        'mother_suffix',
        'mother_birthdate',
        'mother_nationality',
        'mother_religion',
        'mother_contact_no',

        // Spouse's information
        'spouse_last_name',
        'spouse_first_name',
        'spouse_middle_name',
        'spouse_suffix',
        'spouse_birthdate',
        'spouse_nationality',
        'spouse_religion',
        'spouse_contact_no',

        // Death Certificate specific fields
        'deceased_last_name',
        'deceased_first_name',
        'deceased_middle_name',
        'death_date',
        'death_time',
        'death_place',
        'relationship_to_deceased',

        // Contact person information
        'contact_first_name',
        'contact_last_name',
        'contact_middle_name',
        'contact_email',
        'contact_phone',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'father_birthdate' => 'date',
        'mother_birthdate' => 'date',
        'spouse_birthdate' => 'date',
        'death_date' => 'date',
        'death_time' => 'time',
    ];

    public function documentRequest(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    /**
     * Get the full name of the person for this document request
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);

        return implode(' ', $parts);
    }

    /**
     * Get the complete address for this document request
     */
    public function getCompleteAddressAttribute(): string
    {
        $addressParts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->street,
            $this->barangay,
            $this->city,
            $this->province,
            $this->region,
            $this->zip_code,
        ]);

        return implode(', ', $addressParts);
    }

    /**
     * Get father's full name
     */
    public function getFatherFullNameAttribute(): string
    {
        return trim("{$this->father_first_name} {$this->father_middle_name} {$this->father_last_name}");
    }

    /**
     * Get mother's full name
     */
    public function getMotherFullNameAttribute(): string
    {
        return trim("{$this->mother_first_name} {$this->mother_middle_name} {$this->mother_last_name}");
    }

    /**
     * Get spouse's full name
     */
    public function getSpouseFullNameAttribute(): string
    {
        return trim("{$this->spouse_first_name} {$this->spouse_middle_name} {$this->spouse_last_name}");
    }

    /**
     * Get the full name of the deceased person (for death certificates)
     */
    public function getDeceasedFullNameAttribute(): string
    {
        return trim("{$this->deceased_first_name} {$this->deceased_middle_name} {$this->deceased_last_name}");
    }

    /**
     * Get contact information for this document request
     */
    public function getContactInfo(): array
    {
        return [
            'name' => trim("{$this->contact_first_name} {$this->contact_middle_name} {$this->contact_last_name}"),
            'email' => $this->contact_email,
            'phone' => $this->contact_phone,
            'is_third_party' => $this->request_for === 'someone_else',
            'requestor' => $this->documentRequest?->user?->name ?? 'N/A',
        ];
    }

    /**
     * Check if this is a third-party request
     */
    public function isThirdPartyRequest(): bool
    {
        return $this->request_for === 'someone_else';
    }

    /**
     * Get the full contact name
     */
    public function getContactFullNameAttribute(): string
    {
        return trim("{$this->contact_first_name} {$this->contact_middle_name} {$this->contact_last_name}");
    }
}
