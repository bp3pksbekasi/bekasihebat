<div>
    @if ($showForm)
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:40;" wire:click="closeForm"></div>
        <div style="position:fixed;top:0;right:0;width:440px;max-width:100%;height:100%;background:white;box-shadow:-8px 0 24px rgba(0,0,0,0.16);z-index:50;overflow-y:auto;">
            <div style="position:sticky;top:0;background:white;border-bottom:0.5px solid #e5e5e5;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;z-index:51;">
                <div>
                    <div style="font-size:15px;font-weight:500;color:#1a1a1a;">{{ $editId ? 'Edit' : 'Catat' }} kegiatan</div>
                    <div style="font-size:11px;color:#888;margin-top:2px;">Rekam aktivitas sisir RW</div>
                </div>
                <button wire:click="closeForm" type="button" style="width:28px;height:28px;border-radius:6px;border:0.5px solid #e5e5e5;background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
            </div>

            <div style="padding:16px 20px;display:grid;gap:12px;">
                @if ($errors->any())
                    <div style="padding:10px 12px;border-radius:8px;background:#fef2f2;border:0.5px solid #fecaca;color:#dc2626;font-size:12px;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div>
                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Tanggal & waktu</label>
                    <input wire:model="formTanggal" type="datetime-local" style="width:100%;height:38px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;padding:0 12px;font-size:13px;color:#1f2937;">
                </div>

                <div style="display:grid;grid-template-columns:minmax(0,1fr) 110px;gap:10px;">
                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Desa</label>
                        <select wire:model.live="formDesaId" style="width:100%;height:38px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;padding:0 12px;font-size:13px;color:#1f2937;">
                            <option value="">- Pilih desa -</option>
                            @foreach ($this->desaOptions as $desa)
                                <option value="{{ $desa['id'] }}">{{ $desa['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">RW</label>
                        <select wire:model="formRw" style="width:100%;height:38px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;padding:0 12px;font-size:13px;color:#1f2937;">
                            <option value="">- RW -</option>
                            @foreach ($this->rwOptions as $rw)
                                <option value="{{ $rw }}">RW {{ $rw }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Jenis kegiatan</label>
                    <select wire:model="formJenis" style="width:100%;height:38px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;padding:0 12px;font-size:13px;color:#1f2937;">
                        @foreach (\App\Models\KegiatanRw::JENIS_KEGIATAN as $key => $cfg)
                            <option value="{{ $key }}">{{ $cfg['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Pelaksana / PIC</label>
                        <input wire:model="formPelaksana" placeholder="Nama pelaksana" style="width:100%;height:38px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;padding:0 12px;font-size:13px;color:#1f2937;">
                    </div>
                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Jumlah warga</label>
                        <input wire:model="formJumlahWarga" type="number" placeholder="0" style="width:100%;height:38px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;padding:0 12px;font-size:13px;color:#1f2937;">
                    </div>
                </div>

                <div>
                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Catatan / hasil kegiatan</label>
                    <textarea wire:model="formCatatan" rows="3" placeholder="Apa yang terjadi, siapa yang ditemui, hasil diskusi..." style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;background:white;padding:10px 12px;font-size:13px;color:#1f2937;resize:vertical;"></textarea>
                </div>

                <div>
                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Tokoh yang ditemui</label>
                    <input wire:model="formTokoh" placeholder="Nama tokoh + catatan singkat" style="width:100%;height:38px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;padding:0 12px;font-size:13px;color:#1f2937;">
                </div>

                <div>
                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Tindak lanjut</label>
                    <textarea wire:model="formTindakLanjut" rows="2" placeholder="Apa yang harus dilakukan selanjutnya" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;background:white;padding:10px 12px;font-size:13px;color:#1f2937;resize:vertical;"></textarea>
                </div>

                <div>
                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Jadwal kunjungan berikutnya</label>
                    <input wire:model="formJadwalBerikutnya" type="date" style="width:100%;height:38px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;padding:0 12px;font-size:13px;color:#1f2937;">
                </div>

                <div>
                    <label style="font-size:11px;color:#666;display:block;margin-bottom:6px;">Foto kegiatan (maks 5)</label>
                    <input wire:model="formFoto" type="file" multiple accept="image/*" style="font-size:12px;width:100%;">
                    <div style="margin-top:10px;display:grid;gap:8px;">
                        <label style="display:flex;align-items:center;gap:8px;font-size:12px;color:#666;cursor:pointer;">
                            <input wire:model="formJadikanEvent" type="checkbox">
                            <span>Langsung jadikan event setelah simpan</span>
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;font-size:12px;color:#666;cursor:pointer;">
                            <input wire:model="formTampilGaleri" type="checkbox">
                            <span>Tampilkan foto di galeri website publik</span>
                        </label>
                    </div>
                    @if ($existingFoto !== [])
                        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;">
                            @foreach ($existingFoto as $foto)
                                <div style="width:64px;height:64px;border-radius:8px;overflow:hidden;background:#f4f4f5;">
                                    <img src="{{ asset('storage/' . $foto) }}" alt="Foto kegiatan" style="width:100%;height:100%;object-fit:cover;">
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if ($formFoto !== [])
                        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;">
                            @foreach ($formFoto as $foto)
                                <div style="width:64px;height:64px;border-radius:8px;overflow:hidden;background:#f4f4f5;">
                                    <img src="{{ $foto->temporaryUrl() }}" alt="Preview" style="width:100%;height:100%;object-fit:cover;">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div style="position:sticky;bottom:0;background:white;border-top:0.5px solid #e5e5e5;padding:16px 20px;display:flex;gap:10px;z-index:51;">
                <button wire:click="simpanKegiatan" type="button" style="flex:1;height:40px;border:none;border-radius:10px;background:#ea580c;color:white;font-size:13px;font-weight:600;cursor:pointer;">
                    {{ $editId ? 'Update' : 'Simpan' }} kegiatan
                </button>
                <button wire:click="closeForm" type="button" style="height:40px;padding:0 14px;border-radius:10px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:13px;cursor:pointer;">
                    Batal
                </button>
            </div>
        </div>
    @endif
</div>
