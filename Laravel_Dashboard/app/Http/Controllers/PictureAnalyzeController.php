<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use App\Models\Patient;
use App\Models\ImageAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\CombinedDiagnosisService;
use App\Models\CombinedDiagnosis;

class PictureAnalyzeController extends Controller
{
    public function create()
    {
        $categories = Prompt::where('status', 'Active')
                            ->where('type', 'image')
                            ->distinct()
                            ->orderBy('category')
                            ->pluck('category');

        $patients = Patient::orderBy('created_at', 'desc')->get();

        return view('analysis-picture.create', compact('categories', 'patients'));
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'category'       => 'required|string',
            'template_level' => 'required|in:Basic,Advanced,Expert',
            'body_part'      => 'nullable|string|max:100',
            'doctor_notes'   => 'nullable|string',
            'image_files'    => 'required',
            'image_files.*'  => 'file|mimes:jpg,jpeg,png,dcm|max:20480',
        ]);

        $prompt = Prompt::where('category', $request->category)
                        ->where('level', $request->template_level)
                        ->where('type', 'image')
                        ->where('status', 'Active')
                        ->firstOrFail();

        $imageType = $request->category;

        $imagePaths = [];
        foreach ($request->file('image_files') as $file) {
            $imagePaths[] = $file->store('medical_images', 'public');
        }

        $firstImagePath = $imagePaths[0];
        $imageBase64    = base64_encode(Storage::disk('public')->get($firstImagePath));

        $response = Http::timeout(600)->post('http://127.0.0.1:8001/analyze/image', [
            'system_prompt'  => $prompt->system_prompt,
            'image_base64'   => $imageBase64,
            'image_type'     => $imageType,
            'body_part'      => $request->body_part,
            'doctor_notes'   => $request->doctor_notes,
            'template_level' => $request->template_level,
        ]);

        if ($response->failed()) {
            return back()->withErrors(['ai' => 'Gagal menghubungi layanan AI. Coba lagi.']);
        }

        $aiResult = $response->json();

        if (!empty($aiResult['converted_image'])) {
            $convertedPath = 'medical_images/converted_' . time() . '_' . basename($firstImagePath) . '.jpg';
            Storage::disk('public')->put($convertedPath, base64_decode($aiResult['converted_image']));
            $imagePaths = [$convertedPath];
        }

        $resultImages = $imagePaths;
        if (!empty($aiResult['annotated_image'])) {
            $annotatedPath = 'medical_images/annotated_' . time() . '_' . basename($firstImagePath) . '.jpg';
            Storage::disk('public')->put($annotatedPath, base64_decode($aiResult['annotated_image']));
            $resultImages = [$annotatedPath];
        }

        $normalize = function ($value) {
            if (is_array($value)) return implode(', ', $value);
            return $value;
        };

        $normalizeConf = function ($val) {
            if (is_string($val)) $val = str_replace('%', '', $val);
            $f = floatval($val);
            if ($f > 0 && $f <= 1.0) return intval($f * 100);
            return intval($f);
        };

        $analysis = ImageAnalysis::create([
            'patient_id'        => $request->patient_id,
            'prompt_id'         => $prompt->id,
            'template_level'    => $request->template_level,
            'image_type'        => $imageType,
            'body_part'         => $request->body_part,
            'doctor_notes'      => $request->doctor_notes,
            'image_files'       => $imagePaths,
            'result_images'     => $resultImages,
            'diagnosis_short'   => $normalize($aiResult['diagnosis']   ?? '-'),
            'risk_level'        => $normalize($aiResult['risk_level']  ?? '-'),
            'confidence_score'  => $normalizeConf($aiResult['confidence'] ?? 0),
            'confidence_reason' => $normalize($aiResult['confidence_reason'] ?? null),
            'summary'           => $normalize($aiResult['summary']     ?? '-'),
            'differential'      => $normalize($aiResult['differential']?? '-'),
            'recommendation'    => $normalize($aiResult['recommendation'] ?? '-'),
            'medication'        => $normalize($aiResult['medication']      ?? '-'),
            // ── field baru ──
            'needs_clinical_correlation'  => filter_var($aiResult['needs_clinical_correlation'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'clinical_correlation_reason' => $normalize($aiResult['clinical_correlation_reason'] ?? null),
            'ai_result'         => $aiResult,
        ]);

        $combined = app(CombinedDiagnosisService::class)->tryGenerate($analysis->patient_id);

        if ($combined && $combined->status === 'done') {
            return redirect()->route('analysis-combined.result', $combined->id);
        }

        return redirect()->route('analysis-picture.result', $analysis->id);
    }

    public function result($id)
    {
        $analysis = ImageAnalysis::with(['patient', 'prompt'])->findOrFail($id);
        $result   = $analysis->ai_result;

        $combined = CombinedDiagnosis::where('image_analysis_id', $analysis->id)
                        ->where('status', 'done')
                        ->latest()
                        ->first();

        return view('analysis-picture.result', compact('analysis', 'result', 'combined'));
    }

    public function confirm(Request $request, $id)
    {
        // Wajib: tingkat risiko final dari dokter. Obat & catatan opsional.
        $request->validate([
            'doctor_risk_level'         => 'required|in:Rendah,Sedang,Tinggi',
            'medication_recommendation' => 'nullable|string',
            'doctor_notes_validation'   => 'nullable|string',
        ], [
            'doctor_risk_level.required' => 'Tingkat risiko wajib dipilih dokter saat menyetujui diagnosis.',
            'doctor_risk_level.in'       => 'Tingkat risiko harus salah satu dari Rendah, Sedang, atau Tinggi.',
        ]);

        ImageAnalysis::findOrFail($id)->update([
            'validation_status'         => 'confirmed',
            'doctor_risk_level'         => $request->doctor_risk_level,
            'medication_recommendation' => $request->medication_recommendation,
            'doctor_analysis'           => $request->doctor_notes_validation,
        ]);

        return redirect()->route('analysis-history.index')->with('success', 'Diagnosis berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'doctor_notes_validation'   => 'required|string',
            'doctor_diagnosis'          => 'required|string',
            'doctor_risk_level'         => 'required|in:Rendah,Sedang,Tinggi',
            'medication_recommendation' => 'nullable|string',
        ], [
            'doctor_notes_validation.required' => 'Catatan koreksi wajib diisi saat menolak diagnosis AI.',
            'doctor_diagnosis.required'         => 'Diagnosis versi dokter wajib diisi saat menolak diagnosis AI.',
            'doctor_risk_level.required'        => 'Tingkat risiko wajib dipilih dokter saat menolak diagnosis.',
            'doctor_risk_level.in'              => 'Tingkat risiko harus salah satu dari Rendah, Sedang, atau Tinggi.',
        ]);

        ImageAnalysis::findOrFail($id)->update([
            'validation_status'         => 'corrected',
            'doctor_analysis'           => $request->doctor_notes_validation,
            'doctor_diagnosis'          => $request->doctor_diagnosis,
            'doctor_risk_level'         => $request->doctor_risk_level,
            'medication_recommendation' => $request->medication_recommendation,
        ]);

        return redirect()->route('analysis-history.index')->with('success', 'Diagnosis telah dikoreksi oleh dokter.');
    }

    public function downloadPdf($id)
    {
        $analysis = ImageAnalysis::with('patient')->findOrFail($id);
        $result   = $analysis->ai_result;
        $pdf      = \Barryvdh\DomPDF\Facade\Pdf::loadView('analysis.pdf-picture', compact('analysis', 'result'));
        return $pdf->download('analisis-citra-' . $analysis->id . '.pdf');
    }
}