<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Offices;
use App\Models\Services;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AppointmentsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'office_id' => Offices::inRandomOrder()->first()->id,
            'service_id' => Services::inRandomOrder()->first()->id,
            'staff_id' => User::inRandomOrder()->first()->id,
            'booking_date' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'booking_time' => fake()->dateTimeBetween('08:00', '17:00')->format('H:i'),
            'status' => fake()->randomElement(['pending', 'approved', 'cancelled', 'completed', 'no-show']),
            'notes' => fake()->optional(0.7)->sentence(),
        ];
    }
}
