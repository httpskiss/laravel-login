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
       Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->decimal('total_hours', 5, 2)->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'on_leave', 'half_day'])->default('present');
            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('device_info')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_regularized')->default(false);
            $table->text('regularization_reason')->nullable();
            $table->unsignedBigInteger('regularized_by')->nullable();
            $table->timestamp('regularized_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
