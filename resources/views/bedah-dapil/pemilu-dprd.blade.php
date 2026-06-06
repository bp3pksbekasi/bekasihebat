<x-layouts.app.sidebar>
    <flux:main class="!p-0">
        <div style="min-height:100vh;background:#fafafa;">
            <div style="width:100%;margin:0;">
                <div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:flex-start;border-radius:14px 14px 0 0;gap:16px;flex-wrap:nowrap;overflow:hidden;">
                    <div style="display:flex;align-items:center;gap:18px;flex-wrap:nowrap;flex:1 1 auto;min-width:0;overflow:hidden;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:28px;height:28px;background:#fe5000;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 3L20 7V12C20 17 16.5 20.74 12 22C7.5 20.74 4 17 4 12V7L12 3Z" stroke="white" stroke-width="1.5"/>
                                    <path d="M12 7V17" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M7 12H17" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div style="font-weight:500;font-size:14px;">Bedah Dapil</div>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:nowrap;">
                            <div style="font-size:12px;color:#d4d4d8;font-weight:500;">Filter :</div>
                            <select id="dapilSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#fff7f1;color:#993c1d;font-weight:500;min-width:130px;">
                                <option value="">Semua dapil</option>
                            </select>
                            <select id="kecamatanSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;min-width:150px;">
                                <option value="">Semua kecamatan</option>
                            </select>
                            <select id="desaSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;min-width:170px;">
                                <option value="">Semua desa</option>
                            </select>
                            <select id="statusSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;min-width:130px;">
                                <option value="">Semua status</option>
                                <option value="JAGA KUAT">Jaga Kuat</option>
                                <option value="AMANKAN">Amankan</option>
                                <option value="REBUT REALISTIS">Rebut Realistis</option>
                                <option value="GARAP INTENSIF" selected>Garap Intensif</option>
                                <option value="ZONA BERAT">Zona Berat</option>
                            </select>
                            <select id="periodSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f8fafc;font-weight:500;min-width:78px;width:78px;">
                                @forelse ($periodOptions as $period)
                                    <option value="{{ $period['id'] }}" @selected($selectedPeriodId === $period['id'])>{{ $period['tahun'] }}</option>
                                @empty
                                    <option value="">Belum ada periode</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div style="width:26px;height:26px;background:#fe5000;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:500;flex:0 0 auto;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                </div>

                <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;overflow:hidden;">
                    <div style="display:none;">
                        <div id="dataSourceBadge">Mode data: auto</div>
                        <input type="file" id="tpsFileInput" accept=".csv">
                        <div id="sourceStatus">Menunggu auto-load TPS...</div>
                        <input type="file" id="dptFileInput" accept=".csv">
                        <div id="dptStatus">Menunggu auto-load DPT...</div>
                    </div>

                    <div style="padding:20px 20px 0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                            <h1 id="scopeHeading" style="font-size:20px;font-weight:500;color:#1a1a1a;margin:0;">Kabupaten Bekasi</h1>
                            <div id="scopeSubheading" style="font-size:12px;color:#666;">Hasil Pemilu DPRD 2024</div>
                        </div>
                        <div id="scopeMeta" style="display:flex;align-items:center;justify-content:flex-end;gap:6px;font-size:11px;color:#888;flex-wrap:wrap;text-align:right;">Fokus: summary wilayah, ranking partai, dan peta suara 2024.</div>
                        <div id="breadcrumb" style="display:none;align-items:center;justify-content:flex-end;gap:6px;font-size:11px;color:#888;flex-wrap:wrap;text-align:right;">
                            <span id="breadcrumbHome" style="cursor:pointer;">Kabupaten Bekasi</span>
                            <span>›</span>
                            <span id="breadcrumbDapil" style="color:#fe5000;font-weight:500;cursor:pointer;">Dapil 1</span>
                            <span id="breadcrumbDividerKecamatan">›</span>
                            <span id="breadcrumbKecamatan" style="color:#ccc;cursor:pointer;">(pilih kecamatan)</span>
                            <span id="breadcrumbDividerDesa" style="display:none;">›</span>
                            <span id="breadcrumbDesa" style="display:none;color:#ccc;">(pilih desa)</span>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin:14px 0;padding:0 20px;" class="summary-grid">
                        <div id="cardDpt" style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:12px;"></div>
                        <div id="cardSuaraSah" style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:12px;"></div>
                        <div id="cardPks" style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:10px;padding:12px;color:white;"></div>
                    </div>

                    <div style="display:grid;grid-template-columns:minmax(0,0.95fr) minmax(320px,1.05fr);gap:12px;padding:0 20px;align-items:stretch;" class="top-grid">
                        <div id="inlineMapWrap"></div>

                        <div id="partyRankingCard" style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:grid;gap:12px;">
                            <div>
                                <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Ranking Partai</div>
                                <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Top 5 di Bekasi</div>
                            </div>
                            <div id="partyRankWrap" style="display:grid;gap:5px;"></div>
                            <div style="border-top:0.5px solid #e5e5e5;padding-top:12px;display:grid;gap:8px;">
                                <div style="font-size:11px;color:#666;text-transform:uppercase;letter-spacing:0.8px;">Demografi Pemilih</div>
                                <div id="demographyBar"></div>
                            </div>
                        </div>
                    </div>

                    <div style="padding:14px 20px 0;">
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                            <div style="margin-bottom:10px;">
                                <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Status Prioritas PKS</div>
                                <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Sebaran <span id="statusTotalDesa">0</span> kelurahan</div>
                            </div>
                            <div id="statusDashboard" style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:8px;" class="status-grid"></div>
                        </div>
                    </div>

                    <div id="mapAndCalegWrap" style="padding:14px 20px 0;"></div>
                    <div id="villageDetailWrap" style="padding:14px 20px 0;"></div>

                    <div style="padding:14px 20px 20px;display:grid;gap:14px;">
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                                <div>
                                    <div id="drilldownSectionLabel" style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Daftar Dapil</div>
                                    <div id="drilldownHeading" style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">7 Dapil Kabupaten Bekasi</div>
                                </div>
                                <div style="font-size:11px;color:#888;">Klik baris untuk drill-down ke level berikutnya</div>
                            </div>
                            <div id="drilldownTableWrap"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .tab-btn {
                padding: 6px 14px;
                border-radius: 999px;
                font-size: 12px;
                border: 0.5px solid #e5e5e5;
                background: white;
                color: #666;
                cursor: pointer;
            }
            .tab-btn.active {
                background: #1a1a1a;
                color: white;
                border-color: transparent;
            }
            .tab-pane { display: none; }
            .tab-pane.active { display: block; }
            @media (max-width: 1200px) {
                .summary-grid, .top-grid, .status-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
            }
            @media (max-width: 760px) {
                .summary-grid, .top-grid, .status-grid { grid-template-columns: minmax(0, 1fr) !important; }
            }
        </style>
        <div id="detailDrawer" class="hidden" style="position:fixed;top:0;right:0;width:420px;max-width:100vw;height:100vh;background:white;box-shadow:-4px 0 20px rgba(0,0,0,0.1);z-index:50;overflow-y:auto;transition:transform 0.2s;transform:translateX(100%);">
            <div style="padding:16px 20px;border-bottom:0.5px solid #e5e5e5;display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <div>
                    <div id="detailDrawerTitle" style="font-size:15px;font-weight:500;">Detail Wilayah</div>
                    <div id="detailDrawerSubtitle" style="font-size:11px;color:#888;margin-top:2px;">-</div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div id="detailDrawerBadge" style="display:flex;align-items:center;gap:4px;padding:3px 8px;border-radius:999px;font-size:10px;font-weight:500;">
                        <i style="width:6px;height:6px;border-radius:50%;display:inline-block;"></i>
                        <span>Status</span>
                    </div>
                    <button id="detailDrawerClose" type="button" style="width:28px;height:28px;border-radius:6px;border:0.5px solid #e5e5e5;background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
                </div>
            </div>
            <div id="detailDrawerContent" style="padding:16px 20px;"></div>
        </div>
        <div id="detailDrawerBackdrop" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:49;"></div>
        <script>
            const compiledPeriodOptions = {!! json_encode($periodOptions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
            const compiledPayload = {!! json_encode($compiledPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
            const selectedPeriodId = {!! json_encode($selectedPeriodId, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
            const partyColors = {'PKB':'#008000','Gerindra':'#C8102E','PDIP':'#D72027','Golkar':'#FFD700','Nasdem':'#003087','Buruh':'#E31937','Gelora':'#DC143C','PKS':'#fe5000','PKN':'#336699','Hanura':'#4169E1','PAN':'#005BAC','PBB':'#009B3A','Demokrat':'#00529C','PSI':'#EC008C','Perindo':'#CC0000','PPP':'#006600','Ummat':'#2E8B57','Garuda':'#228B22','NasDem':'#003087','PDI-P':'#D72027'};
            const statusConfig = {
                'JAGA KUAT': { color: '#15803d', bg: '#dcfce7', text: '#14532d', dot: '#15803d', label: 'Jaga Kuat', criteria: 'PKS rank 1 & share ≥30%', description: 'PKS unggul jelas. Fokus menjaga basis, merawat struktur, dan mengunci loyalitas.' },
                'AMANKAN': { color: '#65a30d', bg: '#ecfccb', text: '#3f6212', dot: '#65a30d', label: 'Amankan', criteria: 'PKS rank 1, share <30%', description: 'PKS sudah unggul tetapi margin belum tebal. Perlu pengamanan suara dan penguatan tokoh lokal.' },
                'REBUT REALISTIS': { color: '#2563eb', bg: '#dbeafe', text: '#1e3a5f', dot: '#2563eb', label: 'Rebut Realistis', criteria: 'PKS rank 2 & gap ≤5%', description: 'PKS belum unggul, tetapi jaraknya tipis dan realistis untuk direbut dengan kerja terfokus.' },
                'GARAP INTENSIF': { color: '#d97706', bg: '#fff7f1', text: '#993c1d', dot: '#d97706', label: 'Garap Intensif', criteria: 'PKS rank ≤3 atau share ≥12%', description: 'Potensi ada, namun butuh kerja lapangan yang lebih rapat, terukur, dan konsisten.' },
                'ZONA BERAT': { color: '#b91c1c', bg: '#fee2e2', text: '#991b1b', dot: '#b91c1c', label: 'Zona Berat', criteria: 'PKS share <12% & rank >3', description: 'PKS masih lemah di wilayah ini. Prioritasnya membangun fondasi, jaringan, dan pengenalan.' }
            };
            const PKS_PARTY_ID = '12';
            const PKS_PARTY_NAME = 'PKS';
            const TOTAL_DPRD_SEATS = 55;
            const DEFAULT_FOCUS_DAPIL = 'BEKASI 1';
            const DEFAULT_FOCUS_KECAMATAN = 'SETU';
            const dptAutoLoadCandidates = {
                'BEKASI 1': ['/data/pemilu/dpt_dapil1_rt_rw.csv', '/data/pemilu/dpt_pileg2024_bekasi%201.csv'],
                'BEKASI 2': ['/data/pemilu/dpt_dapil2_rt_rw.csv', '/data/pemilu/dpt_pileg2024_bekasi%202.csv'],
                'BEKASI 3': ['/data/pemilu/dpt_dapil3_rt_rw.csv', '/data/pemilu/dpt_pileg2024_bekasi%203.csv'],
                'BEKASI 4': ['/data/pemilu/dpt_bekasi4_rt_rw.csv', '/data/pemilu/dpt_dapil4_rt_rw.csv', '/data/pemilu/dpt_pileg2024_bekasi%204.csv'],
                'BEKASI 5': ['/data/pemilu/dpt_dapil5_rt_rw.csv', '/data/pemilu/dpt_pileg2024_bekasi%205.csv'],
                'BEKASI 6': ['/data/pemilu/dpt_dapil6_rt_rw.csv', '/data/pemilu/dpt_pileg2024_bekasi%206.csv'],
                'BEKASI 7': ['/data/pemilu/dpt_dapil7_rt_rw.csv', '/data/pemilu/dpt_pileg2024_bekasi%207.csv'],
            };
            const mapConfigs = {
                'BEKASI 1': { image: '/images/peta/dapil1.png', villages: [
                    { name: 'CIJENGKOL', district: 'SETU', x: 18.2, y: 18.6 }, { name: 'LUBANGBUAYA', district: 'SETU', x: 22.1, y: 9.2 }, { name: 'CIBENING', district: 'SETU', x: 29.8, y: 28.7 }, { name: 'BURANGKENG', district: 'SETU', x: 12.6, y: 26.7 }, { name: 'TAMAN SARI', district: 'SETU', x: 15.0, y: 39.0 }, { name: 'TAMAN RAHAYU', district: 'SETU', x: 4.5, y: 39.4 }, { name: 'CIKARAGEMAN', district: 'SETU', x: 14.6, y: 48.3 }, { name: 'RAGEMANUNGGAL', district: 'SETU', x: 9.8, y: 54.9 }, { name: 'MUKTIJAYA', district: 'SETU', x: 20.4, y: 56.1 }, { name: 'CILEDUG', district: 'SETU', x: 22.4, y: 30.5 }, { name: 'KERTARAHAYU', district: 'SETU', x: 27.0, y: 43.5 }, { name: 'JAYAMULYA', district: 'SERANG BARU', x: 32.8, y: 56.1 }, { name: 'JAYASAMPURNA', district: 'SERANG BARU', x: 35.0, y: 44.7 }, { name: 'SUKARAGAM', district: 'SERANG BARU', x: 44.0, y: 53.3 }, { name: 'SUKASARI', district: 'SERANG BARU', x: 46.5, y: 46.8 }, { name: 'SIRNAJAYA', district: 'SERANG BARU', x: 40.9, y: 58.4 }, { name: 'CILANGKARA', district: 'SERANG BARU', x: 55.1, y: 44.6 }, { name: 'NAGACIPTA', district: 'SERANG BARU', x: 51.1, y: 60.2 }, { name: 'NAGASARI', district: 'SERANG BARU', x: 51.9, y: 56.0 }, { name: 'CICAU', district: 'CIKARANG PUSAT', x: 61.6, y: 35.3 }, { name: 'SUKAMAHI', district: 'CIKARANG PUSAT', x: 70.8, y: 37.5 }, { name: 'JAYAMUKTI', district: 'CIKARANG PUSAT', x: 74.0, y: 15.8 }, { name: 'HEGARMUKTI', district: 'CIKARANG PUSAT', x: 75.5, y: 23.7 }, { name: 'PASIRANJI', district: 'CIKARANG PUSAT', x: 82.2, y: 31.0 }, { name: 'PASIRTANJUNG', district: 'CIKARANG PUSAT', x: 86.9, y: 24.2 }, { name: 'CIBATU', district: 'CIKARANG SELATAN', x: 74.1, y: 44.5 }, { name: 'CIANTRA', district: 'CIKARANG SELATAN', x: 64.9, y: 44.3 }, { name: 'SUKASEJATI', district: 'CIKARANG SELATAN', x: 82.8, y: 58.8 }, { name: 'SUKADAMI', district: 'CIKARANG SELATAN', x: 65.9, y: 61.0 }, { name: 'SUKARESMI', district: 'CIKARANG SELATAN', x: 74.8, y: 67.0 }, { name: 'SERANG', district: 'CIKARANG SELATAN', x: 74.0, y: 55.8 }, { name: 'PASIRSARI', district: 'CIKARANG SELATAN', x: 82.9, y: 44.4 }, { name: 'CIBARUSAH JAYA', district: 'CIBARUSAH', x: 31.3, y: 76.0 }, { name: 'CIBARUSAH KOTA', district: 'CIBARUSAH', x: 40.2, y: 75.8 }, { name: 'SINDANGMULYA', district: 'CIBARUSAH', x: 33.2, y: 67.8 }, { name: 'WIBAWAMULYA', district: 'CIBARUSAH', x: 43.8, y: 68.4 }, { name: 'RIDOGALIH', district: 'CIBARUSAH', x: 47.9, y: 85.6 }, { name: 'RIDOMANAH', district: 'CIBARUSAH', x: 58.8, y: 77.0 }, { name: 'SIRNAJATI', district: 'CIBARUSAH', x: 37.9, y: 83.6 }, { name: 'MEDALKRISNA', district: 'BOJONGMANGU', x: 64.8, y: 69.9 }, { name: 'SUKAMUKTI', district: 'BOJONGMANGU', x: 75.0, y: 71.1 }, { name: 'SUKABUNGAH', district: 'BOJONGMANGU', x: 84.1, y: 75.2 }, { name: 'KARANGINDAH', district: 'BOJONGMANGU', x: 62.0, y: 86.1 }, { name: 'BOJONGMANGU', district: 'BOJONGMANGU', x: 70.9, y: 80.2 }, { name: 'KARANGMULYA', district: 'BOJONGMANGU', x: 79.6, y: 91.7 }
                ]},
                'BEKASI 4': { image: '/images/peta/dapil4.png', villages: [
                    { name: 'SUKARINGIN', district: 'SUKAWANGI', x: 34.0, y: 8.8 }, { name: 'SUKATENANG', district: 'SUKAWANGI', x: 22.2, y: 17.2 }, { name: 'SUKAKERTA', district: 'SUKAWANGI', x: 51.8, y: 18.0 }, { name: 'SUKAWANGI', district: 'SUKAWANGI', x: 60.3, y: 29.4 }, { name: 'SUKABUDI', district: 'SUKAWANGI', x: 47.2, y: 37.2 }, { name: 'SUKADAYA', district: 'SUKAWANGI', x: 41.8, y: 41.5 }, { name: 'SUKAMEKAR', district: 'SUKAWANGI', x: 27.5, y: 38.7 }, { name: 'SUKABAKTI', district: 'TAMBELANG', x: 56.2, y: 50.0 }, { name: 'SUKAMAJU', district: 'TAMBELANG', x: 66.2, y: 45.2 }, { name: 'SUKAMANTRI', district: 'TAMBELANG', x: 68.8, y: 41.6 }, { name: 'SUKARAHAYU', district: 'TAMBELANG', x: 61.2, y: 44.7 }, { name: 'SUKARAJA', district: 'TAMBELANG', x: 62.8, y: 51.6 }, { name: 'SUKARAPIH', district: 'TAMBELANG', x: 66.7, y: 53.1 }, { name: 'SUKAWIJAYA', district: 'TAMBELANG', x: 74.1, y: 47.8 }, { name: 'SRIAMUR', district: 'TAMBUN UTARA', x: 13.7, y: 56.4 }, { name: 'SRIJAYA', district: 'TAMBUN UTARA', x: 28.1, y: 53.2 }, { name: 'SRIMAHI', district: 'TAMBUN UTARA', x: 21.7, y: 60.6 }, { name: 'SRIMUKTI', district: 'TAMBUN UTARA', x: 20.4, y: 49.1 }, { name: 'SATRIAMEKAR', district: 'TAMBUN UTARA', x: 14.0, y: 70.3 }, { name: 'JEJALENJAYA', district: 'TAMBUN UTARA', x: 23.6, y: 69.1 }, { name: 'SATRIAJAYA', district: 'TAMBUN UTARA', x: 18.6, y: 84.8 }, { name: 'KARANGSATRIA', district: 'TAMBUN UTARA', x: 10.7, y: 96.2 }, { name: 'BANJARSARI', district: 'SUKATANI', x: 67.2, y: 78.2 }, { name: 'SUKAASIH', district: 'SUKATANI', x: 61.1, y: 89.7 }, { name: 'SUKADARMA', district: 'SUKATANI', x: 83.6, y: 62.2 }, { name: 'SUKAHURIP', district: 'SUKATANI', x: 73.5, y: 57.7 }, { name: 'SUKAMANAH', district: 'SUKATANI', x: 65.8, y: 60.8 }, { name: 'SUKAMULYA', district: 'SUKATANI', x: 79.4, y: 72.6 }, { name: 'SUKARUKUN', district: 'SUKATANI', x: 70.2, y: 97.4 }
                ]},
            };
            const kecamatanMapConfigs = {
                'SETU': {
                    image: '/images/peta/kecamatan/setu.png',
                    villages: [
                        { name: 'LUBANGBUAYA', x: 75.0, y: 8.5 },
                        { name: 'CIJENGKOL', x: 56.0, y: 18.5 },
                        { name: 'BURANGKENG', x: 36.0, y: 28.0 },
                        { name: 'CILEDUG', x: 63.0, y: 37.0 },
                        { name: 'CIBENING', x: 87.5, y: 45.0 },
                        { name: 'TAMAN SARI', x: 48.0, y: 55.0 },
                        { name: 'TAMAN RAHAYU', x: 13.5, y: 58.5 },
                        { name: 'CIKARAGEMAN', x: 52.0, y: 69.0 },
                        { name: 'KERTARAHAYU', x: 79.0, y: 71.0 },
                        { name: 'RAGEMANUNGGAL', x: 33.0, y: 84.0 },
                        { name: 'MUKTIJAYA', x: 58.0, y: 87.0 },
                    ],
                },
            };
            const state = { dataset: new Map(), dptDatasets: {}, currentDapil: DEFAULT_FOCUS_DAPIL, currentKecamatan: DEFAULT_FOCUS_KECAMATAN, currentDesa: '', currentStatus: 'GARAP INTENSIF', searchKeyword: '', searchDebounceId: null, activeVillageTab: 'summary', detailDrawer: null, sourceMode: 'csv', selectedPeriodId };       
            const dom = {};

            document.addEventListener('DOMContentLoaded', async () => { cacheDom(); bindEvents(); await loadInitialData(); });

            function cacheDom() {
                ['periodSelect','dapilSelect','kecamatanSelect','desaSelect','statusSelect','resetFilterBtn','searchInput','tpsFileInput','dptFileInput','sourceStatus','dptStatus','dataSourceBadge','scopeMeta','breadcrumb','breadcrumbHome','breadcrumbDapil','breadcrumbKecamatan','breadcrumbDesa','breadcrumbDividerKecamatan','breadcrumbDividerDesa','scopeHeading','scopeSubheading','cardDpt','cardSuaraSah','cardPks','cardKursi','dapilChartWrap','partyRankWrap','demographyBar','statusDashboard','statusTotalDesa','inlineMapWrap','mapAndCalegWrap','villageDetailWrap','drilldownSectionLabel','drilldownHeading','drilldownTableWrap','detailDrawer','detailDrawerTitle','detailDrawerSubtitle','detailDrawerBadge','detailDrawerContent','detailDrawerClose','detailDrawerBackdrop'].forEach((id) => { dom[id] = document.getElementById(id); });
            }

            function bindEvents() {
                dom.periodSelect?.addEventListener('change', () => {
                    const next = dom.periodSelect.value;
                    const url = new URL(window.location.href);
                    if (next) {
                        url.searchParams.set('period', next);
                    } else {
                        url.searchParams.delete('period');
                    }
                    window.location.href = url.toString();
                });
                dom.dapilSelect?.addEventListener('change', () => { state.currentDapil = dom.dapilSelect.value; state.currentKecamatan = state.currentDapil === DEFAULT_FOCUS_DAPIL ? DEFAULT_FOCUS_KECAMATAN : ''; state.currentDesa = ''; render(); });
                dom.kecamatanSelect?.addEventListener('change', () => { state.currentKecamatan = dom.kecamatanSelect.value; state.currentDesa = ''; render(); });
                dom.desaSelect?.addEventListener('change', () => { state.currentDesa = dom.desaSelect.value; render(); });
                if (dom.statusSelect) {
                    dom.statusSelect.value = state.currentStatus;
                    dom.statusSelect.addEventListener('change', () => { state.currentStatus = dom.statusSelect.value; render(); });
                }
                dom.resetFilterBtn?.addEventListener('click', () => { state.currentDapil = DEFAULT_FOCUS_DAPIL; state.currentKecamatan = DEFAULT_FOCUS_KECAMATAN; state.currentDesa = ''; state.currentStatus = ''; state.searchKeyword = ''; dom.dapilSelect.value = DEFAULT_FOCUS_DAPIL; dom.kecamatanSelect.value = DEFAULT_FOCUS_KECAMATAN; dom.statusSelect.value = ''; dom.searchInput.value = ''; render(); });
                dom.tpsFileInput?.addEventListener('change', handleTpsUpload);
                dom.dptFileInput?.addEventListener('change', handleDptUpload);
                dom.breadcrumbHome?.addEventListener('click', resetScope);
                dom.breadcrumbDapil?.addEventListener('click', () => { if (!state.currentDapil) return; state.currentKecamatan = state.currentDapil === DEFAULT_FOCUS_DAPIL ? DEFAULT_FOCUS_KECAMATAN : ''; state.currentDesa = ''; render(); });
                dom.breadcrumbKecamatan?.addEventListener('click', () => { if (!state.currentKecamatan) return; state.currentDesa = ''; render(); });
                dom.detailDrawerClose?.addEventListener('click', closeDetailDrawer);
                dom.detailDrawerBackdrop?.addEventListener('click', closeDetailDrawer);
                window.addEventListener('resize', syncTopPanelHeights);
            }

            async function loadInitialData() {
                try {
                    if (compiledPayload && Array.isArray(compiledPayload.villages) && compiledPayload.villages.length > 0) {
                        loadCompiledPayload(compiledPayload);
                        populateDapilOptions();
                        render();
                        return;
                    }

                    await Promise.all([loadTpsFromPublic(), loadDptFromPublic()]);
                    populateDapilOptions();
                    render();
                } catch (error) {
                    dom.sourceStatus.textContent = `Gagal memuat data: ${error.message}`;
                    dom.sourceStatus.style.color = '#991b1b';
                }
            }

            async function loadTpsFromPublic() {
                state.sourceMode = 'csv';
                if (dom.dataSourceBadge) {
                    dom.dataSourceBadge.textContent = 'Mode data: CSV runtime';
                }
                dom.sourceStatus.textContent = 'Mengunduh TPS CSV...';
                const response = await fetch('/data/pemilu/tps_dprd.csv', { cache: 'no-store' });
                if (!response.ok) throw new Error('TPS CSV tidak ditemukan');
                const text = await response.text();
                state.dataset = buildDataset(parseFocusedTpsCsv(text, DEFAULT_FOCUS_DAPIL));
                dom.sourceStatus.textContent = `TPS siap dimuat untuk ${DEFAULT_FOCUS_DAPIL}.`;
                dom.sourceStatus.style.color = '#166534';
            }

            async function loadDptFromPublic() {
                const candidates = dptAutoLoadCandidates[DEFAULT_FOCUS_DAPIL] || [];
                let loaded = false;
                for (const url of candidates) {
                    try {
                        const response = await fetch(url, { cache: 'no-store' });
                        if (!response.ok) continue;
                        const rows = parseCsvAuto(await response.text());
                        state.dptDatasets[DEFAULT_FOCUS_DAPIL] = buildDptDataset(rows.filter((row) => resolveLatestDapil(row.dapil, row.kecamatan) === DEFAULT_FOCUS_DAPIL));
                        loaded = true;
                        break;
                    } catch (error) {
                        // coba file kandidat berikutnya
                    }
                }
                dom.dptStatus.textContent = loaded ? `DPT siap untuk ${DEFAULT_FOCUS_DAPIL}.` : `Auto-load DPT untuk ${DEFAULT_FOCUS_DAPIL} belum menemukan file yang cocok.`;
                dom.dptStatus.style.color = loaded ? '#166534' : '#b45309';
            }

            function loadCompiledPayload(payload) {
                state.sourceMode = 'db';
                state.dataset = buildDatasetFromCompiledPayload(payload.villages || []);
                state.dptDatasets = {};
                if (dom.dataSourceBadge) {
                    dom.dataSourceBadge.textContent = `Mode data: Summary DB ${payload.period?.tahun || ''}`.trim();
                }
                dom.sourceStatus.textContent = `Summary DB siap untuk ${payload.period?.label || 'periode terpilih'}.`;
                dom.sourceStatus.style.color = '#166534';
                dom.dptStatus.textContent = 'DPT, TPS, dan breakdown wilayah dibaca dari summary database.';
                dom.dptStatus.style.color = '#166534';
            }

            function buildDatasetFromCompiledPayload(villages) {
                const dataset = new Map();

                villages.forEach((item) => {
                    const dapil = resolveLatestDapil(item.dapil, item.kecamatan);
                    const district = normalizeKey(item.kecamatan);
                    const village = normalizeKey(item.desa);
                    const villageKey = item.scope_key || `${dapil}|${district}|${village}`;
                    const dapilObj = getOrCreate(dataset, dapil, () => ({ name: dapil, desaMap: new Map(), kecamatanMap: new Map() }));
                    getOrCreate(dapilObj.kecamatanMap, district, () => ({ name: district, villages: new Set() })).villages.add(villageKey);

                    const partyMap = createPartyMapFromRows(item.party_rows || [], item.top_candidates || []);
                    const tpsMap = new Map((item.tps_rows || []).map((row) => [normalizeKey(row.label), {
                        label: normalizeKey(row.label),
                        totalVotes: numberValue(row.total_votes),
                        pksVotes: numberValue(row.pks_votes),
                        share: Number(row.share || 0),
                        rank: numberValue(row.rank || 99),
                        status: row.status || 'ZONA BERAT',
                        partyMap: new Map(),
                    }]));

                    dapilObj.desaMap.set(villageKey, {
                        key: villageKey,
                        dapil,
                        district,
                        village,
                        label: toTitleCase(item.desa),
                        districtLabel: toTitleCase(item.kecamatan),
                        tpsMap,
                        partyMap,
                        totalTps: numberValue(item.total_tps),
                        analytics: {
                            partyRows: normalizeCompiledPartyRows(item.party_rows || []),
                            totalVotes: numberValue(item.total_votes),
                            pksVotes: numberValue(item.pks_votes),
                            pksPartyVotes: numberValue(item.pks_party_votes),
                            pksCandidateVotes: numberValue(item.pks_candidate_votes),
                            share: Number(item.pks_share || 0),
                            rank: numberValue(item.pks_rank || 99),
                            gapShare: Number(item.pks_gap_share || 0),
                            status: item.status_wilayah || 'ZONA BERAT',
                            pksCandidates: item.top_candidates || [],
                        },
                        precomputedDpt: {
                            totalDpt: numberValue(item.total_dpt),
                            male: numberValue(item.total_laki),
                            female: numberValue(item.total_perempuan),
                            gen_z: numberValue(item.gen_z),
                            millennial: numberValue(item.millennial),
                            gen_x: numberValue(item.gen_x),
                            boomer: numberValue(item.boomer),
                            age_unknown: numberValue(item.age_unknown),
                            totalRw: numberValue(item.total_rw),
                            totalRt: numberValue(item.total_rt),
                            totalScopeTps: numberValue(item.meta?.total_scope_tps ?? item.total_tps),
                            matchedTps: numberValue(item.meta?.matched_tps ?? item.total_tps),
                            missingTps: numberValue(item.meta?.missing_tps ?? 0),
                            rwRows: (item.rw_rows || []).map(normalizeCompiledAreaRow),
                            rtRows: (item.rt_rows || []).map(normalizeCompiledAreaRow),
                        },
                    });
                });

                return dataset;
            }

            function normalizeCompiledPartyRows(rows) {
                return rows.map((row) => ({
                    partyId: String(row.party_id || '').trim(),
                    partyName: String(row.party_name || '').trim(),
                    partyVotes: numberValue(row.party_votes),
                    candidateVotes: numberValue(row.candidate_votes),
                    totalVotes: numberValue(row.total_votes),
                    share: Number(row.share || 0),
                }));
            }

            function createPartyMapFromRows(rows, pksCandidates = []) {
                const partyMap = new Map();
                rows.forEach((row) => {
                    const key = String(row.party_id || '').trim() || normalizeKey(row.party_name);
                    const entry = createPartyEntry(row.party_id, row.party_name);
                    entry.partyVotes = numberValue(row.party_votes);
                    entry.candidateVotes = numberValue(row.candidate_votes);
                    if ((String(row.party_id || '').trim() === PKS_PARTY_ID || normalizeKey(row.party_name) === PKS_PARTY_NAME) && Array.isArray(pksCandidates)) {
                        pksCandidates.forEach((candidate) => {
                            entry.candidates.set(String(candidate.name || ''), numberValue(candidate.votes));
                        });
                    }
                    partyMap.set(key, entry);
                });
                return partyMap;
            }

            function normalizeCompiledAreaRow(row) {
                return {
                    key: row.key,
                    type: row.type,
                    rw: row.rw || '-',
                    rt: row.rt || '-',
                    village: row.village || '',
                    district: row.district || '',
                    totalDpt: numberValue(row.total_dpt),
                    male: numberValue(row.male),
                    female: numberValue(row.female),
                    gen_z: numberValue(row.gen_z),
                    millennial: numberValue(row.millennial),
                    gen_x: numberValue(row.gen_x),
                    boomer: numberValue(row.boomer),
                    age_unknown: numberValue(row.age_unknown),
                    tpsSet: { size: numberValue(row.tps_count) },
                    pksVotes: numberValue(row.pks_votes),
                    share: Number(row.share || 0),
                    rank: numberValue(row.rank || 99),
                    status: row.status || 'ZONA BERAT',
                    topCandidate: row.top_candidate || null,
                    partyRows: row.party_rows || [],
                };
            }

            function parseCsvAuto(text) { return parseDelimitedCsv(text, detectDelimiter(text)); }
            function parseFocusedTpsCsv(text, focusDapil) {
                const rows = parseSemicolonCsv(text);
                return rows.filter((row) => resolveLatestDapil(row.dapil, row.kecamatan) === focusDapil);
            }
            function detectDelimiter(text) { const header = String(text || '').split(/\r?\n/, 1)[0] || ''; return header.includes(';') && !header.includes(',') ? ';' : ','; }
            function parseDelimitedCsv(text, delimiter) {
                const normalized = String(text || '').replace(/^\uFEFF/, '');
                const lines = normalized.split(/\r?\n/).filter((line) => line.trim() !== '');
                if (!lines.length) return [];
                const headers = splitCsvLine(lines[0], delimiter).map((header) => header.trim());
                return lines.slice(1).map((line) => {
                    const values = splitCsvLine(line, delimiter);
                    return headers.reduce((row, header, index) => {
                        row[header] = (values[index] ?? '').trim();
                        return row;
                    }, {});
                });
            }
            function splitCsvLine(line, delimiter) {
                const values = []; let current = ''; let inQuotes = false;
                for (let i = 0; i < line.length; i += 1) {
                    const char = line[i];
                    if (char === '"') {
                        if (inQuotes && line[i + 1] === '"') { current += '"'; i += 1; } else { inQuotes = !inQuotes; }
                    } else if (char === delimiter && !inQuotes) {
                        values.push(current); current = '';
                    } else {
                        current += char;
                    }
                }
                values.push(current);
                return values;
            }

            function normalizeKey(value) { return String(value ?? '').normalize('NFKC').replace(/\s+/g, ' ').trim().toUpperCase(); }
            function resolveLatestDapil(rawDapil, rawKecamatan) {
                const district = normalizeKey(rawKecamatan);
                if (district === 'CIKARANG SELATAN') return 'BEKASI 1';
                const normalized = normalizeKey(rawDapil);
                const match = normalized.match(/BEKASI\s*([1-7])/);
                return match ? `BEKASI ${match[1]}` : normalized;
            }

            function buildDataset(rows) {
                const dataset = new Map();
                rows.forEach((row) => {
                    const dapil = resolveLatestDapil(row.dapil, row.kecamatan);
                    const district = normalizeKey(row.kecamatan);
                    const village = normalizeKey(row.desa);
                    const villageKey = `${dapil}|${district}|${village}`;
                    const tpsLabel = normalizeKey(row.tps);
                    const isAggregate = tpsLabel === 'TPS 000' || Number(row.nomor_urut || 0) === 0;
                    const suara = numberValue(row.suara);
                    const partyId = String(row.partai_id || '').trim();
                    const partyName = String(row.partai || '').trim();
                    const candidateName = String(row.nama || '').trim();
                    const nomorUrut = numberValue(row.nomor_urut);

                    const dapilObj = getOrCreate(dataset, dapil, () => ({ name: dapil, desaMap: new Map(), kecamatanMap: new Map() }));
                    getOrCreate(dapilObj.kecamatanMap, district, () => ({ name: district, villages: new Set() })).villages.add(villageKey);
                    const villageObj = getOrCreate(dapilObj.desaMap, villageKey, () => ({ key: villageKey, dapil, district, village, label: toTitleCase(village), districtLabel: toTitleCase(district), tpsMap: new Map(), partyMap: new Map(), totalTps: 0, analytics: null }));
                    const villageParty = getOrCreate(villageObj.partyMap, partyId || normalizeKey(partyName), () => createPartyEntry(partyId, partyName));

                    if (isAggregate) {
                        villageParty.partyVotes += suara;
                    } else {
                        villageParty.candidateVotes += suara;
                        villageParty.candidates.set(candidateName || `No.${nomorUrut}`, (villageParty.candidates.get(candidateName || `No.${nomorUrut}`) || 0) + suara);
                        const tpsObj = getOrCreate(villageObj.tpsMap, tpsLabel, () => ({ label: tpsLabel, partyMap: new Map(), totalVotes: 0 }));
                        const tpsParty = getOrCreate(tpsObj.partyMap, partyId || normalizeKey(partyName), () => createPartyEntry(partyId, partyName));
                        tpsParty.candidateVotes += suara;
                        tpsParty.candidates.set(candidateName || `No.${nomorUrut}`, (tpsParty.candidates.get(candidateName || `No.${nomorUrut}`) || 0) + suara);
                        tpsObj.totalVotes += suara;
                    }
                });
                dataset.forEach((dapilObj) => dapilObj.desaMap.forEach((villageObj) => { villageObj.totalTps = villageObj.tpsMap.size; villageObj.analytics = analyzePks(villageObj.partyMap, villageObj.totalTps); }));
                return dataset;
            }

            function buildDptDataset(rows) {
                const dataset = new Map();
                rows.forEach((row) => {
                    const dapil = resolveLatestDapil(row.dapil, row.kecamatan);
                    const district = normalizeKey(row.kecamatan);
                    const village = normalizeKey(row.desa);
                    const villageKey = `${dapil}|${district}|${village}`;
                    const tps = normalizeKey(row.tps);
                    const rw = formatRwRt(row.rw);
                    const rt = formatRwRt(row.rt);
                    const pid = String(row.pid || `${villageKey}|${rw}|${rt}|${row.nama}|${row.usia}`).trim();
                    const age = numberValue(row.usia);
                    const totalGenderBase = Math.max(numberValue(row.dpt_tot), 1);
                    const maleShare = numberValue(row.dpt_lk) / totalGenderBase;
                    const femaleShare = numberValue(row.dpt_pr) / totalGenderBase;
                    const villageObj = getOrCreate(dataset, villageKey, () => ({ key: villageKey, dapil, district, village, label: toTitleCase(village), districtLabel: toTitleCase(district), totalDpt: 0, male: 0, female: 0, gen_z: 0, millennial: 0, gen_x: 0, boomer: 0, age_unknown: 0, rwMap: new Map(), tpsMap: new Map(), seen: new Set() }));
                    if (villageObj.seen.has(pid)) return;
                    villageObj.seen.add(pid);
                    villageObj.totalDpt += 1;
                    villageObj.male += Number.isFinite(maleShare) ? maleShare : 0;
                    villageObj.female += Number.isFinite(femaleShare) ? femaleShare : 0;
                    incrementAgeBucket(villageObj, age);
                    const tpsObj = getOrCreate(villageObj.tpsMap, tps, () => ({ label: tps, totalDpt: 0, male: 0, female: 0, gen_z: 0, millennial: 0, gen_x: 0, boomer: 0, age_unknown: 0, rwMap: new Map(), rtMap: new Map() }));
                    tpsObj.totalDpt += 1;
                    tpsObj.male += Number.isFinite(maleShare) ? maleShare : 0;
                    tpsObj.female += Number.isFinite(femaleShare) ? femaleShare : 0;
                    incrementAgeBucket(tpsObj, age);
                    const rwObj = getOrCreate(villageObj.rwMap, rw, () => ({ key: `${villageKey}|RW|${rw}`, rw, villageKey, village: villageObj.label, district: villageObj.districtLabel, totalDpt: 0, male: 0, female: 0, gen_z: 0, millennial: 0, gen_x: 0, boomer: 0, age_unknown: 0, rtMap: new Map(), tpsSet: new Set() }));
                    rwObj.totalDpt += 1; rwObj.male += Number.isFinite(maleShare) ? maleShare : 0; rwObj.female += Number.isFinite(femaleShare) ? femaleShare : 0; incrementAgeBucket(rwObj, age);
                    rwObj.tpsSet.add(tps);
                    const tpsRwObj = getOrCreate(tpsObj.rwMap, rw, () => ({ rw, totalDpt: 0, male: 0, female: 0, gen_z: 0, millennial: 0, gen_x: 0, boomer: 0, age_unknown: 0, rtMap: new Map() }));
                    tpsRwObj.totalDpt += 1; tpsRwObj.male += Number.isFinite(maleShare) ? maleShare : 0; tpsRwObj.female += Number.isFinite(femaleShare) ? femaleShare : 0; incrementAgeBucket(tpsRwObj, age);
                    const rtObj = getOrCreate(rwObj.rtMap, rt, () => ({ key: `${villageKey}|RW|${rw}|RT|${rt}`, rt, rw, villageKey, village: villageObj.label, district: villageObj.districtLabel, totalDpt: 0, male: 0, female: 0, gen_z: 0, millennial: 0, gen_x: 0, boomer: 0, age_unknown: 0, tpsSet: new Set() }));
                    rtObj.totalDpt += 1; rtObj.male += Number.isFinite(maleShare) ? maleShare : 0; rtObj.female += Number.isFinite(femaleShare) ? femaleShare : 0; incrementAgeBucket(rtObj, age);
                    rtObj.tpsSet.add(tps);
                    const tpsRtObj = getOrCreate(tpsObj.rtMap, `${rw}|${rt}`, () => ({ rw, rt, totalDpt: 0, male: 0, female: 0, gen_z: 0, millennial: 0, gen_x: 0, boomer: 0, age_unknown: 0 }));
                    tpsRtObj.totalDpt += 1; tpsRtObj.male += Number.isFinite(maleShare) ? maleShare : 0; tpsRtObj.female += Number.isFinite(femaleShare) ? femaleShare : 0; incrementAgeBucket(tpsRtObj, age);
                });
                return dataset;
            }

            function getSortedParties(partyMap) { return Array.from(partyMap.values()).map((entry) => ({ ...entry, totalVotes: entry.partyVotes + entry.candidateVotes, share: 0 })).sort((a, b) => b.totalVotes - a.totalVotes); }
            function analyzePks(partyMap, totalTps) {
                const partyRows = getSortedParties(partyMap);
                const totalVotes = partyRows.reduce((sum, row) => sum + row.totalVotes, 0);
                partyRows.forEach((row) => { row.share = totalVotes ? row.totalVotes / totalVotes : 0; });
                const pksRow = partyRows.find((row) => row.partyId === PKS_PARTY_ID || normalizeKey(row.partyName) === PKS_PARTY_NAME) || { partyVotes: 0, candidateVotes: 0, totalVotes: 0, share: 0, partyName: PKS_PARTY_NAME, candidates: new Map() };
                const rankIndex = partyRows.findIndex((row) => row.partyId === PKS_PARTY_ID || normalizeKey(row.partyName) === PKS_PARTY_NAME);
                const rank = rankIndex === -1 ? partyRows.length + 1 : rankIndex + 1;
                const leaderShare = partyRows[0]?.share || 0;
                const secondShare = partyRows[1]?.share || 0;
                const gapShare = rank === 1 ? Math.max(0, pksRow.share - secondShare) : Math.max(0, leaderShare - pksRow.share);
                return { partyRows, totalVotes, totalTps, pksVotes: pksRow.totalVotes, pksPartyVotes: pksRow.partyVotes, pksCandidateVotes: pksRow.candidateVotes, share: pksRow.share, rank, gapShare, status: classifyPriority({ pksVotes: pksRow.totalVotes, share: pksRow.share, rank, gapShare }), pksCandidates: Array.from((pksRow.candidates || new Map()).entries()).map(([name, votes]) => ({ name, votes })).sort((a, b) => b.votes - a.votes) };
            }
            function classifyPriority(metrics) {
                const { pksVotes, share, rank, gapShare } = metrics;
                if (pksVotes <= 0) return 'ZONA BERAT';
                if (rank === 1 && share >= 0.3) return 'JAGA KUAT';
                if (rank === 1) return 'AMANKAN';
                if (rank === 2 && gapShare <= 0.05) return 'REBUT REALISTIS';
                if (rank <= 3 || share >= 0.12) return 'GARAP INTENSIF';
                return 'ZONA BERAT';
            }

            function getVisibleVillages(dapilObj = null) {
                const dapilObjects = state.currentDapil ? [state.dataset.get(state.currentDapil)].filter(Boolean) : Array.from(state.dataset.values());
                let villages = dapilObjects.flatMap((entry) => Array.from(entry.desaMap.values()));
                if (dapilObj && !state.currentDapil) villages = Array.from(dapilObj.desaMap.values());
                return villages.filter((village) => {
                    if (state.currentKecamatan && village.district !== state.currentKecamatan) return false;
                    if (state.currentDesa && village.key !== state.currentDesa) return false;
                    if (state.currentStatus && village.analytics.status !== state.currentStatus) return false;
                    if (state.searchKeyword) {
                        const keyword = normalizeKey(state.searchKeyword);
                        return village.village.includes(keyword) || village.district.includes(keyword) || village.dapil.includes(keyword);
                    }
                    return true;
                }).sort((a, b) => b.analytics.pksVotes - a.analytics.pksVotes || compareNatural(a.label, b.label));
            }

            function buildScopeData(dapilObj) {
                const visibleVillages = getVisibleVillages(dapilObj);
                const scopePartyMap = new Map();
                const dptScope = { totalDpt: 0, male: 0, female: 0, gen_z: 0, millennial: 0, gen_x: 0, boomer: 0, age_unknown: 0, totalRw: 0, totalRt: 0 };
                const statusRows = Object.keys(statusConfig).map((key) => ({ key, count: 0, pksVotes: 0 }));
                visibleVillages.forEach((village) => {
                    mergePartyMaps(scopePartyMap, village.partyMap);
                    const dptVillage = getDptVillage(village);
                    const statusRow = statusRows.find((row) => row.key === village.analytics.status);
                    if (statusRow) { statusRow.count += 1; statusRow.pksVotes += village.analytics.pksVotes; }
                    if (dptVillage) {
                        dptScope.totalDpt += dptVillage.totalDpt; dptScope.male += dptVillage.male; dptScope.female += dptVillage.female;
                        dptScope.gen_z += dptVillage.gen_z; dptScope.millennial += dptVillage.millennial; dptScope.gen_x += dptVillage.gen_x; dptScope.boomer += dptVillage.boomer; dptScope.age_unknown += dptVillage.age_unknown;
                        dptScope.totalRw += dptVillage.totalRw ?? dptVillage.rwRows?.length ?? dptVillage.rwMap.size ?? 0;
                        dptScope.totalRt += dptVillage.totalRt ?? dptVillage.rtRows?.length ?? Array.from(dptVillage.rwMap?.values?.() || []).reduce((sum, rw) => sum + rw.rtMap.size, 0);
                    }
                });
                const scopeAnalytics = analyzePks(scopePartyMap, visibleVillages.reduce((sum, village) => sum + village.totalTps, 0));
                const seats = estimateSeats(scopeAnalytics.pksVotes, scopeAnalytics.totalVotes, TOTAL_DPRD_SEATS);
                const level = state.currentDesa ? 'desa' : (state.currentKecamatan ? 'kecamatan' : (state.currentDapil ? 'dapil' : 'kabupaten'));
                return { level, visibleVillages, selectedVillage: state.currentDesa ? findVillageByKey(state.currentDesa) : null, dptScope, scopeAnalytics, statusRows, seats, drilldownRows: buildDrilldownRows(level, visibleVillages), dapilChartRows: buildDapilChartRows(), partyRanking: scopeAnalytics.partyRows.slice(0, 5) };
            }

            function buildDrilldownRows(level, visibleVillages) {
                if (level === 'kabupaten') {
                    return Array.from(state.dataset.values()).map((dapilObj) => {
                        const villages = getVisibleVillages(dapilObj);
                        const partyMap = new Map();
                        let totalDpt = 0;
                        villages.forEach((village) => { mergePartyMaps(partyMap, village.partyMap); totalDpt += getDptVillage(village)?.totalDpt || 0; });
                        const analytics = analyzePks(partyMap, villages.reduce((sum, village) => sum + village.totalTps, 0));
                        return { type: 'dapil', key: dapilObj.name, name: `Dapil ${dapilObj.name.replace('BEKASI ', '')}`, totalDpt, totalVotes: analytics.totalVotes, pksVotes: analytics.pksVotes, share: analytics.share, status: analytics.status };
                    }).sort((a, b) => compareNatural(a.key, b.key));
                }
                if (level === 'dapil') {
                    const grouped = new Map();
                    visibleVillages.forEach((village) => {
                        const row = getOrCreate(grouped, village.district, () => ({ type: 'kecamatan', key: village.district, name: toTitleCase(village.district), partyMap: new Map(), totalDpt: 0 }));
                        row.totalDpt += getDptVillage(village)?.totalDpt || 0;
                        mergePartyMaps(row.partyMap, village.partyMap);
                    });
                    return Array.from(grouped.values()).map((row) => { const analytics = analyzePks(row.partyMap, 0); return { ...row, totalVotes: analytics.totalVotes, pksVotes: analytics.pksVotes, share: analytics.share, status: analytics.status }; }).sort((a, b) => b.pksVotes - a.pksVotes);
                }
                if (level === 'kecamatan') {
                    return visibleVillages.map((village) => ({ type: 'desa', key: village.key, name: village.label, totalDpt: getDptVillage(village)?.totalDpt || 0, totalVotes: village.analytics.totalVotes, pksVotes: village.analytics.pksVotes, share: village.analytics.share, status: village.analytics.status })).sort((a, b) => b.pksVotes - a.pksVotes);
                }
                const village = visibleVillages[0];
                if (!village) return [];
                return Array.from(village.tpsMap.values()).map((tps) => {
                    if (tps.partyMap instanceof Map && tps.partyMap.size > 0) {
                        const analytics = analyzePks(tps.partyMap, 1);
                        return { type: 'tps', key: tps.label, name: tps.label, totalDpt: 0, totalVotes: analytics.totalVotes, pksVotes: analytics.pksVotes, share: analytics.share, status: analytics.status };
                    }

                    return {
                        type: 'tps',
                        key: tps.label,
                        name: tps.label,
                        totalDpt: 0,
                        totalVotes: numberValue(tps.totalVotes),
                        pksVotes: numberValue(tps.pksVotes),
                        share: Number(tps.share || 0),
                        status: tps.status || 'ZONA BERAT',
                    };
                }).sort((a, b) => compareNatural(a.key, b.key));
            }

            function buildDptScopeData(targetVillages) {
                const precomputedVillages = targetVillages.filter((village) => Boolean(village.precomputedDpt));
                if (precomputedVillages.length === targetVillages.length && precomputedVillages.length > 0) {
                    const result = {
                        available: true,
                        totalScopeTps: 0,
                        matchedTps: 0,
                        missingTps: 0,
                        totalDpt: 0,
                        totalMale: 0,
                        totalFemale: 0,
                        generation: { gen_z: 0, millennial: 0, gen_x: 0, boomer: 0, age_unknown: 0 },
                        missingVillages: [],
                        rwRows: [],
                        rtRows: [],
                    };

                    precomputedVillages.forEach((village) => {
                        const dptVillage = village.precomputedDpt;
                        result.totalScopeTps += dptVillage.totalScopeTps || 0;
                        result.matchedTps += dptVillage.matchedTps || 0;
                        result.missingTps += dptVillage.missingTps || 0;
                        result.totalDpt += dptVillage.totalDpt || 0;
                        result.totalMale += dptVillage.male || 0;
                        result.totalFemale += dptVillage.female || 0;
                        result.generation.gen_z += dptVillage.gen_z || 0;
                        result.generation.millennial += dptVillage.millennial || 0;
                        result.generation.gen_x += dptVillage.gen_x || 0;
                        result.generation.boomer += dptVillage.boomer || 0;
                        result.generation.age_unknown += dptVillage.age_unknown || 0;
                        result.rwRows.push(...(dptVillage.rwRows || []));
                        result.rtRows.push(...(dptVillage.rtRows || []));
                    });

                    result.rwRows.sort((a, b) => b.pksVotes - a.pksVotes || compareNatural(a.key, b.key));
                    result.rtRows.sort((a, b) => b.pksVotes - a.pksVotes || compareNatural(a.key, b.key));

                    return result;
                }

                const rwMap = new Map();
                const rtMap = new Map();
                const result = {
                    available: false,
                    totalScopeTps: 0,
                    matchedTps: 0,
                    missingTps: 0,
                    totalDpt: 0,
                    totalMale: 0,
                    totalFemale: 0,
                    generation: { gen_z: 0, millennial: 0, gen_x: 0, boomer: 0, age_unknown: 0 },
                    missingVillages: [],
                    rwRows: [],
                    rtRows: [],
                };

                targetVillages.forEach((village) => {
                    const dptVillage = getDptVillage(village);
                    if (!dptVillage) {
                        result.missingVillages.push(village.label);
                        return;
                    }

                    result.available = true;
                    result.totalDpt += dptVillage.totalDpt;
                    result.totalMale += dptVillage.male;
                    result.totalFemale += dptVillage.female;
                    result.generation.gen_z += dptVillage.gen_z;
                    result.generation.millennial += dptVillage.millennial;
                    result.generation.gen_x += dptVillage.gen_x;
                    result.generation.boomer += dptVillage.boomer;
                    result.generation.age_unknown += dptVillage.age_unknown;

                    dptVillage.tpsMap.forEach((dptTps, tpsKey) => {
                        result.totalScopeTps += 1;
                        const voteTps = village.tpsMap.get(tpsKey);
                        if (!voteTps) {
                            result.missingTps += 1;
                            return;
                        }

                        result.matchedTps += 1;

                        dptTps.rwMap.forEach((rwEntry, rwKey) => {
                            const share = dptTps.totalDpt ? (rwEntry.totalDpt / dptTps.totalDpt) : 0;
                            const rwRow = getOrCreate(rwMap, `${village.key}|${rwKey}`, () => createEstimatedAreaRow({
                                key: `${village.key}|${rwKey}`,
                                type: 'rw',
                                villageKey: village.key,
                                village: village.label,
                                district: village.districtLabel,
                                rw: rwKey,
                            }));
                            mergeDemographyIntoRow(rwRow, rwEntry, tpsKey);
                            addScaledPartyMap(rwRow.partyMap, voteTps.partyMap, share);

                            rwEntry.rtMap.forEach((rtEntry, rtKey) => {
                                const rtShare = dptTps.totalDpt ? (rtEntry.totalDpt / dptTps.totalDpt) : 0;
                                const rtRow = getOrCreate(rtMap, `${village.key}|${rwKey}|${rtKey}`, () => createEstimatedAreaRow({
                                    key: `${village.key}|${rwKey}|${rtKey}`,
                                    type: 'rt',
                                    villageKey: village.key,
                                    village: village.label,
                                    district: village.districtLabel,
                                    rw: rwKey,
                                    rt: rtKey,
                                }));
                                mergeDemographyIntoRow(rtRow, rtEntry, tpsKey);
                                addScaledPartyMap(rtRow.partyMap, voteTps.partyMap, rtShare);
                            });
                        });
                    });
                });

                result.rwRows = transformEstimatedRows(rwMap);
                result.rtRows = transformEstimatedRows(rtMap);
                return result;
            }

            function transformEstimatedRows(groupMap) {
                return Array.from(groupMap.values()).map((row) => {
                    const analytics = analyzePks(row.partyMap, row.tpsSet.size);
                    return {
                        ...row,
                        analytics,
                        totalVotes: analytics.totalVotes,
                        pksVotes: analytics.pksVotes,
                        share: analytics.share,
                        status: analytics.status,
                        rank: analytics.rank,
                    };
                }).sort((a, b) => b.pksVotes - a.pksVotes || compareNatural(a.key, b.key));
            }

            function addScaledPartyMap(targetMap, sourceMap, factor) {
                sourceMap.forEach((entry, key) => {
                    const targetEntry = getOrCreate(targetMap, key, () => createPartyEntry(entry.partyId, entry.partyName));
                    targetEntry.partyVotes += entry.partyVotes * factor;
                    targetEntry.candidateVotes += entry.candidateVotes * factor;
                    entry.candidates.forEach((votes, name) => targetEntry.candidates.set(name, (targetEntry.candidates.get(name) || 0) + (votes * factor)));
                });
            }

            function createEstimatedAreaRow({ key, type, villageKey, village, district, rw = '', rt = '' }) {
                return { key, type, villageKey, village, district, rw, rt, totalDpt: 0, male: 0, female: 0, gen_z: 0, millennial: 0, gen_x: 0, boomer: 0, age_unknown: 0, tpsSet: new Set(), partyMap: new Map() };
            }

            function mergeDemographyIntoRow(target, source, tpsKey) {
                target.totalDpt += source.totalDpt;
                target.male += source.male;
                target.female += source.female;
                target.gen_z += source.gen_z;
                target.millennial += source.millennial;
                target.gen_x += source.gen_x;
                target.boomer += source.boomer;
                target.age_unknown += source.age_unknown;
                target.tpsSet.add(tpsKey);
            }

            function buildDapilChartRows() {
                return Array.from(state.dataset.values()).map((dapilObj) => {
                    const partyMap = new Map();
                    Array.from(dapilObj.desaMap.values()).forEach((village) => mergePartyMaps(partyMap, village.partyMap));
                    const analytics = analyzePks(partyMap, 0);
                    return { dapil: dapilObj.name, pksVotes: analytics.pksVotes, share: analytics.share };
                }).sort((a, b) => compareNatural(a.dapil, b.dapil));
            }

            function render() {
                populateDapilOptions();
                populateKecamatanOptions();
                populateDesaOptions();
                const scopeData = buildScopeData(state.currentDapil ? state.dataset.get(state.currentDapil) : null);
                renderBreadcrumb();
                renderHeader(scopeData);
                renderSummaryCards(scopeData, scopeData.dptScope);
                renderPartyRanking(scopeData);
                renderDemographyBar(scopeData.dptScope);
                renderStatusDashboard(scopeData.statusRows);
                renderMapSection(scopeData);
                renderVillageDetail(scopeData);
                renderDrilldownTable(scopeData.drilldownRows, scopeData.level);
            }

            function renderBreadcrumb() {
                const visible = Boolean(state.currentDapil || state.currentKecamatan || state.currentDesa);
                dom.scopeMeta.style.display = visible ? 'none' : 'flex';
                dom.breadcrumb.style.display = visible ? 'flex' : 'none';
                dom.breadcrumbDapil.textContent = state.currentDapil ? `Dapil ${state.currentDapil.replace('BEKASI ', '')}` : 'Dapil';
                dom.breadcrumbDapil.style.display = state.currentDapil ? 'inline' : 'none';
                dom.breadcrumbDividerKecamatan.style.display = state.currentDapil ? 'inline' : 'none';
                dom.breadcrumbKecamatan.style.display = state.currentDapil ? 'inline' : 'none';
                dom.breadcrumbKecamatan.textContent = state.currentKecamatan ? toTitleCase(state.currentKecamatan) : '(pilih kecamatan)';
                dom.breadcrumbKecamatan.style.color = state.currentKecamatan ? '#fe5000' : '#ccc';
                dom.breadcrumbDividerDesa.style.display = state.currentDesa ? 'inline' : 'none';
                dom.breadcrumbDesa.style.display = state.currentDesa ? 'inline' : 'none';
                dom.breadcrumbDesa.textContent = state.currentDesa ? findVillageByKey(state.currentDesa)?.label || '(pilih desa)' : '(pilih desa)';
                dom.breadcrumbDesa.style.color = state.currentDesa ? '#fe5000' : '#ccc';
            }

            function renderHeader(scopeData) {
                if (scopeData.level === 'kabupaten') {
                    dom.scopeHeading.textContent = 'Kabupaten Bekasi';
                    dom.scopeSubheading.textContent = `Hasil ${currentPeriodLabel()}`;
                } else if (scopeData.level === 'dapil') {
                    dom.scopeHeading.textContent = `Dapil ${state.currentDapil.replace('BEKASI ', '')}`;
                    dom.scopeSubheading.textContent = `${formatNumber(scopeData.visibleVillages.length)} desa/kelurahan · ${formatNumber(scopeData.scopeAnalytics.totalTps)} TPS`;
                } else if (scopeData.level === 'kecamatan') {
                    dom.scopeHeading.textContent = toTitleCase(state.currentKecamatan);
                    dom.scopeSubheading.textContent = `Dalam ${state.currentDapil} · ${formatNumber(scopeData.visibleVillages.length)} desa`;
                } else {
                    dom.scopeHeading.textContent = scopeData.selectedVillage?.label || 'Desa';
                    dom.scopeSubheading.textContent = `${toTitleCase(state.currentKecamatan)} · ${formatNumber(scopeData.scopeAnalytics.totalTps)} TPS`;
                }
            }

            function renderSummaryCards(scopeData, dptScope) {
                const participation = dptScope.totalDpt ? (scopeData.scopeAnalytics.totalVotes / dptScope.totalDpt) : 0;
                dom.cardDpt.innerHTML = renderSummaryCard({ label: 'Total DPT', value: formatNumber(dptScope.totalDpt), subtext: `${formatNumber(scopeData.scopeAnalytics.totalTps)} TPS · ${formatNumber(scopeData.visibleVillages.length)} desa`, icon: '👥' });
                dom.cardSuaraSah.innerHTML = renderSummaryCard({ label: 'Suara Sah', value: formatNumber(scopeData.scopeAnalytics.totalVotes), subtext: `Partisipasi ${formatPercent(participation)}`, icon: '📊' });
                dom.cardPks.innerHTML = renderSummaryCard({ label: 'Suara PKS', value: formatNumber(scopeData.scopeAnalytics.pksVotes), subtext: `${formatPercent(scopeData.scopeAnalytics.share)} suara sah · peringkat ${scopeData.scopeAnalytics.rank}`, icon: '⭐', invert: true });
            }

            function renderDapilChart(rows) {
                const maxVotes = Math.max(1, ...rows.map((row) => row.pksVotes));
                dom.dapilChartWrap.innerHTML = rows.map((row) => {
                    const width = Math.max(6, (row.pksVotes / maxVotes) * 100);
                    const active = state.currentDapil === row.dapil;
                    return `<button type="button" data-dapil-bar="${escapeHtml(row.dapil)}" style="display:flex;align-items:center;gap:10px;font-size:12px;background:${active ? '#fff7f1' : 'transparent'};border:none;padding:4px;border-radius:6px;cursor:pointer;text-align:left;"><div style="width:50px;color:#666;">Dapil ${escapeHtml(row.dapil.replace('BEKASI ', ''))}</div><div style="flex:1;background:#f5f5f5;border-radius:4px;height:22px;overflow:hidden;"><div style="background:#fe5000;height:100%;width:${width}%;border-radius:4px;display:flex;align-items:center;padding-left:8px;color:white;font-size:11px;font-weight:500;">${formatCompactNumber(row.pksVotes)}</div></div><div style="width:46px;text-align:right;font-weight:500;color:#1a1a1a;">${formatPercent(row.share)}</div></button>`;
                }).join('');
                dom.dapilChartWrap.querySelectorAll('[data-dapil-bar]').forEach((button) => button.addEventListener('click', () => { state.currentDapil = button.dataset.dapilBar; state.currentKecamatan = ''; dom.dapilSelect.value = state.currentDapil; render(); }));
            }

            function renderPartyRanking(scopeData) {
                dom.partyRankWrap.innerHTML = scopeData.partyRanking.map((row, index) => {
                    const partyName = normalizePartyName(row.partyName);
                    const color = partyColors[partyName] || '#888';
                    const isPks = normalizeKey(partyName) === PKS_PARTY_NAME;
                    return `<div style="display:grid;grid-template-columns:26px 10px minmax(0,1fr) 70px 54px;align-items:center;column-gap:10px;font-size:12px;padding:${isPks ? '8px 10px' : '6px 2px'};border-radius:${isPks ? '8px' : '0'};background:${isPks ? 'linear-gradient(90deg,#fff4eb 0%,#fff7f1 100%)' : 'transparent'};border:${isPks ? '1px solid #fbd3b6' : '1px solid transparent'};box-shadow:${isPks ? 'inset 3px 0 0 #fe5000, 0 1px 2px rgba(254,80,0,0.08)' : 'none'};"><div style="font-size:${isPks ? '11px' : '10px'};font-weight:${isPks ? '700' : '500'};color:${isPks ? '#fe5000' : '#888'};text-align:center;">${index + 1}</div><div style="width:${isPks ? '10px' : '8px'};height:${isPks ? '10px' : '8px'};border-radius:50%;background:${color};box-shadow:${isPks ? '0 0 0 3px rgba(254,80,0,0.12)' : 'none'};"></div><div style="min-width:0;display:flex;align-items:center;gap:8px;"><div style="font-weight:${isPks ? '700' : '500'};color:#1a1a1a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escapeHtml(partyName)}</div>${isPks ? '<span style="font-size:9px;font-weight:700;letter-spacing:0.6px;color:#ffffff;background:#fe5000;border-radius:999px;padding:3px 7px;line-height:1;">FOKUS</span>' : ''}</div><div style="font-weight:${isPks ? '700' : '500'};color:${isPks ? '#7c2d12' : '#1a1a1a'};text-align:right;font-variant-numeric:tabular-nums;">${formatCompactNumber(row.totalVotes)}</div><div style="text-align:right;color:${isPks ? '#c2410c' : '#888'};font-weight:${isPks ? '700' : '500'};font-variant-numeric:tabular-nums;">${formatPercent(row.share)}</div></div>`;
                }).join('') || '<div style="font-size:12px;color:#888;">Belum ada data partai.</div>';
            }

            function renderDemographyBar(dptScope) {
                const total = Math.max(1, dptScope.totalDpt);
                const slices = [{ label: 'Z', value: dptScope.gen_z, color: '#a78bfa' }, { label: 'Mil', value: dptScope.millennial, color: '#fe5000' }, { label: 'X', value: dptScope.gen_x, color: '#16a34a' }, { label: 'Boom', value: dptScope.boomer, color: '#94a3b8' }];
                dom.demographyBar.innerHTML = `<div style="height:6px;background:#f3f4f6;border-radius:999px;overflow:hidden;display:flex;">${slices.map((slice) => `<span style="display:block;height:100%;background:${slice.color};width:${(slice.value / total) * 100}%;"></span>`).join('')}</div><div style="margin-top:6px;font-size:10px;color:#666;display:flex;gap:12px;flex-wrap:wrap;">${slices.map((slice) => `<span>${slice.label} ${formatPercent(slice.value / total)}</span>`).join('')}</div>`;
            }

            function renderStatusDashboard(rows) {
                dom.statusTotalDesa.textContent = formatNumber(rows.reduce((sum, row) => sum + row.count, 0));
                dom.statusDashboard.innerHTML = rows.map((row) => {
                    const config = statusConfig[row.key];
                    return `<button type="button" data-status-card="${row.key}" style="background:${config.bg};border:none;border-radius:8px;padding:10px;cursor:pointer;text-align:left;"><div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;"><div style="width:8px;height:8px;background:${config.dot};border-radius:50%;"></div><div style="font-size:11px;color:${config.text};font-weight:500;">${config.label}</div></div><div style="font-size:20px;font-weight:500;color:${config.text};line-height:1;">${formatNumber(row.count)}</div><div style="font-size:10px;color:${config.dot};margin-top:2px;">${config.criteria}</div></button>`;
                }).join('');
                dom.statusDashboard.querySelectorAll('[data-status-card]').forEach((button) => button.addEventListener('click', () => { const next = button.dataset.statusCard; state.currentStatus = state.currentStatus === next ? '' : next; dom.statusSelect.value = state.currentStatus; render(); }));
            }

            function renderMapSection(scopeData) {
                const mapHtml = renderMap(scopeData.visibleVillages);
                const calegHtml = renderCalegPanel(scopeData);
                dom.inlineMapWrap.innerHTML = mapHtml;
                dom.mapAndCalegWrap.innerHTML = scopeData.level === 'dapil' ? `<div style="display:grid;grid-template-columns:minmax(0,1fr);gap:12px;">${calegHtml}</div>` : '';
                syncTopPanelHeights();
                document.querySelectorAll('[data-map-marker]').forEach((marker) => marker.addEventListener('click', () => { const village = findVillageByKey(marker.dataset.mapMarker); if (village) { selectVillage(village); } }));
            }

            function renderVillageDetail(scopeData) {
                if (scopeData.level !== 'desa' || !scopeData.selectedVillage) {
                    dom.villageDetailWrap.innerHTML = '';
                    closeDetailDrawer(true);
                    return;
                }

                const dptScopeData = buildDptScopeData([scopeData.selectedVillage]);
                const program = buildProgramRecommendation({
                    statusKey: scopeData.scopeAnalytics.status,
                    totalDpt: dptScopeData.totalDpt,
                    totalMale: dptScopeData.totalMale,
                    totalFemale: dptScopeData.totalFemale,
                    generation: dptScopeData.generation,
                });

                dom.villageDetailWrap.innerHTML = renderVillageTabs(scopeData, dptScopeData, program);

                dom.villageDetailWrap.querySelectorAll('[data-village-tab]').forEach((button) => {
                    button.addEventListener('click', () => {
                        state.activeVillageTab = button.dataset.villageTab;
                        renderVillageDetail(scopeData);
                    });
                });

                dom.villageDetailWrap.querySelectorAll('[data-detail-open]').forEach((button) => {
                    button.addEventListener('click', () => {
                        state.detailDrawer = { type: button.dataset.detailType, key: button.dataset.detailOpen };
                        renderDetailDrawer(dptScopeData, scopeData.scopeAnalytics.status, program);
                    });
                });

                renderDetailDrawer(dptScopeData, scopeData.scopeAnalytics.status, program);
            }

            function renderVillageTabs(scopeData, dptScopeData, program) {
                if (!dptScopeData.available) {
                    return `<div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:16px;font-size:12px;color:#666;">Data DPT belum tersedia untuk desa ini. Upload file DPT via menu Sumber Data untuk membuka breakdown RW/RT.</div>`;
                }

                const summaryCards = [
                    { label: 'DPT Wilayah', value: formatNumber(dptScopeData.totalDpt) },
                    { label: 'TPS Cocok', value: `${formatNumber(dptScopeData.matchedTps)} / ${formatNumber(dptScopeData.totalScopeTps)}` },
                    { label: 'Estimasi PKS', value: `~${formatNumber(scopeData.scopeAnalytics.pksVotes)}` },
                    { label: 'RW Terbaca', value: formatNumber(dptScopeData.rwRows.length) },
                    { label: 'RT Terbaca', value: formatNumber(dptScopeData.rtRows.length) },
                    { label: 'TPS Missing', value: formatNumber(dptScopeData.missingTps) },
                ];

                return `<div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:grid;gap:14px;">
                    <div>
                        <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Analisis Wilayah Detail</div>
                        <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">${escapeHtml(scopeData.selectedVillage.label)} · ${escapeHtml(scopeData.selectedVillage.districtLabel)}</div>
                    </div>
                    <div style="display:flex;gap:4px;flex-wrap:wrap;">
                        ${['summary','rw','rt','demography','program'].map((tab) => `<button type="button" class="tab-btn ${state.activeVillageTab === tab ? 'active' : ''}" data-village-tab="${tab}">${tab === 'summary' ? 'Ringkasan' : (tab === 'rw' ? 'RW' : (tab === 'rt' ? 'RT' : (tab === 'demography' ? 'Demografi' : 'Program')))}</button>`).join('')}
                    </div>
                    <div class="tab-pane ${state.activeVillageTab === 'summary' ? 'active' : ''}" style="display:${state.activeVillageTab === 'summary' ? 'block' : 'none'};">
                        <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;">${summaryCards.map((item) => `<div style="background:#fafafa;border-radius:8px;padding:10px;"><div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.6px;">${item.label}</div><div style="font-size:16px;color:#1a1a1a;font-weight:500;margin-top:5px;">${item.value}</div></div>`).join('')}</div>
                        <div style="margin-top:12px;font-size:11px;color:#666;line-height:1.6;">${escapeHtml(statusConfig[scopeData.scopeAnalytics.status]?.description || '')}</div>
                    </div>
                    <div class="tab-pane ${state.activeVillageTab === 'rw' ? 'active' : ''}" style="display:${state.activeVillageTab === 'rw' ? 'block' : 'none'};">${renderRwTable(dptScopeData.rwRows)}</div>
                    <div class="tab-pane ${state.activeVillageTab === 'rt' ? 'active' : ''}" style="display:${state.activeVillageTab === 'rt' ? 'block' : 'none'};">${renderRtTable(dptScopeData.rtRows)}</div>
                    <div class="tab-pane ${state.activeVillageTab === 'demography' ? 'active' : ''}" style="display:${state.activeVillageTab === 'demography' ? 'block' : 'none'};">${renderVillageDemography(dptScopeData)}</div>
                    <div class="tab-pane ${state.activeVillageTab === 'program' ? 'active' : ''}" style="display:${state.activeVillageTab === 'program' ? 'block' : 'none'};">${renderProgramPane(program, dptScopeData)}</div>
                </div>`;
            }

            function renderRwTable(rows) {
                const limitedRows = rows.slice(0, 25);
                const note = `<div style="font-size:11px;color:#666;margin-bottom:10px;">Angka PKS pada tabel ini adalah estimasi hasil distribusi suara TPS ke level RW berdasarkan proporsi DPT wilayah.</div>`;
                const body = limitedRows.map((row) => `<tr><td style="padding:9px;border-bottom:0.5px solid #eee;">RW ${escapeHtml(row.rw)}</td><td style="padding:9px;border-bottom:0.5px solid #eee;">${escapeHtml(row.village)}</td><td style="padding:9px;border-bottom:0.5px solid #eee;">${formatNumber(row.totalDpt)}</td><td style="padding:9px;border-bottom:0.5px solid #eee;color:#fe5000;font-weight:500;">~${formatNumber(row.pksVotes)}</td><td style="padding:9px;border-bottom:0.5px solid #eee;">${formatPercent(row.share)}</td><td style="padding:9px;border-bottom:0.5px solid #eee;">${formatNumber(row.tpsSet.size)}</td><td style="padding:9px;border-bottom:0.5px solid #eee;">${renderStatusPill(row.status)}</td><td style="padding:9px;border-bottom:0.5px solid #eee;"><button type="button" data-detail-open="${escapeHtml(row.key)}" data-detail-type="rw" style="padding:5px 10px;border-radius:6px;border:0.5px solid #e5e5e5;background:white;cursor:pointer;">Buka</button></td></tr>`).join('');
                const footer = rows.length > limitedRows.length ? `<div style="margin-top:8px;font-size:11px;color:#666;">Tampilkan semua (${formatNumber(rows.length)} rows) belum diaktifkan, saat ini menampilkan 25 teratas.</div>` : '';
                return `${note}<div style="overflow:auto;border:0.5px solid #e5e5e5;border-radius:10px;"><table style="width:100%;border-collapse:collapse;background:white;"><thead><tr style="background:#fafafa;"><th style="padding:9px;text-align:left;font-size:11px;color:#666;font-weight:500;">RW</th><th style="padding:9px;text-align:left;font-size:11px;color:#666;font-weight:500;">Desa</th><th style="padding:9px;text-align:left;font-size:11px;color:#666;font-weight:500;">DPT Wilayah</th><th style="padding:9px;text-align:left;font-size:11px;color:#666;font-weight:500;">Estimasi PKS</th><th style="padding:9px;text-align:left;font-size:11px;color:#666;font-weight:500;">Share</th><th style="padding:9px;text-align:left;font-size:11px;color:#666;font-weight:500;">TPS Terlibat</th><th style="padding:9px;text-align:left;font-size:11px;color:#666;font-weight:500;">Status</th><th style="padding:9px;text-align:left;font-size:11px;color:#666;font-weight:500;">Detail</th></tr></thead><tbody>${body || '<tr><td colspan="8" style="padding:18px;text-align:center;font-size:12px;color:#888;">Belum ada data RW.</td></tr>'}</tbody></table></div>${footer}`;
            }

            function renderRtTable(rows) {
                const limitedRows = rows.slice(0, 30);
                const note = `<div style="font-size:11px;color:#666;margin-bottom:10px;">Urutan RT menggunakan estimasi kekuatan PKS dari distribusi suara TPS menurut proporsi DPT wilayah RT.</div>`;
                const body = limitedRows.map((row) => `<tr><td style="padding:9px;border-bottom:0.5px solid #eee;">RT ${escapeHtml(row.rt)} / RW ${escapeHtml(row.rw)}</td><td style="padding:9px;border-bottom:0.5px solid #eee;">${escapeHtml(row.village)}</td><td style="padding:9px;border-bottom:0.5px solid #eee;">${formatNumber(row.totalDpt)}</td><td style="padding:9px;border-bottom:0.5px solid #eee;color:#fe5000;font-weight:500;">~${formatNumber(row.pksVotes)}</td><td style="padding:9px;border-bottom:0.5px solid #eee;">${formatNumber(row.tpsSet.size)}</td><td style="padding:9px;border-bottom:0.5px solid #eee;">${renderStatusPill(row.status)}</td><td style="padding:9px;border-bottom:0.5px solid #eee;"><button type="button" data-detail-open="${escapeHtml(row.key)}" data-detail-type="rt" style="padding:5px 10px;border-radius:6px;border:0.5px solid #e5e5e5;background:white;cursor:pointer;">Buka</button></td></tr>`).join('');
                return `${note}<div style="overflow:auto;border:0.5px solid #e5e5e5;border-radius:10px;"><table style="width:100%;border-collapse:collapse;background:white;"><thead><tr style="background:#fafafa;"><th style="padding:9px;text-align:left;font-size:11px;color:#666;font-weight:500;">RT / RW</th><th style="padding:9px;text-align:left;font-size:11px;color:#666;font-weight:500;">Desa</th><th style="padding:9px;text-align:left;font-size:11px;color:#666;font-weight:500;">DPT Wilayah</th><th style="padding:9px;text-align:left;font-size:11px;color:#666;font-weight:500;">Estimasi PKS</th><th style="padding:9px;text-align:left;font-size:11px;color:#666;font-weight:500;">TPS</th><th style="padding:9px;text-align:left;font-size:11px;color:#666;font-weight:500;">Status</th><th style="padding:9px;text-align:left;font-size:11px;color:#666;font-weight:500;">Detail</th></tr></thead><tbody>${body || '<tr><td colspan="7" style="padding:18px;text-align:center;font-size:12px;color:#888;">Belum ada data RT.</td></tr>'}</tbody></table></div>`;
            }

            function renderVillageDemography(dptScopeData) {
                const total = Math.max(1, dptScopeData.totalDpt);
                const malePct = dptScopeData.totalMale / total;
                const femalePct = dptScopeData.totalFemale / total;
                const generation = [
                    { label: 'Gen Z', value: dptScopeData.generation.gen_z, color: '#a78bfa' },
                    { label: 'Millennial', value: dptScopeData.generation.millennial, color: '#fe5000' },
                    { label: 'Gen X', value: dptScopeData.generation.gen_x, color: '#16a34a' },
                    { label: 'Boomer', value: dptScopeData.generation.boomer, color: '#94a3b8' },
                ];
                return `<div style="display:grid;gap:14px;"><div><div style="font-size:11px;color:#666;margin-bottom:6px;">Komposisi gender</div><div style="height:8px;border-radius:999px;background:#f3f4f6;overflow:hidden;display:flex;"><span style="width:${malePct * 100}%;background:#2563eb;"></span><span style="width:${femalePct * 100}%;background:#ec4899;"></span></div><div style="margin-top:6px;font-size:11px;color:#666;display:flex;gap:12px;flex-wrap:wrap;"><span>Laki-laki ${formatPercent(malePct)}</span><span>Perempuan ${formatPercent(femalePct)}</span></div></div><div>${generation.map((item) => `<div style="margin-bottom:8px;"><div style="display:flex;justify-content:space-between;gap:10px;font-size:11px;color:#666;margin-bottom:4px;"><span>${item.label}</span><span>${formatPercent(item.value / total)}</span></div><div style="height:8px;border-radius:999px;background:#f3f4f6;overflow:hidden;"><div style="width:${(item.value / total) * 100}%;background:${item.color};height:100%;"></div></div></div>`).join('')}</div></div>`;
            }

            function renderProgramPane(program, dptScopeData) {
                return `<div style="display:grid;gap:12px;"><div style="background:#fafafa;border-radius:8px;padding:12px;"><div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.6px;">Karakter Wilayah</div><div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:4px;">${escapeHtml(program.territory)}</div><div style="font-size:11px;color:#666;margin-top:6px;">${escapeHtml(program.action)}</div></div><div style="display:grid;gap:8px;"><div style="font-size:11px;color:#666;">Fokus aksi: <strong style="color:#1a1a1a;">${escapeHtml(program.focus)}</strong></div><div style="font-size:11px;color:#666;">Pesan utama: ${escapeHtml(program.message)}</div><div style="font-size:11px;color:#666;">Format kegiatan: ${escapeHtml(program.activityFormat)}</div><div style="font-size:11px;color:#666;">Segmen sasaran: ${escapeHtml(program.segments.join(', ') || 'Umum')}</div></div><ol style="margin:0;padding-left:18px;font-size:12px;color:#444;display:grid;gap:6px;">${program.programs.map((item) => `<li>${escapeHtml(item)}</li>`).join('')}</ol><div style="font-size:11px;color:#888;">Skor prioritas program: ${formatNumber(program.score)} berbasis DPT wilayah.</div><div style="font-size:11px;color:#888;">TPS cocok: ${formatNumber(dptScopeData.matchedTps)} dari ${formatNumber(dptScopeData.totalScopeTps)} TPS.</div></div>`;
            }

            function renderDetailDrawer(dptScopeData, villageStatus, program) {
                if (!state.detailDrawer) {
                    closeDetailDrawer(true);
                    return;
                }

                const row = findDetailRow(dptScopeData, state.detailDrawer);
                if (!row) {
                    closeDetailDrawer(true);
                    return;
                }

                const status = statusConfig[row.status] || statusConfig['ZONA BERAT'];
                const total = Math.max(1, row.totalDpt);
                const topCandidate = row.topCandidate || getTopPksCandidate(row.partyMap);
                dom.detailDrawerTitle.textContent = row.type === 'rw' ? `RW ${row.rw} - ${row.village}` : `RT ${row.rt} / RW ${row.rw} - ${row.village}`;
                dom.detailDrawerSubtitle.textContent = `${row.district} | ${getTerritoryCharacter({ gen_z: row.gen_z, millennial: row.millennial, gen_x: row.gen_x, boomer: row.boomer }, row.male, row.female)} | ${formatNumber(row.tpsSet.size)} TPS`;
                dom.detailDrawerBadge.style.background = status.bg;
                dom.detailDrawerBadge.style.color = status.text;
                dom.detailDrawerBadge.querySelector('i').style.background = status.dot;
                dom.detailDrawerBadge.querySelector('span').textContent = status.label;
                dom.detailDrawerContent.innerHTML = `
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                        ${[
                            ['DPT Wilayah', formatNumber(row.totalDpt)],
                            ['Estimasi PKS', `~${formatNumber(row.pksVotes)}`],
                            ['Laki-laki', formatNumber(row.male)],
                            ['Perempuan', formatNumber(row.female)],
                            ['Share PKS', formatPercent(row.share)],
                            ['TPS Terlibat', formatNumber(row.tpsSet.size)],
                        ].map((item) => `<div style="background:#fafafa;border-radius:8px;padding:10px;"><div style="font-size:10px;color:#888;text-transform:uppercase;">${item[0]}</div><div style="font-size:16px;font-weight:500;color:#1a1a1a;margin-top:4px;">${item[1]}</div></div>`).join('')}
                    </div>
                    <div style="margin-top:16px;">
                        <div style="font-size:11px;color:#666;margin-bottom:6px;">Demografi</div>
                        <div style="height:8px;border-radius:999px;background:#f3f4f6;overflow:hidden;display:flex;"><span style="width:${(row.male / total) * 100}%;background:#2563eb;"></span><span style="width:${(row.female / total) * 100}%;background:#ec4899;"></span></div>
                        <div style="margin-top:6px;font-size:11px;color:#666;display:flex;gap:12px;flex-wrap:wrap;"><span>Laki-laki ${formatPercent(row.male / total)}</span><span>Perempuan ${formatPercent(row.female / total)}</span></div>
                        <div style="margin-top:10px;display:grid;gap:8px;">
                            ${[
                                ['Gen Z', row.gen_z, '#a78bfa'],
                                ['Millennial', row.millennial, '#fe5000'],
                                ['Gen X', row.gen_x, '#16a34a'],
                                ['Boomer', row.boomer, '#94a3b8'],
                            ].map((item) => `<div><div style="display:flex;justify-content:space-between;font-size:11px;color:#666;margin-bottom:4px;"><span>${item[0]}</span><span>${formatPercent(item[1] / total)}</span></div><div style="height:8px;border-radius:999px;background:#f3f4f6;overflow:hidden;"><div style="width:${(item[1] / total) * 100}%;background:${item[2]};height:100%;"></div></div></div>`).join('')}
                        </div>
                    </div>
                    <div style="margin-top:16px;display:grid;gap:8px;">
                        <div style="font-size:11px;color:#666;">Fokus aksi: <strong style="color:#1a1a1a;">${escapeHtml(program.focus)}</strong></div>
                        <div style="font-size:11px;color:#666;">Pesan utama: ${escapeHtml(program.message)}</div>
                        <ol style="margin:0;padding-left:18px;font-size:12px;color:#444;display:grid;gap:6px;">${program.programs.map((item) => `<li>${escapeHtml(item)}</li>`).join('')}</ol>
                    </div>
                    <div style="margin-top:16px;padding-top:12px;border-top:0.5px solid #e5e5e5;display:grid;gap:6px;">
                        <div style="font-size:11px;color:#666;">Top caleg PKS: <strong style="color:#1a1a1a;">${escapeHtml(topCandidate?.name || 'Belum ada data')}</strong></div>
                        <div style="font-size:11px;color:#666;">Skor prioritas program: ${formatNumber(program.score)}</div>
                        <div style="font-size:11px;color:#888;">Catatan: estimasi suara RW/RT dibangun dari distribusi proporsional DPT terhadap suara TPS.</div>
                    </div>`;
                dom.detailDrawer.classList.remove('hidden');
                dom.detailDrawerBackdrop.classList.remove('hidden');
                dom.detailDrawer.style.transform = 'translateX(0)';
            }

            function closeDetailDrawer(force = false) {
                if (!force) state.detailDrawer = null;
                dom.detailDrawer.classList.add('hidden');
                dom.detailDrawerBackdrop.classList.add('hidden');
                dom.detailDrawer.style.transform = 'translateX(100%)';
            }

            function findDetailRow(dptScopeData, detailState) {
                const source = detailState.type === 'rw' ? dptScopeData.rwRows : dptScopeData.rtRows;
                return source.find((row) => row.key === detailState.key) || null;
            }

            function buildProgramRecommendation({ statusKey, totalDpt, totalMale, totalFemale, generation }) {
                const territory = getTerritoryCharacter(generation, totalMale, totalFemale);
                let focus;
                let action;
                let programs;
                let message;
                let activityFormat = 'Kombinasi digital dan tatap muka';
                let segments = [];

                switch (statusKey) {
                    case 'JAGA KUAT':
                        focus = 'Pertahankan & Perkuat Basis';
                        action = 'Jaga loyalitas, aktifkan kader, tingkatkan militansi';
                        programs = ['Konsolidasi kader per RW', 'Silaturahmi rutin tokoh', 'Program bantuan sosial berkelanjutan'];
                        message = 'Wilayah ini sudah kuat, fokus menjaga agar tidak digerogoti';
                        break;
                    case 'AMANKAN':
                        focus = 'Amankan Margin & Perkuat Tokoh';
                        action = 'Pengamanan suara, penguatan tokoh lokal, jaga kader tetap solid';
                        programs = ['Pendataan pemilih loyal', 'Penguatan tokoh RT/RW', 'Monitoring ancaman kompetitor'];
                        message = 'Sudah unggul tapi margin tipis, jangan lengah';
                        break;
                    case 'REBUT REALISTIS':
                        focus = 'Rebut dengan Kerja Terfokus';
                        action = 'Target swing voters, kampanye intensif, mobilisasi maksimal';
                        programs = ['Identifikasi swing voters per RT', 'Door-to-door campaign terfokus', 'Program populis quick-win'];
                        message = 'Jarak tipis, bisa direbut jika kerja lebih rapat dari kompetitor';
                        break;
                    case 'GARAP INTENSIF':
                        focus = 'Garap Intensif & Konsisten';
                        action = 'Identifikasi tokoh lokal, bangun jaringan baru, program populis';
                        programs = ['Kerjasama tokoh masyarakat/RT-RW', 'Bakti sosial targeted', 'Diskusi warga rutin'];
                        message = 'Ada peluang tapi butuh kerja keras lapangan yang terukur';
                        break;
                    default:
                        focus = 'Bangun Fondasi';
                        action = 'Mulai dari nol: kenalkan partai, cari simpatisan awal';
                        programs = ['Pengenalan tokoh & program partai', 'Kegiatan keagamaan/sosial', 'Identifikasi potensi kader'];
                        message = 'Investasi jangka panjang, jangan target tinggi dulu';
                        break;
                }

                if (generation.millennial > generation.gen_x && generation.millennial > generation.boomer) {
                    segments.push('Millennial (dominan)');
                    activityFormat = 'Digital campaign + kegiatan komunitas modern';
                } else if (generation.gen_x > generation.millennial) {
                    segments.push('Gen X (dominan)');
                    activityFormat = 'Pertemuan warga + pendekatan personal';
                } else {
                    segments.push('Campuran usia');
                }

                if (totalMale > totalFemale * 1.1) segments.push('Mayoritas laki-laki');
                else if (totalFemale > totalMale * 1.1) segments.push('Mayoritas perempuan');

                return { territory, focus, action, programs, message, activityFormat, segments, score: totalDpt };
            }

            function getTerritoryCharacter(generation, totalMale, totalFemale) {
                if (generation.millennial > generation.gen_x && generation.millennial > generation.boomer) return 'Wilayah muda dan dinamis';
                if (generation.gen_x > generation.millennial) return 'Wilayah keluarga mapan';
                if (totalFemale > totalMale * 1.1) return 'Wilayah dengan basis perempuan kuat';
                if (totalMale > totalFemale * 1.1) return 'Wilayah dengan basis laki-laki dominan';
                return 'Wilayah campuran dengan karakter moderat';
            }

            function getTopPksCandidate(partyMap) {
                const analytics = analyzePks(partyMap, 0);
                return analytics.pksCandidates?.[0] || null;
            }

            function renderMap(visibleVillages) {
                const mapState = getMapState();
                const legend = Object.values(statusConfig).map((item) => `<div style="display:flex;align-items:center;gap:4px;"><span style="width:7px;height:7px;border-radius:50%;background:${item.dot};display:inline-block;"></span>${item.label}</div>`).join('');
                const markerHtml = mapState.markers.map((marker) => `<button type="button" data-map-marker="${escapeHtml(marker.key)}" title="${escapeHtml(marker.label)}" style="position:absolute;left:${marker.x}%;top:${marker.y}%;transform:translate(-50%,-50%);width:${marker.size}px;height:${marker.size}px;border-radius:50%;border:1.5px solid rgba(255,255,255,0.95);background:${marker.color};box-shadow:0 3px 8px rgba(0,0,0,0.18);cursor:pointer;"></button>`).join('');
                return `<div id="mapPanelCard" style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;height:100%;"><div style="margin-bottom:10px;"><div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Peta Wilayah</div><div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">${escapeHtml(mapState.title)}</div></div><div id="mapPanelMedia" style="position:relative;width:100%;flex:1 1 auto;min-height:280px;border-radius:8px;overflow:hidden;border:0.5px solid #d4d4d4;background:#e8efe0;"><img src="${escapeHtml(mapState.image)}" style="width:100%;height:100%;object-fit:contain;${mapState.hasImage ? '' : 'display:none;'}" alt="Peta" onerror="this.style.display='none'; this.parentElement.querySelector('[data-map-placeholder]').style.display='flex';"><div style="position:absolute;inset:0;">${markerHtml}</div><div data-map-placeholder style="position:absolute;inset:0;display:${mapState.hasImage ? 'none' : 'flex'};align-items:center;justify-content:center;color:#888;font-size:11px;"><div style="text-align:center;">Peta belum tersedia</div></div><div style="position:absolute;bottom:8px;left:8px;background:rgba(255,255,255,0.95);padding:5px 8px;border-radius:5px;font-size:9px;display:flex;gap:6px;border:0.5px solid #e5e5e5;flex-wrap:wrap;max-width:82%;">${legend}</div><div style="position:absolute;right:8px;bottom:8px;background:rgba(255,255,255,0.95);padding:5px 8px;border-radius:5px;font-size:9px;color:#666;border:0.5px solid #e5e5e5;">Ukuran = suara PKS</div></div></div>`;
            }

            function syncTopPanelHeights() {
                const mapPanelCard = document.getElementById('mapPanelCard');
                const mapPanelMedia = document.getElementById('mapPanelMedia');
                const partyRankingCard = document.getElementById('partyRankingCard');

                if (!mapPanelCard || !mapPanelMedia || !partyRankingCard) {
                    return;
                }

                if (window.innerWidth <= 960) {
                    mapPanelCard.style.height = '';
                    mapPanelMedia.style.height = '';
                    partyRankingCard.style.height = '';
                    return;
                }

                const targetHeight = Math.max(320, Math.min(partyRankingCard.offsetHeight, 360));
                const headerHeight = mapPanelCard.offsetHeight - mapPanelMedia.offsetHeight;
                partyRankingCard.style.height = `${targetHeight}px`;
                mapPanelCard.style.height = `${targetHeight}px`;
                mapPanelMedia.style.height = `${Math.max(220, targetHeight - headerHeight)}px`;
            }

            function renderDrilldownTable(rows, level) {
                dom.drilldownSectionLabel.textContent = level === 'kabupaten' ? 'Daftar Dapil' : (level === 'dapil' ? 'Daftar Kecamatan' : (level === 'kecamatan' ? 'Daftar Desa' : 'Daftar TPS'));
                dom.drilldownHeading.textContent = level === 'kabupaten'
                    ? `${formatNumber(rows.length)} Dapil Kabupaten Bekasi`
                    : (level === 'dapil'
                        ? `${formatNumber(rows.length)} Kecamatan di ${state.currentDapil}`
                        : (level === 'kecamatan'
                            ? `${formatNumber(rows.length)} Desa/Kelurahan di ${toTitleCase(state.currentKecamatan)}`
                            : `${formatNumber(rows.length)} TPS di ${findVillageByKey(state.currentDesa)?.label || 'desa terpilih'}`));
                const isClickable = level === 'kabupaten' || level === 'dapil' || level === 'kecamatan';
                const dptHeader = level === 'desa' ? 'DPT' : 'DPT';
                const dptCell = (row) => level === 'desa' && !row.totalDpt ? '-' : formatNumber(row.totalDpt);
                const body = rows.map((row) => `<tr data-drilldown-row="${isClickable ? escapeHtml(row.key) : ''}" style="cursor:${isClickable ? 'pointer' : 'default'};"><td style="padding:10px 12px;border-bottom:0.5px solid #eee;font-size:12px;color:#1a1a1a;font-weight:500;">${escapeHtml(row.name)}</td><td style="padding:10px 12px;border-bottom:0.5px solid #eee;font-size:12px;color:#444;">${dptCell(row)}</td><td style="padding:10px 12px;border-bottom:0.5px solid #eee;font-size:12px;color:#444;">${formatNumber(row.totalVotes)}</td><td style="padding:10px 12px;border-bottom:0.5px solid #eee;font-size:12px;color:#fe5000;font-weight:500;">${formatNumber(row.pksVotes)}</td><td style="padding:10px 12px;border-bottom:0.5px solid #eee;font-size:12px;color:#1a1a1a;font-weight:500;">${formatPercent(row.share)}</td><td style="padding:10px 12px;border-bottom:0.5px solid #eee;font-size:12px;">${renderStatusPill(row.status)}</td></tr>`).join('');
                const desaPanelRows = level === 'kecamatan'
                    ? buildDrilldownRows('kecamatan', (state.currentDapil ? [state.dataset.get(state.currentDapil)].filter(Boolean) : Array.from(state.dataset.values()))
                        .flatMap((entry) => Array.from(entry.desaMap.values()))
                        .filter((village) => !state.currentKecamatan || village.district === state.currentKecamatan)
                        .sort((a, b) => b.analytics.pksVotes - a.analytics.pksVotes || compareNatural(a.label, b.label)))
                    : [];
                const desaPanelCounts = desaPanelRows.reduce((totals, row) => {
                    totals.all += 1;
                    totals[row.status] = (totals[row.status] || 0) + 1;
                    return totals;
                }, { all: 0 });
                const desaStatusFilter = level === 'kecamatan'
                    ? `<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:10px;">
                            ${renderStatusFilterChip('', 'Semua', desaPanelCounts.all || 0, state.currentStatus === '', '#9ca3af')}
                            ${renderStatusFilterChip('JAGA KUAT', 'Jaga Kuat', desaPanelCounts['JAGA KUAT'] || 0, state.currentStatus === 'JAGA KUAT', '#15803d')}
                            ${renderStatusFilterChip('AMANKAN', 'Amankan', desaPanelCounts['AMANKAN'] || 0, state.currentStatus === 'AMANKAN', '#65a30d')}
                            ${renderStatusFilterChip('REBUT REALISTIS', 'Rebut Realistis', desaPanelCounts['REBUT REALISTIS'] || 0, state.currentStatus === 'REBUT REALISTIS', '#2563eb')}
                            ${renderStatusFilterChip('GARAP INTENSIF', 'Garap Intensif', desaPanelCounts['GARAP INTENSIF'] || 0, state.currentStatus === 'GARAP INTENSIF', '#d97706')}
                            ${renderStatusFilterChip('ZONA BERAT', 'Zona Berat', desaPanelCounts['ZONA BERAT'] || 0, state.currentStatus === 'ZONA BERAT', '#6b7280')}
                        </div>`
                    : '';
                dom.drilldownTableWrap.innerHTML = `${desaStatusFilter}<div style="overflow:auto;border:0.5px solid #e5e5e5;border-radius:10px;"><table style="width:100%;border-collapse:collapse;background:white;"><thead><tr style="background:#fafafa;"><th style="padding:10px 12px;text-align:left;font-size:11px;color:#666;font-weight:500;">Nama</th><th style="padding:10px 12px;text-align:left;font-size:11px;color:#666;font-weight:500;">${dptHeader}</th><th style="padding:10px 12px;text-align:left;font-size:11px;color:#666;font-weight:500;">Suara Sah</th><th style="padding:10px 12px;text-align:left;font-size:11px;color:#666;font-weight:500;">Suara PKS</th><th style="padding:10px 12px;text-align:left;font-size:11px;color:#666;font-weight:500;">% PKS</th><th style="padding:10px 12px;text-align:left;font-size:11px;color:#666;font-weight:500;">Status</th></tr></thead><tbody>${body || '<tr><td colspan="6" style="padding:18px;text-align:center;font-size:12px;color:#888;">Belum ada data.</td></tr>'}</tbody></table></div>`;
                if (level === 'kecamatan') {
                    dom.drilldownTableWrap.querySelectorAll('[data-desa-status-chip]').forEach((button) => button.addEventListener('click', () => {
                        const next = button.dataset.desaStatusChip || '';
                        state.currentStatus = state.currentStatus === next ? '' : next;
                        if (dom.statusSelect) {
                            dom.statusSelect.value = state.currentStatus;
                        }
                        render();
                    }));
                }
                if (level === 'kabupaten') dom.drilldownTableWrap.querySelectorAll('[data-drilldown-row]').forEach((row) => row.addEventListener('click', () => { const key = row.dataset.drilldownRow; if (!key) return; state.currentDapil = key; state.currentKecamatan = ''; dom.dapilSelect.value = key; render(); }));
                if (level === 'dapil') dom.drilldownTableWrap.querySelectorAll('[data-drilldown-row]').forEach((row) => row.addEventListener('click', () => { const key = row.dataset.drilldownRow; if (!key) return; state.currentKecamatan = key; state.currentDesa = ''; dom.kecamatanSelect.value = key; render(); }));
                if (level === 'kecamatan') dom.drilldownTableWrap.querySelectorAll('[data-drilldown-row]').forEach((row) => row.addEventListener('click', () => { const key = row.dataset.drilldownRow; if (!key) return; const village = findVillageByKey(key); if (village) selectVillage(village); }));
            }

            function renderCalegPanel(scopeData) {
                const rows = scopeData.scopeAnalytics.pksCandidates || [];
                const visibleRows = rows.slice(0, 5);
                return `<div id="calegPanelWrap" style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:grid;gap:12px;align-self:start;"><div><div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">CALEG PKS DAPIL ${escapeHtml(state.currentDapil.replace('BEKASI ', ''))}</div><div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:2px;"><div style="font-size:14px;color:#1a1a1a;font-weight:500;">Urutan Perolehan Suara 2024</div><div style="font-size:10px;color:${scopeData.seats > 0 ? '#14532d' : '#666'};background:${scopeData.seats > 0 ? '#dcfce7' : '#f5f5f5'};padding:4px 8px;border-radius:999px;">${formatNumber(scopeData.seats)} kursi diperoleh</div></div></div><div style="display:grid;gap:8px;">${visibleRows.length ? visibleRows.map((row, index) => renderCalegRow(row, index)).join('') : '<div style="font-size:12px;color:#888;">Belum ada data caleg PKS.</div>'}</div>${rows.length > 5 ? `<div style="font-size:10px;color:#666;">+ ${formatNumber(rows.length - 5)} caleg lainnya</div>` : ''}<div style="padding-top:8px;border-top:0.5px solid #e5e5e5;display:flex;align-items:center;justify-content:space-between;gap:10px;"><div style="font-size:11px;color:#666;">Total suara PKS dapil</div><div style="font-size:14px;font-weight:500;color:#fe5000;">${formatNumber(scopeData.scopeAnalytics.pksVotes)}</div></div></div>`;
            }
            function renderCalegRow(row, index) {
                const rank = index + 1;
                return rank === 1
                    ? `<div style="display:flex;align-items:center;gap:8px;padding:8px;background:#fff7f1;border-radius:7px;border:0.5px solid #fce4ce;"><div style="width:24px;height:24px;border-radius:50%;background:#fe5000;color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:500;">1</div><div style="flex:1;"><div style="font-size:12px;font-weight:500;color:#1a1a1a;">${escapeHtml(row.name)}</div><div style="font-size:10px;color:#666;">Top suara PKS</div></div><div style="text-align:right;"><div style="font-size:13px;font-weight:500;color:#1a1a1a;">${formatNumber(row.votes)}</div><div style="font-size:9px;color:#16a34a;font-weight:500;">TERATAS</div></div></div>`
                    : `<div style="display:flex;align-items:center;gap:8px;padding:7px 8px;border-radius:7px;border:0.5px solid #e5e5e5;"><div style="width:22px;height:22px;border-radius:50%;background:#f5f5f5;color:#666;display:flex;align-items:center;justify-content:center;font-size:10px;">${rank}</div><div style="flex:1;"><div style="font-size:12px;font-weight:500;color:#1a1a1a;">${escapeHtml(row.name)}</div><div style="font-size:10px;color:#666;">Peringkat ${rank}</div></div><div style="font-size:12px;font-weight:500;color:#1a1a1a;">${formatNumber(row.votes)}</div></div>`;
            }

            function getMapState() {
                const level = state.currentKecamatan ? 'kecamatan' : (state.currentDapil ? 'dapil' : 'kabupaten');
                if (level === 'kabupaten') return { title: 'Kabupaten Bekasi', image: '/images/peta/kabupaten-bekasi.png', hasImage: true, markers: [] };
                if (level === 'kecamatan') {
                    const config = kecamatanMapConfigs[state.currentKecamatan];
                    const fallbackImage = `/images/peta/kecamatan/${slugifyLabel(state.currentKecamatan)}.png`;
                    if (!config) return { title: `Kecamatan ${toTitleCase(state.currentKecamatan)}`, image: fallbackImage, hasImage: true, markers: [] };
                    const visibleVillages = getVisibleVillages();
                    const points = new Map(config.villages.map((point) => [normalizeKey(point.name), point]));
                    const maxVotes = Math.max(1, ...visibleVillages.map((village) => village.analytics.pksVotes));
                    const markers = visibleVillages.map((village) => {
                        const point = points.get(village.village);
                        if (!point) return null;
                        return {
                            key: village.key,
                            label: `${village.label} · ${formatNumber(village.analytics.pksVotes)} suara PKS`,
                            x: point.x,
                            y: point.y,
                            size: 10 + Math.round((village.analytics.pksVotes / maxVotes) * 18),
                            color: statusConfig[village.analytics.status].dot,
                        };
                    }).filter(Boolean);
                    return { title: `Kecamatan ${toTitleCase(state.currentKecamatan)}`, image: config.image || fallbackImage, hasImage: true, markers };
                }
                const config = mapConfigs[state.currentDapil];
                if (!config) return { title: `Dapil ${state.currentDapil.replace('BEKASI ', '')}`, image: `/images/peta/dapil${state.currentDapil.replace('BEKASI ', '')}.png`, hasImage: true, markers: [] };
                const visibleVillages = getVisibleVillages();
                const points = new Map(config.villages.map((point) => [`${normalizeKey(point.name)}|${normalizeKey(point.district)}`, point]));
                const maxVotes = Math.max(1, ...visibleVillages.map((village) => village.analytics.pksVotes));
                const markers = visibleVillages.map((village) => { const point = points.get(`${village.village}|${village.district}`); if (!point) return null; return { key: village.key, label: `${village.label} · ${formatNumber(village.analytics.pksVotes)} suara PKS`, x: point.x, y: point.y, size: 10 + Math.round((village.analytics.pksVotes / maxVotes) * 18), color: statusConfig[village.analytics.status].dot }; }).filter(Boolean);
                return { title: `Dapil ${state.currentDapil.replace('BEKASI ', '')}`, image: config.image, hasImage: true, markers };
            }

            function populateDapilOptions() {
                dom.dapilSelect.innerHTML = Array.from(state.dataset.keys()).sort(compareNatural).map((dapil) => `<option value="${escapeHtml(dapil)}">Dapil ${escapeHtml(dapil.replace('BEKASI ', ''))}</option>`).join('');
                dom.dapilSelect.value = state.currentDapil;
            }
            function populateKecamatanOptions() {
                const districts = state.currentDapil ? Array.from(state.dataset.get(state.currentDapil)?.kecamatanMap.keys() || []) : Array.from(new Set(Array.from(state.dataset.values()).flatMap((dapilObj) => Array.from(dapilObj.kecamatanMap.keys()))));
                dom.kecamatanSelect.innerHTML = ['<option value="">Semua kecamatan</option>', ...districts.sort(compareNatural).map((district) => `<option value="${escapeHtml(district)}">${escapeHtml(toTitleCase(district))}</option>`)].join('');
                dom.kecamatanSelect.value = state.currentKecamatan;
            }
            function populateDesaOptions() {
                const villages = getVisibleVillagesForDesaFilter();
                dom.desaSelect.innerHTML = ['<option value="">Semua desa</option>', ...villages.map((village) => `<option value="${escapeHtml(village.key)}">${escapeHtml(village.label)}</option>`)].join('');
                dom.desaSelect.value = state.currentDesa;
            }
            function getVisibleVillagesForDesaFilter() {
                const dapilObjects = state.currentDapil ? [state.dataset.get(state.currentDapil)].filter(Boolean) : Array.from(state.dataset.values());
                let villages = dapilObjects.flatMap((entry) => Array.from(entry.desaMap.values()));
                if (state.currentKecamatan) {
                    villages = villages.filter((village) => village.district === state.currentKecamatan);
                }

                return villages.sort((a, b) => compareNatural(a.label, b.label));
            }

            function getDptVillage(village) { return village.precomputedDpt || state.dptDatasets[village.dapil]?.get(village.key) || null; }
            function findVillageByKey(key) { for (const dapilObj of state.dataset.values()) { if (dapilObj.desaMap.has(key)) return dapilObj.desaMap.get(key); } return null; }
            function handleTpsUpload(event) { const file = event.target.files?.[0]; if (!file) return; file.text().then((text) => { state.dataset = buildDataset(parseSemicolonCsv(text)); dom.sourceStatus.textContent = 'TPS upload berhasil.'; dom.sourceStatus.style.color = '#166534'; render(); }); }
            function handleDptUpload(event) { const file = event.target.files?.[0]; if (!file) return; file.text().then((text) => { const rows = parseCsvAuto(text); const byDapil = rows.reduce((acc, row) => { const dapil = resolveLatestDapil(row.dapil, row.kecamatan); acc[dapil] = acc[dapil] || []; acc[dapil].push(row); return acc; }, {}); Object.entries(byDapil).forEach(([dapil, entries]) => { state.dptDatasets[dapil] = buildDptDataset(entries); }); dom.dptStatus.textContent = `DPT upload berhasil untuk ${Object.keys(byDapil).join(', ')}`; dom.dptStatus.style.color = '#166534'; render(); }); }
            function createPartyEntry(partyId, partyName) { return { partyId: String(partyId || '').trim(), partyName: String(partyName || '').trim(), partyVotes: 0, candidateVotes: 0, candidates: new Map() }; }
            function mergePartyMaps(target, source) { source.forEach((entry, key) => { const targetEntry = getOrCreate(target, key, () => createPartyEntry(entry.partyId, entry.partyName)); targetEntry.partyVotes += entry.partyVotes; targetEntry.candidateVotes += entry.candidateVotes; entry.candidates.forEach((votes, name) => targetEntry.candidates.set(name, (targetEntry.candidates.get(name) || 0) + votes)); }); }
            function incrementAgeBucket(target, age) { if (!Number.isFinite(age) || age <= 0) { target.age_unknown += 1; return; } if (age <= 27) target.gen_z += 1; else if (age <= 43) target.millennial += 1; else if (age <= 59) target.gen_x += 1; else target.boomer += 1; }
            function renderSummaryCard({ label, value, subtext, icon, invert = false }) { const labelColor = invert ? 'rgba(255,255,255,0.78)' : '#666'; const valueColor = invert ? '#fff' : '#1a1a1a'; const subColor = invert ? 'rgba(255,255,255,0.82)' : '#888'; return `<div style="display:flex;justify-content:space-between;gap:10px;align-items:flex-start;"><div><div style="font-size:10px;color:${labelColor};margin-bottom:6px;">${escapeHtml(label)}</div><div style="font-size:20px;font-weight:500;color:${valueColor};line-height:1.05;">${escapeHtml(value)}</div><div style="font-size:10px;color:${subColor};margin-top:4px;">${escapeHtml(subtext)}</div></div><div style="font-size:16px;line-height:1;color:${invert ? '#fff' : '#fe5000'};">${icon}</div></div>`; }
            function renderStatusFilterChip(value, label, count, active, dotColor) { return `<button type="button" data-desa-status-chip="${escapeHtml(value)}" style="display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:999px;border:0.5px solid ${active ? '#d1d5db' : '#e5e7eb'};background:${active ? '#f3f4f6' : '#fafafa'};color:#374151;font-size:12px;cursor:pointer;white-space:nowrap;"><span style="display:inline-flex;align-items:center;gap:6px;"><span style="width:8px;height:8px;border-radius:50%;background:${dotColor};display:inline-block;"></span>${escapeHtml(label)}</span><span style="font-size:11px;color:#6b7280;">${formatNumber(count)}</span></button>`; }
            function renderStatusPill(status) { const config = statusConfig[status] || statusConfig['ZONA BERAT']; return `<span style="display:inline-flex;align-items:center;gap:6px;padding:4px 8px;border-radius:999px;background:${config.bg};color:${config.text};font-size:10px;font-weight:500;"><span style="width:6px;height:6px;border-radius:50%;background:${config.dot};display:inline-block;"></span>${config.label}</span>`; }
            function estimateSeats(pksVotes, totalVotes, totalSeats) { if (!totalVotes) return 0; return Math.max(0, Math.round((pksVotes / totalVotes) * totalSeats)); }
            function normalizePartyName(name) { return String(name || '').trim() === 'NasDem' ? 'Nasdem' : String(name || '').trim(); }
            function selectVillage(village) { state.currentDapil = village.dapil; state.currentKecamatan = village.district; state.currentDesa = village.key; state.activeVillageTab = 'summary'; state.detailDrawer = null; dom.dapilSelect.value = village.dapil; dom.kecamatanSelect.value = village.district; if (dom.desaSelect) dom.desaSelect.value = village.key; render(); }
            function resetScope() { state.currentDapil = DEFAULT_FOCUS_DAPIL; state.currentKecamatan = DEFAULT_FOCUS_KECAMATAN; state.currentDesa = ''; state.activeVillageTab = 'summary'; state.detailDrawer = null; render(); }
            window.resetScope = resetScope;
            function slugifyLabel(value) { return String(value || '').toLowerCase().replace(/\s+/g, '-'); }
            function formatRwRt(value) { const raw = String(value ?? '').trim(); if (!raw) return '-'; const number = Number(raw); return Number.isFinite(number) ? String(number).padStart(3, '0') : raw; }
            function formatNumber(value) { return new Intl.NumberFormat('id-ID').format(Math.round(Number(value) || 0)); }
            function formatCompactNumber(value) { const number = Number(value) || 0; if (number >= 1000000) return `${(number / 1000000).toFixed(1)}M`; if (number >= 1000) return `${(number / 1000).toFixed(1)}K`; return formatNumber(number); }
            function formatPercent(value) { return `${((Number(value) || 0) * 100).toFixed(1)}%`; }
            function currentPeriodLabel() { return compiledPayload?.period?.label || 'Pemilu DPRD'; }
            function numberValue(value) { const cleaned = String(value ?? '').replace(/[^\d.-]/g, ''); const result = Number(cleaned); return Number.isFinite(result) ? result : 0; }
            function toTitleCase(value) { return String(value ?? '').toLowerCase().replace(/\b\w/g, (char) => char.toUpperCase()); }
            function compareNatural(a, b) { return String(a).localeCompare(String(b), 'id-ID', { numeric: true, sensitivity: 'base' }); }
            function getOrCreate(map, key, factory) { if (!map.has(key)) map.set(key, factory()); return map.get(key); }
            function escapeHtml(value) { return String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', '\'': '&#039;' }[char])); }
        </script>
    </flux:main>
</x-layouts.app.sidebar>
