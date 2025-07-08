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

        Schema::create('personal_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Basic Information
            $table->enum('suffix', ['N/A', 'Jr.', 'Sr.'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->enum('sex_at_birth', ['Male', 'Female'])->nullable();
            $table->enum('civil_status', ['Single', 'Married', 'Widowed', 'Divorced', 'Separated'])->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('government_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_information');

    }
};
