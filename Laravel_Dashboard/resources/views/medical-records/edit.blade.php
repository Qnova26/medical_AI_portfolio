@extends('layouts.dashboard')

@section('content')

<div class="space-y-6 max-w-4xl mx-auto">

    {{-- HEADER FORMULIR UBHA DATA --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('medical-records.index') }}" 
           class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-slate-50 flex items-center justify-center transition-colors shadow-sm"
           title="Kembali">
            <i class="bi bi-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-white tracking-tight">
                Ubah Rekam Medis
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Perbarui informasi rekam medis pasien untuk penyesuaian data klinis dan analisis AI.
            </p>
        </div>
    </div>

    {{-- KARTU FORMULIR --}}
    <div class="dashboard-card p-8">

        <form action="{{ route('medical-records.update', 1) }}" method="POST" class="space-y-6">

            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- NOMOR REKAM MEDIS --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 block">
                        Nomor Rekam Medis
                    </label>
                    <input
                        type="text"
                        name="record_number"
                        value="RM-001"
                        required
                        class="w-full pl-4 pr-4 py-3 rounded-xl bg-slate-50/50 text-slate-800 border border-slate-200/80 focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-semibold">
                </div>

                {{-- NAMA PASIEN --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 block">
                        Nama Lengkap Pasien
                    </label>
                    <input
                        type="text"
                        name="patient"
                        value="John Doe"
                        required
                        class="w-full pl-4 pr-4 py-3 rounded-xl bg-slate-50/50 text-slate-800 border border-slate-200/80 focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all text-sm">
                </div>

                {{-- JENIS PEMERIKSAAN (DROPDOWN) --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 block">
                        Jenis Pemeriksaan
                    </label>
                    <div class="relative">
                        <select
                            name="type"
                            required
                            class="w-full appearance-none pl-4 pr-11 py-3 rounded-xl bg-slate-50/50 text-slate-800 border border-slate-200/80 focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all text-sm cursor-pointer">
                            <option value="Rontgen Toraks" selected>Rontgen Toraks (X-Ray)</option>
                            <option value="MRI">MRI</option>
                            <option value="CT Scan">CT Scan</option>
                            <option value="EKG Jantung">EKG Jantung</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400">
                            <i class="bi bi-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                {{-- DOKTER PEMERIKSA --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 block">
                        Dokter Pemeriksa
                    </label>
                    <input
                        type="text"
                        name="doctor"
                        value="dr. Smith, Sp.Rad"
                        required
                        class="w-full pl-4 pr-4 py-3 rounded-xl bg-slate-50/50 text-slate-800 border border-slate-200/80 focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all text-sm">
                </div>

            </div>

            {{-- TOMBOL AKSI --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('medical-records.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 font-medium border border-slate-200/60 transition-colors text-sm">
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium shadow-sm shadow-blue-600/10 transition-colors text-sm flex items-center gap-2">
                    <i class="bi bi-arrow-clockwise"></i>
                    Perbarui Data
                </button>
            </div>

        </form>

    </div>

</div>

@endsection