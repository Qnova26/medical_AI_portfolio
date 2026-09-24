@extends('layouts.dashboard')

@section('content')

@if(session('success'))
<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 4000)"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="-translate-y-5 opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-500"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed top-6 right-6 z-50 bg-emerald-600 text-white px-5 py-3.5 rounded-xl shadow-xl flex items-center gap-3 border border-emerald-500/20">
    <i class="bi bi-check-circle-fill text-lg"></i>
    <span class="text-xs font-bold tracking-wide">{{ session('success') }}</span>
</div>
@endif

<div class="space-y-6 max-w-7xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight dark:text-black">
                Templat Prompt LLM
            </h1>
            <p class="text-sm font-medium text-slate-500 mt-0.5">
                Manajemen dan rekayasa instruksi kecerdasan artifisial untuk sektor rekam medis.
            </p>
        </div>
        
        <a href="{{ route('prompts.create') }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-xs font-bold text-white shadow-md hover:from-blue-700 hover:to-indigo-700 transition-all transform active:scale-[0.98]">
            <i class="bi bi-plus-lg text-sm"></i> Templat Baru
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-100 shadow-[0_8px_30px_rgb(15,23,42,0.02)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-5">Nama Templat</th>
                        <th class="py-3 px-4">Kategori Klinis</th>
                        <th class="py-3 px-4">Tingkat Spekulasi</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-5 text-center">Aksi Manajemen</th>
                    </tr>
                </thead>
                
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @foreach($prompts as $prompt)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        
                        <td class="py-4 px-5">
                            <div class="font-semibold text-slate-800">{{ $prompt->name }}</div>
                        </td>

                        <td class="py-4 px-4">
                            <span class="px-2 py-1 bg-slate-100 rounded text-[11px] font-semibold text-slate-700">
                                @if($prompt->category == 'Radiology')
                                    Radiologi
                                @elseif($prompt->category == 'Clinical')
                                    Klinis
                                @else
                                    {{ $prompt->category }}
                                @endif
                            </span>
                        </td>

                        <td class="py-4 px-4 font-medium text-slate-600">
                            @if($prompt->level == 'Expert')
                                Ahli (Expert)
                            @elseif($prompt->level == 'Advanced')
                                Mahir (Advanced)
                            </td>
                            @else
                                {{ $prompt->level }}
                            @endif
                        </td>

                        <td class="py-4 px-4 text-center">
                            @if($prompt->status == 'Active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-slate-50 text-slate-600 border border-slate-100">
                                    Nonaktif
                                </span>
                            @endif
                        </td>

                        <td class="py-4 px-5">
                            <div class="flex items-center justify-center gap-1.5">
                                
                                <a href="{{ route('prompts.show', $prompt['id']) }}" title="Lihat Detail Prompt"
                                    class="p-1.5 rounded-md border border-slate-200 bg-white text-slate-600 hover:text-cyan-600 hover:border-cyan-200 hover:bg-cyan-50/30 transition-all shadow-sm">
                                    <i class="bi bi-eye text-sm"></i>
                                </a>

                                <a href="{{ route('prompts.edit', $prompt['id']) }}" title="Ubah Konstruksi Prompt"
                                    class="p-1.5 rounded-md border border-slate-200 bg-white text-slate-600 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50/30 transition-all shadow-sm">
                                    <i class="bi bi-pencil text-sm"></i>
                                </a>

                                <form action="{{ route('prompts.destroy', $prompt['id']) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus templat prompt ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus Templat"
                                        class="p-1.5 rounded-md border border-slate-200 bg-white text-slate-600 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50/30 transition-all shadow-sm">
                                        <i class="bi bi-trash text-sm"></i>
                                    </button>
                                </form>
                                
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection