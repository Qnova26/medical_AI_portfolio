@extends('layouts.dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight">Hasil Analisis Citra AI</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Pasien: <span class="font-semibold">{{ $analysis->patient->nama_pasien }}</span> &bull;
                Jenis: <span class="font-semibold">{{ $analysis->image_type }}</span>
                @if($analysis->body_part)
                    &bull; <span class="font-semibold">{{ $analysis->body_part }}</span>
                @endif
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('analysis-history.index') }}"
               class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors flex items-center gap-1.5 shadow-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('analysis-picture.pdf', $analysis->id) }}"
               class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors flex items-center gap-1.5 shadow-sm"
               title="Download PDF">
                <i class="bi bi-file-earmark-pdf text-rose-500"></i> Download PDF
            </a>
            <a href="{{ route('analysis-picture.create') }}"
               class="px-4 py-2 bg-indigo-600 rounded-xl text-xs font-bold text-white hover:bg-indigo-700 transition-colors flex items-center gap-1.5 shadow-sm">
                <i class="bi bi-plus-lg"></i> Analisis Baru
            </a>
        </div>
    </div>

    {{-- ── Banner status gabungan / korelasi klinis ────────────────── --}}
    @if($combined)
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 flex items-start gap-3">
            <i class="bi bi-link-45deg text-indigo-500 text-lg mt-0.5"></i>
            <div class="text-xs text-indigo-800 leading-relaxed">
                <p class="font-bold uppercase tracking-wide mb-1">Sudah Digabung dengan Analisis Klinis</p>
                <p>Hasil analisis ini sudah digabung dengan analisis klinis menjadi satu diagnosis final. Validasi (setujui/tolak) dilakukan di halaman Diagnosis Gabungan, bukan di sini.</p>
                <a href="{{ route('analysis-combined.result', $combined->id) }}"
                   class="inline-flex items-center gap-1 mt-2 font-bold text-indigo-600 hover:underline">
                    Buka Diagnosis Gabungan <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    @else
        @if($analysis->needs_clinical_correlation)
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
                <i class="bi bi-exclamation-triangle-fill text-amber-500 text-lg mt-0.5"></i>
                <div class="text-xs text-amber-800 leading-relaxed">
                    <p class="font-bold uppercase tracking-wide mb-1">Temuan Ini Bersifat Awal</p>
                    <p>AI menilai diperlukan data klinis tambahan untuk memastikan diagnosis: <span class="font-medium">{{ $analysis->clinical_correlation_reason }}</span></p>
                </div>
            </div>
        @endif

        <div>
            <a href="{{ route('analysis.create', ['patient_id' => $analysis->patient_id]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-xs font-bold text-white transition-colors shadow-sm">
                <i class="bi bi-heart-pulse"></i> Lanjutkan ke Analisis Klinis
            </a>
        </div>
    @endif

    {{-- Gambar Berdampingan --}}
    <div class="grid grid-cols-2 gap-4">

        {{-- Citra Asli --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-4 py-2.5 border-b border-slate-100 flex items-center gap-2 shrink-0">
                <i class="bi bi-image text-slate-400 text-sm"></i>
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Citra Asli</h3>
            </div>
            <div class="bg-slate-950 flex-1 flex items-center justify-center" style="min-height: 340px;">
                @forelse($analysis->image_files as $path)
                    <img src="{{ asset('storage/' . $path) }}"
                         class="w-full h-full object-contain"
                         style="max-height: 420px;"
                         alt="Citra Medis"
                         onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden')">
                    <div class="hidden text-slate-500 text-xs text-center p-8">
                        <i class="bi bi-image text-4xl block mb-2 opacity-30"></i>
                        Gambar tidak dapat ditampilkan
                    </div>
                @empty
                    <div class="text-slate-500 text-xs text-center p-8">
                        <i class="bi bi-image text-4xl block mb-2 opacity-30"></i>
                        Tidak ada citra tersimpan
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Hasil Anotasi AI --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="px-4 py-2.5 border-b border-slate-100 flex items-center gap-2 shrink-0">
                <i class="bi bi-bounding-box-circles text-indigo-500 text-sm"></i>
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Hasil Anotasi AI</h3>
            </div>
            <div class="bg-slate-950 flex-1 flex items-center justify-center" style="min-height: 340px;">
                @if(!empty($analysis->result_images))
                    @foreach($analysis->result_images as $path)
                        <img src="{{ asset('storage/' . $path) }}"
                             class="w-full h-full object-contain"
                             style="max-height: 420px;"
                             alt="Hasil Anotasi AI"
                             onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden')">
                        <div class="hidden text-slate-500 text-xs text-center p-8">
                            <i class="bi bi-image text-4xl block mb-2 opacity-30"></i>
                            Gambar tidak dapat ditampilkan
                        </div>
                    @endforeach
                @else
                    <div class="text-slate-500 text-xs text-center p-8">
                        <i class="bi bi-bounding-box text-4xl block mb-2 opacity-30"></i>
                        Belum ada citra hasil anotasi
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Diagnosis AI --}}
    @php
        $riskBg   = match($result['risk_level'] ?? '') {
            'Tinggi' => 'bg-rose-500',
            'Sedang' => 'bg-amber-500',
            default  => 'bg-emerald-500',
        };
        $riskText = match($result['risk_level'] ?? '') {
            'Tinggi' => 'text-rose-600 bg-rose-50 border-rose-200',
            'Sedang' => 'text-amber-600 bg-amber-50 border-amber-200',
            default  => 'text-emerald-600 bg-emerald-50 border-emerald-200',
        };
        $confRaw = str_replace('%', '', $result['confidence'] ?? 0);
        $confFloat = floatval($confRaw);
        $conf = $confFloat > 0 && $confFloat <= 1.0 ? intval($confFloat * 100) : intval($confFloat);
        $confColor = $conf >= 75 ? '#10b981' : ($conf >= 50 ? '#f59e0b' : '#ef4444');
    @endphp

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Header diagnosis --}}
        <div class="px-6 py-5 flex flex-wrap items-start justify-between gap-4 border-b border-slate-100">
            <div class="flex items-start gap-4">
                {{-- Icon pulse --}}
                <div class="w-10 h-10 rounded-xl {{ $riskBg }} flex items-center justify-center shrink-0 mt-0.5">
                    <i class="bi bi-activity text-white text-base"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-1">Diagnosis AI</p>
                    <h2 class="text-xl font-bold text-slate-800">{{ $result['diagnosis'] ?? '-' }}</h2>
                </div>
            </div>

            {{-- Stats --}}
            <div class="flex items-center gap-3 flex-wrap">
                {{-- Risk badge --}}
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-xs font-bold {{ $riskText }}">
                    <span class="w-2 h-2 rounded-full {{ $riskBg }}"></span>
                    Risiko {{ $result['risk_level'] ?? '-' }}
                </span>

                {{-- Template Level badge --}}
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-indigo-200 bg-indigo-50 text-indigo-600 text-xs font-bold">
                    <i class="bi bi-cpu"></i>
                    Prompt {{ ucfirst($analysis->template_level ?? 'Basic') }}
                </span>

                {{-- Confidence donut-style --}}
                <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 rounded-xl border border-slate-100">
                    <svg width="32" height="32" viewBox="0 0 32 32">
                        <circle cx="16" cy="16" r="12" fill="none" stroke="#e2e8f0" stroke-width="4"/>
                        <circle cx="16" cy="16" r="12" fill="none" stroke="{{ $confColor }}" stroke-width="4"
                            stroke-dasharray="{{ round($conf * 75.4 / 100, 1) }} 75.4"
                            stroke-linecap="round"
                            transform="rotate(-90 16 16)"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-slate-700 leading-none">{{ $conf }}%</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">confidence</p>
                    </div>
                </div>

                {{-- Processing time --}}
                <div class="px-3 py-2 bg-slate-50 rounded-xl border border-slate-100 text-center">
                    <p class="text-sm font-bold text-slate-700 leading-none">{{ $result['processing_time'] ?? '-' }}s</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">waktu proses</p>
                </div>
            </div>
        </div>

        {{-- 4 kartu konten --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-slate-50/50 border-t border-slate-100">

            {{-- Ringkasan Klinis --}}
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-6 h-6 rounded-md bg-blue-100 flex items-center justify-center">
                        <i class="bi bi-file-medical text-blue-600 text-xs"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-600">Ringkasan Klinis</p>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    {{ $result['summary'] ?? 'Tidak ada ringkasan tersedia.' }}
                </p>
            </div>

            {{-- Diagnosis Banding --}}
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-6 h-6 rounded-md bg-violet-100 flex items-center justify-center">
                        <i class="bi bi-list-ul text-violet-600 text-xs"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-600">Diagnosis Banding</p>
                </div>
                <div class="text-xs text-slate-500 leading-relaxed">
                    @if(!empty($result['differential']))
                        @if(is_array($result['differential']))
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($result['differential'] as $item)
                                    <span class="px-2 py-1 bg-violet-50 text-violet-700 rounded-md border border-violet-100 text-[11px] font-medium">{{ $item }}</span>
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-wrap gap-1.5">
                                @php
                                    $diffArray = is_array($result['differential']) ? $result['differential'] : explode(',', $result['differential']);
                                @endphp
                                @foreach($diffArray as $item)
                                    <span class="px-2 py-1 bg-violet-50 text-violet-700 rounded-md border border-violet-100 text-[11px] font-medium">{{ trim($item) }}</span>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <span class="text-slate-400">Tidak ada diagnosis banding tersedia.</span>
                    @endif
                </div>
            </div>

            {{-- Rekomendasi Tindakan --}}
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm border-l-4 border-l-emerald-500">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-6 h-6 rounded-md bg-emerald-100 flex items-center justify-center">
                        <i class="bi bi-clipboard-check text-emerald-600 text-xs"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-600">Rekomendasi Tindakan</p>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    @if(isset($result['recommendation']))
                        @if(is_array($result['recommendation']))
                            <ul class="list-disc pl-4 space-y-1">
                                @foreach($result['recommendation'] as $rec)
                                    <li>{{ $rec }}</li>
                                @endforeach
                            </ul>
                        @else
                            {{ $result['recommendation'] }}
                        @endif
                    @else
                        Konsultasikan hasil ini dengan dokter spesialis terkait.
                    @endif
                </p>
            </div>

            {{-- Rekomendasi Obat --}}
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm border-l-4 border-l-indigo-500">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-6 h-6 rounded-md bg-indigo-100 flex items-center justify-center">
                        <i class="bi bi-capsule text-indigo-600 text-xs"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-600">Rekomendasi Obat <span class="font-normal text-slate-400">(AI)</span></p>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    @if(isset($result['medication']))
                        @if(is_array($result['medication']))
                            <ul class="list-disc pl-4 space-y-1">
                                @foreach($result['medication'] as $med)
                                    <li>{{ $med }}</li>
                                @endforeach
                            </ul>
                        @else
                            {{ $result['medication'] }}
                        @endif
                    @else
                        Tidak ada rekomendasi obat spesifik.
                    @endif
                </p>
            </div>

            {{-- Alasan Tingkat Keyakinan --}}
            @if(isset($result['confidence_reason']))
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm border-l-4 border-l-blue-500">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-6 h-6 rounded-md bg-blue-100 flex items-center justify-center">
                        <i class="bi bi-info-circle-fill text-blue-600 text-xs"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-600">Alasan Keyakinan AI</p>
                </div>
                <div class="text-xs text-slate-500 leading-relaxed font-medium">
                    @if(is_array($result['confidence_reason']))
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach($result['confidence_reason'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        {{ $result['confidence_reason'] }}
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- ── Validasi Dokter ─────────────────────────────────────── --}}
        @unless($combined)
        <div class="px-5 py-4 bg-slate-50 border-t border-slate-100">
            <div class="flex items-center gap-2 mb-3">
                <i class="bi bi-patch-check-fill text-indigo-500 text-sm"></i>
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Validasi Dokter</h3>
            </div>

            @if($analysis->validation_status === 'confirmed')
                <div class="flex items-center gap-2 text-emerald-700 text-xs font-bold">
                    <i class="bi bi-check-circle-fill"></i> Diagnosis telah disetujui dokter.
                </div>
                @if($analysis->doctor_analysis)
                    <p class="text-xs text-slate-500 mt-2">Catatan: {{ $analysis->doctor_analysis }}</p>
                @endif
            @elseif($analysis->validation_status === 'corrected')
                <div class="flex items-center gap-2 text-rose-700 text-xs font-bold mb-2">
                    <i class="bi bi-x-circle-fill"></i> Diagnosis AI dikoreksi oleh dokter.
                </div>
                <div class="text-xs text-slate-600 space-y-1">
                    <p><span class="font-bold">Diagnosis Dokter:</span> {{ $analysis->doctor_diagnosis }}</p>
                    @if($analysis->doctor_risk_level)
                        <p><span class="font-bold">Risiko Versi Dokter:</span> {{ $analysis->doctor_risk_level }}</p>
                    @endif
                    @if($analysis->medication_recommendation)
                        <p><span class="font-bold">Obat:</span> {{ $analysis->medication_recommendation }}</p>
                    @endif
                    <p><span class="font-bold">Catatan Koreksi:</span> {{ $analysis->doctor_analysis }}</p>
                </div>
            @else
                <div class="flex gap-2 mb-3">
                    <button type="button" onclick="toggleForm('approve')"
                        class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 text-xs transition-colors">
                        <i class="bi bi-check2-circle"></i> Setujui
                    </button>
                    <button type="button" onclick="toggleForm('reject')"
                        class="flex items-center gap-2 px-4 py-2 bg-rose-600 text-white font-bold rounded-lg hover:bg-rose-700 text-xs transition-colors">
                        <i class="bi bi-x-circle"></i> Tolak
                    </button>
                </div>

                <form id="approve-form" class="hidden space-y-2 bg-white p-3 rounded-lg border border-slate-200" method="POST" action="{{ route('analysis-picture.confirm', $analysis->id) }}">
                @csrf @method('PATCH')
                <div>
                    <label class="text-[11px] font-bold text-slate-500 uppercase">Tingkat Risiko Final (Dokter) *</label>
                    <select name="doctor_risk_level" required
                        class="w-full text-xs border border-slate-200 rounded-lg p-2 mt-1 focus:outline-none focus:border-emerald-500">
                        <option value="">-- Pilih Tingkat Risiko --</option>
                        <option value="Rendah">Rendah</option>
                        <option value="Sedang">Sedang</option>
                        <option value="Tinggi">Tinggi</option>
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-500 uppercase">Rekomendasi Obat (opsional)</label>
                    <input type="text" name="medication_recommendation"
                        class="w-full text-xs border border-slate-200 rounded-lg p-2 mt-1">
                </div>
                <textarea name="doctor_notes_validation" rows="2" placeholder="Catatan dokter (opsional)"
                    class="w-full text-xs border border-slate-200 rounded-lg p-2 focus:outline-none focus:border-emerald-500"></textarea>
                <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700">
                    Kirim Persetujuan
                </button>
            </form>

                <form id="reject-form" class="hidden space-y-2 bg-white p-3 rounded-lg border border-slate-200" method="POST" action="{{ route('analysis-picture.reject', $analysis->id) }}">
                    @csrf @method('PATCH')
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 uppercase">Diagnosis Versi Dokter *</label>
                        <input type="text" name="doctor_diagnosis" required
                            class="w-full text-xs border border-slate-200 rounded-lg p-2 mt-1 focus:outline-none focus:border-rose-500">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 uppercase">Risiko Versi Dokter *</label>
                        <select name="doctor_risk_level" required class="w-full text-xs border border-slate-200 rounded-lg p-2 mt-1">
                            <option value="">-- Pilih Tingkat Risiko --</option>
                            <option value="Rendah">Rendah</option>
                            <option value="Sedang">Sedang</option>
                            <option value="Tinggi">Tinggi</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 uppercase">Rekomendasi Obat (opsional)</label>
                        <input type="text" name="medication_recommendation"
                            class="w-full text-xs border border-slate-200 rounded-lg p-2 mt-1">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 uppercase">Catatan Koreksi *</label>
                        <textarea name="doctor_notes_validation" rows="2" required
                            class="w-full text-xs border border-slate-200 rounded-lg p-2 mt-1 focus:outline-none focus:border-rose-500"></textarea>
                    </div>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-rose-600 text-white text-xs font-bold hover:bg-rose-700">
                        Kirim Penolakan
                    </button>
                </form>
            @endif
        </div>
        @endunless

        {{-- Footer info --}}
        <div class="px-5 py-3 bg-slate-50 border-t border-slate-100">
            <p class="text-[10px] text-slate-400">
                <i class="bi bi-info-circle mr-1"></i>
                Hasil ini adalah rekomendasi AI, bukan pengganti diagnosis dokter.
            </p>
        </div>
    </div>

    {{-- Chat box AI --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="bi bi-chat-dots-fill text-blue-500 text-sm"></i>
                <h3 class="font-bold text-xs text-slate-500 uppercase tracking-wider">Tanya AI</h3>
            </div>
            <button id="clear-chat" title="Hapus riwayat chat"
                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs text-rose-500 hover:bg-rose-50 transition-colors">
                <i class="bi bi-trash3"></i>
                <span class="font-medium">Hapus</span>
            </button>
        </div>

        <div id="chat-box" class="space-y-2 max-h-56 overflow-y-auto p-4 text-xs"></div>

        <div class="px-4 pb-4 flex gap-2">
            <input type="text" id="chat-input" placeholder="Tanya seputar hasil analisis citra ini..."
                class="flex-1 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-50">
            <button id="chat-submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded-xl hover:bg-blue-700 transition-colors">
                <i class="bi bi-send"></i>
            </button>
        </div>
    </div>

</div>

<script>
function toggleForm(which) {
    document.getElementById('approve-form').classList.toggle('hidden', which !== 'approve');
    document.getElementById('reject-form').classList.toggle('hidden', which !== 'reject');
}

const chatType = "image";
const chatId   = {{ $analysis->id }};
const chatBox  = document.getElementById('chat-box');

function markdownToHtml(text) {
    return text
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.+?)\*/g, '<em>$1</em>')
        .replace(/\n\n/g, '</p><p class="mt-2">')
        .replace(/\n/g, '<br>');
}

function appendBubble(role, text) {
    const align   = role === 'user' ? 'justify-end' : 'justify-start';
    const style   = role === 'user' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700';
    const content = role === 'assistant' ? `<p>${markdownToHtml(text)}</p>` : text;
    chatBox.insertAdjacentHTML('beforeend', `
        <div class="flex ${align}">
            <div class="${style} px-3 py-2 rounded-xl max-w-[80%] leading-relaxed">${content}</div>
        </div>`);
    chatBox.scrollTop = chatBox.scrollHeight;
}

async function loadHistory() {
    try {
        const res  = await fetch(`/chat/${chatType}/${chatId}/history`);
        const data = await res.json();
        data.forEach(h => appendBubble(h.role, h.message));
    } catch (err) {
        console.error('Gagal load history:', err);
    }
}

document.getElementById('clear-chat').addEventListener('click', async function () {
    if (!confirm('Hapus semua riwayat chat ini?')) return;
    try {
        const res = await fetch(`/chat/${chatType}/${chatId}/clear`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        if (res.ok) chatBox.innerHTML = '';
        else alert('Gagal menghapus riwayat chat.');
    } catch (err) {
        alert('Koneksi gagal: ' + err.message);
    }
});

async function sendChat() {
    const input    = document.getElementById('chat-input');
    const question = input.value.trim();
    if (!question) return;

    appendBubble('user', question);
    input.value = '';
    appendBubble('assistant', '<i class="bi bi-three-dots"></i> mengetik...');

    try {
        const res = await fetch(`/chat/${chatType}/${chatId}/ask`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ question })
        });

        chatBox.lastElementChild.remove();

        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            appendBubble('assistant', '❌ Error: ' + (err.error || 'Gagal menghubungi AI.'));
            return;
        }

        const data = await res.json();
        appendBubble('assistant', data.answer || 'Gagal mendapat jawaban.');
    } catch (err) {
        chatBox.lastElementChild.remove();
        appendBubble('assistant', '❌ Koneksi gagal: ' + err.message);
    }
}

document.getElementById('chat-submit').addEventListener('click', sendChat);
document.getElementById('chat-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') sendChat();
});

loadHistory();
</script>
@endsection