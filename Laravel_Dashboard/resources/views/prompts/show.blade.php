@extends('layouts.dashboard')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight dark:text-black">
                    {{ $prompt['name'] }}
                </h1>
                @if($prompt['status'] == 'Active')
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-emerald-50 text-emerald-700 border border-emerald-100">Aktif</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-slate-50 text-slate-500 border border-slate-100">Nonaktif</span>
                @endif
            </div>
            <p class="text-sm font-medium text-slate-500 mt-0.5">
                Rincian struktur parameter arsitektur instruksi model bahasa besar.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('prompts.index') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('prompts.edit', $prompt['id']) }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-blue-600 text-xs font-bold text-white shadow-sm hover:bg-blue-700 transition-all">
                <i class="bi bi-pencil-square"></i> Ubah Struktur
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] p-5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-xs">
            <div>
                <span class="block font-bold text-slate-400 uppercase tracking-wider text-[10px]">Kategori Klinis</span>
                <span class="block font-semibold text-slate-700 mt-1 bg-slate-50 border border-slate-100 rounded px-2 py-1 inline-block">
                    @if($prompt['category'] == 'Radiology') Radiologi
                    @elseif($prompt['category'] == 'Cardiology') Kardiologi
                    @elseif($prompt['category'] == 'Neurology') Neurologi
                    @else {{ $prompt['category'] }} @endif
                </span>
            </div>
            <div>
                <span class="block font-bold text-slate-400 uppercase tracking-wider text-[10px]">Tingkat Keahlian</span>
                <span class="block font-medium text-slate-800 mt-1.5">
                    @if($prompt['level'] == 'Expert') Ahli (Expert)
                    @elseif($prompt['level'] == 'Advanced') Mahir (Advanced)
                    @else Dasar (Basic) @endif
                </span>
            </div>
            <div>
                <span class="block font-bold text-slate-400 uppercase tracking-wider text-[10px]">Kode Identifikasi</span>
                <span class="block font-mono font-bold text-slate-400 mt-1.5">#PRM-{{ str_pad($prompt['id'], 3, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>
    </div>

    <div class="space-y-8 pt-2">

        {{-- SYSTEM PROMPT --}}
        <div class="relative mt-4">
            <div class="absolute -top-3.5 left-5 z-10 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-t-lg bg-emerald-600 text-white text-[10px] font-bold uppercase tracking-wider shadow-sm">
                <i class="bi bi-cpu"></i> System Prompt
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.04)] overflow-hidden">
                <div class="border-l-[3px] border-emerald-400">
                    <div class="flex items-center justify-between px-5 pt-5 pb-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700">Konteks Peran</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Identitas dan batasan tetap yang dipegang model AI.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wide bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full px-2.5 py-1">
                                Immutable
                            </span>
                            <button onclick="copyPromptText('system-prompt-content', this)"
                                title="Salin teks"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg border border-slate-200 text-slate-400 hover:text-emerald-600 hover:border-emerald-200 hover:bg-emerald-50 transition-all">
                                <i class="bi bi-clipboard text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="px-5 pb-4">
                        <div id="system-prompt-content"
                            class="bg-slate-50 border border-slate-100 rounded-xl p-4 font-mono text-[13px] text-slate-700 whitespace-pre-wrap leading-relaxed overflow-x-auto selection:bg-emerald-100">
                            {{ $prompt['system_prompt'] }}
                        </div>
                    </div>

                    <div class="px-5 py-2.5 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between text-[10px] font-mono text-slate-400">
                        <span>{{ strlen($prompt['system_prompt']) }} karakter &bull; {{ str_word_count($prompt['system_prompt']) }} kata</span>
                        <span class="text-emerald-500"><i class="bi bi-lock-fill"></i> Terkunci dari pengubahan dinamis</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- USER PROMPT --}}
        <div class="relative mt-6">
            <div class="absolute -top-3.5 left-5 z-10 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-t-lg bg-blue-600 text-white text-[10px] font-bold uppercase tracking-wider shadow-sm">
                <i class="bi bi-terminal"></i> User Prompt
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.04)] overflow-hidden">
                <div class="border-l-[3px] border-blue-400">
                    <div class="flex items-center justify-between px-5 pt-5 pb-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700">Struktur Argumen</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Template instruksi yang diisi data pasien saat analisis dijalankan.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wide bg-blue-50 text-blue-700 border border-blue-100 rounded-full px-2.5 py-1">
                                Dynamic Inputs
                            </span>
                            <button onclick="copyPromptText('user-prompt-content', this)"
                                title="Salin teks"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all">
                                <i class="bi bi-clipboard text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="px-5 pb-4">
                        <div id="user-prompt-content"
                            class="bg-slate-50 border border-slate-100 rounded-xl p-4 font-mono text-[13px] text-slate-700 whitespace-pre-wrap leading-relaxed overflow-x-auto selection:bg-blue-100">
                            {{ $prompt['user_prompt'] }}
                        </div>
                    </div>

                    <div class="px-5 py-2.5 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between text-[10px] font-mono text-slate-400">
                        <span>{{ strlen($prompt['user_prompt']) }} karakter &bull; {{ str_word_count($prompt['user_prompt']) }} kata</span>
                        <span class="text-blue-500"><i class="bi bi-arrow-repeat"></i> Diisi ulang setiap analisis</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    function copyPromptText(elementId, btn) {
        const el = document.getElementById(elementId);
        const text = el.innerText.trim();

        navigator.clipboard.writeText(text).then(() => {
            const icon = btn.querySelector('i');
            const originalClass = icon.className;

            icon.className = 'bi bi-check-lg';
            btn.classList.add('text-emerald-600', 'border-emerald-200', 'bg-emerald-50');

            setTimeout(() => {
                icon.className = originalClass;
                btn.classList.remove('text-emerald-600', 'border-emerald-200', 'bg-emerald-50');
            }, 1500);
        }).catch(() => {
            alert('Gagal menyalin teks ke clipboard.');
        });
    }
</script>
@endsection