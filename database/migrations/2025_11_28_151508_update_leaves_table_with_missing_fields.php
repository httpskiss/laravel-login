<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Add duration type fields
            if (!Schema::hasColumn('leaves', 'duration_type')) {
                $table->string('duration_type')->default('full_day')->after('end_date');
            }
            
            if (!Schema::hasColumn('leaves', 'half_day_time')) {
                $table->string('half_day_time')->nullable()->after('duration_type');
            }
            
            // Add electronic signature path
            if (!Schema::hasColumn('leaves', 'electronic_signature_path')) {
                $table->string('electronic_signature_path')->nullable()->after('signature_data');
            }
            
            // Add handover fields
            if (!Schema::hasColumn('leaves', 'handover_person_id')) {
                $table->foreignId('handover_person_id')->nullable()->after('approved_by')->constrained('users');
            }
            
            if (!Schema::hasColumn('leaves', 'handover_notes')) {
                $table->text('handover_notes')->nullable()->after('handover_person_id');
            }
            
            // Add PDF path if not exists
            if (!Schema::hasColumn('leaves', 'pdf_path')) {
                $table->string('pdf_path')->nullable()->after('travel_itinerary_path');
            }
            
            // Update days column to support decimal
            if (Schema::hasColumn('leaves', 'days')) {
                $table->decimal('days', 4, 1)->change();
            }
            
            // Add missing leave credit fields if they don't exist
            if (!Schema::hasColumn('leaves', 'vacation_earned')) {
                $table->decimal('vacation_earned', 5, 1)->nullable()->after('credit_as_of_date');
            }
            
            if (!Schema::hasColumn('leaves', 'vacation_less')) {
                $table->decimal('vacation_less', 5, 1)->nullable()->after('vacation_earned');
            }
            
            if (!Schema::hasColumn('leaves', 'vacation_balance')) {
                $table->decimal('vacation_balance', 5, 1)->nullable()->after('vacation_less');
            }
            
            if (!Schema::hasColumn('leaves', 'sick_earned')) {
                $table->decimal('sick_earned', 5, 1)->nullable()->after('vacation_balance');
            }
            
            if (!Schema::hasColumn('leaves', 'sick_less')) {
                $table->decimal('sick_less', 5, 1)->nullable()->after('sick_earned');
            }
            
            if (!Schema::hasColumn('leaves', 'sick_balance')) {
                $table->decimal('sick_balance', 5, 1)->nullable()->after('sick_less');
            }
        });
    }

    public function down()
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Remove the added columns
            $table->dropColumn([
                'duration_type',
                'half_day_time',
                'electronic_signature_path',
                'handover_person_id',
                'handover_notes',
                'pdf_path',
                'vacation_earned',
                'vacation_less',
                'vacation_balance',
                'sick_earned',
                'sick_less',
                'sick_balance'
            ]);
            
            // Revert days column to integer if it was changed
            $table->integer('days')->change();
        });
    }
};