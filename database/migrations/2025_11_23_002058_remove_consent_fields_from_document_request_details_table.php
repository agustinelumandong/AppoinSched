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
        Schema::table('document_request_details', function (Blueprint $table) {
            $table->dropColumn([
                'consent_person',
                'consent_relationship',
                'consent_citizenship',
                'consent_residence',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_request_details', function (Blueprint $table) {
            $table->string('consent_person', 100)->nullable()->default('N/A')->after('establishment_purpose');
            $table->string('consent_relationship', 50)->nullable()->default('N/A')->after('consent_person');
            $table->string('consent_citizenship', 100)->nullable()->default('N/A')->after('consent_relationship');
            $table->string('consent_residence', 150)->nullable()->default('N/A')->after('consent_citizenship');
        });
    }
};
