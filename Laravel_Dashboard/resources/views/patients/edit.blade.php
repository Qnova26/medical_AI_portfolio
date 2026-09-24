@extends('layouts.dashboard')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">

    <!-- BAGIAN HEADER -->
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight dark:text-black">
            Ubah Data Pasien
        </h1>
        <p class="text-sm font-medium text-slate-500 mt-0.5">
            Perbarui data demografis dan informasi kontak rekam medis pasien.
        </p>
    </div>

    <!-- KARTU FORM EDIT -->
    <div class="bg-white rounded-xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
        <form action="{{ route('patients.update', $patient['id']) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- 1. ID Pasien -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        ID Pasien <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="id_pasien" value="{{ $patient['id_pasien'] }}" required
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                </div>

                <!-- 2. Nama Lengkap -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Nama Lengkap Pasien <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_pasien" value="{{ $patient['nama_pasien'] }}" required
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                </div>

                <!-- 3. Jenis Kelamin -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <select name="jenis_kelamin" required
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                        <option value="Male" {{ $patient['jenis_kelamin'] == 'Male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Female" {{ $patient['jenis_kelamin'] == 'Female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- 4. Tanggal Lahir -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Tanggal Lahir <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_lahir" value="{{ $patient['tanggal_lahir'] }}" required
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                </div>
            </div>

            <!-- 5. Nomor Telepon -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                    Nomor Telepon / HP <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="no_tlp" value="{{ $patient['no_tlp'] }}" required
                    class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
            </div>

            <!-- 6. Alamat -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                    Alamat Tempat Tinggal <span class="text-rose-500">*</span>
                </label>
                <textarea rows="3" name="alamat" required
                    class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg p-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">{{ $patient['alamat'] }}</textarea>
            </div>

            <!-- TOMBOL AKSI -->
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <!-- Tombol Batal -->
                <a href="{{ route('patients.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-700 transition-all">
                    Batal
                </a>
                
                <!-- Tombol Perbarui -->
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-xs font-bold text-white shadow-sm hover:from-blue-700 hover:to-indigo-700 transition-all transform active:scale-[0.98]">
                    <i class="bi bi-check-lg"></i> Perbarui Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection