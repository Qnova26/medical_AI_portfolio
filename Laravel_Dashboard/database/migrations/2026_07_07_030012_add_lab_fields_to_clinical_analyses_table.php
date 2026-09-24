<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinical_analyses', function (Blueprint $table) {
            // Path file hasil lab yang diupload (opsional), disimpan mirip medical_files
            $table->json('lab_files')->nullable()->after('medical_files');

            // Hasil analisis AI atas file lab tsb (opsional, null kalau gak ada file lab)
            $table->text('lab_analysis')->nullable()->after('lab_files');
        });
    }

    public function down(): void
    {
        Schema::table('clinical_analyses', function (Blueprint $table) {
            $table->dropColumn(['lab_files', 'lab_analysis']);
        });
    }
};