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

    /**
     * Valid document request status values
     */
    public const VALID_STATUSES = ['pending', 'in-progress', 'ready-for-pickup', 'complete', 'cancelled'];

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

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Automatically set status to 'in-progress' when payment_status changes to 'paid'
        static::updating(function ($documentRequest) {
            // Check if payment_status is being changed to 'paid'
            if ($documentRequest->isDirty('payment_status') && $documentRequest->payment_status === 'paid') {
                // Only transition if current status is 'pending'
                if ($documentRequest->getOriginal('status') === 'pending') {
                    $documentRequest->status = 'in-progress';
                }
            }
        });
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Deprecated: Use scopeInProgress() instead
    public function scopeApproved($query)
    {
        return $query->where('status', 'in-progress');
    }

    // Deprecated: Use scopeCancelled() instead
    public function scopeRejected($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Deprecated: Use scopeInProgress() instead
    public function scopeCompleted($query)
    {
        return $query->where('status', 'in-progress');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in-progress');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeReadyForPickup($query)
    {
        return $query->where('status', 'ready-for-pickup');
    }

    public function scopeComplete($query)
    {
        return $query->where('status', 'complete');
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

    // Deprecated: Use isInProgress() instead
    public function isCompleted()
    {
        return $this->status === 'in-progress';
    }

    // Deprecated: Use isCancelled() instead
    public function isRejected()
    {
        return $this->status === 'cancelled';
    }

    // Deprecated: Use isCancelled() instead
    public function isNoShow()
    {
        return $this->status === 'cancelled';
    }

    public function isInProgress()
    {
        return $this->status === 'in-progress';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isReadyForPickup()
    {
        return $this->status === 'ready-for-pickup';
    }

    public function isComplete()
    {
        return $this->status === 'complete';
    }

    // Deprecated: Use isInProgress() instead
    public function isApproved()
    {
        return $this->status === 'in-progress';
    }

    // Deprecated: Use status update directly or new helper methods
    public function markAsCompleted()
    {
        $this->status = 'in-progress';
        $this->completed_date = now();
        $this->save();
    }

    // Deprecated: Use markAsCancelled() instead
    public function markAsRejected($remarks = null)
    {
        $this->status = 'cancelled';
        if ($remarks) {
            $this->remarks = $remarks;
        }
        $this->save();
    }

    // Deprecated: Use markAsCancelled() instead
    public function markAsNoShow()
    {
        $this->status = 'cancelled';
        $this->save();
    }

    // Deprecated: Use markAsInProgress() instead
    public function markAsApproved()
    {
        $this->status = 'in-progress';
        $this->save();
    }

    public function markAsInProgress()
    {
        $this->status = 'in-progress';
        $this->save();
    }

    public function markAsCancelled($remarks = null)
    {
        $this->status = 'cancelled';
        if ($remarks) {
            $this->remarks = $remarks;
        }
        $this->save();
    }

    public function markAsReadyForPickup()
    {
        $this->status = 'ready-for-pickup';
        $this->save();
    }

    public function markAsComplete()
    {
        $this->status = 'complete';
        $this->completed_date = now();
        $this->save();
    }

    public function markAsPaid($paymentReference, $paymentMethod)
    {
        $this->payment_status = 'paid';
        $this->payment_reference = $paymentReference;
        $this->payment_method = $paymentMethod;
        $this->payment_date = now();

        // Automatically set status to 'in-progress' if currently 'pending'
        if ($this->status === 'pending') {
            $this->status = 'in-progress';
        }

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

    public function totalInProgressRequests()
    {
        return self::where('status', 'in-progress')->count();
    }

    public function totalCancelledRequests()
    {
        return self::where('status', 'cancelled')->count();
    }

    public function totalPendingRequests()
    {
        return self::where('status', 'pending')->count();
    }

    // Deprecated: Use totalInProgressRequests() instead
    public function totalCompletedRequests()
    {
        return self::where('status', 'in-progress')->count();
    }

    // Deprecated: Use totalCancelledRequests() instead
    public function totalRejectedRequests()
    {
        return self::where('status', 'cancelled')->count();
    }

    // Deprecated: Use totalCancelledRequests() instead
    public function totalNoShowRequests()
    {
        return self::where('status', 'cancelled')->count();
    }

    // Deprecated: Use totalInProgressRequests() instead
    public function totalApprovedRequests()
    {
        return self::where('status', 'in-progress')->count();
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
