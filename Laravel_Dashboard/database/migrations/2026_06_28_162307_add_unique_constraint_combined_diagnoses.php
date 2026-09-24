<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('combined_diagnoses', function (Blueprint $table) {
            $table->unique(['clinical_analysis_id', 'image_analysis_id'], 'combined_unique_pair');
        });
    }

    public function down()
    {
        Schema::table('combined_diagnoses', function (Blueprint $table) {
            $table->dropUnique('combined_unique_pair');
        });
    }
};