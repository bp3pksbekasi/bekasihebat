<div style="min-height:100vh;background:#fafafa;">
    <div style="width:100%;margin:0;">
        <div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <a href="{{ route('events.index') }}" wire:navigate style="display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border-radius:8px;background:rgba(255,255,255,0.08);color:#f5f5f5;font-size:12px;text-decoration:none;">
                    <i class="ti ti-arrow-left" aria-hidden="true"></i>
                    <span>Kembali</span>
                </a>
                <div>
                    <div style="font-size:15px;font-weight:500;">{{ $pageTitle }}</div>
                    <div style="font-size:11px;color:#a3a3a3;margin-top:2px;">{{ $pageSubtitle }}</div>
                </div>
            </div>
            <div style="font-size:11px;color:#a3a3a3;">{{ now()->format('d M Y H:i') }}</div>
        </div>

        <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;padding:20px;">
            @if (session('message'))
                <div style="margin-bottom:14px;padding:10px 12px;border-radius:8px;background:#ecfdf3;border:0.5px solid #bbf7d0;color:#166534;font-size:12px;">
                    {{ session('message') }}
                </div>
            @endif

            @if ($errors->any())
                <div style="margin-bottom:14px;padding:10px 12px;border-radius:8px;background:#fef2f2;border:0.5px solid #fecaca;color:#dc2626;font-size:12px;">
                    {{ $errors->first() }}
                </div>
            @endif

            @if ($sourceKegiatan !== [])
                <div style="margin-bottom:16px;padding:11px 12px;border-radius:10px;background:#fff7ed;border:0.5px solid #fed7aa;color:#9a3412;font-size:12px;">
                    Dibuat dari kegiatan Sisir RW — {{ $sourceKegiatan['desa'] }} RW {{ $sourceKegiatan['rw'] }} · {{ $sourceKegiatan['tanggal'] }}
                </div>
            @endif

            <div style="display:grid;grid-template-columns:minmax(0,1fr) minmax(320px,0.95fr);gap:14px;" class="event-form-grid">
                <div style="display:grid;gap:12px;">
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                        <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Informasi Utama</div>
                        <div style="display:grid;gap:12px;margin-top:12px;">
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Judul event</label>
                                <input wire:model="judul" type="text" placeholder="Contoh: Pengajian RW 08" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                            </div>
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Deskripsi</label>
                                <textarea wire:model="deskripsi" rows="6" placeholder="Ringkasan agenda, tujuan, dan target peserta..." style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:13px;resize:vertical;"></textarea>
                            </div>
                            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;" class="event-two-col">
                                <div>
                                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Jenis event</label>
                                    <select wire:model="jenis" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                        @foreach (\App\Models\Event::JENIS_EVENT as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Penyelenggara</label>
                                    <input wire:model="penyelenggara" type="text" placeholder="DPRa / Tim Lapangan / Komunitas" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                </div>
                            </div>
                            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;" class="event-two-col">
                                <div>
                                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">PIC nama</label>
                                    <input wire:model="picNama" type="text" placeholder="Nama PIC" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                </div>
                                <div>
                                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">PIC HP</label>
                                    <input wire:model="picHp" type="text" placeholder="08xxxxxxxxxx" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display:grid;gap:12px;">
                    <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                        <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Waktu & Lokasi</div>
                        <div style="display:grid;gap:12px;margin-top:12px;">
                            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;" class="event-two-col">
                                <div>
                                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Tanggal mulai</label>
                                    <input wire:model="tanggalMulai" type="datetime-local" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                </div>
                                <div>
                                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Tanggal selesai</label>
                                    <input wire:model="tanggalSelesai" type="datetime-local" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                </div>
                            </div>
                            <div>
                                <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Alamat lengkap</label>
                                <input wire:model="lokasi" type="text" placeholder="Alamat / titik lokasi kegiatan" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                            </div>
                            <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;" class="event-three-col">
                                <div>
                                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Dapil</label>
                                    <select wire:model.live="lokasiDapil" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                        <option value="">- Pilih dapil -</option>
                                        @foreach ($this->dapilOptions as $dapil)
                                            <option value="{{ $dapil }}">{{ $dapil }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Kecamatan</label>
                                    <select wire:model.live="lokasiKecamatan" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                        <option value="">- Pilih kecamatan -</option>
                                        @foreach ($this->kecamatanOptions as $kecamatan)
                                            <option value="{{ $kecamatan }}">{{ $kecamatan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Desa</label>
                                    <select wire:model="lokasiDesa" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                        <option value="">- Pilih desa -</option>
                                        @foreach ($this->desaOptions as $desa)
                                            <option value="{{ $desa }}">{{ $desa }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;" class="event-two-col">
                                <div>
                                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Kapasitas</label>
                                    <input wire:model="kapasitas" type="number" min="0" placeholder="0 = unlimited" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                </div>
                                <div>
                                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Cover image</label>
                                    <input wire:model="coverImage" type="file" accept="image/*" style="font-size:12px;width:100%;">
                                </div>
                            </div>
                            @if (! empty($existingCover))
                                <div>
                                    <div style="font-size:11px;color:#666;margin-bottom:6px;">Cover saat ini</div>
                                    <div style="width:120px;height:80px;border-radius:10px;overflow:hidden;background:#f4f4f5;border:0.5px solid #e5e7eb;">
                                        <img src="{{ asset('storage/' . $existingCover) }}" alt="Cover event" style="width:100%;height:100%;object-fit:cover;">
                                    </div>
                                </div>
                            @endif
                            <label style="display:flex;align-items:center;gap:8px;font-size:12px;color:#666;cursor:pointer;">
                                <input wire:model="isPublic" type="checkbox">
                                <span>Tampilkan di website publik</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top:16px;display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap;">
                <button wire:click="simpanDraft" type="button" style="height:42px;padding:0 16px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:13px;font-weight:500;cursor:pointer;">
                    {{ $submitDraftLabel }}
                </button>
                <button wire:click="saveAndSubmit" type="button" style="height:42px;padding:0 16px;border-radius:10px;border:none;background:#fe5000;color:white;font-size:13px;font-weight:600;cursor:pointer;">
                    {{ $submitApprovalLabel }}
                </button>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 900px) {
            .event-form-grid,
            .event-three-col,
            .event-two-col {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>
</div>
