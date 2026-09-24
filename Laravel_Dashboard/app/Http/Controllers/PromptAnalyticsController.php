<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use App\Models\ClinicalAnalysis;
use App\Models\ImageAnalysis;
use Illuminate\Support\Facades\DB;

class PromptAnalyticsController extends Controller
{
    public function index()
    {
        $clinicalExecs = ClinicalAnalysis::whereNotNull('prompt_id')->count();
        $imageExecs = ImageAnalysis::whereNotNull('prompt_id')->count();
        $totalExecs = $clinicalExecs + $imageExecs;
        
        $activePrompts = Prompt::count(); // Assuming all are active for now
        
        if ($totalExecs > 0) {
            $sumConfC = ClinicalAnalysis::whereNotNull('prompt_id')->sum('confidence_score') ?? 0;
            $sumConfI = ImageAnalysis::whereNotNull('prompt_id')->sum('confidence_score') ?? 0;
            $avgConf = ($sumConfC + $sumConfI) / $totalExecs;
        } else {
            $avgConf = 0;
        }
        
        // Find Best Prompt (highest usage for simplicity)
        $clinicalGroups = ClinicalAnalysis::whereNotNull('prompt_id')
            ->selectRaw('prompt_id, count(*) as count')
            ->groupBy('prompt_id')->get();
        $imageGroups = ImageAnalysis::whereNotNull('prompt_id')
            ->selectRaw('prompt_id, count(*) as count')
            ->groupBy('prompt_id')->get();
            
        $promptUsage = [];
        foreach($clinicalGroups as $cg) {
            $promptUsage[$cg->prompt_id] = ($promptUsage[$cg->prompt_id] ?? 0) + $cg->count;
        }
        foreach($imageGroups as $ig) {
            $promptUsage[$ig->prompt_id] = ($promptUsage[$ig->prompt_id] ?? 0) + $ig->count;
        }
        
        arsort($promptUsage);
        $bestPromptId = count($promptUsage) > 0 ? array_key_first($promptUsage) : null;
        $bestPromptName = 'None';
        if ($bestPromptId) {
            $bp = Prompt::find($bestPromptId);
            $bestPromptName = $bp ? $bp->name . ' (' . ucfirst($bp->level) . ')' : 'Unknown';
        }

        $summary = [
            'executions' => $totalExecs,
            'active_prompts' => $activePrompts,
            'avg_confidence' => round($avgConf),
            'best_prompt' => $bestPromptName,
        ];

        // Format for Charts
        $usageData = [];
        $confidenceData = [];
        foreach($promptUsage as $pId => $count) {
            $p = Prompt::find($pId);
            if (!$p) continue;
            
            $fullName = $p->name . ' (' . ucfirst($p->level) . ')';
            $usageData[$fullName] = $count;
            
            // Average confidence for this prompt
            $cSum = ClinicalAnalysis::where('prompt_id', $pId)->sum('confidence_score') ?? 0;
            $iSum = ImageAnalysis::where('prompt_id', $pId)->sum('confidence_score') ?? 0;
            $confidenceData[$fullName] = round(($cSum + $iSum) / $count);
        }

        // Fetch All Prompts for Table
        $performanceTable = [];
        $prompts = Prompt::all();
        foreach($prompts as $p) {
            $usage = $promptUsage[$p->id] ?? 0;
            $conf = 0;
            $success = 0;
            
            if ($usage > 0) {
                $cSum = ClinicalAnalysis::where('prompt_id', $p->id)->sum('confidence_score') ?? 0;
                $iSum = ImageAnalysis::where('prompt_id', $p->id)->sum('confidence_score') ?? 0;
                $conf = round(($cSum + $iSum) / $usage);
                
                $cTotalValid = ClinicalAnalysis::where('prompt_id', $p->id)->whereIn('validation_status', ['confirmed', 'corrected'])->count();
                $cSuccess = ClinicalAnalysis::where('prompt_id', $p->id)->where('validation_status', 'confirmed')->count();
                
                $iTotalValid = ImageAnalysis::where('prompt_id', $p->id)->whereIn('validation_status', ['confirmed', 'corrected'])->count();
                $iSuccess = ImageAnalysis::where('prompt_id', $p->id)->where('validation_status', 'confirmed')->count();
                
                $totalValid = $cTotalValid + $iTotalValid;
                
                if ($totalValid > 0) {
                    $success = round((($cSuccess + $iSuccess) / $totalValid) * 100);
                } else {
                    $success = 0; // Belum ada yang divalidasi dokter
                }
            }
            
            $fullName = $p->name . ' (' . ucfirst($p->level) . ')';
            
            $performanceTable[] = [
                'id' => $p->id,
                'name' => $fullName,
                'type' => $p->category ?? 'Umum',
                'usage' => $usage,
                'confidence' => $conf,
                'success' => $success,
                'status' => $p->status ?? 'Active'
            ];
        }

        // Monthly Usage Trend (Last 6 months)
        $monthlyUsage = [];
        $indoMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $indoMonths[$date->month - 1];
            
            $cCount = ClinicalAnalysis::whereNotNull('prompt_id')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)->count();
            $iCount = ImageAnalysis::whereNotNull('prompt_id')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)->count();
                
            $monthlyUsage[$monthName] = $cCount + $iCount;
        }

        // Generate Dynamic Insights
        $aiInsights = [];
        $sortedByConf = $performanceTable;
        usort($sortedByConf, function($a, $b) { return $b['confidence'] <=> $a['confidence']; });
        
        $sortedByUsage = $performanceTable;
        usort($sortedByUsage, function($a, $b) { return $b['usage'] <=> $a['usage']; });
        
        if(count($sortedByUsage) > 0 && $sortedByUsage[0]['usage'] > 0) {
            $topP = $sortedByUsage[0];
            $aiInsights[] = "Instruksi <strong class='text-white'>{$topP['name']}</strong> mendominasi sistem dengan eksekusi {$topP['usage']} kali.";
        } else {
            $aiInsights[] = "Belum ada instruksi yang tereksekusi pada sistem AI.";
        }
        
        if(count($sortedByConf) > 0 && $sortedByConf[0]['confidence'] > 0) {
            $topC = $sortedByConf[0];
            $aiInsights[] = "Evaluasi mencatat <strong class='text-white'>{$topC['name']}</strong> memiliki rasio presisi tertinggi ({$topC['confidence']}%).";
        }
        
        if(count($sortedByConf) > 1 && end($sortedByConf)['usage'] > 0) {
            $lowC = end($sortedByConf);
            $aiInsights[] = "Instruksi <strong class='text-slate-200 italic'>{$lowC['name']}</strong> (Akurasi {$lowC['confidence']}%) mungkin memerlukan optimasi struktur semantik segera.";
        }

        return view('prompt-analytics.index', compact(
            'summary', 'usageData', 'confidenceData', 'performanceTable', 'monthlyUsage', 'aiInsights'
        ));
    }
}
