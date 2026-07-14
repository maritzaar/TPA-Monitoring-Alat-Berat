@extends('layouts.app')

@section('title', __('Detail Aset') . ' - ' . ($alat->id_aset ?? ''))

@section('content')
<div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6 shadow-sm">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800 flex items-center">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                {{ __('Detail Aset') }}: {{ $alat->id_aset ?? __('Tidak Diketahui') }}
            </h2>
            @if($alat)
                <p class="text-slate-500 text-sm mt-1">
                    <span class="font-semibold">{{ __('Model') }}:</span> {{ $alat->model ?? '-' }} |
                    <span class="font-semibold">{{ __('Nomor Seri') }}:</span> {{ $alat->nomor_seri ?? '-' }}
                </p>
            @endif
        </div>
        <a href="{{ route('monitoring.working_hour') }}?start_date={{ $start_date }}&end_date={{ $end_date }}"
           class="bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 transition text-sm font-semibold shadow-sm inline-flex items-center justify-center">
            <i class="fas fa-arrow-left mr-2"></i> {{ __('Kembali') }}
        </a>
    </div>

    <!-- Informasi Periode -->
    <div class="bg-blue-50/55 border border-blue-100 p-4 rounded-lg mb-6 flex items-center space-x-2">
        <i class="fas fa-calendar-alt text-blue-650"></i>
        <p class="text-sm text-slate-700">
            {{ __('Data untuk periode:') }} <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($start_date)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($end_date)->translatedFormat('d M Y') }}</span>
        </p>
    </div>

    <!-- Tabel Data -->
    <div class="overflow-x-auto -mx-4 sm:mx-0 table-scroll">
        <table class="min-w-full divide-y divide-slate-200 border border-slate-100 rounded-lg overflow-hidden">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">{{ __('Tanggal') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">{{ __('Sumber') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">{{ __('Waktu Kerja') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">{{ __('Waktu Operasi') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">{{ __('Waktu Idle') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">{{ __('% Idle') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                @forelse($data as $item)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-4 py-3 text-sm text-slate-650 font-medium whitespace-nowrap">{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2.5 py-0.5 text-xs rounded-full font-semibold border
                            @if($item->sumber_data == 'CATERPILLAR') bg-blue-50 text-blue-750 border-blue-100
                            @elseif($item->sumber_data == 'INTERNAL') bg-emerald-50 text-emerald-800 border-emerald-100
                            @else bg-slate-100 text-slate-700 border-slate-200 @endif">
                            {{ $item->sumber_data }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-700 font-semibold whitespace-nowrap">{{ number_format($item->waktu_kerja, 2) }}</td>
                    <td class="px-4 py-3 text-sm text-slate-700 font-semibold whitespace-nowrap">{{ number_format($item->waktu_operasi, 2) }}</td>
                    <td class="px-4 py-3 text-sm text-slate-700 font-semibold whitespace-nowrap">{{ number_format($item->waktu_idle, 2) }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold border whitespace-nowrap
                            @if(($item->persen_idle ?? 0) < 30) bg-emerald-50 text-emerald-800 border-emerald-100
                            @elseif(($item->persen_idle ?? 0) < 50) bg-amber-50 text-amber-800 border-amber-100
                            @else bg-rose-50 text-rose-800 border-rose-100 @endif">
                            {{ number_format($item->persen_idle ?? 0, 1) }}%
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-10 text-center text-slate-450">
                        <span class="text-sm">{{ __('Tidak ada data untuk aset ini pada periode ') . \Carbon\Carbon::parse($start_date)->translatedFormat('d M Y') . ' s/d ' . \Carbon\Carbon::parse($end_date)->translatedFormat('d M Y') }}</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Summary Footer -->
    @if($data->count() > 0)
    <div class="mt-6 bg-slate-50 border border-slate-100 rounded-xl p-4 transition-colors duration-200">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-slate-450 font-bold uppercase tracking-wider">{{ __('Total Hari') }}</p>
                <p class="text-lg font-bold text-slate-800 mt-1">{{ $data->count() }} {{ app()->getLocale() == 'en' ? 'days' : 'hari' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-450 font-bold uppercase tracking-wider">{{ __('Rata-rata Waktu Kerja') }}</p>
                <p class="text-lg font-bold text-slate-800 mt-1">{{ number_format($data->avg('waktu_kerja') ?? 0, 1) }} {{ app()->getLocale() == 'en' ? 'hours' : 'jam' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-450 font-bold uppercase tracking-wider">{{ __('Rata-rata % Idle') }}</p>
                <p class="text-lg font-bold text-slate-800 mt-1">{{ number_format($data->avg('persen_idle') ?? 0, 1) }}%</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection