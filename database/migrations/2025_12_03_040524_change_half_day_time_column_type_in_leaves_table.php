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
        Schema::table('leaves', function (Blueprint $table) {
            // Change half_day_time from TIME to STRING to store values like 'morning', 'afternoon', 'custom'
            $table->string('half_day_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Revert back to TIME type if migration is rolled back
            $table->time('half_day_time')->nullable()->change();
        });
    }
};
