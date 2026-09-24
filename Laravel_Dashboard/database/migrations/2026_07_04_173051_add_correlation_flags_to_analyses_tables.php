<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinical_analyses', function (Blueprint $table) {
            $table->boolean('needs_imaging_correlation')->nullable()->after('ai_result');
            $table->text('imaging_correlation_reason')->nullable()->after('needs_imaging_correlation');
        });

        Schema::table('image_analyses', function (Blueprint $table) {
            $table->boolean('needs_clinical_correlation')->nullable()->after('ai_result');
            $table->text('clinical_correlation_reason')->nullable()->after('needs_clinical_correlation');
        });
    }

    public function down(): void
    {
        Schema::table('clinical_analyses', function (Blueprint $table) {
            $table->dropColumn(['needs_imaging_correlation', 'imaging_correlation_reason']);
        });

        Schema::table('image_analyses', function (Blueprint $table) {
            $table->dropColumn(['needs_clinical_correlation', 'clinical_correlation_reason']);
        });
    }
};