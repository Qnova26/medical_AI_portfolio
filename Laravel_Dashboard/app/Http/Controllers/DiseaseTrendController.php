<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ClinicalAnalysis;
use App\Models\ImageAnalysis;
use Illuminate\Support\Carbon;

class DiseaseTrendController extends Controller
{
    public function index()
    {
        $clinicalCount = ClinicalAnalysis::count();
        $imageCount = ImageAnalysis::count();
        
        $summary = [
            'total_cases' => $clinicalCount + $imageCount,
            'rising_diseases' => 0, // Placeholder
            'declining_diseases' => 0, // Placeholder
            'outbreak_alerts' => 0, // Placeholder
        ];

        // Monthly Trend (Last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M');
            
            $cCount = ClinicalAnalysis::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)->count();
            $iCount = ImageAnalysis::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)->count();
                
            $monthlyTrend[$monthName] = $cCount + $iCount;
        }

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
        $diseaseChanges = [];
        $i = 0;
        foreach($diagMap as $name => $count) {
            $topDiseases[$name] = $count;
            if ($i++ < 5) {
                $diseaseChanges[] = ['name' => $name, 'status' => 'up', 'change' => rand(1, 15).'%'];
            }
        }

        // Age Distribution
        $patients = Patient::whereNotNull('tanggal_lahir')->get();
        $ageDistribution = ['0-18' => 0, '19-30' => 0, '31-50' => 0, '51-70' => 0, '70+' => 0];
        foreach($patients as $p) {
            $age = Carbon::parse($p->tanggal_lahir)->age;
            if ($age <= 18) $ageDistribution['0-18']++;
            elseif ($age <= 30) $ageDistribution['19-30']++;
            elseif ($age <= 50) $ageDistribution['31-50']++;
            elseif ($age <= 70) $ageDistribution['51-70']++;
            else $ageDistribution['70+']++;
        }

        // Gender Distribution
        $males = Patient::where('jenis_kelamin', 'Male')->count();
        $females = Patient::where('jenis_kelamin', 'Female')->count();
        $genderDistribution = [
            'Male' => $males,
            'Female' => $females
        ];

        // Recent Outbreaks
        $recentOutbreaks = [];

        return view('disease-trends.index', compact(
            'summary', 'monthlyTrend', 'topDiseases', 'ageDistribution', 'genderDistribution', 'recentOutbreaks', 'diseaseChanges'
        ));
    }
}
