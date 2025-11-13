<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFamily extends Model
{
    protected $table = 'user_families';

    protected $fillable = [
        'user_id',
        'personal_information_id',
        'father_last_name',
        'father_first_name',
        'father_middle_name',
        'father_suffix',
        'father_birthdate',
        'father_nationality',
        'father_religion',
        'father_contact_no',
        'mother_last_name',
        'mother_first_name',
        'mother_middle_name',
        'mother_suffix',
        'mother_birthdate',
        'mother_nationality',
        'mother_religion',
        'mother_contact_no',
    ];

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class, 'personal_information_id');
    }

    public function getFatherNameAttribute()
    {
        $parts = array_filter([
            $this->father_last_name,
            $this->father_first_name,
            strtolower($this->father_middle_name ?? '') !== 'n/a' ? $this->father_middle_name : null,
            strtolower($this->father_suffix ?? '') !== 'n/a' ? $this->father_suffix : null,
        ], function ($part) {
            return !empty($part) && strtolower(trim($part)) !== 'n/a';
        });

        return trim(implode(' ', $parts));
    }

    public function getMotherNameAttribute()
    {
        $parts = array_filter([
            $this->mother_last_name,
            $this->mother_first_name,
            strtolower($this->mother_middle_name ?? '') !== 'n/a' ? $this->mother_middle_name : null,
            strtolower($this->mother_suffix ?? '') !== 'n/a' ? $this->mother_suffix : null,
        ], function ($part) {
            return !empty($part) && strtolower(trim($part)) !== 'n/a';
        });

        return trim(implode(' ', $parts));
    }


}
