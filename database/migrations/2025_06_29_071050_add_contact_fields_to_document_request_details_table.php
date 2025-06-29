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
        Schema::table('document_request_details', function (Blueprint $table) {
            $table->string('contact_first_name')->nullable()->after('contact_no');
            $table->string('contact_last_name')->nullable()->after('contact_first_name');
            $table->string('contact_middle_name')->nullable()->after('contact_last_name');
            $table->string('contact_email')->nullable()->after('contact_middle_name');
            $table->string('contact_phone')->nullable()->after('contact_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_request_details', function (Blueprint $table) {
            $table->dropColumn([
                'contact_first_name',
                'contact_last_name',
                'contact_middle_name',
                'contact_email',
                'contact_phone',
            ]);
        });
    }
};
