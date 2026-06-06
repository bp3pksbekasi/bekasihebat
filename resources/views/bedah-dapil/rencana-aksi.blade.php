<x-layouts.app.sidebar>
    <flux:main class="!p-0">
        <div style="min-height:100vh;background:#fafafa;">
            <div style="width:100%;margin:0;">
                <div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:space-between;border-radius:0;gap:16px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:28px;height:28px;background:#fe5000;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:14px;">&#127919;</div>
                            <div style="font-weight:500;font-size:14px;">Bedah Dapil</div>
                        </div>
                        <nav style="display:flex;gap:18px;font-size:12px;color:#aaa;flex-wrap:wrap;">
                            <a href="{{ route('bedah-dapil.pemilu-dprd') }}" style="color:#aaa;text-decoration:none;">Pemilu DPRD</a>
                            <a href="{{ route('bedah-dapil.analisa-caleg') }}" style="color:#aaa;text-decoration:none;">Analisa Caleg</a>
                        </nav>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;font-size:11px;color:#aaa;">
                        <span>Login: <span style="color:white;">{{ auth()->user()->name }}</span></span>
                        <div style="width:26px;height:26px;background:#fe5000;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:500;">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                    </div>
                </div>

                <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0;overflow:hidden;">
                    <div style="background:white;padding:12px 20px;border-bottom:0.5px solid #e5e5e5;display:flex;align-items:center;gap:12px;flex-wrap:wrap;position:relative;">
                        <div style="font-size:11px;color:#666;text-transform:uppercase;letter-spacing:0.8px;">Scope:</div>
                        <select id="dapilSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:12px;background:#fff7f1;color:#993c1d;font-weight:500;"></select>
                        <select id="kecamatanSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:12px;">
                            <option value="">Semua kecamatan</option>
                        </select>
                        <select id="statusSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:12px;">
                            <option value="">Semua status</option>
                            <option value="JAGA KUAT">Jaga Kuat</option>
                            <option value="AMANKAN">Amankan</option>
                            <option value="REBUT REALISTIS">Rebut Realistis</option>
                            <option value="GARAP INTENSIF">Garap Intensif</option>
                            <option value="ZONA BERAT">Zona Berat</option>
                        </select>
                        <input id="searchInput" type="text" placeholder="Cari RW atau desa..." style="padding:5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:12px;width:190px;">
                        <div style="flex:1;"></div>
                        <button id="exportAllBtn" type="button" style="padding:6px 12px;border:none;border-radius:6px;font-size:12px;background:#fe5000;color:white;cursor:pointer;">Export semua</button>
                    </div>

                    <div style="padding:20px 20px 0;">
                        <h1 id="pageHeading" style="font-size:20px;font-weight:500;margin:0;color:#1a1a1a;">Rencana Aksi</h1>
                        <div id="pageSubheading" style="font-size:12px;color:#666;margin-top:2px;">Program kerja lapangan per RW berdasarkan status wilayah Pemilu 2024 · Strategi pemenangan 2029</div>
                    </div>

                    <div id="infoBannerWrap" style="padding:14px 20px 0;"></div>

                    <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:18px 0;padding:0 20px;" class="summary-grid">
                        <div id="cardTotalRw" style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;"></div>
                        <div id="cardTotalProgram" style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;"></div>
                        <div id="cardProgress" style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:10px;padding:14px;color:white;"></div>
                        <div id="cardKendala" style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;"></div>
                    </div>

                    <div style="padding:0 20px 14px;">
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                            <div style="margin-bottom:12px;">
                                <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Progress Per Status</div>
                                <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Capaian program kerja per kategori wilayah</div>
                            </div>
                            <div id="progressBarsWrap"></div>
                        </div>
                    </div>

                    <div style="padding:0 20px 20px;">
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
                                <div>
                                    <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Daftar Wilayah</div>
                                    <div id="rwTableTitle" style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">0 RW · Klik baris untuk lihat & kelola program</div>
                                </div>
                                <button id="printAllBtn" type="button" style="padding:6px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:11px;background:white;cursor:pointer;">Print semua</button>
                            </div>
                            <div id="rwTableWrap"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .aksi-stat-label { font-size: 11px; color: #666; text-transform: uppercase; letter-spacing: 0.6px; }
            .aksi-stat-value { font-size: 22px; color: #1a1a1a; font-weight: 500; line-height: 1.1; margin-top: 10px; }
            .aksi-stat-value.light { color: white; }
            .aksi-stat-sub { font-size: 10px; color: #888; margin-top: 6px; line-height: 1.4; }
            .aksi-stat-sub.light { color: rgba(255,255,255,0.88); }
            @media (max-width: 1100px) {
                .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
            }
            @media (max-width: 700px) {
                .summary-grid { grid-template-columns: minmax(0, 1fr) !important; }
            }
        </style>

        <div id="aksiDrawerBackdrop" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:49;"></div>
        <div id="aksiDrawer" class="hidden" style="position:fixed;top:0;right:0;width:420px;max-width:100vw;height:100vh;background:white;box-shadow:-4px 0 20px rgba(0,0,0,0.1);z-index:50;overflow-y:auto;transform:translateX(100%);transition:transform 0.2s;">
            <div style="padding:16px 20px;border-bottom:0.5px solid #e5e5e5;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                <div>
                    <div id="drawerTitle" style="font-size:15px;font-weight:500;color:#1a1a1a;">RW</div>
                    <div id="drawerSubtitle" style="font-size:11px;color:#888;margin-top:2px;">-</div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div id="drawerStatusBadge" style="display:flex;align-items:center;gap:4px;padding:3px 8px;border-radius:999px;font-size:10px;font-weight:500;">
                        <i style="width:6px;height:6px;border-radius:50%;display:inline-block;"></i>
                        <span>Status</span>
                    </div>
                    <button id="drawerCloseBtn" type="button" style="width:28px;height:28px;border-radius:6px;border:0.5px solid #e5e5e5;background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;">&#10005;</button>
                </div>
            </div>
            <div id="drawerContent" style="padding:16px 20px;"></div>
        </div>

        <script src="{{ asset('js/bedah-dapil-rencana-aksi.js') }}?v={{ filemtime(public_path('js/bedah-dapil-rencana-aksi.js')) }}" defer></script>
    </flux:main>
</x-layouts.app.sidebar>
