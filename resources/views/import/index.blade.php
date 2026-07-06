@extends('layouts.app')

@section('title', __('Import Data'))

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">
        <i class="fas fa-upload text-[#8E6E4F] mr-2"></i> {{ __('Import Data Excel') }}
    </h2>

    <!-- Form Import -->
    <div class="bg-blue-50 p-6 rounded-lg mb-8 border border-blue-200">
        <form action="{{ route('import.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Sumber Data') }}</label>
                    <select name="sumber" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#8E6E4F] focus:ring-[#8E6E4F] p-2 border">
                        <option value="CATERPILLAR">CATERPILLAR</option>
                        <option value="INTERNAL">INTERNAL</option>
                        <option value="SAP">SAP</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('File Excel') }}</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-[#8E6E4F] hover:file:bg-[#FAF7F2]">
                    <p class="text-xs text-gray-500 mt-1">{{ __('Format: .xlsx, .xls, .csv') }}</p>
                </div>
            </div>
            <div class="flex space-x-4">
                <button type="submit" class="bg-[#8E6E4F] text-white px-6 py-2 rounded-lg hover:bg-[#7D5F43] transition">
                    <i class="fas fa-upload mr-2"></i> {{ __('Import Data') }}
                </button>
                <a href="{{ route('import.clear') }}" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition"
                   onclick="return confirm('{{ __('Yakin ingin menghapus semua data?') }}')">
                    <i class="fas fa-trash mr-2"></i> {{ __('Hapus Semua') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Riwayat Unggah File -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">
            <i class="fas fa-history text-[#8E6E4F] mr-2"></i> {{ __('Riwayat Unggah File') }}
        </h3>
        <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nama File') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Sumber') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Baris Ditambahkan') }}</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Tanggal Unggah') }}</th>
                        <th class="px-4 py-2.5 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                    @forelse($history as $log)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-700">
                            <i class="far fa-file-excel text-green-600 mr-2"></i>{{ $log->filename }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-xs rounded-full 
                                @if($log->sumber == 'CATERPILLAR') bg-blue-50 text-blue-700 border border-blue-100
                                @elseif($log->sumber == 'INTERNAL') bg-green-50 text-green-700 border border-green-100
                                @else bg-purple-50 text-purple-700 border border-purple-100 @endif">
                                {{ $log->sumber }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 font-semibold">{{ number_format($log->rows_count) }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $log->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('import.delete-log', $log->id) }}" method="POST" 
                                  onsubmit="return confirm('{{ __('Yakin ingin menghapus data dari file ini?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 transition inline-flex items-center text-xs font-semibold">
                                    <i class="fas fa-trash mr-1"></i> {{ __('Hapus Data') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-400 text-xs">{{ __('Belum ada riwayat unggahan.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel Data -->
    <div>
        <h3 class="text-lg font-semibold mb-4 text-gray-700">
            <i class="fas fa-database text-[#8E6E4F] mr-2"></i> {{ __('Data Tersimpan') }}
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Tanggal') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('ID Aset') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Model') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Sumber') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Waktu Kerja') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Waktu Operasi') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('% Idle') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($data as $item)
                    <tr>
                        <td class="px-4 py-3 text-sm">{{ $item->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-sm font-medium">{{ $item->id_aset }}</td>
                        <td class="px-4 py-3 text-sm">{{ $item->model }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($item->sumber_data == 'CATERPILLAR') bg-blue-100 text-blue-800
                                @elseif($item->sumber_data == 'INTERNAL') bg-green-100 text-green-800
                                @else bg-purple-100 text-purple-800 @endif">
                                {{ $item->sumber_data }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ number_format($item->waktu_kerja, 2) }}</td>
                        <td class="px-4 py-3 text-sm">{{ number_format($item->waktu_operasi, 2) }}</td>
                        <td class="px-4 py-3 text-sm">{{ number_format($item->persen_idle, 2) }}%</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-2xl block mb-2"></i>
                            {{ __('Belum ada data. Silakan import file Excel.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $data->links() }}
        </div>
    </div>
</div>
@endsection