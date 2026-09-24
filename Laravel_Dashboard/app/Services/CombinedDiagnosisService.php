<?php

namespace App\Services;

use App\Models\ClinicalAnalysis;
use App\Models\ImageAnalysis;
use App\Models\CombinedDiagnosis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class CombinedDiagnosisService
{
    /** Urutan tingkat risiko, buat perbandingan "mana yang lebih tinggi". */
    private const RISK_RANK = [
        'Rendah' => 1,
        'Sedang' => 2,
        'Tinggi' => 3,
    ];

    /**
     * Coba generate diagnosis gabungan untuk pasien tertentu.
     *
     * CATATAN ALUR: Method ini ORDER-AGNOSTIC — tidak peduli apakah dokter
     * melakukan Analisis Citra dulu baru Analisis Klinis, atau sebaliknya.
     * Yang diambil selalu ClinicalAnalysis TERBARU dan ImageAnalysis TERBARU
     * milik pasien ini (asal di hari yang sama), lalu dipasangkan. Jadi:
     *   - Citra dulu -> lalu Klinis disimpan -> method ini dipanggil dari
     *     AnalysisController::analyze() -> akan menemukan image yang sudah
     *     ada + clinical yang baru saja dibuat -> digabung.
     *   - Klinis dulu -> lalu Citra disimpan -> method ini HARUS JUGA
     *     dipanggil dari controller Analisis Citra (lihat catatan di bawah)
     *     -> akan menemukan clinical yang sudah ada + image yang baru saja
     *     dibuat -> digabung.
     *
     * PENTING: method ini juga harus dipanggil dari controller Analisis
     * Citra (mis. AnalysisPictureController@analyze) setelah image tersimpan,
     * persis seperti dipanggil dari AnalysisController@analyze setelah
     * clinical tersimpan. Kalau belum, tambahkan:
     *
     *     $combined = app(CombinedDiagnosisService::class)->tryGenerate($image->patient_id);
     *     if ($combined && $combined->status === 'done') {
     *         return redirect()->route('analysis-combined.result', $combined->id);
     *     }
     *
     * persis setelah ImageAnalysis::create(...) di controller citra.
     */
    public function tryGenerate(int $patientId): ?CombinedDiagnosis
    {
        $clinical = ClinicalAnalysis::where('patient_id', $patientId)
            ->orderByDesc('created_at')
            ->first();

        $image = ImageAnalysis::where('patient_id', $patientId)
            ->orderByDesc('created_at')
            ->first();

        // Kalau salah satu belum ada, belum bisa digabung — skip dulu.
        // Ini otomatis benar untuk kedua urutan: kalau baru ada citra
        // (klinis belum), atau baru ada klinis (citra belum), keduanya
        // sama-sama return null di sini sampai pasangannya lengkap.
        if (!$clinical || !$image) {
            return null;
        }

        // Hanya gabungkan kalau clinical & image dari HARI YANG SAMA
        // (satu episode/kunjungan yang sama). Kalau beda hari, jangan asal
        // pasangkan clinical terbaru dengan image terbaru — bisa dari
        // kunjungan yang tidak berhubungan sama sekali.
        $sameDay = $clinical->created_at->isSameDay($image->created_at);
        if (!$sameDay) {
            return null;
        }

        // Cek apakah kombinasi clinical+image ini sudah pernah diproses & sukses.
        $existing = CombinedDiagnosis::where('clinical_analysis_id', $clinical->id)
            ->where('image_analysis_id', $image->id)
            ->first();

        if ($existing && $existing->status === 'done') {
            return $existing;
        }

        $combined = $existing ?? new CombinedDiagnosis([
            'patient_id'            => $patientId,
            'clinical_analysis_id'  => $clinical->id,
            'image_analysis_id'     => $image->id,
        ]);
        $combined->status = 'pending';
        $combined->save();

        try {
            // Kalau dokter sudah mengoreksi diagnosis/risk level, pakai versi
            // dokter itu sebagai sumber yang dikirim ke FastAPI — bukan hasil
            // AI mentah yang mungkin sudah tidak akurat.
            $clinicalDiagnosis = $clinical->doctor_diagnosis ?: $clinical->diagnosis_short;
            $clinicalRisk      = $clinical->doctor_risk_level ?: $clinical->risk_level;

            $imageDiagnosis = $image->doctor_diagnosis ?: $image->diagnosis_short;
            $imageRisk      = $image->doctor_risk_level ?: $image->risk_level;

            $response = Http::timeout(600)->post('http://127.0.0.1:8001/analyze/combined', [
                // ── Output AI Klinis (interpretasi, bukan satu-satunya sumber) ──
                'clinical_diagnosis'         => $clinicalDiagnosis,
                'clinical_risk_level'        => $clinicalRisk,
                'clinical_confidence'        => $clinical->confidence_score,
                'clinical_summary'           => $clinical->summary,
                'clinical_differential'      => $clinical->differential,
                'clinical_recommendation'    => $clinical->recommendation,
                'clinical_medication'        => $clinical->medication,
                'clinical_doctor_diagnosis'  => $clinical->doctor_diagnosis,
                'clinical_validation_status' => $clinical->validation_status,

                // ── DATA MENTAH KLINIS (sumber asli, sebelum diringkas AI) ──
                // Ini penting agar AI combine bisa mengevaluasi ulang dari
                // data asli, bukan cuma dari ringkasan AI klinis yang bisa
                // saja melewatkan detail (mis. hasil lab, riwayat spesifik).
                'clinical_template_level'    => $clinical->template_level,
                'clinical_body_temperature'  => $clinical->body_temperature,
                'clinical_heart_rate'        => $clinical->heart_rate,
                'clinical_respiratory_rate'  => $clinical->respiratory_rate,
                'clinical_blood_pressure'    => $clinical->blood_pressure,
                'clinical_medical_history'   => $clinical->medical_history,
                'clinical_allergies'         => $clinical->allergies,
                'clinical_symptoms'          => $clinical->symptoms,
                'clinical_doctor_notes'      => $clinical->doctor_notes,
                'clinical_other_info'        => $clinical->other_info,

                // ── Output AI Citra ──
                'image_diagnosis'         => $imageDiagnosis,
                'image_risk_level'        => $imageRisk,
                'image_confidence'        => $image->confidence_score,
                'image_summary'           => $image->summary,
                'image_recommendation'    => $image->recommendation,
                'image_medication'        => $image->medication,
                'image_doctor_diagnosis'  => $image->doctor_diagnosis,
                'image_validation_status' => $image->validation_status,

                // ── DATA MENTAH CITRA ──
                'image_type'              => $image->image_type,
                'body_part'               => $image->body_part,
                'image_doctor_notes'      => $image->doctor_notes,
                'image_template_level'    => $image->template_level,
            ]);

            if ($response->failed()) {
                $combined->status    = 'failed';
                $combined->ai_result = [
                    'error'       => 'FastAPI merespons gagal.',
                    'status_code' => $response->status(),
                    'body'        => $response->body(),
                ];
                $combined->save();

                Log::error('CombinedDiagnosis gagal: respons FastAPI gagal', [
                    'patient_id' => $patientId,
                    'status'     => $response->status(),
                    'body'       => $response->body(),
                ]);

                return $combined;
            }

            $result = $response->json();

            // Normalisasi confidence, jaga-jaga FastAPI balikin string ("72"),
            // string persen ("72%"), atau desimal (0.72).
            $rawConfidence = $result['confidence'] ?? 0;
            if (is_string($rawConfidence)) {
                $rawConfidence = str_replace('%', '', $rawConfidence);
            }
            $confidence = floatval($rawConfidence);
            $confidence = ($confidence > 0 && $confidence <= 1.0)
                ? intval($confidence * 100)
                : intval($confidence);

            // Safety net — final_risk_level tidak boleh lebih rendah dari
            // risk level tertinggi di antara clinical & image (termasuk
            // koreksi dokter kalau ada).
            $finalRisk = $this->safeRiskLevel(
                $result['risk_level'] ?? 'Sedang',
                $clinicalRisk,
                $imageRisk,
            );

            $normalize = function ($value) {
                if (is_null($value)) return null;
                if (is_array($value)) return implode(', ', $value);
                return (string) $value;
            };

            $combined->final_diagnosis      = $result['diagnosis']      ?? null;
            $combined->final_risk_level     = $finalRisk;
            $combined->final_confidence     = $confidence;
            $combined->final_summary        = $result['summary']        ?? null;
            $combined->final_recommendation = $result['recommendation'] ?? null;
            $combined->confidence_reason       = $normalize($result['confidence_reason'] ?? null);
            $combined->treatment_information   = $normalize($result['treatment_information'] ?? null);
            $combined->potential_complications = $normalize($result['potential_complications'] ?? null);
            $combined->ai_result            = $result;
            $combined->status               = 'done';
            $combined->save();

            return $combined;

        } catch (Throwable $e) {
            $combined->status    = 'failed';
            $combined->ai_result = [
                'error' => $e->getMessage(),
            ];
            $combined->save();

            Log::error('CombinedDiagnosis exception: ' . $e->getMessage(), [
                'patient_id' => $patientId,
            ]);

            return $combined;
        }
    }

    /**
     * Paksa final_risk_level tidak pernah lebih rendah dari risiko tertinggi
     * di antara clinical & image. Ini jaring pengaman terakhir kalau AI/FastAPI
     * salah menyimpulkan risiko gabungan (mis. Tinggi + Sedang => harusnya
     * minimal Tinggi, bukan diturunkan jadi Sedang).
     */
    private function safeRiskLevel(string $aiFinalRisk, ?string $clinicalRisk, ?string $imageRisk): string
    {
        $maxRank = max(
            self::RISK_RANK[$clinicalRisk] ?? 0,
            self::RISK_RANK[$imageRisk] ?? 0,
        );
        $aiRank = self::RISK_RANK[$aiFinalRisk] ?? 0;

        if ($aiRank < $maxRank) {
            return array_search($maxRank, self::RISK_RANK, true);
        }

        return $aiFinalRisk;
    }
}