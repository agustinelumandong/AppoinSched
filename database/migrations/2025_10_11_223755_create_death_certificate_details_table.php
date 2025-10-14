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
        Schema::create('death_certificate_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_detail_id')->constrained('document_request_details')->onDelete('cascade');

            // Deceased person information
            $table->string('deceased_last_name', 100)->nullable();
            $table->string('deceased_first_name', 100)->nullable();
            $table->string('deceased_middle_name', 100)->nullable();
            $table->enum('deceased_sex', ['Male', 'Female'])->nullable();
            $table->string('deceased_religion', 100)->nullable();
            $table->unsignedTinyInteger('deceased_age')->nullable();
            $table->string('deceased_place_of_birth', 150)->nullable();
            $table->date('deceased_date_of_birth')->nullable();
            $table->enum('deceased_civil_status', ['Single', 'Married', 'Widowed', 'Divorced', 'Separated'])->nullable();
            $table->string('deceased_residence', 255)->nullable();
            $table->string('deceased_occupation', 100)->nullable();

            // Death information
            $table->date('death_date')->nullable();
            $table->time('death_time')->nullable();
            $table->string('death_place', 150)->nullable();
            $table->string('cause_of_death', 255)->nullable();
            $table->string('death_certificate_no', 50)->nullable();

            // Relationship to deceased
            $table->enum('relationship_to_deceased', [
                'Spouse',
                'Child',
                'Parent',
                'Sibling',
                'Grandchild',
                'Grandparent',
                'Other Relative',
                'Legal Representative',
                'Other',
                'N/A'
            ])->nullable();

            // Deceased's parents
            $table->string('deceased_father_last_name', 100)->nullable();
            $table->string('deceased_father_first_name', 100)->nullable();
            $table->string('deceased_father_middle_name', 100)->nullable();
            $table->string('deceased_mother_last_name', 100)->nullable();
            $table->string('deceased_mother_first_name', 100)->nullable();
            $table->string('deceased_mother_middle_name', 100)->nullable();

            // Burial details
            $table->string('burial_cemetery_name', 150)->nullable();
            $table->string('burial_cemetery_address', 255)->nullable();
            $table->date('burial_date')->nullable();
            $table->time('burial_time')->nullable();

            // Informant's declaration
            $table->string('informant_name', 100)->nullable();
            $table->string('informant_address', 255)->nullable();
            $table->string('informant_relationship', 100)->nullable();
            $table->string('informant_contact_no', 50)->nullable();
            $table->string('informant_id_type', 50)->nullable();
            $table->string('informant_id_number', 50)->nullable();

            // Medical certification
            $table->string('attending_physician', 100)->nullable();
            $table->string('physician_license_no', 50)->nullable();
            $table->string('physician_address', 255)->nullable();
            $table->date('certification_date')->nullable();

            $table->timestamps();

            // Add indexes for performance
            $table->index(['document_request_detail_id']);
            $table->index(['death_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('death_certificate_details');
    }
};
