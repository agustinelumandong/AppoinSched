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

        // Temporary Address
        'temporary_address_type',
        'temporary_address_line_1',
        'temporary_address_line_2',
        'temporary_region',
        'temporary_province',
        'temporary_city',
        'temporary_barangay',
        'temporary_street',
        'temporary_zip_code',
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
}
