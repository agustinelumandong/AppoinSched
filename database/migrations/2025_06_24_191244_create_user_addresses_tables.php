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

        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_information_id')->constrained('personal_information');
            $table->enum('address_type', ['Permanent', 'Temporary'])->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('barangay')->nullable();
            $table->string('street')->nullable();
            $table->string('zip_code')->nullable();

            // Temporary Address
            $table->enum('temporary_address_type', ['Permanent', 'Temporary'])->default('Temporary')->nullable();
            $table->string('temporary_address_line_1')->nullable();
            $table->string('temporary_address_line_2')->nullable();
            $table->string('temporary_region')->nullable();
            $table->string('temporary_province')->nullable();
            $table->string('temporary_city')->nullable();
            $table->string('temporary_barangay')->nullable();
            $table->string('temporary_street')->nullable();
            $table->string('temporary_zip_code')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
