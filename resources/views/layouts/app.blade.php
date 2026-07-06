<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Sistem Monitoring Alat Berat'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #FAF7F2;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between">
    <div>
        <!-- Navbar -->
        <nav class="bg-[#A7825E] text-white p-4 shadow-md no-print">
            <div class="container mx-auto flex justify-between items-center">
                <div class="flex items-center space-x-6">
                    <h1 class="text-xl font-bold tracking-wide">MONITORING ALAT BERAT</h1>
                    
                    @auth
                        <div class="border-l border-amber-400/40 h-5 my-auto"></div>
                        <a href="{{ route('monitoring.index') }}" class="hover:text-amber-100 transition flex items-center text-sm font-semibold">
                            <i class="fas fa-chart-line mr-1.5"></i> {{ __('Dashboard') }}
                        </a>
                    @endauth
                </div>
                
                <div class="flex items-center space-x-6">
                    @auth
                        <!-- Profile Dropdown -->
                        <div class="relative inline-block text-left" id="profileDropdownContainer">
                            <button type="button" id="profileDropdownButton" class="flex items-center space-x-2 text-white bg-[#8E6E4F] hover:bg-[#7D5F43] px-4 py-2 rounded-xl transition font-medium focus:outline-none select-none text-sm shadow-sm">
                                <i class="far fa-user-circle text-lg"></i>
                                <span>{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs ml-1 transition-transform duration-200" id="profileDropdownArrow"></i>
                            </button>
                            
                            <!-- Dropdown menu -->
                            <div id="profileDropdownMenu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-stone-100 divide-y divide-stone-100 z-50 text-sm no-print">
                                <div class="p-3">
                                    <p class="text-xs text-stone-400 font-semibold uppercase tracking-wider mb-1 px-2">{{ __('Profil Saya') }}</p>
                                    <a href="{{ route('profile.edit') }}" class="flex items-center space-x-2 px-3 py-2 text-stone-700 hover:bg-[#FAF7F2] hover:text-[#8E6E4F] rounded-lg transition">
                                        <i class="fas fa-user-cog text-[#8E6E4F]"></i>
                                        <span>{{ __('Edit Profil') }}</span>
                                    </a>
                                </div>
                                
                                <div class="p-3">
                                    <p class="text-xs text-stone-400 font-semibold uppercase tracking-wider mb-1 px-2">{{ __('Fitur & Fitur Lain') }}</p>
                                    <a href="{{ route('import.index') }}" class="flex items-center space-x-2 px-3 py-2 text-stone-700 hover:bg-[#FAF7F2] hover:text-[#8E6E4F] rounded-lg transition">
                                        <i class="fas fa-upload text-[#8E6E4F]"></i>
                                        <span>{{ __('Import Data') }}</span>
                                    </a>
                                    <a href="{{ route('bantuan.index') }}" class="flex items-center space-x-2 px-3 py-2 text-stone-700 hover:bg-[#FAF7F2] hover:text-[#8E6E4F] rounded-lg transition">
                                        <i class="fas fa-question-circle text-[#8E6E4F]"></i>
                                        <span>{{ __('Bantuan') }}</span>
                                    </a>
                                </div>

                                <div class="p-3">
                                    <p class="text-xs text-stone-400 font-semibold uppercase tracking-wider mb-1.5 px-2">{{ __('Bahasa') }}</p>
                                    <div class="flex items-center justify-between bg-stone-50 rounded-lg p-1.5 border border-stone-200/50 mx-2">
                                        <a href="{{ route('lang.switch', 'id') }}" class="flex-1 text-center py-1 rounded text-xs font-semibold {{ app()->getLocale() == 'id' ? 'bg-[#8E6E4F] text-white shadow-sm' : 'text-stone-500 hover:text-stone-700' }} transition">ID</a>
                                        <a href="{{ route('lang.switch', 'en') }}" class="flex-1 text-center py-1 rounded text-xs font-semibold {{ app()->getLocale() == 'en' ? 'bg-[#8E6E4F] text-white shadow-sm' : 'text-stone-500 hover:text-stone-700' }} transition">EN</a>
                                    </div>
                                </div>

                                <div class="p-3">
                                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center space-x-2 px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition font-semibold text-left">
                                            <i class="fas fa-sign-out-alt"></i>
                                            <span>{{ __('Keluar') }}</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Language Switcher for Guest -->
                        <div class="flex items-center space-x-1 text-xs bg-[#8E6E4F] rounded-lg p-0.5 border border-[#7D5F43]">
                            <a href="{{ route('lang.switch', 'id') }}" class="px-1.5 py-0.5 rounded {{ app()->getLocale() == 'id' ? 'bg-[#7D5F43] text-white font-bold' : 'text-amber-100 hover:text-white' }} transition">ID</a>
                            <a href="{{ route('lang.switch', 'en') }}" class="px-1.5 py-0.5 rounded {{ app()->getLocale() == 'en' ? 'bg-[#7D5F43] text-white font-bold' : 'text-amber-100 hover:text-white' }} transition">EN</a>
                        </div>
                        <a href="{{ route('login') }}" class="hover:text-amber-100 transition flex items-center text-sm font-semibold">
                            <i class="fas fa-sign-in-alt mr-1.5"></i> {{ __('Masuk') }}
                        </a>
                        <a href="{{ route('register') }}" class="bg-[#8E6E4F] hover:bg-[#7D5F43] text-white px-3 py-1.5 rounded-lg transition flex items-center text-sm font-semibold">
                            <i class="fas fa-user-plus mr-1.5"></i> {{ __('Daftar') }}
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="container mx-auto p-4">
            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 mb-4 rounded shadow-sm">
                    <i class="fas fa-check-circle mr-2 text-emerald-600"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 mb-4 rounded shadow-sm">
                    <i class="fas fa-exclamation-circle mr-2 text-rose-600"></i> {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-stone-100 text-center p-4 text-stone-600 mt-8 border-t border-stone-200 no-print">
        &copy; {{ date('Y') }} PT. Teladan Prima Agro, Tbk | {{ __('Testing') }}
    </footer>

    <!-- Dropdown JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropdownButton = document.getElementById('profileDropdownButton');
            const dropdownMenu = document.getElementById('profileDropdownMenu');
            const dropdownArrow = document.getElementById('profileDropdownArrow');

            if (dropdownButton && dropdownMenu) {
                dropdownButton.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isHidden = dropdownMenu.classList.contains('hidden');
                    
                    if (isHidden) {
                        dropdownMenu.classList.remove('hidden');
                        dropdownArrow.classList.add('rotate-180');
                    } else {
                        dropdownMenu.classList.add('hidden');
                        dropdownArrow.classList.remove('rotate-180');
                    }
                });

                document.addEventListener('click', function (e) {
                    if (!dropdownMenu.classList.contains('hidden') && !e.target.closest('#profileDropdownContainer')) {
                        dropdownMenu.classList.add('hidden');
                        dropdownArrow.classList.remove('rotate-180');
                    }
                });
            }
        });
    </script>
</body>
</html>