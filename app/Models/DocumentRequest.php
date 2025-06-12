<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Offices;
use App\Models\Services;
use App\Models\User;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'staff_id',
        'office_id',
        'service_id',
        'status',
        'remarks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class);
    }

    public function office()
    {
        return $this->belongsTo(Offices::class);
    }

    public function service()
    {
        return $this->belongsTo(Services::class);
    }


}
