<?php

namespace App\Http\Controllers;

use App\Models\AnalysisChat;
use App\Models\ClinicalAnalysis;
use App\Models\ImageAnalysis;
use App\Models\CombinedDiagnosis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function ask(Request $request, $type, $id)
    {
        $request->validate(['question' => 'required|string|max:1000']);

        // Validasi type
        if (!in_array($type, ['clinical', 'image', 'combined'])) {
            return response()->json(['error' => 'Tipe analisis tidak valid.'], 400);
        }

        // Ambil data analisis
        try {
            $analysis = match ($type) {
                'clinical' => ClinicalAnalysis::findOrFail($id),
                'image'    => ImageAnalysis::findOrFail($id),
                'combined' => CombinedDiagnosis::findOrFail($id),
            };
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data analisis tidak ditemukan.'], 404);
        }

        // Susun konteks dari data analisis
        if ($type === 'combined') {
            $aiResult = $analysis->ai_result ?? [];
            $context = array_filter([
                'diagnosis'      => $analysis->final_diagnosis ?? null,
                'risk_level'     => $analysis->final_risk_level ?? null,
                'confidence'     => $analysis->final_confidence ?? null,
                'summary'        => $analysis->final_summary ?? null,
                'recommendation' => $analysis->final_recommendation ?? null,
                'medication'     => $aiResult['medication'] ?? null,
                'correlation'    => $aiResult['correlation'] ?? null,
            ], fn($v) => !is_null($v));
        } else {
            $context = array_filter([
                'diagnosis'      => $analysis->diagnosis_short ?? null,
                'risk_level'     => $analysis->risk_level ?? null,
                'confidence'     => $analysis->confidence_score ?? null,
                'summary'        => $analysis->summary ?? null,
                'differential'   => $analysis->differential ?? null,
                'recommendation' => $analysis->recommendation ?? null,
                'medication'     => $analysis->medication ?? null,
            ], fn($v) => !is_null($v));
        }

        // Simpan pertanyaan user
        try {
            AnalysisChat::create([
                'analysis_type' => $type,
                'analysis_id'   => $id,
                'role'          => 'user',
                'message'       => $request->question,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal simpan chat user: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menyimpan pertanyaan: ' . $e->getMessage()], 500);
        }

        // Ambil history chat (termasuk yang baru disimpan)
        $history = AnalysisChat::where('analysis_type', $type)
            ->where('analysis_id', $id)
            ->orderBy('created_at')
            ->get()
            ->map(fn($h) => [
                'role'    => $h->role,
                'message' => $h->message,
            ])
            ->values()
            ->toArray();

        // Tentukan endpoint FastAPI
        $endpoint = match ($type) {
            'clinical' => 'http://127.0.0.1:8001/chat/clinical',
            'image'    => 'http://127.0.0.1:8001/chat/image',
            'combined' => 'http://127.0.0.1:8001/chat/combined',
        };

        // Kirim ke FastAPI
        try {
            $response = Http::timeout(60)->post($endpoint, [
                'context'  => $context,
                'history'  => $history,
                'question' => $request->question,
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('FastAPI tidak bisa dihubungi: ' . $e->getMessage());
            return response()->json(['error' => 'Layanan AI tidak dapat dihubungi. Pastikan FastAPI berjalan di port 8001.'], 503);
        }

        Log::info('Chat status: ' . $response->status());
        Log::info('Chat body: '   . $response->body());

        if ($response->failed()) {
            Log::error('FastAPI error: ' . $response->body());
            return response()->json([
                'error' => 'Gagal mendapat respons dari AI. Status: ' . $response->status()
            ], 500);
        }

        $answer = $response->json('answer') ?? 'Maaf, tidak ada jawaban dari AI.';

        // Simpan jawaban assistant
        try {
            AnalysisChat::create([
                'analysis_type' => $type,
                'analysis_id'   => $id,
                'role'          => 'assistant',
                'message'       => $answer,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal simpan chat assistant: ' . $e->getMessage());
        }

        return response()->json(['answer' => $answer]);
    }

    public function history($type, $id)
    {
        if (!in_array($type, ['clinical', 'image', 'combined'])) {
            return response()->json([], 200);
        }

        $history = AnalysisChat::where('analysis_type', $type)
            ->where('analysis_id', $id)
            ->orderBy('created_at')
            ->get()
            ->map(fn($h) => [
                'role'    => $h->role,
                'message' => $h->message,
            ])
            ->values();

        return response()->json($history);
    }

    public function clear($type, $id)
    {
        AnalysisChat::where('analysis_type', $type)
            ->where('analysis_id', $id)
            ->delete();

        return response()->json(['success' => true]);
    }
}