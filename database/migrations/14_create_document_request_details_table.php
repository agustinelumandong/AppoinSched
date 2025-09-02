<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->enum('status', [
                'pending',
                'processing',
                'ready',
                'completed',
                'cancelled',
                'rejected'
            ])->default('pending');

            $table->string('to_whom')->nullable();
            $table->text('purpose')->nullable();
            $table->text('special_instruction')->nullable();

            $table->json('form_data')->nullable();
            $table->json('supporting_documents')->nullable(); // List of submitted supporting docs
            $table->text('staff_notes')->nullable();
            $table->text('rejection_instructions')->nullable();

            $table->dateTime('expected_completion_date')->nullable();
            $table->dateTime('actual_completion_date')->nullable();
            $table->timestamps();

            // Indexes for document detail management
            $table->index(['document_request_id', 'status']);
            $table->index(['status', 'expected_completion_date']);
            $table->index(['document_request_id']);
            $table->index(['to_whom', 'document_request_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_request_details');
    }
};
