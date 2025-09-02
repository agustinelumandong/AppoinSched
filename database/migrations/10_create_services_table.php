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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category', 50)->nullable();
            $table->decimal('price', 10, 2)->default(0.00)->nullable();

            // $table->integer('estimated_duration_minutes')->default(30);
            // $table->integer('preparation_time_minutes')->default(5); // Time between appointments
            // $table->json('required_documents')->nullable(); // List of required documents
            // $table->json('prerequisites')->nullable(); // Conditions that must be met
            // $table->text('instructions')->nullable(); // What to bring, how to prepare
            // $table->boolean('requires_appointment')->default(true);
            // $table->boolean('is_online_available')->default(false);

            $table->boolean('is_active')->default(1);
            // $table->integer('priority')->default(1); // For sorting/grouping

            $table->timestamps();
            $table->softDeletes();

            $table->index(['office_id', 'is_active']);
            $table->index(['is_active', 'category']);
            // $table->index(['is_active', 'requires_appointment']);
            // $table->index(['category', 'is_active', 'priority']);
            $table->index('slug');
            $table->index(['price', 'is_active']);
            // $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
