<div style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;box-sizing:border-box;">
    <div style="width:100%;max-width:800px;margin:0 auto;box-sizing:border-box;">
        
        {{-- DARK HEADER --}}
        <div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;border-radius:14px 14px 0 0;">
            <div style="display:flex;align-items:center;gap:12px;">
                <a href="{{ route('buku-induk-rw.detail', ['profilRw' => $program->nomor_rw, 'desa' => $program->targetWilayah->desa]) }}" wire:navigate style="display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border-radius:8px;background:rgba(255,255,255,.08);color:#f5f5f5;font-size:12px;text-decoration:none;">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
                <div>
                    <div style="font-size:15px;font-weight:500;">Laporan Pelaksanaan Program Arahan</div>
                    <div style="font-size:11px;color:#a3a3a3;margin-top:2px;">{{ $program->judul }}</div>
                </div>
            </div>
            <div style="font-size:11px;color:#a3a3a3;">{{ now()->format('d M Y H:i') }}</div>
        </div>

        {{-- WHITE BODY --}}
        <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;padding:20px;">

            @if(session('message'))
                <div style="margin-bottom:16px;padding:10px 12px;border-radius:8px;background:#ecfdf3;border:0.5px solid #bbf7d0;color:#166534;font-size:12px;">{{ session('message') }}</div>
            @endif
            @if($errors->any())
                <div style="margin-bottom:16px;padding:10px 12px;border-radius:8px;background:#fef2f2;border:0.5px solid #fecaca;color:#dc2626;font-size:12px;">{{ $errors->first() }}</div>
            @endif

            <form wire:submit.prevent="save">
                <div style="display:grid;gap:16px;">
                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Ringkasan Laporan *</label>
                        <textarea wire:model="ringkasan" rows="3" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:13px;resize:vertical;" required></textarea>
                    </div>

                    <div style="font-size:12px;font-weight:600;margin-top:8px;color:#fe5000;">Realisasi Infrastruktur (Manual Input)</div>
                    <div style="font-size:11px;color:#888;margin-bottom:8px;">Angka ini hanya sebagai laporan awal. Realisasi program yang sebenarnya (yang dihitung di Peta Kekuatan) didapatkan saat Anda membuat entri Korwe/Korte dengan menandai bahwa mereka adalah hasil dari program ini.</div>

                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(150px, 1fr));gap:12px;">
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Korwe Terbentuk</label>
                            <input wire:model="jumlahKorwe" type="number" min="0" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        </div>
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Korte Terbentuk</label>
                            <input wire:model="jumlahKorte" type="number" min="0" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        </div>
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Penggalang Terekrut</label>
                            <input wire:model="jumlahPenggalang" type="number" min="0" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:8px;">
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Realisasi Anggaran (Rp)</label>
                            <input wire:model="realisasiAnggaran" type="number" min="0" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                        </div>
                        <div>
                            <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Rating Keberhasilan</label>
                            <select wire:model="rating" style="width:100%;height:40px;border-radius:8px;border:0.5px solid #d4d4d8;padding:0 12px;font-size:13px;">
                                <option value="">- Pilih Rating -</option>
                                <option value="sangat_baik">Sangat Baik (Sesuai Target)</option>
                                <option value="baik">Baik</option>
                                <option value="cukup">Cukup (Target Tercapai Sebagian)</option>
                                <option value="kurang">Kurang (Tidak Sesuai Target)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Evaluasi / Kendala</label>
                        <textarea wire:model="evaluasi" rows="3" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:13px;resize:vertical;"></textarea>
                    </div>

                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Tindak Lanjut</label>
                        <textarea wire:model="tindakLanjut" rows="3" style="width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:13px;resize:vertical;"></textarea>
                    </div>

                    <div>
                        <label style="font-size:11px;color:#666;display:block;margin-bottom:5px;">Upload Foto Dokumentasi</label>
                        <input wire:model="foto" type="file" multiple accept="image/*" style="width:100%;font-size:13px;">
                        <div style="font-size:10px;color:#888;margin-top:4px;">Bisa pilih lebih dari satu foto.</div>
                    </div>

                </div>

                <div style="display:flex;justify-content:flex-end;margin-top:24px;padding-top:16px;border-top:0.5px solid #e5e5e5;">
                    <button type="submit" style="padding:10px 24px;border-radius:8px;background:#16a34a;color:white;border:none;font-size:13px;font-weight:600;cursor:pointer;">
                        <span wire:loading.remove wire:target="save">Kirim Laporan & Selesaikan Program</span>
                        <span wire:loading wire:target="save">Memproses...</span>
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>
