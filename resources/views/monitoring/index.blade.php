@extends('layouts.app')

@section('title', __('Dashboard Monitoring'))

@section('content')
<div class="space-y-6">
    <!-- Print Stylesheet -->
    <style>
        @media print {
            nav, footer, .no-print, #assetSearchInput, th:last-child, td:last-child {
                display: none !important;
            }
            main {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .shadow {
                box-shadow: none !important;
                border: 1px solid #e5e7eb !important;
            }
            .rounded-lg {
                border-radius: 0.5rem !important;
            }
        }
    </style>

    <!-- Filter (no-print) -->
    <div class="bg-white rounded-lg shadow p-4 no-print">
        <form action="{{ route('monitoring.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">{{ __('Bulan') }}:</label>
                    <select name="bulan" class="rounded-md border-gray-300 shadow-sm p-2 border">
                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">{{ __('Tahun') }}:</label>
                    <select name="tahun" class="rounded-md border-gray-300 shadow-sm p-2 border">
                        @for($i = 2023; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="bg-[#8E6E4F] text-white px-4 py-2 rounded-lg hover:bg-[#7D5F43] transition">
                    <i class="fas fa-filter mr-2"></i> {{ __('Filter') }}
                </button>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('monitoring.export') }}?bulan={{ $bulan }}&tahun={{ $tahun }}" class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition inline-flex items-center">
                    <i class="fas fa-file-excel mr-2"></i> {{ __('Unduh Excel (XLSX)') }}
                </a>
                <button type="button" onclick="window.print()" class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 transition inline-flex items-center">
                    <i class="fas fa-file-pdf mr-2"></i> {{ __('Cetak Laporan (PDF)') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="p-3 bg-amber-50 rounded-full">
                    <i class="fas fa-tools text-[#8E6E4F] text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Total Aset') }}</p>
                    <p class="text-2xl font-bold">{{ $stats->total_aset ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="p-3 bg-emerald-50 rounded-full">
                    <i class="fas fa-clock text-emerald-700 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Total Waktu Kerja') }}</p>
                    <p class="text-2xl font-bold">{{ number_format($stats->total_waktu_kerja ?? 0, 1) }} @if(app()->getLocale() == 'en') hours @else jam @endif</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="p-3 bg-[#FDF6E2] rounded-full">
                    <i class="fas fa-hourglass-half text-amber-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Rata-rata Idle') }}</p>
                    <p class="text-2xl font-bold">{{ number_format($stats->avg_idle ?? 0, 1) }}%</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="p-3 bg-rose-50 rounded-full">
                    <i class="fas fa-gas-pump text-rose-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Total Bahan Bakar') }}</p>
                    <p class="text-2xl font-bold">{{ number_format($stats->total_bahan_bakar ?? 0, 0) }} L</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Grafik -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Grafik Harian -->
        <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
            <h3 class="text-lg font-semibold mb-4 text-gray-700">
                <i class="fas fa-chart-bar text-[#8E6E4F] mr-2"></i> {{ __('Grafik Harian Jam Kerja') }}
            </h3>
            <div class="relative h-[300px]">
                <canvas id="monitoringChart"></canvas>
            </div>
        </div>

        <!-- Grafik Rincian (Doughnut & Pie) -->
        <div class="bg-white rounded-lg shadow p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-semibold mb-4 text-gray-700">
                    <i class="fas fa-chart-pie text-[#8E6E4F] mr-2"></i> {{ __('Analisis Utilisasi & Area') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-6">
                    <!-- Doughnut Utilisasi -->
                    <div class="flex flex-col items-center">
                        <p class="text-sm font-semibold text-gray-500 mb-2">{{ __('Utilisasi Jam Kerja') }}</p>
                        <div class="w-full max-w-[150px] h-[100px] relative">
                            <canvas id="utilizationChart"></canvas>
                        </div>
                    </div>
                    <!-- Pie Area -->
                    <div class="flex flex-col items-center border-t lg:border-t lg:pt-4 border-gray-100">
                        <p class="text-sm font-semibold text-gray-500 mb-2">{{ __('Beban Operasi per Area') }}</p>
                        <div class="w-full max-w-[150px] h-[100px] relative">
                            <canvas id="areaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $highIdleAssets = $perAset->filter(function($item) {
            return $item->avg_idle > 40;
        });
        
        $areaData = [];
        foreach ($perAset as $item) {
            if ($item->area) {
                $areaData[$item->area] = ($areaData[$item->area] ?? 0) + $item->total_operasi;
            }
        }
    @endphp

    <!-- Alert Panel -->
    @if($highIdleAssets->count() > 0)
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm no-print">
        <div class="flex items-start">
            <div class="flex-shrink-0 mt-0.5">
                <i class="fas fa-exclamation-triangle text-red-600 text-lg animate-bounce"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-semibold text-red-800">{{ __('Peringatan Utilisasi: Tingkat Idle Sangat Tinggi (>40%)') }}</h4>
                <p class="text-xs text-red-700 mt-1">
                    {!! __('Ditemukan <strong>:count unit</strong> alat berat dengan persentase waktu mesin menyala tanpa operasi aktif yang tinggi:', ['count' => $highIdleAssets->count()]) !!}
                </p>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach($highIdleAssets as $asset)
                        <a href="{{ route('monitoring.detail', $asset->id_aset) }}?bulan={{ $bulan }}&tahun={{ $tahun }}" 
                           class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 hover:bg-red-200 transition border border-red-200">
                           <i class="fas fa-tools mr-1 text-red-500"></i> {{ $asset->id_aset }} ({{ number_format($asset->avg_idle, 1) }}%)
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabel Per Aset -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-4">
            <h3 class="text-lg font-semibold text-gray-700">
                <i class="fas fa-table text-[#8E6E4F] mr-2"></i> {{ __('Ringkasan Per Aset') }}
                <span class="text-sm font-normal text-gray-500 ml-2">({{ __($bulan) }} {{ $tahun }})</span>
            </h3>
            <div class="relative max-w-xs w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="assetSearchInput" placeholder="{{ __('Cari Aset...') }}" 
                       class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg shadow-sm focus:ring-[#8E6E4F]/50 focus:border-[#8E6E4F] text-sm focus:outline-none">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('ID Aset') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Model') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Grup Aset') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Area') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Total Kerja') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Total Operasi') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Total Idle') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('% Idle') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Bahan Bakar') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($perAset as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium">{{ $item->id_aset }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->model }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->group_aset ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->area ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">{{ number_format($item->total_kerja, 1) }}</td>
                        <td class="px-4 py-3 text-sm">{{ number_format($item->total_operasi, 1) }}</td>
                        <td class="px-4 py-3 text-sm">{{ number_format($item->total_idle, 1) }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="px-2 py-1 rounded-full text-xs
                                @if(($item->avg_idle ?? 0) < 30) bg-[#E2F7E9] text-emerald-800
                                @elseif(($item->avg_idle ?? 0) < 50) bg-[#FFF6E2] text-amber-800
                                @else bg-rose-50 text-rose-800 @endif">
                                {{ number_format($item->avg_idle ?? 0, 1) }}%
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ number_format($item->total_bakar ?? 0, 0) }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('monitoring.detail', $item->id_aset) }}?bulan={{ $bulan }}&tahun={{ $tahun }}"
                               class="text-[#8E6E4F] hover:text-[#7D5F43] font-semibold">
                                <i class="fas fa-eye"></i> {{ __('Detail') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-2xl block mb-2"></i>
                            {{ __('Belum ada data untuk :bulan :tahun. Silakan import data terlebih dahulu.', ['bulan' => __($bulan), 'tahun' => $tahun]) }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter pencarian tabel aset
    const searchInput = document.getElementById('assetSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('table tbody tr');

            rows.forEach(row => {
                if (row.cells.length < 4) return; // skip row kosong atau action row yang tak sesuai

                const idAset = row.cells[0].textContent.toLowerCase();
                const model = row.cells[1].textContent.toLowerCase();
                const group = row.cells[2].textContent.toLowerCase();
                const area = row.cells[3].textContent.toLowerCase();

                if (idAset.includes(query) || model.includes(query) || group.includes(query) || area.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Ambil data chart harian
    fetch('{{ route("monitoring.chart") }}?bulan={{ $bulan }}&tahun={{ $tahun }}')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('monitoringChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => {
                        const date = new Date(item.tanggal);
                        return date.getDate() + '/' + (date.getMonth() + 1);
                    }),
                    datasets: [
                        {
                            label: '{{ __('Waktu Kerja') }}',
                            data: data.map(item => item.total_kerja),
                            backgroundColor: 'rgba(16, 185, 129, 0.7)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 1
                        },
                        {
                            label: '{{ __('Waktu Operasi') }}',
                            data: data.map(item => item.total_operasi),
                            backgroundColor: 'rgba(142, 110, 79, 0.7)',
                            borderColor: 'rgba(142, 110, 79, 1)',
                            borderWidth: 1
                        },
                        {
                            label: '{{ __('Waktu Idle') }}',
                            data: data.map(item => item.total_idle),
                            backgroundColor: 'rgba(217, 160, 89, 0.7)',
                            borderColor: 'rgba(217, 160, 89, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: '{{ __('Jam') }}'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: '{{ __('Tanggal') }}'
                            }
                        }
                    }
                }
            });
        });

    // 1. Chart Utilisasi (Doughnut)
    const utilCtx = document.getElementById('utilizationChart').getContext('2d');
    new Chart(utilCtx, {
        type: 'doughnut',
        data: {
            labels: ['{{ __('Kerja') }}', '{{ __('Idle') }}'],
            datasets: [{
                data: [{{ $stats->total_waktu_kerja ?? 0 }}, {{ $stats->total_waktu_idle ?? 0 }}],
                backgroundColor: ['#10b981', '#d9a059'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 10 } }
                }
            }
        }
    });

    // 2. Chart Area (Pie)
    const areaCtx = document.getElementById('areaChart').getContext('2d');
    const areaLabels = @json(array_keys($areaData));
    const areaValues = @json(array_values($areaData));
    new Chart(areaCtx, {
        type: 'pie',
        data: {
            labels: areaLabels,
            datasets: [{
                data: areaValues,
                backgroundColor: [
                    '#a7825e', '#10b981', '#d9a059', '#ef4444', '#b08b63', '#ec4899', '#8b7a6c'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 10 } }
                }
            }
        }
    });
});
</script>
@endsection