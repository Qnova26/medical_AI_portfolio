@extends('layouts.dashboard')

@section('content')

<!-- NOTIFIKASI TOAST (ALPINES.JS) -->
@if(session('success'))
<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 4000)"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="-translate-y-5 opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-500"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed top-6 right-6 z-50 bg-emerald-600 text-white px-5 py-3.5 rounded-xl shadow-xl flex items-center gap-3 border border-emerald-500/20">
    <i class="bi bi-check-circle-fill text-lg"></i>
    <span class="text-xs font-bold tracking-wide">{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 4000)"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="-translate-y-5 opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-500"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed top-6 right-6 z-50 bg-rose-600 text-white px-5 py-3.5 rounded-xl shadow-xl flex items-center gap-3 border border-rose-500/20">
    <i class="bi bi-exclamation-circle-fill text-lg"></i>
    <span class="text-xs font-bold tracking-wide">{{ session('error') }}</span>
</div>
@endif

<div class="space-y-6 max-w-7xl mx-auto">

    <!-- BAGIAN HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight dark:text-black">Daftar Pasien</h1>
            <p class="text-sm font-medium text-slate-500 mt-0.5">Registrasi, pemantauan, dan manajemen rekam medis demografis pasien.</p>
        </div>
        <a href="{{ route('patients.create') }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-xs font-bold text-white shadow-md hover:from-blue-700 hover:to-indigo-700 transition-all transform active:scale-[0.98]">
            <i class="bi bi-plus-lg text-sm"></i> Tambah Pasien
        </a>
    </div>

    <!-- KARTU RINGKASAN DATA (KPI STATS) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Pasien -->
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.01)] flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pasien</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($summary['total_patients']) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-slate-50 flex items-center justify-center text-slate-600 text-lg">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>

        <!-- Pasien Laki-laki -->
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.01)] flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Laki-Laki</p>
                <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($summary['male']) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-lg">
                <i class="bi bi-gender-male"></i>
            </div>
        </div>

        <!-- Pasien Perempuan -->
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.01)] flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Perempuan</p>
                <h3 class="text-2xl font-bold text-pink-600 mt-1">{{ number_format($summary['female']) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-pink-50 flex items-center justify-center text-pink-600 text-lg">
                <i class="bi bi-gender-female"></i>
            </div>
        </div>

        <!-- Pasien Risiko Tinggi -->
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.01)] flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Risiko Tinggi</p>
                <h3 class="text-2xl font-bold text-rose-600 mt-1">{{ number_format($summary['high_risk']) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600 text-lg">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
        </div>
    </div>

    <!-- PENCARIAN & FILTER -->
    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.01)]">
        <form action="{{ route('patients.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau ID pasien..."
                    class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg pl-9 pr-4 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
            </div>

            <div>
                <select name="risk_level"
                    class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                    <option value="" {{ request('risk_level') == '' ? 'selected' : '' }}>Semua Tingkat Risiko</option>
                    <option value="Tinggi" {{ request('risk_level') == 'Tinggi' ? 'selected' : '' }}>Tinggi (High)</option>
                    <option value="Sedang" {{ request('risk_level') == 'Sedang' ? 'selected' : '' }}>Sedang (Medium)</option>
                    <option value="Rendah" {{ request('risk_level') == 'Rendah' ? 'selected' : '' }}>Rendah (Low)</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 rounded-lg py-2.5 transition-colors shadow-sm">
                    Terapkan Pencarian
                </button>
                @if(request('search') || request('risk_level'))
                    <a href="{{ route('patients.index') }}"
                        class="inline-flex items-center justify-center px-3 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors"
                        title="Hapus filter">
                        <i class="bi bi-x-lg text-xs"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABEL DATA PASIEN -->
    <div class="bg-white rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-5 text-left">ID Pasien</th>
                        <th class="py-3 px-4 text-left">Nama Lengkap</th>
                        <th class="py-3 px-4 text-left">Usia</th>
                        <th class="py-3 px-4 text-left">Jenis Kelamin</th>
                        <th class="py-3 px-4 text-left">Diagnosis Utama</th>
                        <th class="py-3 px-4 text-center">Status Risiko</th>
                        <th class="py-3 px-5 text-center">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                @forelse($patients as $patient)
                <tr class="hover:bg-slate-50/50 transition-colors">

                    <!-- ID Pasien -->
                    <td class="py-3.5 px-5 font-mono font-bold text-slate-500">
                        {{ $patient->id_pasien }}
                    </td>

                    <!-- Nama -->
                    <td class="py-3.5 px-4 font-semibold text-slate-800">
                        @if($patient->nama_pasien)
                            {{ $patient->nama_pasien }}
                        @else
                            <span class="text-slate-400 italic">Belum diisi</span>
                        @endif
                    </td>

                    <!-- Usia -->
                    <td class="py-3.5 px-4 font-medium text-slate-600">
                        @if($patient->tanggal_lahir)
                            {{ \Carbon\Carbon::parse($patient->tanggal_lahir)->age }} Tahun
                        @else
                            -
                        @endif
                    </td>

                    <!-- Jenis Kelamin -->
                    <td class="py-3.5 px-4">
                        <span class="inline-flex items-center gap-1 font-medium text-slate-700">
                            @if($patient->jenis_kelamin == 'Male')
                                <i class="bi bi-gender-male text-blue-500"></i> Laki-laki
                            @elseif($patient->jenis_kelamin == 'Female')
                                <i class="bi bi-gender-female text-pink-500"></i> Perempuan
                            @else
                                <span class="text-slate-400 italic">-</span>
                            @endif
                        </span>
                    </td>

                    <!-- Diagnosis -->
                    <td class="py-3.5 px-4 text-left">
                        @if($patient->diagnosis)
                            <span class="text-slate-700 dark:text-slate-200 text-[11px] font-semibold">
                                {{ $patient->diagnosis }}
                            </span>
                        @else
                            <span class="text-slate-300 italic text-[11px]">Belum dianalisis</span>
                        @endif
                    </td>

                    <!-- Risk Level -->
                    <td class="py-3.5 px-4 text-center">
                        @if($patient->risk_level === 'Tinggi')
                            <span class="px-2 py-0.5 bg-rose-100 text-rose-700 rounded-full text-[10px] font-bold">Tinggi</span>
                        @elseif($patient->risk_level === 'Sedang')
                            <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold">Sedang</span>
                        @elseif($patient->risk_level === 'Rendah')
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold">Rendah</span>
                        @else
                            <span class="text-slate-300 italic text-[11px]">—</span>
                        @endif
                    </td>

                    <!-- Tombol Aksi -->
                    <td class="py-3.5 px-5">
                        <div class="flex items-center justify-center gap-1.5">

                            <!-- Lihat -->
                            <a href="{{ route('patients.show', $patient->id) }}"
                                class="p-1.5 rounded-md border border-slate-200 bg-white text-slate-600 hover:text-cyan-600 hover:border-cyan-200 hover:bg-cyan-50/30 transition-all shadow-sm">
                                <i class="bi bi-eye text-sm"></i>
                            </a>

                            <!-- Edit -->
                            <a href="{{ route('patients.edit', $patient->id) }}"
                                class="p-1.5 rounded-md border border-slate-200 bg-white text-slate-600 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50/30 transition-all shadow-sm">
                                <i class="bi bi-pencil text-sm"></i>
                            </a>

                            <!-- Hapus -->
                            <form action="{{ route('patients.destroy', $patient->id) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pasien ini dari sistem?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-1.5 rounded-md border border-slate-200 bg-white text-slate-600 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50/30 transition-all shadow-sm">
                                    <i class="bi bi-trash text-sm"></i>
                                </button>
                            </form>

                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-10 text-center text-slate-400 text-xs">
                        <i class="bi bi-search text-2xl block mb-2"></i>
                        Tidak ada pasien yang cocok dengan kata kunci atau filter yang dipilih.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection