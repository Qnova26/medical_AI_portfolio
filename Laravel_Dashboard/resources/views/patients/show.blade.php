@extends('layouts.dashboard')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">
                Detail Informasi Pasien
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('patients.index') }}"
                class="px-4 py-2 border rounded-lg text-xs font-bold">
                ← Kembali
            </a>

            <a href="{{ route('patients.edit', $patient->id) }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-bold">
                Edit
            </a>
        </div>
    </div>

    <!-- CARD -->
    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">

        <!-- HEADER CARD -->
        <div class="bg-slate-50 px-6 py-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                @if($patient->jenis_kelamin == 'Male')
                    ♂
                @else
                    ♀
                @endif
            </div>

            <div>
                <h3 class="font-bold text-lg">
                    {{ $patient->nama_pasien }}
                </h3>
                <p class="text-xs text-gray-500">
                    {{ $patient->id_pasien }}
                </p>
            </div>
        </div>

        <!-- DATA -->
        <div class="p-6 grid grid-cols-2 gap-4 text-sm">

            <div>
                <strong>ID Pasien</strong><br>
                {{ $patient->id_pasien }}
            </div>

            <div>
                <strong>Nama</strong><br>
                {{ $patient->nama_pasien }}
            </div>

            <div>
                <strong>Jenis Kelamin</strong><br>
                {{ $patient->jenis_kelamin == 'Male' ? 'Laki-laki' : 'Perempuan' }}
            </div>

            <div>
                <strong>Tanggal Lahir</strong><br>
                {{ \Carbon\Carbon::parse($patient->tanggal_lahir)->format('d M Y') }}
            </div>

            <div>
                <strong>No HP</strong><br>
                {{ $patient->no_tlp }}
            </div>

            <div class="col-span-2">
                <strong>Alamat</strong><br>
                {{ $patient->alamat }}
            </div>

        </div>
    </div>
</div>
@endsection