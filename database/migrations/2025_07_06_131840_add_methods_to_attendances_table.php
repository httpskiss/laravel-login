<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('method')->default('manual')->after('status');
            $table->string('ip_address')->nullable()->after('method');
            $table->string('device_info')->nullable()->after('ip_address');
            $table->text('location')->nullable()->after('device_info');
                });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['method', 'ip_address', 'device_info', 'location']);
        });
    }
};