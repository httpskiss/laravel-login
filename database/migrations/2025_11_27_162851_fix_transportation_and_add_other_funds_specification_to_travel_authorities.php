<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('travel_authorities', function (Blueprint $table) {

            // Fix transportation enum (remove college_vehicle)
            if (Schema::hasColumn('travel_authorities', 'transportation')) {
                $table->enum('transportation', [
                    'university_vehicle',
                    'public_conveyance',
                    'private_vehicle'
                ])->nullable()->change();
            }

            // Ensure other_funds_specification exists
            if (!Schema::hasColumn('travel_authorities', 'other_funds_specification')) {
                $table->string('other_funds_specification')
                    ->nullable()
                    ->after('source_of_funds');
            }
        });
    }

    public function down()
    {
        Schema::table('travel_authorities', function (Blueprint $table) {

            // Restore old enum (if needed for rollback)
            $table->enum('transportation', [
                'college_vehicle',
                'public_conveyance',
            ])->nullable()->change();

            $table->dropColumn('other_funds_specification');
        });
    }
};
