<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, create a backup table to preserve existing data
        Schema::create('user_addresses_backup', function (Blueprint $table) {
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

            // Present Address (temporary)
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

        // Copy existing data to backup
        DB::statement('INSERT INTO user_addresses_backup SELECT * FROM user_addresses');

        // Drop the original table
        Schema::dropIfExists('user_addresses');

        // Create the new normalized table structure
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_information_id')->constrained('personal_information')->onDelete('cascade');
            $table->enum('address_type', ['Permanent', 'Temporary'])->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('barangay')->nullable();
            $table->string('street')->nullable();
            $table->string('zip_code')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            // Add indexes for performance
            $table->index(['personal_information_id', 'address_type']);
            $table->index(['address_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new table
        Schema::dropIfExists('user_addresses');

        // Restore from backup
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

            // Present Address
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

        // Copy data back from backup
        DB::statement('INSERT INTO user_addresses SELECT * FROM user_addresses_backup');

        // Drop backup table
        Schema::dropIfExists('user_addresses_backup');
    }
};
