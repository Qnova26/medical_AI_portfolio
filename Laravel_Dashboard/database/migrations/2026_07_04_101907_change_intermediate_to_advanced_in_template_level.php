<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Perluas dulu enum-nya biar 'Advanced' bisa masuk, tanpa hapus 'Intermediate' dulu
        DB::statement("ALTER TABLE clinical_analyses MODIFY template_level ENUM('Basic','Intermediate','Advanced','Expert') NOT NULL");
        DB::statement("ALTER TABLE image_analyses MODIFY template_level ENUM('Basic','Intermediate','Advanced','Expert') NOT NULL");

        // Migrasikan data lama yang masih 'Intermediate' jadi 'Advanced'
        DB::table('clinical_analyses')->where('template_level', 'Intermediate')->update(['template_level' => 'Advanced']);
        DB::table('image_analyses')->where('template_level', 'Intermediate')->update(['template_level' => 'Advanced']);

        // Setelah data lama dikonversi, persempit lagi enum-nya, hapus 'Intermediate'
        DB::statement("ALTER TABLE clinical_analyses MODIFY template_level ENUM('Basic','Advanced','Expert') NOT NULL");
        DB::statement("ALTER TABLE image_analyses MODIFY template_level ENUM('Basic','Advanced','Expert') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE clinical_analyses MODIFY template_level ENUM('Basic','Intermediate','Advanced','Expert') NOT NULL");
        DB::statement("ALTER TABLE image_analyses MODIFY template_level ENUM('Basic','Intermediate','Advanced','Expert') NOT NULL");

        DB::table('clinical_analyses')->where('template_level', 'Advanced')->update(['template_level' => 'Intermediate']);
        DB::table('image_analyses')->where('template_level', 'Advanced')->update(['template_level' => 'Intermediate']);

        DB::statement("ALTER TABLE clinical_analyses MODIFY template_level ENUM('Basic','Intermediate','Expert') NOT NULL");
        DB::statement("ALTER TABLE image_analyses MODIFY template_level ENUM('Basic','Intermediate','Expert') NOT NULL");
    }
};