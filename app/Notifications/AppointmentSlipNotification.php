<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Appointments;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class AppointmentSlipNotification extends Notification implements ShouldQueue
{
  use Queueable;

  public function __construct(
    public Appointments $appointment
  ) {
  }

  public function via($notifiable): array
  {
    return ['mail'];
  }

  public function toMail($notifiable): MailMessage
  {
    // Generate PDF for the appointment slip
    $pdf = $this->generateAppointmentSlipPdf();

    return (new MailMessage)
      ->subject('Your Appointment Slip - ' . $this->appointment->reference_number)
      ->greeting('Hello ' . $this->appointment->appointmentDetails->first_name . '!')
      ->line('Your appointment has been successfully scheduled.')
      ->line('Please find your appointment slip attached to this email.')
      ->line('Important: Please bring this slip and a valid ID to your appointment.')
      ->action('View Appointment Details', route('client.appointments'))
      ->line('Thank you for choosing our services!')
      ->attachData($pdf, 'appointment-slip-' . $this->appointment->reference_number . '.pdf', [
        'mime' => 'application/pdf',
      ]);
  }

  private function generateAppointmentSlipPdf(): string
  {
    $appointment = $this->appointment;
    $details = $appointment->appointmentDetails;

    $data = [
      'reference_number' => $appointment->reference_number,
      'office' => $appointment->office,
      'services' => $appointment->service,
      'staff' => $appointment->staff,
      'purpose' => $appointment->purpose,
      'selectedDate' => $appointment->booking_date,
      'selectedTime' => $appointment->booking_time,
      'first_name' => $details->first_name,
      'middle_name' => $details->middle_name,
      'last_name' => $details->last_name,
      'email' => $details->email,
      'phone' => $details->phone,
      'address' => $details->address,
      'city' => $details->city,
      'state' => $details->state,
      'zip_code' => $details->zip_code,
    ];

    $pdf = Pdf::loadView('emails.appointment-slip-pdf', $data);
    return $pdf->output();
  }
}