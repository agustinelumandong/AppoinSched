<?php
declare(strict_types=1);
namespace App\Enums;

enum AdminNotificationEvent: string
{
    // document request
    case UserSubmittedDocumentRequest = 'user_submitted_document_request';
    case UserPaymentProofUploaded = 'user_payment_proof_uploaded';
    case UserPayedDocumentRequest = 'user_payed_document_request';

    // appointment
    case UserAppointmentScheduled = 'user_appointment_scheduled';
    case UserAppointmentCancelled = 'user_appointment_cancelled';
    case UserAppointmentRescheduled = 'user_appointment_rescheduled';
    case UserAppointmentCompleted = 'user_appointment_completed';
}
