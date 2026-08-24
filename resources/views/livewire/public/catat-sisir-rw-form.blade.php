<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6">
    <div class="max-w-2xl mx-auto space-y-4">
        
        <!-- Header Banner (Google Form Style) -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
            <div class="h-3 w-full bg-orange-500"></div>
            <div class="p-6 sm:p-8">
                <h1 class="text-3xl font-medium text-gray-900 mb-2">Input Kegiatan Sisir RW</h1>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Silakan masukkan data kegiatan Sisir RW secara publik.
                </p>
                <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-red-500 font-medium">
                    * Wajib diisi
                </div>
            </div>
        </div>

        @if ($isSubmitted)
            <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <x-heroicon-o-check class="h-6 w-6 text-green-600" />
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Terima Kasih!</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Data kegiatan Sisir RW telah berhasil direkam.
                        </p>
                    </div>
                    <div class="mt-6">
                        <button type="button" wire:click="resetForm" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-600 hover:bg-orange-700">
                            Kirim Jawaban Lain
                        </button>
                    </div>
                </div>
            </div>
        @else
            <form wire:submit="simpan" class="space-y-4">
                
                <!-- Section: Wilayah -->
                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Pilih Wilayah <span class="text-red-500">*</span></h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dapil</label>
                            <select wire:model.live="dapil" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md">
                                <option value="">Pilih Dapil...</option>
                                @foreach($dapilOptions as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                            @error('dapil') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kecamatan</label>
                            <select wire:model.live="kecamatan" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md" {{ empty($dapil) ? 'disabled' : '' }}>
                                <option value="">Pilih Kecamatan...</option>
                                @foreach($kecamatanOptions as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                            @error('kecamatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Desa/Kelurahan</label>
                            <select wire:model.live="desa" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md" {{ empty($kecamatan) ? 'disabled' : '' }}>
                                <option value="">Pilih Desa...</option>
                                @foreach($desaOptions as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                            @error('desa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor RW</label>
                            <select wire:model.live="data_rw_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md" {{ empty($desa) ? 'disabled' : '' }}>
                                <option value="">Pilih RW...</option>
                                @foreach($rwOptions as $opt)
                                    <option value="{{ $opt['id'] }}">{{ $opt['nomor_rw'] }}</option>
                                @endforeach
                            </select>
                            @error('data_rw_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Detail Kegiatan -->
                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Detail Kegiatan</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kegiatan <span class="text-red-500">*</span></label>
                            <select wire:model="formJenis" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md">
                                <option value="">Pilih...</option>
                                @foreach(\App\Models\KegiatanRw::JENIS_KEGIATAN as $key => $config)
                                    <option value="{{ $key }}">{{ $config['label'] }}</option>
                                @endforeach
                            </select>
                            @error('formJenis') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Kegiatan <span class="text-red-500">*</span></label>
                            <input type="datetime-local" wire:model="formTanggal" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            @error('formTanggal') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pelaksana <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="formPelaksana" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            @error('formPelaksana') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Warga Hadir <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="formJumlahWarga" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            @error('formJumlahWarga') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Segmen Peserta <span class="text-red-500">*</span></label>
                            <select wire:model.live="formSegmen" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md">
                                <option value="">Pilih...</option>
                                @foreach(\App\Models\KegiatanRw::SEGMEN_KEGIATAN as $seg)
                                    <option value="{{ $seg }}">{{ $seg }}</option>
                                @endforeach
                            </select>
                            @error('formSegmen') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        @if($formSegmen === 'Other')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sebutkan Segmen Lainnya</label>
                                <input type="text" wire:model="formSegmenOther" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Section: Pejabat Hadir -->
                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Kehadiran Pejabat</h3>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Anggota DPR RI</label>
                            <select wire:model="formDprRi" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md">
                                @foreach(\App\Models\KegiatanRw::DPR_RI_HADIR as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Anggota DPRD Provinsi</label>
                            <select wire:model="formDprdProv" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md">
                                @foreach(\App\Models\KegiatanRw::DPRD_PROV_HADIR as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Anggota DPRD Kabupaten</label>
                            <select wire:model="formDprdKab" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md">
                                @foreach(\App\Models\KegiatanRw::DPRD_KAB_HADIR as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section: Tempat & Tambahan -->
                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Lain-Lain</h3>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tempat Kegiatan <span class="text-red-500">*</span></label>
                            <select wire:model.live="formTempat" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md">
                                <option value="">Pilih...</option>
                                @foreach(\App\Models\KegiatanRw::TEMPAT_KEGIATAN as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                            @error('formTempat') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        @if($formTempat === 'Other')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sebutkan Tempat Lainnya</label>
                                <input type="text" wire:model="formTempatOther" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                            <textarea wire:model="formCatatan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan Tambahan</label>
                            <textarea wire:model="formKeteranganTambahan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4 pb-12">
                    <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Kirim Form
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
