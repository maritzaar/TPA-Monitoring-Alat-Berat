@extends('layouts.app')

@section('title', __('Kelola Pengguna'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
            <i class="fas fa-users text-[#8E6E4F] mr-2"></i>
            {{ __('Kelola Pengguna') }}
        </h2>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200">
                <thead class="bg-stone-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-stone-500 uppercase">{{ __('Nama') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-stone-500 uppercase">{{ __('Alamat Email') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-stone-500 uppercase">{{ __('Tanggal Daftar') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-stone-500 uppercase">{{ __('Peran') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-stone-500 uppercase">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-stone-150 text-sm">
                    @forelse($users as $user)
                    <tr class="hover:bg-stone-50/50">
                        <td class="px-4 py-3 font-medium text-stone-800">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-stone-500">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full font-semibold
                                @if($user->role === 'admin') bg-[#F5EBE0] text-[#704F37] border border-[#E6DCCF]
                                @else bg-stone-100 text-stone-600 border border-stone-200 @endif">
                                {{ $user->role === 'admin' ? __('Admin/Operator') : __('Viewer') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('users.toggle', $user->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('{{ __('Yakin ingin mengubah peran pengguna ini?') }}')">
                                @csrf
                                <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border transition
                                    @if($user->role === 'admin') bg-stone-100 text-stone-700 border-stone-300 hover:bg-stone-200
                                    @else bg-[#8E6E4F] text-white border-transparent hover:bg-[#7D5F43] @endif">
                                    @if($user->role === 'admin')
                                        <i class="fas fa-user-minus mr-1"></i> {{ __('Jadikan Viewer') }}
                                    @else
                                        <i class="fas fa-user-shield mr-1"></i> {{ __('Jadikan Admin') }}
                                    @endif
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-stone-400">
                            <i class="fas fa-inbox text-2xl block mb-2"></i>
                            {{ __('Belum ada pengguna lain terdaftar.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
