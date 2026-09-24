<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('image_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('prompt_id')->constrained('prompts')->cascadeOnDelete();

            // Jenis & lokasi citra
            $table->enum('image_type', ['X-Ray', 'CT-Scan', 'MRI', 'USG', 'ECG', 'DICOM', 'Other']);
            $table->string('body_part')->nullable();

            // File citra
            $table->json('image_files');

            // Catatan dokter
            $table->text('doctor_notes')->nullable();

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
        Schema::dropIfExists('image_analyses');
    }
};