@extends('layouts.dashboard')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight dark:text-black">
            Analitik Kinerja Prompt
        </h1>
        <p class="text-sm font-medium text-slate-500 mt-0.5">
            Pemantauan metrik eksekusi, tingkat kepercayaan (<span class="italic">confidence score</span>), dan efisiensi model LLM.
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] flex flex-col justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Eksekusi</span>
            <h2 class="text-3xl font-extrabold text-slate-800 mt-2 font-mono">
                {{ number_format($summary['executions'], 0, ',', '.') }}
            </h2>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] flex flex-col justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Prompt Aktif</span>
            <h2 class="text-3xl font-extrabold text-blue-600 mt-2 font-mono">
                {{ $summary['active_prompts'] }} <span class="text-xs text-slate-400 font-sans font-normal uppercase">Template</span>
            </h2>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] flex flex-col justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rerata Kepercayaan</span>
            <h2 class="text-3xl font-extrabold text-emerald-600 mt-2 font-mono">
                {{ $summary['avg_confidence'] }}<span class="text-xl font-bold">%</span>
            </h2>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] flex flex-col justify-between">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Akurasi Terbaik</span>
            <h2 class="text-base font-bold text-slate-800 mt-3 truncate text-indigo-600" title="{{ $summary['best_prompt'] }}">
                @if($summary['best_prompt'] == 'Radiology Expert')
                    Pakar Radiologi
                @else
                    {{ $summary['best_prompt'] }}
                @endif
            </h2>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
            <h3 class="font-bold text-sm text-slate-700 mb-4 flex items-center gap-2">
                <span class="w-1.5 h-3.5 bg-blue-600 rounded-full block"></span> Distribusi Penggunaan Prompt
            </h3>
            <div class="relative max-h-[260px] flex justify-center">
                <canvas id="usageChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] lg:col-span-2">
            <h3 class="font-bold text-sm text-slate-700 mb-4 flex items-center gap-2">
                <span class="w-1.5 h-3.5 bg-emerald-600 rounded-full block"></span> Tingkat Akurasi Kepercayaan (%)
            </h3>
            <div class="relative max-h-[260px]">
                <canvas id="confidenceChart"></canvas>
            </div>
        </div>

    </div>

    <div class="bg-white p-5 rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
        <h3 class="font-bold text-sm text-slate-700 mb-4 flex items-center gap-2">
            <span class="w-1.5 h-3.5 bg-indigo-600 rounded-full block"></span> Tren Pertumbuhan Eksekusi Bulanan
        </h3>
        <div class="relative max-h-[280px]">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <div class="bg-white rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] overflow-hidden xl:col-span-2">
            <div class="p-5 border-b border-slate-50">
                <h3 class="text-sm font-bold text-slate-700">
                    Tabel Peringkat Efisiensi Prompt
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-5">Nama Komponen Prompt</th>
                            <th class="py-3 px-4 text-center">Frekuensi Pakai</th>
                            <th class="py-3 px-4 text-center">Skor Validitas</th>
                            <th class="py-3 px-5 text-center">Rasio Keberhasilan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @foreach($performanceTable as $prompt)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="py-3.5 px-5 font-semibold text-slate-800">
                                @if($prompt['name'] == 'Radiology Expert') Pakar Radiologi
                                @elseif($prompt['name'] == 'Clinical Summary') Ringkasan Klinis
                                @elseif($prompt['name'] == 'ECG Analysis') Analisis EKG
                                @elseif($prompt['name'] == 'MRI Analysis') Analisis MRI
                                @else {{ $prompt['name'] }} @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-medium text-slate-600">
                                {{ number_format($prompt['usage'], 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-emerald-600">
                                {{ $prompt['confidence'] }}%
                            </td>
                            <td class="py-3.5 px-5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $prompt['success'] }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Blok Insights Kecerdasan Artifisial -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-4 shadow-md text-white flex flex-col justify-between xl:col-span-3">
            <div>

                <div class="flex items-center gap-2 mb-3 pb-1 border-b border-white/10">
                    <i class="bi bi-stars text-white text-base"></i>
                    <h3 class="text-xs font-bold tracking-tight text-white uppercase tracking-wider">
                        Insight Tren AI
                    </h3>
                </div>

                <!-- Daftar Wawasan -->
                <div class="space-y-2 text-[11px] font-medium leading-relaxed">
                    
                    @foreach($aiInsights as $insight)
                        <div class="bg-white/5 border border-white/10 rounded-md py-1.5 px-3 flex items-center gap-2.5 hover:bg-white/10 transition-colors">
                            <i class="bi bi-check-xs border border-white/25 rounded px-0.5 text-[9px] bg-white/10 text-white flex items-center justify-center h-3.5 w-3.5">✓</i>
                            <span class="text-xs">{!! $insight !!}</span>
                        </div>
                    @endforeach

                </div>
            </div>
            
            <!-- Footer Akurasi Prediksi -->
            <div class="mt-4 pt-2.5 border-t border-white/10 text-[11px] font-medium flex justify-between items-center text-slate-100/80">
                <span>Akurasi Prediksi:</span>
                <span class="font-mono font-bold bg-white/15 px-1.5 py-0.5 rounded border border-white/20 text-[10px]">
                    {{ $summary['avg_confidence'] }}%
                </span>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Global Typography Overrides untuk Chart.js agar senada dengan UI
Chart.defaults.font.family = 'Plus Jakarta Sans, Inter, sans-serif';
Chart.defaults.font.size = 11;
Chart.defaults.color = '#94a3b8';

// 1. Grafik Distribusi Penggunaan
new Chart(document.getElementById('usageChart'), {
    type: 'doughnut',
    data: {
        labels: @json(array_keys($usageData)),
        datasets: [{
            data: @json(array_values($usageData)),
            backgroundColor: ['#2563eb', '#3b82f6', '#0ea5e9', '#06b6d4', '#64748b'],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } }
        }
    }
});

// 2. Grafik Skor Kepercayaan
new Chart(document.getElementById('confidenceChart'), {
    type: 'bar',
    data: {
        labels: @json(array_keys($confidenceData)),
        datasets: [{
            label: 'Skor Rerata (%)',
            data: @json(array_values($confidenceData)),
            backgroundColor: '#10b981',
            borderRadius: 6,
            maxBarThickness: 32
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { min: 0, max: 100, grid: { borderDash: [4, 4] } },
            x: { grid: { display: false } }
        }
    }
});

// 3. Grafik Tren Eksekusi Bulanan
new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels: @json(array_keys($monthlyUsage)),
        datasets: [{
            label: 'Frekuensi Pemanggilan',
            data: @json(array_values($monthlyUsage)),
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99, 102, 241, 0.04)',
            fill: true,
            tension: 0.35,
            borderWidth: 3,
            pointRadius: 4,
            pointBackgroundColor: '#6366f1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { borderDash: [4, 4] } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
@endsection