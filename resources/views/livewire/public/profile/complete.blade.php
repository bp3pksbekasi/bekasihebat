<div class="max-w-[620px] mx-auto px-5 py-8">
    <div class="mb-5">
        <h1 class="text-2xl font-medium text-zinc-900 mb-1">Lengkapi profil Anda</h1>
        <p class="text-sm text-zinc-600">Data ini diperlukan untuk daftar event dan kartu anggota digital Anda.</p>
    </div>

    <form wire:submit="save">
        <div class="bg-white border border-zinc-200 rounded-2xl p-5 mb-4">
            <div class="flex items-center gap-2.5 mb-4 pb-3 border-b border-zinc-100">
                <div class="w-7 h-7 rounded-md flex items-center justify-center" style="background: #fff7f1;">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" style="color: #fe5000;">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-zinc-900">Identitas</div>
                    <div class="text-xs text-zinc-500">Data dasar untuk profil anggota</div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <flux:field>
                    <flux:label>Tanggal Lahir</flux:label>
                    <flux:input wire:model="birth_date" type="date" />
                    <flux:error name="birth_date" />
                </flux:field>

                <flux:field>
                    <flux:label>Jenis Kelamin</flux:label>
                    <select wire:model="gender" class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <option value="">Pilih...</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                    <flux:error name="gender" />
                </flux:field>
            </div>
        </div>

        <div class="bg-white border border-zinc-200 rounded-2xl p-5 mb-5">
            <div class="flex items-center gap-2.5 mb-4 pb-3 border-b border-zinc-100">
                <div class="w-7 h-7 rounded-md flex items-center justify-center" style="background: #fff7f1;">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" style="color: #fe5000;">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-medium text-zinc-900">Alamat & Wilayah</div>
                    <div class="text-xs text-zinc-500">Kab. Bekasi, Jawa Barat</div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <flux:field>
                    <flux:label>Kecamatan</flux:label>
                    <select wire:model.change="kecamatan_code" class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <option value="">Pilih kecamatan...</option>
                        @foreach ($this->kecamatanList as $kec)
                            <option value="{{ $kec->code }}">{{ Str::title(strtolower($kec->name)) }}</option>
                        @endforeach
                    </select>
                    <flux:error name="kecamatan_code" />
                </flux:field>

                <flux:field>
                    <flux:label>Kelurahan</flux:label>
                    <select wire:model="kelurahan_code" @disabled(! $kecamatan_code) class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 disabled:bg-zinc-100 disabled:text-zinc-400 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <option value="">{{ $kecamatan_code ? 'Pilih kelurahan...' : 'Pilih kecamatan dulu' }}</option>
                        @foreach ($this->kelurahanList as $kel)
                            <option value="{{ $kel->code }}">{{ Str::title(strtolower($kel->name)) }}</option>
                        @endforeach
                    </select>
                    <flux:error name="kelurahan_code" />
                </flux:field>
            </div>

            @if ($kelurahan_code)
                <div class="mb-3 rounded-xl border border-orange-100 bg-orange-50 px-3 py-2">
                    <div class="text-[11px] font-medium text-orange-700">Wilayah terpilih</div>
                    <div class="text-sm text-zinc-700">
                        {{ collect($this->kelurahanList)->firstWhere('code', $kelurahan_code)?->name ? Str::title(strtolower(collect($this->kelurahanList)->firstWhere('code', $kelurahan_code)?->name)) : '-' }},
                        {{ collect($this->kecamatanList)->firstWhere('code', $kecamatan_code)?->name ? Str::title(strtolower(collect($this->kecamatanList)->firstWhere('code', $kecamatan_code)?->name)) : '-' }},
                        Kabupaten Bekasi
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <flux:field>
                    <flux:label>RT</flux:label>
                    <select wire:model="rt" class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <option value="">Pilih RT...</option>
                        @foreach ($this->rtOptions() as $number)
                            <option value="{{ $number }}">{{ str_pad((string) $number, 3, '0', STR_PAD_LEFT) }}</option>
                        @endforeach
                    </select>
                    <flux:error name="rt" />
                </flux:field>

                <flux:field>
                    <flux:label>RW</flux:label>
                    <select wire:model="rw" class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200">
                        <option value="">Pilih RW...</option>
                        @foreach ($this->rwOptions() as $number)
                            <option value="{{ $number }}">{{ str_pad((string) $number, 3, '0', STR_PAD_LEFT) }}</option>
                        @endforeach
                    </select>
                    <flux:error name="rw" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Alamat Lengkap</flux:label>
                <flux:textarea wire:model="address_detail" rows="3" placeholder="Jl. Contoh No. 123, Blok A2, Perumahan..." />
                <flux:error name="address_detail" />
            </flux:field>
        </div>

        <button
            type="submit"
            class="w-full py-3 rounded-xl text-white font-medium text-sm mb-3"
            style="background: #fe5000;"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove wire:target="save">Simpan & Lengkapi</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </button>

        <div class="text-center">
            <a href="{{ route('member.dashboard') }}" class="text-xs text-zinc-500">Kembali ke dashboard</a>
        </div>
    </form>
</div>
