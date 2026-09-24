<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('prompt_id')->constrained('prompts')->cascadeOnDelete();
            $table->enum('template_level', ['Basic', 'Intermediate', 'Expert']);

            // Vital Signs
            $table->decimal('body_temperature', 4, 1)->nullable();
            $table->integer('heart_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->string('blood_pressure')->nullable();

            // Anamnesis
            $table->text('medical_history')->nullable();
            $table->text('allergies')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->text('other_info')->nullable();

            // File lab/dokumen
            $table->json('medical_files')->nullable();

            // Hasil AI — field utama dipisah untuk filter/query
            $table->string('diagnosis_short')->nullable();
            $table->enum('risk_level', ['Rendah', 'Sedang', 'Tinggi'])->nullable();
            $table->unsignedTinyInteger('confidence_score')->nullable(); // 0-100

            // Full response AI
            $table->longText('ai_result')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_analyses');
    }
};