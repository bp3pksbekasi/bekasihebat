<div>
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('buku-induk-rw.index') }}" wire:navigate class="text-sm font-medium text-gray-500 hover:text-gray-700">
                    &larr; Kembali
                </a>
                <span class="text-gray-300">/</span>
                <span class="text-sm font-medium text-gray-500">{{ $dataRw->targetWilayah->dapil }}</span>
                <span class="text-gray-300">/</span>
                <span class="text-sm font-medium text-gray-500">{{ $dataRw->targetWilayah->kecamatan }}</span>
                <span class="text-gray-300">/</span>
                <span class="text-sm font-medium text-gray-500">{{ $dataRw->targetWilayah->desa }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight flex items-center gap-3">
                Peta Kekuatan RW {{ str_pad($dataRw->nomor_rw, 3, '0', STR_PAD_LEFT) }}
                @if($profilRw && $profilRw->is_complete)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800">
                        Profil Lengkap
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-orange-100 text-orange-800">
                        Profil Belum Lengkap
                    </span>
                @endif
            </h1>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2 items-start sm:items-center">
            <!-- Peringatan Jika Belum Ada Korwe -->
            @if($korweCount === 0)
                <div wire:key="warning-no-korwe" class="bg-red-50 text-red-700 px-4 py-2 rounded-lg text-sm border border-red-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Belum ada Korwe!
                </div>
            @endif
            
            <button wire:key="btn-open-drawer" wire:click="tambahInfrastruktur" class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Input Korwe / Korte
            </button>
        </div>
    </div>

    <!-- Drawer Input Infrastruktur (Korwe/Korte/Penggalang) -->
    <div x-data="{ openInfraDrawer: false }" 
         @open-infra-drawer.window="openInfraDrawer = true" 
         @keydown.escape.window="openInfraDrawer = false" 
         x-effect="if(openInfraDrawer) document.body.style.overflow = 'hidden'; else document.body.style.overflow = '';"
         x-show="openInfraDrawer" 
         style="display: none;" 
         class="relative z-[60]" 
         aria-labelledby="slide-over-title" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="openInfraDrawer" 
             x-transition.opacity 
             class="fixed inset-0 bg-black bg-opacity-30 transition-opacity" 
             @click="openInfraDrawer = false"></div>

        <!-- Drawer Panel -->
        <div x-show="openInfraDrawer" 
             x-transition:enter="transform transition ease-in-out duration-300" 
             x-transition:enter-start="translate-x-full" 
             x-transition:enter-end="translate-x-0" 
             x-transition:leave="transform transition ease-in-out duration-300" 
             x-transition:leave-start="translate-x-0" 
             x-transition:leave-end="translate-x-full" 
             class="fixed top-0 bottom-0 right-0 w-[480px] max-w-[100vw] h-full bg-white shadow-[-4px_0_20px_rgba(0,0,0,0.1)] overflow-y-auto custom-scrollbar">
            
            <!-- Header -->
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-start sticky top-0 bg-white z-10">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $infraId ? 'Edit Infrastruktur' : 'Input Infrastruktur' }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Korwe, Korte, dan Penggalang</p>
                </div>
                <button @click="openInfraDrawer = false" class="p-1.5 bg-gray-50 text-gray-400 hover:text-gray-600 rounded-md transition-colors border border-transparent hover:border-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Content Placeholder -->
            <div class="p-5">
                <div class="bg-blue-50 text-blue-700 p-4 rounded-lg text-sm mb-5 border border-blue-100 font-medium">
                    Formulir cepat untuk menambahkan data infrastruktur pemenangan di RW {{ str_pad($dataRw->nomor_rw, 3, '0', STR_PAD_LEFT) }}.
                </div>

                <form wire:submit.prevent="simpanInfrastruktur" class="space-y-4" x-data="{ type: @entangle('infraType') }">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">Jenis Infrastruktur</label>
                        <select wire:model="infraType" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none text-sm py-3 px-3">
                            <option value="korwe">Korwe (Koordinator RW)</option>
                            <option value="korte">Korte (Koordinator RT)</option>
                            <option value="penggalang">Penggalang Suara</option>
                        </select>
                        @error('infraType') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div x-show="type === 'korte' || type === 'penggalang'" x-cloak>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">Nomor RT (Contoh: 001)</label>
                        <input type="text" wire:model="infraRt" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none text-sm py-3 px-3" placeholder="001">
                        @error('infraRt') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" wire:model="infraNama" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none text-sm py-3 px-3" placeholder="Contoh: Budi Santoso">
                        @error('infraNama') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">No. HP / WhatsApp</label>
                        <input type="text" wire:model="infraHp" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none text-sm py-3 px-3" placeholder="08123456789">
                        @error('infraHp') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div x-show="type === 'penggalang'" x-cloak>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">Target Pemilih (Opsional)</label>
                        <input type="number" wire:model="infraTarget" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none text-sm py-3 px-3" placeholder="Jumlah target suara">
                        @error('infraTarget') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="pt-4 mt-6 border-t border-gray-100">
                        <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3.5 rounded-lg transition-colors shadow-sm flex items-center justify-center gap-2" wire:loading.attr="disabled">
                            <span wire:key="span-idle" wire:loading.remove wire:target="simpanInfrastruktur">
                                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Simpan Data
                            </span>
                            <span wire:key="span-loading" wire:loading wire:target="simpanInfrastruktur">
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
        <nav class="flex w-full px-2 sm:px-6 min-w-max" aria-label="Tabs">
            <button wire:click="setActiveTab('profil_wilayah')" class="{{ $activeTab === 'profil_wilayah' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }} flex-1 whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm flex justify-center items-center transition-colors cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 {{ $activeTab === 'profil_wilayah' ? 'text-orange-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Profil Wilayah
            </button>
            <button wire:click="setActiveTab('peta_politik')" class="{{ $activeTab === 'peta_politik' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }} flex-1 whitespace-nowrap py-4 px-2 border-b-2 font-medium text-sm flex justify-center items-center transition-colors cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 {{ $activeTab === 'peta_politik' ? 'text-orange-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Peta Politik Lokal
            </button>
            <button wire:click="setActiveTab('strategi')" class="{{ $activeTab === 'strategi' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }} flex-1 whitespace-nowrap py-4 px-2 border-b-2 font-medium text-sm flex justify-center items-center transition-colors cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 {{ $activeTab === 'strategi' ? 'text-orange-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Strategi & Penanggung Jawab
            </button>
            <button wire:click="setActiveTab('struktur')" class="{{ $activeTab === 'struktur' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }} flex-1 whitespace-nowrap py-4 px-2 border-b-2 font-medium text-sm flex justify-center items-center transition-colors cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 {{ $activeTab === 'struktur' ? 'text-orange-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Infrastruktur Pemenangan
            </button>
            <button wire:click="setActiveTab('realisasi')" class="{{ $activeTab === 'realisasi' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 hover:bg-gray-50' }} flex-1 whitespace-nowrap py-4 px-2 border-b-2 font-medium text-sm flex justify-center items-center transition-colors cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 {{ $activeTab === 'realisasi' ? 'text-orange-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Target & Realisasi
            </button>
        </nav>
    </div>

    <!-- Tab Contents -->
    <div class="space-y-6">
        @if(in_array($activeTab, ['profil_wilayah', 'peta_politik', 'strategi']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                @if(!$profilRw)
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900">Profil RW Belum Diisi</h3>
                        <p class="mt-1 text-sm text-gray-500">Data profil sosiologis dan peta politik untuk RW ini belum tersedia.</p>
                        <div class="mt-6">
                            <button wire:click="openProfilDrawer" type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Isi Profil Sekarang
                            </button>
                        </div>
                    </div>
                @else
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900">
                            @if($activeTab === 'profil_wilayah') Profil Wilayah @endif
                            @if($activeTab === 'peta_politik') Peta Politik Lokal @endif
                            @if($activeTab === 'strategi') Strategi & Penanggung Jawab @endif
                        </h3>
                        <button wire:click="openProfilDrawer" type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Profil
                        </button>
                    </div>

                    @if($activeTab === 'profil_wilayah')
                        <!-- Data Demografi -->
                        <div class="mb-8">
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                                <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                                    <div class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider mb-1">DPT Wilayah</div>
                                    <div class="text-xl font-bold text-gray-900">{{ number_format($dataRw->dpt ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                                    <div class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider mb-1">Estimasi PKS</div>
                                    <div class="text-xl font-bold text-orange-600">~{{ number_format($dataRw->estimasi_pks ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                                    <div class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider mb-1">Laki-Laki</div>
                                    <div class="text-xl font-bold text-gray-900">{{ number_format($dataRw->dpt_laki ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                                    <div class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider mb-1">Perempuan</div>
                                    <div class="text-xl font-bold text-gray-900">{{ number_format($dataRw->dpt_perempuan ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                                    <div class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider mb-1">Share PKS</div>
                                    <div class="text-xl font-bold text-gray-900">{{ number_format($dataRw->estimasi_share ?? 0, 1, ',', '.') }}%</div>
                                </div>
                                <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                                    <div class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider mb-1">TPS Terlibat</div>
                                    <div class="text-xl font-bold text-gray-900">{{ number_format($dataRw->jumlah_tps ?? 0, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                                <div class="text-sm font-bold text-gray-900 mb-5 uppercase tracking-wide">Struktur Demografi</div>
                                
                                <!-- Gender Bar -->
                                @php
                                    $dpt = max(1, $dataRw->dpt ?? 1);
                                    $pctLaki = round((($dataRw->dpt_laki ?? 0) / $dpt) * 100, 1);
                                    $pctPr = round((($dataRw->dpt_perempuan ?? 0) / $dpt) * 100, 1);
                                @endphp
                                <div class="mb-4">
                                    <div class="h-2.5 w-full bg-gray-200 rounded-full flex overflow-hidden mb-1.5">
                                        <div class="bg-blue-500 h-full" style="width: {{ $pctLaki }}%"></div>
                                        <div class="bg-pink-500 h-full" style="width: {{ $pctPr }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-500">
                                        <span>Laki-laki {{ number_format($pctLaki, 1, ',', '.') }}%</span>
                                        <span>Perempuan {{ number_format($pctPr, 1, ',', '.') }}%</span>
                                    </div>
                                </div>
                                
                                <!-- Generation Bars -->
                                @php
                                    $pctGenZ = round((($dataRw->gen_z ?? 0) / $dpt) * 100, 1);
                                    $pctMillennial = round((($dataRw->millennial ?? 0) / $dpt) * 100, 1);
                                    $pctGenX = round((($dataRw->gen_x ?? 0) / $dpt) * 100, 1);
                                    $pctBoomer = round((($dataRw->boomer ?? 0) / $dpt) * 100, 1);
                                @endphp
                                <div class="space-y-3">
                                    <div>
                                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                                            <span>Gen Z</span>
                                            <span class="text-gray-500">{{ number_format($pctGenZ, 1, ',', '.') }}%</span>
                                        </div>
                                        <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                            <div class="bg-purple-400 h-full rounded-full" style="width: {{ $pctGenZ }}%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                                            <span>Millennial</span>
                                            <span class="text-gray-500">{{ number_format($pctMillennial, 1, ',', '.') }}%</span>
                                        </div>
                                        <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                            <div class="bg-orange-500 h-full rounded-full" style="width: {{ $pctMillennial }}%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                                            <span>Gen X</span>
                                            <span class="text-gray-500">{{ number_format($pctGenX, 1, ',', '.') }}%</span>
                                        </div>
                                        <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                            <div class="bg-green-500 h-full rounded-full" style="width: {{ $pctGenX }}%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                                            <span>Boomer</span>
                                            <span class="text-gray-500">{{ number_format($pctBoomer, 1, ',', '.') }}%</span>
                                        </div>
                                        <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                            <div class="bg-slate-400 h-full rounded-full" style="width: {{ $pctBoomer }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                            <div class="border-b border-gray-200 pb-3 mb-5">
                                <h4 class="font-bold text-gray-900 text-base">Rincian Profil Sosiologis</h4>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <span class="text-gray-500 block text-[10px] sm:text-xs mb-1 uppercase tracking-wider font-bold">Tipologi Wilayah</span>
                                    <div class="font-medium text-gray-900 text-sm sm:text-base">{{ $profilRw->tipologi ? \App\Models\ProfilRw::TIPOLOGI_OPTIONS[$profilRw->tipologi] ?? $profilRw->tipologi : '-' }}</div>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <span class="text-gray-500 block text-[10px] sm:text-xs mb-1 uppercase tracking-wider font-bold">Ekonomi Dominan</span>
                                    <div class="font-medium text-gray-900 text-sm sm:text-base">{{ $profilRw->ekonomi_dominan ? \App\Models\ProfilRw::EKONOMI_OPTIONS[$profilRw->ekonomi_dominan] ?? $profilRw->ekonomi_dominan : '-' }}</div>
                                </div>
                                <div class="col-span-1 md:col-span-2 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <span class="text-gray-500 block text-[10px] sm:text-xs mb-1 uppercase tracking-wider font-bold">Profil Umum Warga</span>
                                    <div class="font-medium text-gray-900 text-sm sm:text-base leading-relaxed">{{ $profilRw->profil_warga ?: '-' }}</div>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <span class="text-gray-500 block text-[10px] sm:text-xs mb-1 uppercase tracking-wider font-bold">Suara PKS 2019</span>
                                    <div class="font-bold text-orange-600 text-base sm:text-lg">{{ number_format($profilRw->suara_pks_2019 ?? 0) }}</div>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <span class="text-gray-500 block text-[10px] sm:text-xs mb-1 uppercase tracking-wider font-bold">Jumlah KTA</span>
                                    <div class="font-medium text-gray-900 text-sm sm:text-base">{{ number_format($profilRw->jumlah_kta ?? 0) }}</div>
                                </div>
                                <div class="col-span-1 md:col-span-2 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <span class="text-gray-500 block text-[10px] sm:text-xs mb-1 uppercase tracking-wider font-bold">Faktor Penyebab Menang/Kalah</span>
                                    <div class="font-medium text-gray-900 text-sm sm:text-base leading-relaxed">{{ $profilRw->faktor_penyebab ?: '-' }}</div>
                                </div>
                            </div>
                        </div>
                    @elseif($activeTab === 'peta_politik')
                        <div x-data="{ 
                            showTpsModal: false, 
                            showRtModal: false,
                            showPartyDrawer: false, 
                            selectedPartyIdx: 0,
                            partyRows: {{ json_encode($partyRows) }},
                            rtRows: {{ json_encode(array_values($rtRows ?? [])) }},
                            partyColors: {{ json_encode([
                                'PKS' => '#fe5000', 'GERINDRA' => '#b39d82', 'GOLKAR' => '#eab308',
                                'PDIP' => '#ef4444', 'PKB' => '#16a34a', 'DEMOKRAT' => '#1d4ed8',
                                'PAN' => '#0ea5e9', 'PPP' => '#22c55e', 'NASDEM' => '#1e3a8a',
                                'GELORA' => '#be123c', 'UMMAT' => '#000000', 'BURUH' => '#ea580c',
                                'PSI' => '#dc2626', 'PERINDO' => '#0284c7', 'HANURA' => '#d97706',
                                'PBB' => '#15803d', 'GARUDA' => '#b91c1c', 'PKN' => '#991b1b'
                            ]) }}
                        }" x-effect="document.body.style.overflow = (showTpsModal || showRtModal || showPartyDrawer) ? 'hidden' : ''">
                            <div class="mb-6">
                                @if (!empty($partyRows))
                                @php
                                    $partyColors = [
                                        'PKS' => '#fe5000', 'GERINDRA' => '#b39d82', 'GOLKAR' => '#eab308',
                                        'PDIP' => '#ef4444', 'PKB' => '#16a34a', 'DEMOKRAT' => '#1d4ed8',
                                        'PAN' => '#0ea5e9', 'PPP' => '#22c55e', 'NASDEM' => '#1e3a8a',
                                        'GELORA' => '#be123c', 'UMMAT' => '#000000', 'BURUH' => '#ea580c',
                                        'PSI' => '#dc2626', 'PERINDO' => '#0284c7', 'HANURA' => '#d97706',
                                        'PBB' => '#15803d', 'GARUDA' => '#b91c1c', 'PKN' => '#991b1b'
                                    ];

                                    $pksRow = null;
                                    $rivalRow = null;
                                    foreach($partyRows as $idx => $row) {
                                        $pName = strtoupper($row['party_name'] ?? $row['partai'] ?? '');
                                        if ($pName === 'PKS') {
                                            $pksRow = $row;
                                            $rivalRow = ($idx === 0) ? ($partyRows[1] ?? null) : ($partyRows[0] ?? null);
                                            break;
                                        }
                                    }

                                    // Fallback if PKS not found
                                    if (!$pksRow && count($partyRows) > 0) {
                                        $rivalRow = $partyRows[0];
                                    }

                                    $pksShare = $pksRow['share'] ?? 0;
                                    $rivalShare = $rivalRow['share'] ?? 0;
                                    $rivalName = $rivalRow['party_name'] ?? $rivalRow['partai'] ?? '-';
                                    $rivalVotes = $rivalRow['votes'] ?? $rivalRow['suara'] ?? 0;
                                    $gap = $pksShare - $rivalShare;
                                    $gapFormatted = ($gap > 0 ? '+' : '') . number_format($gap * 100, 1, ',', '.') . '%';
                                    $gapColor = $gap > 0 ? 'text-green-600' : ($gap < 0 ? 'text-red-600' : 'text-gray-900');
                                @endphp

                                <!-- Summary Rival -->
                                <div class="flex flex-nowrap overflow-x-auto gap-4 mb-6 pb-2 custom-scrollbar">
                                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm min-w-[140px] flex-1">
                                        <div class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider mb-1">Share PKS</div>
                                        <div class="text-xl font-bold text-gray-900">{{ number_format($pksShare * 100, 1, ',', '.') }}%</div>
                                    </div>
                                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm min-w-[160px] flex-1">
                                        <div class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider mb-1">Rival Terkuat</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $partyColors[strtoupper($rivalName)] ?? '#94a3b8' }}"></span>
                                                {{ $rivalName }}
                                            </span>
                                        </div>
                                        <div class="text-[10px] text-gray-500 mt-1">(~{{ number_format($rivalVotes, 0, ',', '.') }} suara)</div>
                                    </div>
                                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm min-w-[140px] flex-1">
                                        <div class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider mb-1">Selisih (Gap)</div>
                                        <div class="text-xl font-bold {{ $gapColor }}">{{ $gapFormatted }}</div>
                                    </div>
                                    <div @click="showRtModal = true" class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm cursor-pointer hover:bg-gray-50 transition-colors group relative min-w-[140px] flex-1">
                                        <div class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider mb-1 flex justify-between items-center">
                                            <span>Jumlah RT</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-400 group-hover:text-orange-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                        </div>
                                        <div class="text-xl font-bold text-gray-900">{{ number_format($dataRw->jumlah_rt ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                    <div @click="showTpsModal = true" class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm cursor-pointer hover:bg-gray-50 transition-colors group relative min-w-[140px] flex-1">
                                        <div class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider mb-1 flex justify-between items-center">
                                            <span>TPS Terlibat</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-400 group-hover:text-orange-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                        </div>
                                        <div class="text-xl font-bold text-gray-900">{{ number_format($dataRw->jumlah_tps ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>

                                <div class="mb-4 text-xs text-orange-600 font-bold uppercase tracking-wider">Peta Persaingan Partai</div>
                                <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 overflow-hidden">
                                    @php
                                        $maxShare = !empty($partyRows) ? max(array_column($partyRows, 'share')) : 1;
                                    @endphp
                                    <div class="flex flex-row gap-3 overflow-x-auto pb-4 pt-10 px-2 custom-scrollbar items-end h-[320px]">
                                        @foreach ($partyRows as $idx => $tp)
                                            @php
                                                $partyName = $tp['party_name'] ?? $tp['partai'] ?? '-';
                                                $votes = $tp['votes'] ?? $tp['suara'] ?? 0;
                                                $share = $tp['share'] ?? 0;
                                                $color = $partyColors[strtoupper($partyName)] ?? '#94a3b8';
                                                $isPks = strtoupper($partyName) === 'PKS';
                                                $barHeight = $maxShare > 0 ? max(2, ($share / $maxShare) * 100) : 2;
                                            @endphp
                                            <div class="flex flex-col items-center justify-end h-full min-w-[70px] group relative">
                                                
                                                <!-- Tooltip above bar -->
                                                <div class="absolute bottom-[calc({{ $barHeight }}%+4rem)] flex flex-col items-center transition-all duration-300 opacity-0 group-hover:opacity-100 group-hover:-translate-y-2 z-10 bg-gray-900 text-white px-2 py-1.5 rounded-md shadow-lg pointer-events-none whitespace-nowrap">
                                                    <span class="text-xs font-bold">{{ number_format($votes, 0, ',', '.') }} Suara</span>
                                                    <span class="text-[10px] text-gray-300">{{ number_format($share * 100, 1, ',', '.') }}%</span>
                                                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45"></div>
                                                </div>

                                                <!-- Always visible percentage -->
                                                <div class="text-[10px] font-bold mb-1.5 transition-colors {{ $isPks ? 'text-orange-600' : 'text-gray-500' }}">
                                                    {{ number_format($share * 100, 1, ',', '.') }}%
                                                </div>

                                                <!-- The Bar -->
                                                <div class="w-12 rounded-t-md relative flex items-end justify-center transition-all duration-500 shadow-sm border border-b-0 border-black/5"
                                                     style="height: {{ $barHeight }}%; background-color: {{ $color }};">
                                                     
                                                     <!-- Overlay for 3D effect -->
                                                     <div class="absolute inset-0 rounded-t-md bg-gradient-to-t from-black/10 to-transparent"></div>
                                                </div>
                                                
                                                <!-- X-Axis Label -->
                                                <div class="mt-3 flex flex-col items-center w-full border-t border-gray-200 pt-2">
                                                    <div @click="selectedPartyIdx = {{ $idx }}; showPartyDrawer = true" class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold text-white mb-1 shadow-sm cursor-pointer hover:scale-110 hover:shadow-md transition-all" style="background-color: {{ $color }}" title="Klik untuk detail partai">
                                                        {{ $idx + 1 }}
                                                    </div>
                                                    <span class="text-[10px] font-bold text-center {{ $isPks ? 'text-orange-600' : 'text-gray-700' }} line-clamp-1 w-full px-1">
                                                        {{ $partyName }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="col-span-1 md:col-span-2 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <span class="text-gray-500 block text-[10px] sm:text-xs mb-1 uppercase tracking-wider font-bold">Afiliasi Ketua RW & RT</span>
                                    <div class="font-medium text-gray-900 text-sm sm:text-base leading-relaxed whitespace-pre-line">{{ $profilRw->afiliasi_rw_rt ?: '-' }}</div>
                                </div>
                                <div class="col-span-1 md:col-span-2 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <span class="text-gray-500 block text-[10px] sm:text-xs mb-1 uppercase tracking-wider font-bold">Afiliasi Kader Posyandu & DKM</span>
                                    <div class="font-medium text-gray-900 text-sm sm:text-base leading-relaxed whitespace-pre-line">{{ $profilRw->afiliasi_posyandu_dkm ?: '-' }}</div>
                                </div>
                                
                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <span class="text-gray-500 block text-[10px] sm:text-xs mb-1 uppercase tracking-wider font-bold">Pengurus Kompetitor</span> 
                                    <div class="font-medium text-gray-900 text-sm sm:text-base">
                                        @if($profilRw->kompetitor_status === 'ada')
                                            <span class="text-red-600 font-bold">Ada:</span> <span class="text-gray-800">{{ $profilRw->kompetitor_detail }}</span>
                                        @elseif($profilRw->kompetitor_status === 'tidak')
                                            Tidak Ada
                                        @else
                                            Tidak Tahu
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <span class="text-gray-500 block text-[10px] sm:text-xs mb-1 uppercase tracking-wider font-bold">Tim Sukses Lain</span> 
                                    <div class="font-medium text-gray-900 text-sm sm:text-base">
                                        @if($profilRw->tim_sukses_status === 'ada')
                                            <span class="text-red-600 font-bold">Ada:</span> <span class="text-gray-800">{{ $profilRw->tim_sukses_detail }}</span>
                                        @elseif($profilRw->tim_sukses_status === 'tidak')
                                            Tidak Ada
                                        @else
                                            Tidak Tahu
                                        @endif
                                    </div>
                                </div>

                                @if (!empty($tpsRows))
                                    <!-- Drawer TPS Terlibat -->
                                    <!-- Drawer TPS Terlibat -->
                                    <div x-show="showTpsModal" style="display: none;" class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
                                        
                                        <!-- Backdrop -->
                                        <div x-show="showTpsModal" 
                                             x-transition:enter="ease-in-out duration-300" 
                                             x-transition:enter-start="opacity-0" 
                                             x-transition:enter-end="opacity-100" 
                                             x-transition:leave="ease-in-out duration-300" 
                                             x-transition:leave-start="opacity-100" 
                                             x-transition:leave-end="opacity-0" 
                                             style="position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:49;" 
                                             @click="showTpsModal = false" 
                                             aria-hidden="true"></div>

                                        <!-- Drawer Panel -->
                                        <div x-show="showTpsModal" 
                                             x-transition:enter="transform transition ease-in-out duration-300" 
                                             x-transition:enter-start="translate-x-full" 
                                             x-transition:enter-end="translate-x-0" 
                                             x-transition:leave="transform transition ease-in-out duration-300" 
                                             x-transition:leave-start="translate-x-0" 
                                             x-transition:leave-end="translate-x-full" 
                                             style="position:fixed;top:0;right:0;width:640px;max-width:100vw;height:100vh;background:white;box-shadow:-4px 0 20px rgba(0,0,0,0.1);z-index:50;overflow-y:auto;"
                                             class="custom-scrollbar">
                                            
                                            <!-- Header -->
                                            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-start sticky top-0 bg-gray-50 z-10">
                                                <div>
                                                    <h2 class="text-lg font-bold text-gray-900" id="slide-over-title">RW {{ $profilRwId }} - {{ $dataRw->desa ?? '-' }}</h2>
                                                    <p class="text-sm text-gray-500 mt-1">{{ $dataRw->kecamatan ?? '-' }} &middot; {{ count($tpsRows) }} TPS Terlibat</p>
                                                </div>
                                                <button @click="showTpsModal = false" class="p-1.5 bg-white text-gray-400 hover:text-gray-600 rounded-md transition-colors border border-gray-200 hover:border-gray-300 shadow-sm">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>

                                            <!-- Content -->
                                            <div class="p-5">
                                                <div class="bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col w-full overflow-hidden">
                                                    <div class="w-full overflow-x-auto custom-scrollbar">
                                                        <table class="w-full text-left border-collapse min-w-[500px]">
                                                            <thead>
                                                                <tr class="bg-gray-50 border-b border-gray-200">
                                                                    <th class="py-3 px-4 text-[11px] font-bold text-gray-500 uppercase tracking-wider">No TPS</th>
                                                                    <th class="py-3 px-4 text-[11px] font-bold text-gray-500 uppercase tracking-wider">DPT</th>
                                                                    <th class="py-3 px-4 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right">PKS</th>
                                                                    <th class="py-3 px-4 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right">% PKS</th>
                                                                    <th class="py-3 px-4 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center">Rank</th>
                                                                    <th class="py-3 px-4 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-100">
                                                                @foreach ($tpsRows as $tpsRow)
                                                                    @php
                                                                        $status = $tpsRow['status'] ?? '-';
                                                                        $statusKey = strtolower($status);
                                                                        $statusBgColor = match($statusKey) {
                                                                            'jaga kuat' => '#dcfce7',
                                                                            'amankan' => '#ecfccb',
                                                                            'rebut realistis' => '#dbeafe',
                                                                            'garap intensif' => '#ffedd5',
                                                                            'zona berat' => '#fee2e2',
                                                                            default => '#f3f4f6'
                                                                        };
                                                                        $statusTextColor = match($statusKey) {
                                                                            'jaga kuat' => '#14532d',
                                                                            'amankan' => '#3f6212',
                                                                            'rebut realistis' => '#1e3a5f',
                                                                            'garap intensif' => '#9a3412',
                                                                            'zona berat' => '#991b1b',
                                                                            default => '#374151'
                                                                        };
                                                                        $statusIcon = match($statusKey) {
                                                                            'jaga kuat' => '#15803d',
                                                                            'amankan' => '#65a30d',
                                                                            'rebut realistis' => '#2563eb',
                                                                            'garap intensif' => '#ea580c',
                                                                            'zona berat' => '#dc2626',
                                                                            default => '#6b7280'
                                                                        };

                                                                        $rank = $tpsRow['rank'] ?? '-';
                                                                        $rankBgColor = match((string)$rank) {
                                                                            '1' => '#fef08a',
                                                                            '2' => '#e2e8f0',
                                                                            '3' => '#ffedd5',
                                                                            default => '#f1f5f9'
                                                                        };
                                                                        $rankTextColor = match((string)$rank) {
                                                                            '1' => '#854d0e',
                                                                            '2' => '#475569',
                                                                            '3' => '#9a3412',
                                                                            default => '#64748b'
                                                                        };
                                                                    @endphp
                                                                    <tr class="hover:bg-gray-50 transition-colors">
                                                                        <td class="py-3 px-4 text-xs font-bold text-gray-900">{{ str_starts_with(strtoupper($tpsRow['label'] ?? ''), 'TPS') ? $tpsRow['label'] : 'TPS ' . ($tpsRow['label'] ?? '-') }}</td>
                                                                        <td class="py-3 px-4 text-xs text-gray-600">{{ number_format($tpsRow['total_votes'] ?? 0, 0, ',', '.') }}</td>
                                                                        <td class="py-3 px-4 text-xs font-bold text-orange-600 text-right">{{ number_format($tpsRow['pks_votes'] ?? 0, 0, ',', '.') }}</td>
                                                                        <td class="py-3 px-4 text-xs text-gray-600 text-right">{{ number_format(($tpsRow['share'] ?? 0) * 100, 1, ',', '.') }}%</td>
                                                                        <td class="py-3 px-4 text-xs text-center">
                                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold" style="background-color: {{ $rankBgColor }}; color: {{ $rankTextColor }};">
                                                                                #{{ $rank }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="py-3 px-4 text-xs text-center">
                                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wide uppercase" style="background-color: {{ $statusBgColor }}; color: {{ $statusTextColor }};">
                                                                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $statusIcon }}"></span>
                                                                                {{ $status }}
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        </div>
                                    </div>

                                    <!-- Drawer RT Terlibat -->
                                    <div x-show="showRtModal" style="display: none;" class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
                                        <!-- Backdrop -->
                                        <div x-show="showRtModal" 
                                             x-transition:enter="ease-in-out duration-300" 
                                             x-transition:enter-start="opacity-0" 
                                             x-transition:enter-end="opacity-100" 
                                             x-transition:leave="ease-in-out duration-300" 
                                             x-transition:leave-start="opacity-100" 
                                             x-transition:leave-end="opacity-0" 
                                             style="position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:49;" 
                                             @click="showRtModal = false" 
                                             aria-hidden="true"></div>

                                        <!-- Drawer Panel -->
                                        <div x-show="showRtModal" 
                                             x-transition:enter="transform transition ease-in-out duration-300" 
                                             x-transition:enter-start="translate-x-full" 
                                             x-transition:enter-end="translate-x-0" 
                                             x-transition:leave="transform transition ease-in-out duration-300" 
                                             x-transition:leave-start="translate-x-0" 
                                             x-transition:leave-end="translate-x-full" 
                                             style="position:fixed;top:0;right:0;width:640px;max-width:100vw;height:100vh;background:white;box-shadow:-4px 0 20px rgba(0,0,0,0.1);z-index:50;overflow-y:auto;"
                                             class="custom-scrollbar bg-gray-50/50">
                                            
                                            <!-- Header -->
                                            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-start sticky top-0 bg-gray-50 z-10">
                                                <div>
                                                    <h2 class="text-lg font-bold text-gray-900" id="slide-over-title">Peta Politik Per RT</h2>
                                                    <p class="text-sm text-gray-500 mt-1">RW {{ $profilRwId }} - {{ $dataRw->desa ?? '-' }}</p>
                                                </div>
                                                <button @click="showRtModal = false" class="p-1.5 bg-white text-gray-400 hover:text-gray-600 rounded-md transition-colors border border-gray-200 hover:border-gray-300 shadow-sm">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>

                                            <!-- Content -->
                                            <div class="p-5">
                                                <template x-for="(rt, idx) in rtRows" :key="idx">
                                                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6 overflow-hidden">
                                                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                                                            <div class="font-bold text-gray-900">RT <span x-text="rt.rt"></span></div>
                                                            <div class="text-xs text-gray-500"><span x-text="rt.total_dpt"></span> DPT &middot; <span x-text="rt.tps_count"></span> TPS</div>
                                                        </div>
                                                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                                                            <!-- Top 5 Partai -->
                                                            <div>
                                                                <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-3">Top 5 Partai</h4>
                                                                <div class="space-y-3">
                                                                    <template x-for="(party, pIdx) in rt.top_parties" :key="pIdx">
                                                                        <div class="flex justify-between items-center">
                                                                            <div class="flex items-center gap-2">
                                                                                <div class="text-xs font-bold text-gray-500 w-3" x-text="pIdx + 1"></div>
                                                                                <div class="w-2 h-2 rounded-full shadow-sm" :style="'background-color: ' + (partyColors[party.party_name.toUpperCase()] || '#9ca3af')"></div>
                                                                                <div class="text-xs font-bold text-gray-800" x-text="party.party_name"></div>
                                                                            </div>
                                                                            <div class="text-xs font-bold text-gray-900" x-text="party.votes + ' Suara'"></div>
                                                                        </div>
                                                                    </template>
                                                                    <template x-if="!rt.top_parties || rt.top_parties.length === 0">
                                                                        <div class="text-xs text-gray-400 italic">Data tidak tersedia</div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Top 5 Caleg Rival -->
                                                            <div>
                                                                <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-3">
                                                                    Top 5 Caleg <span class="text-gray-800" x-text="rt.rival_party"></span>
                                                                </h4>
                                                                <div class="space-y-3">
                                                                    <template x-for="(cand, cIdx) in rt.top_rival_candidates" :key="cIdx">
                                                                        <div class="flex justify-between items-center group">
                                                                            <div class="flex flex-col">
                                                                                <div class="text-xs font-bold text-gray-800 line-clamp-1" x-text="cand.name"></div>
                                                                                <div class="text-[10px] text-gray-500 font-medium flex items-center gap-1">
                                                                                    <span class="w-1.5 h-1.5 rounded-full inline-block" :style="'background-color: ' + (partyColors[cand.party.toUpperCase()] || '#9ca3af')"></span>
                                                                                    <span x-text="cand.party"></span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="text-xs font-bold text-gray-900 shrink-0" x-text="cand.votes + ' Suara'"></div>
                                                                        </div>
                                                                    </template>
                                                                    <template x-if="!rt.top_rival_candidates || rt.top_rival_candidates.length === 0">
                                                                        <div class="text-xs text-gray-400 italic">Belum ada caleg / Data tidak tersedia</div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template x-if="!rtRows || rtRows.length === 0">
                                                    <div class="text-center py-10 bg-white rounded-xl border border-gray-200">
                                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                        <p class="text-gray-500 text-sm">Tidak ada data RT yang tersedia untuk RW ini.</p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Drawer Partai (Mirip Pemilu DPRD) -->
                                    <div x-show="showPartyDrawer" style="display: none;" class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
                                        
                                        <!-- Backdrop -->
                                        <div x-show="showPartyDrawer" 
                                             x-transition:enter="ease-in-out duration-300" 
                                             x-transition:enter-start="opacity-0" 
                                             x-transition:enter-end="opacity-100" 
                                             x-transition:leave="ease-in-out duration-300" 
                                             x-transition:leave-start="opacity-100" 
                                             x-transition:leave-end="opacity-0" 
                                             style="position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:49;" 
                                             @click="showPartyDrawer = false" 
                                             aria-hidden="true"></div>

                                        <!-- Drawer Panel -->
                                        <div x-show="showPartyDrawer" 
                                             x-transition:enter="transform transition ease-in-out duration-300" 
                                             x-transition:enter-start="translate-x-full" 
                                             x-transition:enter-end="translate-x-0" 
                                             x-transition:leave="transform transition ease-in-out duration-300" 
                                             x-transition:leave-start="translate-x-0" 
                                             x-transition:leave-end="translate-x-full" 
                                             style="position:fixed;top:0;right:0;width:420px;max-width:100vw;height:100vh;background:white;box-shadow:-4px 0 20px rgba(0,0,0,0.1);z-index:50;overflow-y:auto;"
                                             class="custom-scrollbar">
                                            
                                            <!-- Header -->
                                            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-start sticky top-0 bg-white z-10">
                                                <div>
                                                    <div class="text-[10px] text-orange-600 font-bold uppercase tracking-wider mb-1" x-text="'Ranking ' + (selectedPartyIdx + 1)"></div>
                                                    <h2 class="text-xl font-bold text-gray-900" x-text="partyRows[selectedPartyIdx]?.party_name || partyRows[selectedPartyIdx]?.partai || '-'"></h2>
                                                    <div class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                                                        <span class="w-2.5 h-2.5 rounded-full inline-block" :style="{ backgroundColor: partyColors[(partyRows[selectedPartyIdx]?.party_name || partyRows[selectedPartyIdx]?.partai || '').toUpperCase()] || '#94a3b8' }"></span>
                                                        <span>Kekuatan Partai di RW {{ $profilRwId }}</span>
                                                    </div>
                                                </div>
                                                <button @click="showPartyDrawer = false" class="p-1.5 bg-gray-50 text-gray-400 hover:text-gray-600 rounded-md transition-colors border border-transparent hover:border-gray-200">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>

                                            <!-- Content -->
                                            <div class="p-5">
                                                <div class="grid grid-cols-2 gap-3 mb-6">
                                                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 shadow-sm">
                                                        <div class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold mb-1">Total Suara</div>
                                                        <div class="text-2xl font-bold text-gray-900" x-text="(partyRows[selectedPartyIdx]?.total_votes || partyRows[selectedPartyIdx]?.votes || partyRows[selectedPartyIdx]?.suara || 0).toLocaleString('id-ID')"></div>
                                                    </div>
                                                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 shadow-sm">
                                                        <div class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold mb-1">Share Suara</div>
                                                        <div class="text-2xl font-bold text-gray-900" x-text="((partyRows[selectedPartyIdx]?.share || 0) * 100).toFixed(1).replace('.', ',') + '%'"></div>
                                                    </div>
                                                </div>

                                                <div class="mb-6">
                                                    <div class="text-[11px] text-orange-600 font-bold uppercase tracking-wider mb-3 border-b border-gray-100 pb-1">Head-to-Head vs Pemenang RW</div>
                                                    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                                                        <template x-if="selectedPartyIdx === 0">
                                                            <div class="text-sm font-semibold text-green-600 text-center py-3 flex items-center justify-center gap-2">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                                                                Partai ini adalah pemenang di RW ini!
                                                            </div>
                                                        </template>
                                                        <template x-if="selectedPartyIdx !== 0">
                                                            <div class="space-y-4">
                                                                <!-- Selected Party -->
                                                                <div>
                                                                    <div class="flex justify-between text-xs mb-1 font-bold text-gray-900">
                                                                        <span x-text="partyRows[selectedPartyIdx]?.party_name || partyRows[selectedPartyIdx]?.partai"></span>
                                                                        <span x-text="(partyRows[selectedPartyIdx]?.total_votes || partyRows[selectedPartyIdx]?.votes || partyRows[selectedPartyIdx]?.suara || 0).toLocaleString('id-ID') + ' (' + ((partyRows[selectedPartyIdx]?.share || 0) * 100).toFixed(1).replace('.', ',') + '%)'"></span>
                                                                    </div>
                                                                    <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                                                                        <div class="h-full rounded-full" :style="{ width: ((partyRows[selectedPartyIdx]?.share || 0) * 100) + '%', backgroundColor: partyColors[(partyRows[selectedPartyIdx]?.party_name || partyRows[selectedPartyIdx]?.partai || '').toUpperCase()] || '#94a3b8' }"></div>
                                                                    </div>
                                                                </div>
                                                                <!-- Winner Party -->
                                                                <div>
                                                                    <div class="flex justify-between text-[11px] mb-1 font-semibold text-gray-500">
                                                                        <span x-text="'Pemenang: ' + (partyRows[0]?.party_name || partyRows[0]?.partai)"></span>
                                                                        <span x-text="(partyRows[0]?.total_votes || partyRows[0]?.votes || partyRows[0]?.suara || 0).toLocaleString('id-ID') + ' (' + ((partyRows[0]?.share || 0) * 100).toFixed(1).replace('.', ',') + '%)'"></span>
                                                                    </div>
                                                                    <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                                                        <div class="h-full rounded-full bg-gray-400" :style="{ width: ((partyRows[0]?.share || 0) * 100) + '%' }"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>

                                                <!-- Top Caleg (Extracted Data) -->
                                                <template x-if="(partyRows[selectedPartyIdx] && partyRows[selectedPartyIdx].candidates) ? partyRows[selectedPartyIdx].candidates.length > 0 : false">
                                                    <div class="mb-6">
                                                        <div class="text-[11px] text-orange-600 font-bold uppercase tracking-wider mb-3 border-b border-gray-100 pb-1">Top Caleg di RW Ini</div>
                                                        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                                                            <table class="w-full text-left border-collapse">
                                                                <thead>
                                                                    <tr class="bg-gray-50 border-b border-gray-200 text-[10px] uppercase tracking-wider text-gray-500">
                                                                        <th class="py-2 px-3 font-semibold">Nama Caleg</th>
                                                                        <th class="py-2 px-3 font-semibold text-right">Suara</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <template x-for="(caleg, idx) in (partyRows[selectedPartyIdx] && partyRows[selectedPartyIdx].candidates) ? partyRows[selectedPartyIdx].candidates : []" :key="idx">
                                                                        <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                                                                            <td class="py-2 px-3 text-xs font-semibold text-gray-900" x-text="caleg.name"></td>
                                                                            <td class="py-2 px-3 text-xs font-bold text-gray-900 text-right" x-text="caleg.votes.toLocaleString('id-ID')"></td>
                                                                        </tr>
                                                                    </template>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template x-if="(partyRows[selectedPartyIdx] && partyRows[selectedPartyIdx].candidates) ? partyRows[selectedPartyIdx].candidates.length === 0 : true">
                                                    <div class="mb-6 bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                                                        <div class="text-[11px] text-gray-500 font-semibold">Belum ada rincian data caleg.</div>
                                                    </div>
                                                </template>

                                                <!-- Detail TPS for PKS ONLY -->
                                                <template x-if="(partyRows[selectedPartyIdx]?.party_name || partyRows[selectedPartyIdx]?.partai || '').toUpperCase() === 'PKS'">
                                                    <div>
                                                        <div class="text-[11px] text-orange-600 font-bold uppercase tracking-wider mb-3 border-b border-gray-100 pb-1">Sebaran TPS (Khusus PKS)</div>
                                                        <button @click="showPartyDrawer = false; setTimeout(() => { showTpsModal = true; }, 300)" class="w-full bg-orange-50 hover:bg-orange-100 text-orange-700 font-semibold py-3 px-4 rounded-xl border border-orange-200 text-sm transition-colors flex justify-between items-center shadow-sm">
                                                            Lihat Rincian TPS
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                        </button>
                                                    </div>
                                                </template>
                                                <template x-if="(partyRows[selectedPartyIdx]?.party_name || partyRows[selectedPartyIdx]?.partai || '').toUpperCase() !== 'PKS'">
                                                    <div class="bg-gray-50 rounded-xl p-5 text-center border border-gray-100">
                                                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                        <div class="text-sm font-semibold text-gray-700">Rincian TPS Tidak Tersedia</div>
                                                        <div class="text-[11px] text-gray-500 mt-1">Sistem hanya menyimpan rekapitulasi tingkat RW untuk partai kompetitor. Rincian per-TPS khusus untuk PKS.</div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        </div>
                    @elseif($activeTab === 'strategi')
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                            <div class="grid grid-cols-1 gap-4">
                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <span class="text-gray-500 block text-[10px] sm:text-xs mb-1 uppercase tracking-wider font-bold">Strategi Mencapai Target Suara</span>
                                    <div class="font-medium text-gray-900 text-sm sm:text-base leading-relaxed whitespace-pre-line">{{ $profilRw->strategi ?: '-' }}</div>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <span class="text-gray-500 block text-[10px] sm:text-xs mb-1 uppercase tracking-wider font-bold">Penanggung Jawab Dakwah di RW</span>
                                    <div class="font-medium text-gray-900 text-sm sm:text-base">{{ $profilRw->penanggung_jawab ?: '-' }}</div>
                                </div>
                                @if($profilRw->keterangan_lain)
                                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                        <span class="text-gray-500 block text-[10px] sm:text-xs mb-1 uppercase tracking-wider font-bold">Keterangan Lain</span>
                                        <div class="font-medium text-gray-900 text-sm sm:text-base leading-relaxed whitespace-pre-line">{{ $profilRw->keterangan_lain }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        @elseif($activeTab === 'struktur')
            <div class="flex flex-col gap-6">
                <!-- Korwe Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-5 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900 flex items-center">
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full mr-2">{{ count($korwes) }}</span>
                            KORWE (Koordinator RW)
                        </h3>
                    </div>
                    <div class="p-5">
                        @if(count($korwes) === 0)
                            <div class="text-center py-6 text-gray-500 text-sm">Belum ada Korwe terdaftar.</div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @foreach($korwes as $k)
                                    <div class="flex items-center justify-between p-3 bg-white border border-gray-100 rounded-lg shadow-sm hover:border-blue-200 transition-colors">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $k->nama_koordinator }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $k->no_hp }}</p>
                                        </div>
                                        <div class="flex flex-col items-end gap-2">
                                            <span class="px-2 py-1 text-[10px] uppercase tracking-wider font-semibold rounded-full {{ $k->status === 'terbentuk' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $k->status }}
                                            </span>
                                            <div class="flex items-center gap-1">
                                                <button wire:click="editInfrastruktur('korwe', {{ $k->id }})" class="p-1 text-gray-400 hover:text-blue-600 transition-colors" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                                <button wire:click="hapusInfrastruktur('korwe', {{ $k->id }})" wire:confirm="Yakin ingin menghapus Korwe ini?" class="p-1 text-gray-400 hover:text-red-600 transition-colors" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Korte Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-5 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900 flex items-center">
                            <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-0.5 rounded-full mr-2">{{ count($kortes) }}</span>
                            KORTE (Koordinator RT)
                        </h3>
                    </div>
                    <div class="p-5 max-h-[500px] overflow-y-auto custom-scrollbar">
                        @if(count($kortes) === 0)
                            <div class="text-center py-6 text-gray-500 text-sm">Belum ada Korte terdaftar.</div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @foreach($kortes as $k)
                                    <div class="flex items-center justify-between p-3 bg-white border border-gray-100 rounded-lg shadow-sm hover:border-indigo-200 transition-colors">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $k->nama_koordinator }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5 font-medium text-indigo-600">RT {{ str_pad($k->nomor_rt, 3, '0', STR_PAD_LEFT) }} <span class="text-gray-400 font-normal ml-1">• {{ $k->no_hp }}</span></p>
                                        </div>
                                        <div class="flex flex-col items-end gap-2">
                                            <span class="px-2 py-1 text-[10px] uppercase tracking-wider font-semibold rounded-full {{ $k->status === 'terbentuk' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $k->status }}
                                            </span>
                                            <div class="flex items-center gap-1">
                                                <button wire:click="editInfrastruktur('korte', {{ $k->id }})" class="p-1 text-gray-400 hover:text-blue-600 transition-colors" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                                <button wire:click="hapusInfrastruktur('korte', {{ $k->id }})" wire:confirm="Yakin ingin menghapus Korte ini?" class="p-1 text-gray-400 hover:text-red-600 transition-colors" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
        @elseif($activeTab === 'realisasi')
            <div class="flex flex-col gap-6">
                <!-- Target Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Target Penggalang</h3>
                        <p class="text-4xl font-black text-gray-900">{{ $dataRw->target_penggalang }}</p>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Terkumpul</h3>
                        <p class="text-4xl font-black text-emerald-600">{{ count($penggalangs) }}</p>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Pencapaian</h3>
                        @php
                            $percentage = $dataRw->target_penggalang > 0 ? min(100, round((count($penggalangs) / $dataRw->target_penggalang) * 100)) : 0;
                        @endphp
                        <p class="text-4xl font-black {{ $percentage >= 100 ? 'text-emerald-600' : 'text-orange-600' }}">{{ $percentage }}%</p>
                        
                        <div class="w-full bg-gray-200 rounded-full h-3 mt-4">
                            <div class="{{ $percentage >= 100 ? 'bg-emerald-600' : 'bg-orange-500' }} h-3 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Penggalang List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-5 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900">Daftar Penggalang</h3>
                    </div>
                    <div class="p-5 max-h-[600px] overflow-y-auto custom-scrollbar">
                        @if(count($penggalangs) === 0)
                            <div class="text-center py-12 text-gray-500 text-sm">Belum ada penggalang terdaftar.</div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                @foreach($penggalangs as $p)
                                    <div class="flex flex-col p-3 bg-white border border-gray-100 rounded-lg shadow-sm hover:border-emerald-200 transition-colors">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">{{ $p->nama }}</p>
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $p->no_hp ?? '-' }}</p>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <span class="px-2 py-1 inline-flex text-[10px] leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 capitalize">
                                                    {{ str_replace('_', ' ', $p->sumber) }}
                                                </span>
                                                <button wire:click="editInfrastruktur('penggalang', {{ $p->id }})" class="p-1 text-gray-400 hover:text-blue-600 transition-colors" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                                <button wire:click="hapusInfrastruktur('penggalang', {{ $p->id }})" wire:confirm="Yakin ingin menghapus Penggalang Suara ini?" class="p-1 text-gray-400 hover:text-red-600 transition-colors" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-auto pt-2 border-t border-gray-50 flex justify-between items-center text-xs">
                                            <span class="text-gray-500 font-medium">RT</span>
                                            <span class="font-bold text-emerald-600">{{ str_pad($p->rt, 3, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if ($showProfilDrawer && $profilRwId)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:40;" wire:click="closeProfilDrawer"></div>
        <div style="position:fixed;top:0;right:0;width:440px;max-width:100%;height:100%;background:white;box-shadow:-8px 0 24px rgba(0,0,0,0.16);z-index:50;overflow-y:auto;">
            <div style="position:sticky;top:0;background:white;border-bottom:0.5px solid #e5e5e5;padding:16px;z-index:10;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                    <div>
                        <div style="font-size:14px;font-weight:600;color:#1a1a1a;">Profil RW {{ $profilRwId }} - {{ $targetWilayah->desa }}</div>
                        <div style="font-size:11px;color:#888;margin-top:4px;">{{ $targetWilayah->kecamatan }} · {{ $targetWilayah->dapil }}</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        @php
                            $statusCfg = \App\Models\TargetWilayah::STATUS_CONFIG[$autoFillData['status_wilayah'] ?? 'ZONA BERAT'] ?? \App\Models\TargetWilayah::STATUS_CONFIG['ZONA BERAT'];
                        @endphp
                        <span style="padding:3px 8px;border-radius:999px;font-size:10px;font-weight:600;background:{{ $statusCfg['bg'] }};color:{{ $statusCfg['text'] }};">{{ $statusCfg['label'] }}</span>
                        <button wire:click="closeProfilDrawer" type="button" style="width:28px;height:28px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;color:#666;cursor:pointer;">x</button>
                    </div>
                </div>
            </div>

            <div style="padding:16px;display:grid;gap:16px;">
                <div>
                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;font-weight:600;color:#2563eb;margin-bottom:10px;">Data otomatis <span style="font-size:10px;padding:2px 6px;border-radius:999px;background:#dbeafe;color:#2563eb;">auto-fill</span></div>
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-bottom:8px;">
                        <div style="background:#fafafa;border-radius:10px;padding:10px;">
                            <div style="font-size:10px;color:#888;">Jumlah RT</div>
                            <div style="font-size:14px;font-weight:600;color:#1a1a1a;">{{ number_format($autoFillData['jumlah_rt'] ?? 0) }}</div>
                        </div>
                        <div style="background:#fafafa;border-radius:10px;padding:10px;">
                            <div style="font-size:10px;color:#888;">DPT</div>
                            <div style="font-size:14px;font-weight:600;color:#1a1a1a;">{{ number_format($autoFillData['dpt'] ?? 0) }}</div>
                        </div>
                        <div style="background:#fafafa;border-radius:10px;padding:10px;">
                            <div style="font-size:10px;color:#888;">Est. Suara PKS</div>
                            <div style="font-size:14px;font-weight:600;color:#ea580c;">~{{ number_format($autoFillData['estimasi_pks'] ?? 0) }}</div>
                        </div>
                        <div style="background:#fafafa;border-radius:10px;padding:10px;">
                            <div style="font-size:10px;color:#888;">Target 2029</div>
                            <div style="font-size:14px;font-weight:600;color:#ea580c;">{{ number_format($autoFillData['target_suara'] ?? 0) }}</div>
                        </div>
                    </div>
                    <div style="font-size:11px;color:#666;background:#fafafa;border-radius:10px;padding:10px;line-height:1.6;">
                        <strong>Caleg PKS tertinggi:</strong> {{ $autoFillData['caleg_pks_tertinggi'] ?? '-' }}<br>
                        <strong>Partai pemenang:</strong> {{ $autoFillData['partai_pemenang'] ?? '-' }}<br>
                        <strong>3 partai tertinggi:</strong> {{ $autoFillData['top_3_partai'] ?? '-' }}<br>
                        @if ($autoFillData['korwe_nama'] ?? null)
                            <strong>KORWE:</strong> {{ $autoFillData['korwe_nama'] }} ({{ $autoFillData['korwe_status'] }})
                        @endif
                    </div>
                </div>

                <div style="border-top:0.5px solid #e5e5e5;padding-top:16px;display:grid;gap:12px;">
                    <div style="font-size:12px;font-weight:600;color:#d97706;">Profil wilayah</div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Tipologi RW</label>
                        <select wire:model="profilData.tipologi" style="width:100%;height:36px;border-radius:10px;border:0.5px solid #d4d4d8;padding:0 12px;background:white;font-size:13px;">
                            <option value="">- Pilih -</option>
                            @foreach (\App\Models\ProfilRw::TIPOLOGI_OPTIONS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Sumber ekonomi dominan</label>
                        <select wire:model="profilData.ekonomi_dominan" style="width:100%;height:36px;border-radius:10px;border:0.5px solid #d4d4d8;padding:0 12px;background:white;font-size:13px;">
                            <option value="">- Pilih -</option>
                            @foreach (\App\Models\ProfilRw::EKONOMI_OPTIONS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Profil umum warga</label>
                        <textarea wire:model="profilData.profil_warga" rows="2" style="width:100%;border-radius:10px;border:0.5px solid #d4d4d8;padding:10px 12px;background:white;font-size:13px;resize:vertical;" placeholder="Agama, kebiasaan, pragmatisme pemilih..."></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;" class="detail-form-grid">
                        <div>
                            <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Suara PKS 2019</label>
                            <input wire:model="profilData.suara_pks_2019" type="number" style="width:100%;height:36px;border-radius:10px;border:0.5px solid #d4d4d8;padding:0 12px;background:white;font-size:13px;" placeholder="0">
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Jumlah KTA</label>
                            <input wire:model="profilData.jumlah_kta" type="number" style="width:100%;height:36px;border-radius:10px;border:0.5px solid #d4d4d8;padding:0 12px;background:white;font-size:13px;" placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Faktor penyebab menang/kalah</label>
                        <textarea wire:model="profilData.faktor_penyebab" rows="2" style="width:100%;border-radius:10px;border:0.5px solid #d4d4d8;padding:10px 12px;background:white;font-size:13px;resize:vertical;" placeholder="Caleg lokal, tokoh kuat, pragmatisme..."></textarea>
                    </div>
                </div>

                <div style="border-top:0.5px solid #e5e5e5;padding-top:16px;display:grid;gap:12px;">
                    <div style="font-size:12px;font-weight:600;color:#16a34a;">Infrastruktur partai</div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Anggota PKS di RW</label>
                        <textarea wire:model="profilData.anggota_pks" rows="2" style="width:100%;border-radius:10px;border:0.5px solid #d4d4d8;padding:10px 12px;background:white;font-size:13px;resize:vertical;" placeholder="Nama + jenjang keanggotaan"></textarea>
                    </div>
                    @php
                        $infraItems = [
                            ['field' => 'upa_rw', 'label' => 'UPA RW', 'name_field' => 'upa_rw_nama', 'placeholder' => 'Nama pembina'],
                            ['field' => 'rki', 'label' => 'RKI', 'name_field' => 'rki_nama', 'placeholder' => 'Nama penggerak'],
                            ['field' => 'senam', 'label' => 'Titik Senam PKS', 'name_field' => 'senam_nama', 'placeholder' => 'Nama instruktur'],
                            ['field' => 'relawan_milenial', 'label' => 'Relawan Milenial / Geka', 'name_field' => 'relawan_milenial_nama', 'placeholder' => 'Nama + jabatan'],
                        ];
                    @endphp
                    @foreach ($infraItems as $item)
                        <div style="border:0.5px solid #e5e5e5;border-radius:10px;padding:10px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:6px;">
                                <span style="font-size:12px;font-weight:500;color:#1f2937;">{{ $item['label'] }}</span>
                                <select wire:model.live="profilData.{{ $item['field'] }}_status" style="height:30px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;">
                                    <option value="belum">Belum</option>
                                    <option value="sudah">Sudah</option>
                                </select>
                            </div>
                            @if (($profilData[$item['field'] . '_status'] ?? 'belum') === 'sudah')
                                <input wire:model="profilData.{{ $item['name_field'] }}" style="width:100%;height:32px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;" placeholder="{{ $item['placeholder'] }}">
                            @endif
                        </div>
                    @endforeach
                    <div style="border:0.5px solid #e5e5e5;border-radius:10px;padding:10px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:6px;">
                            <span style="font-size:12px;font-weight:500;color:#1f2937;">Caleg terpilih di RW?</span>
                            <select wire:model.live="profilData.caleg_terpilih_ada" style="height:30px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;">
                                <option value="0">Tidak</option>
                                <option value="1">Ya</option>
                            </select>
                        </div>
                        @if (($profilData['caleg_terpilih_ada'] ?? false))
                            <input wire:model="profilData.caleg_terpilih_nama" style="width:100%;height:32px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;" placeholder="Nama caleg">
                        @endif
                    </div>
                </div>

                <div style="border-top:0.5px solid #e5e5e5;padding-top:16px;display:grid;gap:12px;">
                    <div style="font-size:12px;font-weight:600;color:#dc2626;">Peta politik lokal</div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Afiliasi Ketua RW & RT</label>
                        <textarea wire:model="profilData.afiliasi_rw_rt" rows="3" style="width:100%;border-radius:10px;border:0.5px solid #d4d4d8;padding:10px 12px;background:white;font-size:13px;resize:vertical;" placeholder="Ketua RW: Nama - Partai&#10;RT 1: Nama - Partai"></textarea>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Afiliasi Kader Posyandu & DKM</label>
                        <textarea wire:model="profilData.afiliasi_posyandu_dkm" rows="2" style="width:100%;border-radius:10px;border:0.5px solid #d4d4d8;padding:10px 12px;background:white;font-size:13px;resize:vertical;" placeholder="Nama - organisasi - partai"></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;" class="detail-form-grid">
                        <div>
                            <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Pengurus kompetitor?</label>
                            <select wire:model.live="profilData.kompetitor_status" style="width:100%;height:32px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;">
                                <option value="tidak_tahu">Tidak tahu</option>
                                <option value="ada">Ada</option>
                                <option value="tidak">Tidak ada</option>
                            </select>
                            @if (($profilData['kompetitor_status'] ?? '') === 'ada')
                                <input wire:model="profilData.kompetitor_detail" style="width:100%;height:32px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;margin-top:6px;" placeholder="Nama + partai">
                            @endif
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Tim sukses lain?</label>
                            <select wire:model.live="profilData.tim_sukses_status" style="width:100%;height:32px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;">
                                <option value="tidak_tahu">Tidak tahu</option>
                                <option value="ada">Ada</option>
                                <option value="tidak">Tidak ada</option>
                            </select>
                            @if (($profilData['tim_sukses_status'] ?? '') === 'ada')
                                <input wire:model="profilData.tim_sukses_detail" style="width:100%;height:32px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 10px;background:white;font-size:12px;margin-top:6px;" placeholder="Nama + partai">
                            @endif
                        </div>
                    </div>
                </div>

                <div style="border-top:0.5px solid #e5e5e5;padding-top:16px;display:grid;gap:12px;">
                    <div style="font-size:12px;font-weight:600;color:#ea580c;">Strategi & penanggung jawab</div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Strategi mencapai target suara</label>
                        <textarea wire:model="profilData.strategi" rows="3" style="width:100%;border-radius:10px;border:0.5px solid #d4d4d8;padding:10px 12px;background:white;font-size:13px;resize:vertical;" placeholder="Rencana aksi untuk meningkatkan suara"></textarea>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Penanggung jawab dakwah di RW</label>
                        <input wire:model="profilData.penanggung_jawab" style="width:100%;height:36px;border-radius:10px;border:0.5px solid #d4d4d8;padding:0 12px;background:white;font-size:13px;" placeholder="Nama + jenjang">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;color:#666;margin-bottom:6px;">Keterangan lain</label>
                        <textarea wire:model="profilData.keterangan_lain" rows="2" style="width:100%;border-radius:10px;border:0.5px solid #d4d4d8;padding:10px 12px;background:white;font-size:13px;resize:vertical;" placeholder="Catatan tambahan"></textarea>
                    </div>
                </div>
            </div>
            <div style="position:sticky;bottom:0;background:white;border-top:0.5px solid #e5e5e5;padding:16px;display:flex;gap:8px;">
                <button wire:click="simpanProfil" type="button" style="flex:1;height:40px;border:none;border-radius:10px;background:#ea580c;color:white;font-size:13px;font-weight:600;cursor:pointer;">Simpan Profil</button>
                <button wire:click="closeProfilDrawer" type="button" style="height:40px;padding:0 16px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:13px;cursor:pointer;">Batal</button>
            </div>
        </div>
    @endif

</div>