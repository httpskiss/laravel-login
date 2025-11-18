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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('department');
            $table->date('filing_date');
            $table->string('position');
            $table->decimal('salary', 10, 2)->default(0);
            
            // Leave Details
            $table->string('type');
            $table->string('leave_location')->nullable();
            $table->string('abroad_specify')->nullable();
            $table->string('sick_type')->nullable();
            $table->string('hospital_illness')->nullable();
            $table->string('outpatient_illness')->nullable();
            $table->string('special_women_illness')->nullable();
            $table->string('study_purpose')->nullable();
            $table->string('other_purpose_specify')->nullable();
            $table->text('emergency_details')->nullable();
            $table->text('other_leave_details')->nullable();
            
            // Dates and Duration
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days', 4, 1)->default(0);
            $table->enum('commutation', ['requested', 'not_requested'])->default('not_requested');
            $table->text('reason');
            
            // Signature
            $table->text('signature_data')->nullable();
            
            // Leave Credits
            $table->date('credit_as_of_date')->nullable();
            $table->decimal('vacation_earned', 4, 1)->default(0);
            $table->decimal('vacation_less', 4, 1)->default(0);
            $table->decimal('vacation_balance', 4, 1)->default(0);
            $table->decimal('sick_earned', 4, 1)->default(0);
            $table->decimal('sick_less', 4, 1)->default(0);
            $table->decimal('sick_balance', 4, 1)->default(0);
            
            // Approval Details
            $table->enum('recommendation', ['approve', 'disapprove'])->nullable();
            $table->text('disapproval_reason')->nullable();
            $table->enum('approved_for', ['with_pay', 'without_pay', 'others'])->nullable();
            $table->decimal('with_pay_days', 4, 1)->default(0);
            $table->decimal('without_pay_days', 4, 1)->default(0);
            $table->string('others_specify')->nullable();
            $table->text('disapproved_reason')->nullable();
            
            // Status and Admin
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('type');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};