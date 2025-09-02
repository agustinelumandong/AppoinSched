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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('address_id')->constrained()->onDelete('cascade');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('label', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'address_id']);

            $table->index(['user_id', 'is_primary']); 
            $table->index(['user_id', 'is_active']);  
            $table->index('address_id');              
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
