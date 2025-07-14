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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('office_id')->constrained('offices')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->string('to_whom')->nullable();
            $table->string('purpose')->nullable();
            $table->string('reference_number')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'canceled', 'in-progress', 'ready-for-pickup', 'cancelled'])->default('pending');
            $table->text('remarks')->nullable();
            $table->enum('payment_status', ['unpaid', 'processing', 'paid', 'failed'])->default('unpaid');
            $table->string('payment_reference')->nullable();
            $table->timestamp('requested_date')->nullable();
            $table->timestamp('completed_date')->nullable();
            $table->timestamps();
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
