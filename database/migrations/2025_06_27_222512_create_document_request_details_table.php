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

            // Personal Information for this specific request
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->enum('suffix', ['Jr.', 'Sr.'])->nullable();
            $table->string('email');
            $table->string('contact_no')->nullable();
            $table->string('contact_first_name')->nullable()->after('contact_no');
            $table->string('contact_last_name')->nullable()->after('contact_first_name');
            $table->string('contact_middle_name')->nullable()->after('contact_last_name');
            $table->string('contact_email')->nullable()->after('contact_middle_name');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->enum('sex_at_birth', ['Male', 'Female'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->enum('civil_status', ['Single', 'Married', 'Widowed', 'Divorced', 'Separated'])->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->nullable();

            // Address Information for this specific request
            $table->enum('address_type', ['Permanent', 'Temporary'])->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('barangay')->nullable();
            $table->string('street')->nullable();
            $table->string('zip_code')->nullable();

            // Family Information for this specific request
            $table->string('father_last_name')->nullable();
            $table->string('father_first_name')->nullable();
            $table->string('father_middle_name')->nullable();
            $table->enum('father_suffix', ['N/A', 'Jr.', 'Sr.'])->nullable();
            $table->date('father_birthdate')->nullable();
            $table->string('father_nationality')->nullable();
            $table->string('father_religion')->nullable();
            $table->string('father_contact_no')->nullable();

            $table->string('mother_last_name')->nullable();
            $table->string('mother_first_name')->nullable();
            $table->string('mother_middle_name')->nullable();
            $table->enum('mother_suffix', ['N/A', 'Jr.', 'Sr.'])->nullable();
            $table->date('mother_birthdate')->nullable();
            $table->string('mother_nationality')->nullable();
            $table->string('mother_religion')->nullable();
            $table->string('mother_contact_no')->nullable();

            $table->string('spouse_last_name')->nullable();
            $table->string('spouse_first_name')->nullable();
            $table->string('spouse_middle_name')->nullable();
            $table->enum('spouse_suffix', ['N/A', 'Jr.', 'Sr.'])->nullable();
            $table->date('spouse_birthdate')->nullable();
            $table->string('spouse_nationality')->nullable();
            $table->string('spouse_religion')->nullable();
            $table->string('spouse_contact_no')->nullable();

            // Death Certificate specific fields
            $table->string('deceased_last_name')->nullable();
            $table->string('deceased_first_name')->nullable();
            $table->string('deceased_middle_name')->nullable();
            $table->date('death_date')->nullable();
            $table->time('death_time')->nullable();
            $table->string('death_place')->nullable();
            $table->enum('relationship_to_deceased', [
                'Spouse',
                'Child',
                'Parent',
                'Sibling',
                'Grandchild',
                'Grandparent',
                'Other Relative',
                'Legal Representative',
                'Other'
            ])->nullable();

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
