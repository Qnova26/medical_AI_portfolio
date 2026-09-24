<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PromptComparisonController extends Controller
{
    public function index()
    {
        return view(
            'prompt-comparison.index'
        );
    }

    public function compare(Request $request)
    {
        $results = [

            [
                'prompt' => 'Basic Diagnosis',
                'diagnosis' => 'Pneumonia',
                'confidence' => 82
            ],

            [
                'prompt' => 'Clinical Summary',
                'diagnosis' => 'Pneumonia',
                'confidence' => 88
            ],

            [
                'prompt' => 'Radiology Expert',
                'diagnosis' => 'Pneumonia (Moderate)',
                'confidence' => 94
            ]

        ];

        return view(
            'prompt-comparison.result',
            compact('results')
        );
    }
}