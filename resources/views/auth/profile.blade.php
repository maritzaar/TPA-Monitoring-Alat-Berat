@extends('layouts.app')

@section('title', __('Edit Profil'))

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <h2 class="text-xl font-bold mb-6 text-slate-800 flex items-center">
            <i class="fas fa-user-cog text-blue-600 mr-2"></i>
            {{ __('Edit Profil') }}
        </h2>

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-100 text-rose-800 p-4 mb-6 rounded-xl shadow-sm">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Personal Info -->
            <div class="space-y-4 text-left">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider border-b pb-2">
                    {{ __('Informasi Profil') }}
                </h3>
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('Nama Lengkap') }}</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $user->name) }}"
                           class="w-full rounded-lg border border-slate-300 bg-slate-50 text-slate-800 py-2.5 px-3 focus:border-blue-600 focus:ring-blue-600 focus:outline-none text-sm">
                </div>
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-600 mb-1.5">{{ __('Username') }}</label>
                    <input type="email" name="email" id="email" readonly value="{{ $user->email }}"
                           class="w-full rounded-lg border border-slate-200 bg-slate-100 text-slate-500 py-2.5 px-3 cursor-not-allowed text-sm focus:outline-none"
                           title="{{ __('Username tidak dapat diubah') }}">
                    <p class="text-[11px] text-slate-450 mt-1"><i class="fas fa-info-circle mr-1"></i>{{ __('Username tidak dapat diubah.') }}</p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-slate-100">
                <a href="{{ route('monitoring.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-lg transition text-sm font-semibold">
                    {{ __('Batal') }}
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg transition text-sm font-semibold shadow-sm">
                    {{ __('Simpan Perubahan') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
