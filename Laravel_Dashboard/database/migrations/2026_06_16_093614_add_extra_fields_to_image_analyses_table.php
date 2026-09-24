<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('image_analyses', function (Blueprint $table) {
            $table->string('template_level')->nullable()->after('prompt_id');
            $table->json('result_images')->nullable()->after('image_files');
            $table->text('summary')->nullable()->after('confidence_score');
            $table->text('differential')->nullable()->after('summary');
            $table->text('recommendation')->nullable()->after('differential');
        });
    }

    public function down(): void
    {
        Schema::table('image_analyses', function (Blueprint $table) {
            $table->dropColumn(['template_level', 'result_images', 'summary', 'differential', 'recommendation']);
        });
    }
};