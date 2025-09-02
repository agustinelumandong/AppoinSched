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
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number', 50)->unique(); // Human-readable identifier
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Who made the request
            $table->foreignId('office_id')->constrained('offices')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->enum('status', [
                'pending',
                'processing',
                'payment_required',
                'ready',
                'completed',
                'cancelled',
                'rejected',
                'on_hold'
            ])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->text('staff_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Core indexes for main request table
            $table->index(['office_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['service_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('request_number');
            $table->index(['office_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};
