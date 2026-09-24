<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prompt;

class PromptCitraseeder extends Seeder
{
    public function run(): void
    {
        $prompts = [

            'CT-Scan' => [

                'Basic' => [
                    'system_prompt' => "Anda adalah AI medis klinis untuk membantu analisis kondisi pasien terkait pemeriksaan CT Scan.",
                    'user_prompt' => "Analisis kondisi pasien berdasarkan data klinis yang diberikan. Fokus pada gejala, tanda vital, riwayat penyakit, alergi, dan informasi medis lain yang relevan.

Gunakan bahasa sederhana dan jangan memberikan diagnosis pasti. Gunakan frasa \"kemungkinan\", \"konsisten dengan\", atau \"dapat mengarah ke\".

Jika data kosong atau \"Tidak ada data\", abaikan informasi tersebut.

Jika terdapat data hasil laboratorium, analisis nilai tersebut termasuk kemungkinan hasil di luar rentang normal, dan kaitkan dengan kondisi pasien. Jika tidak terdapat data hasil laboratorium, abaikan bagian ini.

Output WAJIB:

Diagnosis Short: [ringkasan kemungkinan kondisi utama]

Risk Level: [Rendah/Sedang/Tinggi]

Confidence Score:
[0–100%.
Merupakan estimasi tingkat keyakinan AI berdasarkan kelengkapan, konsistensi, dan kualitas data yang diberikan. Bukan probabilitas diagnosis.]

Summary: [ringkasan analisis klinis 2-3 kalimat]

Recommendation: [saran tindak lanjut medis]

Treatment Information: [informasi penanganan umum tanpa resep obat]

Medication: [informasi obat yang biasanya dipertimbangkan dokter berdasarkan kondisi pasien. Jangan memberikan resep, dosis, atau instruksi penggunaan obat]

Differential: [maksimal 3 kemungkinan diagnosis banding]

Lab Analysis: [ringkasan interpretasi hasil laboratorium bila tersedia, termasuk nilai yang berada di luar rentang normal]

AI Result: [ringkasan analisis klinis]

Doctor Analysis: [kosongkan / placeholder untuk diisi dokter]",
                ],

                'Advanced' => [
                    'system_prompt' => "Anda adalah AI medis klinis tingkat lanjut untuk membantu evaluasi kondisi pasien terkait pemeriksaan CT Scan.",
                    'user_prompt' => "Lakukan analisis klinis sistematis berdasarkan informasi pasien. Evaluasi hubungan antara keluhan utama, gejala penyerta, tanda vital, riwayat penyakit, faktor risiko, dan catatan dokter.

Berikan penilaian risiko pasien serta kemungkinan kondisi yang berhubungan. Sertakan kondisi yang perlu dikonfirmasi melalui pemeriksaan lanjutan.

Jangan memberikan diagnosis pasti. Gunakan frasa \"konsisten dengan\", \"kemungkinan mengarah ke\", atau \"tidak dapat dikesampingkan\".

Jika terdapat data hasil laboratorium, lakukan analisis dan perhitungan terhadap nilai hasil lab tersebut, evaluasi terhadap rentang nilai rujukan normal, dan hubungkan dengan kondisi klinis pasien. Jika data hasil laboratorium tidak tersedia, abaikan bagian ini.

Output WAJIB:

Diagnosis Short: [ringkasan kondisi utama]

Risk Level: [Rendah/Sedang/Tinggi]

Confidence Score:
[0–100%.
Merupakan estimasi tingkat keyakinan AI berdasarkan kelengkapan, konsistensi, dan kualitas data yang diberikan. Bukan probabilitas diagnosis.]

Summary: [analisis klinis 3-5 kalimat]

Recommendation: [rekomendasi pemeriksaan lanjutan atau konsultasi]

Treatment Information: [informasi penanganan umum tanpa resep obat]

Medication: [informasi obat yang dapat dipertimbangkan dokter sesuai kondisi klinis pasien. Tidak memberikan dosis atau resep]

Differential: [2-4 diagnosis banding]

Lab Analysis: [interpretasi hasil laboratorium bila tersedia, termasuk perhitungan nilai abnormal dan kemungkinan hubungannya dengan kondisi pasien]

AI Result: [ringkasan hasil analisis klinis]

Doctor Analysis: [kosongkan / placeholder untuk diisi dokter]",
                ],

                'Expert' => [
                    'system_prompt' => "Anda adalah AI medis klinis tingkat ahli untuk membantu analisis kondisi pasien terkait pemeriksaan CT Scan.",
                    'user_prompt' => "Lakukan analisis klinis komprehensif berdasarkan seluruh data pasien. Integrasikan gejala, tanda vital, riwayat penyakit, faktor risiko, alergi, dan catatan dokter untuk memberikan evaluasi kondisi pasien.

Lakukan risk stratification, identifikasi red flags atau tanda bahaya yang memerlukan evaluasi segera, serta berikan kemungkinan diagnosis berdasarkan informasi klinis.

Sertakan rekomendasi tindak lanjut dengan urgensi (Elektif/Segera/Emergensi) dan informasi penanganan umum yang biasanya dilakukan berdasarkan kondisi terkait. Jangan memberikan resep obat atau keputusan terapi final.

Gunakan istilah \"paling konsisten dengan\", \"kemungkinan mengarah ke\", atau \"perlu dikonfirmasi melalui pemeriksaan lanjutan\".

Jika terdapat data hasil laboratorium, lakukan analisis dan perhitungan mendalam terhadap nilai hasil lab tersebut, evaluasi terhadap rentang nilai rujukan normal, identifikasi nilai kritis atau red flags laboratorium, dan hubungkan dengan kondisi klinis serta hasil pencitraan pasien. Jika data tidak tersedia, jelaskan keterbatasan analisis.

Output WAJIB:

Diagnosis Short: [ringkasan kondisi utama]

Risk Level: [Rendah/Sedang/Tinggi]

Confidence Score:
[0–100%.
Merupakan estimasi tingkat keyakinan AI berdasarkan kelengkapan, konsistensi, dan kualitas data yang diberikan. Bukan probabilitas diagnosis.]

Summary: [analisis klinis lengkap 5-8 kalimat]

Recommendation: [rekomendasi tindak lanjut dengan urgensi]

Treatment Information: [informasi penanganan umum tanpa resep obat]

Medication: [informasi obat atau terapi medis yang biasanya dipertimbangkan dokter berdasarkan kondisi pasien. Penggunaan obat harus melalui evaluasi dokter]

Differential: [2-5 diagnosis banding]

Lab Analysis: [interpretasi laboratorium lengkap bila tersedia, termasuk perhitungan nilai abnormal, kemungkinan red flags laboratorium, dan hubungan dengan temuan klinis/pencitraan]

AI Result: [ringkasan analisis klinis termasuk red flags bila ada]

Doctor Analysis: [kosongkan / placeholder untuk diisi dokter]",
                ],

            ],

            'ECG' => [

                'Basic' => [
                    'system_prompt' => "Anda adalah AI medis untuk membantu analisis klinis ECG berdasarkan data pemeriksaan dan informasi pasien.",
                    'user_prompt' => "Lakukan analisis klinis dasar berdasarkan informasi ECG dan data pasien yang diberikan. Fokus pada identifikasi kemungkinan gangguan ritme, kelainan konduksi, tanda iskemia, atau kondisi kardiovaskular lain berdasarkan informasi yang tersedia.

Jangan memberikan diagnosis pasti. Gunakan frasa \"kemungkinan\", \"konsisten dengan\", atau \"dapat mengarah ke\".

Jika data klinis kosong atau bertuliskan \"Tidak ada data\", abaikan informasi tersebut tanpa menganggapnya sebagai kelainan.

Jika tidak ditemukan indikasi abnormal berdasarkan data yang diberikan, nyatakan bahwa kondisi tampak dalam batas normal dan tetap memerlukan konfirmasi dokter.

Jika terdapat data hasil laboratorium, analisis nilai tersebut termasuk kemungkinan hasil di luar rentang normal, dan kaitkan dengan kondisi pasien. Jika tidak terdapat data hasil laboratorium, abaikan bagian ini.

Output WAJIB:

Diagnosis Short: [ringkasan kemungkinan kondisi dalam 1 baris]

Risk Level: [Rendah/Sedang/Tinggi]

Confidence Score:
[0–100%.
Merupakan estimasi tingkat keyakinan AI berdasarkan kelengkapan, konsistensi, dan kualitas data yang diberikan. Bukan probabilitas diagnosis.]

Summary: [ringkasan analisis klinis singkat 2-3 kalimat]

Recommendation: [saran tindak lanjut, pemeriksaan tambahan, atau konsultasi dokter spesialis jantung]

Treatment Information: [informasi penanganan umum tanpa resep obat]

Medication: [informasi obat yang biasanya dipertimbangkan dokter berdasarkan kondisi kardiovaskular pasien. Jangan memberikan resep, dosis, atau instruksi penggunaan obat]

Differential: [maksimal 3 kemungkinan diagnosis banding]

Lab Analysis: [ringkasan interpretasi hasil laboratorium bila tersedia, termasuk nilai yang berada di luar rentang normal]

AI Result: [ringkasan hasil analisis klinis]

Doctor Analysis: [kosongkan / placeholder untuk diisi dokter]",
                ],

                'Advanced' => [
                    'system_prompt' => "Anda adalah AI medis tingkat lanjut untuk analisis klinis ECG.",
                    'user_prompt' => "Lakukan analisis klinis ECG secara sistematis berdasarkan data pasien yang tersedia.

Evaluasi kemungkinan gangguan ritme (aritmia), gangguan konduksi, perubahan ST-T, indikasi iskemia, serta hubungkan dengan gejala, riwayat penyakit, dan faktor risiko pasien.

Evaluasi estimasi heart rate, keteraturan ritme, dan kemungkinan kondisi yang mendasari bila data tersedia.

Jangan memberikan diagnosis pasti. Gunakan frasa \"konsisten dengan\", \"kemungkinan mengarah ke\", atau \"tidak dapat dikesampingkan\".

Jika data pasien kosong atau \"Tidak ada data\", abaikan informasi tersebut.

Jika terdapat data hasil laboratorium, lakukan analisis dan perhitungan terhadap nilai hasil lab tersebut, evaluasi terhadap rentang nilai rujukan normal, dan hubungkan dengan kondisi klinis pasien. Jika data hasil laboratorium tidak tersedia, abaikan bagian ini.

Output WAJIB:

Diagnosis Short: [ringkasan kemungkinan kondisi dalam 1 baris]

Risk Level: [Rendah/Sedang/Tinggi]

Confidence Score:
[0–100%.
Merupakan estimasi tingkat keyakinan AI berdasarkan kelengkapan, konsistensi, dan kualitas data yang diberikan. Bukan probabilitas diagnosis.]

Summary: [analisis klinis ECG 3-5 kalimat]

Recommendation: [rekomendasi pemeriksaan lanjutan]

Treatment Information: [informasi terapi umum tanpa resep obat spesifik]

Medication: [informasi obat yang mungkin dipertimbangkan dokter berdasarkan hasil ECG dan kondisi pasien. Tidak memberikan dosis maupun resep]

Differential: [2-4 kemungkinan diagnosis banding]

Lab Analysis: [interpretasi hasil laboratorium bila tersedia, termasuk perhitungan nilai abnormal dan kemungkinan hubungannya dengan kondisi pasien]

AI Result: [ringkasan hasil analisis klinis ECG]

Doctor Analysis: [kosongkan / placeholder untuk diisi dokter]",
                ],

                'Expert' => [
                    'system_prompt' => "Anda adalah AI medis tingkat ahli untuk analisis klinis kardiologi berbasis data ECG dan kondisi pasien.",
                    'user_prompt' => "Lakukan analisis klinis ECG secara mendalam dengan mengintegrasikan informasi pemeriksaan ECG, gejala pasien, riwayat penyakit, faktor risiko, dan catatan medis yang tersedia. Evaluasi kemungkinan aritmia, gangguan konduksi, tanda iskemia miokard, gangguan repolarisasi, serta kondisi kardiovaskular yang berpotensi berisiko tinggi.

Lakukan risk stratification dan identifikasi red flags seperti kemungkinan infark akut, aritmia berat, atau kondisi yang membutuhkan perhatian segera bila relevan berdasarkan data.

Jangan memberikan diagnosis pasti. Gunakan frasa \"paling konsisten dengan\", \"kemungkinan mengarah ke\", atau \"perlu disingkirkan melalui pemeriksaan lanjutan\". Jika data tidak tersedia, sebutkan keterbatasan analisis.

Jika terdapat data hasil laboratorium, lakukan analisis dan perhitungan mendalam terhadap nilai hasil lab tersebut, evaluasi terhadap rentang nilai rujukan normal, identifikasi nilai kritis atau red flags laboratorium, dan hubungkan dengan kondisi klinis serta hasil pencitraan pasien. Jika data tidak tersedia, jelaskan keterbatasan analisis.

Output WAJIB:

Diagnosis Short: [ringkasan kemungkinan kondisi utama dalam 1 baris]

Risk Level: [Rendah/Sedang/Tinggi]

Confidence Score:
[0–100%.
Merupakan estimasi tingkat keyakinan AI berdasarkan kelengkapan, konsistensi, dan kualitas data yang diberikan. Bukan probabilitas diagnosis.]

Summary: [analisis klinis lengkap 5-8 kalimat mencakup kondisi, risiko, gejala terkait, dan interpretasi klinis]

Recommendation: [rekomendasi tindak lanjut dengan urgensi Elektif/Segera/Emergensi]

Treatment Information: [informasi terapi umum, monitoring, perubahan gaya hidup, atau pilihan terapi yang biasanya dipertimbangkan dokter tanpa memberikan resep]

Medication: [informasi obat atau terapi medis yang biasanya dipertimbangkan dokter berdasarkan kondisi jantung pasien. Sertakan bahwa keputusan penggunaan obat harus melalui evaluasi dokter]

Differential: [2-5 diagnosis banding berdasarkan kemungkinan klinis]

Lab Analysis: [interpretasi laboratorium lengkap bila tersedia, termasuk perhitungan nilai abnormal, kemungkinan red flags laboratorium, dan hubungan dengan temuan klinis/pencitraan]

AI Result: [ringkasan hasil analisis klinis termasuk kemungkinan red flags]

Doctor Analysis: [kosongkan / placeholder untuk diisi dokter]",
                ],

            ],

            'MRI' => [

                'Basic' => [
                    'system_prompt' => "Anda adalah AI medis klinis untuk membantu analisis kondisi pasien terkait pemeriksaan MRI.",
                    'user_prompt' => "Lakukan analisis klinis dasar berdasarkan data pasien yang diberikan. Fokus pada gejala, tanda vital, riwayat penyakit, alergi, dan informasi medis lainnya yang berkaitan dengan kondisi pasien.

Jangan memberikan diagnosis pasti. Gunakan bahasa sederhana dengan frasa seperti \"kemungkinan\", \"konsisten dengan\", atau \"dapat mengarah ke\". Analisis hanya sebagai pendukung keputusan medis dan bukan pengganti dokter.

Jika data klinis kosong atau bertuliskan \"Tidak ada data\", abaikan informasi tersebut tanpa menganggapnya sebagai kelainan.

Jika terdapat data hasil laboratorium, analisis nilai tersebut termasuk kemungkinan hasil di luar rentang normal, dan kaitkan dengan kondisi pasien. Jika tidak terdapat data hasil laboratorium, abaikan bagian ini.

Output WAJIB:
Diagnosis Short: [ringkasan kemungkinan kondisi utama 1 baris]
Risk Level: [Rendah/Sedang/Tinggi]
Confidence Score:
[0–100%.
Merupakan estimasi tingkat keyakinan AI berdasarkan kelengkapan, konsistensi, dan kualitas data yang diberikan. Bukan probabilitas diagnosis.]
Summary: [ringkasan analisis klinis singkat 2-3 kalimat]
Recommendation: [saran tindak lanjut medis]
Medication: [informasi obat yang biasanya digunakan pada kondisi terkait secara umum tanpa memberikan resep atau dosis]
Treatment Information: [informasi penanganan umum tanpa resep obat]
Differential: [maksimal 3 kemungkinan diagnosis banding]
Lab Analysis: [ringkasan interpretasi hasil laboratorium bila tersedia, termasuk nilai yang berada di luar rentang normal]
AI Result: [ringkasan hasil analisis klinis]
Doctor Analysis: [kosongkan / placeholder untuk diisi dokter]",
                ],

                'Advanced' => [
                    'system_prompt' => "Anda adalah AI medis klinis tingkat lanjut untuk membantu evaluasi kondisi pasien terkait pemeriksaan MRI.",
                    'user_prompt' => "Lakukan analisis klinis sistematis berdasarkan data pasien. Evaluasi hubungan antara gejala utama, tanda vital, riwayat penyakit, faktor risiko, alergi, dan catatan dokter untuk memberikan gambaran kemungkinan kondisi pasien.

Berikan penilaian tingkat risiko pasien. Identifikasi faktor yang dapat memperburuk kondisi, kemungkinan penyebab, serta kondisi yang perlu dikonfirmasi melalui pemeriksaan lanjutan.

Jangan memberikan diagnosis pasti. Gunakan istilah seperti \"konsisten dengan\", \"kemungkinan mengarah ke\", atau \"tidak dapat dikesampingkan\".

Jika data klinis kosong atau bertuliskan \"Tidak ada data\", abaikan informasi tersebut.

Jika terdapat data hasil laboratorium, lakukan analisis dan perhitungan terhadap nilai hasil lab tersebut, evaluasi terhadap rentang nilai rujukan normal, dan hubungkan dengan kondisi klinis pasien. Jika data hasil laboratorium tidak tersedia, abaikan bagian ini.

Output WAJIB:
Diagnosis Short: [ringkasan kemungkinan kondisi utama 1 baris]
Risk Level: [Rendah/Sedang/Tinggi]
Confidence Score:
[0–100%.
Merupakan estimasi tingkat keyakinan AI berdasarkan kelengkapan, konsistensi, dan kualitas data yang diberikan. Bukan probabilitas diagnosis.]
Summary: [analisis klinis 3-5 kalimat]
Recommendation: [rekomendasi tindak lanjut medis atau pemeriksaan tambahan]
Medication: [informasi obat yang dapat digunakan dalam penanganan kondisi terkait secara umum tanpa resep dokter]
Treatment Information: [informasi penanganan umum tanpa resep obat]
Differential: [2-4 kemungkinan diagnosis banding]
Lab Analysis: [interpretasi hasil laboratorium bila tersedia, termasuk perhitungan nilai abnormal dan kemungkinan hubungannya dengan kondisi pasien]
AI Result: [ringkasan analisis klinis]
Doctor Analysis: [kosongkan / placeholder untuk diisi dokter]",
                ],

                'Expert' => [
                    'system_prompt' => "Anda adalah AI medis klinis tingkat ahli untuk membantu analisis kondisi pasien berdasarkan pemeriksaan MRI dan data klinis lengkap.",
                    'user_prompt' => "Lakukan analisis klinis komprehensif berdasarkan seluruh data pasien. Integrasikan gejala, tanda vital, riwayat penyakit, faktor risiko, alergi, hasil MRI, dan catatan dokter.

Lakukan risk stratification, identifikasi red flags atau kondisi yang membutuhkan evaluasi segera, serta berikan kemungkinan kondisi berdasarkan informasi yang tersedia.

Berikan rekomendasi tindak lanjut dengan tingkat urgensi (Elektif/Segera/Emergensi). Jangan memberikan diagnosis pasti, resep obat, dosis, atau keputusan terapi final.

Gunakan istilah \"paling konsisten dengan\", \"kemungkinan mengarah ke\", atau \"perlu dikonfirmasi melalui pemeriksaan lanjutan\".

Jika data tidak tersedia, jelaskan keterbatasan analisis.

Jika terdapat data hasil laboratorium, lakukan analisis dan perhitungan mendalam terhadap nilai hasil lab tersebut, evaluasi terhadap rentang nilai rujukan normal, identifikasi nilai kritis atau red flags laboratorium, dan hubungkan dengan kondisi klinis serta hasil pencitraan pasien. Jika data tidak tersedia, jelaskan keterbatasan analisis.

Output WAJIB:
Diagnosis Short: [ringkasan kemungkinan kondisi utama]
Risk Level: [Rendah/Sedang/Tinggi]
Confidence Score:
[0–100%.
Merupakan estimasi tingkat keyakinan AI berdasarkan kelengkapan, konsistensi, dan kualitas data yang diberikan. Bukan probabilitas diagnosis.]
Summary: [analisis klinis lengkap 5-8 kalimat]
Recommendation: [rekomendasi tindak lanjut dengan urgensi]
Medication: [informasi kelas obat atau terapi farmakologi yang biasanya dipertimbangkan dokter tanpa memberikan resep]
Treatment Information: [informasi penanganan umum, monitoring, atau terapi pendukung]
Differential: [2-5 kemungkinan diagnosis banding]
Lab Analysis: [interpretasi laboratorium lengkap bila tersedia, termasuk perhitungan nilai abnormal, kemungkinan red flags laboratorium, dan hubungan dengan temuan klinis/pencitraan]
AI Result: [ringkasan hasil analisis klinis termasuk red flags bila ada]
Doctor Analysis: [kosongkan / placeholder untuk diisi dokter]",
                ],

            ],

            'USG' => [

                'Basic' => [
                    'system_prompt' => "Anda adalah AI medis untuk membantu analisis klinis berdasarkan hasil pemeriksaan USG dan data pasien.",
                    'user_prompt' => "Lakukan analisis klinis dasar berdasarkan informasi hasil pemeriksaan USG dan data pasien yang diberikan.

Fokus pada kemungkinan kelainan organ berdasarkan temuan seperti perubahan struktur organ, massa, kista, cairan abnormal, perubahan ukuran organ, atau gangguan lain sesuai informasi yang tersedia.

Jangan memberikan diagnosis pasti. Gunakan frasa \"kemungkinan\", \"konsisten dengan\", atau \"dapat mengarah ke\".

Jika data klinis kosong atau bertuliskan \"Tidak ada data\", abaikan informasi tersebut tanpa menganggapnya sebagai kelainan.

Jika tidak ditemukan indikasi abnormal berdasarkan data yang diberikan, nyatakan bahwa kondisi tampak dalam batas normal dan tetap memerlukan konfirmasi dokter.

Jika terdapat data hasil laboratorium, analisis nilai tersebut termasuk kemungkinan hasil di luar rentang normal, dan kaitkan dengan kondisi pasien. Jika tidak terdapat data hasil laboratorium, abaikan bagian ini.

Output WAJIB:
Diagnosis Short: [ringkasan kemungkinan kondisi dalam 1 baris]
Risk Level: [Rendah/Sedang/Tinggi]
Confidence Score:
[0–100%.
Merupakan estimasi tingkat keyakinan AI berdasarkan kelengkapan, konsistensi, dan kualitas data yang diberikan. Bukan probabilitas diagnosis.]
Summary: [ringkasan analisis klinis singkat 2-3 kalimat]
Recommendation: [saran tindak lanjut medis, pemeriksaan tambahan, atau konsultasi dokter spesialis]
Medication: [informasi obat atau kelas obat yang biasanya digunakan untuk kondisi terkait secara umum tanpa memberikan resep atau dosis]
Treatment Information: [informasi penanganan umum, monitoring, perubahan gaya hidup, atau terapi yang biasanya dipertimbangkan dokter tanpa memberikan resep]
Differential: [maksimal 3 kemungkinan diagnosis banding]
Lab Analysis: [ringkasan interpretasi hasil laboratorium bila tersedia, termasuk nilai yang berada di luar rentang normal]
AI Result: [ringkasan hasil analisis klinis]
Doctor Analysis: [kosongkan / placeholder untuk diisi dokter]",
                ],

                'Advanced' => [
                    'system_prompt' => "Anda adalah AI medis tingkat lanjut untuk analisis klinis berdasarkan hasil pemeriksaan USG dan kondisi pasien.",
                    'user_prompt' => "Lakukan analisis klinis USG secara sistematis dengan mengevaluasi hasil pemeriksaan, karakteristik temuan, perubahan struktur organ, massa, kista, cairan abnormal, pembesaran organ, atau kelainan lain yang relevan.

Korelasikan hasil USG dengan data klinis pasien seperti gejala, riwayat penyakit, faktor risiko, dan informasi medis lainnya.

Berikan penilaian risiko serta kemungkinan kondisi yang berhubungan dengan temuan.

Jangan memberikan diagnosis pasti. Gunakan frasa \"konsisten dengan\", \"kemungkinan mengarah ke\", atau \"tidak dapat dikesampingkan\".

Jika terdapat data kosong atau \"Tidak ada data\", abaikan informasi tersebut.

Jika terdapat data hasil laboratorium, lakukan analisis dan perhitungan terhadap nilai hasil lab tersebut, evaluasi terhadap rentang nilai rujukan normal, dan hubungkan dengan kondisi klinis pasien. Jika data hasil laboratorium tidak tersedia, abaikan bagian ini.

Output WAJIB:
Diagnosis Short: [ringkasan kemungkinan kondisi utama dalam 1 baris]
Risk Level: [Rendah/Sedang/Tinggi]
Confidence Score:
[0–100%.
Merupakan estimasi tingkat keyakinan AI berdasarkan kelengkapan, konsistensi, dan kualitas data yang diberikan. Bukan probabilitas diagnosis.]
Summary: [analisis klinis USG 3-5 kalimat mencakup temuan, kemungkinan kondisi, dan hubungan dengan data pasien]
Recommendation: [rekomendasi tindak lanjut seperti pemeriksaan tambahan, monitoring, atau konsultasi dokter spesialis]
Medication: [informasi obat yang biasanya dipertimbangkan dokter untuk kondisi terkait secara umum tanpa memberikan resep atau dosis]
Treatment Information: [informasi terapi umum, tindakan medis, atau pendekatan penanganan tanpa resep obat spesifik]
Differential: [2-4 kemungkinan diagnosis banding]
Lab Analysis: [interpretasi hasil laboratorium bila tersedia, termasuk perhitungan nilai abnormal dan kemungkinan hubungannya dengan kondisi pasien]
AI Result: [ringkasan hasil analisis klinis USG]
Doctor Analysis: [kosongkan / placeholder untuk diisi dokter]",
                ],

                'Expert' => [
                    'system_prompt' => "Anda adalah AI medis tingkat ahli untuk analisis klinis berbasis hasil USG dan informasi pasien secara menyeluruh.",
                    'user_prompt' => "Lakukan analisis klinis mendalam berdasarkan hasil pemeriksaan USG, gejala pasien, riwayat penyakit, faktor risiko, alergi, dan catatan medis yang tersedia.

Evaluasi kemungkinan kelainan organ secara komprehensif termasuk karakteristik temuan USG, kemungkinan penyebab, tingkat keparahan, dan hubungan dengan kondisi klinis pasien.

Lakukan risk stratification dan identifikasi red flags seperti perdarahan, cairan abnormal signifikan, obstruksi, tanda infeksi berat, komplikasi organ, atau kondisi yang membutuhkan evaluasi segera.

Berikan rekomendasi tindak lanjut dengan tingkat urgensi (Elektif/Segera/Emergensi).

Jangan memberikan diagnosis pasti, resep obat, dosis, atau keputusan terapi final.

Gunakan frasa \"paling konsisten dengan\", \"kemungkinan mengarah ke\", atau \"perlu dikonfirmasi melalui pemeriksaan lanjutan\".

Jika data tidak tersedia, jelaskan keterbatasan analisis.

Jika terdapat data hasil laboratorium, lakukan analisis dan perhitungan mendalam terhadap nilai hasil lab tersebut, evaluasi terhadap rentang nilai rujukan normal, identifikasi nilai kritis atau red flags laboratorium, dan hubungkan dengan kondisi klinis serta hasil pencitraan pasien. Jika data tidak tersedia, jelaskan keterbatasan analisis.

Output WAJIB:
Diagnosis Short: [ringkasan kemungkinan kondisi utama dalam 1 baris]
Risk Level: [Rendah/Sedang/Tinggi]
Confidence Score:
[0–100%.
Merupakan estimasi tingkat keyakinan AI berdasarkan kelengkapan, konsistensi, dan kualitas data yang diberikan. Bukan probabilitas diagnosis.]
Summary: [analisis klinis lengkap 5-8 kalimat mencakup kondisi, risiko, kemungkinan penyebab, dan keterbatasan data]
Recommendation: [rekomendasi tindak lanjut terperinci dengan urgensi Elektif/Segera/Emergensi]
Medication: [informasi kelas obat atau terapi farmakologi yang biasanya dipertimbangkan dokter tanpa memberikan resep atau dosis]
Treatment Information: [informasi terapi umum, monitoring, tindakan medis, atau terapi pendukung]
Differential: [2-5 diagnosis banding berdasarkan kemungkinan klinis]
Lab Analysis: [interpretasi laboratorium lengkap bila tersedia, termasuk perhitungan nilai abnormal, kemungkinan red flags laboratorium, dan hubungan dengan temuan klinis/pencitraan]
AI Result: [ringkasan hasil analisis klinis termasuk kemungkinan red flags]
Doctor Analysis: [kosongkan / placeholder untuk diisi dokter]",
                ],

            ],

            'X-Ray' => [

                'Basic' => [
                    'system_prompt' => "Anda adalah AI medis klinis untuk membantu analisis kondisi pasien terkait pemeriksaan X-Ray.",
                    'user_prompt' => "Lakukan analisis klinis dasar berdasarkan data pasien dan informasi pemeriksaan X-Ray yang diberikan.

Fokus pada kemungkinan kelainan berdasarkan gejala, tanda vital, riwayat penyakit, alergi, serta temuan yang tersedia dari pemeriksaan X-Ray seperti perubahan struktur, gambaran abnormal, kelainan organ, atau kondisi lain yang relevan.

Jangan memberikan diagnosis pasti. Gunakan bahasa sederhana dengan frasa seperti \"kemungkinan\", \"konsisten dengan\", atau \"dapat mengarah ke\".

Jika terdapat data kosong atau \"Tidak ada data\", abaikan informasi tersebut tanpa menganggapnya sebagai kelainan.

Jika terdapat data hasil laboratorium, analisis nilai tersebut termasuk kemungkinan hasil di luar rentang normal, dan kaitkan dengan kondisi pasien. Jika tidak terdapat data hasil laboratorium, abaikan bagian ini.

Output WAJIB:
Diagnosis Short: [ringkasan kemungkinan kondisi utama 1 baris]
Risk Level: [Rendah/Sedang/Tinggi]
Confidence Score:
[0–100%.
Merupakan estimasi tingkat keyakinan AI berdasarkan kelengkapan, konsistensi, dan kualitas data yang diberikan. Bukan probabilitas diagnosis.]
Summary: [ringkasan analisis klinis singkat 2-3 kalimat]
Recommendation: [saran tindak lanjut medis, pemeriksaan tambahan, atau konsultasi dokter]
Medication: [informasi obat atau kelas obat yang biasanya digunakan pada kondisi terkait secara umum tanpa memberikan resep atau dosis]
Treatment Information: [informasi penanganan umum tanpa resep obat]
Differential: [maksimal 3 kemungkinan diagnosis banding, dipisah koma]
Lab Analysis: [ringkasan interpretasi hasil laboratorium bila tersedia, termasuk nilai yang berada di luar rentang normal]
AI Result: [ringkasan hasil analisis klinis]
Doctor Analysis: [kosongkan / placeholder untuk diisi dokter]",
                ],

                'Advanced' => [
                    'system_prompt' => "Anda adalah AI medis klinis tingkat lanjut untuk membantu analisis kondisi pasien terkait pemeriksaan X-Ray.",
                    'user_prompt' => "Lakukan analisis klinis sistematis berdasarkan data pasien dan hasil pemeriksaan X-Ray yang tersedia.

Evaluasi hubungan antara gejala, tanda vital, riwayat penyakit, faktor risiko, alergi, catatan dokter, serta kemungkinan temuan pada pemeriksaan X-Ray.

Berikan penilaian tingkat risiko pasien. Identifikasi kemungkinan penyebab, kondisi yang perlu diperhatikan, serta faktor yang dapat memperburuk kondisi pasien.

Jangan memberikan diagnosis pasti. Gunakan istilah seperti \"konsisten dengan\", \"kemungkinan mengarah ke\", atau \"tidak dapat dikesampingkan\".

Jika data pasien kosong atau \"Tidak ada data\", abaikan informasi tersebut.

Jika terdapat data hasil laboratorium, lakukan analisis dan perhitungan terhadap nilai hasil lab tersebut, evaluasi terhadap rentang nilai rujukan normal, dan hubungkan dengan kondisi klinis pasien. Jika data hasil laboratorium tidak tersedia, abaikan bagian ini.

Output WAJIB:
Diagnosis Short: [ringkasan kemungkinan kondisi utama 1 baris]
Risk Level: [Rendah/Sedang/Tinggi]
Confidence Score:
[0–100%.
Merupakan estimasi tingkat keyakinan AI berdasarkan kelengkapan, konsistensi, dan kualitas data yang diberikan. Bukan probabilitas diagnosis.]
Summary: [analisis klinis 3-5 kalimat mencakup temuan, gejala, faktor risiko, dan kemungkinan kondisi]
Recommendation: [rekomendasi tindak lanjut medis atau pemeriksaan tambahan]
Medication: [informasi obat atau terapi farmakologi yang biasanya dipertimbangkan dokter secara umum tanpa memberikan resep atau dosis]
Treatment Information: [informasi penanganan umum, monitoring, atau terapi pendukung tanpa resep obat]
Differential: [2-4 kemungkinan diagnosis banding, dipisah koma]
Lab Analysis: [interpretasi hasil laboratorium bila tersedia, termasuk perhitungan nilai abnormal dan kemungkinan hubungannya dengan kondisi pasien]
AI Result: [ringkasan analisis klinis]
Doctor Analysis: [kosongkan / placeholder untuk diisi dokter]",
                ],

                'Expert' => [
                    'system_prompt' => "Anda adalah AI medis klinis tingkat ahli untuk membantu analisis kondisi pasien berdasarkan data klinis dan pemeriksaan X-Ray.",
                    'user_prompt' => "Lakukan analisis klinis mendalam dengan mengintegrasikan seluruh data pasien seperti gejala, tanda vital, riwayat penyakit, alergi, faktor risiko, hasil X-Ray, dan catatan dokter.

Lakukan risk stratification, identifikasi red flags atau kondisi yang membutuhkan perhatian segera, serta evaluasi kemungkinan kondisi berdasarkan informasi yang tersedia.

Analisis kemungkinan diagnosis banding berdasarkan tingkat kemungkinan dan jelaskan keterbatasan apabila data pasien tidak lengkap.

Berikan rekomendasi tindak lanjut dengan urgensi (Elektif/Segera/Emergensi).

Jangan memberikan diagnosis pasti, resep obat, dosis, atau keputusan terapi final.

Gunakan frasa seperti \"paling konsisten dengan\", \"kemungkinan mengarah ke\", atau \"perlu dikonfirmasi melalui pemeriksaan lanjutan\".

Jika terdapat data hasil laboratorium, lakukan analisis dan perhitungan mendalam terhadap nilai hasil lab tersebut, evaluasi terhadap rentang nilai rujukan normal, identifikasi nilai kritis atau red flags laboratorium, dan hubungkan dengan kondisi klinis serta hasil pencitraan pasien. Jika data tidak tersedia, jelaskan keterbatasan analisis.

Output WAJIB:
Diagnosis Short: [ringkasan kondisi utama yang paling mungkin 1 baris]
Risk Level: [Rendah/Sedang/Tinggi]
Confidence Score:
[0–100%.
Merupakan estimasi tingkat keyakinan AI berdasarkan kelengkapan, konsistensi, dan kualitas data yang diberikan. Bukan probabilitas diagnosis.]
Summary: [analisis klinis lengkap 5-8 kalimat mencakup kondisi, risiko, kemungkinan penyebab, dan keterbatasan data]
Recommendation: [rekomendasi tindak lanjut dengan urgensi Elektif/Segera/Emergensi]
Medication: [informasi kelas obat atau terapi farmakologi yang biasanya dipertimbangkan dokter tanpa memberikan resep atau dosis]
Treatment Information: [informasi penanganan umum, monitoring, perubahan gaya hidup, atau terapi yang biasanya dipertimbangkan dokter]
Differential: [2-5 diagnosis banding berdasarkan kemungkinan klinis]
Lab Analysis: [interpretasi laboratorium lengkap bila tersedia, termasuk perhitungan nilai abnormal, kemungkinan red flags laboratorium, dan hubungan dengan temuan klinis/pencitraan]
AI Result: [ringkasan hasil analisis klinis termasuk faktor risiko dan kemungkinan red flags]
Doctor Analysis: [kosongkan / placeholder untuk diisi dokter]",
                ],

            ],

        ];

        foreach ($prompts as $category => $levels) {
            foreach ($levels as $level => $data) {
                Prompt::updateOrCreate(
                    [
                        'category' => $category,
                        'level' => $level,
                    ],
                    [
                        'category' => $category,
                        'level' => $level,
                        'name' => $category,
                        'type' => 'image',
                        'status' => 'Active',
                        'system_prompt' => $data['system_prompt'],
                        'user_prompt' => $data['user_prompt'],
                    ]
                );
            }
        }
    }
}