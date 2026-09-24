<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageAnalysis extends Model
{
    protected $fillable = [
        'patient_id',
        'prompt_id',
        'template_level',
        'image_type',
        'body_part',
        'doctor_notes',
        'image_files',
        'result_images',
        'diagnosis_short',
        'risk_level',
        'confidence_score',
        'confidence_reason',
        'summary',
        'differential',
        'recommendation',
        'medication',
        'ai_result',
        'validation_status',
        'doctor_analysis',
        'doctor_diagnosis',
        'doctor_risk_level',
        'medication_recommendation',
        'needs_clinical_correlation',
        'clinical_correlation_reason',
    ];

    protected $casts = [
        'image_files'                 => 'array',
        'result_images'               => 'array',
        'ai_result'                   => 'array',
        'differential'                => 'array',
        'needs_clinical_correlation'  => 'boolean',
    ];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function prompt()  { return $this->belongsTo(Prompt::class); }
}