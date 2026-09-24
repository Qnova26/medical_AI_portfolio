<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\ClinicalAnalysis;
use App\Models\ImageAnalysis;
use App\Models\CombinedDiagnosis;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $search     = trim($request->input('search', ''));
        $riskFilter = $request->input('risk_level', '');

        $patients = Patient::orderBy('created_at', 'desc')->get();

        $clinicals = ClinicalAnalysis::whereIn('patient_id', $patients->pluck('id'))->get()->keyBy('patient_id');
        $images    = ImageAnalysis::whereIn('patient_id', $patients->pluck('id'))->get()->keyBy('patient_id');
        $combined  = CombinedDiagnosis::whereIn('patient_id', $patients->pluck('id'))->get()->keyBy('patient_id');

        // Tambah info diagnosis & risk ke tiap pasien
        $patients = $patients->map(function ($patient) use ($clinicals, $images, $combined) {
            $cln = $clinicals->get($patient->id);
            $img = $images->get($patient->id);
            $cmb = $combined->get($patient->id);

            // ── Prioritas: Combined (hanya yg statusnya 'done') → Clinical → Image → null ──
            if ($cmb && $cmb->status === 'done') {

                if ($cmb->validation_status === 'corrected') {
                    // Dokter menolak & mengoreksi diagnosis gabungan
                    $patient->diagnosis  = $cmb->doctor_final_diagnosis;
                    $patient->risk_level = $cmb->doctor_risk_level ?? $cmb->final_risk_level;
                } elseif ($cmb->validation_status === 'confirmed') {
                    // Dokter menyetujui (doctor_risk_level wajib diisi saat confirm combined,
                    // fallback ke final_risk_level cuma untuk jaga-jaga data lama/rusak)
                    $patient->diagnosis  = $cmb->final_diagnosis;
                    $patient->risk_level = $cmb->doctor_risk_level ?? $cmb->final_risk_level;
                } else {
                    // Belum divalidasi dokter sama sekali → pakai hasil AI
                    $patient->diagnosis  = $cmb->final_diagnosis;
                    $patient->risk_level = $cmb->final_risk_level;
                }

            } elseif ($cln) {

                if ($cln->validation_status === 'corrected') {
                    // Dokter menolak & mengoreksi diagnosis klinis
                    $patient->diagnosis  = $cln->doctor_diagnosis;
                    $patient->risk_level = $cln->doctor_risk_level ?? $cln->risk_level;
                } elseif ($cln->validation_status === 'confirmed') {
                    // confirm() klinis tidak mewajibkan doctor_risk_level,
                    // jadi fallback ke hasil AI kalau dokter tidak mengisinya
                    $patient->diagnosis  = $cln->diagnosis_short;
                    $patient->risk_level = $cln->doctor_risk_level ?? $cln->risk_level;
                } else {
                    // Belum divalidasi dokter → pakai hasil AI
                    $patient->diagnosis  = $cln->diagnosis_short;
                    $patient->risk_level = $cln->risk_level;
                }

            } elseif ($img) {

                if ($img->validation_status === 'corrected') {
                    $patient->diagnosis  = $img->doctor_diagnosis;
                    $patient->risk_level = $img->doctor_risk_level ?? $img->risk_level;
                } elseif ($img->validation_status === 'confirmed') {
                    $patient->diagnosis  = $img->diagnosis_short;
                    $patient->risk_level = $img->doctor_risk_level ?? $img->risk_level;
                } else {
                    $patient->diagnosis  = $img->diagnosis_short;
                    $patient->risk_level = $img->risk_level;
                }

            } else {
                $patient->diagnosis  = null;
                $patient->risk_level = null;
            }

            return $patient;
        });

        // Ringkasan KPI selalu dihitung dari SELURUH data pasien,
        // supaya angkanya nggak ikut berubah cuma karena sedang mencari/filter.
        $summary = [
            'total_patients' => $patients->count(),
            'male'           => $patients->where('jenis_kelamin', 'Male')->count(),
            'female'         => $patients->where('jenis_kelamin', 'Female')->count(),
            'high_risk'      => $patients->where('risk_level', 'Tinggi')->count(),
        ];

        // Terapkan pencarian nama / ID pasien
        if ($search !== '') {
            $patients = $patients->filter(function ($patient) use ($search) {
                $haystack = strtolower($patient->nama_pasien . ' ' . $patient->id_pasien);
                return str_contains($haystack, strtolower($search));
            });
        }

        // Terapkan filter tingkat risiko
        // (dilakukan setelah mapping karena risk_level bukan kolom asli tabel patients)
        if ($riskFilter !== '') {
            $patients = $patients->filter(function ($patient) use ($riskFilter) {
                return $patient->risk_level === $riskFilter;
            });
        }

        $patients = $patients->values();

        return view('patients.index', compact('patients', 'summary'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pasien' => 'required|unique:patients,id_pasien',
            'nama_pasien' => 'required',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'no_tlp' => 'required',
            'alamat' => 'required',
        ]);

        Patient::create($request->all());

        return redirect()
            ->route('patients.index')
            ->with('success', 'Pasien berhasil ditambahkan');
    }

    public function show($id)
    {
        $patient = Patient::findOrFail($id);
        return view('patients.show', compact('patient'));
    }

    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $patient->update($request->all());

        return redirect()
            ->route('patients.index')
            ->with('success', 'Pasien berhasil diupdate');
    }

    public function destroy($id)
    {
        Patient::destroy($id);

        return redirect()
            ->route('patients.index')
            ->with('success', 'Pasien berhasil dihapus');
    }
}