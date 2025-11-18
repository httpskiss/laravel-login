<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('travel_authority_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_authority_id')->constrained()->onDelete('cascade');
            $table->string('approval_type'); // recommending_approval, allotment_available, funds_available, final_approval
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->string('approver_role'); // Role of the approver
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('comments')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('travel_authority_approvals');
    }
};