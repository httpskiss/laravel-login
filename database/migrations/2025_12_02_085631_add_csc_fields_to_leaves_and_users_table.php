// 2025_12_02_000000_add_csc_fields_to_leaves_and_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Employee classification for CSC rules
            $table->enum('employee_classification', [
                'regular', 
                'teacher', 
                'part_time', 
                'contractual',
                'local_elective',
                'judicial',
                'executive',
                'faculty'
            ])->default('regular')->after('department');
            
            // Employment details
            $table->enum('employment_status', [
                'permanent',
                'temporary',
                'casual',
                'coterminous'
            ])->default('permanent')->after('employee_classification');
            
            $table->decimal('work_hours_per_week', 5, 2)->default(40.00)->after('employment_status');
            $table->decimal('work_hours_per_day', 5, 2)->default(8.00)->after('work_hours_per_week');
            
            // For teachers specifically
            $table->boolean('is_teacher')->default(false)->after('work_hours_per_day');
            $table->decimal('vacation_service_credits', 8, 3)->default(0)->after('is_teacher');
            
            // For maternity/paternity
            $table->enum('marital_status', ['single', 'married', 'widowed', 'separated'])->nullable()->after('vacation_service_credits');
            $table->integer('delivery_count')->default(0)->after('marital_status'); // For paternity leave tracking
            $table->date('last_delivery_date')->nullable()->after('delivery_count');
            
            // For leave computation
            $table->date('last_leave_computation_date')->nullable()->after('last_delivery_date');
        });

        Schema::table('leaves', function (Blueprint $table) {
            // ========== CSC-SPECIFIC FIELDS ==========
            
            // 1. Employee Type Classification
            if (!Schema::hasColumn('leaves', 'csc_employee_type')) {
                $table->enum('csc_employee_type', [
                    'regular', 
                    'teacher', 
                    'part_time', 
                    'contractual',
                    'local_elective',
                    'judicial',
                    'executive',
                    'faculty'
                ])->nullable()->after('user_id');
            }
            
            // 2. Leave Basis
            if (!Schema::hasColumn('leaves', 'leave_basis')) {
                $table->enum('leave_basis', [
                    'standard_vl_sl',      // Regular employees
                    'teacher_pvp',         // Teachers (Proportional Vacation Pay)
                    'special_law',         // Those covered by special laws
                    'part_time_proportional'
                ])->nullable()->after('csc_employee_type');
            }
            
            // 3. For Teachers Only
            if (!Schema::hasColumn('leaves', 'is_vacation_service')) {
                $table->boolean('is_vacation_service')->default(false)->after('leave_basis');
            }
            
            if (!Schema::hasColumn('leaves', 'service_credits_used')) {
                $table->decimal('service_credits_used', 8, 4)->default(0)->after('is_vacation_service');
            }
            
            // 4. Detailed Time Tracking (for partial days)
            if (!Schema::hasColumn('leaves', 'start_time')) {
                $table->time('start_time')->nullable()->after('end_date');
            }
            
            if (!Schema::hasColumn('leaves', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
            
            if (!Schema::hasColumn('leaves', 'total_hours')) {
                $table->decimal('total_hours', 6, 2)->nullable()->after('end_time');
            }
            
            if (!Schema::hasColumn('leaves', 'equivalent_days_csc')) {
                $table->decimal('equivalent_days_csc', 8, 4)->nullable()->after('total_hours');
            }
            
            // 5. For Maternity/Paternity Leave
            if (!Schema::hasColumn('leaves', 'maternity_delivery_date')) {
                $table->date('maternity_delivery_date')->nullable()->after('type');
            }
            
            if (!Schema::hasColumn('leaves', 'paternity_delivery_count')) {
                $table->integer('paternity_delivery_count')->nullable()->after('maternity_delivery_date');
            }
            
            if (!Schema::hasColumn('leaves', 'is_miscarriage')) {
                $table->boolean('is_miscarriage')->default(false)->after('paternity_delivery_count');
            }
            
            // 6. For Special Leave Privileges (SLP)
            if (!Schema::hasColumn('leaves', 'slp_type')) {
                $table->enum('slp_type', [
                    'funeral_mourning',
                    'graduation',
                    'enrollment',
                    'wedding_anniversary',
                    'birthday',
                    'hospitalization',
                    'accident',
                    'relocation',
                    'government_transaction',
                    'calamity',
                    'none'
                ])->default('none')->after('is_miscarriage');
            }
            
            // 7. For Leave Without Pay (LWOP) Computation
            if (!Schema::hasColumn('leaves', 'is_lwop')) {
                $table->boolean('is_lwop')->default(false)->after('slp_type');
            }
            
            if (!Schema::hasColumn('leaves', 'lwop_deduction_rate')) {
                $table->decimal('lwop_deduction_rate', 5, 4)->nullable()->after('is_lwop');
            }
            
            if (!Schema::hasColumn('leaves', 'lwop_days_charged')) {
                $table->decimal('lwop_days_charged', 8, 4)->nullable()->after('lwop_deduction_rate');
            }
            
            // 8. For Monetization/Commutation
            if (!Schema::hasColumn('leaves', 'is_monetized')) {
                $table->boolean('is_monetized')->default(false)->after('lwop_days_charged');
            }
            
            if (!Schema::hasColumn('leaves', 'monetized_days')) {
                $table->decimal('monetized_days', 8, 4)->nullable()->after('is_monetized');
            }
            
            if (!Schema::hasColumn('leaves', 'monetization_amount')) {
                $table->decimal('monetization_amount', 12, 2)->nullable()->after('monetized_days');
            }
            
            // 9. For Forced/Mandatory Leave
            if (!Schema::hasColumn('leaves', 'is_forced_leave')) {
                $table->boolean('is_forced_leave')->default(false)->after('monetization_amount');
            }
            
            // 10. For Terminal Leave
            if (!Schema::hasColumn('leaves', 'is_terminal_leave')) {
                $table->boolean('is_terminal_leave')->default(false)->after('is_forced_leave');
            }
            
            if (!Schema::hasColumn('leaves', 'separation_type')) {
                $table->enum('separation_type', [
                    'retirement',
                    'voluntary_resignation',
                    'separation_no_fault',
                    'none'
                ])->default('none')->after('is_terminal_leave');
            }
            
            // 11. Computation Details
            if (!Schema::hasColumn('leaves', 'computation_method')) {
                $table->string('computation_method')->nullable()->after('separation_type');
            }
            
            if (!Schema::hasColumn('leaves', 'computation_notes')) {
                $table->text('computation_notes')->nullable()->after('computation_method');
            }
            
            // 12. Medical Certificate Details
            if (!Schema::hasColumn('leaves', 'medical_certificate_issued_date')) {
                $table->date('medical_certificate_issued_date')->nullable()->after('medical_certificate_path');
            }
            
            if (!Schema::hasColumn('leaves', 'medical_certificate_validity_days')) {
                $table->integer('medical_certificate_validity_days')->nullable()->after('medical_certificate_issued_date');
            }
            
            if (!Schema::hasColumn('leaves', 'is_fit_to_work')) {
                $table->boolean('is_fit_to_work')->default(true)->after('medical_certificate_validity_days');
            }
            
            // 13. Actual Service Computation
            if (!Schema::hasColumn('leaves', 'actual_service_days')) {
                $table->decimal('actual_service_days', 8, 4)->nullable()->after('is_fit_to_work');
            }
            
            if (!Schema::hasColumn('leaves', 'included_in_service')) {
                $table->boolean('included_in_service')->default(true)->after('actual_service_days');
            }
            
            // 14. New Indexes for Performance
            $table->index(['csc_employee_type', 'type', 'status'], 'leaves_csc_type_status_index');
            $table->index(['user_id', 'is_lwop'], 'leaves_user_lwop_index');
            $table->index(['user_id', 'is_monetized'], 'leaves_user_monetized_index');
            $table->index(['user_id', 'is_terminal_leave'], 'leaves_user_terminal_index');
        });

        // Create leave_balances table for tracking
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('as_of_date');
            
            // Regular Leave Credits
            $table->decimal('vacation_leave', 10, 4)->default(0);
            $table->decimal('sick_leave', 10, 4)->default(0);
            
            // For Teachers
            $table->decimal('vacation_service_credits', 10, 4)->default(0);
            $table->decimal('proportional_vacation_pay', 10, 4)->default(0);
            
            // Special Leave Privileges
            $table->decimal('special_leave_privileges', 5, 2)->default(3.00); // Max 3 days per year
            
            // Maternity/Paternity
            $table->decimal('maternity_leave', 5, 2)->default(0); // 60 days max
            $table->integer('paternity_leave_count')->default(0); // Max 4 deliveries
            $table->decimal('paternity_leave_days', 5, 2)->default(7.00); // 7 days per delivery
            
            // Forced Leave Tracking
            $table->decimal('forced_leave_taken', 5, 2)->default(0); // Min 5 days per year
            
            // Monetization Tracking
            $table->decimal('monetized_this_year', 10, 4)->default(0); // Max 30 days per year
            
            // Audit Fields
            $table->timestamp('last_computed_at')->nullable();
            $table->text('computation_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->unique(['user_id', 'as_of_date']);
            $table->index(['user_id', 'as_of_date']);
        });

        // Create leave_credit_earnings table for detailed tracking
        Schema::create('leave_credit_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('earning_date');
            $table->enum('credit_type', [
                'vacation_leave',
                'sick_leave', 
                'vacation_service',
                'maternity',
                'paternity',
                'special_privilege',
                'forced_leave'
            ]);
            $table->decimal('days_earned', 10, 4);
            $table->decimal('rate_per_day', 10, 4)->nullable();
            $table->text('description');
            $table->json('computation_details')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'earning_date']);
            $table->index(['user_id', 'credit_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_credit_earnings');
        Schema::dropIfExists('leave_balances');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'employee_classification',
                'employment_status',
                'work_hours_per_week',
                'work_hours_per_day',
                'is_teacher',
                'vacation_service_credits',
                'marital_status',
                'delivery_count',
                'last_delivery_date',
                'last_leave_computation_date'
            ]);
        });
        
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn([
                'csc_employee_type',
                'leave_basis',
                'is_vacation_service',
                'service_credits_used',
                'start_time',
                'end_time',
                'total_hours',
                'equivalent_days_csc',
                'maternity_delivery_date',
                'paternity_delivery_count',
                'is_miscarriage',
                'slp_type',
                'is_lwop',
                'lwop_deduction_rate',
                'lwop_days_charged',
                'is_monetized',
                'monetized_days',
                'monetization_amount',
                'is_forced_leave',
                'is_terminal_leave',
                'separation_type',
                'computation_method',
                'computation_notes',
                'medical_certificate_issued_date',
                'medical_certificate_validity_days',
                'is_fit_to_work',
                'actual_service_days',
                'included_in_service'
            ]);
            
            // Drop indexes
            $table->dropIndex('leaves_csc_type_status_index');
            $table->dropIndex('leaves_user_lwop_index');
            $table->dropIndex('leaves_user_monetized_index');
            $table->dropIndex('leaves_user_terminal_index');
        });
    }
};