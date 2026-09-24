<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clinical_analyses', function (Blueprint $table) {
            if (!Schema::hasColumn('clinical_analyses', 'doctor_diagnosis')) {
                $table->string('doctor_diagnosis')->nullable();
            }
            if (!Schema::hasColumn('clinical_analyses', 'medication_recommendation')) {
                $table->text('medication_recommendation')->nullable();
            }
        });

        Schema::table('image_analyses', function (Blueprint $table) {
            if (!Schema::hasColumn('image_analyses', 'doctor_risk_level')) {
                $table->string('doctor_risk_level')->nullable();
            }
            if (!Schema::hasColumn('image_analyses', 'doctor_diagnosis')) {
                $table->string('doctor_diagnosis')->nullable();
            }
            if (!Schema::hasColumn('image_analyses', 'medication_recommendation')) {
                $table->text('medication_recommendation')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('clinical_analyses', function (Blueprint $table) {
            $table->dropColumn(['doctor_diagnosis', 'medication_recommendation']);
        });
        Schema::table('image_analyses', function (Blueprint $table) {
            $table->dropColumn(['doctor_risk_level', 'doctor_diagnosis', 'medication_recommendation']);
        });
    }
};