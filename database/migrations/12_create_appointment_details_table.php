<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointment_details', function (Blueprint $table) {
            // Primary and foreign keys
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('set null');

            // Core appointment details
            $table->string('to_whom')->nullable();
            $table->date('booking_date');
            $table->time('booking_time');
            $table->string('purpose')->nullable();
            $table->json('form_data')->nullable();
            $table->text('special_instructions')->nullable();
            $table->enum(
                'status',
                [
                    'pending',
                    'in_progress',
                    'completed',
                    'cancelled',
                    'on_hold'
                ]
            )->default('pending');
            $table->text('staff_notes')->nullable();

            // Timestamps
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['service_id', 'booking_date', 'booking_time']);
            $table->index(['appointment_id', 'status']);
            $table->index(['service_id', 'status']);
            $table->index(['appointment_id', 'service_id']);
            $table->index(['appointment_id', 'booking_date']);
            $table->index(['status', 'booking_date']);
            $table->index(['booking_date', 'service_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_details');
    }
};
