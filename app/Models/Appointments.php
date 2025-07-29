<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointments extends Model
{
    /**
     * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointments>
     */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'office_id',
        'booking_date',
        'booking_time',
        'notes',
        'purpose',
        'reference_number',
        'status',
    ];

    /** @var array<int,string> */
    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function office()
    {
        return $this->belongsTo(Offices::class, 'office_id');
    }

    public function appointmentDetails()
    {
        return $this->hasOne(AppointmentDetails::class, 'appointment_id');
    }

    public function details()
    {
        return $this->hasOne(AppointmentDetails::class, 'appointment_id');
    }

}
