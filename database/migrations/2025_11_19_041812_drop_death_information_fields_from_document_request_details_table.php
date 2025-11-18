<?php

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
        Schema::table('document_request_details', function (Blueprint $table) {
            // Drop Date and Place of Death fields
            $table->dropColumn(['death_date', 'death_time', 'death_place']);

            // Drop Relationship to Deceased
            $table->dropColumn('relationship_to_deceased');

            // Drop Additional Deceased Information
            $table->dropColumn([
                'deceased_sex',
                'deceased_religion',
                'deceased_age',
                'deceased_place_of_birth',
                'deceased_date_of_birth',
                'deceased_civil_status',
                'deceased_residence',
                'deceased_occupation',
            ]);

            // Drop Burial Details
            $table->dropColumn(['burial_cemetery_name', 'burial_cemetery_address']);

            // Drop Informant's Declaration
            $table->dropColumn([
                'informant_name',
                'informant_address',
                'informant_relationship',
                'informant_contact_no',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_request_details', function (Blueprint $table) {
            // Restore Date and Place of Death fields
            $table->date('death_date')->nullable();
            $table->time('death_time')->nullable();
            $table->string('death_place', 150)->nullable()->default('N/A');

            // Restore Relationship to Deceased
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

            // Restore Additional Deceased Information
            $table->enum('deceased_sex', ['Male', 'Female'])->nullable();
            $table->string('deceased_religion', 100)->nullable()->default('N/A');
            $table->unsignedTinyInteger('deceased_age')->nullable();
            $table->string('deceased_place_of_birth', 150)->nullable()->default('N/A');
            $table->date('deceased_date_of_birth')->nullable();
            $table->enum('deceased_civil_status', ['Single', 'Married', 'Widowed', 'Divorced', 'Separated'])->nullable();
            $table->string('deceased_residence', 255)->nullable()->default('N/A');
            $table->string('deceased_occupation', 100)->nullable()->default('N/A');

            // Restore Burial Details
            $table->string('burial_cemetery_name', 150)->nullable()->default('N/A');
            $table->string('burial_cemetery_address', 255)->nullable()->default('N/A');

            // Restore Informant's Declaration
            $table->string('informant_name', 100)->nullable()->default('N/A');
            $table->string('informant_address', 255)->nullable()->default('N/A');
            $table->string('informant_relationship', 100)->nullable()->default('N/A');
            $table->string('informant_contact_no', 50)->nullable()->default('N/A');
        });
    }
};
