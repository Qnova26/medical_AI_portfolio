<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index()
    {
        $summary = [
            'total_records' => 1938,
            'pending' => 112,
            'analyzed' => 1568,
            'completed' => 258,
        ];

        $records = [
            [
                'id' => 1,
                'record_number' => 'RM-001',
                'patient' => 'John Doe',
                'type' => 'Rontgen Toraks',
                'doctor' => 'dr. Smith, Sp.Rad',
                'date' => '2026-06-01',
                'status' => 'Dianalisis'
            ],
            [
                'id' => 2,
                'record_number' => 'RM-002',
                'patient' => 'Maria Smith',
                'type' => 'MRI Otak',
                'doctor' => 'dr. Anderson, Sp.N',
                'date' => '2026-06-02',
                'status' => 'Menunggu'
            ],
            [
                'id' => 3,
                'record_number' => 'RM-003',
                'patient' => 'Michael Brown',
                'type' => 'EKG Jantung',
                'doctor' => 'dr. Wilson, Sp.JP',
                'date' => '2026-06-03',
                'status' => 'Selesai'
            ]
        ];

        return view(
            'medical-records.index',
            compact('summary', 'records')
        );
    }

    public function create()
    {
        return view('medical-records.create');
    }

    public function store(Request $request)
    {
        return redirect()
            ->route('medical-records.index')
            ->with('success', 'Rekam medis berhasil ditambahkan.');
    }

    public function show($id)
    {
        return view('medical-records.show');
    }

    public function edit($id)
    {
        return view('medical-records.edit');
    }

    public function update(Request $request, $id)
    {
        return redirect()
            ->route('medical-records.index')
            ->with('success', 'Rekam medis berhasil diperbarui.');
    }

    public function destroy($id)
    {
        return redirect()
            ->route('medical-records.index')
            ->with('success', 'Rekam medis berhasil dihapus.');
    }
}