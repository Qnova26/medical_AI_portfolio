<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        Patient::insert([

            [
                'id_pasien' => 'PAT-001',
                'nama_pasien' => 'Budi Santoso',
                'tanggal_lahir' => '1988-05-12',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Denpasar, Bali',
                'no_tlp' => '081234567890',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id_pasien' => 'PAT-002',
                'nama_pasien' => 'Siti Rahmawati',
                'tanggal_lahir' => '1992-08-21',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Badung, Bali',
                'no_tlp' => '081345678901',
                'created_at' => now(),
                'updated_at' => now(),
            ]

        ]);
    }
}