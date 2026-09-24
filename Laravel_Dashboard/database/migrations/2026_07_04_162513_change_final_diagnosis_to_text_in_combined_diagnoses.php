<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Isi dulu baris yang NULL biar gak ganggu proses alter
        DB::table('combined_diagnoses')
            ->whereNull('final_diagnosis')
            ->update(['final_diagnosis' => '']);

        Schema::table('combined_diagnoses', function (Blueprint $table) {
            $table->text('final_diagnosis')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('combined_diagnoses', function (Blueprint $table) {
            $table->string('final_diagnosis', 255)->nullable()->change();
        });
    }
};