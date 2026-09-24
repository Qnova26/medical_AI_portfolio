<aside class="w-64 bg-white border-r border-slate-200 shadow-sm flex flex-col min-h-screen shrink-0">

    {{-- LOGO DAN IDENTITAS UTAMA --}}
    <div class="p-6">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-r from-blue-500 to-teal-400 flex items-center justify-center text-white shrink-0">
                <i class="bi bi-heart-pulse-fill text-xl"></i>
            </div>
            <div>
                <h1 class="font-bold text-lg text-slate-800 leading-tight">AI Medis</h1>
                <p class="text-xs text-slate-500">Pendukung Keputusan Klinis</p>
            </div>
        </div>
    </div>

    <div class="border-b border-slate-200"></div>

    {{-- NAVIGASI UTAMA --}}
    <nav class="flex-1 overflow-y-auto p-4">

        {{-- KATEGORI 1: KECERDASAN MEDIS --}}
        <p class="text-xs uppercase font-bold text-slate-400 mb-3 tracking-wider">Kecerdasan Medis</p>
        
        <a href="/dashboard" class="menu-item {{ request()->is('dashboard') ? 'active-menu' : '' }}">
            <i class="bi bi-grid"></i>
            <span>Dashboard</span>
        </a>

        <!-- <a href="{{ route('clinical.insights') }}" class="menu-item">
            <i class="bi bi-activity"></i>
            <span>Wawasan Klinis</span>
        </a>

        <a href="{{ route('disease-trends.index') }}" class="menu-item">
            <i class="bi bi-graph-up-arrow"></i>
            <span>Tren Penyakit</span>
        </a>

        <a href="{{ route('risk-monitoring.index') }}" class="menu-item">
            <i class="bi bi-shield-exclamation"></i>
            <span>Pemantauan Risiko</span>
        </a> -->

        {{-- KATEGORI 2: ANALISIS AI --}}
        <p class="text-xs uppercase font-bold text-slate-400 mt-6 mb-3 tracking-wider">Analisis AI</p>

        <a href="{{ route('analysis.create') }}" class="menu-item {{ request()->routeIs('analysis.create') ? 'active-menu' : '' }}">
            <i class="bi bi-cpu"></i>
            <span>Analisis Baru</span>
        </a>

        <!-- {{-- 
        <a href="{{ route('prompt-comparison.index') }}" class="menu-item">
            <i class="bi bi-layers"></i>
            <span>Perbandingan Instruksi</span>
        </a> 
        --}} -->

        <a href="{{ route('analysis-picture.create') }}" class="menu-item {{ request()->routeIs('analysis-picture.create') ? 'active-menu' : '' }}">
            <i class="bi bi-image"></i>
            <span>Analisis Citra Medis</span>
        </a>

        <a href="{{ route('analysis-history.index') }}" class="menu-item {{ request()->routeIs('analysis-history.*') || request()->routeIs('analysis.result') || request()->routeIs('analysis-picture.result') ? 'active-menu' : '' }}">
            <i class="bi bi-clock-history"></i>
            <span>Riwayat Analisis</span>
        </a>

        {{-- KATEGORI 3: MANAJEMEN PASIEN --}}
        <p class="text-xs uppercase font-bold text-slate-400 mt-6 mb-3 tracking-wider">Manajemen Pasien</p>

        <a href="{{ route('patients.index') }}" class="menu-item {{ request()->routeIs('patients.*') ? 'active-menu' : '' }}">
            <i class="bi bi-people"></i>
            <span>Data Pasien</span>
        </a>

        <!-- <a href="{{ route('medical-records.index') }}" class="menu-item">
            <i class="bi bi-folder2-open"></i>
            <span>Rekam Medis</span>
        </a> -->

        {{-- KATEGORI 4: REKAYASA PROMPT --}}
        <p class="text-xs uppercase font-bold text-slate-400 mt-6 mb-3 tracking-wider">Rekayasa Instruksi</p>

        <a href="/prompts" class="menu-item {{ request()->is('prompts*') ? 'active-menu' : '' }}">
            <i class="bi bi-chat-square-text"></i>
            <span>Template Prompt</span>
        </a>

        <a href="{{ route('prompt-analytics.index') }}" class="menu-item {{ request()->routeIs('prompt-analytics.*') ? 'active-menu' : '' }}">
            <i class="bi bi-bar-chart-line"></i>
            <span>Analisis Instruksi</span>
        </a>

    </nav>

    {{-- BAGIAN AKUN PENGGUNA & KELUAR --}}
    <div class="p-4 border-t border-slate-200 bg-slate-50/60">
        <div class="flex items-center justify-between gap-2">
            <div class="truncate">
                <p class="font-semibold text-sm text-slate-800 truncate">
                    {{ Auth::user()->name }}
                </p>
                <p class="text-xs text-slate-500">Analis Medis</p>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" class="text-red-500 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition-colors" title="Keluar Sistem">
                    <i class="bi bi-box-arrow-right text-lg"></i>
                </button>
            </form>
        </div>
    </div>

</aside>