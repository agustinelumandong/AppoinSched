<?php

declare(strict_types=1);

namespace App\Enums;

enum RequestNotificationEvent: string
{
  case Submitted = 'submitted';
  case Approved = 'approved';
  case Rejected = 'rejected';
  case PaymentProofUploaded = 'payment_uploaded';
  case PaymentVerified = 'payment_verified';
  case AppointmentScheduled = 'appointment_scheduled';
  case AppointmentApproved = 'appointment_approved';
  case AppointmentCancelled = 'appointment_cancelled';
  case AppointmentRescheduled = 'appointment_rescheduled';
  case AppointmentReminder = 'appointment_reminder';
  case Completed = 'completed';
}