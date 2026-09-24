@extends('layouts.dashboard')

@section('content')

{{-- Tom Select CSS --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css" rel="stylesheet">

<style>
    /* CSS TOM SELECT DEFAULT (LIGHT MODE) */
    .ts-wrapper.single .ts-control {
        background-color: rgb(248 250 252);
        border: 1px solid rgb(226 232 240);
        border-radius: 0.5rem;
        padding: 0.625rem 0.75rem;
        font-size: 0.75rem;
        color: rgb(51 65 85);
        box-shadow: none;
        cursor: text;
    }
    .ts-wrapper.single.focus .ts-control {
        border-color: rgb(59 130 246);
        background-color: white;
    }
    .ts-dropdown {
        border: 1px solid rgb(226 232 240);
        border-radius: 0.5rem;
        box-shadow: 0 4px 20px rgba(15,23,42,0.08);
        font-size: 0.75rem;
    }
    .ts-dropdown .ts-dropdown-content .option {
        padding: 0;
    }
    .ts-dropdown .ts-dropdown-content .option.active {
        background-color: rgb(239 246 255);
        color: inherit;
    }
    .ts-wrapper .ts-control input {
        font-size: 0.75rem;
        color: rgb(51 65 85);
    }

    /* CSS TOM SELECT (DARK MODE) */
    .dark .ts-wrapper.single .ts-control {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #f1f5f9 !important;
    }
    .dark .ts-wrapper.single.focus .ts-control {
        background-color: #0f172a !important;
    }
    .dark .ts-dropdown {
        background-color: #1e293b !important;
        border-color: #334155 !important;
        color: #f1f5f9 !important;
    }
    .dark .ts-dropdown .ts-dropdown-content .option.active {
        background-color: #334155 !important;
        color: #f1f5f9 !important;
    }
    .dark .ts-wrapper .ts-control input {
        color: #f1f5f9 !important;
    }
</style>

<div class="max-w-4xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Analisis Citra Medis Baru</h1>
        <p class="text-sm text-slate-500">Unggah berkas citra medis dan pilih model prompt AI untuk proses diagnosis.</p>
    </div>

    @if ($errors->any())
        <div class="p-4 mb-4 text-sm text-rose-700 bg-rose-50 rounded-xl border border-rose-200">
            <div class="font-bold mb-1">Terjadi kesalahan:</div>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('analysis-picture.run') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- PASIEN & BAGIAN TUBUH --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- DROPDOWN PASIEN (Tom Select, sama seperti Analisis Klinis) --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Pasien <span class="text-rose-500">*</span>
                    </label>
                    <select name="patient_id" id="patient-select" required>
                        <option value="">Cari ID atau nama pasien...</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}"
                                    data-id="{{ $patient->id_pasien }}"
                                    data-nama="{{ $patient->nama_pasien }}">
                                {{ $patient->id_pasien }} — {{ $patient->nama_pasien }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Bagian Tubuh
                    </label>
                    <input type="text" name="body_part" placeholder="cth: Thorax, Kepala..."
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500 transition-colors">
                </div>
            </div>
        </div>

        {{-- AREA UPLOAD FILE --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <label class="block text-sm font-bold text-slate-700 mb-3">Upload Berkas Medis</label>
            <div class="border-2 border-dashed border-slate-300 rounded-xl p-10 text-center hover:border-blue-500 transition-colors">
                <input type="file" name="image_files[]" id="file-upload" class="hidden" multiple
                       accept="image/*,.dcm">
                <label for="file-upload" class="cursor-pointer">
                    <i class="bi bi-cloud-arrow-up text-4xl text-slate-400"></i>
                    <p class="text-sm text-slate-600 mt-2">Klik untuk upload atau drag & drop</p>
                    <p class="text-[10px] text-slate-400 mt-1">PNG, JPG, DICOM (Max 10MB)</p>
                </label>
                <div id="file-list-preview" class="text-[11px] text-emerald-600 font-medium mt-2"></div>
            </div>
        </div>

        {{-- TEMPLATE PROMPT --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">
                    Pilih Kategori Pemeriksaan <span class="text-rose-500">*</span>
                </label>
                <select name="category" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="" disabled selected>-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">
                    Tingkatan Analisis <span class="text-rose-500">*</span>
                </label>
                <select name="template_level" id="template_level_select" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="" disabled selected>-- Pilih Tingkatan --</option>
                    <option value="Basic">Dasar (Basic)</option>
                    <option value="Advanced">Menengah (Advanced)</option>
                    <option value="Expert">Ahli (Expert)</option>
                </select>
                <div id="template_level_desc" class="hidden mt-3 p-3 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-700 leading-relaxed">
                </div>
            </div>
            <div class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                <p class="text-[11px] text-blue-700 leading-relaxed">
                    <i class="bi bi-info-circle mr-1"></i>
                    <strong>Catatan:</strong> Model dan tingkatan yang dipilih akan menentukan fokus serta kedalaman ekstraksi fitur visual pada citra medis.
                </p>
            </div>
        </div>

        {{-- CATATAN DOKTER --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <label class="block text-sm font-bold text-slate-700 mb-2">Catatan Dokter (Opsional)</label>
            <textarea name="doctor_notes" rows="3" placeholder="Observasi klinis awal atau panduan interpretasi..."
                class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg p-2.5 focus:outline-none focus:border-blue-500 transition-colors"></textarea>
        </div>

        {{-- TOMBOL AKSI --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('analysis-picture.create') }}"
               class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition-all">
                Batal
            </a>
            <button type="submit"
                class="px-6 py-2.5 rounded-xl text-sm font-bold bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition-all">
                <i class="bi bi-cpu mr-2"></i> Jalankan Analisis AI
            </button>
        </div>
    </form>
</div>

{{-- Tom Select JS --}}
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>
    // ── Tom Select: Dropdown Pasien ──────────────────────────────────────
    new TomSelect('#patient-select', {
        valueField: 'value',
        labelField: 'text',
        searchField: ['text'],
        placeholder: 'Cari ID atau nama pasien...',
        maxOptions: 10,
        render: {
            option: function(data, escape) {
                const parts = escape(data.text).split(' — ');
                const id    = parts[0] || '';
                const nama  = parts[1] || '';
                return `<div class="flex items-center gap-2 px-3 py-2 hover:bg-slate-50">
                            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded min-w-[56px] text-center inline-block flex-shrink-0">${id}</span>
                            <span class="text-xs text-slate-700">${nama}</span>
                        </div>`;
            },
            item: function(data, escape) {
                const parts = escape(data.text).split(' — ');
                const id    = parts[0] || '';
                const nama  = parts[1] || '';
                return `<div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-bold text-slate-400 min-w-[40px] inline-block flex-shrink-0">${id}</span>
                            <span class="text-xs text-slate-700">${nama}</span>
                        </div>`;
            },
            no_results: function() {
                return `<div class="px-3 py-2 text-xs text-slate-400">Pasien tidak ditemukan.</div>`;
            }
        }
    });

    // ── Preview File Upload ──────────────────────────────────────────────
    document.getElementById('file-upload').addEventListener('change', function() {
        const preview = document.getElementById('file-list-preview');
        preview.innerHTML = this.files.length > 0
            ? `<i class="bi bi-check-circle-fill"></i> ${this.files.length} berkas terpilih: `
              + Array.from(this.files).map(f => f.name).join(', ')
            : '';
    });

    // ── Deskripsi Tingkatan Template ──────────────────────────────────────
    const levelSelect = document.getElementById('template_level_select');
    const levelDesc = document.getElementById('template_level_desc');
    const levelDescriptions = {
        'Basic': '<i class="bi bi-lightning-charge mr-1"></i> <strong>Dasar:</strong> Respon tercepat untuk identifikasi dasar.',
        'Advanced': '<i class="bi bi-bar-chart-steps mr-1"></i> <strong>Menengah:</strong> Keseimbangan optimal antara kecepatan dan detail.',
        'Expert': '<i class="bi bi-gem mr-1"></i> <strong>Ahli:</strong> Analisis paling mendetail dan akurasi tertinggi.'
    };

    if (levelSelect) {
        levelSelect.addEventListener('change', function() {
            if (this.value && levelDescriptions[this.value]) {
                levelDesc.innerHTML = levelDescriptions[this.value];
                levelDesc.classList.remove('hidden');
            } else {
                levelDesc.classList.add('hidden');
            }
        });
    }
</script>
@endsection