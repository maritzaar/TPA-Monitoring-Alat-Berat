<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Monitoring Alat Berat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-[#8E6E4F] via-[#A7825E] to-[#B49A80] min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute w-96 h-96 rounded-full bg-[#FAF7F2]/10 -top-12 -left-12 blur-3xl"></div>
    <div class="absolute w-96 h-96 rounded-full bg-[#8E6E4F]/10 -bottom-12 -right-12 blur-3xl"></div>
 
    <div class="w-full max-w-md bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-2xl p-8 relative z-10">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <div class="inline-flex p-3 bg-[#8E6E4F]/30 rounded-2xl border border-white/20 text-[#FAF7F2] mb-3 shadow-lg shadow-[#8E6E4F]/10">
                <i class="fas fa-shield-halved text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-white tracking-wide">{{ __('Selamat Datang Kembali') }}</h2>
            <p class="text-stone-200 text-sm mt-1">{{ __('Masuk untuk mengakses dashboard monitoring') }}</p>
        </div>
 
        <!-- Success/Error Banner -->
        @if(session('success'))
            <div class="bg-emerald-500/20 border border-emerald-500/30 text-emerald-100 p-3.5 mb-6 rounded-xl text-sm flex items-center">
                <i class="fas fa-check-circle mr-2.5"></i> {{ session('success') }}
            </div>
        @endif
 
        @if($errors->any())
            <div class="bg-rose-500/20 border border-rose-500/30 text-rose-100 p-3.5 mb-6 rounded-xl text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
 
        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            
            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs font-semibold text-[#FAF7F2] uppercase tracking-wider mb-2">{{ __('Alamat Email') }}</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-stone-300">
                        <i class="far fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}"
                           class="block w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-stone-300 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-white transition text-sm"
                           placeholder="nama@email.com">
                </div>
            </div>
 
            <!-- Password Input -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label for="password" class="block text-xs font-semibold text-[#FAF7F2] uppercase tracking-wider">{{ __('Kata Sandi') }}</label>
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-stone-300">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" required
                           class="block w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-stone-300 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-white transition text-sm"
                           placeholder="••••••••">
                </div>
            </div>
 
            <!-- Remember Me -->
            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" 
                       class="h-4 w-4 rounded bg-white/10 border-white/20 text-[#8E6E4F] focus:ring-[#8E6E4F]/50 focus:ring-offset-stone-900">
                <label for="remember" class="ml-2 block text-sm text-stone-200">{{ __('Ingat saya di perangkat ini') }}</label>
            </div>
 
            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full py-3 bg-gradient-to-r from-[#8E6E4F] to-[#7D5F43] hover:from-[#7D5F43] hover:to-[#8E6E4F] text-white font-semibold rounded-xl transition duration-300 transform active:scale-98 shadow-lg shadow-[#8E6E4F]/20 flex items-center justify-center">
                {{ __('Masuk') }} <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </button>
        </form>
 
        <!-- Redirect Link -->
        <div class="mt-8 text-center text-sm">
            <span class="text-stone-200">{{ __('Belum punya akun?') }}</span>
            <a href="{{ route('register') }}" class="text-[#FAF7F2] hover:text-white font-semibold ml-1 transition hover:underline">{{ __('Daftar sekarang') }}</a>
        </div>
    </div>
</body>
</html>
