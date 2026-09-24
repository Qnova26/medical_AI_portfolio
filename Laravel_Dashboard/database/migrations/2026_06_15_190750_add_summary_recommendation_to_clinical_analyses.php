<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::table('clinical_analyses', function (Blueprint $table) {
            $table->text('summary')->nullable()->after('confidence_score');
            $table->text('recommendation')->nullable()->after('summary');
            $table->text('differential')->nullable()->after('recommendation');
        });
    }

  
    public function down(): void
    {
        Schema::table('clinical_analyses', function (Blueprint $table) {
            //
        });
    }
};
