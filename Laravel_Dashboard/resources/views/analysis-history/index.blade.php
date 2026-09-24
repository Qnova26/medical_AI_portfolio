@extends('layouts.dashboard')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <div>
        <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Riwayat Analisis AI</h1>
        <p class="text-sm font-medium text-slate-500 mt-0.5">Pantau, filter, dan tinjau kembali rekam diagnosis klinis yang diproses oleh AI.</p>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Sesi</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($summary['total_analysis']) }}</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-lg">
                <i class="bi bi-folder-check"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hari Ini</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">+{{ $summary['today_analysis'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 text-lg">
                <i class="bi bi-lightning-charge"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rata-rata Akurasi</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $summary['avg_confidence'] }}%</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 text-lg">
                <i class="bi bi-shield-check"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rasio Sukses AI</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $summary['success_rate'] }}%</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 text-lg">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex flex-col md:flex-row gap-3">
        <div class="relative flex-1">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" id="search-input" placeholder="Cari nama pasien atau diagnosis..."
                class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg pl-9 pr-4 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
        </div>
        <select id="status-filter"
            class="w-full md:w-52 text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
            <option value="Semua">Semua Status</option>
            <option value="confirmed">Dikonfirmasi</option>
            <option value="corrected">Dikoreksi</option>
            <option value="pending">Belum Ditinjau</option>
        </select>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4 text-left">Pasien</th>
                        <th class="py-3 px-4 text-left">Diagnosis Klinis</th>
                        <th class="py-3 px-4 text-left">Diagnosis Citra</th>
                        <th class="py-3 px-4 text-left">Diagnosis Final</th>
                        <th class="py-3 px-4 text-left">Confidence</th>
                        <th class="py-3 px-4 text-left">Status Validasi</th>
                        <th class="py-3 px-4 text-left">Tanggal</th>
                        <th class="py-3 px-4 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($histories as $h)
                    <tr class="table-row-items hover:bg-slate-50/50 transition-colors"
                        data-status="{{ $h['final_status'] }}"
                        data-patient="{{ $h['patient'] }}"
                        data-clinical-diagnosis="{{ $h['clinical_diagnosis'] }}"
                        data-image-diagnosis="{{ $h['image_diagnosis'] }}"

                        data-patient-name="{{ $h['patient'] }}"
                        data-date="{{ $h['date_display'] }}"
                        data-avg-confidence="{{ $h['avg_confidence'] }}"
                        data-is-analyzed="{{ $h['is_analyzed'] ? '1' : '0' }}"
                        data-final-status="{{ $h['final_status'] }}"

                        data-clinical-id="{{ $h['clinical_id'] }}"
                        data-clinical-raw-id="{{ $h['clinical_raw_id'] }}"
                        data-clinical-diagnosis2="{{ $h['clinical_diagnosis'] }}"
                        data-clinical-risk="{{ $h['clinical_risk_level'] }}"
                        data-clinical-prompt="{{ $h['clinical_prompt'] }}"
                        data-clinical-template="{{ $h['clinical_template_level'] }}"
                        data-clinical-penjelasan="{{ $h['clinical_penjelasan'] }}"
                        data-clinical-differential="{{ $h['clinical_differential'] }}"
                        data-clinical-recommendation="{{ $h['clinical_recommendation'] }}"
                        data-clinical-analisis="{{ $h['clinical_analisis_dokter'] }}"
                        data-clinical-doctor-diagnosis="{{ $h['clinical_doctor_diagnosis'] }}"
                        data-clinical-validation="{{ $h['clinical_validation'] }}"
                        data-clinical-confidence="{{ $h['clinical_confidence'] }}"
                        data-clinical-files="{{ $h['clinical_files'] }}"
                        data-clinical-doctor-risk="{{ $h['clinical_doctor_risk_level'] ?? '' }}"
                        data-clinical-medication="{{ $h['clinical_medication'] ?? '' }}"
                        data-clinical-medication-doctor="{{ $h['clinical_medication_doctor'] ?? '' }}"
                        data-clinical-needs-imaging="{{ !empty($h['clinical_needs_imaging_correlation']) ? '1' : '0' }}"
                        data-clinical-imaging-reason="{{ $h['clinical_imaging_correlation_reason'] ?? '' }}"

                        data-body-temperature="{{ $h['body_temperature'] }}"
                        data-heart-rate="{{ $h['heart_rate'] }}"
                        data-respiratory-rate="{{ $h['respiratory_rate'] }}"
                        data-blood-pressure="{{ $h['blood_pressure'] }}"
                        data-medical-history="{{ $h['medical_history'] }}"
                        data-allergies="{{ $h['allergies'] }}"
                        data-symptoms="{{ $h['symptoms'] }}"
                        data-doctor-notes="{{ $h['doctor_notes'] }}"
                        data-other-info="{{ $h['other_info'] }}"

                        data-image-id="{{ $h['image_id'] }}"
                        data-image-raw-id="{{ $h['image_raw_id'] }}"
                        data-image-diagnosis2="{{ $h['image_diagnosis'] }}"
                        data-image-risk="{{ $h['image_risk_level'] }}"
                        data-image-type="{{ $h['image_type'] }}"
                        data-image-body-part="{{ $h['image_body_part'] }}"
                        data-image-prompt="{{ $h['image_prompt'] }}"
                        data-image-penjelasan="{{ $h['image_penjelasan'] }}"
                        data-image-recommendation="{{ $h['image_recommendation'] }}"
                        data-image-analisis="{{ $h['image_analisis_dokter'] }}"
                        data-image-doctor-diagnosis="{{ $h['image_doctor_diagnosis'] }}"
                        data-image-validation="{{ $h['image_validation'] }}"
                        data-image-confidence="{{ $h['image_confidence'] }}"
                        data-image-files="{{ $h['image_files'] }}"
                        data-image-result-images="{{ $h['image_result_images'] }}"
                        data-image-doctor-risk="{{ $h['image_doctor_risk_level'] ?? '' }}"
                        data-image-medication="{{ $h['image_medication'] ?? '' }}"
                        data-image-medication-doctor="{{ $h['image_medication_doctor'] ?? '' }}"
                        data-image-needs-clinical="{{ !empty($h['image_needs_clinical_correlation']) ? '1' : '0' }}"
                        data-image-clinical-reason="{{ $h['image_clinical_correlation_reason'] ?? '' }}"

                        data-combined-id="{{ $h['combined_id'] ?? '' }}"
                        data-combined-status="{{ $h['combined_status'] }}"
                        data-combined-diagnosis="{{ $h['combined_diagnosis'] }}"
                        data-combined-risk="{{ $h['combined_risk_level'] }}"
                        data-combined-confidence="{{ $h['combined_confidence'] }}"
                        data-combined-summary="{{ $h['combined_summary'] }}"
                        data-combined-recommendation="{{ $h['combined_recommendation'] }}"
                        data-combined-correlation="{{ $h['combined_correlation'] }}"
                        data-combined-validation="{{ $h['combined_validation'] ?? '' }}">

                        <td class="py-3.5 px-4 text-left font-semibold text-slate-800">{{ $h['patient'] }}</td>

                        <td class="py-3.5 px-4 text-left">
                            @if($h['clinical_diagnosis'])
                                <div class="flex items-center gap-1.5">
                                    <span class="text-slate-700 dark:text-slate-200 text-xs font-medium">{{ Str::limit($h['clinical_diagnosis'], 40) }}</span>
                                    @if(!empty($h['clinical_needs_imaging_correlation']) && ($h['combined_status'] ?? null) !== 'done')
                                        <i class="bi bi-exclamation-triangle-fill text-amber-500 text-[11px]"
                                           title="AI menilai butuh korelasi citra: {{ $h['clinical_imaging_correlation_reason'] ?? '' }}"></i>
                                    @endif
                                </div>
                            @else
                                <span class="text-slate-300 text-[11px]">—</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 text-left">
                            @if($h['image_diagnosis'])
                                <div class="flex items-center gap-1.5">
                                    <span class="text-slate-700 dark:text-slate-200 text-xs font-medium">{{ Str::limit($h['image_diagnosis'], 40) }}</span>
                                    @if(!empty($h['image_needs_clinical_correlation']) && ($h['combined_status'] ?? null) !== 'done')
                                        <i class="bi bi-exclamation-triangle-fill text-amber-500 text-[11px]"
                                           title="AI menilai butuh korelasi klinis: {{ $h['image_clinical_correlation_reason'] ?? '' }}"></i>
                                    @endif
                                </div>
                            @else
                                <span class="text-slate-300 text-[11px]">—</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 text-left">
                            @if($h['combined_status'] === 'done')
                                <a href="{{ route('analysis-combined.result', $h['combined_id']) }}"
                                   class="text-teal-700 hover:underline text-xs font-bold">
                                    {{ Str::limit($h['combined_diagnosis'], 40) }}
                                </a>
                            @elseif($h['combined_status'] === 'pending')
                                <span class="inline-block whitespace-nowrap px-2 py-1 bg-amber-50 text-amber-600 rounded text-[11px] font-semibold">⏳ Diproses</span>
                            @elseif($h['combined_status'] === 'failed')
                                <span class="inline-block whitespace-nowrap px-2 py-1 bg-rose-50 text-rose-600 rounded text-[11px] font-semibold">⚠️ Gagal</span>
                            @else
                                <span class="text-slate-300 text-[11px]">—</span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 text-left font-bold {{ $h['avg_confidence'] >= 90 ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $h['avg_confidence'] > 0 ? $h['avg_confidence'] . '%' : '—' }}
                        </td>
                        <td class="py-3.5 px-4 text-left">
                            @if($h['final_status'] === 'confirmed')
                                <span class="inline-block whitespace-nowrap px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold uppercase">Dikonfirmasi</span>
                            @elseif($h['final_status'] === 'corrected')
                                <span class="inline-block whitespace-nowrap px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold uppercase">Dikoreksi</span>
                            @else
                                <span class="inline-block whitespace-nowrap px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold uppercase">Belum Ditinjau</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-left text-slate-500 font-medium">{{ $h['date_display'] }}</td>
                        <td class="py-3.5 px-4 text-left">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" onclick="openDetailModal(this)"
                                    class="px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-[11px] font-bold text-slate-600 hover:text-cyan-600 hover:border-cyan-200 hover:bg-cyan-50/30 transition-all shadow-sm">
                                    <i class="bi bi-eye"></i> Lihat
                                </button>
                                <button type="button" onclick="goToValidation(this)"
                                    class="px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-[11px] font-bold text-slate-600 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all shadow-sm">
                                    <i class="bi bi-patch-check"></i> Validasi
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400 text-sm">
                            <i class="bi bi-inbox text-2xl block mb-2"></i>
                            Belum ada riwayat analisis.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ==================== MODAL DETAIL (READ-ONLY) ==================== --}}
<div id="detail-modal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl border border-slate-100 max-w-2xl w-full overflow-hidden transform scale-95 transition-all duration-200" id="modal-card">
        <div class="bg-slate-50 px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded bg-blue-100 text-blue-600 flex items-center justify-center text-xs"><i class="bi bi-cpu-fill"></i></div>
                <h3 class="font-bold text-slate-800 text-sm">Detail Hasil Analisis AI</h3>
            </div>
            <button onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 text-lg"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="p-5 space-y-5 text-xs max-h-[78vh] overflow-y-auto">

            {{-- Info pasien --}}
            <div class="grid grid-cols-2 gap-4 bg-slate-50 p-3 rounded-lg border border-slate-100">
                <div>
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide">Nama Pasien</span>
                    <span id="modal-patient" class="font-bold text-slate-800 text-sm"></span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide">Tanggal</span>
                    <span id="modal-date" class="font-medium text-slate-700"></span>
                </div>
            </div>

            {{-- ANALISIS CITRA --}}
            <div id="section-image" class="rounded-lg border border-violet-100 overflow-hidden">
                <div class="bg-violet-50 px-4 py-2.5 flex items-center gap-2">
                    <i class="bi bi-image text-violet-600 text-sm"></i>
                    <span class="text-[11px] font-bold text-violet-700 uppercase tracking-wider">Analisis Citra</span>
                    <span id="modal-image-id" class="font-mono text-[10px] text-violet-400"></span>
                    <a id="btn-detail-image" href="#"
                        class="ml-auto flex items-center gap-1 px-2.5 py-1 rounded-lg bg-violet-600 hover:bg-violet-700 text-white text-[10px] font-bold transition-colors shadow-sm">
                        <i class="bi bi-box-arrow-up-right"></i> Buka & Validasi
                    </a>
                </div>
                <div class="p-4 space-y-3">
                    <div id="mi-correlation-banner" class="hidden bg-amber-50 border border-amber-200 rounded-lg p-2.5 flex items-start gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-amber-500 mt-0.5"></i>
                        <p class="text-[11px] text-amber-800 leading-relaxed">AI menilai perlu korelasi klinis tambahan: <span id="mi-correlation-reason" class="font-medium"></span></p>
                    </div>

                    <div class="bg-slate-50 rounded-lg p-3 border border-slate-100 space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Info Citra</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div><span class="text-slate-400">Jenis:</span> <span id="mi-type" class="font-medium text-slate-700"></span></div>
                            <div><span class="text-slate-400">Bagian Tubuh:</span> <span id="mi-bodypart" class="font-medium text-slate-700"></span></div>
                            <div><span class="text-slate-400">Prompt:</span> <span id="mi-prompt" class="font-medium text-slate-700"></span></div>
                        </div>
                    </div>

                    <div id="mi-files-section">
                        <span class="text-slate-500 font-medium block mb-1">Citra yang Dianalisis</span>
                        <div id="mi-files" class="flex flex-wrap gap-2"></div>
                    </div>

                    <div id="mi-result-images-section" class="mt-3">
                        <span class="text-slate-500 font-medium block mb-1">Hasil Anotasi AI</span>
                        <div id="mi-result-images" class="flex flex-wrap gap-2"></div>
                    </div>

                    <div class="border-t border-slate-100 pt-3 space-y-2">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Hasil Analisis AI</p>
                        <div class="flex justify-between pb-1 border-b border-slate-100">
                            <span class="text-slate-500">Diagnosis</span>
                            <span id="modal-image-diagnosis" class="font-bold text-violet-600 bg-violet-50 px-2 py-0.5 rounded text-[11px]"></span>
                        </div>
                        <div class="flex justify-between pb-1 border-b border-slate-100">
                            <span class="text-slate-500">Tingkat Risiko</span>
                            <span id="modal-image-risk" class="font-bold text-[11px]"></span>
                        </div>
                        <div class="flex justify-between pb-1 border-b border-slate-100">
                            <span class="text-slate-500">Confidence</span>
                            <span id="modal-image-confidence" class="font-bold text-slate-800"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block mb-1">Temuan Klinis</span>
                            <p id="modal-image-penjelasan" class="text-slate-700 bg-slate-50 p-2 rounded text-[11px] leading-relaxed"></p>
                        </div>
                        <div>
                            <span class="text-slate-500 block mb-1">Rekomendasi</span>
                            <p id="modal-image-recommendation" class="text-slate-700 bg-emerald-50 p-2 rounded text-[11px] leading-relaxed border-l-2 border-emerald-400"></p>
                        </div>
                        <div>
                            <span class="text-slate-500 block mb-1">Rekomendasi Obat (AI)</span>
                            <p id="modal-image-medication" class="text-slate-700 bg-violet-50 p-2 rounded text-[11px] leading-relaxed border-l-2 border-violet-400"></p>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-3 space-y-2">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Validasi Dokter</p>
                        <div class="flex justify-between pb-1 border-b border-slate-100">
                            <span class="text-slate-500">Status</span>
                            <span id="modal-image-validation"></span>
                        </div>
                        <div class="flex justify-between pb-1 border-b border-slate-100">
                            <span class="text-slate-500">Tingkat Risiko (Dokter)</span>
                            <span id="modal-image-doctor-risk" class="font-bold text-[11px]"></span>
                        </div>
                        <div id="mi-doctor-diagnosis-row" class="hidden">
                            <span class="text-slate-500 block mb-1">Koreksi Diagnosis Dokter</span>
                            <p id="modal-image-doctor-diagnosis" class="text-slate-800 font-bold bg-amber-50 p-2 rounded text-[11px] border-l-2 border-amber-400"></p>
                        </div>
                        <div>
                            <span class="text-slate-500 block mb-1">Catatan Dokter</span>
                            <p id="modal-image-analisis" class="text-slate-800 bg-blue-50 p-2 rounded text-[11px] leading-relaxed"></p>
                        </div>
                        <div>
                            <span class="text-slate-500 block mb-1">Rekomendasi Obat (Dokter)</span>
                            <p id="modal-image-doctor-medication" class="text-slate-800 bg-blue-50 p-2 rounded text-[11px] leading-relaxed"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ANALISIS KLINIS --}}
            <div id="section-clinical" class="rounded-lg border border-indigo-100 overflow-hidden">
                <div class="bg-indigo-50 px-4 py-2.5 flex items-center gap-2">
                    <i class="bi bi-file-earmark-medical text-indigo-600 text-sm"></i>
                    <span class="text-[11px] font-bold text-indigo-700 uppercase tracking-wider">Analisis Klinis</span>
                    <span id="modal-clinical-id" class="font-mono text-[10px] text-indigo-400"></span>
                    <a id="btn-detail-clinical" href="#"
                        class="ml-auto flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold transition-colors shadow-sm">
                        <i class="bi bi-box-arrow-up-right"></i> Buka & Validasi
                    </a>
                </div>
                <div class="p-4 space-y-3">
                    <div id="mc-correlation-banner" class="hidden bg-amber-50 border border-amber-200 rounded-lg p-2.5 flex items-start gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-amber-500 mt-0.5"></i>
                        <p class="text-[11px] text-amber-800 leading-relaxed">AI menilai perlu korelasi citra tambahan: <span id="mc-correlation-reason" class="font-medium"></span></p>
                    </div>

                    <div class="bg-slate-50 rounded-lg p-3 space-y-2 border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Data Input Pasien</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div><span class="text-slate-400">Template:</span> <span id="mc-template" class="font-medium text-slate-700"></span></div>
                            <div><span class="text-slate-400">Prompt:</span> <span id="mc-prompt" class="font-medium text-slate-700"></span></div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 mt-2">
                            <div><span class="text-slate-400">Suhu:</span> <span id="mc-temp" class="font-medium text-slate-700"></span></div>
                            <div><span class="text-slate-400">Nadi:</span> <span id="mc-hr" class="font-medium text-slate-700"></span></div>
                            <div><span class="text-slate-400">Pernapasan:</span> <span id="mc-rr" class="font-medium text-slate-700"></span></div>
                            <div><span class="text-slate-400">Tekanan Darah:</span> <span id="mc-bp" class="font-medium text-slate-700"></span></div>
                        </div>
                        <div class="space-y-1 mt-1">
                            <div><span class="text-slate-400">Riwayat:</span> <span id="mc-history" class="font-medium text-slate-700"></span></div>
                            <div><span class="text-slate-400">Alergi:</span> <span id="mc-allergy" class="font-medium text-slate-700"></span></div>
                            <div><span class="text-slate-400">Gejala:</span> <span id="mc-symptoms" class="font-medium text-slate-700"></span></div>
                            <div><span class="text-slate-400">Catatan Dokter:</span> <span id="mc-notes" class="font-medium text-slate-700"></span></div>
                            <div><span class="text-slate-400">Info Lain:</span> <span id="mc-other" class="font-medium text-slate-700"></span></div>
                        </div>
                    </div>

                    <div id="mc-files-section">
                        <span class="text-slate-500 font-medium block mb-1">File Medis Diunggah</span>
                        <div id="mc-files" class="flex flex-wrap gap-2"></div>
                    </div>

                    <div class="border-t border-slate-100 pt-3 space-y-2">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Hasil Analisis AI</p>
                        <div class="flex justify-between pb-1 border-b border-slate-100">
                            <span class="text-slate-500">Diagnosis</span>
                            <span id="modal-clinical-diagnosis" class="font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded text-[11px]"></span>
                        </div>
                        <div class="flex justify-between pb-1 border-b border-slate-100">
                            <span class="text-slate-500">Tingkat Risiko</span>
                            <span id="modal-clinical-risk" class="font-bold text-[11px]"></span>
                        </div>
                        <div class="flex justify-between pb-1 border-b border-slate-100">
                            <span class="text-slate-500">Confidence</span>
                            <span id="modal-clinical-confidence" class="font-bold text-slate-800"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block mb-1">Ringkasan Klinis</span>
                            <p id="modal-clinical-penjelasan" class="text-slate-700 bg-slate-50 p-2 rounded text-[11px] leading-relaxed"></p>
                        </div>
                        <div>
                            <span class="text-slate-500 block mb-1">Diagnosis Banding</span>
                            <p id="modal-clinical-differential" class="text-slate-700 bg-slate-50 p-2 rounded text-[11px] leading-relaxed"></p>
                        </div>
                        <div>
                            <span class="text-slate-500 block mb-1">Rekomendasi</span>
                            <p id="modal-clinical-recommendation" class="text-slate-700 bg-emerald-50 p-2 rounded text-[11px] leading-relaxed border-l-2 border-emerald-400"></p>
                        </div>
                        <div>
                            <span class="text-slate-500 block mb-1">Rekomendasi Obat (AI)</span>
                            <p id="modal-clinical-medication" class="text-slate-700 bg-indigo-50 p-2 rounded text-[11px] leading-relaxed border-l-2 border-indigo-400"></p>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-3 space-y-2">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Validasi Dokter</p>
                        <div class="flex justify-between pb-1 border-b border-slate-100">
                            <span class="text-slate-500">Status</span>
                            <span id="modal-clinical-validation"></span>
                        </div>
                        <div class="flex justify-between pb-1 border-b border-slate-100">
                            <span class="text-slate-500">Tingkat Risiko (Dokter)</span>
                            <span id="modal-clinical-doctor-risk" class="font-bold text-[11px]"></span>
                        </div>
                        <div id="mc-doctor-diagnosis-row" class="hidden">
                            <span class="text-slate-500 block mb-1">Koreksi Diagnosis Dokter</span>
                            <p id="modal-clinical-doctor-diagnosis" class="text-slate-800 font-bold bg-amber-50 p-2 rounded text-[11px] border-l-2 border-amber-400"></p>
                        </div>
                        <div>
                            <span class="text-slate-500 block mb-1">Catatan Dokter</span>
                            <p id="modal-clinical-analisis" class="text-slate-800 bg-blue-50 p-2 rounded text-[11px] leading-relaxed"></p>
                        </div>
                        <div>
                            <span class="text-slate-500 block mb-1">Rekomendasi Obat (Dokter)</span>
                            <p id="modal-clinical-doctor-medication" class="text-slate-800 bg-blue-50 p-2 rounded text-[11px] leading-relaxed"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DIAGNOSIS FINAL GABUNGAN --}}
            <div id="section-combined" class="rounded-lg border border-teal-100 overflow-hidden">
                <div class="bg-teal-50 px-4 py-2.5 flex items-center gap-2">
                    <i class="bi bi-stars text-teal-600 text-sm"></i>
                    <span class="text-[11px] font-bold text-teal-700 uppercase tracking-wider">Diagnosis Final (Gabungan AI)</span>
                    <span id="modal-combined-status" class="ml-auto"></span>
                    <a id="btn-detail-combined" href="#"
                        class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-[10px] font-bold transition-colors shadow-sm">
                        <i class="bi bi-box-arrow-up-right"></i> Buka & Validasi
                    </a>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex justify-between pb-1 border-b border-slate-100">
                        <span class="text-slate-500">Diagnosis Final</span>
                        <span id="modal-combined-diagnosis" class="font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded text-[11px]"></span>
                    </div>
                    <div class="flex justify-between pb-1 border-b border-slate-100">
                        <span class="text-slate-500">Tingkat Risiko</span>
                        <span id="modal-combined-risk" class="font-bold text-[11px]"></span>
                    </div>
                    <div class="flex justify-between pb-1 border-b border-slate-100">
                        <span class="text-slate-500">Confidence</span>
                        <span id="modal-combined-confidence" class="font-bold text-slate-800"></span>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-1">Ringkasan Gabungan</span>
                        <p id="modal-combined-summary" class="text-slate-700 bg-slate-50 p-2 rounded text-[11px] leading-relaxed"></p>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-1">Korelasi Klinis & Citra</span>
                        <p id="modal-combined-correlation" class="text-slate-700 bg-teal-50 p-2 rounded text-[11px] leading-relaxed border-l-2 border-teal-400"></p>
                    </div>
                    <div>
                        <span class="text-slate-500 block mb-1">Rekomendasi Final</span>
                        <p id="modal-combined-recommendation" class="text-slate-700 bg-emerald-50 p-2 rounded text-[11px] leading-relaxed border-l-2 border-emerald-400"></p>
                    </div>
                    <div class="flex justify-between pb-1 border-t border-slate-100 pt-2">
                        <span class="text-slate-500">Status Validasi Final</span>
                        <span id="modal-combined-validation"></span>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-100 rounded-lg p-3 text-[11px] text-slate-500 flex items-start gap-2">
                <i class="bi bi-info-circle mt-0.5"></i>
                <p>Untuk menyetujui atau menolak hasil analisis (termasuk mengisi diagnosis versi dokter), buka tombol <span class="font-bold">"Buka & Validasi"</span> pada bagian terkait di atas — proses validasi dilakukan di halaman hasil masing-masing agar catatan koreksi selalu lengkap.</p>
            </div>

        </div>
        <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="closeDetailModal()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-lg transition-colors text-[11px]">Tutup</button>
        </div>
    </div>
</div>

<script>
document.getElementById('search-input').addEventListener('input', filterTable);
document.getElementById('status-filter').addEventListener('change', filterTable);

function filterTable() {
    const q      = document.getElementById('search-input').value.toLowerCase();
    const status = document.getElementById('status-filter').value;
    document.querySelectorAll('.table-row-items').forEach(row => {
        const matchSearch = !q
            || (row.dataset.patient || '').toLowerCase().includes(q)
            || (row.dataset.clinicalDiagnosis || '').toLowerCase().includes(q)
            || (row.dataset.imageDiagnosis || '').toLowerCase().includes(q)
            || (row.dataset.combinedDiagnosis || '').toLowerCase().includes(q);
        const matchStatus = status === 'Semua' || row.dataset.finalStatus === status;
        row.style.display = (matchSearch && matchStatus) ? '' : 'none';
    });
}

function riskBadge(level) {
    if (!level) return '—';
    const map = {
        'Tinggi': 'text-rose-600 bg-rose-50',
        'Sedang': 'text-amber-600 bg-amber-50',
        'Rendah': 'text-emerald-600 bg-emerald-50',
    };
    const cls = map[level] || 'text-slate-600 bg-slate-50';
    return `<span class="px-2 py-0.5 rounded text-[11px] font-bold ${cls}">${level}</span>`;
}

function validationBadge(status) {
    if (status === 'confirmed') return `<span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 font-bold rounded-full text-[10px]">Dikonfirmasi</span>`;
    if (status === 'corrected') return `<span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 font-bold rounded-full text-[10px]">Dikoreksi</span>`;
    return `<span class="px-2.5 py-0.5 bg-slate-200 text-slate-600 font-bold rounded-full text-[10px]">Belum Ditinjau</span>`;
}

function renderFiles(files, container, isImage = false) {
    container.innerHTML = '';
    if (!files || files.length === 0) {
        container.innerHTML = '<span class="text-slate-300 text-[11px]">Tidak ada file</span>';
        return;
    }
    files.forEach(f => {
        if (isImage) {
            container.innerHTML += `
                <a href="${f.url}" target="_blank">
                    <img src="${f.url}" alt="${f.name}"
                        class="w-24 h-24 object-cover rounded-lg border border-slate-200 hover:opacity-80 transition-opacity">
                </a>`;
        } else {
            container.innerHTML += `
                <a href="${f.url}" target="_blank"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-[11px] font-medium text-slate-700 transition-colors">
                    <i class="bi bi-file-earmark text-slate-500"></i> ${f.name}
                </a>`;
        }
    });
}

function openDetailModal(btn) {
    const d = btn.closest('tr').dataset;

    document.getElementById('modal-patient').innerText = d.patientName;
    document.getElementById('modal-date').innerText    = d.date;

    // Clinical
    const hasClinical = d.clinicalId && d.clinicalId !== '';
    const secClin = document.getElementById('section-clinical');
    if (hasClinical) {
        secClin.classList.remove('hidden');
        document.getElementById('modal-clinical-id').innerText           = d.clinicalId;
        document.getElementById('mc-template').innerText                 = d.clinicalTemplate || '—';
        document.getElementById('mc-prompt').innerText                   = d.clinicalPrompt || '—';
        document.getElementById('mc-temp').innerText                     = d.bodyTemperature ? d.bodyTemperature + ' °C' : '—';
        document.getElementById('mc-hr').innerText                       = d.heartRate ? d.heartRate + ' bpm' : '—';
        document.getElementById('mc-rr').innerText                       = d.respiratoryRate ? d.respiratoryRate + ' rpm' : '—';
        document.getElementById('mc-bp').innerText                       = d.bloodPressure ? d.bloodPressure + ' mmHg' : '—';
        document.getElementById('mc-history').innerText                  = d.medicalHistory || '—';
        document.getElementById('mc-allergy').innerText                  = d.allergies || '—';
        document.getElementById('mc-symptoms').innerText                 = d.symptoms || '—';
        document.getElementById('mc-notes').innerText                    = d.doctorNotes || '—';
        document.getElementById('mc-other').innerText                    = d.otherInfo || '—';
        document.getElementById('modal-clinical-diagnosis').innerText    = d.clinicalDiagnosis2 || '—';
        document.getElementById('modal-clinical-risk').innerHTML         = riskBadge(d.clinicalRisk);
        document.getElementById('modal-clinical-confidence').innerText   = d.clinicalConfidence ? d.clinicalConfidence + '%' : '—';
        document.getElementById('modal-clinical-penjelasan').innerText   = d.clinicalPenjelasan || '—';
        document.getElementById('modal-clinical-differential').innerText = d.clinicalDifferential || '—';
        document.getElementById('modal-clinical-recommendation').innerText = d.clinicalRecommendation || '—';
        document.getElementById('modal-clinical-medication').innerText   = d.clinicalMedication || 'Tidak ada rekomendasi obat dari AI.';
        document.getElementById('modal-clinical-validation').innerHTML   = validationBadge(d.clinicalValidation);
        document.getElementById('modal-clinical-doctor-risk').innerHTML  = riskBadge(d.clinicalDoctorRisk);

        const dcRow = document.getElementById('mc-doctor-diagnosis-row');
        if (d.clinicalDoctorDiagnosis && d.clinicalDoctorDiagnosis !== '' && d.clinicalDoctorDiagnosis !== 'null') {
            dcRow.classList.remove('hidden');
            document.getElementById('modal-clinical-doctor-diagnosis').innerText = d.clinicalDoctorDiagnosis;
        } else {
            dcRow.classList.add('hidden');
        }

        document.getElementById('modal-clinical-analisis').innerText = (d.clinicalAnalisis && d.clinicalAnalisis !== 'null' && d.clinicalAnalisis !== '')
            ? d.clinicalAnalisis : 'Belum diisi oleh dokter.';

        document.getElementById('modal-clinical-doctor-medication').innerText = (d.clinicalMedicationDoctor && d.clinicalMedicationDoctor !== 'null' && d.clinicalMedicationDoctor !== '')
            ? d.clinicalMedicationDoctor : 'Belum diisi oleh dokter.';

        const mcBanner = document.getElementById('mc-correlation-banner');
        if (d.clinicalNeedsImaging === '1' && d.combinedStatus !== 'done') {
            mcBanner.classList.remove('hidden');
            document.getElementById('mc-correlation-reason').innerText = d.clinicalImagingReason || '-';
        } else {
            mcBanner.classList.add('hidden');
        }

        const btnDetailClin = document.getElementById('btn-detail-clinical');
        if (d.clinicalRawId && d.clinicalRawId !== '' && d.clinicalRawId !== 'null') {
            btnDetailClin.href = '/analysis/result/' + d.clinicalRawId;
            btnDetailClin.classList.remove('hidden');
        } else {
            btnDetailClin.classList.add('hidden');
        }

        try {
            renderFiles(JSON.parse(d.clinicalFiles || '[]'), document.getElementById('mc-files'), false);
        } catch(e) {}
    } else {
        secClin.classList.add('hidden');
    }

    // Image
    const hasImage = d.imageId && d.imageId !== '';
    const secImg = document.getElementById('section-image');
    if (hasImage) {
        secImg.classList.remove('hidden');
        document.getElementById('modal-image-id').innerText              = d.imageId;
        document.getElementById('mi-type').innerText                     = d.imageType || '—';
        document.getElementById('mi-bodypart').innerText                 = d.imageBodyPart || '—';
        document.getElementById('mi-prompt').innerText                   = d.imagePrompt || '—';
        document.getElementById('modal-image-diagnosis').innerText       = d.imageDiagnosis2 || '—';
        document.getElementById('modal-image-risk').innerHTML            = riskBadge(d.imageRisk);
        document.getElementById('modal-image-confidence').innerText      = d.imageConfidence ? d.imageConfidence + '%' : '—';
        document.getElementById('modal-image-penjelasan').innerText      = d.imagePenjelasan || '—';
        document.getElementById('modal-image-recommendation').innerText  = d.imageRecommendation || '—';
        document.getElementById('modal-image-medication').innerText      = d.imageMedication || 'Tidak ada rekomendasi obat dari AI.';
        document.getElementById('modal-image-validation').innerHTML      = validationBadge(d.imageValidation);
        document.getElementById('modal-image-doctor-risk').innerHTML     = riskBadge(d.imageDoctorRisk);

        const miRow = document.getElementById('mi-doctor-diagnosis-row');
        if (d.imageDoctorDiagnosis && d.imageDoctorDiagnosis !== '' && d.imageDoctorDiagnosis !== 'null') {
            miRow.classList.remove('hidden');
            document.getElementById('modal-image-doctor-diagnosis').innerText = d.imageDoctorDiagnosis;
        } else {
            miRow.classList.add('hidden');
        }

        document.getElementById('modal-image-analisis').innerText = (d.imageAnalisis && d.imageAnalisis !== 'null' && d.imageAnalisis !== '')
            ? d.imageAnalisis : 'Belum diisi oleh dokter.';

        document.getElementById('modal-image-doctor-medication').innerText = (d.imageMedicationDoctor && d.imageMedicationDoctor !== 'null' && d.imageMedicationDoctor !== '')
            ? d.imageMedicationDoctor : 'Belum diisi oleh dokter.';

        const miBanner = document.getElementById('mi-correlation-banner');
        if (d.imageNeedsClinical === '1' && d.combinedStatus !== 'done') {
            miBanner.classList.remove('hidden');
            document.getElementById('mi-correlation-reason').innerText = d.imageClinicalReason || '-';
        } else {
            miBanner.classList.add('hidden');
        }

        const btnDetailImg = document.getElementById('btn-detail-image');
        if (d.imageRawId && d.imageRawId !== '' && d.imageRawId !== 'null') {
            btnDetailImg.href = '/analysis-picture/result/' + d.imageRawId;
            btnDetailImg.classList.remove('hidden');
        } else {
            btnDetailImg.classList.add('hidden');
        }

        try {
            renderFiles(JSON.parse(d.imageFiles || '[]'), document.getElementById('mi-files'), true);
            renderFiles(JSON.parse(d.imageResultImages || '[]'), document.getElementById('mi-result-images'), true);
        } catch(e) {}
    } else {
        secImg.classList.add('hidden');
    }

    // Combined
    const hasCombined = d.combinedStatus && d.combinedStatus !== 'none' && d.combinedStatus !== 'failed' && d.combinedStatus !== '';
    const secCombined = document.getElementById('section-combined');
    if (hasCombined) {
        secCombined.classList.remove('hidden');
        const statusMap = {
            'done'   : '<span class="px-2 py-0.5 bg-teal-100 text-teal-700 rounded-full text-[10px] font-bold">Selesai</span>',
            'pending': '<span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold">⏳ Diproses</span>',
        };
        document.getElementById('modal-combined-status').innerHTML         = statusMap[d.combinedStatus] || '';
        document.getElementById('modal-combined-diagnosis').innerText      = d.combinedDiagnosis || '—';
        document.getElementById('modal-combined-risk').innerHTML           = riskBadge(d.combinedRisk);
        document.getElementById('modal-combined-confidence').innerText     = d.combinedConfidence ? d.combinedConfidence + '%' : '—';
        document.getElementById('modal-combined-summary').innerText        = d.combinedSummary || '—';
        document.getElementById('modal-combined-correlation').innerText    = d.combinedCorrelation || '—';
        document.getElementById('modal-combined-recommendation').innerText = d.combinedRecommendation || '—';
        document.getElementById('modal-combined-validation').innerHTML     = validationBadge(d.combinedValidation);

        const btnDetailCombined = document.getElementById('btn-detail-combined');
        if (d.combinedId && d.combinedId !== '' && d.combinedId !== 'null' && d.combinedStatus === 'done') {
            btnDetailCombined.href = '/analysis-combined/' + d.combinedId;
            btnDetailCombined.classList.remove('hidden');
        } else {
            btnDetailCombined.classList.add('hidden');
        }
    } else {
        secCombined.classList.add('hidden');
    }

    document.getElementById('detail-modal').classList.remove('hidden');
    setTimeout(() => document.getElementById('modal-card').classList.replace('scale-95', 'scale-100'), 10);
}

function closeDetailModal() {
    document.getElementById('modal-card').classList.replace('scale-100', 'scale-95');
    setTimeout(() => document.getElementById('detail-modal').classList.add('hidden'), 150);
}

// Tombol "Validasi" di tabel: arahkan langsung ke halaman result yang tepat
// (gabungan kalau sudah done, kalau belum prioritaskan yang masih pending)
function goToValidation(btn) {
    const d = btn.closest('tr').dataset;

    if (d.combinedStatus === 'done' && d.combinedId) {
        window.location.href = '/analysis-combined/' + d.combinedId;
        return;
    }

    const clinicalPending = d.clinicalRawId && d.clinicalValidation === 'pending';
    const imagePending    = d.imageRawId && d.imageValidation === 'pending';

    if (clinicalPending) {
        window.location.href = '/analysis/result/' + d.clinicalRawId;
    } else if (imagePending) {
        window.location.href = '/analysis-picture/result/' + d.imageRawId;
    } else if (d.clinicalRawId) {
        window.location.href = '/analysis/result/' + d.clinicalRawId;
    } else if (d.imageRawId) {
        window.location.href = '/analysis-picture/result/' + d.imageRawId;
    }
}

window.addEventListener('click', function(e) {
    if (e.target === document.getElementById('detail-modal')) closeDetailModal();
});
</script>
@endsection