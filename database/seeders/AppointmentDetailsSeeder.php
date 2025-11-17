<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Appointments;
use App\Models\AppointmentDetails;
use Illuminate\Database\Seeder;

class AppointmentDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $appointments = Appointments::whereDoesntHave('appointmentDetails')->get();

        $firstNames = [
            'Maria', 'Jose', 'Juan', 'Ana', 'Carlos', 'Rosa', 'Pedro', 'Carmen',
            'Antonio', 'Elena', 'Manuel', 'Rita', 'Francisco', 'Lourdes', 'Miguel', 'Teresa',
        ];

        $lastNames = [
            'Santos', 'Reyes', 'Cruz', 'Bautista', 'Ocampo', 'Garcia', 'Mendoza', 'Torres',
            'Flores', 'Rivera', 'Gonzales', 'Villanueva', 'Fernandez', 'Ramos', 'Lopez', 'Aquino',
        ];

        foreach ($appointments as $appointment) {
            $user = $appointment->user;

            AppointmentDetails::create([
                'appointment_id' => $appointment->id,
                'request_for' => fake()->randomElement(['myself', 'someone_else']),
                'first_name' => $user->first_name ?? fake()->randomElement($firstNames),
                'middle_name' => $user->middle_name ?? fake()->optional(0.7)->randomElement($firstNames),
                'last_name' => $user->last_name ?? fake()->randomElement($lastNames),
                'email' => $user->email ?? fake()->safeEmail(),
                'phone' => $user->personalInformation?->contact_no ?? fake()->numerify('09#########'),
                'metadata' => [
                    'certificate_requests' => fake()->optional(0.6)->randomElements(
                        ['Birth Certificate', 'Marriage Certificate', 'Death Certificate'],
                        fake()->numberBetween(1, 2)
                    ),
                    'reference_numbers' => fake()->optional(0.4)->randomElements(
                        ['BC-' . strtoupper(fake()->bothify('######')), 'MC-' . strtoupper(fake()->bothify('######'))],
                        fake()->numberBetween(1, 2)
                    ),
                ],
                'purpose' => $appointment->purpose ?? fake()->sentence(),
                'notes' => fake()->optional(0.4)->sentence(),
            ]);
        }

        $this->command->info('Appointment details seeded successfully!');
    }
}

