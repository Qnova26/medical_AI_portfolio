<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('image_analyses', function (Blueprint $table) {
            $table->text('diagnosis_short')->nullable()->change();
        });
        Schema::table('clinical_analyses', function (Blueprint $table) {
            $table->text('diagnosis_short')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('image_analyses', function (Blueprint $table) {
            $table->string('diagnosis_short', 255)->nullable()->change();
        });
        Schema::table('clinical_analyses', function (Blueprint $table) {
            $table->string('diagnosis_short', 255)->nullable()->change();
        });
    }
};
