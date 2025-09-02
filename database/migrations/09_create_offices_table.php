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
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('code', 20)->nullable()->unique();

            // Pinag iisipan pa kung ilalagay ba or hindi
            // $table->foreignId('address_id')->nullable()->constrained()->onDelete('set null');
            // $table->string('phone', 20)->nullable();
            // $table->string('email', 100)->nullable();
            // $table->string('head_of_office', 100)->nullable();
            // $table->time('opening_time')->default('08:00:00');
            // $table->time('closing_time')->default('17:00:00');
            // $table->json('operating_days')->nullable(); // [1,2,3,4,5] for Mon-Fri
            // $table->json('holiday_schedule')->nullable(); // Flexible holiday data
            // $table->integer('max_daily_appointments')->default(50);
            $table->boolean('is_active')->default(true);
            // $table->decimal('service_fee_multiplier', 5, 2)->default(1.00); // Office-specific pricing

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'name']);
            $table->index('slug');
            $table->index('code');
            // $table->index(['is_active', 'opening_time', 'closing_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
