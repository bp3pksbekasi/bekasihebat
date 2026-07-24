<div>
    @if ($showForm)
        {{-- Backdrop --}}
        <div
            wire:click="closeForm"
            style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:40;backdrop-filter:blur(2px);"
        ></div>

        {{-- Drawer Panel --}}
        <div style="
            position:fixed;
            top:0;right:0;
            width:100%;
            max-width:520px;
            height:100%;
            background:#fff;
            box-shadow:-8px 0 32px rgba(0,0,0,0.18);
            z-index:50;
            overflow-y:auto;
            display:flex;
            flex-direction:column;
        ">
            {{-- Header --}}
            <div style="
                position:sticky;top:0;
                background:#fff;
                border-bottom:1px solid #e5e7eb;
                padding:18px 20px;
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:12px;
                z-index:51;
            ">
                <div>
                    <div style="font-size:17px;font-weight:700;color:#111827;letter-spacing:-0.2px;">
                        {{ $editId ? 'Edit' : 'Catat' }} Kegiatan Sisir RW
                    </div>
                    <div style="font-size:12px;color:#6b7280;margin-top:3px;">Rekam aktivitas kunjungan ke RW</div>
                </div>
                <button
                    wire:click="closeForm"
                    type="button"
                    style="
                        width:36px;height:36px;
                        border-radius:8px;
                        border:1px solid #e5e7eb;
                        background:#f9fafb;
                        cursor:pointer;
                        display:flex;align-items:center;justify-content:center;
                        font-size:16px;color:#6b7280;
                        flex-shrink:0;
                    "
                >✕</button>
            </div>

            {{-- Form Body --}}
            <div style="padding:20px;display:grid;gap:20px;flex:1;">

                @if ($errors->any())
                    <div style="padding:12px 16px;border-radius:10px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;font-size:14px;font-weight:500;">
                        ⚠ {{ $errors->first() }}
                    </div>
                @endif

                {{-- Tanggal & Waktu --}}
                <div>
                    <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">
                        Tanggal &amp; Waktu
                    </label>
                    <input
                        wire:model="formTanggal"
                        type="date"
                        style="
                            width:100%;height:48px;
                            border-radius:10px;
                            border:1.5px solid #d1d5db;
                            background:#fff;
                            padding:0 14px;
                            font-size:15px;
                            color:#111827;
                            box-sizing:border-box;
                        "
                    >
                </div>

                {{-- Desa --}}
                <div>
                    <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Desa / Kelurahan</label>
                    <select
                        wire:model.live="formDesaId"
                        style="
                            width:100%;height:48px;
                            border-radius:10px;
                            border:1.5px solid #d1d5db;
                            background:#fff;
                            padding:0 14px;
                            font-size:15px;
                            color:#111827;
                            box-sizing:border-box;
                        "
                    >
                        <option value="">— Pilih Desa —</option>
                        @foreach ($this->desaOptions as $desa)
                            <option value="{{ $desa['id'] }}">{{ $desa['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- RW --}}
                <div>
                    <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">RW</label>
                    <select
                        wire:model="formRw"
                        style="
                            width:100%;height:48px;
                            border-radius:10px;
                            border:1.5px solid #d1d5db;
                            background:#fff;
                            padding:0 14px;
                            font-size:15px;
                            color:#111827;
                            box-sizing:border-box;
                        "
                    >
                        <option value="">— RW —</option>
                        @foreach ($this->rwOptions as $rw)
                            <option value="{{ $rw }}">RW {{ $rw }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Jenis Kegiatan --}}
                <div>
                    <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Jenis Kegiatan</label>
                    <select
                        wire:model="formJenis"
                        style="
                            width:100%;height:48px;
                            border-radius:10px;
                            border:1.5px solid #d1d5db;
                            background:#fff;
                            padding:0 14px;
                            font-size:15px;
                            color:#111827;
                            box-sizing:border-box;
                        "
                    >
                        @foreach (\App\Models\KegiatanRw::JENIS_KEGIATAN as $key => $cfg)
                            <option value="{{ $key }}">{{ $cfg['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Pelaksana --}}
                <div>
                    <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Pelaksana / PIC</label>
                    <input
                        wire:model="formPelaksana"
                        type="text"
                        placeholder="Nama pelaksana"
                        style="
                            width:100%;height:48px;
                            border-radius:10px;
                            border:1.5px solid #d1d5db;
                            background:#fff;
                            padding:0 14px;
                            font-size:15px;
                            color:#111827;
                            box-sizing:border-box;
                        "
                    >
                </div>

                {{-- Jumlah Warga --}}
                <div>
                    <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Jumlah Warga</label>
                    <input
                        wire:model="formJumlahWarga"
                        type="number"
                        placeholder="0"
                        inputmode="numeric"
                        style="
                            width:100%;height:48px;
                            border-radius:10px;
                            border:1.5px solid #d1d5db;
                            background:#fff;
                            padding:0 14px;
                            font-size:15px;
                            color:#111827;
                            box-sizing:border-box;
                        "
                    >
                </div>

                {{-- Catatan --}}
                <div>
                    <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Catatan / Hasil Kegiatan</label>
                    <textarea
                        wire:model="formCatatan"
                        rows="4"
                        placeholder="Apa yang terjadi, siapa yang ditemui, hasil diskusi..."
                        style="
                            width:100%;
                            border-radius:10px;
                            border:1.5px solid #d1d5db;
                            background:#fff;
                            padding:12px 14px;
                            font-size:15px;
                            color:#111827;
                            resize:vertical;
                            line-height:1.5;
                            box-sizing:border-box;
                        "
                    ></textarea>
                </div>

                {{-- Tokoh --}}
                <div>
                    <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Tokoh yang Ditemui</label>
                    <input
                        wire:model="formTokoh"
                        type="text"
                        placeholder="Nama tokoh + catatan singkat"
                        style="
                            width:100%;height:48px;
                            border-radius:10px;
                            border:1.5px solid #d1d5db;
                            background:#fff;
                            padding:0 14px;
                            font-size:15px;
                            color:#111827;
                            box-sizing:border-box;
                        "
                    >
                </div>

                {{-- Tindak Lanjut --}}
                <div>
                    <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Tindak Lanjut</label>
                    <textarea
                        wire:model="formTindakLanjut"
                        rows="3"
                        placeholder="Apa yang harus dilakukan selanjutnya"
                        style="
                            width:100%;
                            border-radius:10px;
                            border:1.5px solid #d1d5db;
                            background:#fff;
                            padding:12px 14px;
                            font-size:15px;
                            color:#111827;
                            resize:vertical;
                            line-height:1.5;
                            box-sizing:border-box;
                        "
                    ></textarea>
                </div>



                {{-- Foto --}}
                <div>
                    <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Foto Kegiatan <span style="font-weight:400;color:#9ca3af;">(maks. 5 foto)</span></label>
                    <label style="
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        gap:10px;
                        width:100%;
                        height:52px;
                        border-radius:10px;
                        border:1.5px dashed #d1d5db;
                        background:#f9fafb;
                        cursor:pointer;
                        font-size:14px;
                        color:#6b7280;
                        box-sizing:border-box;
                    ">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Pilih / Ambil Foto
                        <input wire:model="formFoto" type="file" multiple accept="image/*" capture="environment" style="display:none;">
                    </label>

                    {{-- Preview foto baru --}}
                    @if ($formFoto !== [])
                        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:12px;">
                            @foreach ($formFoto as $foto)
                                <div style="width:72px;height:72px;border-radius:10px;overflow:hidden;background:#f4f4f5;border:1px solid #e5e7eb;">
                                    <img src="{{ $foto->temporaryUrl() }}" alt="Preview" style="width:100%;height:100%;object-fit:cover;">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Foto existing --}}
                    @if ($existingFoto !== [])
                        <div style="margin-top:12px;">
                            <div style="font-size:12px;color:#9ca3af;margin-bottom:8px;">Foto sebelumnya:</div>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                @foreach ($existingFoto as $foto)
                                    <div style="width:72px;height:72px;border-radius:10px;overflow:hidden;background:#f4f4f5;border:1px solid #e5e7eb;">
                                        <img src="{{ asset('storage/' . $foto) }}" alt="Foto kegiatan" style="width:100%;height:100%;object-fit:cover;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Checkboxes --}}
                    <div style="display:grid;gap:12px;margin-top:16px;">
                        <label style="display:flex;align-items:center;gap:12px;cursor:pointer;padding:12px 14px;border-radius:10px;background:#f9fafb;border:1px solid #e5e7eb;">
                            <input wire:model="formTampilGaleri" type="checkbox" style="width:20px;height:20px;accent-color:#ea580c;cursor:pointer;flex-shrink:0;">
                            <span style="font-size:14px;color:#374151;line-height:1.4;">Tampilkan foto di <strong>galeri website publik</strong></span>
                        </label>
                    </div>
                </div>

            </div>

            {{-- Footer Buttons --}}
            <div style="
                position:sticky;bottom:0;
                background:#fff;
                border-top:1px solid #e5e7eb;
                padding:16px 20px;
                display:flex;
                gap:10px;
                z-index:51;
            ">
                <button
                    wire:click="simpanKegiatan"
                    type="button"
                    style="
                        flex:1;
                        height:50px;
                        border:none;
                        border-radius:12px;
                        background:#ea580c;
                        color:#fff;
                        font-size:15px;
                        font-weight:700;
                        cursor:pointer;
                        letter-spacing:0.2px;
                    "
                >
                    {{ $editId ? '💾 Update' : '✓ Simpan' }} Kegiatan
                </button>
                <button
                    wire:click="closeForm"
                    type="button"
                    style="
                        height:50px;
                        padding:0 20px;
                        border-radius:12px;
                        border:1.5px solid #e5e7eb;
                        background:#fff;
                        color:#6b7280;
                        font-size:15px;
                        font-weight:600;
                        cursor:pointer;
                    "
                >
                    Batal
                </button>
            </div>
        </div>
    @endif
</div>
