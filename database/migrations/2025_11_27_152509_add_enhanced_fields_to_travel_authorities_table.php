<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('travel_authorities', function (Blueprint $table) {

            if (!Schema::hasColumn('travel_authorities', 'travel_type')) {
                $table->enum('travel_type', [
                    'official_time',
                    'official_business', 
                    'personal_abroad',
                    'official_travel'
                ])->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('travel_authorities', 'duration_type')) {
                $table->enum('duration_type', [
                    'single_day',
                    'multiple_days'
                ])->default('single_day')->after('travel_type');
            }

            if (!Schema::hasColumn('travel_authorities', 'start_date')) {
                $table->date('start_date')->nullable()->after('inclusive_date_of_travel');
            }

            if (!Schema::hasColumn('travel_authorities', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }

            // transport: allow change only if column exists
            if (Schema::hasColumn('travel_authorities', 'transportation')) {
                $table->enum('transportation', [
                    'university_vehicle',
                    'public_conveyance',
                    'private_vehicle'
                ])->nullable()->change();
            }

            if (!Schema::hasColumn('travel_authorities', 'source_of_funds')) {
                $table->enum('source_of_funds', [
                    'mooe',
                    'personal',
                    'other'
                ])->nullable()->after('estimated_expenses');
            }

            if (!Schema::hasColumn('travel_authorities', 'other_funds_specification')) {
                $table->string('other_funds_specification')->nullable()->after('source_of_funds');
            }
        });
    }


    public function down()
    {
        Schema::table('travel_authorities', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn([
                'travel_type',
                'duration_type',
                'start_date',
                'end_date',
                'source_of_funds',
                'other_funds_specification'
            ]);
            
            // Revert transportation enum to original values
            $table->enum('transportation', [
                'college_vehicle',
                'public_conveyance'
            ])->nullable()->change();
            
            // Drop indexes
            $table->dropIndex(['travel_type', 'status']);
            $table->dropIndex(['duration_type']);
            $table->dropIndex(['start_date', 'end_date']);
        });
    }
};