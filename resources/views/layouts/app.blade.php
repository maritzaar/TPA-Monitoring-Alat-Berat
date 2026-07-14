<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beranda') - Teladan Prima Agro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        forest: '#218838',
                        chalice: '#AAAAAA',
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        serif: ['Merriweather', 'serif'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="{{ asset('js/chart.js') }}"></script>
    <script>
        // Check local storage for theme
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&family=Outfit:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Outfit', sans-serif; font-size: 1rem; }
        @media print { .no-print { display: none !important; } }

        /* Scale up Tailwind text utility classes globally for readability & comfort */
        .text-\[9px\] { font-size: 0.75rem !important; }   /* ~12px */
        .text-\[10px\] { font-size: 0.8rem !important; }   /* ~12.8px */
        .text-xs { font-size: 0.875rem !important; }        /* ~14px */
        .text-sm { font-size: 0.975rem !important; }        /* ~15.6px */
        .text-base { font-size: 1.1rem !important; }        /* ~17.6px */
        .text-lg { font-size: 1.25rem !important; }        /* ~20px */
        .text-xl { font-size: 1.45rem !important; }        /* ~23px */
        .text-2xl { font-size: 1.75rem !important; }        /* ~28px */
        .text-3xl { font-size: 2.15rem !important; }        /* ~34px */

        /* Smooth sidebar transition */
        #sidebarDrawer { transition: transform 0.28s cubic-bezier(.4,0,.2,1); }
        #sidebarBackdrop { transition: opacity 0.28s ease; }

        /* Scrollable table wrapper on mobile */
        .table-scroll { -webkit-overflow-scrolling: touch; }

        /* Desktop sidebar offset transition */
        #mainWrapper { transition: padding-left 0.28s cubic-bezier(.4,0,.2,1); }

        /* Page load animation */
        @keyframes pageFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .page-transition {
            animation: pageFadeIn 0.35s ease-out forwards;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 flex flex-col transition-colors duration-200">

    @auth
    <!-- Sidebar Backdrop (mobile only) -->
    <div id="sidebarBackdrop"
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-20 hidden opacity-0 no-print"></div>

    <!-- Sidebar Drawer -->
    <aside id="sidebarDrawer"
           class="fixed top-16 left-0 bottom-0 w-64 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 z-30 -translate-x-full flex flex-col shadow-sm no-print">

        <!-- Mobile-only header inside drawer -->
        <div class="h-14 bg-[#0F172A] dark:bg-slate-950 flex items-center justify-between px-4 text-white md:hidden flex-shrink-0 transition-colors duration-200">
            <span class="font-bold tracking-wide flex items-center text-sm">
                <i class="fas fa-desktop mr-2 text-blue-400"></i>
                Navigation Menu
            </span>
            <button id="sidebarClose" class="text-white hover:text-blue-400 transition focus:outline-none p-1">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Nav links -->
        <nav class="flex-1 overflow-y-auto p-3 space-y-1">

            {{-- Home --}}
            <a href="{{ route('home') }}"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg transition text-sm font-semibold
               {{ Route::currentRouteName() === 'home'
                   ? 'bg-blue-50 dark:bg-blue-900/30 text-forest border-l-4 border-blue-600 pl-3'
                   : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:text-slate-800 dark:hover:text-slate-200' }}">
                <i class="fas fa-home w-5 text-center text-sm"></i>
                <span>Home</span>
            </a>

            {{-- ── MONITORING GROUP ── --}}
            @php
                $monitoringRoutes = ['monitoring.working_hour', 'monitoring.fuel', 'monitoring.working_hour_detail', 'monitoring.fuel_detail', 'monitoring.flow'];
                $monitoringActive = in_array(Route::currentRouteName(), $monitoringRoutes);
            @endphp
            <div>
                {{-- Group header button --}}
                <button type="button" id="navMonitoringToggle"
                        class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition text-sm font-bold
                               {{ $monitoringActive ? 'text-forest bg-forest/10 dark:bg-forest/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50' }}">
                            <div class="flex items-center">
                                <i class="fas fa-chart-line w-5 mr-3 text-center text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-200 transition-colors"></i>
                                Pemantauan
                            </div>
                    <i id="navMonitoringChevron"
                       class="fas fa-chevron-down text-xs transition-transform duration-200
                              {{ $monitoringActive ? 'rotate-180' : '' }}"></i>
                </button>

                {{-- Sub-items --}}
                <div id="navMonitoringMenu"
                     class="overflow-hidden transition-all duration-200 ease-in-out
                            {{ $monitoringActive ? 'opacity-100' : 'max-h-0 opacity-0' }}"
                     @if($monitoringActive) style="max-height: 200px;" @endif>
                    <div class="mt-1 ml-4 pl-3 border-l-2 border-slate-200 space-y-2">

                        {{-- Laporan Working Hour --}}
                        <a href="{{ route('monitoring.working_hour') }}"
                           class="flex items-center space-x-2.5 px-3 py-2 rounded-lg transition text-xs font-semibold
                                  {{ in_array(Route::currentRouteName(), ['monitoring.working_hour', 'monitoring.working_hour_detail'])
                                      ? 'bg-forest text-white shadow-sm'
                                      : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-200' }}">
                            <i class="fas fa-clock w-4 text-center"></i>
                            <span>Jam Kerja</span>
                        </a>

                        {{-- Laporan Fuel --}}
                        <a href="{{ route('monitoring.fuel') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg transition text-xs font-semibold
                                    {{ request()->routeIs('monitoring.fuel*') ? 'bg-forest text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-200' }}">
                                    <i class="fas fa-gas-pump text-[10px] mr-2 {{ request()->routeIs('monitoring.fuel*') ? 'text-forest' : 'text-slate-300' }}"></i>
                                    Konsumsi BBM
                                </a>

                        {{-- Alur Sistem --}}
                        <a href="{{ route('monitoring.flow') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg transition text-xs font-semibold
                                    {{ request()->routeIs('monitoring.flow*') ? 'bg-forest text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-200' }}">
                                    <i class="fas fa-project-diagram text-[10px] mr-2 {{ request()->routeIs('monitoring.flow*') ? 'text-forest' : 'text-slate-300' }}"></i>
                                    Alur Sistem
                                </a>

                    </div>
                </div>
            </div>

            {{-- ── ADMIN GROUP ── --}}
            @if(Auth::user()->role === 'admin')
            @php
                $adminRoutes = ['import.index','import.upload','import.clear','users.index'];
                $adminActive = in_array(Route::currentRouteName(), $adminRoutes);
            @endphp
            <div>
                <button type="button" id="navAdminToggle"
                        class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition text-sm font-bold
                               {{ $adminActive ? 'text-forest bg-forest/10 dark:bg-forest/20' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50' }}">
                    <span class="flex items-center space-x-3">
                        <i class="fas fa-shield-alt w-5 text-center text-sm"></i>
                        <span>Kelola Data</span>
                    </span>
                    <i id="navAdminChevron"
                       class="fas fa-chevron-down text-xs transition-transform duration-200
                              {{ $adminActive ? 'rotate-180' : '' }}"></i>
                </button>

                <div id="navAdminMenu"
                     class="overflow-hidden transition-all duration-200 ease-in-out
                            {{ $adminActive ? 'max-h-40 opacity-100' : 'max-h-0 opacity-0' }}">
                    <div class="mt-1 ml-4 pl-3 border-l-2 border-slate-200 space-y-0.5">

                        <a href="{{ route('import.index') }}"
                           class="flex items-center space-x-2.5 px-3 py-2 rounded-lg transition text-xs font-semibold
                                  {{ Route::currentRouteName() === 'import.index'
                                      ? 'bg-forest text-white shadow-sm'
                                      : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-200' }}">
                            <i class="fas fa-upload w-4 text-center"></i>
                            <span>Import Data</span>
                        </a>

                        <a href="{{ route('users.index') }}" class="flex items-center space-x-2.5 px-3 py-2 rounded-lg transition text-xs font-semibold
                                    {{ request()->routeIs('users.index') ? 'bg-forest text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-slate-200' }}">
                                    <i class="fas fa-users-cog text-[10px] mr-2 {{ request()->routeIs('users.index') ? 'text-forest' : 'text-slate-300' }}"></i>
                                    Kelola Pengguna
                                </a>

                    </div>
                </div>
            </div>
            @endif

        </nav>

        <!-- Sidebar footer -->
        <div class="p-3 border-t border-slate-100 text-[10px] text-slate-400 text-center uppercase tracking-wider font-semibold flex-shrink-0">
            TELADAN PRIMA AGRO &copy; {{ date('Y') }}
        </div>
    </aside>
    @endauth

    <!-- ======== TOP NAVBAR ======== -->
    <nav class="fixed top-0 left-0 right-0 h-16 bg-[#0F172A] dark:bg-slate-950 border-b border-transparent dark:border-slate-800 text-white px-3 sm:px-4 shadow-md z-40 flex justify-between items-center no-print transition-colors duration-200">
        <!-- Left: hamburger + brand -->
        <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
            @auth
            <button id="sidebarToggle"
                    class="text-white hover:text-blue-400 transition focus:outline-none p-1 flex-shrink-0"
                    title="Toggle Menu">
                <i class="fas fa-bars text-xl"></i>
            </button>
            @endauth
            <a href="{{ route('home') }}" class="flex items-center hover:opacity-80 transition-opacity">
                <h1 class="text-xs sm:text-sm md:text-base font-bold tracking-wider flex items-center select-none whitespace-nowrap text-white">
                    <img src="{{ asset('images/logo.png') }}" alt="TPA Logo" class="h-8 w-auto mr-2 flex-shrink-0">
                    <span>TELADAN PRIMA AGRO</span>
                </h1>
            </a>
        </div>

        <!-- Right: theme toggle + user info -->
        <div class="flex items-center space-x-2 sm:space-x-4 flex-shrink-0">
            <!-- Theme Toggle -->
            <button id="themeToggleBtn" class="text-slate-300 hover:text-white transition focus:outline-none p-2 rounded-full hover:bg-slate-800">
                <i id="themeToggleIcon" class="fas fa-moon text-lg"></i>
            </button>
            
            @auth
            <div class="relative inline-block text-left" id="profileDropdownContainer">
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <!-- User name -->
                    <span class="hidden sm:inline text-xs text-slate-300 font-bold uppercase tracking-wide max-w-[140px] lg:max-w-[200px] truncate select-none">
                        {{ Auth::user()->name }}
                    </span>
                    <!-- Avatar button -->
                    <button type="button" id="profileDropdownButton"
                            class="w-9 h-9 rounded-full bg-forest hover:bg-blue-700 text-white font-bold
                                   flex items-center justify-center transition focus:outline-none select-none text-sm shadow flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </button>
                </div>

                <!-- Dropdown -->
                <div id="profileDropdownMenu"
                     class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 divide-y divide-slate-100 dark:divide-slate-700 z-50 text-sm no-print">
                    <div class="p-3">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1.5 px-2">Profil Saya</p>
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center space-x-2 px-3 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:text-forest dark:hover:text-forest rounded-lg transition font-medium">
                            <i class="fas fa-user-cog text-slate-400"></i>
                            <span>Edit Profil</span>
                        </a>
                    </div>
                    <div class="p-3">
                        <form action="{{ route('logout') }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center space-x-2 px-3 py-2 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition font-medium text-left">
                                <i class="fas fa-sign-out-alt opacity-70"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @else
            <!-- Guest controls -->
            <a href="{{ route('login') }}" class="bg-forest hover:bg-blue-700 text-white px-2.5 py-1.5 rounded-lg transition flex items-center text-sm font-semibold shadow-sm">
                <i class="fas fa-sign-in-alt mr-1"></i>
                <span>Login</span>
            </a>
            @endauth
        </div>
    </nav>

    <!-- ======== BODY WRAPPER ======== -->
    <div id="mainWrapper" class="pt-16 flex-1 flex flex-col min-h-[calc(100vh-4rem)]">
        <main class="flex-1 p-3 sm:p-4 md:p-6 w-full max-w-screen-2xl mx-auto page-transition">

            @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-3 sm:p-4 mb-4 rounded shadow-sm no-print flex items-start space-x-2">
                <i class="fas fa-check-circle text-emerald-600 mt-0.5 flex-shrink-0"></i>
                <span class="text-sm">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-3 sm:p-4 mb-4 rounded shadow-sm no-print flex items-start space-x-2">
                <i class="fas fa-exclamation-circle text-rose-600 mt-0.5 flex-shrink-0"></i>
                <span class="text-sm">{{ session('error') }}</span>
            </div>
            @endif

            @yield('content')
        </main>

        <footer class="bg-slate-100 text-center p-3 text-slate-500 text-xs border-t border-slate-200 no-print">
            &copy; {{ date('Y') }} PT. Teladan Prima Agro &mdash; @testing
        </footer>
    </div>

    <!-- ======== JS ======== -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Profile dropdown ---
        const dropBtn  = document.getElementById('profileDropdownButton');
        const dropMenu = document.getElementById('profileDropdownMenu');
        if (dropBtn && dropMenu) {
            dropBtn.addEventListener('click', e => { e.stopPropagation(); dropMenu.classList.toggle('hidden'); });
            document.addEventListener('click', e => {
                if (!e.target.closest('#profileDropdownContainer')) dropMenu.classList.add('hidden');
            });
        }

        // --- Sidebar toggle (ALL screen sizes) ---
        const toggle   = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('sidebarClose');
        const drawer   = document.getElementById('sidebarDrawer');
        const backdrop = document.getElementById('sidebarBackdrop');
        const wrapper  = document.getElementById('mainWrapper');

        const SIDEBAR_KEY = 'sidebarOpen';
        const isDesktop   = () => window.innerWidth >= 768;

        function openSidebar(save = true) {
            if (!drawer) return;
            drawer.classList.remove('-translate-x-full');
            if (!isDesktop()) {
                backdrop.classList.remove('hidden');
                requestAnimationFrame(() => backdrop.classList.replace('opacity-0','opacity-100'));
            } else {
                if (wrapper) wrapper.style.paddingLeft = '16rem';
                backdrop.classList.add('hidden');
            }
            if (save) localStorage.setItem(SIDEBAR_KEY, '1');
        }

        function closeSidebar(save = true) {
            if (!drawer) return;
            drawer.classList.add('-translate-x-full');
            if (!isDesktop()) {
                backdrop.classList.replace('opacity-100','opacity-0');
                setTimeout(() => backdrop.classList.add('hidden'), 280);
            } else {
                if (wrapper) wrapper.style.paddingLeft = '0';
            }
            if (save) localStorage.setItem(SIDEBAR_KEY, '0');
        }

        function toggleSidebar() {
            if (drawer.classList.contains('-translate-x-full')) openSidebar();
            else closeSidebar();
        }

        if (drawer) {
            if (isDesktop() && localStorage.getItem(SIDEBAR_KEY) !== '0') {
                openSidebar(false);
            }
        }

        if (toggle)   toggle.addEventListener('click', toggleSidebar);
        if (closeBtn) closeBtn.addEventListener('click', () => closeSidebar());
        if (backdrop) backdrop.addEventListener('click', () => closeSidebar());

        if (drawer) {
            drawer.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    if (!isDesktop()) closeSidebar();
                });
            });
        }

        window.addEventListener('resize', () => {
            if (!drawer) return;
            if (isDesktop()) {
                backdrop.classList.add('hidden');
                backdrop.classList.replace('opacity-100','opacity-0');
                if (!drawer.classList.contains('-translate-x-full')) {
                    if (wrapper) wrapper.style.paddingLeft = '16rem';
                } else {
                    if (wrapper) wrapper.style.paddingLeft = '0';
                }
            } else {
                if (wrapper) wrapper.style.paddingLeft = '0';
            }
        });
        // --- Collapsible sidebar nav groups ---
        function setupNavGroup(toggleId, menuId, chevronId) {
            const toggle  = document.getElementById(toggleId);
            const menu    = document.getElementById(menuId);
            const chevron = document.getElementById(chevronId);
            if (!toggle || !menu) return;
            toggle.addEventListener('click', () => {
                const isOpen = menu.style.maxHeight && menu.style.maxHeight !== '0px';
                if (isOpen) {
                    menu.style.maxHeight = '0px';
                    menu.style.opacity   = '0';
                    if (chevron) chevron.style.transform = 'rotate(0deg)';
                } else {
                    menu.style.maxHeight = menu.scrollHeight + 'px';
                    menu.style.opacity   = '1';
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                }
            });
        }
        setupNavGroup('navMonitoringToggle', 'navMonitoringMenu', 'navMonitoringChevron');
        setupNavGroup('navAdminToggle',      'navAdminMenu',      'navAdminChevron');
        
        // --- Theme Toggle Logic ---
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const themeToggleIcon = document.getElementById('themeToggleIcon');
        
        function updateThemeIcon() {
            if (document.documentElement.classList.contains('dark')) {
                themeToggleIcon.classList.remove('fa-moon');
                themeToggleIcon.classList.add('fa-sun');
            } else {
                themeToggleIcon.classList.remove('fa-sun');
                themeToggleIcon.classList.add('fa-moon');
            }
        }
        
        // Initial icon update
        if (themeToggleIcon) updateThemeIcon();
        
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.theme = 'light';
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.theme = 'dark';
                }
                updateThemeIcon();
            });
        }
    });
    </script>
</body>
</html>