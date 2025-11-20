<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DocumentRequest;
use App\Models\User;
use App\Models\Offices;
use App\Models\Services;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentRequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = User::role('client')->whereHas('personalInformation')->get();
        $offices = Offices::all();
        $services = Services::all();
        $staffMembers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['MCR-staff', 'MTO-staff', 'BPLS-staff', 'MCR-admin', 'MTO-admin', 'BPLS-admin']);
        })->get();

        if ($clients->isEmpty() || $offices->isEmpty() || $services->isEmpty()) {
            $this->command->warn('No clients, offices, or services found. Skipping document requests seeding.');
            return;
        }

        $statuses = ['pending', 'in-progress', 'cancelled'];
        $paymentStatuses = ['unpaid', 'paid', 'failed'];
        $paymentMethods = ['Cash', 'GCash', 'PayMaya', 'Bank Transfer', 'Credit Card'];

        // Create document requests for each service
        foreach ($services as $service) {
            $office = $service->office;
            $requestCount = fake()->numberBetween(8, 12);

            for ($i = 0; $i < $requestCount; $i++) {
                $client = $clients->random();
                $paymentStatus = fake()->randomElement($paymentStatuses);

                // Determine status based on payment status
                // If paid, status should be in-progress (will be automatically set by model)
                // Otherwise, can be pending or cancelled
                if ($paymentStatus === 'paid') {
                    $status = 'in-progress';
                } else {
                    $status = fake()->randomElement(['pending', 'cancelled']);
                }

                // Assign staff member based on office
                $officeStaff = $staffMembers->filter(function ($staff) use ($office) {
                    $officeId = $staff->getOfficeIdForStaff();
                    return $officeId === $office->id;
                });

                $staffId = null;
                if ($officeStaff->isNotEmpty() && $status === 'in-progress') {
                    $staffId = $officeStaff->random()->id;
                }

                // Generate dates based on status
                $requestedDate = fake()->dateTimeBetween('-30 days', 'now');
                $completedDate = null;
                $paymentDate = null;

                // Only set completed_date for in-progress requests that have payment
                if ($status === 'in-progress' && $paymentStatus === 'paid') {
                    $completedDate = fake()->optional(0.3)->dateTimeBetween($requestedDate, 'now');
                }

                if ($paymentStatus === 'paid') {
                    $paymentDate = fake()->dateTimeBetween($requestedDate, $completedDate ?? 'now');
                }

                // Determine purpose based on service type
                $purpose = null;
                if ($service->title === 'Death Certificate') {
                    // Death Certificate purposes
                    $purpose = fake()->randomElement([
                        'Legal Proceedings',
                        'Insurance Claims',
                        'Property Transfer',
                        'Social Security Benefits',
                        'Veterans Benefits',
                        'Genealogy Research',
                        'Personal Records',
                        'Other',
                    ]);

                    // If "Other", set purpose to custom text
                    if ($purpose === 'Other') {
                        $purpose = fake()->randomElement([
                            'Estate Settlement',
                            'Beneficiary Claim',
                            'Family Records',
                        ]);
                    }
                } else {
                    // Non-Death Certificate purposes
                    $purpose = fake()->randomElement([
                        'school_requirement',
                        'employment',
                        'government_id',
                        'hospital_use',
                        'others',
                    ]);

                    // If "others", set purpose to custom text
                    if ($purpose === 'others') {
                        $purpose = fake()->randomElement([
                            'Travel Documentation',
                            'Business Registration',
                            'Personal Records',
                            'Legal Documentation',
                        ]);
                    }
                }

                DocumentRequest::create([
                    'user_id' => $client->id,
                    'staff_id' => $staffId,
                    'office_id' => $office->id,
                    'service_id' => $service->id,
                    'to_whom' => fake()->optional(0.6)->name(),
                    'purpose' => $purpose,
                    'reference_number' => 'DR-' . strtoupper(Str::random(10)),
                    'status' => $status,
                    'remarks' => fake()->optional(0.5)->sentence(),
                    'payment_status' => $paymentStatus,
                    'payment_reference' => $paymentStatus === 'paid' ? 'PAY-' . strtoupper(Str::random(8)) : null,
                    'payment_method' => $paymentStatus === 'paid' ? fake()->randomElement($paymentMethods) : null,
                    'payment_proof_path' => $paymentStatus === 'paid' ? 'payments/proof_' . Str::random(10) . '.pdf' : null,
                    'payment_date' => $paymentDate,
                    'requested_date' => $requestedDate,
                    'completed_date' => $completedDate,
                ]);
            }
        }

        $this->command->info('Document requests seeded successfully!');
    }
}

