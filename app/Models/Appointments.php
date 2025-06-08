<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointments extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'staff_id',
        'office_id',
        'service_id',
        'booking_date',
        'booking_time',
        'status',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function office()
    {
        return $this->belongsTo(Offices::class, 'office_id');
    }

    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeNoShow($query)
    {
        return $query->where('status', 'no-show');
    }


}
