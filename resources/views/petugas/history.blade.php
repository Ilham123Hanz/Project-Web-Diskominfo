@extends('layouts.petugas')

@section('content')
<div class="container mx-auto px-6 py-8">
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Riwayat Log Patroli Personal</h1>
                <p class="text-sm text-slate-500 mt-1">Pantau status laporan yang telah Anda kirimkan ke Admin.</p>
            </div>
            
            <form action="{{ route('petugas.patrol.history') }}" method="GET" class="flex flex-col sm:flex-row gap-3 mt-4 md:mt-0">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari OPD atau ID Log..." 
                    class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-64"
                >
                
                <select 
                    name="status" 
                    onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Semua Status</option>
                    <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Disetujui Admin</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Menunggu Validasi</option>
                    <option value="Rejection" {{ request('status') == 'Rejection' ? 'selected' : '' }}>Perlu Perbaikan</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-100 text-slate-600 font-semibold text-xs tracking-wider uppercase">
                        <th class="px-6 py-4">Tgl / ID Log</th>
                        <th class="px-6 py-4">OPD Sasaran</th>
                        <th class="px-6 py-4">Kategori Insiden</th>
                        <th class="px-6 py-4">Status Validasi</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($patrols as $patrol)
                        <tr class="{{ $patrol->status == 'Rejection' ? 'bg-red-50/50' : 'hover:bg-slate-50/80' }} transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-slate-700 block">{{ \Carbon\Carbon::parse($patrol->date_log)->format('d Jun Y') }}</span>
                                <span class="text-xs text-slate-400 font-mono">{{ $patrol->id_log }}</span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-700">
                                {{ $patrol->opd_sasaran }}
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ $patrol->kategori_insiden }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($patrol->status == 'Approved')
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Disetujui Admin</span>
                                @elseif($patrol->status == 'Pending')
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Menunggu Validasi</span>
                                @elseif($patrol->status == 'Rejection')
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Perlu Perbaikan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($patrol->status == 'Rejection')
                                    <a href="{{ route('petugas.patrol.edit', $patrol->id) }}" class="inline-block px-4 py-1.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors shadow-sm">
                                        Edit Revisi
                                    </a>
                                @else
                                    <a href="{{ route('petugas.patrol.show', $patrol->id) }}" class="inline-block text-xs font-bold text-blue-600 hover:underline">
                                        Lihat Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 italic">
                                Belum ada riwayat laporan patroli yang terekam.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $patrols->links() }}
        </div>
    </div>
</div>
@endsection