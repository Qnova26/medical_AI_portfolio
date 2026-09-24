@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">

    <!-- HEADER SECTION -->
    <div>
    
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight dark:text-black">
            Tren Penyakit
        </h1>
        
        <p class="text-sm font-medium text-slate-500 mt-0.5">
            Pantau evolusi penyakit, peringatan wabah, dan tren kesehatan populasi
        </p>
    </div>

    <!-- KPI CARDS SECTION -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">

        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Kasus</p>
            <h2 class="text-2xl font-extrabold text-blue-600 dark:text-blue-400">{{ number_format($summary['total_cases']) }}</h2>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Penyakit Meningkat</p>
            <h2 class="text-2xl font-extrabold text-orange-600">{{ $summary['rising_diseases'] }}</h2>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Penyakit Menurun</p>
            <h2 class="text-2xl font-extrabold text-emerald-500">{{ $summary['declining_diseases'] }}</h2>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Peringatan Wabah</p>
            <h2 class="text-2xl font-extrabold text-rose-500">{{ $summary['outbreak_alerts'] }}</h2>
        </div>
    </div>

    <!-- MAIN MONITORING CHARTS  -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        <div class="xl:col-span-2 bg-white rounded-xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">

            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-sm text-slate-800 flex items-center gap-1.5">
                        <i class="bi bi-graph-up text-blue-500"></i> Tren Kasus Bulanan
                    </h3>
                    <p class="text-[11px] text-slate-400">Evolusi Kasus Penyakit Secara Berkala</p>
                </div>

                <button class="text-xs font-semibold px-2.5 py-1 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center gap-1">
                     6 Bulan Terakhir <i class="bi bi-chevron-down text-[10px]"></i>
                </button>
            </div>

            <div class="relative h-[240px]">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        <!-- Top Diagnoses Progress Bar Layout -->
        <div class="bg-white rounded-xl p-5 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-sm text-slate-800">Diagnosis Terbanyak</h3>
                    <a href="#" class="text-xs text-blue-500 hover:underline">Lihat Semua</a>
                </div>

                <div class="space-y-3.5">
                    <div>
                        <div class="flex justify-between text-xs font-medium text-slate-700 mb-1">
                            <span>Pneumonia</span> <span class="font-bold">{{ $topDiseases['Pneumonia'] ?? 320 }}</span>
                        </div>

                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: 85%"></div>
                        </div>
                        
                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-medium text-slate-700 mb-1">
                            <span>Stroke</span> <span class="font-bold">{{ $topDiseases['Stroke'] ?? 190 }}</span>
                        </div>

                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-sky-400 h-1.5 rounded-full" style="width: 55%"></div>
                        </div>

                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-medium text-slate-700 mb-1">
                            <span>COVID-19</span> <span class="font-bold">{{ $topDiseases['COVID-19'] ?? 145 }}</span>
                        </div>

                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-teal-400 h-1.5 rounded-full" style="width: 42%"></div>
                        </div>

                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-medium text-slate-700 mb-1">
                            <span>Dengue</span> <span class="font-bold">{{ $topDiseases['Dengue'] ?? 120 }}</span>
                        </div>

                        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-indigo-400 h-1.5 rounded-full" style="width: 35%"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LOWER ANALYTICS & MONITOR LAYOUT -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <!-- MONITOR TABLE -->
        <div class="xl:col-span-2 bg-white rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-50 flex justify-between items-center">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-1.5">
                    <i class="bi bi-activity text-slate-700"></i> Monitor Perubahan Penyakit
                </h3>
                <span class="text-slate-400 text-xs font-medium">30 Hari Terakhir</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[500px]">
                    <thead>
                        <tr class="bg-slate-50/60 text-slate-400 text-[10px] font-bold uppercase tracking-wider border-b border-slate-100">
                            <th class="text-left py-2.5 px-5">Nama Penyakit</th>
                            <th class="text-left py-2.5 px-5">Tren Perubahan</th>
                            <th class="text-center py-2.5 px-5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-xs text-slate-600">
                        @foreach($diseaseChanges as $disease)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="py-2.5 px-5 font-semibold text-slate-400">
                                {{ $disease['name'] }}
                            </td>
                            <td class="py-2.5 px-5">
                                @if($disease['status'] == 'up')
                                    <span class="text-rose-600 font-bold">▲ {{ $disease['change'] }}</span>
                                @elseif($disease['status'] == 'down')
                                    <span class="text-emerald-600 font-bold">▼ {{ $disease['change'] }}</span>
                                @else
                                    <span class="text-slate-500 font-bold">► {{ $disease['change'] }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-5 text-center">
                                @if($disease['status'] == 'up')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded bg-rose-50 text-rose-600 border border-rose-100 font-medium text-[10px]">
                                        Meningkat
                                    </span>
                                @elseif($disease['status'] == 'down')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-100 font-medium text-[10px]">
                                        Menurun
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded bg-slate-50 text-slate-600 border border-slate-200 font-medium text-[10px]">
                                        Stabil
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SIDEBAR INSIGHTS -->
        <div class="space-y-4 flex flex-col justify-between">
            <!-- Peringatan Wabah Card -->
            <div class="bg-white rounded-xl p-4 border border-slate-100 border-l-4 border-l-rose-500 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
                <div class="flex items-center gap-2 mb-2">
                    <i class="bi bi-exclamation-triangle-fill text-rose-500 text-base"></i>
                    <h3 class="text-xs font-bold text-slate-800">Peringatan Wabah</h3>
                </div>
                <p class="text-[11px] text-slate-600 leading-relaxed">
                    Kasus Pneumonia meningkat <span class="text-rose-600 font-bold">18%</span> dalam 30 hari terakhir. Pemantauan ketat direkomendasikan untuk pasien di atas usia 50 tahun.
                </p>
            </div>

            <!-- AI Insights Card -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl p-4 text-white shadow-md flex flex-col justify-between flex-1">
                <div class="flex items-center gap-2 mb-3">
                    <i class="bi bi-stars text-base text-cyan-300"></i>
                    <h2 class="text-xs font-bold tracking-tight">Wawasan Tren AI</h2>
                </div>
                <div class="space-y-2 text-[11px] text-blue-50/90 flex-1 flex flex-col justify-center">
                    <div class="flex items-start gap-2 bg-white/5 p-2 rounded-lg border border-white/10">
                        <i class="bi bi-check text-cyan-300 mt-0.5"></i>
                        <p>Pneumonia tetap menjadi diagnosis paling dominan di semua kelompok umur.</p>
                    </div>
                    <div class="flex items-start gap-2 bg-white/5 p-2 rounded-lg border border-white/10">
                        <i class="bi bi-check text-cyan-300 mt-0.5"></i>
                        <p>Lonjakan signifikan terdeteksi pada klaster pasien lansia (>50 tahun).</p>
                    </div>
                </div>
                <div class="pt-2 mt-2 border-t border-white/10 flex justify-between items-center text-[10px] text-blue-200">
                    <span>Akurasi Prediksi:</span>
                    <span class="font-mono bg-white/10 px-1.5 py-0.5 rounded text-white font-bold">91%</span>
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

const monthlyTrend = @json($monthlyTrend);

// Line Chart Tren Bulanan
new Chart(document.getElementById('monthlyTrendChart'), {
    type: 'line',
    data: {
        labels: Object.keys(monthlyTrend),
        datasets: [{
            label: 'Jumlah Kasus',
            data: Object.values(monthlyTrend),
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
            y: { grid: { color: '#f8fafc' }, border: { dash: [5, 5] } },
            x: { grid: { display: false } }
        },
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
@endsection