<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$clinicals = \App\Models\ClinicalAnalysis::all();
$images = \App\Models\ImageAnalysis::all();
echo "Clinicals:\n";
foreach($clinicals as $c) echo $c->id.' - Patient: '.$c->patient_id.' - Date: '.$c->created_at->format('Y-m-d')."\n";
echo "Images:\n";
foreach($images as $i) echo $i->id.' - Patient: '.$i->patient_id.' - Date: '.$i->created_at->format('Y-m-d')."\n";
