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
        // Standardize document_requests status enum (remove duplicate 'cancelled')
        Schema::table('document_requests', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'completed',
                'cancelled',
                'in-progress',
                'ready-for-pickup'
            ])->default('pending')->change();
        });

        // Standardize suffix enums across all tables
        $suffixEnum = ['N/A', 'Jr.', 'Sr.', 'I', 'II', 'III'];

        // Update personal_information table
        Schema::table('personal_information', function (Blueprint $table) use ($suffixEnum) {
            $table->enum('suffix', $suffixEnum)->nullable()->change();
        });

        // Update user_families table
        Schema::table('user_families', function (Blueprint $table) use ($suffixEnum) {
            $table->enum('father_suffix', $suffixEnum)->nullable()->change();
            $table->enum('mother_suffix', $suffixEnum)->nullable()->change();
        });

        // Update document_request_details table
        Schema::table('document_request_details', function (Blueprint $table) use ($suffixEnum) {
            $table->enum('suffix', $suffixEnum)->nullable()->change();
            $table->enum('father_suffix', $suffixEnum)->nullable()->change();
            $table->enum('mother_suffix', $suffixEnum)->nullable()->change();
        });

        // Standardize civil_status enum across all tables
        $civilStatusEnum = ['Single', 'Married', 'Widowed', 'Divorced', 'Separated'];

        // Update personal_information table
        Schema::table('personal_information', function (Blueprint $table) use ($civilStatusEnum) {
            $table->enum('civil_status', $civilStatusEnum)->nullable()->change();
        });

        // Update document_request_details table
        Schema::table('document_request_details', function (Blueprint $table) use ($civilStatusEnum) {
            $table->enum('civil_status', $civilStatusEnum)->nullable()->change();
        });

        // Update death_certificate_details table
        Schema::table('death_certificate_details', function (Blueprint $table) use ($civilStatusEnum) {
            $table->enum('deceased_civil_status', $civilStatusEnum)->nullable()->change();
        });

        // Update marriage_license_details table
        Schema::table('marriage_license_details', function (Blueprint $table) use ($civilStatusEnum) {
            $table->enum('groom_civil_status', $civilStatusEnum)->nullable()->change();
        });

        // Standardize sex enum across all tables
        $sexEnum = ['Male', 'Female'];

        // Update personal_information table
        Schema::table('personal_information', function (Blueprint $table) use ($sexEnum) {
            $table->enum('sex_at_birth', $sexEnum)->nullable()->change();
        });

        // Update document_request_details table
        Schema::table('document_request_details', function (Blueprint $table) use ($sexEnum) {
            $table->enum('sex_at_birth', $sexEnum)->nullable()->change();
        });

        // Update death_certificate_details table
        Schema::table('death_certificate_details', function (Blueprint $table) use ($sexEnum) {
            $table->enum('deceased_sex', $sexEnum)->nullable()->change();
        });

        // Update marriage_license_details table
        Schema::table('marriage_license_details', function (Blueprint $table) use ($sexEnum) {
            $table->enum('groom_sex', $sexEnum)->nullable()->change();
            $table->enum('bride_sex', $sexEnum)->nullable()->change();
        });

        // Standardize address_type enum
        $addressTypeEnum = ['Permanent', 'Temporary'];

        // Update user_addresses table
        Schema::table('user_addresses', function (Blueprint $table) use ($addressTypeEnum) {
            $table->enum('address_type', $addressTypeEnum)->nullable()->change();
        });

        // Update document_request_details table
        Schema::table('document_request_details', function (Blueprint $table) use ($addressTypeEnum) {
            $table->enum('address_type', $addressTypeEnum)->nullable()->change();
        });

        // Standardize request_for enum
        Schema::table('document_request_details', function (Blueprint $table) {
            $table->enum('request_for', ['myself', 'someone_else'])->default('myself')->change();
        });

        // Standardize certificate_type enum
        Schema::table('document_request_details', function (Blueprint $table) {
            $table->enum('certificate_type', ['birth', 'death', 'marriage'])->nullable()->change();
        });

        // Standardize relationship_to_deceased enum
        Schema::table('death_certificate_details', function (Blueprint $table) {
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
            ])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not easily reversible as it changes enum values
        // The down method would need to be implemented based on the original enum values
        // For safety, we'll leave this empty and recommend manual rollback if needed
    }
};
