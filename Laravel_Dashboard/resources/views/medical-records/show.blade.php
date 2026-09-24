@extends('layouts.dashboard')

@section('content')

<div class="space-y-6 max-w-4xl mx-auto">

    {{-- HEADER DETAIL DATA --}}
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('medical-records.index') }}" 
               class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 flex items-center justify-center transition-colors shadow-sm"
               title="Kembali ke Daftar">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-slate-800 dark:text-black tracking-tight">
                    Detail Rekam Medis
                </h1>
                <p class="text-sm font-medium text-slate-500 mt-0.5">
                    Informasi lengkap hasil pemeriksaan klinis pasien dan status analisis log AI.
                </p>
            </div>
        </div>

        {{-- TOMBOL EDIT CEPAT --}}
        <a href="{{ route('medical-records.edit', 1) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-200 font-medium transition-all shadow-sm text-sm">
            <i class="bi bi-pencil"></i>
            <span class="hidden sm:inline">Ubah Data</span>
        </a>
    </div>

    {{-- KARTU UTAMA DATA REKAM MEDIS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- BLOK INFORMASI PASIEN & KLINIS (KIRI) --}}
        <div class="dashboard-card p-8 md:col-span-2 space-y-6">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-4">
                <i class="bi bi-person-lines-fill text-cyan-600 text-lg"></i>
                <h3 class="font-bold text-slate-800 text-base">Informasi Klinis Pasien</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                        Nomor Rekam Medis
                    </h4>
                    <p class="text-base font-bold text-slate-900 bg-slate-50 px-3 py-2 rounded-xl border border-slate-100 inline-block">
                        RM-001
                    </p>
                </div>

                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                        Nama Lengkap Pasien
                    </h4>
                    <p class="text-base font-bold text-slate-800 pt-1">
                        John Doe
                    </p>
                </div>

                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                        Jenis Pemeriksaan
                    </h4>
                    <p class="pt-1">
                        <span class="px-2.5 py-1 rounded-lg bg-cyan-50 text-cyan-700 font-semibold text-xs border border-cyan-100">
                            Rontgen Toraks (X-Ray)
                        </span>
                    </p>
                </div>

                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                        Dokter Pemeriksa
                    </h4>
                    <p class="text-base font-bold text-slate-800 pt-1">
                        dr. Smith, Sp.Rad
                    </p>
                </div>

            </div>
        </div>

        {{-- BLOK STATUS AI & METADATA (KANAN) --}}
        <div class="dashboard-card p-8 space-y-6 bg-slate-50/50">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-4">
                <i class="bi bi-cpu text-blue-600 text-lg"></i>
                <h3 class="font-bold text-slate-800 text-base">Status Sistem AI</h3>
            </div>

            <div class="space-y-4">
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Status Analisis
                    </h4>
                    <span class="inline-flex items-center gap-1.5 bg-cyan-50 text-cyan-700 px-3 py-1 rounded-full text-xs font-bold border border-cyan-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-500 animate-pulse"></span>
                        Dianalisis AI
                    </span>
                </div>

                <div class="border-t border-slate-200/60 my-2"></div>

                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                        Tanggal Masuk Sistem
                    </h4>
                    <p class="text-xs font-medium text-slate-600">
                        01 Jun 2026, 09:15 WITA
                    </p>
                </div>

                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                        Terakhir Diperbarui
                    </h4>
                    <p class="text-xs font-medium text-slate-600">
                        01 Jun 2026, 10:30 WITA
                    </p>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection