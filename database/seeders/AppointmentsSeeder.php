<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Appointments;
use App\Models\User;
use App\Models\Offices;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AppointmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = Offices::all();
        $clients = User::role('client')->get();
        $statuses = ['on-going', 'cancelled', 'completed'];

        if ($clients->isEmpty() || $offices->isEmpty()) {
            $this->command->warn('No clients or offices found. Skipping appointments seeding.');
            return;
        }

        // Create appointments for each office
        foreach ($offices as $office) {
            // Create 10-15 appointments per office
            $appointmentCount = fake()->numberBetween(10, 15);

            for ($i = 0; $i < $appointmentCount; $i++) {
                $client = $clients->random();
                $status = fake()->randomElement($statuses);

                // Generate booking date - mix of past, present, and future
                $daysOffset = fake()->numberBetween(-60, 90); // -60 days to +90 days
                $bookingDate = now()->addDays($daysOffset)->format('Y-m-d');

                // Generate booking time (8 AM to 5 PM)
                $hour = fake()->numberBetween(8, 17);
                $minute = fake()->randomElement([0, 15, 30, 45]);
                $bookingTime = sprintf('%02d:%02d:00', $hour, $minute);

                $appointment = Appointments::create([
                    'user_id' => $client->id,
                    'office_id' => $office->id,
                    'booking_date' => $bookingDate,
                    'booking_time' => $bookingTime,
                    'reference_number' => 'APT-' . strtoupper(Str::random(10)),
                    'purpose' => fake()->randomElement([
                        'Document Request Consultation',
                        'Certificate Application',
                        'Business Permit Inquiry',
                        'Payment Processing',
                        'General Inquiry',
                        'Document Verification',
                    ]),
                    'notes' => fake()->optional(0.5)->sentence(),
                    'status' => $status,
                ]);
            }
        }

        $this->command->info('Appointments seeded successfully!');
    }
}

