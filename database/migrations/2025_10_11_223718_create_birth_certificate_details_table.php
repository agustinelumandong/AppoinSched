<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('birth_certificate_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_detail_id')->constrained('document_request_details')->onDelete('cascade');

            // Birth-specific information
            $table->string('hospital_name', 150)->nullable();
            $table->string('hospital_address', 255)->nullable();
            $table->string('attendant_name', 100)->nullable();
            $table->string('attendant_qualification', 100)->nullable();
            $table->string('attendant_license_no', 50)->nullable();
            $table->string('attendant_address', 255)->nullable();

            // Birth weight and measurements
            $table->decimal('birth_weight', 5, 2)->nullable(); // in kg
            $table->decimal('birth_length', 5, 2)->nullable(); // in cm

            // Birth order information
            $table->integer('birth_order')->nullable();
            $table->integer('total_children')->nullable();

            // Additional birth information
            $table->string('birth_place_type', 50)->nullable(); // Hospital, Home, Clinic, etc.
            $table->text('birth_notes')->nullable();

            $table->timestamps();

            // Add indexes for performance
            $table->index(['document_request_detail_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birth_certificate_details');
    }
};
