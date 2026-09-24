@extends('layouts.dashboard')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight dark:text-black">
            Tambah Templat Prompt
        </h1>
        <p class="text-sm font-medium text-slate-500 mt-0.5">
            Buat konstruksi instruksi LLM baru dengan spesifikasi peran (*system*) dan perintah (*user*).
        </p>
    </div>

    <div class="bg-white rounded-xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
        <form action="{{ route('prompts.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Nama Templat <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" placeholder="Contoh: Pakar Radiologi Ekspertis" required
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Kategori <span class="text-rose-500">*</span>
                    </label>
                    <select name="category" required
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                        <option value="Umum">Umum (Klinis)</option>
                        <option value="CT-Scan">CT-Scan (Citra)</option>
                        <option value="ECG">ECG (Citra)</option>
                        <option value="MRI">MRI (Citra)</option>
                        <option value="USG">USG (Citra)</option>
                        <option value="X-Ray">X-Ray (Citra)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Tingkat Keahlian <span class="text-rose-500">*</span>
                    </label>
                    <select name="level" required
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                        <option value="Basic">Dasar (Basic)</option>
                        <option value="Advanced">Mahir (Advanced)</option>
                        <option value="Expert">Ahli (Expert)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                    System Prompt (Instruksi Peran & Konteks LLM) <span class="text-rose-500">*</span>
                </label>
                <textarea name="system_prompt" rows="5" required placeholder="Anda adalah seorang spesialis..."
                    class="w-full text-xs font-mono text-slate-700 bg-slate-50 border border-slate-200 rounded-lg p-3 focus:outline-none focus:border-blue-500 focus:bg-white transition-all leading-relaxed"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                    User Prompt (Instruksi Tugas / Masukan Dinamis) <span class="text-rose-500">*</span>
                </label>
                <textarea name="user_prompt" rows="5" required placeholder="Analisis rekam medis berikut..."
                    class="w-full text-xs font-mono text-slate-700 bg-slate-50 border border-slate-200 rounded-lg p-3 focus:outline-none focus:border-blue-500 focus:bg-white transition-all leading-relaxed"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <a href="{{ route('prompts.index') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-700 transition-all">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-xs font-bold text-white shadow-sm hover:from-blue-700 hover:to-indigo-700 transition-all transform active:scale-[0.98]">
                    Simpan Templat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection