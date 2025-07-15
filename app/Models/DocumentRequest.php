<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Offices;
use App\Models\Services;
use App\Models\User;
use App\Models\DocumentRequestDetails;

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
        'to_whom',
        'purpose',
        'reference_number',
        'requested_date',
        'completed_date',
        'payment_status',
        'payment_reference',
        'payment_method',
        'payment_proof_path',
        'payment_date',
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

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeNoShow($query)
    {
        return $query->where('status', 'no-show');
    }

    public function details()
    {
        return $this->hasOne(DocumentRequestDetails::class);
    }

}
