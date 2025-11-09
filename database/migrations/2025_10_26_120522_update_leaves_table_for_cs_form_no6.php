<?php
// [file name]: 2025_10_26_114059_update_leaves_table_for_cs_form_no6.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Drop existing columns that conflict with new structure
            $table->dropColumn(['admin_notes', 'approved_by', 'approved_at']);
            
            // Add new columns for CS Form No. 6
            $table->string('department')->after('user_id');
            $table->date('filing_date')->after('department');
            $table->string('position')->after('filing_date');
            $table->decimal('salary', 10, 2)->after('position');
            
            // Modify type enum to include all CS Form types
            $table->enum('type', [
                'vacation', 'mandatory', 'sick', 'maternity', 'paternity',
                'special_privilege', 'solo_parent', 'study', 'vawc',
                'rehabilitation', 'special_women', 'emergency', 'adoption',
                'monetization', 'terminal', 'other'
            ])->change();
            
            // Add details columns
            $table->string('leave_location')->nullable()->after('type');
            $table->string('abroad_specify')->nullable()->after('leave_location');
            $table->string('sick_type')->nullable()->after('abroad_specify');
            $table->string('hospital_illness')->nullable()->after('sick_type');
            $table->string('outpatient_illness')->nullable()->after('hospital_illness');
            $table->string('special_women_illness')->nullable()->after('outpatient_illness');
            $table->string('study_purpose')->nullable()->after('special_women_illness');
            $table->string('other_purpose_specify')->nullable()->after('study_purpose');
            $table->string('emergency_details')->nullable()->after('other_purpose_specify');
            $table->string('other_leave_details')->nullable()->after('emergency_details');
            
            // Modify existing columns
            $table->decimal('days', 5, 1)->change(); // For half days support
            $table->enum('commutation', ['requested', 'not_requested'])->default('not_requested')->after('days');
            
            // Add signature and administrative columns
            $table->string('signature_data')->after('reason');
            $table->date('credit_as_of_date')->nullable()->after('signature_data');
            $table->decimal('vacation_earned', 5, 1)->nullable()->after('credit_as_of_date');
            $table->decimal('vacation_less', 5, 1)->nullable()->after('vacation_earned');
            $table->decimal('vacation_balance', 5, 1)->nullable()->after('vacation_less');
            $table->decimal('sick_earned', 5, 1)->nullable()->after('vacation_balance');
            $table->decimal('sick_less', 5, 1)->nullable()->after('sick_earned');
            $table->decimal('sick_balance', 5, 1)->nullable()->after('sick_less');
            $table->enum('recommendation', ['approve', 'disapprove'])->nullable()->after('sick_balance');
            $table->text('disapproval_reason')->nullable()->after('recommendation');
            $table->enum('approved_for', ['with_pay', 'without_pay', 'others'])->nullable()->after('disapproval_reason');
            $table->integer('with_pay_days')->nullable()->after('approved_for');
            $table->integer('without_pay_days')->nullable()->after('with_pay_days');
            $table->string('others_specify')->nullable()->after('without_pay_days');
            $table->text('disapproved_reason')->nullable()->after('others_specify');
            
            // Re-add admin columns with proper structure
            $table->text('admin_notes')->nullable()->after('disapproved_reason');
            $table->unsignedBigInteger('approved_by')->nullable()->after('admin_notes');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            
            // Add foreign key constraint
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Remove foreign key first
            $table->dropForeign(['approved_by']);
            
            // Drop all new columns
            $table->dropColumn([
                'department', 'filing_date', 'position', 'salary',
                'leave_location', 'abroad_specify', 'sick_type', 'hospital_illness',
                'outpatient_illness', 'special_women_illness', 'study_purpose',
                'other_purpose_specify', 'emergency_details', 'other_leave_details',
                'commutation', 'signature_data', 'credit_as_of_date', 'vacation_earned',
                'vacation_less', 'vacation_balance', 'sick_earned', 'sick_less',
                'sick_balance', 'recommendation', 'disapproval_reason', 'approved_for',
                'with_pay_days', 'without_pay_days', 'others_specify', 'disapproved_reason'
            ]);
            
            // Revert type enum
            $table->enum('type', ['vacation', 'sick', 'emergency', 'maternity', 'paternity', 'bereavement', 'other'])->change();
            $table->integer('days')->change();
        });
    }
};