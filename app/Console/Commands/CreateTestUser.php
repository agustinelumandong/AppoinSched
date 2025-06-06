<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-test-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test user with known credentials for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@test.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role
        if (!$user->hasRole('admin')) {
            $user->assignRole('admin');
            $this->info('Admin role assigned to user!');
        }

        $this->info('Test user created successfully!');
        $this->info('Email: admin@test.com');
        $this->info('Password: password');

        return 0;
    }
}
