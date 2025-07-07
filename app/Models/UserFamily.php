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
        'spouse_last_name',
        'spouse_first_name',
        'spouse_middle_name',
        'spouse_suffix',
        'spouse_birthdate',
        'spouse_nationality',
        'spouse_religion',
        'spouse_contact_no',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class, 'personal_information_id');
    }

    public function getFatherNameAttribute()
    {
        return $this->father_last_name . ' ' . $this->father_first_name . ' ' . $this->father_middle_name . ' ' . $this->father_suffix;
    }

    public function getMotherNameAttribute()
    {
        return $this->mother_last_name . ' ' . $this->mother_first_name . ' ' . $this->mother_middle_name . ' ' . $this->mother_suffix;
    }

    public function getSpouseNameAttribute()
    {
        return $this->spouse_last_name . ' ' . $this->spouse_first_name . ' ' . $this->spouse_middle_name . ' ' . $this->spouse_suffix;
    }
}
