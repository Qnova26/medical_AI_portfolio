<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';

    protected $fillable = [
        'id_pasien',
        'nama_pasien',
        'tanggal_lahir',
        'jenis_kelamin',
        'no_tlp',
        'alamat'
    ];

/*
    public function analysis()
    {
        return $this->hasOne(AnalysisResult::class);
    }
*/
    public function getUmurAttribute()
    {
        return Carbon::parse($this->tanggal_lahir)->age;
    }

    public function getNameAttribute()
    {
        return $this->nama_pasien;
    }

    public function clinicalAnalyses()
    {
        return $this->hasMany(ClinicalAnalysis::class, 'patient_id');
    }

    public function imageAnalyses()
    {
        return $this->hasMany(ImageAnalysis::class, 'patient_id');
    }
}