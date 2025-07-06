<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('allowances', 10, 2);
            $table->decimal('deductions', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('net_salary', 10, 2);
            $table->date('payment_date');
            $table->enum('payroll_status', ['pending', 'paid'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll');
    }
};