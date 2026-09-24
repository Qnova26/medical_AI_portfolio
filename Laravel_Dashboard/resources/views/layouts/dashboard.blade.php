<!DOCTYPE html>
<html lang="id" 
    x-data="{ 
        darkMode: localStorage.getItem('darkMode') === 'true' 
    }" 
    :class="{ 'dark': darkMode }"
    @keydown.window.prevent.ctrl.k="darkMode = !darkMode">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sistem Pendukung Keputusan Klinis & Analisis Data AI Medis</title>

    {{-- SCRIPT ANTI-FLASH (FOUC) UNTUK DARK MODE --}}
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- GAYA CSS KUSTOM --}}
<style>
body {
    background: #f5f7fb;
}

.bg-white {
    box-shadow: 0 10px 30px rgba(15,23,42,.05);
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 16px;
    color: #64748b;
    transition: .2s;
    margin-bottom: 6px;
}

.menu-item:hover {
    background: #eff6ff;
    color: #2563eb;
}

.active-menu {
    background: linear-gradient(135deg, #2563eb, #14b8a6);
    color: white !important;
}

/* CSS SAPU JAGAT UNTUK DARK MODE V2 */
.dark body, .dark main, .dark header, .dark .bg-\[\#f5f7fb\] {
    background-color: #0f172a !important; /* slate-900 */
}
.dark .bg-white, .dark .dashboard-card, .dark .kpi-card {
    background-color: #1e293b !important; /* slate-800 */
    border-color: #334155 !important;
    box-shadow: 0 10px 30px rgba(0,0,0,.5);
}
.dark .bg-slate-50, .dark .bg-slate-100, .dark .bg-slate-50\/60, .dark .bg-slate-50\/80 {
    background-color: #0f172a !important;
}
/* TEXT COLORS */
.dark .text-slate-800, .dark .text-slate-700, 
.dark h1, .dark h2, .dark h3, .dark h4, .dark h5, .dark h6 {
    color: #f1f5f9 !important; /* slate-100 */
}
.dark .text-slate-600, .dark .text-slate-500, .dark p, .dark span, .dark label, .dark div {
    color: #cbd5e1 !important; /* slate-300 */
}
/* FORMS */
.dark input, .dark select, .dark textarea {
    background-color: #0f172a !important;
    color: #f1f5f9 !important;
    border-color: #334155 !important;
}
.dark input::placeholder, .dark textarea::placeholder {
    color: #64748b !important;
}
/* TABLES */
.dark th {
    background-color: #0f172a !important;
    color: #f8fafc !important;
    border-color: #334155 !important;
}
.dark td, .dark hr {
    border-color: #334155 !important;
    background-color: transparent !important;
}
.dark tr:hover td {
    background-color: #1e293b !important;
}
/* SIDEBAR MENU */
.dark .active-menu {
    background: #0f172a !important;
    color: #60a5fa !important;
}
.dark .menu-item:hover {
    background: #334155 !important;
}
/* BADGES / "STABILO" COLORS FIX */
.dark .bg-emerald-50, .dark .bg-emerald-100 { background-color: rgba(6,78,59,0.5) !important; color: #6ee7b7 !important; border-color: rgba(6,78,59,0.8) !important; }
.dark .text-emerald-600, .dark .text-emerald-700 { color: #34d399 !important; }
.dark .bg-amber-50, .dark .bg-amber-100 { background-color: rgba(120,53,15,0.5) !important; color: #fcd34d !important; border-color: rgba(120,53,15,0.8) !important; }
.dark .text-amber-600, .dark .text-amber-700 { color: #fbbf24 !important; }
.dark .bg-rose-50, .dark .bg-rose-100, .dark .bg-red-50, .dark .bg-red-100 { background-color: rgba(159,18,57,0.5) !important; color: #fda4af !important; border-color: rgba(159,18,57,0.8) !important; }
.dark .text-rose-600, .dark .text-rose-700, .dark .text-red-500, .dark .text-red-600 { color: #fb7185 !important; }
.dark .bg-blue-50, .dark .bg-blue-100 { background-color: rgba(30,58,138,0.5) !important; color: #93c5fd !important; border-color: rgba(30,58,138,0.8) !important; }
.dark .text-blue-600, .dark .text-blue-700 { color: #60a5fa !important; }
.dark .bg-indigo-50, .dark .bg-indigo-100 { background-color: rgba(49,46,129,0.5) !important; color: #a5b4fc !important; border-color: rgba(49,46,129,0.8) !important; }
.dark .text-indigo-600, .dark .text-indigo-700 { color: #818cf8 !important; }
.dark .bg-violet-50, .dark .bg-violet-100 { background-color: rgba(76,29,149,0.5) !important; color: #c4b5fd !important; border-color: rgba(76,29,149,0.8) !important; }
.dark .text-violet-600, .dark .text-violet-700 { color: #a78bfa !important; }
.dark .bg-teal-50, .dark .bg-teal-100 { background-color: rgba(17,94,89,0.5) !important; color: #5eead4 !important; border-color: rgba(17,94,89,0.8) !important; }
.dark .text-teal-600, .dark .text-teal-700 { color: #2dd4bf !important; }
.dark .bg-pink-50, .dark .bg-pink-100 { background-color: rgba(190,24,93,0.5) !important; color: #f9a8d4 !important; border-color: rgba(190,24,93,0.8) !important; }
.dark .text-pink-600, .dark .text-pink-700 { color: #f472b6 !important; }
.dark .bg-slate-200 { background-color: #334155 !important; color: #f1f5f9 !important; border-color: #475569 !important; }
/* BORDERS FIX FOR MODAL */
.dark .border-indigo-100, .dark .border-violet-100, .dark .border-teal-100, .dark .border-slate-100, .dark .border-slate-200 { border-color: #334155 !important; }
/* SPECIFIC OVERRIDES */
.dark .kpi-icon { background-color: transparent !important; }
.dark .ai-card p, .dark .ai-card h1, .dark .ai-card h3 { color: white !important; }

.dashboard-card {
    background: white;
    border-radius: 28px;
    padding: 24px;
    box-shadow: 0 10px 30px rgba(15,23,42,.05);
    display: flex;
    flex-direction: column;
}

.kpi-card {
    background: #fff;
    border-radius: 24px;
    padding: 22px;
    display: flex;
    align-items: center;
    gap: 14px;
    height: 130px;
    box-shadow: 0 10px 30px rgba(15,23,42,.05);
}

.kpi-icon {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}

.kpi-label {
    color: #64748b;
    font-size: 15px;
    line-height: 1.3;
}

.kpi-value {
    font-size: 2rem;
    font-weight: 700;
    line-none: 1;
    margin-top: 4px;
}

.ai-card {
    background: linear-gradient(135deg, #2563EB, #06B6D4);
    border-radius: 28px;
    padding: 24px;
    color: white;
    height: 100%;
    box-shadow: 0 20px 40px rgba(37,99,235,.25);
}

.chart-container {
    height: 280px;
    position: relative;
    width: 100%;
}

.progress-bg {
    width: 100%;
    height: 8px;
    border-radius: 999px;
    background: #edf2f7;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    border-radius: 999px;
    background: linear-gradient(90deg, #2563eb, #06b6d4);
}

canvas {
    max-width: 100%;
}

.dark {
        background-color: #0f172a; /* Warna background utama gelap */
    }
    .dark body {
        background-color: #0f172a;
    }
    .dark .bg-white {
        background-color: #1e293b; /* Warna card gelap */
        box-shadow: 0 10px 30px rgba(0,0,0,.3);
    }
    .dark .text-slate-800 { color: #f1f5f9; }
    .dark .text-slate-700 { color: #cbd5e1; }
    .dark .bg-slate-50 { background-color: #0f172a; }
    .dark .border-slate-100 { border-color: #334155; }
    .dark .bg-slate-50\/80 { background-color: #0f172a; }
</style>

</head>

<body
    class="bg-[#f5f7fb]">

<div class="flex h-screen overflow-hidden">

    @include('partials.sidebar')

    <main class="flex-1 overflow-y-auto bg-[#f5f7fb]">

        <header class="sticky top-0 z-40 bg-[#f5f7fb]/90 backdrop-blur-md px-8 pt-6 pb-2">
    
            <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] p-6">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-5 mb-5">
                    
                    {{-- KOLOM PENCARIAN --}}
                    <div class="relative flex-1 max-w-md">
                        <input
                            type="text"
                            placeholder="Cari pasien, diagnosis, instruksi AI..."
                            class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50/80 text-slate-700 placeholder-slate-400 border border-transparent focus:outline-none focus:border-blue-500/30 focus:bg-white focus:shadow-sm transition-all text-sm">
                        <i class="bi bi-search absolute left-3.5 top-2.5 text-slate-400 text-sm"></i>
                    </div>

                    {{-- MENU KANAN ATAS --}}
                    <div class="flex items-center gap-4 ml-4">
                        <div class="px-3.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-semibold flex items-center gap-2 border border-emerald-100/60 shrink-0">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            AI Online
                        </div>

                        <div class="h-6 w-[1px] bg-slate-200/80"></div>

                        {{-- TOMBOL MODE GELAP/TERANG --}}
                        <button
                            @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                            class="w-9 h-9 rounded-xl text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center justify-center transition-all">
                            <i x-show="!darkMode" class="bi bi-moon text-base"></i>
                            <i x-show="darkMode" class="bi bi-sun text-base"></i>
                        </button>

                        {{-- AVATAR PENGGUNA --}}
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-r from-blue-500 to-teal-500 text-white flex items-center justify-center font-bold text-sm shadow-sm shadow-blue-500/10">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>

                </div>

                {{-- JUDUL HALAMAN DAN TANGGAL --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">
                            Dashboard AI Medis
                        </h1>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Sistem Pendukung Keputusan Klinis & Analisis Data
                        </p>
                    </div>

                    <div class="flex items-center gap-2.5 bg-slate-50/80 px-3.5 py-2 rounded-xl border border-slate-100 self-start sm:self-auto">
                        <i class="bi bi-calendar3 text-slate-400 text-xs"></i>
                        <div class="text-left">
                            <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider leading-none">Tanggal Hari Ini</p>
                            <p class="text-xs font-bold text-slate-700 mt-1 leading-none">
                                {{ now()->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        {{-- AREA KONTEN UTAMA --}}
        <div class="p-10 max-w-[1700px] mx-auto">

            @yield('content')

        </div>

    </main>

</div>

@stack('scripts')

</body>
</html>