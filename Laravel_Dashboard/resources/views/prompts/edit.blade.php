@extends('layouts.dashboard')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight dark:text-black">
                Ubah Konfigurasi Prompt
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-0.5">
                Perbarui meta data beserta logika instruksi arsitektur rekayasa prompt.
            </p>
        </div>

        <a href="{{ route('prompts.index') }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
        <form action="{{ route('prompts.update', $prompt['id']) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Nama Perintah (Prompt Name)
                    </label>
                    <input type="text" name="name" value="{{ $prompt['name'] }}" required
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Kategori
                    </label>
                    <select name="category" required
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                        <option value="Umum" {{ $prompt['category']=='Umum' ? 'selected' : '' }}>Umum (Klinis)</option>
                        <option value="CT-Scan" {{ $prompt['category']=='CT-Scan' ? 'selected' : '' }}>CT-Scan (Citra)</option>
                        <option value="ECG" {{ $prompt['category']=='ECG' ? 'selected' : '' }}>ECG (Citra)</option>
                        <option value="MRI" {{ $prompt['category']=='MRI' ? 'selected' : '' }}>MRI (Citra)</option>
                        <option value="USG" {{ $prompt['category']=='USG' ? 'selected' : '' }}>USG (Citra)</option>
                        <option value="X-Ray" {{ $prompt['category']=='X-Ray' ? 'selected' : '' }}>X-Ray (Citra)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Tingkat Keahlian (Expertise Level)
                    </label>
                    <select name="level" required
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                        <option value="Basic" {{ $prompt['level']=='Basic' ? 'selected' : '' }}>Dasar (Basic)</option>
                        <option value="Advanced" {{ $prompt['level']=='Advanced' ? 'selected' : '' }}>Mahir (Advanced)</option>
                        <option value="Expert" {{ $prompt['level']=='Expert' ? 'selected' : '' }}>Ahli (Expert)</option>
                    </select>
                </div>


                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Status Distribusi
                    </label>
                    <select name="status" required
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                        <option value="Active" {{ $prompt['status']=='Active' ? 'selected' : '' }}>Aktif (Active)</option>
                        <option value="Inactive" {{ $prompt['status']=='Inactive' ? 'selected' : '' }}>Nonaktif (Inactive)</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                    System Prompt
                </label>
                <textarea name="system_prompt" rows="5" required
                    class="w-full text-xs font-mono text-slate-700 bg-slate-50 border border-slate-200 rounded-lg p-3 focus:outline-none focus:border-blue-500 focus:bg-white transition-all leading-relaxed">{{ $prompt['system_prompt'] }}</textarea>
            </div>

            <div class="mt-4">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                    User Prompt
                </label>
                <textarea name="user_prompt" rows="6" required
                    class="w-full text-xs font-mono text-slate-700 bg-slate-50 border border-slate-200 rounded-lg p-3 focus:outline-none focus:border-blue-500 focus:bg-white transition-all leading-relaxed">{{ $prompt['user_prompt'] }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50/80 rounded-xl p-4 border border-slate-100 text-xs">
                <div>
                    <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Waktu Pembuatan</span>
                    <span class="block font-semibold text-slate-700 mt-0.5">{{ $prompt['created_at'] }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-bold uppercase tracking-wider text-[10px]">Pembaruan Terakhir</span>
                    <span class="block font-semibold text-slate-700 mt-0.5">{{ $prompt['updated_at'] }}</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <a href="{{ route('prompts.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 text-xs font-bold text-white shadow-sm hover:bg-blue-700 transition-all transform active:scale-[0.98]">
                    <i class="bi bi-check-lg"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection