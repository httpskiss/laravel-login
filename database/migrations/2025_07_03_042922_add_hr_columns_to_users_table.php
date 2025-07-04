<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new columns
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');
            $table->string('employee_id')->unique()->after('last_name');
            $table->string('department')->after('employee_id');
            $table->string('profile_photo_path', 2048)->nullable()->after('department');
            $table->enum('user_status', ['Active', 'On Leave', 'Suspended', 'Terminated'])
                  ->default('Active')
                  ->after('profile_photo_path');
            $table->date('hire_date')->nullable()->after('user_status');
            $table->string('phone', 20)->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable()->after('address');
            $table->date('dob')->nullable()->after('gender');
            
            // Remove old column
            $table->dropColumn('name');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Reverse the changes
            $table->string('name')->after('id');
            
            $table->dropColumn([
                'first_name',
                'last_name',
                'employee_id',
                'department',
                'profile_photo_path',
                'user_status',
                'hire_date',
                'phone',
                'address',
                'gender',
                'dob'
            ]);
        });
    }
};