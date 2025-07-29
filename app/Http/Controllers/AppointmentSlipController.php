<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Appointments;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class AppointmentSlipController extends Controller
{
  public function downloadPdf(string $reference_number): Response
  {
    $appointment = Appointments::with(['office', 'appointmentDetails'])
      ->where('reference_number', $reference_number)
      ->first();

    if (!$appointment) {
      abort(404, 'Appointment not found');
    }

    $details = $appointment->appointmentDetails;

    $data = [
      'reference_number' => $appointment->reference_number,
      'office' => $appointment->office,
      'purpose' => $appointment->purpose,
      'selectedDate' => $appointment->booking_date,
      'selectedTime' => $appointment->booking_time,
      'first_name' => $details->first_name,
      'middle_name' => $details->middle_name,
      'last_name' => $details->last_name,
      'email' => $details->email,
      'phone' => $details->phone,
      'metadata' => $details->metadata ?? null,
    ];

    $pdf = Pdf::loadView('emails.appointment-slip-pdf', $data);

    return $pdf->download('appointment-slip-' . $reference_number . '.pdf');
  }
}
