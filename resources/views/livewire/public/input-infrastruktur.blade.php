<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6">
    <div class="max-w-2xl mx-auto space-y-4">
        
        <!-- Header Banner (Google Form Style) -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
            <div class="h-3 w-full bg-green-500"></div>
            <img src="{{ asset('images/form-header-infra.png') }}" alt="Form Header" class="w-full h-auto object-cover max-h-48">
            <div class="p-6 sm:p-8">
                <h1 class="text-3xl font-medium text-gray-900 mb-2">Form Input Infrastruktur</h1>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Formulir untuk menambahkan data Korwe, Korte, dan Penggalang ke dalam database Bekasi Hebat. Silakan lengkapi data di bawah ini sesuai dengan kondisi riil di lapangan.
                </p>
                <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-red-500 font-medium">
                    * Menunjukkan pertanyaan yang wajib diisi
                </div>
            </div>
        </div>

        @if ($isSubmitted)
            <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                <h2 class="text-2xl font-medium text-gray-900 mb-4">Terima Kasih!</h2>
                <p class="text-gray-600 mb-6">Data infrastruktur telah berhasil disimpan ke dalam database Bekasi Hebat.</p>
                <button type="button" wire:click="$set('isSubmitted', false)" class="text-green-600 hover:text-green-700 font-medium text-sm underline">
                    Input data infrastruktur lainnya
                </button>
            </div>
        @else
            <form wire:submit.prevent="submit" class="space-y-4">
                
                <!-- Pilihan Wilayah -->
                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Dapil <span class="text-red-500">*</span></h3>
                    <select wire:model.live="dapil" class="py-2.5 px-3 block w-full sm:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-0 focus:outline-none text-base sm:text-lg" required>
                        <option value="">Pilih</option>
                        @foreach($dapilOptions as $opt)
                            <option value="{{ $opt }}">Dapil {{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('dapil') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Kecamatan <span class="text-red-500">*</span></h3>
                    <select wire:model.live="kecamatan" class="py-2.5 px-3 block w-full sm:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-0 focus:outline-none text-base sm:text-lg" {{ empty($kecamatanOptions) ? 'disabled' : '' }} required>
                        <option value="">Pilih</option>
                        @foreach($kecamatanOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('kecamatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Desa/Kelurahan <span class="text-red-500">*</span></h3>
                    <select wire:model.live="desa" class="py-2.5 px-3 block w-full sm:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-0 focus:outline-none text-base sm:text-lg" {{ empty($desaOptions) ? 'disabled' : '' }} required>
                        <option value="">Pilih</option>
                        @foreach($desaOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('desa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Nomor RW <span class="text-red-500">*</span></h3>
                    <select wire:model.live="data_rw_id" class="py-2.5 px-3 block w-full sm:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-0 focus:outline-none text-base sm:text-lg" {{ empty($rwOptions) ? 'disabled' : '' }} required>
                        <option value="">Pilih</option>
                        @foreach($rwOptions as $opt)
                            <option value="{{ $opt['id'] }}">RW {{ $opt['nomor_rw'] }}</option>
                        @endforeach
                    </select>
                    @error('data_rw_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                @if($data_rw_id)
                <div class="space-y-4">
                    <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                        <div class="flex items-center gap-2 mb-6 border-b pb-2">
                            <h3 class="text-lg font-medium text-green-600 uppercase tracking-wide">Data Infrastruktur</h3>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-base font-medium text-gray-900 mb-2">Jenis Infrastruktur <span class="text-red-500">*</span></h3>
                                <select wire:model.live="infraType" class="py-2.5 px-3 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:border-green-500 text-base sm:text-lg" required>
                                    <option value="korwe">Korwe (Koordinator RW)</option>
                                    <option value="korte">Korte (Koordinator RT)</option>
                                    <option value="penggalang">Penggalang Suara</option>
                                </select>
                                @error('infraType') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            @if($infraType === 'korte' || $infraType === 'penggalang')
                            <div>
                                <h3 class="text-base font-medium text-gray-900 mb-2">Nomor RT <span class="text-red-500">*</span></h3>
                                <input type="text" wire:model="infraRt" class="block w-full sm:w-2/3 border-0 border-b border-gray-300 bg-transparent py-2 px-0 text-gray-900 focus:border-green-500 focus:ring-0 focus:outline-none text-base sm:text-lg" placeholder="Contoh: 001" required>
                                @error('infraRt') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            @endif

                            <div>
                                <h3 class="text-base font-medium text-gray-900 mb-2">Nama Lengkap <span class="text-red-500">*</span></h3>
                                <input type="text" wire:model="infraNama" class="block w-full border-0 border-b border-gray-300 bg-transparent py-2 px-0 text-gray-900 focus:border-green-500 focus:ring-0 focus:outline-none text-base sm:text-lg" placeholder="Jawaban Anda" required>
                                @error('infraNama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <h3 class="text-base font-medium text-gray-900 mb-2">No WhatsApp <span class="text-red-500">*</span></h3>
                                <input type="tel" wire:model="infraHp" class="block w-full sm:w-2/3 border-0 border-b border-gray-300 bg-transparent py-2 px-0 text-gray-900 focus:border-green-500 focus:ring-0 focus:outline-none text-base sm:text-lg" placeholder="08..." required>
                                @error('infraHp') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            @if($infraType === 'penggalang')
                            <div>
                                <h3 class="text-base font-medium text-gray-900 mb-2">Target Jangkauan (Suara) <span class="text-red-500">*</span></h3>
                                <input type="number" wire:model="infraTarget" class="block w-full sm:w-1/3 border-0 border-b border-gray-300 bg-transparent py-2 px-0 text-gray-900 focus:border-green-500 focus:ring-0 focus:outline-none text-base sm:text-lg" placeholder="Contoh: 50" min="1" required>
                                @error('infraTarget') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="flex justify-between items-center pt-4">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 px-6 rounded-md shadow-sm transition-colors text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Kirim Data
                        </button>
                        <button type="button" wire:click="$set('data_rw_id', '')" class="text-gray-500 hover:text-gray-700 text-sm font-medium transition-colors">
                            Batal
                        </button>
                    </div>
                </div>
                @endif
            </form>
        @endif
        
        <!-- Footer -->
        <div class="text-center text-xs text-gray-400 mt-8 pb-8">
            <p>Sistem Informasi Bekasi Hebat</p>
            <p class="mt-1">Jangan pernah mengirimkan sandi melalui Google Formulir ini.</p>
        </div>
    </div>
</div>
