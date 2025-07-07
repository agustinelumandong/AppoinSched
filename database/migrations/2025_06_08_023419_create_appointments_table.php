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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('office_id')->constrained('offices')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->date('booking_date');
            $table->time('booking_time');
            $table->enum('status', ['pending', 'approved', 'cancelled', 'completed', 'no-show'])->default('pending');
            $table->text('notes')->nullable();
            $table->string('to_whom')->nullable()->after('notes');
            $table->string('purpose')->nullable()->after('to_whom');
            $table->string('client_first_name')->nullable()->after('purpose');
            $table->string('client_last_name')->nullable()->after('client_first_name');
            $table->string('client_middle_name')->nullable()->after('client_last_name');
            $table->string('client_email')->nullable()->after('client_last_name');
            $table->string('client_phone')->nullable()->after('client_email');
            $table->string('client_address')->nullable()->after('client_phone');
            $table->string('client_city')->nullable()->after('client_address');
            $table->string('client_state')->nullable()->after('client_city');
            $table->string('client_zip_code', 10)->nullable()->after('client_state');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
