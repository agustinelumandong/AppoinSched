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
        Schema::table('user_families', function (Blueprint $table) {
            // Remove the redundant user_id foreign key
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // Add proper foreign key constraint with cascade delete
            $table->foreign('personal_information_id')
                ->references('id')
                ->on('personal_information')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_families', function (Blueprint $table) {
            // Remove the foreign key constraint
            $table->dropForeign(['personal_information_id']);

            // Add back the user_id column and foreign key
            $table->foreignId('user_id')->constrained('users');
        });
    }
};
