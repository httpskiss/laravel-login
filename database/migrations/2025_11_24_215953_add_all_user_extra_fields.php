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

            /**
             * 1. PERSONAL INFORMATION
             */
            $table->string('middle_name')->nullable()->after('first_name');
            $table->boolean('is_pwd')->default(false)->after('last_name');

            // Gender identity
            $table->enum('gender', [
                'Male',
                'Female',
                'Non-Binary',
                'Genderqueer',
                'Genderfluid',
                'Agender',
                'Bigender',
                'Two-Spirit',
                'Cisgender Male',
                'Cisgender Female',
                'Transgender Male',
                'Transgender Female',
                'Transmasculine',
                'Transfeminine',
                'Androgynous',
                'Demiboy',
                'Demigirl',
                'Neutrois',
                'Pangender',
                'Gender Nonconforming',
                'Questioning',
                'Prefer not to say',
                'Other'
            ])->nullable()->after('is_pwd');

            $table->string('gender_other')->nullable()->after('gender');
            $table->enum('civil_status', ['Single', 'Married', 'Divorced', 'Widowed', 'Separated'])->nullable()->after('gender_other');
            $table->enum('sex', ['Male', 'Female'])->nullable()->after('civil_status');

            /**
             * 2. CONTACT & EMERGENCY INFO
             */
            $table->string('emergency_contact_name')->nullable()->after('address');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');

            /**
             * 3. PROFESSIONAL INFORMATION
             */
            $table->string('position')->nullable()->after('department');

            $table->enum('employee_type', [
                'full_time',
                'part_time',
                'contract'
            ])->nullable()->after('position');

            $table->enum('employment_type', [
                'Permanent Employee',
                'Non-Permanent Employee',
                'Contract of Service',
                'Part-Time'
            ])->nullable()->after('employee_type');

            $table->enum('employee_category', [
                'Teaching',
                'Non-Teaching',
                'Teaching/Non-Teaching'
            ])->nullable()->after('employment_type');

            $table->string('designation')->nullable()->after('position');
            $table->string('work_schedule')->nullable()->after('employee_type');

            /**
             * 4. EDUCATIONAL INFORMATION
             */
            $table->string('program')->nullable()->after('department');
            $table->enum('highest_educational_attainment', [
                'Elementary',
                'High School',
                'Vocational',
                'Associate Degree',
                'Bachelor Degree',
                'Master Degree',
                'Doctorate',
                'Post-Doctorate'
            ])->nullable()->after('program');

            /**
             * 5. COMMUNICATION PREFERENCES
             */
            $table->boolean('email_notifications')->default(true)->after('settings');
            $table->boolean('push_notifications')->default(true)->after('email_notifications');
            $table->boolean('sms_notifications')->default(false)->after('push_notifications');

            /**
             * 6. PRIVACY SETTINGS
             */
            $table->enum('profile_visibility', ['public', 'private', 'contacts_only'])->default('private')->after('sms_notifications');
            $table->enum('email_visibility', ['public', 'private', 'contacts_only'])->default('private')->after('profile_visibility');

            /**
             * 7. PROFILE COMPLETION
             */
            $table->boolean('profile_completed')->default(false)->after('settings');
            $table->timestamp('profile_completed_at')->nullable()->after('profile_completed');

            /**
             * 8. INDEXES
             */
            $table->index(['user_status', 'department']);
            $table->index(['profile_completed']);
            $table->index(['employment_type', 'employee_category']);
            $table->index(['department', 'program']);
            $table->index(['civil_status']);
            $table->index(['gender']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Drop all added columns
            $table->dropColumn([
                'middle_name',
                'is_pwd',
                'gender',
                'gender_other',
                'civil_status',
                'sex',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relationship',
                'position',
                'employee_type',
                'employment_type',
                'employee_category',
                'designation',
                'work_schedule',
                'program',
                'highest_educational_attainment',
                'email_notifications',
                'push_notifications',
                'sms_notifications',
                'profile_visibility',
                'email_visibility',
                'profile_completed',
                'profile_completed_at',
            ]);
        });

        // Drop indexes
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['user_status', 'department']);
            $table->dropIndex(['profile_completed']);
            $table->dropIndex(['employment_type', 'employee_category']);
            $table->dropIndex(['department', 'program']);
            $table->dropIndex(['civil_status']);
            $table->dropIndex(['gender']);
        });
    }
};
