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
      RequestNotificationEvent::PaymentVerified => (new MailMessage)
        ->subject('Payment Confirmed – Document in Process')
        ->line('We’ve verified your payment.')
        ->line('Expected release date: ' . $this->data['release_date']),
      RequestNotificationEvent::AppointmentScheduled => (new MailMessage)
        ->subject('Appointment Scheduled')
        ->line("Your appointment is set for {$this->data['date']} at {$this->data['time']}")
        ->line("Location: {$this->data['location']}")
        ->line("Service: {$this->data['service']}"),
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
      RequestNotificationEvent::AppointmentScheduled => 'Appointment scheduled.',
      RequestNotificationEvent::AppointmentApproved => 'Appointment approved.',
      RequestNotificationEvent::AppointmentCancelled => 'Appointment cancelled.',
      RequestNotificationEvent::AppointmentRescheduled => 'Appointment rescheduled.',
      RequestNotificationEvent::Completed => 'Document is ready for pickup.',
      RequestNotificationEvent::AppointmentReminder => 'Reminder for your appointment.',
    };
  }
}