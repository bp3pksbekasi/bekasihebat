<div data-flux-main style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;">
    <div style="background:#1a1a1a;color:white;padding:12px 20px;border-radius:14px 14px 0 0;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;flex:1 1 auto;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:30px;height:30px;border-radius:8px;background:#16a34a;display:flex;align-items:center;justify-content:center;">
                    <i class="ti ti-brand-whatsapp" style="font-size:16px;color:white;" aria-hidden="true"></i>
                </div>
                <div style="font-size:14px;font-weight:600;">Setting WhatsApp (Whapify)</div>
            </div>
        </div>
    </div>

    <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;overflow:hidden;padding:24px;">
        @if (session()->has('message'))
            <div style="margin-bottom:20px;padding:12px 14px;border-radius:10px;background:#f0fdf4;border:0.5px solid #bbf7d0;color:#166534;font-size:13px;display:flex;align-items:center;gap:8px;">
                <i class="ti ti-circle-check" style="font-size:16px;"></i>
                <span>{{ session('message') }}</span>
            </div>
        @endif

        <div style="margin-bottom:24px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <h1 style="font-size:20px;font-weight:500;color:#1a1a1a;margin:0;">Pengaturan Integrasi WhatsApp</h1>
            <div style="font-size:12px;color:#666;">Kelola kredensial API Whapify untuk pengiriman notifikasi dan pesan WhatsApp dari sistem.</div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;" class="settings-grid">
            <!-- Left Side: Configuration Form -->
            <div style="background:#f9fafb;border:0.5px solid #e5e7eb;border-radius:14px;padding:20px;display:flex;flex-direction:column;gap:18px;">
                <div>
                    <h3 style="font-size:15px;font-weight:700;color:#111827;margin:0 0 4px;">Kredensial API</h3>
                    <p style="font-size:11px;color:#6b7280;margin:0;">Dapatkan API Secret dan Account ID di dashboard whapify.id Anda.</p>
                </div>

                <form wire:submit="save" style="display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">API Secret Token</label>
                        <input type="password" wire:model.defer="secret" placeholder="Masukkan Whapify API Secret" style="width:100%;padding:10px 12px;border:0.5px solid #d1d5db;border-radius:10px;font-size:13px;outline:none;">
                        @error('secret') <div style="font-size:11px;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Account Unique ID</label>
                        <input type="text" wire:model.defer="account" placeholder="Masukkan Whapify Account ID" style="width:100%;padding:10px 12px;border:0.5px solid #d1d5db;border-radius:10px;font-size:13px;outline:none;">
                        @error('account') <div style="font-size:11px;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                    </div>

                    <div style="margin-top:8px;">
                        <button type="submit" style="padding:10px 18px;border:none;border-radius:10px;background:#16a34a;color:white;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                            <i class="ti ti-device-floppy" style="font-size:15px;"></i>
                            <span>Simpan Konfigurasi</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Side: Test Utility -->
            <div style="background:#f9fafb;border:0.5px solid #e5e7eb;border-radius:14px;padding:20px;display:flex;flex-direction:column;gap:18px;">
                <div>
                    <h3 style="font-size:15px;font-weight:700;color:#111827;margin:0 0 4px;">Uji Coba Pengiriman</h3>
                    <p style="font-size:11px;color:#6b7280;margin:0;">Kirim pesan uji coba untuk memverifikasi apakah akun WhatsApp sudah terhubung dengan benar.</p>
                </div>

                <form wire:submit.prevent="sendTest" style="display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Nomor HP Penerima (Contoh: 08123456789)</label>
                        <input type="text" wire:model.defer="testRecipient" placeholder="Masukkan nomor HP tujuan" style="width:100%;padding:10px 12px;border:0.5px solid #d1d5db;border-radius:10px;font-size:13px;outline:none;">
                        @error('testRecipient') <div style="font-size:11px;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;">Isi Pesan Uji Coba</label>
                        <textarea wire:model.defer="testMessage" rows="3" placeholder="Tulis isi pesan..." style="width:100%;padding:10px 12px;border:0.5px solid #d1d5db;border-radius:10px;font-size:13px;outline:none;resize:vertical;"></textarea>
                        @error('testMessage') <div style="font-size:11px;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <button type="submit" style="padding:10px 18px;border:none;border-radius:10px;background:#3b82f6;color:white;font-size:13px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                            <i class="ti ti-send" style="font-size:15px;"></i>
                            <span>Kirim Uji Coba</span>
                        </button>
                    </div>
                </form>

                @if ($statusMessage !== '')
                    <div style="padding:12px;border-radius:10px;font-size:12px;word-break:break-all;line-height:1.5;display:flex;align-items:flex-start;gap:8px;
                        background:{{ $statusType === 'success' ? '#f0fdf4' : '#fef2f2' }};
                        border:0.5px solid {{ $statusType === 'success' ? '#bbf7d0' : '#fca5a5' }};
                        color:{{ $statusType === 'success' ? '#166534' : '#991b1b' }};">
                        <i class="ti ti-{{ $statusType === 'success' ? 'circle-check' : 'alert-circle' }}" style="font-size:16px;margin-top:2px;"></i>
                        <div>
                            <strong style="display:block;margin-bottom:2px;">{{ $statusType === 'success' ? 'Pengiriman Berhasil' : 'Pengiriman Gagal' }}</strong>
                            {{ $statusMessage }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 960px) {
            .settings-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>
</div>
