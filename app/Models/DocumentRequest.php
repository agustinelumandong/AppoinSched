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

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isNoShow()
    {
        return $this->status === 'no-show';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->completed_date = now();
        $this->save();
    }

    public function markAsRejected($remarks = null)
    {
        $this->status = 'rejected';
        if ($remarks) {
            $this->remarks = $remarks;
        }
        $this->save();
    }

    public function markAsNoShow()
    {
        $this->status = 'no-show';
        $this->save();
    }

    public function markAsApproved()
    {
        $this->status = 'approved';
        $this->save();
    }

    public function markAsPaid($paymentReference, $paymentMethod)
    {
        $this->payment_status = 'paid';
        $this->payment_reference = $paymentReference;
        $this->payment_method = $paymentMethod;
        $this->payment_date = now();
        $this->save();
    }

    public function markAsUnpaid()
    {
        $this->payment_status = 'unpaid';
        $this->payment_reference = null;
        $this->payment_method = null;
        $this->payment_date = null;
        $this->save();
    }

    public function generateReferenceNumber()
    {
        $this->reference_number = 'DR-' . strtoupper(uniqid());
        $this->save();
    }

    public function scopeSearch($query, $term)
    {
        $term = "%$term%";

        $query->where(function ($query) use ($term) {
            $query->where('reference_number', 'like', $term)
                  ->orWhere('to_whom', 'like', $term)
                  ->orWhere('purpose', 'like', $term)
                  ->orWhereHas('user', function ($q) use ($term) {
                      $q->where('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhere('email', 'like', $term);
                  })
                  ->orWhereHas('staff', function ($q) use ($term) {
                      $q->where('first_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhere('email', 'like', $term);
                  })
                  ->orWhereHas('office', function ($q) use ($term) {
                      $q->where('name', 'like', $term);
                  })
                  ->orWhereHas('service', function ($q) use ($term) {
                      $q->where('name', 'like', $term);
                  });
        });
    }

    public function totalDocumentRequests()
    {
        return self::count();
    }

    public function totalCompletedRequests()
    {
        return self::where('status', 'completed')->count();
    }

    public function totalPendingRequests()
    {
        return self::where('status', 'pending')->count();
    }

    public function totalRejectedRequests()
    {
        return self::where('status', 'rejected')->count();
    }

    public function totalNoShowRequests()
    {
        return self::where('status', 'no-show')->count();
    }

    public function totalApprovedRequests()
    {
        return self::where('status', 'approved')->count();
    }

    public function totalPaidRequests()
    {
        return self::where('payment_status', 'paid')->count();
    }

    public function totalUnpaidRequests()
    {
        return self::where('payment_status', 'unpaid')->count();
    }

    public function totalRequestsByService($serviceId)
    {
        return self::where('service_id', $serviceId)->count();
    }

    public function totalRequestsByOffice($officeId)
    {
        return self::where('office_id', $officeId)->count();
    }

    public function scopeRecent($query, $limit = 5)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    

}
