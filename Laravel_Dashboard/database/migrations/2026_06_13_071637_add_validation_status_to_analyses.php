<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clinical_analyses', function (Blueprint $table) {
            $table->enum('validation_status', ['pending', 'confirmed', 'corrected'])
                  ->default('pending');
        });

        Schema::table('image_analyses', function (Blueprint $table) {
            $table->enum('validation_status', ['pending', 'confirmed', 'corrected'])
                  ->default('pending');
        });
    }

    public function down(): void
    {
        Schema::table('clinical_analyses', function (Blueprint $table) {
            $table->dropColumn('validation_status');
        });

        Schema::table('image_analyses', function (Blueprint $table) {
            $table->dropColumn('validation_status');
        });
    }
};