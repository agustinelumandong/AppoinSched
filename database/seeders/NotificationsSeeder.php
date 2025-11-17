<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping notifications seeding.');
            return;
        }

        $notificationTypes = [
            'App\Notifications\AppointmentScheduled',
            'App\Notifications\AppointmentApproved',
            'App\Notifications\AppointmentCancelled',
            'App\Notifications\DocumentRequestSubmitted',
            'App\Notifications\DocumentRequestApproved',
            'App\Notifications\DocumentRequestRejected',
            'App\Notifications\PaymentUploaded',
            'App\Notifications\PaymentVerified',
            'App\Notifications\DocumentReadyForPickup',
            'App\Notifications\DocumentRequestCompleted',
        ];

        foreach ($users as $user) {
            // Create 3-8 notifications per user
            $notificationCount = fake()->numberBetween(3, 8);

            for ($i = 0; $i < $notificationCount; $i++) {
                $type = fake()->randomElement($notificationTypes);
                $isRead = fake()->boolean(40); // 40% read

                $data = [
                    'title' => $this->getNotificationTitle($type),
                    'message' => fake()->sentence(),
                    'type' => $type,
                    'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
                ];

                DB::table('notifications')->insert([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'type' => $type,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $user->id,
                    'data' => json_encode($data),
                    'read_at' => $isRead ? now() : null,
                    'created_at' => $data['created_at'],
                    'updated_at' => $data['created_at'],
                ]);
            }
        }

        $this->command->info('Notifications seeded successfully!');
    }

    /**
     * Get notification title based on type
     */
    private function getNotificationTitle(string $type): string
    {
        return match (true) {
            str_contains($type, 'AppointmentScheduled') => 'Appointment Scheduled',
            str_contains($type, 'AppointmentApproved') => 'Appointment Approved',
            str_contains($type, 'AppointmentCancelled') => 'Appointment Cancelled',
            str_contains($type, 'DocumentRequestSubmitted') => 'Document Request Submitted',
            str_contains($type, 'DocumentRequestApproved') => 'Document Request Approved',
            str_contains($type, 'DocumentRequestRejected') => 'Document Request Rejected',
            str_contains($type, 'PaymentUploaded') => 'Payment Uploaded',
            str_contains($type, 'PaymentVerified') => 'Payment Verified',
            str_contains($type, 'DocumentReadyForPickup') => 'Document Ready for Pickup',
            str_contains($type, 'DocumentRequestCompleted') => 'Document Request Completed',
            default => 'Notification',
        };
    }
}

