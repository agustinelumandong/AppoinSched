<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointments;
use App\Models\User;

class TestAppointments extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user and staff member


        Appointments::factory(10)->create();
        Appointments::create([
            'user_id' => 13,
            'staff_id' => 15,
            'office_id' => 13,
            'service_id' => 3,
            'booking_date' => now(),
            'booking_time' => now(),
            'status' => 'pending',
            'notes' => 'Test appointment',
        ]);


    }
}
