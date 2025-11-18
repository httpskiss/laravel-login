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
        Schema::table('leaves', function (Blueprint $table) {
            // Add columns for file uploads
            $table->string('medical_certificate_path')->nullable()->after('signature_data');
            $table->string('travel_itinerary_path')->nullable()->after('medical_certificate_path');
            
            // Add columns for emergency contact
            $table->string('emergency_contact_name')->nullable()->after('travel_itinerary_path');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');
            
            // Add columns for work handover
            $table->foreignId('handover_person_id')->nullable()->constrained('users')->after('emergency_contact_relationship');
            $table->text('handover_notes')->nullable()->after('handover_person_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn([
                'medical_certificate_path',
                'travel_itinerary_path',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relationship',
                'handover_person_id',
                'handover_notes'
            ]);
        });
    }
};