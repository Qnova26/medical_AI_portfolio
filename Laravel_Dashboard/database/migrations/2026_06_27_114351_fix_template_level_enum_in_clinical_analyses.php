<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE clinical_analyses MODIFY template_level ENUM('Basic','Intermediate','Expert') NOT NULL");
        DB::statement("ALTER TABLE image_analyses MODIFY template_level ENUM('Basic','Intermediate','Expert') NOT NULL");
    }

    public function down(): void
    {
        
    }
};