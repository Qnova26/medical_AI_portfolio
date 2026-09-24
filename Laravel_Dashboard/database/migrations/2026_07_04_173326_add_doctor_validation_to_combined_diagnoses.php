<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('combined_diagnoses', function (Blueprint $table) {
            $table->enum('validation_status', ['pending', 'confirmed', 'corrected'])
                  ->default('pending')
                  ->after('status');
            $table->text('doctor_notes')->nullable()->after('validation_status');
            $table->text('doctor_final_diagnosis')->nullable()->after('doctor_notes');
            $table->text('doctor_medication')->nullable()->after('doctor_final_diagnosis');
        });
    }

    public function down(): void
    {
        Schema::table('combined_diagnoses', function (Blueprint $table) {
            $table->dropColumn(['validation_status', 'doctor_notes', 'doctor_final_diagnosis', 'doctor_medication']);
        });
    }
};