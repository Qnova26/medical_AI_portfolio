<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ClinicalAnalysis;
use App\Models\ImageAnalysis;
use Illuminate\Support\Carbon;

class RiskMonitoringController extends Controller
{
    public function index()
    {
        $highRisk = ClinicalAnalysis::where('risk_level', 'Tinggi')->count() + ImageAnalysis::where('risk_level', 'Tinggi')->count();
        $mediumRisk = ClinicalAnalysis::where('risk_level', 'Sedang')->count() + ImageAnalysis::where('risk_level', 'Sedang')->count();
        $lowRisk = ClinicalAnalysis::where('risk_level', 'Rendah')->count() + ImageAnalysis::where('risk_level', 'Rendah')->count();

        $summary = [
            'high_risk' => $highRisk,
            'medium_risk' => $mediumRisk,
            'low_risk' => $lowRisk,
            'critical_alerts' => 0,
        ];

        $riskDistribution = [
            'Low' => $lowRisk,
            'Medium' => $mediumRisk,
            'High' => $highRisk,
        ];

        // Risk by age
        $patients = Patient::whereHas('clinicalAnalyses', function($q) {
            $q->where('risk_level', 'Tinggi');
        })->orWhereHas('imageAnalyses', function($q) {
            $q->where('risk_level', 'Tinggi');
        })->get();
        
        $riskByAge = ['0-18' => 0, '19-30' => 0, '31-50' => 0, '51-70' => 0, '70+' => 0];
        foreach($patients as $p) {
            if (!$p->tanggal_lahir) continue;
            $age = Carbon::parse($p->tanggal_lahir)->age;
            if ($age <= 18) $riskByAge['0-18']++;
            elseif ($age <= 30) $riskByAge['19-30']++;
            elseif ($age <= 50) $riskByAge['31-50']++;
            elseif ($age <= 70) $riskByAge['51-70']++;
            else $riskByAge['70+']++;
        }

        // Critical Patients
        $criticalPatients = [];
        foreach($patients->take(5) as $p) {
            $diag = optional($p->clinicalAnalyses()->where('risk_level', 'Tinggi')->first())->diagnosis_short 
                     ?? optional($p->imageAnalyses()->where('risk_level', 'Tinggi')->first())->diagnosis_short 
                     ?? 'Unknown';
            $criticalPatients[] = [
                'id' => $p->id_pasien,
                'name' => $p->nama_pasien,
                'diagnosis' => $diag,
                'risk' => 'High',
                'action_status' => 'Pending'
            ];
        }

        return view('risk-monitoring.index', compact(
            'summary', 'riskDistribution', 'riskByAge', 'criticalPatients'
        ));
    }
}
