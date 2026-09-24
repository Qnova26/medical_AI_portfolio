@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">

    <!-- HEADER HALAMAN -->
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight dark:text-black">Wawasan Klinis</h1>
        <p class="text-sm font-medium text-slate-500 mt-0.5">Analitik Kesehatan Populasi & Prediksi AI Langsung</p>
    </div>

    <!-- KARTU RINGKASAN METRIK -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">

        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Kasus</p>
            <h2 class="text-2xl font-extrabold text-slate-800">{{ number_format($summary['total_cases'], 0, ',', '.') }}</h2>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kasus Aktif</p>
            <h2 class="text-2xl font-extrabold text-blue-600">{{ number_format($summary['active_cases'], 0, ',', '.') }}</h2>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Risiko Tinggi</p>
            <h2 class="text-2xl font-extrabold text-rose-500">{{ number_format($summary['high_risk'], 0, ',', '.') }}</h2>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Rerata Kepercayaan AI</p>
            <h2 class="text-2xl font-extrabold text-emerald-500">{{ $summary['confidence'] }}%</h2>
        </div>

    </div>

    <!-- GRAFIK TREN UTAMA & DIAGNOSIS TERTINGGI -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-sm text-slate-800 flex items-center gap-1.5">
                        <i class="bi bi-graph-up text-blue-500"></i> Pemantauan Tren Penyakit
                    </h3>
                    <p class="text-[11px] text-slate-400">Kasus Klinis Bulanan</p>
                </div>
                <button class="text-xs font-semibold px-2.5 py-1 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center gap-1">
                    6 Bulan Terakhir <i class="bi bi-chevron-down text-[10px]"></i>
                </button>
            </div>
            <div class="relative h-[240px]">
                <canvas id="ageChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-sm text-slate-800">Diagnosis Tertinggi</h3>
                    <a href="#" class="text-xs text-blue-500 hover:underline">Lihat Semua</a>
                </div>
                <div class="space-y-3.5">
                    <div>
                        <div class="flex justify-between text-xs font-medium text-slate-700 mb-1">
                            <span>Pneumonia</span> <span class="font-bold">320</span>
                        </div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: 85%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs font-medium text-slate-700 mb-1">
                            <span>Stroke</span> <span class="font-bold">190</span>
                        </div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-sky-400 h-1.5 rounded-full" style="width: 55%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs font-medium text-slate-700 mb-1">
                            <span>COVID-19</span> <span class="font-bold">145</span>
                        </div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-teal-400 h-1.5 rounded-full" style="width: 42%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs font-medium text-slate-700 mb-1">
                            <span>Tumor</span> <span class="font-bold">120</span>
                        </div>
                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-indigo-400 h-1.5 rounded-full" style="width: 35%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEKSI DIAGRAM METRIK DEMOGRAFI -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
            <h3 class="font-bold text-xs text-slate-800 mb-3 flex items-center gap-1.5">
                <i class="bi bi-gender-ambiguous text-blue-500"></i> Distribusi Jenis Kelamin
            </h3>
            <div class="relative h-[160px] flex justify-center">
                <canvas id="genderChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
            <h3 class="font-bold text-xs text-slate-800 mb-3 flex items-center gap-1.5">
                <i class="bi bi-shield-exclamation text-amber-500"></i> Stratifikasi Risiko
            </h3>
            <div class="relative h-[160px] flex justify-center">
                <canvas id="riskChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
            <h3 class="font-bold text-xs text-slate-800 mb-3 flex items-center gap-1.5">
                <i class="bi bi-virus text-teal-500"></i> Distribusi Penyakit
            </h3>
            <div class="relative h-[160px]">
                <canvas id="diseaseInsightChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] flex flex-col justify-between">
            <h3 class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                <i class="bi bi-diagram-3 text-slate-600"></i> Metrik Populasi
            </h3>
            <div class="grid grid-cols-2 gap-2 my-auto">
                <div class="bg-slate-50/70 p-2.5 rounded-lg border border-slate-100/50">
                    <p class="text-[10px] text-slate-400 font-medium uppercase">Rerata Usia</p>
                    <p class="text-base font-bold text-slate-700 mt-0.5">47 Thn</p>
                </div>
                <div class="bg-slate-50/70 p-2.5 rounded-lg border border-slate-100/50">
                    <p class="text-[10px] text-slate-400 font-medium uppercase">Kritis</p>
                    <p class="text-base font-bold text-rose-500 mt-0.5">11 Aktif</p>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL ALARM PASIEN & WAWASAN TREN AI -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-50">
                <h3 class="text-sm font-bold text-slate-800">Peringatan Pasien Risiko Tinggi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[500px]">
                    <thead>
                        <tr class="bg-slate-50/60 text-slate-400 text-[10px] font-bold uppercase tracking-wider border-b border-slate-100">
                            <th class="text-left py-2.5 px-5">ID Pasien</th>
                            <th class="text-left py-2.5 px-5">Nama</th>
                            <th class="text-left py-2.5 px-5">Diagnosis</th>
                            <th class="text-center py-2.5 px-5">Risiko</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-xs text-slate-600">
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="py-2.5 px-5 font-semibold text-slate-400">P001</td>
                            <td class="py-2.5 px-5 font-medium text-slate-800">John Doe</td>
                            <td class="py-2.5 px-5">Pneumonia</td>
                            <td class="py-2.5 px-5 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-rose-50 text-rose-600 border border-rose-100 font-medium text-[10px]">
                                    Tinggi
                                </span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="py-2.5 px-5 font-semibold text-slate-400">P002</td>
                            <td class="py-2.5 px-5 font-medium text-slate-800">Maria Smith</td>
                            <td class="py-2.5 px-5">Stroke</td>
                            <td class="py-2.5 px-5 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-rose-50 text-rose-600 border border-rose-100 font-medium text-[10px]">
                                    Tinggi
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- BLOK INSIGHTS GRADASI -->
        <div class="bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl p-4 text-white shadow-md flex flex-col justify-between">
            <div class="flex items-center gap-2 mb-3">
                <i class="bi bi-stars text-lg text-cyan-300"></i>
                <h2 class="text-sm font-bold tracking-tight">Wawasan Dibuat oleh AI</h2>
            </div>
            <div class="space-y-2.5 text-xs text-blue-50/90 flex-1 flex flex-col justify-center">
                <div class="flex items-start gap-2 bg-white/5 p-2 rounded-lg border border-white/10">
                    <i class="bi bi-check text-cyan-300"></i>
                    <p>Pneumonia menjadi diagnosis dominan dalam 30 hari terakhir.</p>
                </div>
                <div class="flex items-start gap-2 bg-white/5 p-2 rounded-lg border border-white/10">
                    <i class="bi bi-check text-cyan-300"></i>
                    <p>Risiko tertinggi pada kelompok usia &gt;50 tahun.</p>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
Chart.defaults.font.family = "Plus Jakarta Sans, Inter, sans-serif";
Chart.defaults.color = "#a6b5cc";
Chart.defaults.font.size = 11;

// 1. Grafik Distribusi Jenis Kelamin
new Chart(document.getElementById('genderChart'), {
    type: 'doughnut',
    data: {
        labels: ['Laki-laki', 'Perempuan'],
        datasets: [{
            data: [58, 42],
            backgroundColor: ['#3b82f6', '#a855f7'],
            borderWidth: 0,
            hoverOffset: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 10, font: { size: 10 } } }
        }
    }
});

// 2. Grafik Tren Kasus Bulanan
new Chart(document.getElementById('ageChart'), {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
        datasets: [{
            label: 'Kasus Bulanan',
            data: [120, 140, 180, 150, 220, 260],
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.04)',
            fill: true,
            tension: 0.4,
            borderWidth: 2.5,
            pointRadius: 3,
            pointBackgroundColor: '#3b82f6'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { grid: { color: '#f8fafc' }, border: { dash: [5, 5] }, ticks: { stepSize: 20 } },
            x: { grid: { display: false } }
        }
    },
    plugins: [{
        beforeInit: function(chart) {
            chart.options.plugins.legend.display = false;
        }
    }]
});

// 3. Grafik Distribusi Penyakit 
new Chart(document.getElementById('diseaseInsightChart'), {
    type: 'bar',
    data: {
        labels: ['Pneu', 'Strk', 'Cvd', 'Tmr', 'Ari'],
        datasets: [{
            data: [320, 190, 145, 120, 95],
            backgroundColor: '#0ea5e9',
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { grid: { display: false } },
            x: { grid: { display: false } }
        }
    },
    plugins: [{
        beforeInit: function(chart) {
            chart.options.plugins.legend.display = false;
        }
    }]
});

// 4. Grafik Stratifikasi Risiko
new Chart(document.getElementById('riskChart'), {
    type: 'pie',
    data: {
        labels: ['Rendah', 'Sedang', 'Tinggi'],
        datasets: [{
            data: [67, 22, 11],
            backgroundColor: ['#10b981', '#f59e0b', '#f43f5e'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 10, font: { size: 10 } } }
        }
    }
});
</script>
@endpush
@endsection