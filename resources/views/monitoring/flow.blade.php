@extends('layouts.app')
@section('title', 'Alur Sistem')
@section('content')

<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <i class="fas fa-project-diagram text-forest"></i>
                Alur Integrasi & Aliran Data Sistem
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Visualisasi bagaimana data monitoring diolah, diintegrasikan, dan disajikan pada dashboard.
            </p>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="border-b border-slate-200 dark:border-white/5">
        <nav class="-mb-px flex space-x-6 sm:space-x-8" aria-label="Tabs">
            <button id="tab-solar" onclick="switchTab('solar')" 
                    class="border-forest text-forest dark:text-emerald-400 whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition flex items-center gap-2 focus:outline-none">
                <i class="fas fa-gas-pump text-sm"></i>
                <span>Monitoring Pemakaian Solar</span>
            </button>
            <button id="tab-alat" onclick="switchTab('alat')" 
                    class="border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:border-slate-350 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition flex items-center gap-2 focus:outline-none">
                <i class="fas fa-tractor text-sm"></i>
                <span>Monitoring Penggunaan Alat Berat</span>
            </button>
        </nav>
    </div>

    <!-- Tab Content: Solar -->
    <div id="content-solar" class="relative opacity-100 visible w-full transition-all duration-300">
        <div class="bg-white dark:bg-[#0B1120]/40 rounded-2xl border border-slate-200 dark:border-white/5 p-4 sm:p-8 shadow-sm">
            
            <div class="text-center max-w-2xl mx-auto mb-8">
                <span class="px-2.5 py-1 text-xs font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-full">Pipelines Sinkronisasi</span>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mt-2">Sinkronisasi Data Kilometer vs Konsumsi BBM</h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Mengintegrasikan data jarak tempuh kendaraan dari GPS AGI dengan catatan konsumsi solar dispenser.</p>
            </div>

            <!-- Two-Track Input Streams -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start max-w-4xl mx-auto">
                
                <!-- Track 1: GPS Telemetry -->
                <div class="space-y-3">
                    <div class="text-center md:text-left mb-4">
                        <span class="text-xs font-bold text-blue-500 uppercase tracking-wider">Jalur A: Data Telemetri GPS</span>
                    </div>

                    <!-- Step 1 -->
                    <div class="bg-slate-900 text-white dark:bg-slate-950 border border-slate-800 rounded-xl p-4 shadow-sm flex items-start space-x-3 w-full max-w-sm mx-auto transform hover:scale-[1.02] transition-all duration-200">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-white/10 text-blue-400 flex-shrink-0 font-bold">
                            <i class="fas fa-mountain-city text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold">Estate LKE</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Lokasi wilayah operasional pengumpulan data.</p>
                        </div>
                    </div>

                    <!-- Arrow -->
                    <div class="text-center text-slate-300 dark:text-slate-700 py-1">
                        <i class="fas fa-chevron-down text-sm"></i>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/5 rounded-xl p-4 shadow-sm flex items-start space-x-3 w-full max-w-sm mx-auto transform hover:scale-[1.02] transition-all duration-200">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex-shrink-0 font-bold">
                            <i class="fas fa-satellite-dish text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Sistem AGI GPS</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pengiriman data koordinat & telemetri nirkabel.</p>
                        </div>
                    </div>

                    <!-- Arrow -->
                    <div class="text-center text-slate-300 dark:text-slate-700 py-1">
                        <i class="fas fa-chevron-down text-sm"></i>
                    </div>

                    <!-- Step 3 -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/5 rounded-xl p-4 shadow-sm flex items-start space-x-3 w-full max-w-sm mx-auto transform hover:scale-[1.02] transition-all duration-200">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex-shrink-0 font-bold">
                            <i class="fas fa-truck-pickup text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Vehicle Identifiers</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Identitas kendaraan: No. Mesin / Rangka / Nopol.</p>
                        </div>
                    </div>

                    <!-- Arrow -->
                    <div class="text-center text-slate-300 dark:text-slate-700 py-1">
                        <i class="fas fa-chevron-down text-sm"></i>
                    </div>

                    <!-- Step 4 -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/5 rounded-xl p-4 shadow-sm flex items-start space-x-3 w-full max-w-sm mx-auto transform hover:scale-[1.02] transition-all duration-200">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex-shrink-0 font-bold">
                            <i class="fas fa-road text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Data Kilometer (KM)</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Log jarak tempuh akhir per kendaraan.</p>
                        </div>
                    </div>
                </div>

                <!-- Track 2: Fuel Consumption -->
                <div class="space-y-3">
                    <div class="text-center md:text-left mb-4">
                        <span class="text-xs font-bold text-amber-500 uppercase tracking-wider">Jalur B: Transaksi Dispenser Solar</span>
                    </div>

                    <!-- Step 1 -->
                    <div class="bg-amber-600 text-white dark:bg-amber-950 dark:text-amber-200 border border-amber-500/25 dark:border-amber-900/50 rounded-xl p-4 shadow-sm flex items-start space-x-3 w-full max-w-sm mx-auto transform hover:scale-[1.02] transition-all duration-200">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-white/10 text-amber-200 flex-shrink-0 font-bold">
                            <i class="fas fa-id-card text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold">Nomor Kendaraan TPA</h4>
                            <p class="text-xs text-amber-100 dark:text-amber-400 mt-0.5">Verifikasi plat nomor (e.g. E031 XX) saat isi BBM.</p>
                        </div>
                    </div>

                    <!-- Arrow -->
                    <div class="text-center text-slate-300 dark:text-slate-700 py-1">
                        <i class="fas fa-chevron-down text-sm"></i>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/5 rounded-xl p-4 shadow-sm flex items-start space-x-3 w-full max-w-sm mx-auto transform hover:scale-[1.02] transition-all duration-200">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex-shrink-0 font-bold">
                            <i class="fas fa-gas-pump text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Pemakaian Solar</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Catatan dispenser BBM dalam satuan liter.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Merging Divider -->
            <div class="hidden md:flex justify-around items-center max-w-4xl mx-auto my-6 text-slate-300 dark:text-slate-700">
                <div class="w-1/2 flex justify-center">
                    <i class="fas fa-long-arrow-alt-down text-2xl animate-bounce"></i>
                </div>
                <div class="w-1/2 flex justify-center">
                    <i class="fas fa-long-arrow-alt-down text-2xl animate-bounce"></i>
                </div>
            </div>
            
            <div class="md:hidden text-center text-slate-300 dark:text-slate-700 py-4">
                <i class="fas fa-arrow-down text-xl"></i>
            </div>

            <!-- Process & Output -->
            <div class="space-y-4 max-w-xl mx-auto">
                <!-- Merge Node -->
                <div class="bg-[#0F172A] text-white dark:bg-slate-950 border border-slate-800 dark:border-white/5 rounded-2xl p-5 shadow-lg flex items-start space-x-4 transform hover:scale-[1.02] transition-all duration-200">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-forest text-white flex-shrink-0 font-bold shadow-md">
                        <i class="fas fa-cogs text-lg animate-spin" style="animation-duration: 8s;"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-base font-bold text-emerald-455 text-emerald-400">Sistem Kontrol Pemakaian Solar</h4>
                        <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                            Algoritma ETL backend secara otomatis melakukan pencocokan silang (cross-match) data kilometer tempuh harian (Jalur A) dengan data liter solar yang diisi (Jalur B) menggunakan pencocokan plat nomor kendaraan.
                        </p>
                    </div>
                </div>

                <!-- Arrow -->
                <div class="text-center text-slate-300 dark:text-slate-700 py-1">
                    <i class="fas fa-chevron-down text-lg"></i>
                </div>

                <!-- Report Node -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/5 rounded-xl p-4 shadow-sm flex items-start space-x-3 transform hover:scale-[1.02] transition-all duration-200">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex-shrink-0 font-bold">
                        <i class="fas fa-file-invoice text-base"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Laporan Kontrol Pemakaian BBM</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Penyusunan ringkasan efisiensi bahan bakar bulanan & mingguan.</p>
                    </div>
                </div>

                <!-- Arrow -->
                <div class="text-center text-slate-300 dark:text-slate-700 py-1">
                    <i class="fas fa-chevron-down text-sm"></i>
                </div>

                <!-- Output Table Node -->
                <div class="bg-white dark:bg-slate-900 border border-dashed border-slate-300 dark:border-white/10 rounded-xl p-4 shadow-sm flex items-start space-x-3 transform hover:scale-[1.02] transition-all duration-200">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex-shrink-0 font-bold">
                        <i class="fas fa-table text-base"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Tabel: Kilometer vs Liter</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Analisis efisiensi riil (KM/Liter) per unit alat transportasi untuk pengambilan keputusan.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Tab Content: Alat Berat UT -->
    <div id="content-alat" class="absolute top-0 left-0 opacity-0 invisible pointer-events-none w-full transition-all duration-300">
        <div class="bg-white dark:bg-[#0B1120]/40 rounded-2xl border border-slate-200 dark:border-white/5 p-4 sm:p-8 shadow-sm">
            
            <div class="text-center max-w-2xl mx-auto mb-8">
                <span class="px-2.5 py-1 text-xs font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-full">Data Ingestion Pipeline</span>
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mt-2">Pipa Data Monitoring Alat Berat (Heavy Equipment)</h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Mengintegrasikan data jam kerja (Hour Meter/HM) Caterpillar dengan master data internal dan SAP.</p>
            </div>

            <!-- Three Source Streams -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto items-stretch">
                
                <!-- Source 1 -->
                <div class="flex flex-col items-center bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-white/5 rounded-2xl p-4 text-center transform hover:scale-[1.02] transition-all duration-200">
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 rounded-md mb-3">Sumber Data 1</span>
                    <div class="w-12 h-12 rounded-full bg-amber-500 text-white flex items-center justify-center text-lg mb-3 shadow-md">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">CATERPILLAR VL</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex-1">Website telemetry resmi Caterpillar (VL.cat.com).</p>
                    <i class="fas fa-chevron-down text-slate-300 dark:text-slate-700 my-3"></i>
                    <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-white/5 rounded-lg p-2.5 text-xs text-slate-600 dark:text-slate-400 w-full font-medium">
                        <i class="fas fa-file-csv text-emerald-500 mr-1.5"></i>
                        Caterpillar Export File
                    </div>
                </div>

                <!-- Source 2 -->
                <div class="flex flex-col items-center bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-white/5 rounded-2xl p-4 text-center transform hover:scale-[1.02] transition-all duration-200">
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-md mb-3">Sumber Data 2</span>
                    <div class="w-12 h-12 rounded-full bg-slate-905 bg-slate-900 text-white flex items-center justify-center text-lg mb-3 shadow-md">
                        <i class="fas fa-database"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Master Data Internal</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex-1">Database aset & registrasi unit internal perusahaan.</p>
                    <i class="fas fa-chevron-down text-slate-300 dark:text-slate-700 my-3"></i>
                    <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-white/5 rounded-lg p-2.5 text-xs text-slate-600 dark:text-slate-400 w-full font-medium">
                        <i class="fas fa-file-excel text-blue-500 mr-1.5"></i>
                        Master Excel / System DB
                    </div>
                </div>

                <!-- Source 3 -->
                <div class="flex flex-col items-center bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-white/5 rounded-2xl p-4 text-center transform hover:scale-[1.02] transition-all duration-200">
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-100 dark:bg-blue-950/50 text-blue-700 dark:text-blue-400 rounded-md mb-3">Sumber Data 3</span>
                    <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center text-lg mb-3 shadow-md">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">SAP ERP</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex-1">Data master SAP logistik dan operasional perusahaan.</p>
                    <i class="fas fa-chevron-down text-slate-300 dark:text-slate-700 my-3"></i>
                    <div class="bg-white dark:bg-slate-950 border border-slate-200 dark:border-white/5 rounded-lg p-2.5 text-xs text-slate-600 dark:text-slate-400 w-full font-medium">
                        <i class="fas fa-file-code text-indigo-500 mr-1.5"></i>
                        SAP Data Extract
                    </div>
                </div>

            </div>

            <!-- Merging Down Arrows -->
            <div class="hidden md:flex justify-around items-center max-w-5xl mx-auto my-6 text-slate-300 dark:text-slate-700">
                <i class="fas fa-long-arrow-alt-down text-2xl animate-bounce"></i>
                <i class="fas fa-long-arrow-alt-down text-2xl animate-bounce"></i>
                <i class="fas fa-long-arrow-alt-down text-2xl animate-bounce"></i>
            </div>
            <div class="md:hidden text-center text-slate-300 dark:text-slate-700 py-4">
                <i class="fas fa-arrow-down text-xl"></i>
            </div>

            <!-- Processing & Storage Pipeline -->
            <div class="space-y-4 max-w-xl mx-auto">
                <!-- Step 1: Clean & Standardize -->
                <div class="bg-[#0F172A] text-white dark:bg-slate-950 border border-slate-800 dark:border-white/5 rounded-xl p-4 shadow-sm flex items-start space-x-3 transform hover:scale-[1.02] transition-all duration-200">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-amber-500 text-white flex-shrink-0 font-bold shadow-md">
                        <i class="fas fa-cogs text-base"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-amber-400">Normalisasi & Standardisasi Data</h4>
                        <p class="text-xs text-slate-300 mt-0.5">Membersihkan noise, menyeragamkan format kode unit, tanggal, jam kerja, serta melakukan konversi unit yang berbeda.</p>
                    </div>
                </div>

                <!-- Arrow -->
                <div class="text-center text-slate-300 dark:text-slate-700 py-1">
                    <i class="fas fa-chevron-down text-sm"></i>
                </div>

                <!-- Step 2: Merge / Join -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/5 rounded-xl p-4 shadow-sm flex items-start space-x-3 transform hover:scale-[1.02] transition-all duration-200">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex-shrink-0 font-bold">
                        <i class="fas fa-compress-arrows-alt text-base"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Merge / Join by Unit ID</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Menyatukan data dari ketiga jalur sumber dengan kata kunci pengenal Unit ID.</p>
                    </div>
                </div>

                <!-- Arrow -->
                <div class="text-center text-slate-300 dark:text-slate-700 py-1">
                    <i class="fas fa-chevron-down text-sm"></i>
                </div>

                <!-- Step 3: Database storage -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/5 rounded-xl p-4 shadow-sm flex items-start space-x-3 transform hover:scale-[1.02] transition-all duration-200">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex-shrink-0 font-bold">
                        <i class="fas fa-database text-base"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Penyimpanan Database Lokasi (SQLite)</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Data terstruktur hasil integrasi disimpan ke dalam database SQLite lokal.</p>
                    </div>
                </div>

                <!-- Arrow -->
                <div class="text-center text-slate-300 dark:text-slate-700 py-1">
                    <i class="fas fa-chevron-down text-sm"></i>
                </div>

                <!-- Step 4: Laravel App -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/5 rounded-xl p-4 shadow-sm flex items-start space-x-3 transform hover:scale-[1.02] transition-all duration-200">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-forest text-white flex-shrink-0 font-bold shadow-md">
                        <i class="fas fa-desktop text-base"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">Aplikasi Web Laravel (Dashboard)</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Menyajikan dashboard visualisasi Operating Hours (HM), konsumsi BBM harian, dan grafik analitis.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function switchTab(tabId) {
        const tabSolar = document.getElementById('tab-solar');
        const tabAlat = document.getElementById('tab-alat');
        
        tabSolar.className = "border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:border-slate-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition flex items-center gap-2 focus:outline-none";
        tabAlat.className = "border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:border-slate-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition flex items-center gap-2 focus:outline-none";
        
        const contentSolar = document.getElementById('content-solar');
        const contentAlat = document.getElementById('content-alat');
        
        contentSolar.classList.remove('relative', 'opacity-100', 'visible');
        contentSolar.classList.add('absolute', 'top-0', 'left-0', 'opacity-0', 'invisible', 'pointer-events-none');
        
        contentAlat.classList.remove('relative', 'opacity-100', 'visible');
        contentAlat.classList.add('absolute', 'top-0', 'left-0', 'opacity-0', 'invisible', 'pointer-events-none');
        
        if(tabId === 'solar') {
            tabSolar.className = "border-forest text-forest dark:text-emerald-400 whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition flex items-center gap-2 focus:outline-none";
            contentSolar.classList.remove('absolute', 'top-0', 'left-0', 'opacity-0', 'invisible', 'pointer-events-none');
            contentSolar.classList.add('relative', 'opacity-100', 'visible');
        } else {
            tabAlat.className = "border-forest text-forest dark:text-emerald-400 whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition flex items-center gap-2 focus:outline-none";
            contentAlat.classList.remove('absolute', 'top-0', 'left-0', 'opacity-0', 'invisible', 'pointer-events-none');
            contentAlat.classList.add('relative', 'opacity-100', 'visible');
        }
    }
</script>
@endsection
