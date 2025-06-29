<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('document_request_details', function (Blueprint $table) {
            // Death Certificate specific fields
            $table->string('deceased_last_name')->nullable();
            $table->string('deceased_first_name')->nullable();
            $table->string('deceased_middle_name')->nullable();
            $table->date('death_date')->nullable();
            $table->time('death_time')->nullable();
            $table->string('death_place')->nullable();
            $table->enum('relationship_to_deceased', [
                'Spouse',
                'Child',
                'Parent',
                'Sibling',
                'Grandchild',
                'Grandparent',
                'Other Relative',
                'Legal Representative',
                'Other'
            ])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_request_details', function (Blueprint $table) {
            $table->dropColumn([
                'deceased_last_name',
                'deceased_first_name',
                'deceased_middle_name',
                'death_date',
                'death_time',
                'death_place',
                'relationship_to_deceased'
            ]);
        });
    }
};
