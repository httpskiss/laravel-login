<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('travel_authorities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('travel_authority_no')->nullable();
            $table->string('designation');
            $table->string('destination');
            $table->date('inclusive_date_of_travel');
            $table->text('purpose');
            $table->enum('transportation', ['college_vehicle', 'public_conveyance'])->nullable();
            $table->enum('estimated_expenses', ['official_time', 'with_expenses'])->default('official_time');
            $table->text('source_of_funds')->nullable();
            $table->enum('status', ['pending', 'recommending_approval', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('travel_authorities');
    }
};