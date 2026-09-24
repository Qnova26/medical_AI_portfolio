@extends('layouts.dashboard')

@section('content')

<div class="space-y-6">

    {{-- BARIS 1 --}}
    <div class="grid grid-cols-12 gap-6 items-stretch">

        {{-- AREA INDIKATOR KINERJA UTAMA (KPI) --}}
        <div class="col-span-7">

            <div class="grid grid-cols-2 gap-5">

                {{-- TOTAL PASIEN --}}
                <div class="kpi-card">
                    <div class="kpi-icon bg-blue-100 text-blue-600">
                        <i class="bi bi-people"></i>
                    </div>

                    <div>
                        <p class="kpi-label">Total Pasien</p>

                        <h2 class="kpi-value">
                            {{ number_format($summary['total_patients']) }}
                        </h2>
                    </div>
                </div>

                {{-- ANALISIS HARI INI --}}
                <div class="kpi-card">
                    <div class="kpi-icon bg-emerald-100 text-emerald-600">
                        <i class="bi bi-activity"></i>
                    </div>

                    <div>
                        <p class="kpi-label">Analisis Hari Ini</p>

                        <h2 class="kpi-value">
                            {{ $summary['analyses_today'] }}
                        </h2>
                    </div>
                </div>

                {{-- RISIKO TINGGI --}}
                <div class="kpi-card">
                    <div class="kpi-icon bg-red-100 text-red-500">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>

                    <div>
                        <p class="kpi-label">Risiko Tinggi</p>

                        <h2 class="kpi-value text-red-500">
                            {{ $summary['high_risk'] }}
                        </h2>
                    </div>
                </div>

                {{-- AKURASI ANALISIS --}}
                <div class="kpi-card">
                    <div class="kpi-icon bg-green-100 text-green-600">
                        <i class="bi bi-shield-check"></i>
                    </div>

                    <div>
                        <p class="kpi-label">Akurasi Analisis</p>

                        <h2 class="kpi-value text-green-600">
                            {{ $summary['confidence'] }}%
                        </h2>
                    </div>
                </div>

            </div>

        </div>

        {{-- KARTU KECERDASAN AI --}}
        <div class="col-span-5">

            <div class="ai-card">

                <div class="flex justify-between items-center h-full">

                    <div class="flex-1">

                        <div class="flex items-center gap-2 mb-5">

                            <i class="bi bi-cpu text-lg"></i>

                            <h3 class="font-bold text-[28px] leading-tight">
                                Analisis Klinis AI
                            </h3>

                        </div>

                        <div class="space-y-4 text-[15px]">

                            @foreach($aiInsights as $insight)
                                <p class="line-clamp-2" title="{{ $insight }}">{{ $insight }}</p>
                            @endforeach

                        </div>

                    </div>

                    <div class="border-l border-white/20 pl-5 ml-5 text-center shrink-0">

                        <p class="text-white/80 text-sm">
                            Akurasi AI
                        </p>

                        <h1 class="text-5xl font-bold leading-none mt-2">
                            {{ $summary['confidence'] }}%
                        </h1>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- BARIS 2 --}}
    <div class="grid grid-cols-12 gap-6 items-start">

        {{-- GRAFIK TREN PENYAKIT --}}
        <div class="col-span-8">

            <div class="dashboard-card p-8">

                <div class="flex justify-between items-center mb-8">

                    <div>

                        <h3 class="text-2xl font-bold">
                            Pemantauan Tren Penyakit
                        </h3>

                        <p class="text-slate-500">
                            Kasus Klinis Bulanan
                        </p>

                    </div>

                    <div class="relative inline-block">
                        
                        <select id="trendFilter"
                            class="appearance-none bg-none pl-5 pr-11 py-2.5 rounded-full border border-blue-600 bg-white text-slate-800 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 cursor-pointer transition-all">
                            <option value="7_days">7 Hari Terakhir</option>
                            <option value="30_days">30 Hari Terakhir</option>
                            <option value="6_months" selected>6 Bulan Terakhir</option>
                            <option value="year">Tahun Lalu</option>
                        </select>
                        
                        {{-- Container ikon kustom --}}
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-blue-600">
                            <i class="bi bi-chevron-down text-xs"></i>
                        </div>
                    </div>

                </div>

                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>

            </div>

        </div>

        {{-- SISI KANAN --}}
        <div class="col-span-4 space-y-6">

            {{-- DIAGNOSIS TERTINGGI --}}
            <div class="dashboard-card p-6">

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold">
                        Diagnosis Teratas
                    </h3>
                </div>

                <div class="space-y-5">

                    @foreach($topDiagnoses as $item)

                    <div>

                        <div class="flex justify-between mb-2">

                            <span class="font-medium">
                                {{ $item['name'] }}
                            </span>

                            <span class="font-semibold text-blue-600">
                                {{ $item['count'] }}
                            </span>

                        </div>

                        <div class="progress-bg">

                            <div
                                class="progress-fill"
                                style="width:{{ ($item['count']/30)*100 }}%">
                            </div>

                        </div>

                    </div>

                    @endforeach

                </div>

            </div>

            </div>

    </div>

    {{-- BARIS 3 --}}
    <div class="grid grid-cols-12 gap-6">

        {{-- PENGGUNAAN PROMPT --}}
        <div class="col-span-4 dashboard-card p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-xl font-bold text-slate-800">Penggunaan Instruksi</h3>
                <p class="text-xs text-slate-400 mb-4">Distribusi berdasarkan modalitas</p>
            </div>
            <div class="relative w-full aspect-square max-h-[220px] mx-auto">
                <canvas id="promptChart"></canvas>
            </div>
        </div>

        {{-- DISTRIBUSI TINGKAT AKURASI --}}
        <div class="col-span-4 dashboard-card p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-xl font-bold text-slate-800">Distribusi Akurasi</h3>
                <p class="text-xs text-slate-400 mb-4">Interval akurasi sistem AI</p>
            </div>
            <div class="relative w-full aspect-square max-h-[220px] mx-auto">
                <canvas id="confidenceChart"></canvas>
            </div>
        </div>

        {{-- DISTRIBUSI RISIKO --}}
        <div class="col-span-4 dashboard-card p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-xl font-bold text-slate-800">Distribusi Risiko</h3>
                <p class="text-xs text-slate-400 mb-4">Segmentasi tingkat risiko pasien</p>
            </div>
            <div class="relative w-full h-[220px] mt-2">
                <canvas id="riskChart"></canvas>
            </div>
        </div>
        
    </div>

</div>

@endsection

@push('scripts')

<script>

// Grafik Tren Kasus Bulanan (Line Chart)
let trendChart = new Chart(
document.getElementById('trendChart'),
{
    type:'line',
    data:{
        labels: @json($monthLabels),
        datasets:[{
            data:@json($monthlyCases),
            borderColor:'#2563EB',
            backgroundColor:'rgba(37,99,235,.12)',
            fill:true,
            tension:.4,
            pointRadius:4,
            pointHoverRadius:6
        }]
    },
    options:{
        maintainAspectRatio:false,
        plugins:{
            legend:{
                display:false
            }
        }
    }
});

document.getElementById('trendFilter').addEventListener('change', function() {
    const filterVal = this.value;
    fetch(window.location.pathname + '?ajax=1&filter=' + filterVal)
        .then(res => res.json())
        .then(data => {
            trendChart.data.labels = data.labels;
            trendChart.data.datasets[0].data = data.cases;
            trendChart.update();
        });
});

// Penggunaan Instruksi → doughnut (Berdasarkan Jenis Pemeriksaan)
new Chart(document.getElementById('promptChart'), {
    type: 'doughnut',
    data: {
        labels: @json($promptLabels),
        datasets: [{ data: @json($promptUsage), borderWidth: 0 }]
    },
    options: {
        maintainAspectRatio: false,
        cutout: '62%',
        plugins: { legend: { position: 'bottom', labels: { padding: 10, boxWidth: 10 } } }
    }
});

// Distribusi Akurasi → doughnut
new Chart(document.getElementById('confidenceChart'), {
    type: 'doughnut',
    data: {
        labels: ['>90% (Sangat Yakin)', '80-89% (Cukup Yakin)', '<80% (Kurang Yakin)'],
        datasets: [{ data: @json($confidenceDistribution), borderWidth: 0 }]
    },
    options: {
        maintainAspectRatio: false,
        cutout: '62%',
        plugins: { legend: { position: 'bottom', labels: { padding: 10, boxWidth: 10 } } }
    }
});

// Risiko → bar dengan warna semantik Bahasa Indonesia
new Chart(document.getElementById('riskChart'), {
    type: 'bar',
    data: {
        labels: ['Rendah', 'Sedang', 'Tinggi'],
        datasets: [{
            data: @json($riskDistribution),
            backgroundColor: ['rgba(37,99,235,.15)','rgba(245,158,11,.15)','rgba(239,68,68,.15)'],
            borderColor: ['#2563eb','#f59e0b','#ef4444'],
            borderWidth: 1.5,
            borderRadius: 8
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { x: { grid: { display: false } } }
    }
});

</script>

@endpush