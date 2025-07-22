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
        'government_id_type',
        'government_id_image_path',

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

        // Marriage License Modular Fields
        // Groom Personal Info
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
        // Groom Father
        'groom_father_first_name',
        'groom_father_middle_name',
        'groom_father_last_name',
        'groom_father_suffix',
        'groom_father_citizenship',
        'groom_father_residence',
        // Groom Mother
        'groom_mother_first_name',
        'groom_mother_middle_name',
        'groom_mother_last_name',
        'groom_mother_citizenship',
        'groom_mother_residence',
        // Bride Personal Info
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
        // Bride Father
        'bride_father_first_name',
        'bride_father_middle_name',
        'bride_father_last_name',
        'bride_father_suffix',
        'bride_father_citizenship',
        'bride_father_residence',
        // Bride Mother
        'bride_mother_first_name',
        'bride_mother_middle_name',
        'bride_mother_last_name',
        'bride_mother_citizenship',
        'bride_mother_residence',
        // Consent Section
        'consent_person',
        'consent_relationship',
        'consent_citizenship',
        'consent_residence',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'father_birthdate' => 'date',
        'mother_birthdate' => 'date', 
        'death_date' => 'date', // For death certificates
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
