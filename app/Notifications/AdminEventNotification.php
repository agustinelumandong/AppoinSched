<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Enums\AdminNotificationEvent;

class AdminEventNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public AdminNotificationEvent $event,
        public array $data = []
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        $settings = $notifiable->notification_settings ?? [];
        if (isset($settings[$this->event->value]) && $settings[$this->event->value] === false) {
            return [];
        }
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        // return (new MailMessage)->markdown('mail.admin-event-notification');
        return match ($this->event) {
            AdminNotificationEvent::UserSubmittedDocumentRequest => (new MailMessage)
                ->subject('User Submitted Document Request')
                ->line('A user has submitted a document request.')
                ->line('Reference #: ' . $this->data['reference_no'])
                ->line('Details: ' . $this->data['summary']),
            AdminNotificationEvent::UserAppointmentScheduled => (new MailMessage)
                ->subject('User Scheduled Appointment')
                ->line('A user has scheduled an appointment.')
                ->line('Reference #: ' . $this->data['reference_no'])
                ->line('Date: ' . $this->data['date'])
                ->line('Time: ' . $this->data['time'])
                ->line('Location: ' . $this->data['location']),
            AdminNotificationEvent::UserPayedDocumentRequest => (new MailMessage)
                ->subject('User Paid Document Request')
                ->line('A user has paid for a document request.')
                ->line('Reference #: ' . $this->data['reference_no'])
                ->line('Summary: ' . $this->data['summary'])
                ->line('Payment Method: ' . $this->data['payment_method']),
            AdminNotificationEvent::UserAppointmentCancelled => (new MailMessage)
                ->subject('User Cancelled Appointment')
                ->line('A user has cancelled an appointment.')
                ->line('Reference #: ' . $this->data['reference_no'])
                ->line('Date: ' . $this->data['date'])
                ->line('Time: ' . $this->data['time'])
                ->line('Location: ' . $this->data['location']),
            AdminNotificationEvent::UserAppointmentRescheduled => (new MailMessage)
                ->subject('User Rescheduled Appointment')
                ->line('A user has rescheduled an appointment.')
                ->line('Reference #: ' . $this->data['reference_no'])
                ->line('Date: ' . $this->data['date'])
                ->line('Time: ' . $this->data['time'])
                ->line('Location: ' . $this->data['location']),
            AdminNotificationEvent::UserAppointmentCompleted => (new MailMessage)
                ->subject('User Completed Appointment')
                ->line('A user has completed an appointment.')
                ->line('Reference #: ' . $this->data['reference_no'])
                ->line('Date: ' . $this->data['date'])
                ->line('Time: ' . $this->data['time'])
                ->line('Location: ' . $this->data['location']),
        };
    }


    public function toDatabase($notifiable)
    {
        return [
            'event' => $this->event->value,
            'message' => $this->generateSummaryMessage()
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    protected function generateSummaryMessage(): string
    {
        return match ($this->event) {
            AdminNotificationEvent::UserSubmittedDocumentRequest => 'A user has submitted a document request. Reference #: ' . $this->data['reference_no'] . ' Summary: ' . $this->data['summary'],
            AdminNotificationEvent::UserPaymentProofUploaded => 'A user has uploaded a payment proof.',
            AdminNotificationEvent::UserPayedDocumentRequest => 'A user has paid for a document request. Reference #: ' . $this->data['reference_no'] . ' Summary: ' . $this->data['summary'] . ' Payment Method: ' . $this->data['payment_method'],
            AdminNotificationEvent::UserAppointmentScheduled => 'A user has scheduled an appointment. Reference #: ' . $this->data['reference_no'] . ' Service: ' . $this->data['service'] . ' Date: ' . $this->data['date'] . ' Time: ' . $this->data['time'],
            AdminNotificationEvent::UserAppointmentCancelled => 'A user has cancelled an appointment. Reference #: ' . $this->data['reference_no'] . ' Service: ' . $this->data['service'] . ' Date: ' . $this->data['date'] . ' Time: ' . $this->data['time'],
            AdminNotificationEvent::UserAppointmentRescheduled => 'A user has rescheduled an appointment. Reference #: ' . $this->data['reference_no'] . ' Service: ' . $this->data['service'] . ' Date: ' . $this->data['date'] . ' Time: ' . $this->data['time'],
            AdminNotificationEvent::UserAppointmentCompleted => 'A user has completed an appointment. Reference #: ' . $this->data['reference_no'] . ' Service: ' . $this->data['service'] . ' Date: ' . $this->data['date'] . ' Time: ' . $this->data['time'],
        };
    }
}
