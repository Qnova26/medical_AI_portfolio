@extends('layouts.dashboard')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Hasil Analisis AI</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Pasien: <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $analysis->patient->nama_pasien }}</span> &bull;
                Template: <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $analysis->prompt->name }} ({{ ucfirst($analysis->prompt->level) }})</span>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('analysis-history.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 dark:text-slate-200 transition-colors shadow-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('analysis.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-xs font-bold text-white transition-colors shadow-sm">
                <i class="bi bi-plus-lg"></i> Analisis Baru
            </a>
        </div>
    </div>

    {{-- ── Banner status gabungan / korelasi citra ────────────────── --}}
    @if($combined)
        <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-4 flex items-start gap-3">
            <i class="bi bi-link-45deg text-indigo-500 text-lg mt-0.5"></i>
            <div class="text-xs text-indigo-800 dark:text-indigo-200 leading-relaxed">
                <p class="font-bold uppercase tracking-wide mb-1">Sudah Digabung dengan Analisis Citra</p>
                <p>Hasil analisis ini sudah digabung dengan analisis citra menjadi satu diagnosis final. Validasi (setujui/tolak) dilakukan di halaman Diagnosis Gabungan, bukan di sini.</p>
                <a href="{{ route('analysis-combined.result', $combined->id) }}"
                   class="inline-flex items-center gap-1 mt-2 font-bold text-indigo-600 dark:text-indigo-300 hover:underline">
                    Buka Diagnosis Gabungan <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    @else
        @if($analysis->needs_imaging_correlation)
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 flex items-start gap-3">
                <i class="bi bi-exclamation-triangle-fill text-amber-500 text-lg mt-0.5"></i>
                <div class="text-xs text-amber-800 dark:text-amber-200 leading-relaxed">
                    <p class="font-bold uppercase tracking-wide mb-1">Temuan Ini Bersifat Awal</p>
                    <p>AI menilai diperlukan data citra tambahan untuk memastikan diagnosis: <span class="font-medium">{{ $analysis->imaging_correlation_reason }}</span></p>
                </div>
            </div>
        @endif

        <div>
            <a href="{{ route('analysis-picture.create', ['patient_id' => $analysis->patient_id]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-xs font-bold text-white transition-colors shadow-sm">
                <i class="bi bi-images"></i> Lanjutkan ke Analisis Citra
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kiri: hasil utama --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Diagnosis + Risk Level --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-100 dark:border-slate-700 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
                <div class="flex items-center gap-2 mb-3 border-b border-slate-50 dark:border-slate-700/50 pb-2">
                    <i class="bi bi-heart-pulse-fill text-rose-500 text-sm"></i>
                    <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider">Diagnosis Utama</h3>
                </div>

                <div class="flex items-center justify-between flex-wrap gap-3">
                    <p class="text-lg font-bold text-slate-800 dark:text-white">{{ $result['diagnosis'] }}</p>

                    @php
                        $riskColor = match($result['risk_level']) {
                            'Tinggi' => 'bg-rose-100 text-rose-700 border-rose-200 dark:bg-rose-900/30 dark:text-rose-400 dark:border-rose-800',
                            'Sedang' => 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800',
                            default  => 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800',
                        };
                        $riskIcon = match($result['risk_level']) {
                            'Tinggi' => 'bi-exclamation-triangle-fill',
                            'Sedang' => 'bi-dash-circle-fill',
                            default  => 'bi-check-circle-fill',
                        };
                    @endphp
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $riskColor }}">
                            <i class="bi {{ $riskIcon }}"></i>
                            Risiko {{ $result['risk_level'] }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border border-indigo-200 bg-indigo-50 text-indigo-600">
                            <i class="bi bi-cpu"></i>
                            Prompt {{ ucfirst($analysis->template_level ?? 'Basic') }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2 mb-2 mt-5">
                    <i class="bi bi-file-earmark-medical text-blue-500 text-sm"></i>
                    <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider">Ringkasan Klinis</h3>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed bg-slate-50/50 dark:bg-slate-800/50 rounded-lg p-3 border border-slate-100 dark:border-slate-700/50">
                    {{ $result['summary'] }}
                </p>
            </div>

            {{-- Diagnosis Banding --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-100 dark:border-slate-700 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
                <div class="flex items-center gap-2 mb-3 border-b border-slate-50 dark:border-slate-700/50 pb-2">
                    <i class="bi bi-shuffle text-amber-500 text-sm"></i>
                    <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider">Diagnosis Banding (Differential Diagnosis)</h3>
                </div>
                <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    @if(isset($result['differential']))
                        @if(is_array($result['differential']))
                            <ul class="list-disc pl-4 space-y-1">
                                @foreach($result['differential'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            {{ $result['differential'] }}
                        @endif
                    @else
                        Tidak ada diagnosis banding tersedia.
                    @endif
                </div>
            </div>

            {{-- Rekomendasi Tindakan --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-100 dark:border-slate-700 shadow-[0_8px_30px_rgb(15,23,42,0.02)] border-l-4 border-l-emerald-500">
                <div class="flex items-center gap-2 mb-3 border-b border-slate-50 dark:border-slate-700/50 pb-2">
                    <i class="bi bi-clipboard-check-fill text-emerald-500 text-sm"></i>
                    <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider">Rekomendasi Tindakan</h3>
                </div>
                <div class="text-xs text-slate-700 dark:text-slate-200 leading-relaxed font-medium">
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
                        Tidak ada rekomendasi spesifik.
                    @endif
                </div>
            </div>

                        {{-- Hasil Analisis Lab --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-100 dark:border-slate-700 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
        <div class="flex items-center gap-2 mb-3 border-b border-slate-50 dark:border-slate-700/50 pb-2">
            <i class="bi bi-clipboard2-pulse text-teal-500 text-sm"></i>
            <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider">Hasil Analisis Lab</h3>
        </div>

        @if(!empty($analysis->lab_analysis))
            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed bg-slate-50/50 dark:bg-slate-800/50 rounded-lg p-3 border border-slate-100 dark:border-slate-700/50">
                {{ $analysis->lab_analysis }}
            </p>
            @if(!empty($analysis->lab_files))
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach($analysis->lab_files as $path)
                        <a href="{{ asset('storage/' . $path) }}" target="_blank"
                        class="inline-flex items-center gap-1 text-[11px] text-indigo-600 hover:underline">
                            <i class="bi bi-file-earmark-text"></i> Lihat file lab {{ $loop->iteration }}
                        </a>
                    @endforeach
                </div>
            @endif
        @else
            <p class="text-xs text-slate-400 italic">
                Tidak ada input data lab untuk analisis ini.
            </p>
        @endif
    </div>

      {{-- Potensi Komplikasi --}}
            @if(isset($result['potential_complications']))
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-100 dark:border-slate-700 shadow-[0_8px_30px_rgb(15,23,42,0.02)] border-l-4 border-l-rose-500">
                <div class="flex items-center gap-2 mb-3 border-b border-slate-50 dark:border-slate-700/50 pb-2">
                    <i class="bi bi-exclamation-octagon-fill text-rose-500 text-sm"></i>
                    <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider">Potensi Komplikasi (AI)</h3>
                </div>
                <div class="text-xs text-slate-700 dark:text-slate-200 leading-relaxed font-medium">
                    @if(is_array($result['potential_complications']))
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach($result['potential_complications'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        {{ $result['potential_complications'] }}
                    @endif
                </div>
            </div>
            @endif
    
            {{-- ── Validasi Dokter ─────────────────────────────────── --}}
            {{--
                PENTING: blok ini divalidasi terhadap model ClinicalAnalysis ($analysis),
                BUKAN CombinedDiagnosis ($combined), karena AnalysisController@confirm dan
                @reject menulis ke tabel clinical_analyses. $combined bisa null (belum
                digabung dengan citra), jadi jangan pernah baca $combined->validation_status
                di sini.
            --}}
            @if($combined)
                {{-- Sudah digabung dengan analisis citra: validasi dilakukan di halaman
                     Diagnosis Gabungan, bukan di sini (lihat banner di atas). --}}
            @else
                <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-100 dark:border-slate-700 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
                    <div class="flex items-center gap-2 mb-3 border-b border-slate-50 dark:border-slate-700/50 pb-2">
                        <i class="bi bi-patch-check-fill text-indigo-500 text-sm"></i>
                        <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider">Validasi Dokter (Keputusan Final)</h3>
                    </div>

                    @if($analysis->validation_status === 'confirmed')
                        <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-400 text-xs font-bold mb-2">
                            <i class="bi bi-check-circle-fill"></i> Diagnosis telah disetujui dokter.
                        </div>
                        <div class="text-xs text-slate-600 dark:text-slate-300 space-y-1">
                            @if($analysis->doctor_risk_level)
                                <p><span class="font-bold">Tingkat Risiko (Dokter):</span> {{ $analysis->doctor_risk_level }}</p>
                            @endif
                            @if($analysis->medication_recommendation)
                                <p><span class="font-bold">Obat:</span> {{ $analysis->medication_recommendation }}</p>
                            @endif
                            @if($analysis->doctor_analysis)
                                <p><span class="font-bold">Catatan:</span> {{ $analysis->doctor_analysis }}</p>
                            @endif
                        </div>
                    @elseif($analysis->validation_status === 'corrected')
                        <div class="flex items-center gap-2 text-rose-700 dark:text-rose-400 text-xs font-bold mb-2">
                            <i class="bi bi-x-circle-fill"></i> Diagnosis dikoreksi oleh dokter.
                        </div>
                        <div class="text-xs text-slate-600 dark:text-slate-300 space-y-1">
                            <p><span class="font-bold">Diagnosis Final Dokter:</span> {{ $analysis->doctor_diagnosis }}</p>
                            @if($analysis->doctor_risk_level)
                                <p><span class="font-bold">Tingkat Risiko (Dokter):</span> {{ $analysis->doctor_risk_level }}</p>
                            @endif
                            @if($analysis->medication_recommendation)
                                <p><span class="font-bold">Obat:</span> {{ $analysis->medication_recommendation }}</p>
                            @endif
                            <p><span class="font-bold">Catatan Koreksi:</span> {{ $analysis->doctor_analysis }}</p>
                        </div>
                    @else
                        <div class="flex gap-2 mb-3">
                            <button type="button" onclick="toggleForm('approve')"
                                class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors">
                                <i class="bi bi-check-lg"></i> Setujui
                            </button>
                            <button type="button" onclick="toggleForm('reject')"
                                class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors">
                                <i class="bi bi-x-lg"></i> Tolak
                            </button>
                        </div>

                        <form id="approve-form" class="hidden space-y-2" method="POST" action="{{ route('analysis.confirm', $analysis->id) }}">
                        @csrf
                        @method('PATCH')
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
                            <div>
                                <label class="text-[11px] font-bold text-slate-500 uppercase">Catatan Dokter (opsional)</label>
                                <textarea name="doctor_notes_validation" rows="2" placeholder="Catatan dokter (opsional)"
                                    class="w-full text-xs border border-slate-200 rounded-lg p-2 focus:outline-none focus:border-emerald-500"></textarea>
                            </div>
                            <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700">
                                Kirim Persetujuan
                            </button>
                        </form>

                        <form id="reject-form" class="hidden space-y-2" method="POST" action="{{ route('analysis.reject', $analysis->id) }}">
                            @csrf
                            <div>
                                <label class="text-[11px] font-bold text-slate-500 uppercase">Diagnosis Final Versi Dokter *</label>
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
            @endif
        </div>

        {{-- Kanan: confidence + disclaimer + obat --}}
        <div class="space-y-4">

            {{-- Confidence --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl"></div>
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <i class="bi bi-shield-check text-blue-500 dark:text-cyan-400"></i>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Tingkat Keyakinan AI</h3>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-5xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-cyan-400 dark:to-blue-400">
                            {{ $result['confidence'] }}
                        </span>
                        <span class="text-xl font-bold text-blue-600 dark:text-cyan-400">%</span>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-500 dark:from-blue-500 dark:to-cyan-400 h-1.5 rounded-full"
                             style="width: {{ $result['confidence'] }}%"></div>
                    </div>
                    <div class="flex justify-between items-center text-[10px] text-slate-400 mt-2">
                        <span>Mesin Validasi AI</span>
                        <span class="font-mono text-blue-500 dark:text-cyan-300 font-bold">Presisi Tinggi</span>
                    </div>
                </div>
            </div>

            {{-- Disclaimer --}}
            <div class="bg-amber-50/50 dark:bg-amber-900/20 rounded-xl p-4 border border-amber-100/70 dark:border-amber-700/30 text-amber-800 dark:text-amber-200">
                <div class="flex items-start gap-2 text-xs">
                    <i class="bi bi-exclamation-triangle-fill text-amber-500 mt-0.5 shrink-0"></i>
                    <div class="space-y-1">
                        <p class="font-bold text-[11px] uppercase tracking-wider">Pemberitahuan Klinis</p>
                        <p class="text-[11px] text-amber-700/90 dark:text-amber-300/90 leading-relaxed">
                            Hasil diagnostik AI ini ditujukan murni untuk mendukung proses pengambilan keputusan medis.
                            Validasi klinis akhir tetap menjadi tanggung jawab sepenuhnya dari dokter spesialis atau tenaga medis yang merawat pasien.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Rekomendasi Obat --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-100 dark:border-slate-700 shadow-[0_8px_30px_rgb(15,23,42,0.02)] border-l-4 border-l-indigo-500">
                <div class="flex items-center gap-2 mb-3 border-b border-slate-50 dark:border-slate-700/50 pb-2">
                    <i class="bi bi-capsule text-indigo-500 text-sm"></i>
                    <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider">Rekomendasi Obat (AI)</h3>
                </div>
                <div class="text-xs text-slate-700 dark:text-slate-200 leading-relaxed font-medium">
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
                </div>
            </div>

            {{-- Alasan Tingkat Keyakinan --}}
            @if(isset($result['confidence_reason']))
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-100 dark:border-slate-700 shadow-[0_8px_30px_rgb(15,23,42,0.02)] border-l-4 border-l-blue-500">
                <div class="flex items-center gap-2 mb-3 border-b border-slate-50 dark:border-slate-700/50 pb-2">
                    <i class="bi bi-info-circle-fill text-blue-500 text-sm"></i>
                    <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider">Alasan Keyakinan AI</h3>
                </div>
                <div class="text-xs text-slate-700 dark:text-slate-200 leading-relaxed font-medium">
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

            {{-- Rekomendasi Terapi --}}
            @if(isset($result['treatment_information']))
            <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-100 dark:border-slate-700 shadow-[0_8px_30px_rgb(15,23,42,0.02)] border-l-4 border-l-emerald-500">
                <div class="flex items-center gap-2 mb-3 border-b border-slate-50 dark:border-slate-700/50 pb-2">
                    <i class="bi bi-heart-pulse-fill text-emerald-500 text-sm"></i>
                    <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider">Rekomendasi Terapi (AI)</h3>
                </div>
                <div class="text-xs text-slate-700 dark:text-slate-200 leading-relaxed font-medium">
                    @if(is_array($result['treatment_information']))
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach($result['treatment_information'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        {{ $result['treatment_information'] }}
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>

    

    {{-- Chat box AI --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between mb-3 border-b border-slate-50 dark:border-slate-700/50 pb-2">
            <div class="flex items-center gap-2">
                <i class="bi bi-chat-dots-fill text-blue-500 text-sm"></i>
                <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider">Tanya Lebih Lanjut ke AI</h3>
            </div>
            <button id="clear-chat" title="Hapus riwayat chat"
                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs text-rose-500 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                <i class="bi bi-trash3"></i>
                <span class="font-medium">Hapus</span>
            </button>
        </div>

        <div id="chat-box" class="space-y-2 max-h-72 overflow-y-auto mb-3 text-xs"></div>

        <form id="chat-form" class="flex gap-2">
            <input type="text" id="chat-input" placeholder="Tanya seputar hasil diagnosis ini..."
                class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-blue-500">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700">
                <i class="bi bi-send"></i>
            </button>
        </form>
    </div>
</div>

<script>
function toggleForm(which) {
    document.getElementById('approve-form').classList.toggle('hidden', which !== 'approve');
    document.getElementById('reject-form').classList.toggle('hidden', which !== 'reject');
}

const chatType = "clinical";
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
    const style   = role === 'user' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700 dark:text-slate-200';
    const content = role === 'assistant' ? `<p>${markdownToHtml(text)}</p>` : text;
    chatBox.insertAdjacentHTML('beforeend', `
        <div class="flex ${align}">
            <div class="${style} px-3 py-2 rounded-lg max-w-[80%] leading-relaxed">${content}</div>
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
        if (res.ok) {
            chatBox.innerHTML = '';
        } else {
            alert('Gagal menghapus riwayat chat.');
        }
    } catch (err) {
        alert('Koneksi gagal: ' + err.message);
    }
});

document.getElementById('chat-form').addEventListener('submit', async function (e) {
    e.preventDefault();
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
});

loadHistory();
</script>
@endsection