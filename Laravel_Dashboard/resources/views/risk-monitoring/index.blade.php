@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">

    <!-- HEADER HALAMAN -->
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight dark:text-black">
            Pemantauan Risiko
        </h1>
        <p class="text-sm font-medium text-slate-500 mt-0.5">
            Pantau tingkat risiko pasien dan peringatan kritis
        </p>
    </div>

    <!-- KARTU RINGKASAN METRIK -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Risiko Tinggi</p>
            <h2 class="text-2xl font-extrabold text-red-500">{{ $summary['high_risk'] }}</h2>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Risiko Sedang</p>
            <h2 class="text-2xl font-extrabold text-orange-500">{{ $summary['medium_risk'] }}</h2>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Risiko Rendah</p>
            <h2 class="text-2xl font-extrabold text-emerald-500">{{ $summary['low_risk'] }}</h2>
        </div>
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Peringatan Kritis</p>
            <h2 class="text-2xl font-extrabold text-rose-600">{{ $summary['critical_alerts'] }}</h2>
        </div>
    </div>

    <!-- GRAFIK ANALISIS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
            <h3 class="font-bold text-xs text-slate-800 mb-3 flex items-center gap-1.5">
                <i class="bi bi-pie-chart text-blue-500"></i> Distribusi Risiko
            </h3>
            <div class="relative h-[160px] flex justify-center">
                <canvas id="riskDistributionChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
            <h3 class="font-bold text-xs text-slate-800 mb-3 flex items-center gap-1.5">
                <i class="bi bi-bar-chart-line text-indigo-500"></i> Risiko Berdasarkan Kelompok Usia
            </h3>
            <div class="relative h-[160px]">
                <canvas id="riskAgeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- STATUS KRITIS & WAWASAN AI -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white rounded-xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] border-l-4 border-l-rose-500 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-xs text-slate-800 flex items-center gap-1.5 mb-3">
                    <i class="bi bi-exclamation-octagon-fill text-rose-500"></i> Status Risiko Kritis
                </h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    <span class="font-bold text-rose-600">4 pasien</span> memerlukan perhatian segera dari petugas medis. Skor risiko tertinggi yang terdeteksi: <span class="font-bold text-slate-800">95%</span>.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-3 mt-4">
                <div class="bg-slate-50/70 p-2.5 rounded-lg border border-slate-100/50">
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Rerata Skor Risiko</p>
                    <p class="text-sm font-bold text-slate-700 mt-0.5">64.5%</p>
                </div>
                <div class="bg-slate-50/70 p-2.5 rounded-lg border border-slate-100/50">
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Tindakan Diperlukan</p>
                    <p class="text-sm font-bold text-rose-500 mt-0.5">Segera</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl p-4 text-white shadow-md flex flex-col justify-between">
            <div class="flex items-center gap-2 mb-2">
                <i class="bi bi-stars text-base text-cyan-300"></i>
                <h2 class="text-xs font-bold tracking-tight">Analisis Risiko AI</h2>
            </div>
            <div class="space-y-1.5 text-[11px] text-blue-50/90 flex-1 flex flex-col justify-center">
                <div class="flex items-start gap-1.5 bg-white/5 p-1.5 rounded-lg border border-white/10">
                    <i class="bi bi-check-circle-fill text-cyan-300 mt-0.5"></i>
                    <p>Sebagian besar pasien berisiko tinggi berada pada kelompok usia >50 tahun.</p>
                </div>
                <div class="flex items-start gap-1.5 bg-white/5 p-1.5 rounded-lg border border-white/10">
                    <i class="bi bi-check-circle-fill text-cyan-300 mt-0.5"></i>
                    <p>Penyakit pernapasan menyumbang 42% dari seluruh kasus kritis.</p>
                </div>
            </div>
            <div class="pt-1.5 mt-2 border-t border-white/10 flex justify-between items-center text-[9px] text-blue-200">
                <span>Tingkat Kepercayaan:</span>
                <span class="font-mono bg-white/10 px-1 rounded text-white font-bold">93%</span>
            </div>
        </div>
    </div>

    <!-- TABEL DATA PASIEN -->
    <div class="bg-white rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-50 flex justify-between items-center">
            <h3 class="text-sm font-bold text-slate-800">Pasien Risiko Tinggi</h3>
            <span class="text-[11px] text-slate-400 font-medium">Pemantauan Langsung</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[500px]">
                <thead>
                    <tr class="bg-slate-50/60 text-slate-400 text-[10px] font-bold uppercase tracking-wider border-b border-slate-100">
                        <th class="text-left py-2.5 px-5">ID Pasien</th>
                        <th class="text-left py-2.5 px-5">Nama</th>
                        <th class="text-left py-2.5 px-5">Diagnosis</th>
                        <th class="text-center py-2.5 px-5">Skor Risiko</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs text-slate-600">
                    @foreach($criticalPatients as $patient)
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="py-2.5 px-5 font-semibold text-slate-400">{{ $patient['id'] }}</td>
                        <td class="py-2.5 px-5 font-medium text-slate-800">{{ $patient['name'] }}</td>
                        <td class="py-2.5 px-5">{{ $patient['diagnosis'] }}</td>
                        <td class="py-2.5 px-5 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-rose-50 text-rose-600 border border-rose-100 font-medium text-[10px]">
                                {{ $patient['risk'] }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
Chart.defaults.font.family = "Plus Jakarta Sans, Inter, sans-serif";
Chart.defaults.color = "#a6b5cc";
Chart.defaults.font.size = 10;

// 1. Grafik Distribusi Risiko
const riskDistribution = @json($riskDistribution);

// Menerjemahkan label objek dari penanda key aslinya jika diperlukan di frontend
const indonesianLabels = {
    'High': 'Tinggi',
    'Medium': 'Sedang',
    'Low': 'Rendah'
};
const translatedLabels = Object.keys(riskDistribution).map(label => indonesianLabels[label] || label);

new Chart(document.getElementById('riskDistributionChart'), {
    type: 'doughnut',
    data: {
        labels: translatedLabels,
        datasets: [{
            data: Object.values(riskDistribution),
            backgroundColor: ['#f43f5e', '#f59e0b', '#10b981'], // Warna Tinggi, Sedang, Rendah
            borderWidth: 0,
            hoverOffset: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { 
                position: 'bottom', 
                labels: { boxWidth: 8, padding: 8, font: { size: 9 } } 
            }
        }
    }
});

// 2. Grafik Risiko Berdasarkan Kelompok Usia
const riskByAge = @json($riskByAge);
new Chart(document.getElementById('riskAgeChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(riskByAge),
        datasets: [{
            label: 'Pasien',
            data: Object.values(riskByAge),
            backgroundColor: '#6366f1', // Warna grafik indigo
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { grid: { display: false }, ticks: { stepSize: 1 } },
            x: { grid: { display: false } }
        },
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
@endsection