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
            $table->foreignId('document_request_id')->constrained('document_requests')->onDelete('cascade');

            // Personal Information (compact text fields to reduce row size)
            $table->string('last_name', 100);
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->enum('suffix', ['N/A', 'Jr.', 'Sr.', 'I', 'II', 'III'])->nullable();
            $table->string('email', 150);
            $table->string('contact_no', 50)->nullable();
            $table->string('contact_first_name', 100)->nullable();
            $table->string('contact_last_name', 100)->nullable();
            $table->string('contact_middle_name', 100)->nullable();
            $table->string('contact_email', 150)->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->enum('sex_at_birth', ['Male', 'Female'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth', 150)->nullable();
            $table->enum('civil_status', ['Single', 'Married', 'Widowed', 'Divorced', 'Separated'])->nullable();
            $table->string('religion', 100)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('government_id_type', 100)->nullable();
            $table->string('government_id_image_path', 255)->nullable();

            // Address Information
            $table->enum('address_type', ['Permanent', 'Temporary'])->nullable();
            $table->string('address_line_1', 150)->nullable();
            $table->string('address_line_2', 150)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('barangay', 100)->nullable();
            $table->string('street', 100)->nullable();
            $table->string('zip_code', 20)->nullable();

            // Family Information
            $table->string('father_last_name', 100)->nullable();
            $table->string('father_first_name', 100)->nullable();
            $table->string('father_middle_name', 100)->nullable();
            $table->enum('father_suffix', ['N/A', 'Jr.', 'Sr.', 'I', 'II', 'III'])->nullable();
            $table->date('father_birthdate')->nullable();
            $table->string('father_nationality', 100)->nullable();
            $table->string('father_religion', 100)->nullable();
            $table->string('father_contact_no', 50)->nullable();

            $table->string('mother_last_name', 100)->nullable();
            $table->string('mother_first_name', 100)->nullable();
            $table->string('mother_middle_name', 100)->nullable();
            $table->enum('mother_suffix', ['N/A', 'Jr.', 'Sr.', 'I', 'II', 'III'])->nullable();
            $table->date('mother_birthdate')->nullable();
            $table->string('mother_nationality', 100)->nullable();
            $table->string('mother_religion', 100)->nullable();
            $table->string('mother_contact_no', 50)->nullable();

            // Death Certificate specific fields
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
            $table->date('death_date')->nullable();
            $table->time('death_time')->nullable();
            $table->string('death_place', 150)->nullable();
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
            // Informant's declaration
            $table->string('informant_name', 100)->nullable();
            $table->string('informant_address', 255)->nullable();
            $table->string('informant_relationship', 100)->nullable();
            $table->string('informant_contact_no', 50)->nullable();

            // Marriage License Modular Fields
            // Groom Personal Info
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
            $table->string('groom_civil_status', 50)->nullable();
            // Groom Father
            $table->string('groom_father_first_name', 100)->nullable();
            $table->string('groom_father_middle_name', 100)->nullable();
            $table->string('groom_father_last_name', 100)->nullable();
            $table->string('groom_father_suffix', 20)->nullable();
            $table->string('groom_father_citizenship', 100)->nullable();
            $table->string('groom_father_residence', 150)->nullable();
            // Groom Mother
            $table->string('groom_mother_first_name', 100)->nullable();
            $table->string('groom_mother_middle_name', 100)->nullable();
            $table->string('groom_mother_last_name', 100)->nullable();
            $table->string('groom_mother_citizenship', 100)->nullable();
            $table->string('groom_mother_residence', 150)->nullable();
            // Bride Personal Info
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
            // Bride Father
            $table->string('bride_father_first_name', 100)->nullable();
            $table->string('bride_father_middle_name', 100)->nullable();
            $table->string('bride_father_last_name', 100)->nullable();
            $table->string('bride_father_suffix', 20)->nullable();
            $table->string('bride_father_citizenship', 100)->nullable();
            $table->string('bride_father_residence', 150)->nullable();
            // Bride Mother
            $table->string('bride_mother_first_name', 100)->nullable();
            $table->string('bride_mother_middle_name', 100)->nullable();
            $table->string('bride_mother_last_name', 100)->nullable();
            $table->string('bride_mother_citizenship', 100)->nullable();
            $table->string('bride_mother_residence', 150)->nullable();
            // Consent Section
            $table->string('consent_person', 100)->nullable();
            $table->string('consent_relationship', 50)->nullable();
            $table->string('consent_citizenship', 100)->nullable();
            $table->string('consent_residence', 150)->nullable();

            // Indicates if this is for the requesting user or someone else
            $table->enum('request_for', ['myself', 'someone_else'])->default('myself');

            $table->timestamps();
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
