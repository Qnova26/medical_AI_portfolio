<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ClinicalAnalysis;
use App\Models\ImageAnalysis;
use Illuminate\Support\Carbon;

class ClinicalInsightsController extends Controller
{
    public function index()
    {
        $clinicalCount = ClinicalAnalysis::count();
        $imageCount = ImageAnalysis::count();
        
        $activeClinical = ClinicalAnalysis::where('validation_status', 'pending')->count();
        $activeImage = ImageAnalysis::where('validation_status', 'pending')->count();
        
        $highRiskClinical = ClinicalAnalysis::where('risk_level', 'Tinggi')->count();
        $highRiskImage = ImageAnalysis::where('risk_level', 'Tinggi')->count();
        
        $avgConfC = ClinicalAnalysis::avg('confidence_score') ?? 0;
        $avgConfI = ImageAnalysis::avg('confidence_score') ?? 0;
        $avgConf = ($avgConfC + $avgConfI) / 2;

        $summary = [
            'total_cases' => $clinicalCount + $imageCount,
            'active_cases' => $activeClinical + $activeImage,
            'high_risk' => $highRiskClinical + $highRiskImage,
            'confidence' => round($avgConf),
        ];

        // Top Diseases
        $clinicalDiags = ClinicalAnalysis::whereNotNull('diagnosis_short')
            ->selectRaw('diagnosis_short as name, count(*) as count')
            ->groupBy('diagnosis_short')->get()->toArray();
        $imageDiags = ImageAnalysis::whereNotNull('diagnosis_short')
            ->selectRaw('diagnosis_short as name, count(*) as count')
            ->groupBy('diagnosis_short')->get()->toArray();
            
        $diagMap = [];
        foreach(array_merge($clinicalDiags, $imageDiags) as $d) {
            $name = trim($d['name']);
            if(!isset($diagMap[$name])) $diagMap[$name] = 0;
            $diagMap[$name] += $d['count'];
        }
        arsort($diagMap);
        
        $topDiseases = [];
        $diseaseChartData = [];
        $i = 0;
        foreach($diagMap as $name => $count) {
            if ($i++ < 5) {
                $topDiseases[] = ['name' => $name, 'count' => $count];
                $diseaseChartData[$name] = $count;
            }
        }

        // High Risk Patients
        $highRiskPatients = [];
        $highRiskPatientsQuery = Patient::whereHas('clinicalAnalyses', function($q) {
            $q->where('risk_level', 'Tinggi');
        })->orWhereHas('imageAnalyses', function($q) {
            $q->where('risk_level', 'Tinggi');
        })->take(5)->get();
        
        foreach($highRiskPatientsQuery as $p) {
            $diag = optional($p->clinicalAnalyses()->where('risk_level', 'Tinggi')->first())->diagnosis_short 
                     ?? optional($p->imageAnalyses()->where('risk_level', 'Tinggi')->first())->diagnosis_short 
                     ?? 'Unknown';
            $highRiskPatients[] = [
                'id' => $p->id_pasien,
                'name' => $p->nama_pasien,
                'diagnosis' => $diag,
                'risk' => 'High'
            ];
        }

        // Gender Data
        $males = Patient::where('jenis_kelamin', 'Male')->count();
        $females = Patient::where('jenis_kelamin', 'Female')->count();
        $genderData = [
            'Male' => $males,
            'Female' => $females
        ];

        // Age Data
        $patients = Patient::whereNotNull('tanggal_lahir')->get();
        $ageData = ['0-18' => 0, '19-30' => 0, '31-50' => 0, '51-70' => 0, '70+' => 0];
        foreach($patients as $p) {
            $age = Carbon::parse($p->tanggal_lahir)->age;
            if ($age <= 18) $ageData['0-18']++;
            elseif ($age <= 30) $ageData['19-30']++;
            elseif ($age <= 50) $ageData['31-50']++;
            elseif ($age <= 70) $ageData['51-70']++;
            else $ageData['70+']++;
        }

        // Risk Data
        $riskData = [
            'Low' => ClinicalAnalysis::where('risk_level', 'Rendah')->count() + ImageAnalysis::where('risk_level', 'Rendah')->count(),
            'Medium' => ClinicalAnalysis::where('risk_level', 'Sedang')->count() + ImageAnalysis::where('risk_level', 'Sedang')->count(),
            'High' => $summary['high_risk']
        ];

        return view('clinical-insights.index', compact(
            'summary', 'topDiseases', 'highRiskPatients', 'genderData', 'ageData', 'diseaseChartData', 'riskData'
        ));
    }
}
