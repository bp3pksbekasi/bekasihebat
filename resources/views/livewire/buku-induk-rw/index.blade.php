<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Peta Kekuatan RW</h1>
        <p class="text-sm text-gray-500 mt-1">Manajemen terpadu profil dan struktur pemenangan di tingkat RW.</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 p-5">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Cari Desa/RW</label>
                <input type="text" wire:model.live.debounce.500ms="search" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2 px-3 text-sm" placeholder="Ketik kata kunci...">
            </div>
            
            @if(($this->accessScope['mode'] ?? 'global') !== 'dapil')
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Dapil</label>
                <select wire:model.live="selectedDapil" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2 px-3 text-sm">
                    <option value="">Semua Dapil</option>
                    @foreach($dapils as $dapil)
                        <option value="{{ $dapil }}">{{ $dapil }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Kecamatan</label>
                <select wire:model.live="selectedKecamatan" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2 px-3 text-sm">
                    <option value="">Semua Kecamatan</option>
                    @foreach($kecamatans as $kec)
                        <option value="{{ $kec }}">{{ $kec }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Desa/Kelurahan</label>
                <select wire:model.live="selectedDesa" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2 px-3 text-sm">
                    <option value="">Semua Desa</option>
                    @foreach($desas as $desa)
                        <option value="{{ $desa }}">{{ $desa }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Status Prioritas</label>
                <select wire:model.live="selectedStatus" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2 px-3 text-sm">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $key => $status)
                        <option value="{{ $key }}">{{ $status['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-[0.5]">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Tahun</label>
                <select wire:model.live="selectedTahun" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 py-2 px-3 text-sm">
                    @foreach ([2026, 2027, 2028, 2029] as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-5 text-white shadow-sm">
            <div class="text-xs font-medium uppercase tracking-wider opacity-90 mb-1">Target Korwe {{ $selectedTahun }}</div>
            <div class="text-3xl font-bold mb-1">{{ number_format($summary['target_korwe']) }}</div>
            <div class="text-xs opacity-85">Tercapai: {{ number_format($summary['tercapai_korwe']) }}</div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-5 text-white shadow-sm">
            <div class="text-xs font-medium uppercase tracking-wider opacity-90 mb-1">Target Korte {{ $selectedTahun }}</div>
            <div class="text-3xl font-bold mb-1">{{ number_format($summary['target_korte']) }}</div>
            <div class="text-xs opacity-85">Tercapai: {{ number_format($summary['tercapai_korte']) }}</div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-5 text-white shadow-sm">
            <div class="text-xs font-medium uppercase tracking-wider opacity-90 mb-1">Target Penggalang {{ $selectedTahun }}</div>
            <div class="text-3xl font-bold mb-1">{{ number_format($summary['target_penggalang']) }}</div>
            <div class="text-xs opacity-85">Tercapai: {{ number_format($summary['tercapai_penggalang']) }}</div>
        </div>
        @php
            $profilPct = $summary['total_rw'] > 0 ? round(($summary['profil_terisi'] / $summary['total_rw']) * 100) : 0;
        @endphp
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Profil RW Terisi</div>
            <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($summary['profil_terisi']) }} <span class="text-lg text-gray-400 font-normal">/ {{ number_format($summary['total_rw']) }}</span></div>
            <div class="flex items-center gap-2">
                <div class="w-full bg-gray-200 rounded-full h-1.5">
                    <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $profilPct }}%"></div>
                </div>
                <div class="text-xs text-gray-500 font-medium">{{ $profilPct }}%</div>
            </div>
            <div class="text-xs text-gray-400 mt-1.5">{{ number_format($summary['profil_lengkap']) }} profil lengkap</div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilayah</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Profil</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Korwe</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Korte</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Penggalang</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rws as $rw)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="text-sm font-bold text-gray-900">RW {{ str_pad($rw->nomor_rw, 3, '0', STR_PAD_LEFT) }}</div>
                                    @php $statusConfig = $rw->status_config; @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider" style="background-color: {{ $statusConfig['bg'] }}; color: {{ $statusConfig['text'] }};">
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-500 mt-0.5">{{ $rw->targetWilayah->desa }}, {{ $rw->targetWilayah->kecamatan }}</div>
                                <div class="text-xs text-gray-500 font-semibold mt-1 uppercase tracking-wide">{{ $rw->targetWilayah->dapil }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $profil = $profilRws[$rw->target_wilayah_id . '_' . ltrim((string) $rw->nomor_rw, '0')] ?? null;
                                @endphp
                                @if($profil && $profil->completion_percent > 0)
                                    <div class="flex items-center">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 max-w-[100px] mr-2">
                                            <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $profil->completion_percent }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-600">{{ $profil->completion_percent }}%</span>
                                    </div>
                                    <div class="text-xs mt-1 {{ $profil->is_complete ? 'text-green-600 font-medium' : 'text-orange-500' }}">
                                        {{ $profil->is_complete ? 'Lengkap' : 'Belum Lengkap' }}
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Belum Diisi
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full {{ $rw->korwe_count > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-800' }} font-bold text-sm">
                                    {{ $rw->korwe_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full {{ $rw->korte_count > 0 ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-200 text-gray-800' }} font-bold text-sm">
                                    {{ $rw->korte_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm font-bold {{ $rw->penggalang_count > 0 ? 'text-emerald-700' : 'text-gray-800' }}">
                                    {{ $rw->penggalang_count }}
                                </div>
                                <div class="text-xs text-gray-600">Target: {{ $rw->target_penggalang }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('buku-induk-rw.detail', $rw->id) }}" wire:navigate class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                                    Buka Peta Kekuatan
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <span class="block text-base font-medium text-gray-900">Tidak ada data RW</span>
                                <span class="block mt-1 text-sm">Gunakan filter pencarian di atas untuk menemukan wilayah.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($rws->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $rws->links() }}
            </div>
        @endif
    </div>
</div>
