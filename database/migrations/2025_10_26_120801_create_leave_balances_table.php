<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('year');
            $table->decimal('vacation_earned', 5, 1)->default(0);
            $table->decimal('vacation_used', 5, 1)->default(0);
            $table->decimal('sick_earned', 5, 1)->default(0);
            $table->decimal('sick_used', 5, 1)->default(0);
            $table->decimal('emergency_earned', 5, 1)->default(0);
            $table->decimal('emergency_used', 5, 1)->default(0);
            $table->decimal('special_earned', 5, 1)->default(0);
            $table->decimal('special_used', 5, 1)->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_balances');
    }
};