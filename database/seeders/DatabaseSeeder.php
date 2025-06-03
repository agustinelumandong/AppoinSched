<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        DB::table('users')->delete();
        User::factory()->create([
            'first_name' => 'John',
            'middle_name' => 'Doe',
            'last_name' => 'Doe',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
