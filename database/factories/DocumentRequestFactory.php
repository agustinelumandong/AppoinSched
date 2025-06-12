<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Offices;
use App\Models\Services;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DocumentRequest>
 */
class DocumentRequestFactory extends Factory
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
            'staff_id' => User::inRandomOrder()->first()->id,
            'office_id' => Offices::inRandomOrder()->first()->id,
            'service_id' => Services::inRandomOrder()->first()->id,
            'status' => fake()->randomElement(['pending', 'approved', 'rejected', 'completed']),
            'remarks' => fake()->optional(0.7)->sentence(),
        ];
    }
}
