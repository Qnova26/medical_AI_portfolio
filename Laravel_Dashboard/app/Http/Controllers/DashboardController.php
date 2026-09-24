<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function overview()
    {
        // 1. Summary
        $totalPatients = \App\Models\Patient::count();
        $analysesToday = \App\Models\ClinicalAnalysis::whereDate('created_at', today())->count() + 
                         \App\Models\ImageAnalysis::whereDate('created_at', today())->count();
        
        $highRisk = \App\Models\ClinicalAnalysis::where('risk_level', 'Tinggi')->count() + 
                    \App\Models\ImageAnalysis::where('risk_level', 'Tinggi')->count();
        
        $totalClinical = \App\Models\ClinicalAnalysis::count();
        $totalImage = \App\Models\ImageAnalysis::count();
        $totalAnalyses = $totalClinical + $totalImage;

        if ($totalAnalyses > 0) {
            $sumClinical = \App\Models\ClinicalAnalysis::sum('confidence_score') ?? 0;
            $sumImage = \App\Models\ImageAnalysis::sum('confidence_score') ?? 0;
            $confidence = ($sumClinical + $sumImage) / $totalAnalyses;
        } else {
            $confidence = 0;
        }

        $summary = [
            'total_patients' => $totalPatients,
            'analyses_today' => $analysesToday,
            'high_risk'      => $highRisk,
            'confidence'     => round($confidence),
        ];

        // 2. Top Diagnoses (from ImageAnalysis + ClinicalAnalysis)
        // For simplicity, we just group by diagnosis_short in ImageAnalysis and ClinicalAnalysis, merge and sort
        $clinicalDiags = \App\Models\ClinicalAnalysis::whereNotNull('diagnosis_short')
            ->selectRaw('diagnosis_short as name, count(*) as count')
            ->groupBy('diagnosis_short')->get()->toArray();
        $imageDiags = \App\Models\ImageAnalysis::whereNotNull('diagnosis_short')
            ->selectRaw('diagnosis_short as name, count(*) as count')
            ->groupBy('diagnosis_short')->get()->toArray();
        
        $diagMap = [];
        foreach(array_merge($clinicalDiags, $imageDiags) as $d) {
            $name = $d['name'] == '-' ? 'Unknown' : $d['name'];
            if(!isset($diagMap[$name])) $diagMap[$name] = 0;
            $diagMap[$name] += $d['count'];
        }
        arsort($diagMap);
        $topDiagnoses = [];
        foreach(array_slice($diagMap, 0, 5) as $name => $count) {
            $topDiagnoses[] = ['name' => Str::limit($name, 30), 'count' => $count];
        }  
        if(empty($topDiagnoses)) {
            $topDiagnoses = [['name'=>'Belum ada data', 'count'=>0]];
        }

        // 3. High Risk Patients (recent ones)
        $highRiskPatients = [];
        $recentHighRiskImage = \App\Models\ImageAnalysis::with('patient')->where('risk_level', 'Tinggi')->orderBy('created_at', 'desc')->take(3)->get();
        foreach($recentHighRiskImage as $hr) {
            $highRiskPatients[] = [
                'id' => 'P' . str_pad($hr->patient->id, 3, '0', STR_PAD_LEFT),
                'name' => $hr->patient->nama_pasien,
                'diagnosis' => Str::limit($hr->diagnosis_short, 20),
                'risk' => 'Tinggi'
            ];
        }
        if(empty($highRiskPatients)) {
             // fallback to latest if no high risk
             $fallback = \App\Models\ImageAnalysis::with('patient')->orderBy('created_at', 'desc')->take(3)->get();
             foreach($fallback as $hr) {
                $highRiskPatients[] = [
                    'id' => 'P' . str_pad($hr->patient->id, 3, '0', STR_PAD_LEFT),
                    'name' => $hr->patient->nama_pasien,
                    'diagnosis' => Str::limit($hr->diagnosis_short, 20),
                    'risk' => $hr->risk_level
                ];
             }
        }

        // 4. Trend Cases based on Filter
        $filter = request()->query('filter', '6_months');
        $monthlyCases = [];
        $monthLabels = [];

        if ($filter == '7_days') {
            for ($i = 6; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subDays($i);
                $monthLabels[] = $date->format('d M');
                
                $c = \App\Models\ClinicalAnalysis::whereDate('created_at', $date->toDateString())->count();
                $img = \App\Models\ImageAnalysis::whereDate('created_at', $date->toDateString())->count();
                $monthlyCases[] = $c + $img;
            }
        } elseif ($filter == '30_days') {
            // Show grouped by 3 days or just 30 days? We'll show all 30 days for precise trend
            for ($i = 29; $i >= 0; $i--) {
                $date = \Carbon\Carbon::now()->subDays($i);
                // Only show label every 5 days to prevent clutter, but show all data points
                $monthLabels[] = ($i % 5 == 0 || $i == 29 || $i == 0) ? $date->format('d M') : '';
                
                $c = \App\Models\ClinicalAnalysis::whereDate('created_at', $date->toDateString())->count();
                $img = \App\Models\ImageAnalysis::whereDate('created_at', $date->toDateString())->count();
                $monthlyCases[] = $c + $img;
            }
        } elseif ($filter == 'year') {
            for ($i = 11; $i >= 0; $i--) {
                $month = \Carbon\Carbon::now()->subMonths($i);
                $indoMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $monthLabels[] = $indoMonths[$month->month - 1] . " '" . $month->format('y');
                
                $c = \App\Models\ClinicalAnalysis::whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->count();
                $img = \App\Models\ImageAnalysis::whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->count();
                $monthlyCases[] = $c + $img;
            }
        } else {
            // 6_months (default)
            for ($i = 5; $i >= 0; $i--) {
                $month = \Carbon\Carbon::now()->subMonths($i);
                $indoMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                $monthLabels[] = $indoMonths[$month->month - 1];
                
                $c = \App\Models\ClinicalAnalysis::whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->count();
                $img = \App\Models\ImageAnalysis::whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->count();
                $monthlyCases[] = $c + $img;
            }
        }

        // 5. Prompt Usage (Prompt breakdown)
        $prompts = \App\Models\Prompt::all();
        $promptUsage = [];
        $promptLabels = [];
        foreach($prompts as $p) {
            $total = \App\Models\ClinicalAnalysis::where('prompt_id', $p->id)->count() + 
                     \App\Models\ImageAnalysis::where('prompt_id', $p->id)->count();
            if($total > 0) {
                $promptUsage[] = $total;
                $promptLabels[] = Str::limit($p->name . ' (' . ucfirst($p->level) . ')', 20);
            }
        }
        if(empty($promptUsage)) {
             $promptUsage = [1];
             $promptLabels = ['Belum ada data'];
        }

        // 6. Confidence Distribution
        $cHigh = \App\Models\ImageAnalysis::where('confidence_score', '>=', 90)->count() + \App\Models\ClinicalAnalysis::where('confidence_score', '>=', 90)->count();
        $cMed = \App\Models\ImageAnalysis::whereBetween('confidence_score', [80, 89])->count() + \App\Models\ClinicalAnalysis::whereBetween('confidence_score', [80, 89])->count();
        $cLow = \App\Models\ImageAnalysis::where('confidence_score', '<', 80)->count() + \App\Models\ClinicalAnalysis::where('confidence_score', '<', 80)->count();
        $confidenceDistribution = [$cHigh, $cMed, $cLow];
        if(array_sum($confidenceDistribution) == 0) {
            $confidenceDistribution = [0, 0, 0];
        }

        // 7. Risk Distribution
        $rLow = \App\Models\ImageAnalysis::where('risk_level', 'Rendah')->count() + \App\Models\ClinicalAnalysis::where('risk_level', 'Rendah')->count();
        $rMed = \App\Models\ImageAnalysis::where('risk_level', 'Sedang')->count() + \App\Models\ClinicalAnalysis::where('risk_level', 'Sedang')->count();
        $rHigh = \App\Models\ImageAnalysis::where('risk_level', 'Tinggi')->count() + \App\Models\ClinicalAnalysis::where('risk_level', 'Tinggi')->count();
        $riskDistribution = [$rLow, $rMed, $rHigh];

        // 8. AI Insights
        $aiInsights = [];
        $topDiagNameFull = count($diagMap) > 0 ? array_key_first($diagMap) : 'Belum ada data';
        
        if ($topDiagNameFull !== 'Belum ada data') {
            $cleanName = $topDiagNameFull;
            
            // Jika AI mengembalikan kalimat panjang (lebih dari 4 kata)
            if (str_word_count($cleanName) > 4) {
                // Deteksi jika kalimatnya berarti belum ada keputusan pasti
                if (preg_match('/(belum terklasifikasi|ambigu|verifikasi|tidak spesifik|tidak jelas)/i', $cleanName)) {
                    $shortName = 'Belum Terklasifikasi (Butuh Observasi)';
                } else if (preg_match('/(normal|sehat|tidak ada kelainan)/i', $cleanName)) {
                    $shortName = 'Kondisi Normal / Sehat';
                } else {
                    // Buang kata pengantar bertele-tele
                    $temp = str_ireplace(['Data paling konsisten dengan kondisi ', 'Kemungkinan besar ', 'Diagnosis mengarah pada ', 'Pasien mengalami '], '', $cleanName);
                    // Ambil 3 kata inti
                    $shortName = \Illuminate\Support\Str::words($temp, 3, ' (Kompleks)');
                }
            } else {
                $shortName = $cleanName;
            }
            
            $aiInsights[] = "↑ Dominasi kasus: " . ucfirst(trim($shortName));
        } else {
            $aiInsights[] = "Belum ada tren diagnosis";
        }
        $aiInsights[] = "↑ " . $rHigh . " Pasien Terdeteksi Risiko Tinggi";
        $aiInsights[] = "↑ " . $totalAnalyses . " Total Analisis Medis Selesai";

        if (request()->has('ajax')) {
            return response()->json([
                'labels' => $monthLabels,
                'cases'  => $monthlyCases
            ]);
        }

        return view(
            'dashboard.overview',
            compact(
                'summary',
                'topDiagnoses',
                'highRiskPatients',
                'monthlyCases',
                'monthLabels',
                'promptUsage',
                'promptLabels',
                'confidenceDistribution',
                'riskDistribution',
                'aiInsights',
                'filter'
            )
        );
    }
}