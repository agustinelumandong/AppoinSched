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
        
        Appointments::factory(10)->create();
    }
}
