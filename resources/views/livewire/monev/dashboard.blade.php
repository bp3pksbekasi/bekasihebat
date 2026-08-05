<div>

    {{-- NOTIFY --}}
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3500)"
        x-show="show"
        x-transition
        class="fixed top-4 right-4 z-50 flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-medium shadow-lg"
        :class="type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'"
        style="display:none;"
    >
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span x-text="message"></span>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-6">

        {{-- HEADER --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900">Monev Lapangan</h1>
                <p class="mt-0.5 text-sm text-zinc-500">Deteksi otomatis wilayah yang perlu perhatian berdasarkan data lapangan</p>
            </div>
            @if($isDpd)
            <div class="flex items-center gap-2">
                <label class="text-xs text-zinc-500 font-medium">Pilih Kecamatan:</label>
                <select wire:model.live="filterKecamatan" class="rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-sm text-zinc-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">— Pilih Kecamatan —</option>
                    @foreach($this->kecamatanOptions as $kec)
                        <option value="{{ $kec }}">{{ $kec }}</option>
                    @endforeach
                </select>
                <div wire:loading wire:target="filterKecamatan">
                    <svg class="animate-spin h-4 w-4 text-orange-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
            </div>
            @endif
        </div>

        {{-- BELUM PILIH KECAMATAN (DPD/Admin) --}}
        @if(! $loaded)
            <div class="flex flex-col items-center justify-center rounded-2xl bg-white border border-zinc-100 shadow-sm py-20 text-center">
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-orange-50">
                    <svg class="h-8 w-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <h3 class="text-base font-bold text-zinc-800">Pilih kecamatan untuk mulai analisa</h3>
                <p class="mt-1.5 text-sm text-zinc-400 max-w-xs">Data flag dihitung per kecamatan. Pilih dulu dari dropdown di atas agar tidak berat.</p>
                <div class="mt-6">
                    <select wire:model.live="filterKecamatan" class="rounded-xl border-2 border-orange-200 bg-orange-50 px-4 py-2.5 text-sm font-semibold text-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="">— Pilih Kecamatan —</option>
                        @foreach($this->kecamatanOptions as $kec)
                            <option value="{{ $kec }}">{{ $kec }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @else

        {{-- RINGKASAN SCORECARD --}}
        @php $r = $this->ringkasan; @endphp
        <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-xl bg-white p-4 shadow-sm border border-zinc-100">
                <div class="text-2xl font-bold text-zinc-800">{{ $r['total'] }}</div>
                <div class="mt-0.5 text-xs text-zinc-500">Total Flag Aktif</div>
            </div>
            <div class="rounded-xl bg-red-50 p-4 shadow-sm border border-red-100">
                <div class="text-2xl font-bold text-red-600">{{ $r['merah'] }}</div>
                <div class="mt-0.5 text-xs text-red-400">🔴 Flag Merah</div>
            </div>
            <div class="rounded-xl bg-amber-50 p-4 shadow-sm border border-amber-100">
                <div class="text-2xl font-bold text-amber-600">{{ $r['kuning'] }}</div>
                <div class="mt-0.5 text-xs text-amber-400">🟡 Flag Kuning</div>
            </div>
            <div class="rounded-xl bg-emerald-50 p-4 shadow-sm border border-emerald-100">
                <div class="text-2xl font-bold text-emerald-600">{{ $r['sudah_dicatat'] }}</div>
                <div class="mt-0.5 text-xs text-emerald-400">✅ Sudah Dicatat</div>
            </div>
        </div>

        {{-- SECTION 1: SCORECARD FLAG --}}
        <div class="mb-6 rounded-2xl bg-white shadow-sm border border-zinc-100 overflow-hidden">
            <div class="border-b border-zinc-100 px-5 py-4 flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-zinc-800">🚨 Deteksi Otomatis — Flag Wilayah Bermasalah</h2>
                    <p class="mt-0.5 text-xs text-zinc-400">Dihitung dari data sisir RW, Korwe/Korte, Penggalang Suara, dan Profil RW yang sudah ada</p>
                </div>
                <div wire:loading wire:target="filterKecamatan">
                    <svg class="animate-spin h-5 w-5 text-orange-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>
            </div>

            @if($this->flagsGrouped->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-zinc-400">
                    <svg class="h-10 w-10 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-sm font-medium">Tidak ada flag aktif</div>
                    <div class="text-xs mt-1">Semua wilayah dalam kondisi normal</div>
                </div>
            @else
                <div class="divide-y divide-zinc-50">
                    @foreach($this->flagsGrouped as $kecamatan => $desaGroup)
                        <div class="px-5 py-3">
                            <div class="mb-2 text-xs font-semibold uppercase tracking-widest text-zinc-400">{{ $kecamatan }}</div>
                            @foreach($desaGroup as $desa => $flags)
                                <div class="mb-3">
                                    <div class="mb-1.5 text-xs font-medium text-zinc-500">{{ $desa }}</div>
                                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                        @foreach($flags as $flag)
                                            @php
                                                $jenisLabel = \App\Models\CatatanMonev::JENIS_OPTIONS[$flag['jenis']] ?? $flag['jenis'];
                                                $isMerah = $flag['severity'] === 'merah';
                                                $hasOpen = $flag['has_open_catatan'] ?? false;
                                            @endphp
                                            <div class="relative rounded-xl border p-3.5 transition-all {{ $hasOpen ? 'opacity-50 bg-zinc-50 border-zinc-200' : ($isMerah ? 'border-red-200 bg-red-50' : 'border-amber-200 bg-amber-50') }}">
                                                @if($hasOpen)
                                                    <span class="absolute right-2 top-2 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">Sudah Dicatat</span>
                                                @endif
                                                <div class="flex items-start gap-2 mb-2">
                                                    <span class="text-base leading-none">{{ $isMerah ? '🔴' : '🟡' }}</span>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-xs font-bold {{ $isMerah ? 'text-red-700' : 'text-amber-700' }}">RW {{ $flag['nomor_rw'] }}</div>
                                                        <div class="text-[10px] font-medium {{ $isMerah ? 'text-red-500' : 'text-amber-500' }}">{{ $jenisLabel }}</div>
                                                    </div>
                                                </div>
                                                <p class="text-xs text-zinc-600 leading-snug mb-3">{{ $flag['detail'] }}</p>
                                                @if(! $hasOpen)
                                                    <button
                                                        wire:click="bukaFormCatat({{ json_encode($flag) }})"
                                                        class="w-full rounded-lg bg-white border border-zinc-200 px-3 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 transition-colors"
                                                    >
                                                        + Catat Temuan
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- SECTION 2: DAFTAR CATATAN MONEV --}}
        <div class="mb-6 rounded-2xl bg-white shadow-sm border border-zinc-100 overflow-hidden">
            <div class="border-b border-zinc-100 px-5 py-4 flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-zinc-800">📋 Catatan Temuan & Tindak Lanjut</h2>
                </div>
                <div class="flex gap-1 rounded-lg bg-zinc-100 p-0.5">
                    <button wire:click="$set('filterStatus', 'terbuka')" class="rounded-md px-3 py-1 text-xs font-medium transition-colors {{ $filterStatus === 'terbuka' ? 'bg-white text-zinc-800 shadow-sm' : 'text-zinc-500 hover:text-zinc-700' }}">Terbuka</button>
                    <button wire:click="$set('filterStatus', 'selesai')" class="rounded-md px-3 py-1 text-xs font-medium transition-colors {{ $filterStatus === 'selesai' ? 'bg-white text-zinc-800 shadow-sm' : 'text-zinc-500 hover:text-zinc-700' }}">Selesai</button>
                </div>
            </div>

            <div wire:loading wire:target="updatedFilterStatus" class="px-5 py-8 text-center">
                <svg class="animate-spin h-5 w-5 text-zinc-400 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>

            <div wire:loading.remove wire:target="updatedFilterStatus">
                @if($this->catatanList->isEmpty())
                    <div class="py-10 text-center text-sm text-zinc-400">Belum ada catatan {{ $filterStatus }}.</div>
                @else
                    <div class="divide-y divide-zinc-50">
                        @foreach($this->catatanList as $catatan)
                            <div class="px-5 py-4">
                                <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-[10px] font-semibold text-zinc-600">{{ $catatan->jenis_label }}</span>
                                            <span class="text-xs font-medium text-zinc-700">{{ $catatan->targetWilayah?->kecamatan }} — {{ $catatan->targetWilayah?->desa }} RW {{ $catatan->nomor_rw }}</span>
                                            @if($catatan->sumber === 'otomatis')
                                                <span class="rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-600">Auto</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-zinc-700">{{ $catatan->temuan }}</p>
                                        @if($catatan->tindak_lanjut)
                                            <p class="mt-1 text-xs text-zinc-500">↳ <em>{{ $catatan->tindak_lanjut }}</em></p>
                                        @endif
                                        <div class="mt-2 flex flex-wrap gap-3 text-[10px] text-zinc-400">
                                            <span>PIC: {{ $catatan->pic_nama ?: '-' }}</span>
                                            <span>Level: {{ strtoupper($catatan->level_penanggung_jawab ?? '-') }}</span>
                                            <span>Umur: {{ $catatan->umur_hari }} hari</span>
                                            <span>Oleh: {{ $catatan->creator?->name ?? '-' }}</span>
                                        </div>
                                    </div>
                                    @if($catatan->isTerbuka())
                                        <div class="mt-2 sm:mt-0 sm:ml-4">
                                            <button wire:click="bukaFormSelesai('{{ $catatan->id }}')" class="rounded-lg bg-emerald-50 border border-emerald-200 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition-colors whitespace-nowrap">
                                                ✓ Tandai Selesai
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- SECTION 3: AKUNTABILITAS DPC --}}
        @if($isDpd || $isDpc)
        <div class="rounded-2xl bg-white shadow-sm border border-zinc-100 overflow-hidden">
            <div class="border-b border-zinc-100 px-5 py-4">
                <h2 class="font-semibold text-zinc-800">📊 Akuntabilitas per Kecamatan (DPC)</h2>
                <p class="mt-0.5 text-xs text-zinc-400">Diurutkan dari tingkat tindak lanjut terendah — kecamatan paling atas perlu perhatian DPD</p>
            </div>
            @if($this->akuntabilitasDpc->isEmpty())
                <div class="py-10 text-center text-sm text-zinc-400">Belum ada data flag untuk ditampilkan.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-100 bg-zinc-50 text-left text-xs text-zinc-500">
                                <th class="px-5 py-3 font-semibold">Kecamatan</th>
                                <th class="px-4 py-3 font-semibold text-right">Total Flag</th>
                                <th class="px-4 py-3 font-semibold text-right">% Ditindaklanjuti</th>
                                <th class="px-4 py-3 font-semibold text-right">Rata-rata Selesai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-50">
                            @foreach($this->akuntabilitasDpc as $row)
                                @php
                                    $pct = $row['pct_tindak_lanjut'];
                                    $rowColor = $pct < 30 ? 'bg-red-50' : ($pct < 70 ? 'bg-amber-50' : 'bg-emerald-50');
                                    $barColor = $pct < 30 ? 'bg-red-400' : ($pct < 70 ? 'bg-amber-400' : 'bg-emerald-500');
                                @endphp
                                <tr class="{{ $rowColor }}">
                                    <td class="px-5 py-3 font-medium text-zinc-700">{{ $row['kecamatan'] }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-zinc-800">{{ $row['total_flag'] }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <div class="w-20 rounded-full bg-zinc-200 h-1.5 overflow-hidden">
                                                <div class="{{ $barColor }} h-full rounded-full" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <span class="w-10 text-right font-semibold {{ $pct < 30 ? 'text-red-600' : ($pct < 70 ? 'text-amber-600' : 'text-emerald-600') }}">{{ $pct }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right text-zinc-600">
                                        {{ $row['avg_umur_selesai'] !== null ? $row['avg_umur_selesai'] . ' hari' : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @endif

        @endif {{-- end @if($loaded) --}}
    </div>
</div>

{{-- MODAL: FORM CATAT TEMUAN --}}
@if($showCatatForm)
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 px-4" wire:click.self="tutupFormCatat">
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-zinc-100 px-5 py-4">
            <div>
                <h3 class="font-bold text-zinc-800">Catat Temuan</h3>
                <p class="text-xs text-zinc-400 mt-0.5">RW {{ $selectedFlag['nomor_rw'] ?? '' }} — {{ $selectedFlag['desa'] ?? '' }}, {{ $selectedFlag['kecamatan'] ?? '' }}</p>
            </div>
            <button wire:click="tutupFormCatat" class="text-zinc-400 hover:text-zinc-600">✕</button>
        </div>
        <div class="p-5 space-y-4">
            <div class="rounded-lg bg-zinc-50 border border-zinc-200 px-4 py-3 text-xs text-zinc-600">
                <div class="font-semibold text-zinc-700 mb-0.5">{{ \App\Models\CatatanMonev::JENIS_OPTIONS[$selectedFlag['jenis'] ?? ''] ?? '' }}</div>
                <div>{{ $selectedFlag['detail'] ?? '' }}</div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-zinc-600">Keterangan Temuan <span class="text-red-500">*</span></label>
                <textarea wire:model="formTemuan" rows="3" placeholder="Jelaskan kondisi di lapangan..." class="w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"></textarea>
                @error('formTemuan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-zinc-600">Rencana Tindak Lanjut <span class="text-zinc-400">(opsional)</span></label>
                <textarea wire:model="formTindakLanjut" rows="2" placeholder="Apa yang akan dilakukan?" class="w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-zinc-600">Level Penanggung Jawab</label>
                    <select wire:model="formLevel" class="w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                        @foreach(\App\Models\CatatanMonev::LEVEL_OPTIONS as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-zinc-600">Nama PIC <span class="text-zinc-400">(opsional)</span></label>
                    <input wire:model="formPicNama" type="text" placeholder="Nama koordinator..." class="w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
            </div>
        </div>
        <div class="flex gap-2 border-t border-zinc-100 px-5 py-4">
            <button wire:click="tutupFormCatat" class="flex-1 rounded-lg border border-zinc-200 py-2.5 text-sm font-semibold text-zinc-600 hover:bg-zinc-50">Batal</button>
            <button wire:click="simpanCatatan" wire:loading.attr="disabled" class="flex-1 rounded-lg bg-orange-600 py-2.5 text-sm font-bold text-white hover:bg-orange-700 disabled:opacity-60">
                <span wire:loading.remove wire:target="simpanCatatan">Simpan Catatan</span>
                <span wire:loading wire:target="simpanCatatan">Menyimpan...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- MODAL: FORM TANDAI SELESAI --}}
@if($showSelesaiForm)
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 px-4" wire:click.self="tutupFormSelesai">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-zinc-100 px-5 py-4">
            <h3 class="font-bold text-zinc-800">Tandai Selesai</h3>
            <button wire:click="tutupFormSelesai" class="text-zinc-400 hover:text-zinc-600">✕</button>
        </div>
        <div class="p-5">
            <label class="mb-1 block text-xs font-semibold text-zinc-600">Tindak Lanjut yang Dilakukan <span class="text-zinc-400">(opsional)</span></label>
            <textarea wire:model="selesaiTindakLanjut" rows="3" placeholder="Ceritakan apa yang sudah dilakukan..." class="w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 resize-none"></textarea>
        </div>
        <div class="flex gap-2 border-t border-zinc-100 px-5 py-4">
            <button wire:click="tutupFormSelesai" class="flex-1 rounded-lg border border-zinc-200 py-2.5 text-sm font-semibold text-zinc-600 hover:bg-zinc-50">Batal</button>
            <button wire:click="tandaiSelesai" wire:loading.attr="disabled" class="flex-1 rounded-lg bg-emerald-600 py-2.5 text-sm font-bold text-white hover:bg-emerald-700 disabled:opacity-60">
                <span wire:loading.remove wire:target="tandaiSelesai">✓ Konfirmasi Selesai</span>
                <span wire:loading wire:target="tandaiSelesai">Menyimpan...</span>
            </button>
        </div>
    </div>
</div>
@endif
