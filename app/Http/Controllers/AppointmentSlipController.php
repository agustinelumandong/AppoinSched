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
    $appointment = Appointments::where('reference_number', $reference_number)
      ->with(['office', 'service', 'staff', 'appointmentDetails'])
      ->first();

    if (!$appointment) {
      abort(404, 'Appointment not found');
    }

    $details = $appointment->appointmentDetails;

    $data = [
      'reference_number' => $appointment->reference_number,
      'office' => $appointment->office,
      'service' => $appointment->service,
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

    return $pdf->download('appointment-slip-' . $reference_number . '.pdf');
  }
}