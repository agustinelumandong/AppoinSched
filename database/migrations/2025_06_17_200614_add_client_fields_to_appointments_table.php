<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('to_whom')->nullable()->after('notes');
            $table->string('purpose')->nullable()->after('to_whom');
            $table->string('client_first_name')->nullable()->after('purpose');
            $table->string('client_last_name')->nullable()->after('client_first_name');
            $table->string('client_middle_name')->nullable()->after('client_last_name');
            $table->string('client_email')->nullable()->after('client_last_name');
            $table->string('client_middle_name')->nullable()->after('client_email');
            $table->string('client_phone')->nullable()->after('client_email');
            $table->string('client_address')->nullable()->after('client_phone');
            $table->string('client_city')->nullable()->after('client_address');
            $table->string('client_state')->nullable()->after('client_city');
            $table->string('client_zip_code', 10)->nullable()->after('client_state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'to_whom',
                'purpose',
                'client_first_name',
                'client_last_name',
                'client_middle_name',
                'client_email',
                'client_phone',
                'client_address',
                'client_city',
                'client_state',
                'client_zip_code',
            ]);
        });
    }
};
