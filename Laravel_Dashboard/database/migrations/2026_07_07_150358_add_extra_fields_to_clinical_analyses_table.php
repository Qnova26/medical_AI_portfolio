<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinical_analyses', function (Blueprint $table) {
            $table->text('confidence_reason')->nullable()->after('confidence_score');
            $table->text('treatment_information')->nullable()->after('recommendation');
            $table->text('potential_complications')->nullable()->after('treatment_information');
        });
    }

    public function down(): void
    {
        Schema::table('clinical_analyses', function (Blueprint $table) {
            $table->dropColumn(['confidence_reason', 'treatment_information', 'potential_complications']);
        });
    }
};
