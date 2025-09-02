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
        Schema::create('document_request_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_id')->constrained('document_requests')->onDelete('cascade');
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->string('payment_reference')->nullable();
            $table->string('payment_method')->nullable(); // cash, online, check, etc.
            $table->string('payment_proof_path')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->text('payment_notes')->nullable();
            $table->timestamps();

            // Payment-specific indexes
            $table->unique('document_request_id'); // One payment record per request
            $table->index(['payment_status', 'due_date']);
            $table->index(['payment_method', 'payment_date']);
            $table->index(['payment_status', 'payment_date']);
            $table->index('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_request_payments');
    }
};
