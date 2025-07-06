<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::rename('payroll', 'payrolls');
    }

    public function down()
    {
        Schema::rename('payrolls', 'payroll');
    }
};