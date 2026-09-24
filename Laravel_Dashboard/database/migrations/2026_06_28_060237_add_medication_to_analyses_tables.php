<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clinical_analyses', function (Blueprint $table) {
            if (!Schema::hasColumn('clinical_analyses', 'medication')) {
                $table->text('medication')->nullable();
            }
        });
        Schema::table('image_analyses', function (Blueprint $table) {
            if (!Schema::hasColumn('image_analyses', 'medication')) {
                $table->text('medication')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('clinical_analyses', function (Blueprint $table) {
            $table->dropColumn('medication');
        });
        Schema::table('image_analyses', function (Blueprint $table) {
            $table->dropColumn('medication');
        });
    }
};