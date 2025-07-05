<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentDetails extends Model
{
  protected $fillable = [
    'appointment_id',
    'request_for',
    'first_name',
    'middle_name',
    'last_name',
    'email',
    'phone',
    'address',
    'city',
    'state',
    'zip_code',
    'purpose',
    'notes',
  ];

  public function appointment(): BelongsTo
  {
    return $this->belongsTo(Appointments::class, 'appointment_id');
  }

  /**
   * Get the full name of the person for this appointment
   */
  public function getFullNameAttribute(): string
  {
    $parts = array_filter([
      $this->first_name,
      $this->middle_name,
      $this->last_name,
    ]);
    return implode(' ', $parts);
  }

  /**
   * Get the complete address for this appointment
   */
  public function getCompleteAddressAttribute(): string
  {
    $addressParts = array_filter([
      $this->address,
      $this->city,
      $this->state,
      $this->zip_code,
    ]);
    return implode(', ', $addressParts);
  }
}