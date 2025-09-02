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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number')->unique()->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('office_id')->constrained('offices')->onDelete('cascade');
            $table->enum('status', [
                'pending',
                'confirmed',
                'in_progress',
                'completed',
                'cancelled',
                'no_show',
                'rescheduled'
            ])->default('pending');
            // $table->date('booking_date');
            // $table->time('booking_time');
            // $table->string('purpose')->nullable();
            // $table->text('notes')->nullable();
            // $table->enum('status', ['on-going', 'cancelled', 'completed'])->default('on-going');
            // $table->dateTime('actual_start_at')->nullable();
            // $table->dateTime('actual_end_at')->nullable();
            // $table->integer('total_duration_minutes')->default(30);
            // $table->decimal('total_amount', 10, 2)->default(0.00);
            // $table->decimal('paid_amount', 10, 2)->default(0.00);
            // $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending');
            // $table->text('cancellation_reason')->nullable();
            // $table->text('staff_notes')->nullable();
            // $table->json('metadata')->nullable(); // Additional flexible data
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_staff')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Critical indexes for appointment scheduling aligned with AppoinSched
            // $table->index(['office_id', 'booking_date', 'booking_time']); // Primary scheduling query
            // $table->index(['user_id', 'booking_date']);
            // $table->index(['status', 'booking_date']);
            $table->index([ 'office_id', 'status']); //'booking_date',
            $table->index('appointment_number');
            // $table->index(['assigned_staff', 'booking_date']);
            $table->index(['status', 'office_id']); // For AppoinSched status filtering by office
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
