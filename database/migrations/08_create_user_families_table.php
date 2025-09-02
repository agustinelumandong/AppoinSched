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
        Schema::create('user_families', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // user auth
            $table->foreignId('contact_id')->constrained('contacts')->onDelete('cascade');
            $table->string('relationship', 50); // e.g. Father, Mother
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('suffix', 10)->nullable();
            $table->date('birthdate')->nullable();
            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();
            $table->string('contact_no', 20)->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'relationship']);

            $table->index(['user_id', 'relationship']);
            $table->index(['user_id', 'is_active']);
            // $table->index(['family_member_id', 'relationship']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_families');
    }
};
