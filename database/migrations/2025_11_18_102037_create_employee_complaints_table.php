command forthis. <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employee_complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('complaint_number')->unique()->nullable();
            $table->enum('type', [
                'harassment',
                'discrimination', 
                'workplace_bullying',
                'safety_concern',
                'ethical_concern',
                'work_environment',
                'management_issue',
                'other'
            ]);
            $table->string('subject');
            $table->text('description');
            $table->text('incident_details')->nullable();
            $table->date('incident_date')->nullable();
            $table->string('location')->nullable();
            $table->json('involved_parties')->nullable(); // Store names/roles of involved people
            $table->json('evidence_files')->nullable(); // Store file paths
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', [
                'submitted', 
                'under_review', 
                'investigation_started',
                'resolved',
                'rejected',
                'closed'
            ])->default('submitted');
            $table->text('hr_remarks')->nullable();
            $table->foreignId('assigned_hr_id')->nullable()->constrained('users');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            $table->boolean('is_confidential')->default(true);
            $table->boolean('is_anonymous')->default(false);
            $table->timestamps();
        });

        Schema::create('complaint_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('employee_complaints')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('users');
            $table->string('update_type'); // status_change, note, assignment, resolution
            $table->text('description');
            $table->boolean('is_internal_note')->default(false); // Not visible to employee
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaint_updates');
        Schema::dropIfExists('employee_complaints');
    }
};