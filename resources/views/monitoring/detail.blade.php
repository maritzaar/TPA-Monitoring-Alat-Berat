@extends('layouts.app')

@section('title', __('Detail Aset') . ' - ' . ($alat->id_aset ?? ''))

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-info-circle text-[#8E6E4F] mr-2"></i>
                {{ __('Detail Aset') }}: {{ $alat->id_aset ?? 'Tidak Diketahui' }}
            </h2>
            @if($alat)
                <p class="text-gray-600 mt-1">
                    <span class="font-semibold">Model:</span> {{ $alat->model ?? '-' }} |
                    <span class="font-semibold">Serial:</span> {{ $alat->nomor_seri ?? '-' }}
                </p>
            @endif
        </div>
        <a href="{{ route('monitoring.index') }}?bulan={{ $bulan }}&tahun={{ $tahun }}"
           class="bg-[#A7825E] text-white px-4 py-2 rounded-lg hover:bg-[#8E6E4F] transition text-sm font-semibold shadow-sm">
            <i class="fas fa-arrow-left mr-2"></i> {{ __('Kembali') }}
        </a>
    </div>

    <!-- Informasi Periode -->
    <div class="bg-[#FAF7F2] border border-[#E6DCCF] p-4 rounded-lg mb-6 shadow-sm">
        <p class="text-sm text-gray-700">
            <i class="fas fa-calendar-alt text-[#8E6E4F] mr-2"></i>
            {{ __('Data untuk periode:') }} <span class="font-semibold">{{ __($bulan) }} {{ $tahun }}</span>
        </p>
    </div>

    <!-- Tabel Data -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Tanggal') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Sumber') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Waktu Kerja') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Waktu Operasi') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Waktu Idle') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('% Idle') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Bahan Bakar') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Laju Bakar') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($data as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full font-semibold
                            @if($item->sumber_data == 'CATERPILLAR') bg-[#F5EBE0] text-[#704F37] border border-[#E6DCCF]
                            @elseif($item->sumber_data == 'INTERNAL') bg-emerald-50 text-emerald-800 border border-emerald-100
                            @else bg-stone-100 text-stone-800 border border-stone-200 @endif">
                            {{ $item->sumber_data }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm">{{ number_format($item->waktu_kerja, 2) }}</td>
                    <td class="px-4 py-3 text-sm">{{ number_format($item->waktu_operasi, 2) }}</td>
                    <td class="px-4 py-3 text-sm">{{ number_format($item->waktu_idle, 2) }}</td>
                    <td class="px-4 py-3 text-sm">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            @if(($item->persen_idle ?? 0) < 30) bg-[#E2F7E9] text-emerald-800
                            @elseif(($item->persen_idle ?? 0) < 50) bg-[#FFF6E2] text-amber-800
                            @else bg-rose-50 text-rose-800 @endif">
                            {{ number_format($item->persen_idle ?? 0, 1) }}%
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm">{{ number_format($item->total_bahan_bakar ?? 0, 0) }}</td>
                    <td class="px-4 py-3 text-sm">{{ number_format($item->laju_bakar ?? 0, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-2xl block mb-2"></i>
                        {{ __('Tidak ada data untuk aset ini pada periode :bulan :tahun.', ['bulan' => __($bulan), 'tahun' => $tahun]) }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Summary Footer -->
    @if($data->count() > 0)
    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-gray-500">{{ __('Total Hari') }}</p>
                <p class="text-lg font-semibold">{{ $data->count() }} @if(app()->getLocale() == 'en') days @else hari @endif</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ __('Rata-rata Waktu Kerja') }}</p>
                <p class="text-lg font-semibold">{{ number_format($data->avg('waktu_kerja') ?? 0, 1) }} @if(app()->getLocale() == 'en') hours @else jam @endif</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ __('Rata-rata % Idle') }}</p>
                <p class="text-lg font-semibold">{{ number_format($data->avg('persen_idle') ?? 0, 1) }}%</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ __('Total Bahan Bakar') }}</p>
                <p class="text-lg font-semibold">{{ number_format($data->sum('total_bahan_bakar') ?? 0, 0) }} L</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection