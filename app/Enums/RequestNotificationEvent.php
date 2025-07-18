<?php

declare(strict_types=1);

namespace App\Enums;

enum RequestNotificationEvent: string
{
  case Submitted = 'submitted';
  case Approved = 'approved';
  case Rejected = 'rejected';
  case Completed = 'completed';
  case PaymentProofUploaded = 'payment_uploaded';

  // Payment Status Update
  case PaymentVerified = 'payment_verified';
  case PaymentProcessing = 'payment_processing'; 
  case PaymentFailed = 'payment_failed';

  // Document Status Update
  case DocumentApproved = 'document_approved';
  case DocumentRejected = 'document_rejected';
  case DocumentCompleted = 'document_completed';
  case DocumentCancelled = 'document_cancelled';
  case DocumentInProgress = 'document_in_progress';
  case DocumentReadyForPickup = 'document_ready_for_pickup';

  
  
  // Appointment Status Update  
  case AppointmentApproved = 'appointment_approved';
  case AppointmentScheduled = 'appointment_scheduled';
  case AppointmentCancelled = 'appointment_cancelled';
  case AppointmentRescheduled = 'appointment_rescheduled';
  case AppointmentReminder = 'appointment_reminder';
}