<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\RequestNotificationEvent;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RequestEventNotification extends Notification
{
  public function __construct(
    public RequestNotificationEvent $event,
    public array $data = []
  ) {
  }

  public function via($notifiable)
  {
    $settings = $notifiable->notification_settings ?? [];
    if (isset($settings[$this->event->value]) && $settings[$this->event->value] === false) {
      return [];
    }
    return ['mail', 'database'];
  }

  public function toMail($notifiable)
  {
    return match ($this->event) {
      RequestNotificationEvent::Submitted => (new MailMessage)
        ->subject('Your Document Request has been Submitted')
        ->line('Thank you for your submission.')
        ->line("Reference #: {$this->data['reference_no']}")
        ->line('Details: ' . $this->data['summary']),
      RequestNotificationEvent::Approved => (new MailMessage)
        ->subject('Request Approved – Proceed to Payment')
        ->line('Your request has been approved.')
        ->line('Payment Instructions: ' . $this->data['payment_instructions']),
      RequestNotificationEvent::Rejected => (new MailMessage)
        ->subject('Request Rejected – Action Required')
        ->line('Unfortunately, your request was rejected.')
        ->line('Reason: ' . $this->data['rejection_reason']),
      RequestNotificationEvent::PaymentProofUploaded => (new MailMessage)
        ->subject('Payment Proof Received')
        ->line('Your proof of payment has been received and is under review.'),
      // Payment Status Update
      RequestNotificationEvent::PaymentVerified => (new MailMessage)
        ->subject('Payment Confirmed – Document in Process')
        ->line('We’ve verified your payment.')
        ->line('On your request ' . $this->data['reference_no'] . ' will be processed and you will be notified when it is ready for pickup.'),
      RequestNotificationEvent::PaymentProcessing => (new MailMessage)
        ->subject('Payment Processing')
        ->line('We’ve received your payment for your request ' . $this->data['reference_no'] . ' and it is being processed.'),
      RequestNotificationEvent::PaymentFailed => (new MailMessage)
        ->subject('Payment Failed')
        ->line('Your payment has failed for your request ' . $this->data['reference_no'] . '.')
        ->line('Please try again.'),
      // Document Status Update
      RequestNotificationEvent::DocumentApproved => (new MailMessage)
        ->subject('Document Approved')
        ->line('Your document request ' . $this->data['reference_no'] . ' has been approved.'),
      RequestNotificationEvent::DocumentRejected => (new MailMessage)
        ->subject('Document Rejected')
        ->line('Your document request ' . $this->data['reference_no'] . ' has been rejected.'),
      RequestNotificationEvent::DocumentCompleted => (new MailMessage)
        ->subject('Document Completed')
        ->line('Your document request ' . $this->data['reference_no'] . ' has been completed.'),
      RequestNotificationEvent::DocumentCancelled => (new MailMessage)
        ->subject('Document Cancelled')
        ->line('Your document request ' . $this->data['reference_no'] . ' has been cancelled.'),
      RequestNotificationEvent::DocumentInProgress => (new MailMessage)
        ->subject('Document in Progress')
        ->line('Your document request ' . $this->data['reference_no'] . ' is being processed.'),
      RequestNotificationEvent::DocumentReadyForPickup => (new MailMessage)
        ->subject('Document Ready for Pickup')
        ->line('Your document request ' . $this->data['reference_no'] . ' is ready for pickup.'),
      RequestNotificationEvent::AppointmentScheduled => (new MailMessage)
        ->subject('Appointment Scheduled')
        ->line("Your appointment is set for {$this->data['date']} at {$this->data['time']}")
        ->line("Location: {$this->data['location']}"),
      RequestNotificationEvent::AppointmentApproved => (new MailMessage)
        ->subject('Appointment Approved')
        ->line("Your appointment has been approved.")
        ->line("Date: {$this->data['date']} at {$this->data['time']}")
        ->line("Location: {$this->data['location']}"),
      RequestNotificationEvent::AppointmentCancelled => (new MailMessage)
        ->subject('Appointment Cancelled')
        ->line("Your appointment has been cancelled.")
        ->line("Date: {$this->data['date']} at {$this->data['time']}")
        ->line("Reason: " . ($this->data['reason'] ?? 'No reason provided')),
      RequestNotificationEvent::AppointmentRescheduled => (new MailMessage)
        ->subject('Appointment Rescheduled')
        ->line("Your appointment has been rescheduled.")
        ->line("New Date: {$this->data['new_date']} at {$this->data['new_time']}")
        ->line("Previous Date: {$this->data['old_date']} at {$this->data['old_time']}"),
      RequestNotificationEvent::Completed => (new MailMessage)
        ->subject('Your Document is Ready for Pickup')
        ->line('Your requested document is now ready.')
        ->line("Pickup Instructions: {$this->data['instructions']}"),
      RequestNotificationEvent::AppointmentReminder => (new MailMessage)
        ->subject('Reminder: Your Appointment is Tomorrow')
        ->line("Just a reminder that your appointment is on {$this->data['date']} at {$this->data['time']}.")
        ->line("Location: {$this->data['location']}"),
    };
  }

  public function toDatabase($notifiable)
  {
    return [
      'event' => $this->event->value,
      'message' => $this->generateSummaryMessage()
    ];
  }

  protected function generateSummaryMessage(): string
  {
    return match ($this->event) {
      RequestNotificationEvent::Submitted => 'You submitted a document request.',
      RequestNotificationEvent::Approved => 'Your request was approved.',
      RequestNotificationEvent::Rejected => 'Your request was rejected.',
      RequestNotificationEvent::PaymentProofUploaded => 'Payment proof uploaded.',
      RequestNotificationEvent::PaymentVerified => 'Your payment was verified.',
      RequestNotificationEvent::PaymentProcessing => 'Your payment is being processed.',
      RequestNotificationEvent::PaymentFailed => 'Your payment has failed.',
      RequestNotificationEvent::DocumentApproved => 'Your document has been approved.',
      RequestNotificationEvent::DocumentRejected => 'Your document has been rejected.',
      RequestNotificationEvent::DocumentCompleted => 'Your document has been completed.',
      RequestNotificationEvent::DocumentCancelled => 'Your document has been cancelled.',
      RequestNotificationEvent::DocumentInProgress => 'Your document is being processed.',
      RequestNotificationEvent::DocumentReadyForPickup => 'Your document is ready for pickup.',
      RequestNotificationEvent::AppointmentScheduled => 'Appointment scheduled.',
      RequestNotificationEvent::AppointmentApproved => 'Appointment approved.',
      RequestNotificationEvent::AppointmentCancelled => 'Appointment cancelled.',
      RequestNotificationEvent::AppointmentRescheduled => 'Appointment rescheduled.',
      RequestNotificationEvent::Completed => 'Document is ready for pickup.',
      RequestNotificationEvent::AppointmentReminder => 'Reminder for your appointment.',
    };
  }
}