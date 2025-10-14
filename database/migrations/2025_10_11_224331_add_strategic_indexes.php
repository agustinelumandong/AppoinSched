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
        // Add indexes to document_requests table
        Schema::table('document_requests', function (Blueprint $table) {
            $table->index(['status']);
            $table->index(['payment_status']);
            $table->index(['reference_number']);
            $table->index(['requested_date']);
            $table->index(['user_id', 'status']);
            $table->index(['office_id', 'status']);
            $table->index(['service_id', 'status']);
            $table->index(['staff_id', 'status']);
            $table->index(['created_at']);
        });

        // Add indexes to appointments table
        Schema::table('appointments', function (Blueprint $table) {
            $table->index(['status']);
            $table->index(['booking_date']);
            $table->index(['reference_number']);
            $table->index(['user_id', 'status']);
            $table->index(['office_id', 'status']);
            $table->index(['booking_date', 'booking_time']);
        });

        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->index(['email']);
            $table->index(['created_at']);
        });

        // Add indexes to personal_information table
        Schema::table('personal_information', function (Blueprint $table) {
            $table->index(['user_id']);
            $table->index(['date_of_birth']);
        });

        // Add indexes to user_families table
        Schema::table('user_families', function (Blueprint $table) {
            $table->index(['personal_information_id']);
        });

        // Add indexes to offices table
        Schema::table('offices', function (Blueprint $table) {
            $table->index(['slug']);
            $table->index(['name']);
        });

        // Add indexes to services table
        Schema::table('services', function (Blueprint $table) {
            $table->index(['office_id']);
            $table->index(['slug']);
            $table->index(['is_active']);
            $table->index(['office_id', 'is_active']);
        });

        // Add indexes to document_request_details table
        Schema::table('document_request_details', function (Blueprint $table) {
            $table->index(['document_request_id']);
            $table->index(['certificate_type']);
            $table->index(['request_for']);
            $table->index(['document_request_id', 'certificate_type']);
        });

        // Add indexes to birth_certificate_details table
        Schema::table('birth_certificate_details', function (Blueprint $table) {
            $table->index(['document_request_detail_id']);
        });

        // Add indexes to death_certificate_details table
        Schema::table('death_certificate_details', function (Blueprint $table) {
            $table->index(['document_request_detail_id']);
            $table->index(['death_date']);
        });

        // Add indexes to marriage_license_details table
        Schema::table('marriage_license_details', function (Blueprint $table) {
            $table->index(['document_request_detail_id']);
            $table->index(['marriage_date']);
        });

        // Add indexes to notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_type', 'notifiable_id']);
            $table->index(['read_at']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from document_requests table
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['reference_number']);
            $table->dropIndex(['requested_date']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['office_id', 'status']);
            $table->dropIndex(['service_id', 'status']);
            $table->dropIndex(['staff_id', 'status']);
            $table->dropIndex(['created_at']);
        });

        // Remove indexes from appointments table
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['booking_date']);
            $table->dropIndex(['reference_number']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['office_id', 'status']);
            $table->dropIndex(['booking_date', 'booking_time']);
        });

        // Remove indexes from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['created_at']);
        });

        // Remove indexes from personal_information table
        Schema::table('personal_information', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['date_of_birth']);
        });

        // Remove indexes from user_families table
        Schema::table('user_families', function (Blueprint $table) {
            $table->dropIndex(['personal_information_id']);
        });

        // Remove indexes from offices table
        Schema::table('offices', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['name']);
        });

        // Remove indexes from services table
        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['office_id']);
            $table->dropIndex(['slug']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['office_id', 'is_active']);
        });

        // Remove indexes from document_request_details table
        Schema::table('document_request_details', function (Blueprint $table) {
            $table->dropIndex(['document_request_id']);
            $table->dropIndex(['certificate_type']);
            $table->dropIndex(['request_for']);
            $table->dropIndex(['document_request_id', 'certificate_type']);
        });

        // Remove indexes from birth_certificate_details table
        Schema::table('birth_certificate_details', function (Blueprint $table) {
            $table->dropIndex(['document_request_detail_id']);
        });

        // Remove indexes from death_certificate_details table
        Schema::table('death_certificate_details', function (Blueprint $table) {
            $table->dropIndex(['document_request_detail_id']);
            $table->dropIndex(['death_date']);
        });

        // Remove indexes from marriage_license_details table
        Schema::table('marriage_license_details', function (Blueprint $table) {
            $table->dropIndex(['document_request_detail_id']);
            $table->dropIndex(['marriage_date']);
        });

        // Remove indexes from notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['notifiable_type', 'notifiable_id']);
            $table->dropIndex(['read_at']);
            $table->dropIndex(['created_at']);
        });
    }
};
