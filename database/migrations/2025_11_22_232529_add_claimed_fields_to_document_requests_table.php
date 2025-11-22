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
        Schema::table('document_requests', function (Blueprint $table) {
            $table->enum('claimed_by', ['OWNER', 'AUTHORIZED PERSON'])->nullable()->after('completed_date');
            $table->timestamp('claimed_date_time')->nullable()->after('claimed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn(['claimed_by', 'claimed_date_time']);
        });
    }
};
