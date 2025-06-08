<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

}
