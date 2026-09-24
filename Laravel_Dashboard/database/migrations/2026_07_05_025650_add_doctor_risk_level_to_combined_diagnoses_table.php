<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('combined_diagnoses', function (Blueprint $table) {
            $table->string('doctor_risk_level')->nullable()->after('final_risk_level');
        });
    }

    public function down(): void
    {
        Schema::table('combined_diagnoses', function (Blueprint $table) {
            $table->dropColumn('doctor_risk_level');
        });
    }
};