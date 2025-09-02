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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('last_name', 50);
            $table->string('suffix', 10)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum(
                'gender',
                [
                    'male',
                    'female',
                    'other',
                    'prefer_not_to_say'
                ]
            )->nullable();
            $table->string('nationality', 50)->nullable();
            $table->string('civil_status', 20)->nullable();
            $table->string('government_id_type')->nullable();
            $table->string('government_id_image_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['first_name', 'last_name']);
            $table->index(['date_of_birth']);
            $table->index(['last_name', 'first_name']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};




