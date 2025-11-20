<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Appointments;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelPastAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:cancel-past';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel appointments that have passed their scheduled time by 30 minutes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for past appointments to cancel...');

        $now = Carbon::now();
        $cancelledCount = 0;

        // Find appointments that are:
        // 1. Status is 'on-going'
        // 2. Booking date + time + 30 minutes is in the past
        $pastAppointments = Appointments::where('status', 'on-going')
            ->get()
            ->filter(function ($appointment) use ($now) {
                try {
                    $appointmentDateTime = Carbon::parse($appointment->booking_date . ' ' . $appointment->booking_time);
                    $cancelTime = $appointmentDateTime->copy()->addMinutes(30);
                    return $now->greaterThanOrEqualTo($cancelTime);
                } catch (\Exception $e) {
                    Log::error('Error parsing appointment datetime', [
                        'appointment_id' => $appointment->id,
                        'booking_date' => $appointment->booking_date,
                        'booking_time' => $appointment->booking_time,
                        'error' => $e->getMessage(),
                    ]);
                    return false;
                }
            });

        foreach ($pastAppointments as $appointment) {
            $appointment->update(['status' => 'cancelled']);
            $cancelledCount++;

            Log::info('Appointment automatically cancelled', [
                'appointment_id' => $appointment->id,
                'reference_number' => $appointment->reference_number,
                'booking_date' => $appointment->booking_date,
                'booking_time' => $appointment->booking_time,
                'cancelled_at' => $now,
            ]);
        }

        if ($cancelledCount > 0) {
            $this->info("Successfully cancelled {$cancelledCount} appointment(s).");
        } else {
            $this->info('No appointments to cancel.');
        }

        return Command::SUCCESS;
    }
}

