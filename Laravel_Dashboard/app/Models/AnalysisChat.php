<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalysisChat extends Model
{
    protected $fillable = ['analysis_type', 'analysis_id', 'role', 'message'];
}