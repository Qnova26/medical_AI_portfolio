<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1e293b; }
        .header { text-align: center; border-bottom: 2px solid #7c3aed; padding-bottom: 12px; margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: bold; color: #7c3aed; }
        .subtitle { font-size: 11px; color: #64748b; margin-top: 4px; }
        .section { margin-bottom: 16px; }
        .section-title { font-size: 11px; font-weight: bold; text-transform: uppercase;
                         letter-spacing: 1px; color: #7c3aed; border-bottom: 1px solid #ede9fe;
                         padding-bottom: 4px; margin-bottom: 8px; }
        .label { color: #64748b; }
        .value { font-weight: bold; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: bold; }
        .badge-tinggi { background: #fee2e2; color: #dc2626; }
        .badge-sedang { background: #fef3c7; color: #d97706; }
        .badge-rendah { background: #d1fae5; color: #059669; }
        .box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;
               padding: 10px; margin-top: 4px; font-size: 11px; line-height: 1.6; }
        .box-green  { background: #f0fdf4; border-left: 3px solid #22c55e; }
        .box-violet { background: #f5f3ff; border-left: 3px solid #7c3aed; }
        .footer { margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px;
                  font-size: 10px; color: #94a3b8; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 4px 8px; }
        tr:nth-child(even) td { background: #f8fafc; }
        .img-grid { margin-top: 8px; }
        .img-box { display: inline-block; border: 1px solid #e2e8f0; border-radius: 6px;
                   overflow: hidden; margin-right: 8px; margin-bottom: 8px; }
        .img-box img { width: 220px; height: 220px; object-fit: cover; display: block; }
        .img-label { font-size: 10px; color: #64748b; text-align: center;
                     padding: 4px; background: #f8fafc; }
    </style>
</head>
<body>

<div class="header">
    <div class="title">Laporan Analisis Citra Medis</div>
    <div class="subtitle">AI Medis — Sistem Pendukung Keputusan Klinis</div>
    <div class="subtitle">Digenerate: {{ now()->format('d M Y, H:i') }}</div>
</div>

{{-- Info Pasien --}}
<div class="section">
    <div class="section-title">Informasi Pasien</div>
    <table>
        <tr>
            <td class="label" width="40%">Nama Pasien</td>
            <td class="value">{{ $analysis->patient->nama_pasien ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Analisis</td>
            <td>{{ $analysis->created_at?->format('d M Y, H:i') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">ID Analisis</td>
            <td>IMG-{{ str_pad($analysis->id, 3, '0', STR_PAD_LEFT) }}</td>
        </tr>
    </table>
</div>

{{-- Info Citra --}}
<div class="section">
    <div class="section-title">Info Citra</div>
    <table>
        <tr>
            <td class="label" width="40%">Jenis Citra</td>
            <td>{{ $analysis->image_type ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Bagian Tubuh</td>
            <td>{{ $analysis->body_part ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Prompt / Template</td>
            <td>{{ $analysis->prompt->name ?? '-' }}</td>
        </tr>
    </table>
</div>

{{-- Citra Asli --}}
@php
    $imageFiles = is_string($analysis->image_files)
        ? json_decode($analysis->image_files, true)
        : ($analysis->image_files ?? []);
@endphp

@if(!empty($imageFiles))
<div class="section">
    <div class="section-title">Citra yang Dianalisis</div>
    <div class="img-grid">
        @foreach($imageFiles as $imgPath)
            @php
                $fullPath = storage_path('app/public/' . $imgPath);
                $base64   = '';
                $mime     = 'image/jpeg';
                if (file_exists($fullPath)) {
                    $mime   = mime_content_type($fullPath) ?: 'image/jpeg';
                    $base64 = base64_encode(file_get_contents($fullPath));
                }
            @endphp
            @if($base64)
            <div class="img-box">
                <img src="data:{{ $mime }};base64,{{ $base64 }}" alt="Citra Medis">
                <div class="img-label">Citra Asli</div>
            </div>
            @endif
        @endforeach
    </div>
</div>
@endif

{{-- Hasil Anotasi AI --}}
@php
    $resultImages = is_string($analysis->result_images)
        ? json_decode($analysis->result_images, true)
        : ($analysis->result_images ?? []);
@endphp

@if(!empty($resultImages))
<div class="section">
    <div class="section-title">Hasil Anotasi AI</div>
    <div class="img-grid">
        @foreach($resultImages as $imgPath)
            @php
                $fullPath = storage_path('app/public/' . $imgPath);
                $base64   = '';
                $mime     = 'image/jpeg';
                if (file_exists($fullPath)) {
                    $mime   = mime_content_type($fullPath) ?: 'image/jpeg';
                    $base64 = base64_encode(file_get_contents($fullPath));
                }
            @endphp
            @if($base64)
            <div class="img-box">
                <img src="data:{{ $mime }};base64,{{ $base64 }}" alt="Anotasi AI">
                <div class="img-label">Deteksi otomatis oleh AI</div>
            </div>
            @endif
        @endforeach
    </div>
</div>
@endif

{{-- Hasil AI --}}
@php
    $aiResult = is_string($analysis->ai_result)
        ? json_decode($analysis->ai_result, true)
        : ($analysis->ai_result ?? []);
        
    function safeString($val) {
        if (is_array($val)) {
            $flat = [];
            foreach ($val as $v) {
                $flat[] = is_array($v) || is_object($v) ? json_encode($v) : $v;
            }
            return implode(', ', $flat);
        }
        if (is_object($val)) return json_encode($val);
        return (string) $val;
    }
@endphp

<div class="section">
    <div class="section-title">Hasil Analisis AI</div>
    <table>
        <tr>
            <td class="label" width="40%">Diagnosis</td>
            <td class="value" style="color:#7c3aed;">{{ $analysis->diagnosis_short ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tingkat Risiko</td>
            <td>
                @php $risk = $analysis->risk_level ?? ''; @endphp
                @if($risk === 'Tinggi')
                    <span class="badge badge-tinggi">Tinggi</span>
                @elseif($risk === 'Sedang')
                    <span class="badge badge-sedang">Sedang</span>
                @elseif($risk === 'Rendah')
                    <span class="badge badge-rendah">Rendah</span>
                @else
                    —
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Confidence</td>
            <td class="value">{{ $analysis->confidence_score ? $analysis->confidence_score . '%' : '—' }}</td>
        </tr>
    </table>

    @if(!empty($aiResult['clinical_notes']))
    <div style="margin-top:8px;">
        <div class="label" style="margin-bottom:4px;">Temuan Klinis</div>
        <div class="box">{{ safeString($aiResult['clinical_notes']) }}</div>
    </div>
    @endif

    @if(!empty($aiResult['recommendation']))
    <div style="margin-top:8px;">
        <div class="label" style="margin-bottom:4px;">Rekomendasi</div>
        <div class="box box-green">{{ safeString($aiResult['recommendation']) }}</div>
    </div>
    @endif

    @php
        $aiMedication = $aiResult['medication_recommendation'] ?? $aiResult['medication'] ?? null;
    @endphp
    @if(!empty($aiMedication))
    <div style="margin-top:8px;">
        <div class="label" style="margin-bottom:4px;">Rekomendasi Obat (AI)</div>
        <div class="box box-violet">{{ safeString($aiMedication) }}</div>
    </div>
    @endif
</div>

{{-- Validasi Dokter --}}
<div class="section">
    <div class="section-title">Validasi Dokter</div>
    <table>
        <tr>
            <td class="label" width="40%">Status Validasi</td>
            <td>
                @if($analysis->validation_status === 'confirmed')
                    <span class="badge" style="background:#d1fae5;color:#065f46;">Dikonfirmasi</span>
                @elseif($analysis->validation_status === 'corrected')
                    <span class="badge" style="background:#fef3c7;color:#92400e;">Dikoreksi</span>
                @else
                    <span class="badge" style="background:#f1f5f9;color:#64748b;">Belum Ditinjau</span>
                @endif
            </td>
        </tr>
        @if($analysis->doctor_risk_level)
        <tr>
            <td class="label">Tingkat Risiko (Dokter)</td>
            <td>{{ $analysis->doctor_risk_level }}</td>
        </tr>
        @endif
        @if($analysis->doctor_diagnosis)
        <tr>
            <td class="label">Koreksi Diagnosis</td>
            <td class="value">{{ $analysis->doctor_diagnosis }}</td>
        </tr>
        @endif
    </table>

    @if($analysis->doctor_analysis)
    <div style="margin-top:8px;">
        <div class="label" style="margin-bottom:4px;">Catatan Dokter</div>
        <div class="box" style="background:#eff6ff;border-left:3px solid #3b82f6;">{{ $analysis->doctor_analysis }}</div>
    </div>
    @endif

    @if($analysis->medication_recommendation)
    <div style="margin-top:8px;">
        <div class="label" style="margin-bottom:4px;">Rekomendasi Obat (Dokter)</div>
        <div class="box" style="background:#eff6ff;border-left:3px solid #3b82f6;">{{ $analysis->medication_recommendation }}</div>
    </div>
    @endif
</div>

<div class="footer">
    Dokumen ini digenerate otomatis oleh sistem AI Medis. Bukan pengganti diagnosis dokter profesional.
</div>

</body>
</html>