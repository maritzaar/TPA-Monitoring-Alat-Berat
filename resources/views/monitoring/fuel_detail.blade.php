@extends('layouts.app')

@section('title', __('Detail Bahan Bakar') . ' - ' . ($alat->unit_code ?? ''))

@section('content')
<div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6 shadow-sm">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800 flex items-center">
                <i class="fas fa-gas-pump text-emerald-600 mr-2"></i>
                {{ __('Detail Bahan Bakar') }}: {{ $alat->unit_code ?? __('Tidak Diketahui') }}
            </h2>
            @if($alat)
                <p class="text-slate-500 text-sm mt-1">
                    <span class="font-semibold">{{ __('Nomor Polisi/Unit') }}:</span> {{ $alat->police_number ?? '-' }} |
                    <span class="font-semibold">{{ __('Grup') }}:</span> {{ $alat->group_aset ?? '-' }}
                </p>
            @endif
        </div>
        <a href="{{ route('monitoring.fuel') }}?bulan={{ $bulan }}&tahun={{ $tahun }}"
           class="bg-emerald-600 text-white px-4 py-2.5 rounded-lg hover:bg-emerald-700 transition text-sm font-semibold shadow-sm inline-flex items-center justify-center">
            <i class="fas fa-arrow-left mr-2"></i> {{ __('Kembali') }}
        </a>
    </div>

    <!-- Informasi Periode -->
    <div class="bg-emerald-50/55 border border-emerald-100 p-4 rounded-lg mb-6 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center space-x-2">
            <i class="fas fa-calendar-alt text-emerald-650"></i>
            <p class="text-sm text-slate-700">
                {{ __('Data untuk periode:') }} <span class="font-bold text-slate-800">{{ __($bulan) }} {{ $tahun }}</span>
            </p>
        </div>
        
        <!-- Search Input -->
        <div class="relative w-full sm:w-64">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-450">
                <i class="fas fa-search text-xs"></i>
            </div>
            <input type="text" id="internalOrderSearchInput" placeholder="Cari Internal Order..."
                   class="pl-8 pr-3 py-1.5 w-full border border-slate-300 rounded-lg text-sm bg-slate-50 text-slate-800 placeholder-slate-400 focus:ring-emerald-600 focus:border-emerald-600 focus:outline-none transition-all">
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="overflow-x-auto -mx-4 sm:mx-0 table-scroll">
        <table class="min-w-full divide-y divide-slate-200 border border-slate-100 rounded-lg overflow-hidden">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">{{ __('Tanggal') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">{{ __('Internal Order') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">{{ __('Cost Center') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Volume (L)</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">{{ __('Harga') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">{{ __('Lokasi Pengisian') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                @forelse($data as $item)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-4 py-3 text-sm text-slate-655 font-medium whitespace-nowrap">{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="px-4 py-3 text-sm text-slate-700 font-semibold whitespace-nowrap">{{ $item->internal_order ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-slate-700 font-semibold whitespace-nowrap">{{ $item->cost_center ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-emerald-600 font-bold whitespace-nowrap">{{ number_format($item->total_quantity, 0) }}</td>
                    <td class="px-4 py-3 text-sm text-slate-700 font-semibold whitespace-nowrap">{{ number_format($item->amount_price, 0) }}</td>
                    <td class="px-4 py-3 text-sm text-slate-700 whitespace-nowrap">{{ $item->loc_filling ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-slate-450">
                        <i class="fas fa-inbox text-3xl block mb-2 text-slate-350"></i>
                        <span class="text-sm">{{ __('Tidak ada data untuk aset ini pada periode :bulan :tahun.', ['bulan' => __($bulan), 'tahun' => $tahun]) }}</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Summary Footer -->
     <!-- test -->
    @if($data->count() > 0)
    <div class="mt-6 bg-slate-50 border border-slate-100 rounded-xl p-4 transition-colors duration-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-slate-455 font-bold uppercase tracking-wider">{{ __('Total Transaksi') }}</p>
                <p class="text-lg font-bold text-slate-800 mt-1">{{ $data->count() }} transaksi</p>
            </div>
            <div>
                <p class="text-xs text-slate-450 font-bold uppercase tracking-wider">{{ __('Total Bahan Bakar') }}</p>
                <p class="text-lg font-bold text-emerald-600 mt-1">{{ number_format($data->sum('total_quantity') ?? 0, 0) }} L</p>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('internalOrderSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('table tbody tr').forEach(row => {
                // If there's no data, skip
                if (row.cells.length < 2) return;
                const ioText = row.cells[1].textContent.toLowerCase(); // Column 2 is Internal Order (index 1)
                row.style.display = ioText.includes(q) ? '' : 'none';
            });
        });
    }
});
</script>
@endsection