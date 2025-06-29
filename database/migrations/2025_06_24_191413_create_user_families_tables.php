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
        Schema::create('user_families', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('personal_information_id')->constrained('personal_information');
            // Father's Information
            $table->string('father_last_name')->nullable();
            $table->string('father_first_name')->nullable();
            $table->string('father_middle_name')->nullable();
            $table->enum('father_suffix', ['N/A', 'Jr.', 'Sr.'])->nullable();
            $table->date('father_birthdate')->nullable();
            $table->string('father_nationality')->nullable();
            $table->string('father_religion')->nullable();
            $table->string('father_contact_no', 20)->nullable();

            // Mother's Information
            $table->string('mother_last_name')->nullable();
            $table->string('mother_first_name')->nullable();
            $table->string('mother_middle_name')->nullable();
            $table->enum('mother_suffix', ['N/A', 'Jr.', 'Sr.'])->nullable();
            $table->date('mother_birthdate')->nullable();
            $table->string('mother_nationality')->nullable();
            $table->string('mother_religion')->nullable();
            $table->string('mother_contact_no', 20)->nullable();

            // Spouse's Information (nullable if unmarried)
            $table->string('spouse_last_name')->nullable();
            $table->string('spouse_first_name')->nullable();
            $table->string('spouse_middle_name')->nullable();
            $table->enum('spouse_suffix', ['N/A', 'Jr.', 'Sr.'])->nullable();
            $table->date('spouse_birthdate')->nullable();
            $table->string('spouse_nationality')->nullable();
            $table->string('spouse_religion')->nullable();
            $table->string('spouse_contact_no', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_families');
        Schema::dropIfExists('personal_information');
        Schema::dropIfExists('user_addresses');
    }
};
