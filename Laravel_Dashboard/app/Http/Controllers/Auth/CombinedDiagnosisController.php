<?php

namespace App\Http\Controllers;

use App\Models\CombinedDiagnosis;
use App\Models\Patient;
use App\Services\CombinedDiagnosisService;
use Illuminate\Http\Request;

class CombinedDiagnosisController extends Controller
{
    public function index()
    {
        $combined = CombinedDiagnosis::with(['patient', 'clinicalAnalysis', 'imageAnalysis'])
            ->latest()
            ->paginate(20);

        return view('combined-diagnosis.index', compact('combined'));
    }

    public function show(CombinedDiagnosis $combinedDiagnosis)
    {
        $combinedDiagnosis->load(['patient', 'clinicalAnalysis', 'imageAnalysis']);
        return view('combined-diagnosis.show', compact('combinedDiagnosis'));
    }

    // Trigger manual jika butuh regenerasi
    public function regenerate(int $patientId)
    {
        // Hapus combined lama dulu
        $clinical = \App\Models\ClinicalAnalysis::where('patient_id', $patientId)->latest()->first();
        $image    = \App\Models\ImageAnalysis::where('patient_id', $patientId)->latest()->first();

        if ($clinical && $image) {
            CombinedDiagnosis::where('clinical_analysis_id', $clinical->id)
                ->where('image_analysis_id', $image->id)
                ->delete();
        }

        $result = app(CombinedDiagnosisService::class)->tryGenerate($patientId);

        return back()->with(
            $result?->status === 'done' ? 'success' : 'error',
            $result?->status === 'done' ? 'Diagnosis final berhasil diperbarui.' : 'Gagal menganalisis.'
        );
    }
}