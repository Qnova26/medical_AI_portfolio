<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use App\Models\Patient;
use App\Models\ClinicalAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\CombinedDiagnosisService;
use App\Models\CombinedDiagnosis;

class AnalysisController extends Controller
{
    // Kategori prompt clinical saat ini fix hanya "Umum".
    // Kalau nanti nambah kategori lagi, kembalikan input category dari form
    // dan hapus konstanta ini.
    private const PROMPT_CATEGORY = 'Umum';

    public function create()
    {
        $promptsData = Prompt::where('status', 'Active')
                            ->where('type', 'clinical')
                            ->get(['category', 'level', 'system_prompt', 'user_prompt']);

        $patients = Patient::orderBy('created_at', 'desc')->get();

        $selectedPatientId = request('patient_id');

        return view('analysis.create', compact('promptsData', 'patients', 'selectedPatientId'));
    }

    // Ekstensi yang bisa dibaca langsung sebagai gambar oleh AI vision.
    // File di luar ini (mis. .dcm) tetap disimpan sebagai berkas medis,
    // tapi dilewati dari analisis lab karena modelnya gak bisa baca formatnya.
    private const LAB_READABLE_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];

    public function analyze(Request $request)
    {
        $request->validate([
            'patient_id'                    => 'required|exists:patients,id',
            'template_level'                => 'required|in:Basic,Advanced,Expert',
            'vital_signs.body_temperature'  => 'nullable|string',
            'vital_signs.heart_rate'        => 'nullable|string',
            'vital_signs.respiratory_rate'  => 'nullable|string',
            'vital_signs.blood_pressure'    => 'nullable|string',
            'medical_history'               => 'nullable|string',
            'allergies'                      => 'nullable|string',
            'symptoms'                       => 'nullable|string',
            'doctor_notes'                   => 'nullable|string',
            'other_info'                     => 'nullable|string',
            'medical_files.*'                => 'nullable|file|max:10240',
        ]);

        $prompt = Prompt::where('category', self::PROMPT_CATEGORY)
                        ->where('level', $request->template_level)
                        ->where('type', 'clinical')
                        ->where('status', 'Active')
                        ->firstOrFail();

        // ── Unggah Berkas Medis ────────────────────────────────────────
        // $filePaths       -> semua file disimpan seperti biasa ke DB (medical_files).
        // $labFilePaths    -> subset file yang formatnya bisa dibaca AI (pdf/jpg/png),
        //                     dipakai lagi buat ditampilkan di card "Hasil Analisis Lab".
        // $labFilesPayload -> isi file yang sama (base64), dikirim ke AI buat dianalisis.
        $filePaths       = [];
        $labFilePaths    = [];
        $labFilesPayload = [];

        if ($request->hasFile('medical_files')) {
            foreach ($request->file('medical_files') as $file) {
                $storedPath  = $file->store('medical_records', 'public');
                $filePaths[] = $storedPath;

                $extension = strtolower($file->getClientOriginalExtension());
                if (in_array($extension, self::LAB_READABLE_EXTENSIONS, true)) {
                    $labFilePaths[] = $storedPath;

                    $labFilesPayload[] = [
                        'filename'  => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'data'      => base64_encode(file_get_contents($file->getRealPath())),
                    ];
                }
            }
        }

        $patient = Patient::findOrFail($request->patient_id);

        $response = Http::timeout(600)->post('http://127.0.0.1:8001/analyze/clinical', [
            'system_prompt'    => $prompt->system_prompt,
            'user_prompt'      => $prompt->user_prompt,
            'patient_name'     => $patient->nama_pasien,
            'template_level'   => $request->template_level,
            'body_temperature' => $request->input('vital_signs.body_temperature'),
            'heart_rate'       => $request->input('vital_signs.heart_rate'),
            'respiratory_rate' => $request->input('vital_signs.respiratory_rate'),
            'blood_pressure'   => $request->input('vital_signs.blood_pressure'),
            'medical_history'  => $request->medical_history,
            'allergies'        => $request->allergies,
            'symptoms'         => $request->symptoms,
            'doctor_notes'     => $request->doctor_notes,
            'other_info'       => $request->other_info,
            'lab_files'        => $labFilesPayload,
        ]);

        if ($response->failed()) {
            return back()->withErrors(['ai' => 'Error: ' . $response->body()])->withInput();
        }

        $aiResult = $response->json();

        $normalize = function ($value) {
            if (is_null($value)) return null;
            if (is_array($value)) return implode(', ', $value);
            return (string) $value;
        };

        $normalizeConf = function ($val) {
            if (is_string($val)) $val = str_replace('%', '', $val);
            $f = floatval($val);
            if ($f > 0 && $f <= 1.0) return intval($f * 100);
            return intval($f);
        };

        $analysis = ClinicalAnalysis::create([
            'patient_id'        => $request->patient_id,
            'prompt_id'         => $prompt->id,
            'template_level'    => $request->template_level,
            'body_temperature'  => $request->input('vital_signs.body_temperature'),
            'heart_rate'        => $request->input('vital_signs.heart_rate'),
            'respiratory_rate'  => $request->input('vital_signs.respiratory_rate'),
            'blood_pressure'    => $request->input('vital_signs.blood_pressure'),
            'medical_history'   => $request->medical_history,
            'allergies'         => $request->allergies,
            'symptoms'          => $request->symptoms,
            'doctor_notes'      => $request->doctor_notes,
            'other_info'        => $request->other_info,
            'medical_files'     => $filePaths,
            'lab_files'         => $labFilePaths,
            // Kalau gak ada lab_files yang dikirim, AI gak akan balikin key 'lab_analysis',
            // jadi otomatis ke-null -> di view ditampilkan "Tidak ada input data lab".
            'lab_analysis'      => $normalize($aiResult['lab_analysis']    ?? null),
            'diagnosis_short'   => $normalize($aiResult['diagnosis']       ?? null),
            'risk_level'        => $normalize($aiResult['risk_level']      ?? null),
            'confidence_score'  => $normalizeConf($aiResult['confidence']  ?? 0),
            'confidence_reason' => $normalize($aiResult['confidence_reason'] ?? null),
            'summary'           => $normalize($aiResult['summary']         ?? null),
            'recommendation'    => $normalize($aiResult['recommendation']  ?? null),
            'differential'      => $normalize($aiResult['differential']    ?? null),
            'medication'        => $normalize($aiResult['medication']      ?? null),
            'treatment_information' => $normalize($aiResult['treatment_information'] ?? null),
            'potential_complications' => $normalize($aiResult['potential_complications'] ?? null),
            'needs_imaging_correlation'  => filter_var($aiResult['needs_imaging_correlation'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'imaging_correlation_reason' => $normalize($aiResult['imaging_correlation_reason'] ?? null),
            'ai_result'         => $aiResult,
        ]);

        $combined = app(CombinedDiagnosisService::class)->tryGenerate($analysis->patient_id);

        if ($combined && $combined->status === 'done') {
            return redirect()->route('analysis-combined.result', $combined->id);
        }

        return redirect()->route('analysis.result', $analysis->id);
    }

    public function result($id)
    {
        $analysis = ClinicalAnalysis::with(['patient', 'prompt'])->findOrFail($id);
        $result   = $analysis->ai_result;

        $combined = CombinedDiagnosis::where('clinical_analysis_id', $analysis->id)
                        ->where('status', 'done')
                        ->latest()
                        ->first();

        return view('analysis.result', compact('analysis', 'result', 'combined'));
    }

    public function confirm(Request $request, $id)
    {
        $request->validate([
            'doctor_risk_level'         => 'required|in:Rendah,Sedang,Tinggi',
            'medication_recommendation' => 'nullable|string',
            'doctor_notes_validation'   => 'nullable|string',
        ], [
            'doctor_risk_level.required' => 'Tingkat risiko wajib dipilih dokter saat menyetujui diagnosis.',
            'doctor_risk_level.in'       => 'Tingkat risiko harus salah satu dari Rendah, Sedang, atau Tinggi.',
        ]);

        ClinicalAnalysis::findOrFail($id)->update([
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

        ClinicalAnalysis::findOrFail($id)->update([
            'validation_status'         => 'corrected',
            'doctor_analysis'           => $request->doctor_notes_validation,
            'doctor_diagnosis'          => $request->doctor_diagnosis,
            'doctor_risk_level'         => $request->doctor_risk_level,
            'medication_recommendation' => $request->medication_recommendation,
        ]);

        return redirect()->route('analysis-history.index')->with('success', 'Diagnosis telah dikoreksi oleh dokter.');
    }
}