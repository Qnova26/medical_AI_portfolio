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
        // Gunakan Schema::create untuk membuat tabel baru
        Schema::create('patients', function (Blueprint $table) {
            $table->id(); // Ini akan membuat kolom 'id' otomatis (Primary Key)
            $table->string('id_pasien')->unique();
            $table->string('nama_pasien');
            $table->date('tanggal_lahir');
            $table->string('jenis_kelamin');
            $table->text('alamat')->nullable();
            $table->string('no_tlp')->nullable();
            $table->timestamps(); // Ini akan membuat kolom 'created_at' dan 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus tabel jika dibatalkan
        Schema::dropIfExists('patients');
    }
};