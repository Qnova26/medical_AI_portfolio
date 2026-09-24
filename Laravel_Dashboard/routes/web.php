<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\AnalysisHistoryController;
use App\Http\Controllers\PictureAnalyzeController;
use App\Http\Controllers\ClinicalInsightsController;
use App\Http\Controllers\PromptComparisonController;
use App\Http\Controllers\DiseaseTrendController;
use App\Http\Controllers\RiskMonitoringController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\PromptAnalyticsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CombinedDiagnosisController;

Route::get('/', function () {
    return redirect()->route('login');
});

// DASHBOARD
Route::get(
    '/dashboard',
    [DashboardController::class, 'overview']
)
->middleware(['auth'])
->name('dashboard');


// CLINICAL INSIGHTS
Route::middleware('auth')->group(function () {

    Route::get(
        '/clinical-insights',
        [ClinicalInsightsController::class, 'index']
    )->name('clinical.insights');

});

// DISEASE TREND
Route::middleware('auth')->group(function () {

    Route::get(
        '/disease-trends',
        [DiseaseTrendController::class,'index']
    )->name('disease-trends.index');

});

// RISK MONITORING
Route::middleware('auth')->group(function () {

    Route::get(
        '/risk-monitoring',
        [RiskMonitoringController::class, 'index']
    )->name('risk-monitoring.index');

});

// ANALYSIS
Route::middleware('auth')->group(function () {

    Route::get(
        '/analysis/new',
        [AnalysisController::class, 'create']
    )->name('analysis.create');

    Route::post(
        '/analysis/run',
        [AnalysisController::class, 'analyze']
    )->name('analysis.run');

});

// ANALYSIS PICTURE
Route::middleware('auth')->group(function () {

    Route::get(
        '/analysis-picture/create',
        [PictureAnalyzeController::class, 'create']
    )->name('analysis-picture.create');

    Route::post(
        '/analysis-picture/analyze',
        [PictureAnalyzeController::class, 'analyze']
    )->name('analysis-picture.analyze');

    Route::get(
        '/analysis-picture/result/{id}', 
        [PictureAnalyzeController::class, 'result']
    )->name('analysis-picture.result');

});

// PROMPT MANAGEMENT
Route::middleware('auth')->group(function () {

    Route::resource(
        'prompts',
        PromptController::class
    );

});

Route::middleware('auth')->group(function () {

    Route::get(
        '/prompt-analytics',
        [PromptAnalyticsController::class, 'index']
    )->name('prompt-analytics.index');

});


// PROMPT COMPARISON
Route::middleware('auth')->group(function () {

    Route::get(
        '/prompt-comparison',
        [PromptComparisonController::class, 'index']
    )->name('prompt-comparison.index');

    Route::post(
        '/prompt-comparison/run',
        [PromptComparisonController::class, 'compare']
    )->name('prompt-comparison.run');

});

// PATIENT
Route::resource('patients', PatientController::class);

// MEDICAL RECORD
Route::middleware('auth')->group(function () {

    Route::resource(
        'medical-records',
        MedicalRecordController::class
    );

});

// ANALYSIS HISTORY
Route::middleware('auth')->group(function () {

    Route::get(
        '/analysis-history',
        [AnalysisHistoryController::class, 'index']
    )->name('analysis-history.index');

    Route::put(
        '/analysis-history/{id}/update-status',
        [AnalysisHistoryController::class, 'updateStatus']
    )->name('analysis-history.update-status');

});

// Analisis Klinis
Route::get('/analysis/create', [AnalysisController::class, 'create'])->name('analysis.create');
Route::post('/analysis/run', [AnalysisController::class, 'analyze'])->name('analysis.run');
Route::get('/analysis/result/{id}', [AnalysisController::class, 'result'])->name('analysis.result');

// Analisis Citra
Route::get('/analysis-picture/create', [PictureAnalyzeController::class, 'create'])->name('analysis-picture.create');
Route::post('/analysis-picture/run', [PictureAnalyzeController::class, 'analyze'])->name('analysis-picture.run');
Route::get('/analysis-picture/result/{id}', [PictureAnalyzeController::class, 'result'])->name('analysis-picture.result');

Route::get('/analysis-history', [AnalysisHistoryController::class, 'index'])->name('analysis-history.index');
Route::patch('/analysis-history/validate', [AnalysisHistoryController::class, 'validateCombined'])->name('analysis-history.validate');
Route::put('/analysis-history/{type}/{id}/update-analisis', [AnalysisHistoryController::class, 'updateAnalisis'])->name('analysis-history.update-analisis');

Route::middleware('auth')->group(function () {
    Route::post('/chat/{type}/{id}/ask',    [ChatController::class, 'ask'])->name('chat.ask');
    Route::get('/chat/{type}/{id}/history', [ChatController::class, 'history'])->name('chat.history');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/analysis-combined/{id}', [CombinedDiagnosisController::class, 'result'])->name('analysis-combined.result');
Route::post('/analysis-combined/{id}/confirm', [CombinedDiagnosisController::class, 'confirm'])->name('analysis-combined.confirm');
Route::post('/analysis-combined/{id}/reject', [CombinedDiagnosisController::class, 'reject'])->name('analysis-combined.reject');
Route::post('/analysis/{id}/reject', [AnalysisController::class, 'reject'])->name('analysis.reject');

Route::post('/analysis-history/combined', [AnalysisHistoryController::class, 'storeCombined'])
    ->name('analysis-history.combined.store');

Route::patch('/analysis/{id}/confirm', [AnalysisController::class, 'confirm'])->name('analysis.confirm');
Route::patch('/analysis/{id}/reject', [AnalysisController::class, 'reject'])->name('analysis.reject');
Route::patch('/analysis-picture/{id}/confirm', [PictureAnalyzeController::class, 'confirm'])->name('analysis-picture.confirm');
Route::patch('/analysis-picture/{id}/reject', [PictureAnalyzeController::class, 'reject'])->name('analysis-picture.reject');
Route::get('/analysis-picture/{id}/pdf',       [PictureAnalyzeController::class, 'downloadPdf'])->name('analysis-picture.pdf');

Route::delete('/chat/{type}/{id}/clear', [ChatController::class, 'clear'])->name('chat.clear');

require __DIR__.'/auth.php';
