<div>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Antrean Profil RW</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data persetujuan survei profil RW dari masyarakat.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <select wire:model.live="selectedDapil" class="border-gray-300 rounded-md shadow-sm py-2.5 px-3 focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                <option value="">Semua Dapil</option>
                @foreach($this->dapilOptions as $dapil)
                    <option value="{{ $dapil }}">{{ $dapil }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedKecamatan" class="border-gray-300 rounded-md shadow-sm py-2.5 px-3 focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                <option value="">Semua Kecamatan</option>
                @foreach($this->kecamatanOptions as $kecamatan)
                    <option value="{{ $kecamatan }}">{{ $kecamatan }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedDesa" class="border-gray-300 rounded-md shadow-sm py-2.5 px-3 focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                <option value="">Semua Desa</option>
                @foreach($this->desaOptions as $desa)
                    <option value="{{ $desa }}">{{ $desa }}</option>
                @endforeach
            </select>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, no hp..." class="w-full sm:w-64 border-gray-300 rounded-md shadow-sm py-2.5 px-3 focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl / Waktu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengisi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Desa / RW</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($submissions as $row)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $row->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $row->nama_pengisi }}</div>
                                <div class="text-sm text-gray-500">{{ $row->no_hp_pengisi }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $row->desa }} <br>
                                <span class="text-gray-500 text-xs">RW {{ str_pad($row->nomor_rw, 3, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($row->status === 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @elseif($row->status === 'approved')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($row->status === 'pending')
                                    <button wire:click="viewDetail({{ $row->id }})" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                        Tinjau Data
                                    </button>
                                @else
                                    <button wire:click="viewDetail({{ $row->id }})" class="text-gray-500 hover:text-gray-700 px-2 py-1 text-xs">
                                        Lihat Detail
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">
                                Tidak ada data antrean pengajuan profil RW.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($submissions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>

    @if($showDetail && $detailRecord)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:40;" wire:click="closeDetail"></div>
        <div style="position:fixed;top:0;right:0;width:560px;max-width:100%;height:100%;background:white;box-shadow:-8px 0 24px rgba(0,0,0,0.16);z-index:50;overflow-y:auto;display:flex;flex-direction:column;">
            <div style="padding:16px 24px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;background:#fafafa;position:sticky;top:0;z-index:10;">
                <div class="flex flex-col">
                    <h3 style="font-size:18px;font-weight:700;color:#111827;margin:0;">Detail Profil RW</h3>
                    @if($detailRecord->status === 'pending')
                        <span class="text-xs text-yellow-600 font-medium mt-1">Menunggu Persetujuan</span>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @if($detailRecord->status === 'pending')
                        <button wire:click="approve({{ $detailRecord->id }})" onclick="confirm('Yakin ingin menyetujui pengajuan ini? Data riil RW akan diperbarui.') || event.stopImmediatePropagation()" class="text-white bg-green-600 hover:bg-green-700 px-4 py-2 rounded-md shadow-sm text-sm font-semibold transition-colors">
                            Approve
                        </button>
                        <button wire:click="reject({{ $detailRecord->id }})" onclick="confirm('Yakin ingin menolak pengajuan ini?') || event.stopImmediatePropagation()" class="text-red-600 bg-red-50 border border-red-200 hover:bg-red-100 px-4 py-2 rounded-md shadow-sm text-sm font-semibold transition-colors">
                            Reject
                        </button>
                    @endif
                    <div class="w-px h-6 bg-gray-300 mx-1"></div>
                    <button type="button" wire:click="closeDetail" style="background:none;border:none;cursor:pointer;color:#6b7280;padding:6px;" class="hover:bg-gray-200 rounded-full transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            </div>
            
            <div style="flex:1;padding:28px 24px;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 text-sm text-gray-800">
                    <div><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Nama Pengisi</span> <div class="font-medium text-gray-900 text-base">{{ $detailRecord->nama_pengisi }}</div></div>
                    <div><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">No HP</span> <div class="font-medium text-gray-900 text-base">{{ $detailRecord->no_hp_pengisi }}</div></div>
                    
                    <div class="col-span-1 md:col-span-2"><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Wilayah</span> <div class="font-medium text-gray-900 text-base">{{ $detailRecord->dapil }} &middot; {{ $detailRecord->kecamatan }} &middot; {{ $detailRecord->desa }} &middot; RW {{ str_pad($detailRecord->nomor_rw, 3, '0', STR_PAD_LEFT) }}</div></div>
                    <div class="col-span-1 md:col-span-2"><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Tipologi</span> <div class="font-medium text-gray-900">{{ $detailRecord->tipologi ?? '-' }}</div></div>
                    
                    <div><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">DPT (L+P)</span> <div class="font-medium text-gray-900">{{ $detailRecord->dpt ?? '-' }} <span class="text-gray-500 text-xs ml-1">({{ $detailRecord->dpt_laki ?? '0' }} L, {{ $detailRecord->dpt_perempuan ?? '0' }} P)</span></div></div>
                    <div><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Demografi Pemilih</span> <div class="font-medium text-gray-900">Gen Z: {{ $detailRecord->gen_z ?? '-' }} | Milenial: {{ $detailRecord->millennial ?? '-' }} | Gen X: {{ $detailRecord->gen_x ?? '-' }} | Boomer: {{ $detailRecord->boomer ?? '-' }}</div></div>
                    
                    <div class="col-span-1 md:col-span-2 border-t border-gray-200 pt-6 mt-2"><h4 class="font-bold text-gray-900 text-base flex items-center gap-2"><span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-sm">A</span> Analisa Politik & Elektoral</h4></div>
                    
                    <div class="col-span-1 md:col-span-2"><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Karakteristik & Profil Warga</span> <div class="font-medium text-gray-900">{{ $detailRecord->profil_warga ?? '-' }}</div>@if($detailRecord->profil_warga_keterangan)<div class="text-gray-500 mt-1 italic">{{ $detailRecord->profil_warga_keterangan }}</div>@endif</div>
                    
                    <div class="col-span-1 md:col-span-2"><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Suara PKS Pemilu 2019/2024</span> <div class="font-medium text-gray-900">{{ $detailRecord->suara_pks_2019 ?? '-' }}</div></div>
                    <div class="col-span-1 md:col-span-2"><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Faktor Kemenangan/Kekalahan PKS</span> <div class="font-medium text-gray-900">{{ $detailRecord->faktor_penyebab ?? '-' }}</div>@if($detailRecord->faktor_penyebab_keterangan)<div class="text-gray-500 mt-1 italic">{{ $detailRecord->faktor_penyebab_keterangan }}</div>@endif</div>
                    
                    <div class="col-span-1 md:col-span-2 border-t border-gray-200 pt-6 mt-2"><h4 class="font-bold text-gray-900 text-base flex items-center gap-2"><span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-sm">B</span> Kekuatan Mesin PKS</h4></div>
                    
                    <div><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Anggota/Kader PKS Aktif</span> <div class="font-medium text-gray-900">{{ $detailRecord->anggota_pks ?? '0' }} <span class="text-gray-500 font-normal">Orang</span></div></div>
                    <div><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Jumlah KTA</span> <div class="font-medium text-gray-900">{{ $detailRecord->jumlah_kta ?? '0' }} <span class="text-gray-500 font-normal">KTA</span></div></div>
                    
                    <div class="col-span-1 md:col-span-2"><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Struktur Pengajian RW</span> <div class="font-medium text-gray-900">{{ $detailRecord->upa_rw_status ?? '-' }}</div>@if($detailRecord->upa_rw_nama)<div class="text-gray-500 mt-1">{{ $detailRecord->upa_rw_nama }}</div>@endif</div>
                    <div class="col-span-1 md:col-span-2"><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Struktur RKI (Rumah Keluarga Indonesia)</span> <div class="font-medium text-gray-900">{{ $detailRecord->rki_status ?? '-' }}</div>@if($detailRecord->rki_nama)<div class="text-gray-500 mt-1">{{ $detailRecord->rki_nama }}</div>@endif</div>
                    
                    <div class="col-span-1 md:col-span-2"><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Titik Senam PKS</span> <div class="font-medium text-gray-900">{{ $detailRecord->senam_status ?? '-' }}</div>@if($detailRecord->senam_nama)<div class="text-gray-500 mt-1">{{ $detailRecord->senam_nama }}</div>@endif</div>
                    <div class="col-span-1 md:col-span-2"><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Relawan Muda</span> <div class="font-medium text-gray-900">{{ $detailRecord->relawan_milenial_status ?? '-' }}</div>@if($detailRecord->relawan_milenial_nama)<div class="text-gray-500 mt-1">{{ $detailRecord->relawan_milenial_nama }}</div>@endif</div>
                    
                    <div class="col-span-1 md:col-span-2"><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Aleg terpilih di RW (Bisa partai lain)</span> <div class="font-medium text-gray-900">{{ $detailRecord->caleg_terpilih_ada ?? '-' }}</div>@if($detailRecord->caleg_terpilih_nama)<div class="text-gray-500 mt-1">{{ $detailRecord->caleg_terpilih_nama }}</div>@endif</div>
                    
                    <div class="col-span-1 md:col-span-2 border-t border-gray-200 pt-6 mt-2"><h4 class="font-bold text-gray-900 text-base flex items-center gap-2"><span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-sm">C</span> Kondisi Lingkungan & Strategi</h4></div>
                    
                    <div class="col-span-1 md:col-span-2"><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Siapa yang terafiliasi dengan PKS?</span> <div class="font-medium text-gray-900">RW/RT: {{ $detailRecord->afiliasi_rw_rt ?? '-' }} <br> Posyandu/DKM: {{ $detailRecord->afiliasi_posyandu_dkm ?? '-' }}</div></div>
                    
                    <div class="col-span-1 md:col-span-2"><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Analisa Kompetitor Terkuat</span> <div class="font-medium text-gray-900">{{ $detailRecord->kompetitor_status ?? '-' }}</div>@if($detailRecord->kompetitor_detail)<div class="text-gray-500 mt-1 italic">{{ $detailRecord->kompetitor_detail }}</div>@endif</div>
                    <div class="col-span-1 md:col-span-2"><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Pergerakan Tim Sukses/Relawan Pesaing</span> <div class="font-medium text-gray-900">{{ $detailRecord->tim_sukses_status ?? '-' }}</div>@if($detailRecord->tim_sukses_detail)<div class="text-gray-500 mt-1 italic">{{ $detailRecord->tim_sukses_detail }}</div>@endif</div>
                    
                    <div class="col-span-1 md:col-span-2"><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Strategi Prioritas Pemenangan di RW ini</span> <div class="font-medium text-gray-900">{{ $detailRecord->strategi ?? '-' }}</div>@if($detailRecord->strategi_keterangan)<div class="text-gray-500 mt-1">{{ $detailRecord->strategi_keterangan }}</div>@endif</div>
                    
                    <div class="col-span-1 md:col-span-2"><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Penanggung Jawab Wilayah (PJ RW)</span> <div class="font-medium text-gray-900">{{ $detailRecord->penanggung_jawab ?? '-' }}</div></div>
                    <div class="col-span-1 md:col-span-2"><span class="text-gray-500 block text-xs mb-1 uppercase tracking-wider font-semibold">Keterangan Lain / Kendala Khusus</span> <div class="font-medium text-gray-900">{{ $detailRecord->keterangan_lain ?? '-' }}</div></div>
                </div>
            </div>
            
            <div style="padding:16px 20px;border-top:1px solid #e5e7eb;display:flex;justify-content:flex-end;gap:10px;background:#fafafa;">
                <button type="button" wire:click="closeDetail" class="border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md">
                    Tutup
                </button>
            </div>
        </div>
    @endif
</div>
