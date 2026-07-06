@extends('layouts.app')

@section('title', __('Bantuan'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
            <i class="fas fa-question-circle text-[#8E6E4F] mr-2"></i>
            {{ __('Bantuan & Panduan Penggunaan') }}
        </h2>

        <div class="space-y-6">
            <!-- Section 1: Import Data -->
            <div class="border-b pb-4">
                <h3 class="text-lg font-semibold text-[#8E6E4F] flex items-center mb-3">
                    <i class="fas fa-upload mr-2"></i> 1. {{ __('Panduan Import Excel') }}
                </h3>
                <p class="text-sm text-gray-600 mb-2 leading-relaxed">
                    {{ __('Anda dapat mengimpor data utilisasi alat berat dari 3 format vendor yang didukung:') }}
                </p>
                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 pl-2 mb-3">
                    <li><strong>CATERPILLAR</strong>: {{ __('Data ekspor langsung dari sistem Caterpillar Product Link.') }}</li>
                    <li><strong>INTERNAL</strong>: {{ __('Template laporan internal dengan kolom standard operasional.') }}</li>
                    <li><strong>SAP</strong>: {{ __('Format data ekspor dari sistem Enterprise Resource Planning SAP.') }}</li>
                </ul>
                <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-800 p-3 rounded-r-lg text-sm">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>{{ __('Penting') }}:</strong> {{ __('Pastikan format ekstensi file yang diunggah adalah .xlsx atau .xls, dan header kolom sesuai dengan standard data alat berat.') }}
                </div>
            </div>

            <!-- Section 2: Dashboard Metrics -->
            <div class="border-b pb-4">
                <h3 class="text-lg font-semibold text-[#8E6E4F] flex items-center mb-3">
                    <i class="fas fa-chart-line mr-2"></i> 2. {{ __('Membaca Indikator Dashboard') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-150">
                        <strong class="text-gray-800 block mb-1"><i class="fas fa-hourglass-half text-yellow-600 mr-1"></i> % Idle</strong>
                        {{ __('Persentase waktu di mana mesin menyala namun tidak melakukan aktivitas operasi. Semakin rendah nilai % idle, semakin efisien penggunaan alat berat.') }}
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-150">
                        <strong class="text-gray-800 block mb-1"><i class="fas fa-gas-pump text-red-600 mr-1"></i> Laju Bakar (L/Jam)</strong>
                        {{ __('Rata-rata konsumsi bahan bakar alat berat per jam operasional.') }}
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-150">
                        <strong class="text-gray-800 block mb-1"><i class="fas fa-exclamation-triangle text-red-600 mr-1"></i> {{ __('Peringatan Idle Tinggi') }} (>40%)</strong>
                        {{ __('Jika rata-rata % idle aset melebihi 40%, panel peringatan merah akan muncul di dashboard untuk menandakan terjadinya pemborosan bahan bakar.') }}
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-150">
                        <strong class="text-gray-800 block mb-1"><i class="fas fa-chart-pie text-indigo-600 mr-1"></i> {{ __('Beban per Area') }}</strong>
                        {{ __('Grafik lingkaran yang menampilkan distribusi total beban waktu operasi berdasarkan lokasi kerja/area.') }}
                    </div>
                </div>
            </div>

            <!-- Section 3: Troubleshooting -->
            <div>
                <h3 class="text-lg font-semibold text-[#8E6E4F] flex items-center mb-3">
                    <i class="fas fa-wrench mr-2"></i> 3. {{ __('Pemecahan Masalah (FAQ)') }}
                </h3>
                <div class="space-y-3 text-sm text-gray-600">
                    <div>
                        <strong class="text-gray-800 block"><i class="far fa-question-circle text-[#8E6E4F] mr-1"></i> {{ __('Mengapa grafik harian saya kosong setelah import?') }}</strong>
                        <p class="mt-1 leading-relaxed">
                            {{ __('Hal ini biasanya terjadi karena Anda memfilter bulan atau tahun yang tidak sesuai dengan data di dalam file Excel. Silakan periksa kolom tanggal pada data yang Anda import, atau biarkan filter default memuat otomatis bulan terbaru.') }}
                        </p>
                    </div>
                    <div>
                        <strong class="text-gray-800 block"><i class="far fa-question-circle text-[#8E6E4F] mr-1"></i> {{ __('Bagaimana cara membatalkan import jika terjadi kesalahan data?') }}</strong>
                        <p class="mt-1 leading-relaxed">
                            {{ __('Buka halaman Import Data, lihat bagian "Riwayat Unggah File", lalu klik tombol "Hapus Data" di baris nama file yang salah. Sistem akan otomatis menghapus data terkait tanpa mengganggu data file lainnya.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
