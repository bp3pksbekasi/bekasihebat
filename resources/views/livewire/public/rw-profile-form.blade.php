<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6">
    <div class="max-w-2xl mx-auto space-y-4">
        
        <!-- Header Banner (Google Form Style) -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
            <div class="h-3 w-full bg-orange-500"></div>
            <div class="p-6 sm:p-8">
                <h1 class="text-3xl font-medium text-gray-900 mb-2">Form Profil RW Publik</h1>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Pembaruan data profil RW untuk database Bekasi Hebat. Silakan lengkapi data di bawah ini sesuai dengan kondisi riil di lapangan. Pastikan nomor WhatsApp Anda aktif karena data akan diverifikasi.
                </p>
                <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-red-500 font-medium">
                    * Menunjukkan pertanyaan yang wajib diisi
                </div>
            </div>
        </div>

        @if ($isSubmitted)
            <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                <h2 class="text-2xl font-medium text-gray-900 mb-4">Terima Kasih!</h2>
                <p class="text-gray-600 mb-6">Tanggapan Anda telah berhasil disimpan ke dalam sistem database Bekasi Hebat.</p>
                <button type="button" wire:click="$set('isSubmitted', false)" class="text-orange-600 hover:text-orange-700 font-medium text-sm underline">
                    Kirim tanggapan lain
                </button>
            </div>
        @else
            <form wire:submit.prevent="submit" class="space-y-4">
                
                <!-- Identitas Pengisi -->
                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Nama Lengkap <span class="text-red-500">*</span></h3>
                    <input type="text" wire:model="nama_pengisi" class="block w-full sm:w-2/3 border-0 border-b border-gray-300 bg-transparent py-2 px-0 text-gray-900 focus:border-orange-500 focus:ring-0 focus:outline-none text-base sm:text-lg focus:outline-none" placeholder="Jawaban Anda" required>
                    @error('nama_pengisi') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">No WhatsApp <span class="text-red-500">*</span></h3>
                    <input type="tel" wire:model="no_hp_pengisi" class="block w-full sm:w-2/3 border-0 border-b border-gray-300 bg-transparent py-2 px-0 text-gray-900 focus:border-orange-500 focus:ring-0 focus:outline-none text-base sm:text-lg focus:outline-none" placeholder="Jawaban Anda" required>
                    @error('no_hp_pengisi') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Pilihan Wilayah -->
                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Dapil <span class="text-red-500">*</span></h3>
                    <select wire:model.live="dapil" class="py-2.5 px-3 block w-full sm:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-0 focus:outline-none text-base sm:text-lg focus:outline-none" required>
                        <option value="">Pilih</option>
                        @foreach($dapilOptions as $opt)
                            <option value="{{ $opt }}">Dapil {{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('dapil') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Kecamatan <span class="text-red-500">*</span></h3>
                    <select wire:model.live="kecamatan" class="py-2.5 px-3 block w-full sm:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-0 focus:outline-none text-base sm:text-lg focus:outline-none" {{ empty($kecamatanOptions) ? 'disabled' : '' }} required>
                        <option value="">Pilih</option>
                        @foreach($kecamatanOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('kecamatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Desa/Kelurahan <span class="text-red-500">*</span></h3>
                    <select wire:model.live="desa" class="py-2.5 px-3 block w-full sm:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-0 focus:outline-none text-base sm:text-lg focus:outline-none" {{ empty($desaOptions) ? 'disabled' : '' }} required>
                        <option value="">Pilih</option>
                        @foreach($desaOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('desa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Nomor RW <span class="text-red-500">*</span></h3>
                    <select wire:model.live="data_rw_id" class="py-2.5 px-3 block w-full sm:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-0 focus:outline-none text-base sm:text-lg focus:outline-none" {{ empty($rwOptions) ? 'disabled' : '' }} required>
                        <option value="">Pilih</option>
                        @foreach($rwOptions as $opt)
                            <option value="{{ $opt['id'] }}">RW {{ $opt['nomor_rw'] }}</option>
                        @endforeach
                    </select>
                    @error('data_rw_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- FORM PROFIL RW LENGKAP -->
                @if($data_rw_id)
                <div class="space-y-4">
                    
                    <!-- 1. PROFIL WILAYAH -->
                    <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                        <div class="flex items-center gap-2 mb-6 border-b pb-2">
                            <h3 class="text-lg font-medium text-amber-600 uppercase tracking-wide">Profil Wilayah</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipologi RW</label>
                                <select wire:model.live="tipologi" class="py-2.5 px-3 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none">
                                    <option value="">- Pilih -</option>
                                    @foreach(\App\Models\ProfilRw::TIPOLOGI_OPTIONS as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sumber Ekonomi Dominan</label>
                                <select wire:model.live="ekonomi_dominan" class="py-2.5 px-3 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none">
                                    <option value="">- Pilih -</option>
                                    @foreach(\App\Models\ProfilRw::EKONOMI_OPTIONS as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Profil Umum Warga</label>
                                <select wire:model="profil_warga" class="py-2.5 px-3 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:border-orange-500 text-base sm:text-lg">
                                    <option value="">- Pilih Profil -</option>
                                    @foreach(\App\Models\ProfilRw::PROFIL_OPTIONS as $label)
                                        <option value="{{ $label }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <textarea wire:model="profil_warga_keterangan" class="py-2.5 px-3 mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:border-orange-500 text-base sm:text-lg" rows="2" placeholder="Keterangan tambahan (opsional)"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Faktor Penyebab Menang/Kalah</label>
                                <select wire:model="faktor_penyebab" class="py-2.5 px-3 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:border-orange-500 text-base sm:text-lg">
                                    <option value="">- Pilih Faktor Utama -</option>
                                    @foreach(\App\Models\ProfilRw::FAKTOR_OPTIONS as $label)
                                        <option value="{{ $label }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <textarea wire:model="faktor_penyebab_keterangan" class="py-2.5 px-3 mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:border-orange-500 text-base sm:text-lg" rows="2" placeholder="Keterangan tambahan (opsional)"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 2. INFRASTRUKTUR PARTAI -->
                    <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                        <div class="flex items-center gap-2 mb-6 border-b pb-2">
                            <h3 class="text-lg font-medium text-emerald-600 uppercase tracking-wide">Infrastruktur Partai</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Anggota PKS di RW</label>
                                <textarea wire:model="anggota_pks" class="py-2.5 px-3 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none" rows="2" placeholder="Nama + jenjang keanggotaan"></textarea>
                            </div>

                            <div class="rounded-lg border border-gray-200 p-4 bg-gray-50">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700">UPA RW</label>
                                    <select wire:model.live="upa_rw_status" class="py-2.5 px-3 block w-32 border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none font-medium">
                                        <option value="belum">Belum</option>
                                        <option value="sudah">Sudah</option>
                                    </select>
                                </div>
                                @if($upa_rw_status === 'sudah')
                                <div class="space-y-2 mt-3 pt-3 border-t border-gray-200">
                                    <input type="text" wire:model="upa_rw_nama_input" class="py-2.5 px-3 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none" placeholder="Nama pembina">
                                    <input type="text" wire:model="upa_rw_no_hp_input" class="py-2.5 px-3 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none" placeholder="No WhatsApp (contoh: 08123456789)">
                                </div>
                                @endif
                            </div>

                            <div class="rounded-lg border border-gray-200 p-4 bg-gray-50">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700">RKI</label>
                                    <select wire:model.live="rki_status" class="py-2.5 px-3 block w-32 border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none font-medium">
                                        <option value="belum">Belum</option>
                                        <option value="sudah">Sudah</option>
                                    </select>
                                </div>
                                @if($rki_status === 'sudah')
                                <div class="space-y-2 mt-3 pt-3 border-t border-gray-200">
                                    <input type="text" wire:model="rki_nama_input" class="py-2.5 px-3 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none" placeholder="Nama penggerak">
                                    <input type="text" wire:model="rki_no_hp_input" class="py-2.5 px-3 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none" placeholder="No WhatsApp (contoh: 08123456789)">
                                </div>
                                @endif
                            </div>

                            <div class="rounded-lg border border-gray-200 p-4 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-medium text-gray-700">Titik Senam PKS</label>
                                    <select wire:model.live="senam_status" class="py-2.5 px-3 block w-32 border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none font-medium">
                                        <option value="belum">Belum</option>
                                        <option value="sudah">Sudah</option>
                                    </select>
                                </div>
                                @if($senam_status === 'sudah')
                                    <div class="mt-3 space-y-2">
                                        <input type="text" wire:model="senam_nama_input" class="py-2.5 px-3 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:border-orange-500 text-base sm:text-lg" placeholder="Nama instruktur / lokasi">
                                        <input type="text" wire:model="senam_no_hp_input" class="py-2.5 px-3 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:border-orange-500 text-base sm:text-lg" placeholder="No WhatsApp (contoh: 08123456789)">
                                    </div>
                                @endif
                            </div>

                            <div class="rounded-lg border border-gray-200 p-4 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-medium text-gray-700">Relawan Milenial / Geka</label>
                                    <select wire:model.live="relawan_milenial_status" class="py-2.5 px-3 block w-32 border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none font-medium">
                                        <option value="belum">Belum</option>
                                        <option value="sudah">Sudah</option>
                                    </select>
                                </div>
                                @if($relawan_milenial_status === 'sudah')
                                    <div class="mt-3 space-y-2">
                                        <input type="text" wire:model="relawan_milenial_nama_input" class="py-2.5 px-3 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:border-orange-500 text-base sm:text-lg" placeholder="Nama + jabatan">
                                        <input type="text" wire:model="relawan_milenial_no_hp_input" class="py-2.5 px-3 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:border-orange-500 text-base sm:text-lg" placeholder="No WhatsApp (contoh: 08123456789)">
                                    </div>
                                @endif
                            </div>

                            <div class="rounded-lg border border-gray-200 p-4 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-medium text-gray-700">Caleg terpilih di RW?</label>
                                    <select wire:model.live="caleg_terpilih_ada" class="py-2.5 px-3 block w-32 border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none font-medium">
                                        <option value="0">Tidak</option>
                                        <option value="1">Ya</option>
                                    </select>
                                </div>
                                @if($caleg_terpilih_ada == 1)
                                    <div class="mt-3">
                                        <input type="text" wire:model="caleg_terpilih_nama" class="py-2.5 px-3 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:border-orange-500 text-base sm:text-lg" placeholder="Nama caleg">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 3. PETA POLITIK LOKAL -->
                    <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                        <div class="flex items-center gap-2 mb-6 border-b pb-2">
                            <h3 class="text-lg font-medium text-red-600 uppercase tracking-wide">Peta Politik Lokal</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Afiliasi Ketua RW & RT</label>
                                <textarea wire:model="afiliasi_rw_rt" class="py-2.5 px-3 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none" rows="3" placeholder="Ketua RW: Nama - Partai&#10;RT 1: Nama - Partai"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Afiliasi Kader Posyandu & DKM</label>
                                <textarea wire:model="afiliasi_posyandu_dkm" class="py-2.5 px-3 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none" rows="2" placeholder="Nama - organisasi - partai"></textarea>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="rounded-lg border border-gray-200 p-4 bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-sm font-medium text-gray-700">Pengurus kompetitor?</label>
                                        <select wire:model.live="kompetitor_status" class="py-2.5 px-3 block w-32 border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none font-medium">
                                            <option value="tidak_tahu">Tidak tahu</option>
                                            <option value="ada">Ada</option>
                                            <option value="tidak">Tidak ada</option>
                                        </select>
                                    </div>
                                    @if($kompetitor_status === 'ada')
                                        <input type="text" wire:model="kompetitor_detail" class="py-2.5 px-3 mt-3 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none" placeholder="Nama + partai">
                                    @endif
                                </div>
                                <div class="rounded-lg border border-gray-200 p-4 bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-sm font-medium text-gray-700">Tim sukses lain?</label>
                                        <select wire:model.live="tim_sukses_status" class="py-2.5 px-3 block w-32 border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none font-medium">
                                            <option value="tidak_tahu">Tidak tahu</option>
                                            <option value="ada">Ada</option>
                                            <option value="tidak">Tidak ada</option>
                                        </select>
                                    </div>
                                    @if($tim_sukses_status === 'ada')
                                        <input type="text" wire:model="tim_sukses_detail" class="py-2.5 px-3 mt-3 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none" placeholder="Nama + partai">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. STRATEGI & PENANGGUNG JAWAB -->
                    <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8 border border-gray-200">
                        <div class="flex items-center gap-2 mb-6 border-b pb-2">
                            <h3 class="text-lg font-medium text-orange-600 uppercase tracking-wide">Strategi & Penanggung Jawab</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Strategi Mencapai Target Suara</label>
                                <select wire:model="strategi" class="py-2.5 px-3 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:border-orange-500 text-base sm:text-lg">
                                    <option value="">- Pilih Strategi Utama -</option>
                                    @foreach(\App\Models\ProfilRw::STRATEGI_OPTIONS as $label)
                                        <option value="{{ $label }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <textarea wire:model="strategi_keterangan" class="py-2.5 px-3 mt-2 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:border-orange-500 text-base sm:text-lg" rows="2" placeholder="Keterangan tambahan (opsional)"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Penanggung Jawab Dakwah di RW</label>
                                <input type="text" wire:model="penanggung_jawab" class="py-2.5 px-3 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none" placeholder="Nama + jenjang">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Lain</label>
                                <textarea wire:model="keterangan_lain" class="py-2.5 px-3 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-0 focus:outline-none focus:ring-0 focus:border-orange-500 focus:outline-none text-base sm:text-lg focus:outline-none" rows="2" placeholder="Catatan tambahan"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Collapsible Data Pemilih -->
                    <div x-data="{ expanded: false }" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <button type="button" @click="expanded = !expanded" class="w-full px-6 py-4 flex justify-between items-center bg-gray-50 hover:bg-gray-100 transition-colors focus:outline-none">
                            <h2 class="text-base font-medium text-gray-900">Lihat Data Pemilih (DPT) & Demografi</h2>
                            <svg :class="{'rotate-180': expanded}" class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="expanded" class="p-6 border-t border-gray-200" style="display: none;">
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Total DPT</p>
                                    <p class="text-lg font-medium text-gray-900">{{ $dpt ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Laki-laki</p>
                                    <p class="text-lg font-medium text-gray-900">{{ $dpt_laki ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Perempuan</p>
                                    <p class="text-lg font-medium text-gray-900">{{ $dpt_perempuan ?? '-' }}</p>
                                </div>
                            </div>
                            <hr class="my-4 border-gray-100">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Gen Z</p>
                                    <p class="text-lg font-medium text-gray-900">{{ $gen_z ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Millennial</p>
                                    <p class="text-lg font-medium text-gray-900">{{ $millennial ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Gen X</p>
                                    <p class="text-lg font-medium text-gray-900">{{ $gen_x ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Boomer</p>
                                    <p class="text-lg font-medium text-gray-900">{{ $boomer ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Collapsible Infrastruktur & Target Suara -->
                    <div x-data="{ expanded: false }" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <button type="button" @click="expanded = !expanded" class="w-full px-6 py-4 flex justify-between items-center bg-gray-50 hover:bg-gray-100 transition-colors focus:outline-none">
                            <h2 class="text-base font-medium text-gray-900">Lihat Infrastruktur & Target Suara</h2>
                            <svg :class="{'rotate-180': expanded}" class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="expanded" class="p-6 border-t border-gray-200" style="display: none;">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Jumlah RT</p>
                                    <p class="text-lg font-medium text-gray-900">{{ $jumlah_rt ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Jumlah TPS</p>
                                    <p class="text-lg font-medium text-gray-900">{{ $jumlah_tps ?? '-' }}</p>
                                </div>
                            </div>
                            <hr class="my-4 border-gray-100">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Estimasi Suara PKS</p>
                                    <p class="text-lg font-medium text-gray-900">{{ $estimasi_pks ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Target Suara per RW</p>
                                    <p class="text-lg font-medium text-orange-600">{{ $target_suara_per_rw ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Estimasi Share (%)</p>
                                    <p class="text-lg font-medium text-gray-900">{{ $estimasi_share ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Estimasi Ranking</p>
                                    <p class="text-lg font-medium text-gray-900">{{ $estimasi_ranking ?? '-' }}</p>
                                </div>
                            </div>
                            <hr class="my-4 border-gray-100">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Status Wilayah</p>
                                    <p class="text-lg font-medium text-gray-900">{{ $status_wilayah ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Prioritas Urutan</p>
                                    <p class="text-lg font-medium text-gray-900">{{ $prioritas_urutan ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-2">
                    <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-0 focus:outline-none">
                        Kirim
                    </button>
                    <button type="button" wire:click="$set('data_rw_id', '')" class="text-sm text-gray-500 hover:text-gray-900 font-medium">
                        Kosongkan formulir
                    </button>
                </div>
                @endif

            </form>
        @endif
    </div>
</div>
