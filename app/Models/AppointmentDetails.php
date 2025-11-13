<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentDetails extends Model
{
    protected $fillable = [
        'appointment_id',
        'request_for',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'metadata', // JSON field for certificates metadata
        'purpose',
        'notes',

    ];

    protected $casts = [
        'metadata' => 'array', // Cast metadata to array for easier access
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointments::class, 'appointment_id');
    }

    // Helper method to get certificate requests
    public function getCertificateRequests(): array
    {
        return $this->metadata['certificate_requests'] ?? [];
    }

    // Helper method to get document reference numbers
    public function getDocumentReferenceNumbers(): array
    {
        return $this->metadata['reference_numbers'] ?? [];
    }

    /**
     * Get the full name of the person for this appointment
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            strtolower($this->middle_name ?? '') !== 'n/a' ? $this->middle_name : null,
            $this->last_name,
            strtolower($this->suffix ?? '') !== 'n/a' ? $this->suffix : null,
        ], function ($part) {
            return !empty($part) && strtolower(trim($part)) !== 'n/a';
        });
        return trim(implode(' ', $parts));
    }

    /**
     * Get the complete address for this appointment
     */
    public function getCompleteAddressAttribute(): string
    {
        $addressParts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip_code,
        ]);
        return implode(', ', $addressParts);
    }
}