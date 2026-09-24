<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    DB::statement("ALTER TABLE clinical_analyses MODIFY template_level ENUM('Standar', 'Intermediate', 'Expert')");
}

public function down(): void
{
    DB::statement("ALTER TABLE clinical_analyses MODIFY template_level ENUM('Basic', 'Intermediate', 'Expert')");
}
};