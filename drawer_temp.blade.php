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

    <style>
        @media (max-width: 1200px) {
            .detail-summary-grid,
            .detail-top-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 760px) {
            .detail-summary-grid,
            .detail-top-grid,
            .detail-form-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>
</div>
