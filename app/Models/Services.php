<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Appointments;
use App\Models\DocumentRequest;


class Services extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'title',
        'slug',
        'description',
        'price',
        'is_active'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function office()
    {
        return $this->belongsTo(Offices::class, 'office_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointments::class, 'service_id');
    }

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class, 'service_id');
    }



}
