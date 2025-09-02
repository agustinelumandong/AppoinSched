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
        Schema::create('document_request_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_id')->constrained('document_requests')->onDelete('cascade');
            $table->dateTime('requested_date')->nullable();
            $table->dateTime('started_processing_date')->nullable();
            $table->dateTime('completed_date')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('processing_time_hours')->nullable(); // Auto-calculated
            $table->integer('total_documents')->default(0); // Count of documents in request
            $table->text('processing_notes')->nullable();
            $table->timestamps();

            // Analytics-specific indexes for reporting
            $table->unique('document_request_id'); // One analytics record per request
            $table->index(['requested_date', 'completed_date'], 'idx_req_comp_dates');
            $table->index(['processed_by', 'completed_date'], 'idx_processed_comp_date');
            $table->index(['created_by', 'requested_date'], 'idx_created_req_date');
            $table->index(['processing_time_hours', 'completed_date'], 'idx_proc_time_comp_date');
            $table->index(['total_documents', 'completed_date'], 'idx_total_docs_comp_date');
            $table->index(['requested_date', 'started_processing_date'], 'idx_req_start_dates'); // For pending time analysis
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_request_analytics');
    }
};
