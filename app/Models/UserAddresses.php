<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class, 'personal_information_id');
    }

    public function getCompleteAddressAttribute()
    {
        return $this->address_line_1 . ' ' . $this->address_line_2 . ' ' . $this->region . ' ' . $this->province . ' ' . $this->city . ' ' . $this->barangay . ' ' . $this->zip_code;
    }

    /**
     * Check if this address is complete (has all required fields)
     */
    public function isComplete(): bool
    {
        return !empty($this->address_line_1) &&
            !empty($this->region) &&
            !empty($this->province) &&
            !empty($this->city) &&
            !empty($this->barangay) &&
            !empty($this->zip_code);
    }

    /**
     * Scope to get permanent addresses
     */
    public function scopePermanent($query)
    {
        return $query->where('address_type', 'Permanent');
    }

    /**
     * Scope to get temporary addresses
     */
    public function scopeTemporary($query)
    {
        return $query->where('address_type', 'Temporary');
    }
}
