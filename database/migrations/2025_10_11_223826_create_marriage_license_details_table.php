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
        Schema::create('marriage_license_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_detail_id')->constrained('document_request_details')->onDelete('cascade');

            // Marriage ceremony information
            $table->date('marriage_date')->nullable();
            $table->time('marriage_time')->nullable();
            $table->string('marriage_place', 150)->nullable();
            $table->string('marriage_venue', 150)->nullable();
            $table->string('marriage_venue_address', 255)->nullable();

            // Groom information
            $table->string('groom_first_name', 100)->nullable();
            $table->string('groom_middle_name', 100)->nullable();
            $table->string('groom_last_name', 100)->nullable();
            $table->string('groom_suffix', 20)->nullable();
            $table->unsignedTinyInteger('groom_age')->nullable();
            $table->date('groom_date_of_birth')->nullable();
            $table->string('groom_place_of_birth', 150)->nullable();
            $table->enum('groom_sex', ['Male', 'Female'])->nullable();
            $table->string('groom_citizenship', 100)->nullable();
            $table->string('groom_residence', 150)->nullable();
            $table->string('groom_religion', 100)->nullable();
            $table->enum('groom_civil_status', ['Single', 'Married', 'Widowed', 'Divorced', 'Separated'])->nullable();

            // Groom's parents
            $table->string('groom_father_first_name', 100)->nullable();
            $table->string('groom_father_middle_name', 100)->nullable();
            $table->string('groom_father_last_name', 100)->nullable();
            $table->string('groom_father_suffix', 20)->nullable();
            $table->string('groom_father_citizenship', 100)->nullable();
            $table->string('groom_father_residence', 150)->nullable();

            $table->string('groom_mother_first_name', 100)->nullable();
            $table->string('groom_mother_middle_name', 100)->nullable();
            $table->string('groom_mother_last_name', 100)->nullable();
            $table->string('groom_mother_citizenship', 100)->nullable();
            $table->string('groom_mother_residence', 150)->nullable();

            // Bride information
            $table->string('bride_first_name', 100)->nullable();
            $table->string('bride_middle_name', 100)->nullable();
            $table->string('bride_last_name', 100)->nullable();
            $table->string('bride_suffix', 20)->nullable();
            $table->unsignedTinyInteger('bride_age')->nullable();
            $table->date('bride_date_of_birth')->nullable();
            $table->string('bride_place_of_birth', 150)->nullable();
            $table->enum('bride_sex', ['Male', 'Female'])->nullable();
            $table->string('bride_citizenship', 100)->nullable();
            $table->string('bride_residence', 150)->nullable();
            $table->string('bride_religion', 100)->nullable();
            $table->string('bride_civil_status', 50)->nullable();

            // Bride's parents
            $table->string('bride_father_first_name', 100)->nullable();
            $table->string('bride_father_middle_name', 100)->nullable();
            $table->string('bride_father_last_name', 100)->nullable();
            $table->string('bride_father_suffix', 20)->nullable();
            $table->string('bride_father_citizenship', 100)->nullable();
            $table->string('bride_father_residence', 150)->nullable();

            $table->string('bride_mother_first_name', 100)->nullable();
            $table->string('bride_mother_middle_name', 100)->nullable();
            $table->string('bride_mother_last_name', 100)->nullable();
            $table->string('bride_mother_citizenship', 100)->nullable();
            $table->string('bride_mother_residence', 150)->nullable();

            // Special permit information (for minors)
            $table->string('establishment_name', 100)->nullable();
            $table->string('establishment_address', 150)->nullable();
            $table->string('establishment_purpose', 150)->nullable();

            // Consent information (for minors)
            $table->string('consent_person', 100)->nullable();
            $table->string('consent_relationship', 50)->nullable();
            $table->string('consent_citizenship', 100)->nullable();
            $table->string('consent_residence', 150)->nullable();

            // Marriage license information
            $table->string('license_number', 50)->nullable();
            $table->date('license_issued_date')->nullable();
            $table->string('license_issued_place', 150)->nullable();

            // Witnesses
            $table->string('witness1_name', 100)->nullable();
            $table->string('witness1_address', 255)->nullable();
            $table->string('witness2_name', 100)->nullable();
            $table->string('witness2_address', 255)->nullable();

            // Officiant information
            $table->string('officiant_name', 100)->nullable();
            $table->string('officiant_title', 100)->nullable();
            $table->string('officiant_license_no', 50)->nullable();
            $table->string('officiant_address', 255)->nullable();

            $table->timestamps();

            // Add indexes for performance
            $table->index(['document_request_detail_id']);
            $table->index(['marriage_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marriage_license_details');
    }
};
