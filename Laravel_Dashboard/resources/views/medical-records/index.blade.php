@extends('layouts.dashboard')

@section('content')

<div class="space-y-6">

    {{-- HEADER HALAMAN --}}
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 dark:text-black tracking-tight">
                Rekam Medis
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-0.5">
                Manajemen, Pelacakan, dan Peninjauan Rekam Medis Pasien
            </p>
        </div>

        <a
            href="{{ route('medical-records.create') }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white font-medium shadow-sm shadow-cyan-600/10 transition-colors self-start sm:self-auto text-sm">
            <i class="bi bi-plus-lg"></i>
            Rekam Medis Baru
        </a>
    </div>

    {{-- KARTU RINGKASAN METRIK (KPI) --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">

        {{-- TOTAL REKAM MEDIS --}}
        <div class="kpi-card">
            <div class="kpi-icon bg-slate-100 text-slate-600">
                <i class="bi bi-folder2"></i>
            </div>
            <div>
                <p class="kpi-label">Total Rekam Medis</p>
                <h2 class="kpi-value text-slate-800">
                    {{ number_format($summary['total_records']) }}
                </h2>
            </div>
        </div>

        {{-- MENUNGGU ANTRIAN --}}
        <div class="kpi-card">
            <div class="kpi-icon bg-orange-50 text-orange-600">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <p class="kpi-label">Menunggu</p>
                <h2 class="kpi-value text-orange-600">
                    {{ number_format($summary['pending']) }}
                </h2>
            </div>
        </div>

        {{-- SELESAI DIANALISIS AI --}}
        <div class="kpi-card">
            <div class="kpi-icon bg-cyan-50 text-cyan-600">
                <i class="bi bi-cpu"></i>
            </div>
            <div>
                <p class="kpi-label">Dianalisis AI</p>
                <h2 class="kpi-value text-cyan-600">
                    {{ number_format($summary['analyzed']) }}
                </h2>
            </div>
        </div>

        {{-- BERHASIL DISETUJUI --}}
        <div class="kpi-card">
            <div class="kpi-icon bg-green-50 text-green-600">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <p class="kpi-label">Selesai Klinis</p>
                <h2 class="kpi-value text-green-600">
                    {{ number_format($summary['completed']) }}
                </h2>
            </div>
        </div>

    </div>

    {{-- TABEL DATA REKAM MEDIS --}}
    <div class="dashboard-card p-6">
        
        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 font-semibold uppercase tracking-wider text-[11px]">
                        <th class="py-4 px-4">No. Rekam Medis</th>
                        <th class="py-4 px-2">Nama Pasien</th>
                        <th class="py-4 px-2">Jenis Pemeriksaan</th>
                        <th class="py-4 px-2">Dokter Pemeriksa</th>
                        <th class="py-4 px-2">Tanggal Masuk</th>
                        <th class="py-4 px-2 text-center">Status Analisis</th>
                        <th class="py-4 px-4 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-50 text-slate-700 font-medium">

                    @foreach($records as $record)
                    <tr class="hover:bg-slate-50/50 transition-colors">

                        <td class="py-4 px-4 text-slate-900 font-bold">
                            {{ $record['record_number'] }}
                        </td>

                        <td class="py-4 px-2">
                            {{ $record['patient'] }}
                        </td>

                        <td class="py-4 px-2">
                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs">
                                {{ $record['type'] }}
                            </span>
                        </td>

                        <td class="py-4 px-2 text-slate-500">
                            {{ $record['doctor'] }}
                        </td>

                        <td class="py-4 px-2 text-slate-500">
                            {{ \Carbon\Carbon::parse($record['date'])->format('d M Y') }}
                        </td>

                        <td class="py-4 px-2 text-center">
                            @if($record['status'] == 'Menunggu')
                                <span class="inline-flex items-center gap-1.5 bg-orange-50 text-orange-700 px-3 py-1 rounded-full text-xs font-semibold border border-orange-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                    Menunggu
                                </span>
                            @elseif($record['status'] == 'Dianalisis')
                                <span class="inline-flex items-center gap-1.5 bg-cyan-50 text-cyan-700 px-3 py-1 rounded-full text-xs font-semibold border border-cyan-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-500 animate-pulse"></span>
                                    Dianalisis AI
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-3 py-1 rounded-full text-xs font-semibold border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Selesai
                                </span>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-right">
                            <div class="flex gap-2 justify-end">

                                <a
                                    href="{{ route('medical-records.show', $record['id']) }}"
                                    class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-cyan-50 text-slate-500 hover:text-cyan-600 flex items-center justify-center border border-slate-100 transition-colors"
                                    title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a
                                    href="{{ route('medical-records.edit', $record['id']) }}"
                                    class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-blue-50 text-slate-500 hover:text-blue-600 flex items-center justify-center border border-slate-100 transition-colors"
                                    title="Ubah Data">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('medical-records.destroy', $record['id']) }}"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus rekam medis ini?')"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-red-50 text-slate-500 hover:text-red-600 flex items-center justify-center border border-slate-100 transition-colors"
                                        title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                            </div>
                        </td>

                    </tr>
                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection