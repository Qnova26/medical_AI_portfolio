<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('combined_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('clinical_analysis_id')->constrained()->cascadeOnDelete();
            $table->foreignId('image_analysis_id')->constrained()->cascadeOnDelete();
            $table->string('final_diagnosis')->nullable();
            $table->string('final_risk_level')->nullable();
            $table->unsignedTinyInteger('final_confidence')->default(0);
            $table->text('final_summary')->nullable();
            $table->text('final_recommendation')->nullable();
            $table->json('ai_result')->nullable();
            $table->enum('status', ['pending', 'done', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combined_diagnoses');
    }
};