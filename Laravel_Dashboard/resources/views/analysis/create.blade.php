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

<div class="space-y-6 max-w-5xl mx-auto">

    <!-- BAGIAN HEADER -->
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">
            Ruang Kerja Analisis AI
        </h1>
        <p class="text-sm font-medium text-slate-500 mt-0.5">
            Buat kasus analisis klinis baru yang komprehensif dengan bantuan arsitektur AI medis.
        </p>
    </div>

    {{-- BLOK ERROR VALIDASI / ERROR AI --}}
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-lg p-3">
            <p class="font-bold uppercase tracking-wide mb-1">Terjadi Kesalahan</p>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- KARTU FORM -->
    <div class="bg-white rounded-xl p-6 border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)]">
        <form action="{{ route('analysis.run') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- BARIS 1: PASIEN & TINGKATAN TEMPLATE (+ DESKRIPSI DI BAWAHNYA) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- KOLOM KIRI: DROPDOWN PASIEN (Tom Select) -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Pasien <span class="text-rose-500">*</span>
                    </label>
                    <select name="patient_id" id="patient-select" required>
                        <option value="">Cari ID atau nama pasien...</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}"
                                    data-id="{{ $patient->id_pasien }}"
                                    data-nama="{{ $patient->nama_pasien }}"
                                    @selected($selectedPatientId == $patient->id)>
                                {{ $patient->id_pasien }} — {{ $patient->nama_pasien }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- KOLOM KANAN: TINGKATAN TEMPLATE + DESKRIPSI (normal flow, mendorong konten di bawahnya) -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Tingkatan Template <span class="text-rose-500">*</span>
                    </label>

                    <select name="template_level" id="template_level_select" required
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-colors">
                        <option value="" disabled selected>-- Pilih Tingkatan --</option>
                        <option value="Basic">Dasar (Basic)</option>
                        <option value="Advanced">Menengah (Advanced)</option>
                        <option value="Expert">Ahli (Expert)</option>
                    </select>

                    <!-- DESKRIPSI: normal flow, muncul di bawah dropdown -->
                    <div id="template_level_desc"
                        class="hidden mt-3 p-3 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-700 leading-relaxed">
                    </div>
                </div>

            </div>

            <!-- BAGIAN TANDA VITAL -->
            <div class="bg-slate-50/50 rounded-xl p-4 border border-slate-100 space-y-3">
                <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Tanda Vital (Vital Signs)
                </span>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[11px] font-medium text-slate-500 mb-1">Suhu Tubuh (Body Temperature)</label>
                        <div class="relative flex items-center">
                            <input type="text" name="vital_signs[body_temperature]" placeholder="36.5"
                                class="w-full text-xs text-slate-700 bg-white border border-slate-200 rounded-lg pl-3 pr-8 py-2 focus:outline-none focus:border-blue-500 transition-colors">
                            <span class="absolute right-3 text-[10px] font-medium text-slate-400">°C</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-slate-500 mb-1">Denyut Nadi (Heart Rate)</label>
                        <div class="relative flex items-center">
                            <input type="text" name="vital_signs[heart_rate]" placeholder="75"
                                class="w-full text-xs text-slate-700 bg-white border border-slate-200 rounded-lg pl-3 pr-10 py-2 focus:outline-none focus:border-blue-500 transition-colors">
                            <span class="absolute right-3 text-[10px] font-medium text-slate-400">bpm</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-slate-500 mb-1">Laju Pernapasan (Respiratory Rate)</label>
                        <div class="relative flex items-center">
                            <input type="text" name="vital_signs[respiratory_rate]" placeholder="18"
                                class="w-full text-xs text-slate-700 bg-white border border-slate-200 rounded-lg pl-3 pr-10 py-2 focus:outline-none focus:border-blue-500 transition-colors">
                            <span class="absolute right-3 text-[10px] font-medium text-slate-400">rpm</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-slate-500 mb-1">Tekanan Darah (Blood Pressure)</label>
                        <div class="relative flex items-center">
                            <input type="text" name="vital_signs[blood_pressure]" placeholder="120/80"
                                class="w-full text-xs text-slate-700 bg-white border border-slate-200 rounded-lg pl-3 pr-14 py-2 focus:outline-none focus:border-blue-500 transition-colors">
                            <span class="absolute right-3 text-[10px] font-medium text-slate-400">mmHg</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIWAYAT MEDIS & ALERGI -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Riwayat Penyakit (Medical History)</label>
                    <textarea rows="3" name="medical_history" placeholder="Contoh: Hipertensi sejak 2021, Diabetes Tipe 2..."
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg p-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-colors placeholder:text-slate-300"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Alergi (Allergies)</label>
                    <textarea rows="3" name="allergies" placeholder="Contoh: Penisilin, Makanan Laut, NKDA..."
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg p-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-colors placeholder:text-slate-300"></textarea>
                </div>
            </div>

            <!-- GEJALA & CATATAN DOKTER -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Gejala Aktual (Symptoms)</label>
                    <textarea rows="4" name="symptoms" placeholder="Jelaskan gejala yang dirasakan pasien saat ini..."
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg p-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-colors placeholder:text-slate-300"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Catatan Dokter (Doctor Notes)</label>
                    <textarea rows="4" name="doctor_notes" placeholder="Masukkan observasi klinis awal atau panduan dokter..."
                        class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg p-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-colors placeholder:text-slate-300"></textarea>
                </div>
            </div>

            <!-- INFORMASI LAINNYA -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Informasi Lainnya (Other Info)</label>
                <textarea rows="2" name="other_info" placeholder="Konteks tambahan (misalnya riwayat keluarga, riwayat perjalanan terbaru)..."
                    class="w-full text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg p-2.5 focus:outline-none focus:border-blue-500 focus:bg-white transition-colors placeholder:text-slate-300"></textarea>
            </div>

            <!-- UNGGAH BERKAS -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                    Unggah Berkas Medis (Bisa memilih lebih dari 1 file)
                </label>
                <div class="mt-1 flex justify-center px-6 pt-4 pb-5 border-2 border-slate-200 border-dashed rounded-lg bg-slate-50/50 hover:bg-slate-50 transition-colors">
                    <div class="space-y-1 text-center">
                        <i class="bi bi-cloud-arrow-up text-3xl text-slate-400 block mb-1"></i>
                        <div class="flex items-center justify-center text-xs text-slate-600">
                            <label for="medical_files" class="relative cursor-pointer rounded-md font-semibold text-blue-600 hover:text-blue-500 bg-slate-100/50 dark:bg-slate-800/80 px-3 py-1.5 transition-colors">
                                <span>Unggah berkas</span>
                                <input id="medical_files" name="medical_files[]" type="file" class="hidden" multiple>
                            </label>
                            <p class="pl-3">atau seret dan lepas di sini</p>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-2">Data DICOM, Gambar, PDF, atau EKG hingga maksimal 10MB per file</p>
                        <div id="file-list-preview" class="text-[11px] text-emerald-600 font-medium mt-2 space-y-0.5"></div>
                    </div>
                </div>
            </div>

            <!-- TOMBOL AKSI -->
            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-xs font-bold text-white shadow-sm hover:from-blue-700 hover:to-indigo-700 transition-all transform active:scale-[0.98]">
                    <i class="bi bi-cpu"></i> Jalankan Analisis AI
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Tom Select JS --}}
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<script>
    // ── Tom Select: Dropdown Pasien ──────────────────────────────────────
    const patientSelect = new TomSelect('#patient-select', {
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
                            <span class="w-[70px] text-center text-[10px] font-bold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded inline-block">${id}</span>
                            <span class="text-xs text-slate-700">${nama}</span>
                        </div>`;
            },
            item: function(data, escape) {
                const parts = escape(data.text).split(' — ');
                const id    = parts[0] || '';
                const nama  = parts[1] || '';
                return `<div class="flex items-center gap-1.5">
                           <span class="w-[70px] text-center text-[10px] font-bold text-slate-400 inline-block">${id}</span>
                            <span class="text-xs text-slate-700">${nama}</span>
                        </div>`;
            },
            no_results: function() {
                return `<div class="px-3 py-2 text-xs text-slate-400">Pasien tidak ditemukan.</div>`;
            }
        }
    });

    @if($selectedPatientId)
        patientSelect.setValue('{{ $selectedPatientId }}');
    @endif

    // ── Deskripsi Tingkatan Template ──────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        const levelSelect = document.getElementById('template_level_select');
        const levelDesc    = document.getElementById('template_level_desc');

        const levelDescriptions = {
            'Basic': '<i class="bi bi-lightning-charge mr-1"></i> <strong>Dasar:</strong> Respon tercepat untuk identifikasi dasar.',
            'Advanced': '<i class="bi bi-bar-chart-steps mr-1"></i> <strong>Menengah:</strong> Keseimbangan optimal antara kecepatan dan detail.',
            'Expert': '<i class="bi bi-gem mr-1"></i> <strong>Ahli:</strong> Analisis paling mendetail dan akurasi tertinggi.'
        };

        levelSelect.addEventListener('change', function() {
            if (this.value && levelDescriptions[this.value]) {
                levelDesc.innerHTML = levelDescriptions[this.value];
                levelDesc.classList.remove('hidden');
            } else {
                levelDesc.classList.add('hidden');
            }
        });
    });

    // ── Preview File Upload ──────────────────────────────────────────────
    document.getElementById('medical_files').addEventListener('change', function() {
        const preview = document.getElementById('file-list-preview');
        preview.innerHTML = this.files.length > 0
            ? `<i class="bi bi-check-circle-fill"></i> ${this.files.length} berkas terpilih: `
              + Array.from(this.files).map(f => f.name).join(', ')
            : '';
    });
</script>

@endsection