<?php

namespace App\Http\Controllers;

use App\Models\ClinicalAnalysis;
use App\Models\CombinedDiagnosis;
use App\Models\ImageAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnalysisHistoryController extends Controller
{
    public function index()
    {
        $clinicals = ClinicalAnalysis::with(['patient', 'prompt'])->orderBy('created_at', 'desc')->get();
        $images    = ImageAnalysis::with(['patient', 'prompt'])->orderBy('created_at', 'desc')->get();

        $combineds = CombinedDiagnosis::with(['clinicalAnalysis.patient', 'clinicalAnalysis.prompt',
                                               'imageAnalysis.patient', 'imageAnalysis.prompt'])
            ->orderBy('created_at', 'desc')
            ->get();

        $histories = collect();
        $usedClinicalIds = [];
        $usedImageIds = [];

        // 1) Gabungkan Klinis dan Citra secara otomatis jika Pasien dan Hari (Tanggal) sama
        foreach ($clinicals as $clinical) {
            $dateClin = $clinical->created_at->format('Y-m-d');

            // Cari ImageAnalysis yang pasiennya sama dan harinya sama
            $image = $images->first(function($img) use ($clinical, $dateClin, $usedImageIds) {
                return $img->patient_id === $clinical->patient_id
                    && $img->created_at->format('Y-m-d') === $dateClin
                    && !in_array($img->id, $usedImageIds);
            });

            if ($image) {
                // Pasangan ketemu
                $usedClinicalIds[] = $clinical->id;
                $usedImageIds[] = $image->id;

                // Cek apakah data combined di database sudah ada
                $combined = $combineds->first(function($c) use ($clinical, $image) {
                    return $c->clinical_analysis_id == $clinical->id || $c->image_analysis_id == $image->id;
                });

                $histories->push($this->buildRow($clinical, $image, $combined));
            } else {
                // Tidak ada pasangan image di hari yang sama
                // Cek apakah record ini bagian dari combined (misal image-nya beda hari tapi dipasangkan manual)
                $combined = $combineds->firstWhere('clinical_analysis_id', $clinical->id);
                if ($combined && $combined->imageAnalysis) {
                    if (!in_array($combined->image_analysis_id, $usedImageIds)) {
                        $usedImageIds[] = $combined->image_analysis_id;
                    }
                    $histories->push($this->buildRow($clinical, $combined->imageAnalysis, $combined));
                } else {
                    $histories->push($this->buildRow($clinical, null, $combined));
                }
                $usedClinicalIds[] = $clinical->id;
            }
        }

        // 2) Masukkan sisa ImageAnalysis yang belum terpasangkan
        foreach ($images as $image) {
            if (!in_array($image->id, $usedImageIds)) {
                $combined = $combineds->firstWhere('image_analysis_id', $image->id);
                if ($combined && $combined->clinicalAnalysis && !in_array($combined->clinical_analysis_id, $usedClinicalIds)) {
                    // Jika ada combined manual
                    $usedClinicalIds[] = $combined->clinical_analysis_id;
                    $histories->push($this->buildRow($combined->clinicalAnalysis, $image, $combined));
                } else {
                    $histories->push($this->buildRow(null, $image, $combined));
                }
            }
        }

        $histories = $histories->sortByDesc('date_display')->values();

        $reviewed    = $histories->filter(fn($h) => $h['final_status'] !== 'pending');
        $confirmed   = $histories->filter(fn($h) => $h['final_status'] === 'confirmed');
        $successRate = $reviewed->count()
            ? round($confirmed->count() / $reviewed->count() * 100) : 0;

        $summary = [
            'total_analysis' => $histories->count(),
            'today_analysis' => $histories->filter(fn($h) =>
                str_starts_with($h['date_display'], now()->format('Y-m-d'))
            )->count(),
            'avg_confidence' => $histories->count()
                ? round($histories->avg('avg_confidence')) : 0,
            'success_rate'   => $successRate,
        ];

        return view('analysis-history.index', compact('summary', 'histories'));
    }

    /**
     * Bangun 1 baris riwayat gabungan dari data klinis, citra, dan combined.
     *
     * ATURAN STATUS VALIDASI FINAL:
     * - Jika HANYA ada 1 jenis analisis (klinis saja ATAU citra saja):
     *   status final mengikuti validation_status analisis tunggal itu.
     * - Jika ADA KEDUANYA (klinis DAN citra dipasangkan):
     *   status final HANYA mengikuti validation_status pada CombinedDiagnosis.
     *   Validasi individual klinis/citra tidak lagi relevan untuk status final,
     *   karena begitu dipasangkan, keputusan final ada di diagnosis gabungan.
     *   Jika combined belum ada / belum diproses / belum divalidasi -> 'pending'.
     */
    private function buildRow(?ClinicalAnalysis $clinical, ?ImageAnalysis $image, ?CombinedDiagnosis $combined)
    {
        $patientName = $clinical?->patient->nama_pasien
                    ?? $image?->patient->nama_pasien ?? '-';

        $date = ($combined?->created_at ?? $clinical?->created_at ?? $image?->created_at)
                    ?->format('Y-m-d H:i') ?? '-';

        $clinicalConfidence = $clinical?->confidence_score ?? 0;
        $imageConfidence    = $image?->confidence_score ?? 0;
        $confidenceValues   = array_filter([$clinicalConfidence, $imageConfidence]);
        $avgConfidence      = count($confidenceValues)
            ? round(array_sum($confidenceValues) / count($confidenceValues)) : 0;

        $clinStatus = $clinical?->validation_status ?? 'pending';
        $imgStatus  = $image?->validation_status ?? 'pending';

        $isPaired = $clinical && $image; // Ada klinis DAN citra -> ini kasus "combined"

        if ($isPaired) {
            // Kasus 2 analisis: status final HANYA ikut validasi combined.
            $finalStatus = $combined?->validation_status ?? 'pending';
        } else {
            // Kasus 1 analisis: status final ikut validasi analisis tunggal itu.
            $finalStatus = $clinical ? $clinStatus : ($image ? $imgStatus : 'pending');
        }

        $clinicalFiles = [];
        if ($clinical && !empty($clinical->medical_files)) {
            $files = is_string($clinical->medical_files)
                ? json_decode($clinical->medical_files, true)
                : $clinical->medical_files;
            foreach ((array) $files as $path) {
                $clinicalFiles[] = [
                    'name' => basename($path),
                    'url'  => asset('storage/' . $path),
                ];
            }
        }

        $imageFiles = [];
        if ($image && !empty($image->image_files)) {
            $files = is_string($image->image_files)
                ? json_decode($image->image_files, true)
                : $image->image_files;
            foreach ((array) $files as $path) {
                $imageFiles[] = [
                    'name' => basename($path),
                    'url'  => asset('storage/' . $path),
                ];
            }
        }

        $imageResultImages = [];
        if ($image && !empty($image->result_images)) {
            $files = is_string($image->result_images)
                ? json_decode($image->result_images, true)
                : $image->result_images;
            foreach ((array) $files as $path) {
                $imageResultImages[] = [
                    'name' => basename($path),
                    'url'  => asset('storage/' . $path),
                ];
            }
        }

        $aiResult = is_string($clinical?->ai_result)
            ? json_decode($clinical->ai_result, true)
            : ($clinical?->ai_result ?? []);

        $imageAiResult = is_string($image?->ai_result)
            ? json_decode($image->ai_result, true)
            : ($image?->ai_result ?? []);

        $combinedAiResult = is_array($combined?->ai_result)
            ? $combined->ai_result
            : (is_string($combined?->ai_result) ? json_decode($combined->ai_result, true) : []);

        // Jika digabungkan tapi belum ada data AI (CombinedDiagnosis tidak ada di database)
        if (!$combined && $clinical && $image) {
            $mockDiag = $clinical->diagnosis_short . ' (Klinis) & ' . $image->diagnosis_short . ' (Citra)';
            $mockSumm = "Berdasarkan analisis silang, data klinis menunjukkan indikasi {$clinical->diagnosis_short}, sementara citra medis mengarah pada {$image->diagnosis_short}. Kombinasi kedua data ini memperkuat perlunya penanganan segera.";

            $riskArr = [$clinical->risk_level, $image->risk_level];
            $mockRisk = in_array('Tinggi', $riskArr) ? 'Tinggi' : (in_array('Sedang', $riskArr) ? 'Sedang' : 'Rendah');

            $combStatus         = 'done'; // Agar muncul di UI sebagai diagnosis gabungan sementara
            $combDiagnosis      = $mockDiag;
            $combRisk           = $mockRisk;
            $combConfidence     = $avgConfidence;
            $combSummary        = $mockSumm;
            $combRecommendation = "Tindak lanjuti dengan rencana pengobatan komprehensif berdasarkan temuan klinis dan radiologis.";
            $combCorrelation    = "Korelasi kuat ditemukan secara otomatis karena waktu pemeriksaan (hari yang sama).";
            $combValidation     = 'pending'; // Belum ada record combined asli -> belum ada validasi dokter
        } else {
            $combStatus         = $combined?->status ?? 'none';
            $combDiagnosis      = $combined?->final_diagnosis ?? '';
            $combRisk           = $combined?->final_risk_level ?? '';
            $combConfidence     = $combined?->final_confidence ?? 0;
            $combSummary        = $combined?->final_summary ?? '';
            $combRecommendation = $combined?->final_recommendation ?? '';
            $combCorrelation    = $combinedAiResult['correlation'] ?? '';
            $combValidation     = $combined?->validation_status ?? 'pending';
        }

        return [
            'patient'        => $patientName,
            'date_display'   => $date,
            'avg_confidence' => $avgConfidence,
            'is_analyzed'    => $finalStatus !== 'pending',
            'final_status'   => $finalStatus,

            'patient_id'        => $clinical?->patient_id ?? $image?->patient_id,
            'combined_id'       => $combined?->id ?? '',

            // Clinical
            'clinical_id'               => $clinical ? 'CLN-' . str_pad($clinical->id, 3, '0', STR_PAD_LEFT) : '',
            'clinical_raw_id'           => $clinical?->id ?? '',
            'clinical_diagnosis'        => $clinical?->diagnosis_short ?? '',
            'clinical_risk_level'       => $clinical?->risk_level ?? '',
            'clinical_prompt'           => $clinical?->prompt->name ?? '',
            'clinical_template_level'   => $clinical?->template_level ?? '',
            'clinical_penjelasan'       => $aiResult['summary'] ?? '',
            'clinical_differential'     => is_array($aiResult['differential'] ?? '') ? implode(', ', $aiResult['differential']) : ($aiResult['differential'] ?? ''),
            'clinical_recommendation'   => is_array($aiResult['recommendation'] ?? '') ? implode(', ', $aiResult['recommendation']) : ($aiResult['recommendation'] ?? ''),
            'clinical_analisis_dokter'  => $clinical?->doctor_analysis ?? '',
            'clinical_doctor_diagnosis' => $clinical?->doctor_diagnosis ?? '',
            'clinical_validation'       => $clinStatus,
            'clinical_confidence'       => $clinicalConfidence,
            'clinical_files'            => json_encode($clinicalFiles),
            'clinical_doctor_risk_level'=> $clinical?->doctor_risk_level ?? '',
            'clinical_medication'       => is_array($aiResult['medication_recommendation'] ?? $aiResult['medication'] ?? '') ? implode(', ', $aiResult['medication_recommendation'] ?? $aiResult['medication'] ?? '') : ($aiResult['medication_recommendation'] ?? $aiResult['medication'] ?? ''),
            'clinical_medication_doctor'=> $clinical?->medication_recommendation ?? '',

            // Vital signs
            'body_temperature' => $clinical?->body_temperature ?? '',
            'heart_rate'       => $clinical?->heart_rate ?? '',
            'respiratory_rate' => $clinical?->respiratory_rate ?? '',
            'blood_pressure'   => $clinical?->blood_pressure ?? '',
            'medical_history'  => $clinical?->medical_history ?? '',
            'allergies'        => $clinical?->allergies ?? '',
            'symptoms'         => $clinical?->symptoms ?? '',
            'doctor_notes'     => $clinical?->doctor_notes ?? '',
            'other_info'       => $clinical?->other_info ?? '',

            // Image
            'image_id'               => $image ? 'IMG-' . str_pad($image->id, 3, '0', STR_PAD_LEFT) : '',
            'image_raw_id'           => $image?->id ?? '',
            'image_diagnosis'        => $image?->diagnosis_short ?? '',
            'image_risk_level'       => $image?->risk_level ?? '',
            'image_type'             => $image?->image_type ?? '',
            'image_body_part'        => $image?->body_part ?? '',
            'image_prompt'           => $image?->prompt->name ?? '',
            'image_penjelasan'       => $imageAiResult['clinical_notes'] ?? $imageAiResult['summary'] ?? '',
            'image_recommendation'   => is_array($imageAiResult['recommendation'] ?? '') ? implode(', ', $imageAiResult['recommendation']) : ($imageAiResult['recommendation'] ?? ''),
            'image_analisis_dokter'  => $image?->doctor_analysis ?? '',
            'image_doctor_diagnosis' => $image?->doctor_diagnosis ?? '',
            'image_validation'       => $imgStatus,
            'image_confidence'       => $imageConfidence,
            'image_files'            => json_encode($imageFiles),
            'image_result_images'    => json_encode($imageResultImages),
            'image_doctor_risk_level'=> $image?->doctor_risk_level ?? '',
            'image_medication'       => is_array($imageAiResult['medication_recommendation'] ?? $imageAiResult['medication'] ?? '') ? implode(', ', $imageAiResult['medication_recommendation'] ?? $imageAiResult['medication'] ?? '') : ($imageAiResult['medication_recommendation'] ?? $imageAiResult['medication'] ?? ''),
            'image_medication_doctor'=> $image?->medication_recommendation ?? '',

            // Combined
            'combined_status'         => $combStatus,
            'combined_diagnosis'      => $combDiagnosis,
            'combined_risk_level'     => $combRisk,
            'combined_confidence'     => $combConfidence,
            'combined_summary'        => $combSummary,
            'combined_recommendation' => $combRecommendation,
            'combined_correlation'    => $combCorrelation,
            'combined_validation'     => $combValidation,
        ];
    }

    public function storeCombined(Request $request)
    {
        $request->validate([
            'patient_id'            => 'required|exists:patients,id',
            'clinical_analysis_id'  => 'required|exists:clinical_analyses,id',
            'image_analysis_id'     => 'required|exists:image_analyses,id',
        ]);

        $combined = CombinedDiagnosis::firstOrCreate(
            [
                'clinical_analysis_id' => $request->clinical_analysis_id,
                'image_analysis_id'    => $request->image_analysis_id,
            ],
            [
                'patient_id' => $request->patient_id,
                'status'     => 'pending',
            ]
        );

        return redirect()->route('analysis-history.index')
            ->with('success', 'Analisis klinis dan citra berhasil dipasangkan. Sedang diproses AI.');
    }

    public function validateCombined(Request $request)
    {
        if ($request->filled('clinical_id') && $request->filled('clinical_validation')) {
            ClinicalAnalysis::where('id', $request->clinical_id)->update([
                'validation_status' => $request->clinical_validation,
                'doctor_diagnosis'  => $request->doctor_diagnosis,
            ]);
        }

        if ($request->filled('image_id') && $request->filled('image_validation')) {
            ImageAnalysis::where('id', $request->image_id)->update([
                'validation_status' => $request->image_validation,
                'doctor_diagnosis'  => $request->doctor_diagnosis,
            ]);
        }

        return redirect()->route('analysis-history.index')->with('success', 'Penilaian medis gabungan berhasil disimpan!');
    }

    public function updateAnalisis(Request $request, $type, $id)
    {
        $request->validate([
            'analisis_dokter'           => 'required|string|max:2000',
            'validation_status'         => 'required|in:confirmed,corrected',
            'doctor_diagnosis'          => 'nullable|string|max:255',
            'doctor_risk_level'         => 'nullable|in:Rendah,Sedang,Tinggi',
            'medication_recommendation' => 'nullable|string|max:2000',
        ]);

        $analysis = $type === 'clinical'
            ? ClinicalAnalysis::findOrFail($id)
            : ImageAnalysis::findOrFail($id);

        $analysis->update([
            'doctor_analysis'           => $request->analisis_dokter,
            'validation_status'         => $request->validation_status,
            'doctor_diagnosis'          => $request->doctor_diagnosis,
            'doctor_risk_level'         => $request->doctor_risk_level,
            'medication_recommendation' => $request->medication_recommendation,
        ]);

        return redirect()->back()->with('success', 'Validasi & Analisis Dokter berhasil disimpan.');
    }
}