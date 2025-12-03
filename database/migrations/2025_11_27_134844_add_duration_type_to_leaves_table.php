<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->enum('duration_type', ['full_day', 'half_day', 'multiple_days'])
                  ->default('full_day')
                  ->after('end_date');
            $table->time('half_day_time')->nullable()->after('duration_type');
        });
    }

    public function down()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn(['duration_type', 'half_day_time']);
        });
    }
};