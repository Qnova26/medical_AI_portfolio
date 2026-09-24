<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$a = App\Models\ImageAnalysis::latest()->first();
echo 'ID: ' . $a->id . "\n";
echo 'image_files: ' . json_encode($a->image_files) . "\n";
echo 'result_images: ' . json_encode($a->result_images) . "\n";
