<?php

namespace App\Http\Controllers;

use App\Models\CombinedDiagnosis;
use Illuminate\Http\Request;

class CombinedDiagnosisController extends Controller
{
    public function result($id)
    {
        $combined = CombinedDiagnosis::with(['patient', 'clinicalAnalysis', 'imageAnalysis'])->findOrFail($id);
        $result   = $combined->ai_result;
        return view('analysis-combined.result', compact('combined', 'result'));
    }

    public function confirm(Request $request, $id)
    {
        // Wajib: tingkat risiko final dari dokter. Catatan & obat opsional.
        $request->validate([
            'doctor_risk_level' => 'required|in:Rendah,Sedang,Tinggi',
            'doctor_medication' => 'nullable|string',
            'doctor_notes'      => 'nullable|string',
        ], [
            'doctor_risk_level.required' => 'Tingkat risiko wajib dipilih dokter saat menyetujui diagnosis.',
            'doctor_risk_level.in'       => 'Tingkat risiko harus salah satu dari Rendah, Sedang, atau Tinggi.',
        ]);

        CombinedDiagnosis::findOrFail($id)->update([
            'validation_status' => 'confirmed',
            'doctor_risk_level' => $request->doctor_risk_level,
            'doctor_medication' => $request->doctor_medication,
            'doctor_notes'      => $request->doctor_notes,
        ]);

        return redirect()->route('analysis-history.index')->with('success', 'Diagnosis gabungan berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'doctor_notes'           => 'required|string',
            'doctor_final_diagnosis' => 'required|string',
            'doctor_risk_level'      => 'required|in:Rendah,Sedang,Tinggi',
            'doctor_medication'      => 'nullable|string',
        ], [
            'doctor_notes.required'           => 'Catatan koreksi wajib diisi saat menolak diagnosis gabungan.',
            'doctor_final_diagnosis.required' => 'Diagnosis final versi dokter wajib diisi saat menolak diagnosis gabungan.',
            'doctor_risk_level.required'      => 'Tingkat risiko wajib dipilih dokter saat menolak diagnosis.',
            'doctor_risk_level.in'            => 'Tingkat risiko harus salah satu dari Rendah, Sedang, atau Tinggi.',
        ]);

        CombinedDiagnosis::findOrFail($id)->update([
            'validation_status'      => 'corrected',
            'doctor_notes'           => $request->doctor_notes,
            'doctor_final_diagnosis' => $request->doctor_final_diagnosis,
            'doctor_risk_level'      => $request->doctor_risk_level,
            'doctor_medication'      => $request->doctor_medication,
        ]);

        return redirect()->route('analysis-history.index')->with('success', 'Diagnosis gabungan telah dikoreksi oleh dokter.');
    }
}