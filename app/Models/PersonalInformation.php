<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalInformation extends Model
{
    protected $table = 'personal_information';

    protected $fillable = [
        'user_id',
        'suffix',
        'date_of_birth',
        'place_of_birth',
        'sex_at_birth',
        'civil_status',
        'religion',
        'nationality',
        'contact_no',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userAddresses()
    {
        return $this->hasMany(UserAddresses::class, 'personal_information_id');
    }

    public function userFamilies()
    {
        return $this->hasMany(UserFamily::class, 'personal_information_id');
    }

 
}
