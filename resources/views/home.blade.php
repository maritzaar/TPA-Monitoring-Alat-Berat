@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden no-print min-h-[calc(100vh-12rem)] flex items-center">
    <div class="flex flex-col md:flex-row items-center justify-between p-6 md:p-12">
        
        <!-- Left Side: Illustration -->
        <div class="w-full md:w-1/2 flex justify-center mb-8 md:mb-0">
            <img src="{{ asset('images/logo.png') }}" alt="Teladan Prima Agro" class="max-w-[250px] md:max-w-xs object-contain">
        </div>
        
        <!-- Right Side: Text -->
        <div class="w-full md:w-1/2 text-center md:text-left flex flex-col justify-center">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-slate-800 mb-4" style="color: #c81e28;">
                Selamat Datang di Teladan Prima Agro
            </h1>
            <p class="text-slate-500 text-sm md:text-base max-w-lg mx-auto md:mx-0">
                Pilih menu di samping kiri untuk melihat monitoring secara terperinci.
            </p>
        </div>
        
    </div>
</div>
@endsection
