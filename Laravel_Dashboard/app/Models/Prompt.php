<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    protected $fillable = [
        'name',
        'category',
        'level',
        'type',
        'version',
        'status',
        'system_prompt',
        'user_prompt'
    ];
}