<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CombinedDiagnosis extends Model
{
    protected $fillable = [
        'patient_id',
        'clinical_analysis_id',
        'image_analysis_id',
        'final_diagnosis',
        'final_risk_level',
        'final_confidence',
        'final_summary',
        'final_recommendation',
        'confidence_reason',
        'treatment_information',
        'potential_complications',
        'ai_result',
        'status',
        'validation_status',
        'doctor_notes',
        'doctor_final_diagnosis',
        'doctor_risk_level',
        'doctor_medication',
    ];

    protected $casts = [
        'ai_result' => 'array',
    ];

    public function patient()          { return $this->belongsTo(Patient::class); }
    public function clinicalAnalysis() { return $this->belongsTo(ClinicalAnalysis::class); }
    public function imageAnalysis()    { return $this->belongsTo(ImageAnalysis::class); }
}