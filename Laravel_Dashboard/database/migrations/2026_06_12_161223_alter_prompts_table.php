<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prompts', function (Blueprint $table) {
            $table->string('category')->after('name');
            $table->enum('level', ['Basic', 'Advanced', 'Expert'])->after('category');
            $table->string('version')->after('level');
            $table->string('status')->default('Active')->after('version');
            $table->text('system_prompt')->after('status');
            $table->text('user_prompt')->after('system_prompt');
            $table->dropColumn('content');
        });
    }

    public function down(): void
    {
        Schema::table('prompts', function (Blueprint $table) {
            $table->dropColumn(['category', 'level', 'version', 'status', 'system_prompt', 'user_prompt']);
            $table->text('content')->after('name');
        });
    }
};