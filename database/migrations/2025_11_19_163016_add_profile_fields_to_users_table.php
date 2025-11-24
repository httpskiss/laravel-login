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
        Schema::table('users', function (Blueprint $table) {
            // Profile completion tracking
            $table->boolean('profile_completed')->default(false)->after('settings');
            $table->timestamp('profile_completed_at')->nullable()->after('profile_completed');
            
            // Additional personal information
            $table->string('emergency_contact_name')->nullable()->after('address');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');
            
            // Professional information
            $table->string('position')->nullable()->after('department');
            $table->string('employee_type')->nullable()->after('position'); // full_time, part_time, contract
            $table->string('work_schedule')->nullable()->after('employee_type');
            
            // Communication preferences
            $table->boolean('email_notifications')->default(true)->after('settings');
            $table->boolean('push_notifications')->default(true)->after('email_notifications');
            $table->boolean('sms_notifications')->default(false)->after('push_notifications');
            
            // Privacy settings
            $table->enum('profile_visibility', ['public', 'private', 'contacts_only'])->default('private')->after('sms_notifications');
            $table->enum('email_visibility', ['public', 'private', 'contacts_only'])->default('private')->after('profile_visibility');
            
            // Indexes for better performance
            $table->index(['user_status', 'department']);
            $table->index(['profile_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_completed',
                'profile_completed_at',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relationship',
                'position',
                'employee_type',
                'work_schedule',
                'email_notifications',
                'push_notifications',
                'sms_notifications',
                'profile_visibility',
                'email_visibility'
            ]);
            
            $table->dropIndex(['user_status', 'department']);
            $table->dropIndex(['profile_completed']);
        });
    }
};