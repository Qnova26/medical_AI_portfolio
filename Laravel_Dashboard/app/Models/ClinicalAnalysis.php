<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicalAnalysis extends Model
{
    protected $fillable = [
    'patient_id', 'prompt_id', 'template_level',
    'body_temperature', 'heart_rate', 'respiratory_rate', 'blood_pressure',
    'medical_history', 'allergies', 'symptoms', 'doctor_notes', 'other_info',
    'medical_files', 'lab_files', 'lab_analysis', 'ai_result', 'diagnosis_short', 'risk_level', 'confidence_score',
    'summary', 'recommendation', 'differential', 'medication', 'confidence_reason', 'treatment_information', 'potential_complications',
    'doctor_analysis', 'validation_status',
    'doctor_diagnosis', 'doctor_risk_level', 'medication_recommendation',
    'needs_imaging_correlation', 'imaging_correlation_reason',
];

protected $casts = [
    'medical_files'             => 'array',
    'lab_files'                 => 'array',
    'ai_result'                 => 'array',
    'needs_imaging_correlation' => 'boolean',
];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function prompt()  { return $this->belongsTo(Prompt::class); }
}