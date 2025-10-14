<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class UserAddresses extends Model
{
    protected $table = 'user_addresses';

    protected $fillable = [
        'personal_information_id',
        'address_type',
        'address_line_1',
        'address_line_2',
        'region',
        'province',
        'city',
        'barangay',
        'street',
        'zip_code',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function personalInformation(): BelongsTo
    {
        return $this->belongsTo(PersonalInformation::class, 'personal_information_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope to get permanent addresses
     */
    public function scopePermanent(Builder $query): Builder
    {
        return $query->where('address_type', 'Permanent');
    }

    /**
     * Scope to get temporary addresses
     */
    public function scopeTemporary(Builder $query): Builder
    {
        return $query->where('address_type', 'Temporary');
    }

    /**
     * Scope to get primary addresses
     */
    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }

    /**
     * Get the complete formatted address
     */
    public function getCompleteAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->street,
            $this->barangay,
            $this->city,
            $this->province,
            $this->region,
            $this->zip_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get a short address (city, province only)
     */
    public function getShortAddressAttribute(): string
    {
        $parts = array_filter([
            $this->city,
            $this->province,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Check if this is a permanent address
     */
    public function isPermanent(): bool
    {
        return $this->address_type === 'Permanent';
    }

    /**
     * Check if this is a temporary address
     */
    public function isTemporary(): bool
    {
        return $this->address_type === 'Temporary';
    }

    /**
     * Check if this is the primary address
     */
    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    /**
     * Set this address as primary and unset others
     */
    public function setAsPrimary(): void
    {
        // Unset other primary addresses for this personal information
        static::where('personal_information_id', $this->personal_information_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        // Set this as primary
        $this->update(['is_primary' => true]);
    }
}
