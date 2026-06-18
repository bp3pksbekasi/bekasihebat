<x-layouts.app.sidebar>
    <flux:main class="!p-0">
        <div style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;box-sizing:border-box;">
            <div style="width:100%;margin:0;box-sizing:border-box;">
                <div style="background:#1a1a1a;color:white;padding:12px 20px;display:flex;align-items:center;justify-content:flex-start;border-radius:14px 14px 0 0;gap:16px;flex-wrap:nowrap;overflow:hidden;">
                    <div style="display:flex;align-items:center;gap:18px;flex-wrap:nowrap;flex:1 1 auto;min-width:0;overflow:hidden;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:28px;height:28px;background:#fe5000;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:14px;">&#127919;</div>
                            <div style="font-weight:500;font-size:14px;">Bedah Dapil</div>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:nowrap;min-width:0;">
                            <div style="font-size:12px;color:#d4d4d8;font-weight:500;">Filter :</div>
                            <select id="dapilSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#fff7f1;color:#993c1d;font-weight:500;min-width:130px;">
                                <option value="">Semua dapil</option>
                            </select>
                            <select id="kecamatanSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;min-width:140px;">
                                <option value="">Semua kecamatan</option>
                            </select>
                            <select id="desaSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;min-width:130px;">
                                <option value="">Semua desa</option>
                            </select>
                            <select id="partaiSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;min-width:140px;">
                                <option value="">Semua partai</option>
                            </select>
                            <select id="genderSelect" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;min-width:130px;">
                                <option value="">Semua gender</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <input id="searchInput" type="text" placeholder="Cari nama caleg..." style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;width:190px;min-width:190px;">
                        </div>
                    </div>
                    <div style="width:26px;height:26px;background:#fe5000;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:500;flex:0 0 auto;">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                </div>

                <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;overflow:hidden;">
                    <div style="display:none;">
                        <input type="file" id="csvFileInput" accept=".csv">
                        <div id="sourceStatus">Menunggu auto-load data caleg...</div>
                    </div>

                    <div style="padding:20px 20px 0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                            <h1 id="pageHeading" style="font-size:20px;font-weight:500;margin:0;color:#1a1a1a;">Kabupaten Bekasi</h1>
                            <div id="pageSubheading" style="font-size:12px;color:#666;">Analisa caleg · 7 dapil</div>
                        </div>
                        <div id="pageMeta" style="display:flex;align-items:center;justify-content:flex-end;gap:6px;font-size:11px;color:#888;flex-wrap:wrap;text-align:right;">Fokus: ranking suara, sebaran PKS, head-to-head, dan analisa gender.</div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin:18px 0;padding:0 20px;" class="summary-grid">
                        <div id="cardTotalCaleg" style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;"></div>
                        <div id="cardTotalSuara" style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;"></div>
                        <div id="cardPksCaleg" style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:10px;padding:14px;color:white;"></div>
                        <div id="cardPksSuara" style="background:linear-gradient(135deg,#fe5000,#d94400);border-radius:10px;padding:14px;color:white;"></div>
                        <div id="cardRataSuara" style="background:white;border:0.5px solid #e5e5e5;border-radius:10px;padding:14px;"></div>
                    </div>

                    <div class="kaderisasi-3col-grid" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;padding:0 20px 20px;box-sizing:border-box;">
                        <!-- Column 1: Map Card -->
                        <div id="inlineMapWrap" style="box-sizing:border-box;height:100%;"></div>

                        <!-- Column 2: Ranking Caleg -->
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;gap:12px;box-sizing:border-box;height:100%;">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:4px;flex:0 0 auto;">
                                <div>
                                    <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Ranking Caleg</div>
                                    <div id="rankingTitle" style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Top 15 peraih suara tertinggi</div>
                                </div>
                                <div style="font-size:11px;color:#888;">Klik nama untuk lihat detail</div>
                            </div>
                            <div id="calegRankingWrap" style="display:grid;gap:6px;flex:1 1 auto;overflow-y:auto;max-height:430px;padding-right:4px;"></div>
                        </div>

                        <!-- Column 3: Caleg PKS Sebaran -->
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;gap:12px;box-sizing:border-box;height:100%;">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:4px;flex:0 0 auto;">
                                <div>
                                    <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Caleg PKS</div>
                                    <div id="pksSebaranTitle" style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Sebaran suara per kecamatan</div>
                                </div>
                                <div style="font-size:11px;color:#888;">Basis kuat per caleg</div>
                            </div>
                            <div id="pksSebaranWrap" style="display:grid;gap:12px;flex:1 1 auto;overflow-y:auto;max-height:430px;padding-right:4px;"></div>
                        </div>
                    </div>

                    <div style="display:none;" id="genderAnalysisWrap"></div>

                    <div style="display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:14px;padding:0 20px 20px;align-items:stretch;box-sizing:border-box;height:490px;" class="double-grid caleg-row-two">
                        <!-- Column 1: Head-to-Head -->
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;height:490px;overflow:hidden;" class="panel-row-two">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px;flex:0 0 auto;">
                                <div>
                                    <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Head-to-Head</div>
                                    <div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">PKS vs kompetitor per desa</div>
                                    <div style="font-size:11px;color:#888;margin-top:3px;">Perbandingan suara PKS dengan dua partai terbesar di tiap desa</div>
                                </div>
                                <button id="toggleHeadToHeadBtn" type="button" style="padding:5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:11px;background:white;color:#444;cursor:pointer;display:none;">Tampilkan semua</button>
                            </div>
                            <div id="headToHeadWrap" style="flex:1 1 auto;overflow-y:auto;min-height:0;padding-right:4px;display:flex;flex-direction:column;"></div>
                        </div>

                        <!-- Column 2: Tabel Caleg Lengkap -->
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;height:490px;overflow:hidden;" class="panel-row-two">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px;flex-wrap:wrap;flex:0 0 auto;">
                                <div>
                                    <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Tabel Caleg Lengkap</div>
                                    <div id="fullTableTitle" style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Semua caleg</div>
                                </div>
                                <button id="exportCsvBtn" type="button" style="padding:6px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:11px;background:white;color:#444;cursor:pointer;">Ekspor CSV</button>
                            </div>
                            <div id="fullTableWrap" style="flex:1 1 auto;overflow-y:auto;min-height:0;padding-right:4px;display:flex;flex-direction:column;"></div>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:14px;padding:0 20px 20px;align-items:stretch;box-sizing:border-box;height:490px;" class="double-grid caleg-row-three">
                        <!-- Column 1: Sebaran per RW -->
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;height:490px;overflow:hidden;" class="panel-row-two">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px;flex:0 0 auto;">
                                <div>
                                    <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Caleg PKS per RW</div>
                                    <div id="pksRwTitle" style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">Sebaran suara per RW</div>
                                    <div style="font-size:11px;color:#888;margin-top:3px;">Top 5 Caleg PKS dengan perolehan tertinggi di tiap RW</div>
                                </div>
                            </div>
                            <div id="pksRwWrap" style="flex:1 1 auto;overflow-y:auto;min-height:0;padding-right:4px;display:flex;flex-direction:column;gap:12px;"></div>
                        </div>

                        <!-- Column 2: Head-to-Head per RW -->
                        <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;box-sizing:border-box;height:490px;overflow:hidden;" class="panel-row-two">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px;flex:0 0 auto;">
                                <div>
                                    <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Head-to-Head per RW</div>
                                    <div id="h2hRwTitle" style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">PKS vs kompetitor per RW</div>
                                    <div style="font-size:11px;color:#888;margin-top:3px;">Perbandingan suara PKS dengan dua partai terbesar di tiap RW</div>
                                </div>
                                <button id="toggleH2hRwBtn" type="button" style="padding:5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:11px;background:white;color:#444;cursor:pointer;display:none;">Tampilkan semua</button>
                            </div>
                            <div id="h2hRwWrap" style="flex:1 1 auto;overflow-y:auto;min-height:0;padding-right:4px;display:flex;flex-direction:column;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .caleg-stat-label { font-size: 11px; color: #666; text-transform: uppercase; letter-spacing: 0.6px; }
            .caleg-stat-value { font-size: 22px; color: #1a1a1a; font-weight: 500; line-height: 1.1; margin-top: 10px; }
            .caleg-stat-value.is-light { color: white; }
            .caleg-stat-sub { font-size: 10px; color: #888; margin-top: 6px; line-height: 1.4; }
            .caleg-stat-sub.is-light { color: rgba(255,255,255,0.88); }
            .sort-btn { display: inline-flex; align-items: center; gap: 4px; cursor: pointer; user-select: none; }
            .drawer-hidden { transform: translateX(100%); }
            .drawer-visible { transform: translateX(0); }
            @media (min-width: 1025px) {
                .kaderisasi-3col-grid {
                    height: 490px;
                }
                .kaderisasi-3col-grid > div {
                    height: 100%;
                    overflow: hidden;
                }
                #mapPanelCard {
                    height: 100% !important;
                }
                #mapPanelMedia {
                    height: 330px !important;
                    flex: none !important;
                }
                .caleg-row-two, .caleg-row-three {
                    height: 490px;
                }
                .panel-row-two {
                    height: 100% !important;
                }
            }
            @media (max-width: 1024px) {
                .kaderisasi-3col-grid {
                    grid-template-columns: minmax(0, 1fr) !important;
                    height: auto !important;
                }
                .kaderisasi-3col-grid > div {
                    height: auto !important;
                }
                #mapPanelMedia {
                    height: 320px !important;
                    flex: none !important;
                }
                .caleg-row-two, .caleg-row-three {
                    grid-template-columns: minmax(0, 1fr) !important;
                    height: auto !important;
                }
                .panel-row-two {
                    height: auto !important;
                }
            }
            @media (max-width: 1280px) {
                .summary-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
            }
            @media (max-width: 980px) {
                .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
                .double-grid { grid-template-columns: minmax(0, 1fr) !important; }
            }
            @media (max-width: 680px) {
                .summary-grid { grid-template-columns: minmax(0, 1fr) !important; }
            }
        </style>

        <div id="calegDrawerBackdrop" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:49;"></div>
        <div id="calegDrawer" class="hidden drawer-hidden" style="position:fixed;top:0;right:0;width:440px;max-width:100vw;height:100vh;background:white;box-shadow:-4px 0 20px rgba(0,0,0,0.1);z-index:50;overflow-y:auto;transition:transform 0.2s;">
            <div style="padding:16px 20px;border-bottom:0.5px solid #e5e5e5;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                <div>
                    <div id="drawerTitle" style="font-size:15px;font-weight:500;color:#1a1a1a;">Detail Caleg</div>
                    <div id="drawerSubtitle" style="font-size:11px;color:#888;margin-top:3px;">-</div>
                </div>
                <button id="drawerCloseBtn" type="button" style="width:28px;height:28px;border-radius:6px;border:0.5px solid #e5e5e5;background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;">&#10005;</button>
            </div>
            <div id="drawerContent" style="padding:16px 20px;"></div>
        </div>

        <script>
            const compiledPayload = @json($compiledPayload);
            (function() {
                const partyColors = {
                "PKB": "#008000", "Gerindra": "#C8102E", "PDIP": "#D72027", "PDI-P": "#D72027",
                "Golkar": "#FFD700", "Nasdem": "#003087", "NasDem": "#003087", "Buruh": "#E31937",
                "Gelora": "#DC143C", "PKS": "#fe5000", "PKN": "#336699", "Hanura": "#4169E1",
                "Garuda": "#228B22", "PAN": "#005BAC", "PBB": "#009B3A", "Demokrat": "#00529C",
                "PSI": "#EC008C", "Perindo": "#CC0000", "PPP": "#006600", "Ummat": "#2E8B57"
            };

            const tpsToRwMap = new Map();
            if (window.compiledPayload && window.compiledPayload.villages) {
                window.compiledPayload.villages.forEach((v) => {
                    const villageKey = `${normalizeKey(v.dapil)}__${normalizeKey(v.kecamatan)}__${normalizeKey(v.desa)}`;
                    const tpsMap = new Map();
                    if (v.rw_rows) {
                        v.rw_rows.forEach((rwRow) => {
                            if (rwRow.tps_list) {
                                rwRow.tps_list.forEach((tpsName) => {
                                    const num = String(tpsName || '').replace(/[^\d]/g, '');
                                    tpsMap.set(num, rwRow.rw);
                                });
                            }
                        });
                    }
                    tpsToRwMap.set(villageKey, tpsMap);
                });
            }

            const state = {
                dataset: null,
                currentDapil: '',
                currentKecamatan: '',
                currentDesa: '',
                currentPartai: '',
                currentGender: '',
                searchKeyword: '',
                searchDebounceId: null,
                currentSort: { column: 'suara', direction: 'desc' },
                currentPage: 1,
                selectedCalegKey: '',
                showAllHeadToHead: false,
                showAllHeadToHeadRw: false,
            };

            const dom = {};

            let initializedForThisScope = false;
            async function init() {
                if (initializedForThisScope) return;
                if (!document.getElementById('calegRankingWrap')) return;
                initializedForThisScope = true;
                cacheDom();
                bindEvents();
                await autoLoadData();
            }
             function cacheDom() {
                [
                    'dapilSelect', 'kecamatanSelect', 'desaSelect', 'partaiSelect', 'genderSelect', 'searchInput', 'csvFileInput', 'sourceStatus',
                    'pageHeading', 'pageSubheading', 'pageMeta', 'rankingTitle', 'pksSebaranTitle', 'toggleHeadToHeadBtn', 'fullTableTitle',
                    'cardTotalCaleg', 'cardTotalSuara', 'cardPksCaleg', 'cardPksSuara', 'cardRataSuara',
                    'calegRankingWrap', 'pksSebaranWrap', 'headToHeadWrap', 'genderAnalysisWrap', 'fullTableWrap',
                    'exportCsvBtn', 'calegDrawerBackdrop', 'calegDrawer', 'drawerTitle', 'drawerSubtitle', 'drawerContent', 'drawerCloseBtn',
                    'inlineMapWrap',
                    'pksRwTitle', 'pksRwWrap', 'h2hRwTitle', 'h2hRwWrap', 'toggleH2hRwBtn'
                ].forEach((id) => { dom[id] = document.getElementById(id); });
            }

            function bindEvents() {
                dom.dapilSelect.addEventListener('change', () => {
                    state.currentDapil = dom.dapilSelect.value;
                    state.currentKecamatan = '';
                    state.currentDesa = '';
                    state.currentPage = 1;
                    state.showAllHeadToHead = false;
                    state.showAllHeadToHeadRw = false;
                    populateKecamatanOptions();
                    populateDesaOptions();
                    render();
                });
                dom.kecamatanSelect.addEventListener('change', () => {
                    state.currentKecamatan = dom.kecamatanSelect.value;
                    state.currentDesa = '';
                    state.currentPage = 1;
                    populateDesaOptions();
                    render();
                });
                dom.desaSelect.addEventListener('change', () => {
                    state.currentDesa = dom.desaSelect.value;
                    state.currentPage = 1;
                    render();
                });
                dom.partaiSelect.addEventListener('change', () => {
                    state.currentPartai = dom.partaiSelect.value;
                    state.currentPage = 1;
                    render();
                });
                dom.genderSelect.addEventListener('change', () => {
                    state.currentGender = dom.genderSelect.value;
                    state.currentPage = 1;
                    render();
                });
                dom.searchInput.addEventListener('input', (event) => {
                    clearTimeout(state.searchDebounceId);
                    state.searchDebounceId = window.setTimeout(() => {
                        state.searchKeyword = event.target.value.trim();
                        state.currentPage = 1;
                        render();
                    }, 300);
                });
                dom.csvFileInput.addEventListener('change', handleManualUpload);
                dom.exportCsvBtn.addEventListener('click', exportVisibleCsv);
                dom.drawerCloseBtn.addEventListener('click', closeCalegDrawer);
                dom.calegDrawerBackdrop.addEventListener('click', closeCalegDrawer);
                dom.toggleHeadToHeadBtn.addEventListener('click', () => {
                    state.showAllHeadToHead = !state.showAllHeadToHead;
                    render();
                });
                dom.toggleH2hRwBtn.addEventListener('click', () => {
                    state.showAllHeadToHeadRw = !state.showAllHeadToHeadRw;
                    render();
                });
            }

            async function autoLoadData() {
                dom.sourceStatus.textContent = 'Memuat /data/pemilu/tps_dprd.csv...';
                try {
                    const response = await fetch('/data/pemilu/tps_dprd.csv');
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    const text = await response.text();
                    const rows = parseSemicolonCsv(text);
                    state.dataset = buildCalegDataset(rows);
                    dom.sourceStatus.textContent = `Auto-load berhasil: ${formatNumber(state.dataset.totalRows)} baris caleg dibaca.`;
                    populateFilters();
                    render();
                } catch (error) {
                    dom.sourceStatus.textContent = `Gagal auto-load: ${error.message}`;
                }
            }

            async function handleManualUpload(event) {
                const [file] = event.target.files || [];
                if (!file) return;
                const text = await file.text();
                const rows = parseSemicolonCsv(text);
                state.dataset = buildCalegDataset(rows);
                dom.sourceStatus.textContent = `File manual dimuat: ${file.name} (${formatNumber(state.dataset.totalRows)} baris caleg).`;
                populateFilters();
                render();
            }

            function parseSemicolonCsv(text) {
                const normalized = String(text || '').replace(/^\uFEFF/, '').trim();
                if (!normalized) return [];
                const lines = normalized.split(/\r?\n/).filter(Boolean);
                if (!lines.length) return [];
                const headers = splitCsvLine(lines.shift(), ';').map((header) => normalizeHeader(header));
                return lines.map((line) => {
                    const values = splitCsvLine(line, ';');
                    return headers.reduce((acc, header, index) => {
                        acc[header] = String(values[index] ?? '').trim();
                        return acc;
                    }, {});
                });
            }

            function splitCsvLine(line, delimiter) {
                const output = [];
                let current = '';
                let inQuotes = false;
                for (let i = 0; i < line.length; i += 1) {
                    const char = line[i];
                    const next = line[i + 1];
                    if (char === '"') {
                        if (inQuotes && next === '"') {
                            current += '"';
                            i += 1;
                        } else {
                            inQuotes = !inQuotes;
                        }
                    } else if (char === delimiter && !inQuotes) {
                        output.push(current);
                        current = '';
                    } else {
                        current += char;
                    }
                }
                output.push(current);
                return output;
            }

            function normalizeHeader(header) {
                return String(header || '').trim().toLowerCase().replace(/\s+/g, '_');
            }

            function normalizeKey(value) {
                return String(value || '')
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-zA-Z0-9]+/g, ' ')
                    .trim()
                    .toUpperCase();
            }

            function resolveLatestDapil(rawDapil) {
                return normalizeKey(rawDapil);
            }
             function buildCalegDataset(rows) {
                const dataset = { dapils: new Map(), totalRows: 0, allPartyNames: new Set() };
                rows.forEach((row) => {
                    const nomorUrut = Number(row.nomor_urut || 0);
                    if (!Number.isFinite(nomorUrut) || nomorUrut <= 0) return;
                    const dapil = resolveLatestDapil(row.dapil);
                    const partaiId = String(row.partai_id || '').trim();
                    const partai = normalizePartyName(row.partai);
                    const nama = String(row.nama || '').trim();
                    const gender = String(row.gender || '').trim().toUpperCase();
                    const kecamatan = normalizeKey(row.kecamatan);
                    const desa = normalizeKey(row.desa);
                    const suara = numberValue(row.suara);
                    const calegKey = `${dapil}__${partaiId}__${nomorUrut}__${normalizeKey(nama)}`;
                    const villageKey = `${dapil}__${kecamatan}__${desa}`;
                    const dapilObj = getOrCreate(dataset.dapils, dapil, () => ({
                        dapil,
                        calegMap: new Map(),
                        partyMap: new Map(),
                        villagePartyMap: new Map(),
                        rwPartyMap: new Map(),
                    }));
                    const partyObj = getOrCreate(dapilObj.partyMap, partaiId || partai, () => ({
                        partaiId: partaiId || partai,
                        partai,
                        totalSuaraCaleg: 0,
                        calegMap: new Map(),
                        lakilaki: 0,
                        perempuan: 0,
                    }));
                    const calegObj = getOrCreate(dapilObj.calegMap, calegKey, () => {
                        const entry = {
                            key: calegKey,
                            nama,
                            partaiId: partaiId || partai,
                            partai,
                            nomorUrut,
                            gender,
                            dapil,
                            totalSuara: 0,
                            desaMap: new Map(),
                            kecamatanMap: new Map(),
                            rwMap: new Map(),
                            tpsSet: new Set(),
                            partyTotalSuara: 0,
                            partyRank: 0,
                            rank: 0,
                            isElectedEstimate: false,
                        };
                        partyObj.calegMap.set(calegKey, entry);
                        if (gender === 'L') partyObj.lakilaki += 1;
                        if (gender === 'P') partyObj.perempuan += 1;
                        return entry;
                    });

                    dataset.totalRows += 1;
                    dataset.allPartyNames.add(partai);
                    calegObj.totalSuara += suara;
                    calegObj.tpsSet.add(`${villageKey}__${String(row.tps || '').trim()}`);
                    const desaObj = getOrCreate(calegObj.desaMap, villageKey, () => ({ desa: toTitleCase(desa), kecamatan: toTitleCase(kecamatan), suara: 0 }));
                    desaObj.suara += suara;
                    const kecamatanObj = getOrCreate(calegObj.kecamatanMap, kecamatan, () => ({ kecamatan: toTitleCase(kecamatan), suara: 0 }));
                    kecamatanObj.suara += suara;

                    // Resolve RW mapping using tpsToRwMap
                    const tpsDigits = String(row.tps || '').replace(/[^\d]/g, '');
                    const villageTpsMap = tpsToRwMap.get(villageKey);
                    const rwNum = villageTpsMap ? villageTpsMap.get(tpsDigits) : null;
                    if (rwNum) {
                        const rwKey = `${villageKey}__${rwNum}`;
                        const rwObj = getOrCreate(calegObj.rwMap, rwKey, () => ({ rw: rwNum, desa: toTitleCase(desa), kecamatan: toTitleCase(kecamatan), suara: 0 }));
                        rwObj.suara += suara;

                        const rwPartyObj = getOrCreate(dapilObj.rwPartyMap, rwKey, () => ({
                            rw: rwNum,
                            desa: toTitleCase(desa),
                            kecamatan: toTitleCase(kecamatan),
                            partyTotals: new Map(),
                        }));
                        const rwPartyEntry = getOrCreate(rwPartyObj.partyTotals, partai, () => ({ partai, suara: 0 }));
                        rwPartyEntry.suara += suara;
                    }

                    partyObj.totalSuaraCaleg += suara;

                    const villagePartyObj = getOrCreate(dapilObj.villagePartyMap, villageKey, () => ({
                        desa: toTitleCase(desa),
                        kecamatan: toTitleCase(kecamatan),
                        partyTotals: new Map(),
                    }));
                    const villagePartyEntry = getOrCreate(villagePartyObj.partyTotals, partai, () => ({ partai, suara: 0 }));
                    villagePartyEntry.suara += suara;
                });;

                dataset.dapils.forEach((dapilObj) => {
                    const allCaleg = Array.from(dapilObj.calegMap.values()).sort((a, b) => b.totalSuara - a.totalSuara);
                    allCaleg.forEach((caleg, index) => { caleg.rank = index + 1; });
                    const totalSuaraDapil = allCaleg.reduce((sum, caleg) => sum + caleg.totalSuara, 0);
                    dapilObj.totalSuara = totalSuaraDapil;

                    dapilObj.partyMap.forEach((partyObj) => {
                        const sortedPartyCaleg = Array.from(partyObj.calegMap.values()).sort((a, b) => b.totalSuara - a.totalSuara);
                        const estimatedSeats = estimatePartySeats(partyObj.totalSuaraCaleg, totalSuaraDapil, 8);
                        sortedPartyCaleg.forEach((caleg, index) => {
                            caleg.partyRank = index + 1;
                            caleg.partyTotalSuara = partyObj.totalSuaraCaleg;
                            caleg.isElectedEstimate = estimatedSeats > 0 && index < estimatedSeats;
                        });
                        partyObj.calegList = sortedPartyCaleg;
                        partyObj.calegCount = sortedPartyCaleg.length;
                    });
                });

                return dataset;
            }

            function getScopedCalegVotes(caleg) {
                if (state.currentDesa) {
                    const desaObj = caleg.desaMap.get(state.currentDesa);
                    return desaObj ? desaObj.suara : 0;
                }
                if (state.currentKecamatan) {
                    const kecObj = caleg.kecamatanMap.get(state.currentKecamatan);
                    return kecObj ? kecObj.suara : 0;
                }
                return caleg.totalSuara;
            }

            function buildScopeData(dapilKey) {
                if (!state.dataset) {
                    return {
                        allCaleg: [], visibleCaleg: [], pksCaleg: [], topPartai: [], genderStats: createEmptyGenderStats(),
                        headToHead: [], headToHeadRw: [], totalSuara: 0, totalParties: 0, pksShare: 0, pksRank: 0,
                    };
                }

                const targetDapils = dapilKey ? [state.dataset.dapils.get(dapilKey)].filter(Boolean) : Array.from(state.dataset.dapils.values());
                const allCaleg = targetDapils.flatMap((dapilObj) => Array.from(dapilObj.calegMap.values()));
                const visibleCaleg = applyCalegFilters(allCaleg);
                const partyMap = buildPartySummary(visibleCaleg);
                const topPartai = Array.from(partyMap.values()).sort((a, b) => b.totalSuara - a.totalSuara);
                const totalSuara = visibleCaleg.reduce((sum, caleg) => sum + caleg.scopedVotes, 0);
                const pksParty = topPartai.find((party) => party.partai === 'PKS');
                const pksCaleg = visibleCaleg.filter((caleg) => caleg.partai === 'PKS').sort((a, b) => b.scopedVotes - a.scopedVotes);
                const pksRank = pksParty ? (topPartai.findIndex((party) => party.partai === 'PKS') + 1) : 0;
                return {
                    allCaleg,
                    visibleCaleg,
                    pksCaleg,
                    topPartai,
                    genderStats: buildGenderStats(visibleCaleg, topPartai),
                    headToHead: buildHeadToHead(targetDapils),
                    headToHeadRw: buildHeadToHeadRw(targetDapils),
                    totalSuara,
                    totalParties: topPartai.length,
                    pksShare: totalSuara ? (pksParty?.totalSuara || 0) / totalSuara : 0,
                    pksRank,
                };
            }

            function applyCalegFilters(calegList) {
                return calegList.map((caleg) => {
                    const scopedVotes = getScopedCalegVotes(caleg);
                    return {
                        ...caleg,
                        scopedVotes: scopedVotes
                    };
                }).filter((caleg) => {
                    if (caleg.scopedVotes <= 0) return false;
                    if (state.currentPartai && caleg.partai !== state.currentPartai) return false;
                    if (state.currentGender && caleg.gender !== state.currentGender) return false;
                    if (state.searchKeyword) {
                        const keyword = normalizeKey(state.searchKeyword);
                        if (!normalizeKey(caleg.nama).includes(keyword)) return false;
                    }
                    return true;
                }).sort((a, b) => b.scopedVotes - a.scopedVotes);
            }

            function buildPartySummary(calegList) {
                const partyMap = new Map();
                calegList.forEach((caleg) => {
                    const party = getOrCreate(partyMap, caleg.partai, () => ({
                        partai: caleg.partai,
                        totalSuara: 0,
                        calegCount: 0,
                        maleCount: 0,
                        femaleCount: 0,
                    }));
                    party.totalSuara += caleg.scopedVotes;
                    party.calegCount += 1;
                    if (caleg.gender === 'L') party.maleCount += 1;
                    if (caleg.gender === 'P') party.femaleCount += 1;
                });
                return partyMap;
            }

            function buildHeadToHead(targetDapils) {
                const rows = [];
                targetDapils.forEach((dapilObj) => {
                    if (!dapilObj) return;
                    dapilObj.villagePartyMap.forEach((village, key) => {
                        if (state.currentDesa && key !== state.currentDesa) return;
                        const parts = key.split('__');
                        const district = parts[1];
                        if (state.currentKecamatan && district.toUpperCase() !== state.currentKecamatan) return;

                        const rankedParties = Array.from(village.partyTotals.values()).sort((a, b) => b.suara - a.suara);
                        const pksEntry = rankedParties.find((party) => party.partai === 'PKS') || { partai: 'PKS', suara: 0 };
                        const pksRank = Math.max(1, rankedParties.findIndex((party) => party.partai === 'PKS') + 1 || (rankedParties.length + 1));
                        const topCompetitors = rankedParties.filter((party) => party.partai !== 'PKS').slice(0, 2);
                        rows.push({
                            desa: village.desa,
                            kecamatan: village.kecamatan,
                            pksSuara: pksEntry.suara,
                            pksRank,
                            topCompetitors,
                        });
                    });
                });
                return rows.sort((a, b) => b.pksSuara - a.pksSuara);
            }

            function buildHeadToHeadRw(targetDapils) {
                const rows = [];
                targetDapils.forEach((dapilObj) => {
                    if (!dapilObj || !dapilObj.rwPartyMap) return;
                    dapilObj.rwPartyMap.forEach((rwObj, key) => {
                        const parts = key.split('__');
                        const vKey = `${parts[0]}__${parts[1]}__${parts[2]}`;
                        if (state.currentDesa && vKey !== state.currentDesa) return;
                        if (state.currentKecamatan && parts[1].toUpperCase() !== state.currentKecamatan) return;

                        const rankedParties = Array.from(rwObj.partyTotals.values()).sort((a, b) => b.suara - a.suara);
                        const pksEntry = rankedParties.find((party) => party.partai === 'PKS') || { partai: 'PKS', suara: 0 };
                        const pksRank = Math.max(1, rankedParties.findIndex((party) => party.partai === 'PKS') + 1 || (rankedParties.length + 1));
                        const topCompetitors = rankedParties.filter((party) => party.partai !== 'PKS').slice(0, 2);
                        rows.push({
                            rw: rwObj.rw,
                            desa: rwObj.desa,
                            kecamatan: rwObj.kecamatan,
                            pksSuara: pksEntry.suara,
                            pksRank,
                            topCompetitors,
                        });
                    });
                });
                return rows.sort((a, b) => b.pksSuara - a.pksSuara);
            }

            function buildGenderStats(calegList, topPartai) {
                if (!calegList.length) return createEmptyGenderStats();
                const perParty = topPartai.slice(0, 5).map((party) => ({
                    partai: party.partai,
                    maleCount: party.maleCount,
                    femaleCount: party.femaleCount,
                    total: Math.max(1, party.calegCount),
                }));
                const maleCaleg = calegList.filter((caleg) => caleg.gender === 'L');
                const femaleCaleg = calegList.filter((caleg) => caleg.gender === 'P');
                const totalMaleVotes = maleCaleg.reduce((sum, caleg) => sum + caleg.scopedVotes, 0);
                const totalFemaleVotes = femaleCaleg.reduce((sum, caleg) => sum + caleg.scopedVotes, 0);
                const pksFemale = calegList.filter((caleg) => caleg.partai === 'PKS' && caleg.gender === 'P').sort((a, b) => b.scopedVotes - a.scopedVotes);
                const totalPksVotes = calegList.filter((caleg) => caleg.partai === 'PKS').reduce((sum, caleg) => sum + caleg.scopedVotes, 0);
                const pksFemaleVotes = pksFemale.reduce((sum, caleg) => sum + caleg.scopedVotes, 0);
                const highlightFemale = pksFemale[0];
                const insight = `PKS punya ${formatNumber(pksFemale.length)} caleg perempuan pada scope ini. ${highlightFemale ? `${highlightFemale.nama} meraih suara tertinggi (${formatNumber(highlightFemale.scopedVotes)}).` : 'Belum ada caleg perempuan PKS pada scope ini.'} Caleg perempuan PKS meraup ${formatPercent(totalPksVotes ? (pksFemaleVotes / totalPksVotes) : 0)} dari total suara PKS.`;
                return {
                    perParty,
                    avgMale: maleCaleg.length ? (totalMaleVotes / maleCaleg.length) : 0,
                    avgFemale: femaleCaleg.length ? (totalFemaleVotes / femaleCaleg.length) : 0,
                    totalMale: maleCaleg.length,
                    totalFemale: femaleCaleg.length,
                    totalMaleVotes,
                    totalFemaleVotes,
                    insight,
                };
            }

            function createEmptyGenderStats() {
                return { perParty: [], avgMale: 0, avgFemale: 0, totalMale: 0, totalFemale: 0, totalMaleVotes: 0, totalFemaleVotes: 0, insight: 'Belum ada data gender pada scope ini.' };
            }

            function populateFilters() {
                populateDapilSelect();
                populateKecamatanOptions();
                populateDesaOptions();
                populatePartaiSelect();
            }

            function populateKecamatanOptions() {
                if (!state.dataset) return;
                const districts = new Set();
                if (state.currentDapil) {
                    const dapilObj = state.dataset.dapils.get(state.currentDapil);
                    if (dapilObj) {
                        dapilObj.villagePartyMap.forEach((val) => {
                            districts.add(val.kecamatan.toUpperCase());
                        });
                    }
                } else {
                    state.dataset.dapils.forEach((dapilObj) => {
                        dapilObj.villagePartyMap.forEach((val) => {
                            districts.add(val.kecamatan.toUpperCase());
                        });
                    });
                }
                const sortedDistricts = Array.from(districts).sort(compareNatural);
                dom.kecamatanSelect.innerHTML = `<option value="">Semua kecamatan</option>${sortedDistricts.map((d) => `<option value="${escapeHtml(d)}">${escapeHtml(toTitleCase(d))}</option>`).join('')}`;
                dom.kecamatanSelect.value = state.currentKecamatan;
            }

            function populateDesaOptions() {
                if (!state.dataset) return;
                const villages = [];
                if (state.currentDapil) {
                    const dapilObj = state.dataset.dapils.get(state.currentDapil);
                    if (dapilObj) {
                        dapilObj.villagePartyMap.forEach((val, key) => {
                            if (!state.currentKecamatan || val.kecamatan.toUpperCase() === state.currentKecamatan) {
                                villages.push({ key, label: val.desa });
                            }
                        });
                    }
                } else {
                    state.dataset.dapils.forEach((dapilObj) => {
                        dapilObj.villagePartyMap.forEach((val, key) => {
                            if (!state.currentKecamatan || val.kecamatan.toUpperCase() === state.currentKecamatan) {
                                villages.push({ key, label: val.desa });
                            }
                        });
                    });
                }
                villages.sort((a, b) => compareNatural(a.label, b.label));
                dom.desaSelect.innerHTML = `<option value="">Semua desa</option>${villages.map((v) => `<option value="${escapeHtml(v.key)}">${escapeHtml(v.label)}</option>`).join('')}`;
                dom.desaSelect.value = state.currentDesa;
            }

            function populateDapilSelect() {
                const dapils = Array.from(state.dataset?.dapils.keys() || []).sort(compareNatural);
                dom.dapilSelect.innerHTML = `<option value="">Semua dapil</option>${dapils.map((dapil) => `<option value="${escapeHtml(dapil)}">${escapeHtml(toTitleCase(dapil))}</option>`).join('')}`;
                dom.dapilSelect.value = state.currentDapil;
            }

            function populatePartaiSelect() {
                const parties = Array.from(state.dataset?.allPartyNames || []).sort((a, b) => a.localeCompare(b));
                dom.partaiSelect.innerHTML = `<option value="">Semua partai</option>${parties.map((partai) => `<option value="${escapeHtml(partai)}">${escapeHtml(partai)}</option>`).join('')}`;
                dom.partaiSelect.value = state.currentPartai;
            }

            function render() {
                if (!state.dataset) return;
                const scopeData = buildScopeData(state.currentDapil);
                renderHeader(scopeData);
                renderSummaryCards(scopeData);
                dom.inlineMapWrap.innerHTML = renderMap();
                renderCalegRanking(scopeData);
                renderPksSebaran(scopeData);
                renderHeadToHead(scopeData);
                renderPksRwSebaran(scopeData);
                renderHeadToHeadRw(scopeData);
                renderGenderAnalysis(scopeData);
                renderFullTable(scopeData);
                renderCalegDrawer(scopeData);
            }

            function renderHeader(scopeData) {
                const dapilLabel = state.currentDapil ? `Dapil ${state.currentDapil.replace('BEKASI ', '')}` : 'Kabupaten Bekasi';
                dom.pageHeading.textContent = dapilLabel;
                dom.pageSubheading.textContent = `Analisa caleg · ${formatNumber(scopeData.visibleCaleg.length)} caleg · ${formatNumber(scopeData.totalParties)} partai`;
                dom.pageMeta.textContent = state.currentDapil
                    ? `Fokus ${dapilLabel}: ranking suara, basis PKS, head-to-head, dan gender.`
                    : 'Fokus: ranking suara, sebaran PKS, head-to-head, dan analisa gender.';
                dom.rankingTitle.textContent = `Top 15 peraih suara tertinggi${state.currentDapil ? ` (${dapilLabel})` : ''}`;
                dom.pksSebaranTitle.textContent = `Sebaran suara per kecamatan${state.currentDapil ? ` - ${dapilLabel}` : ''}`;
                dom.fullTableTitle.textContent = `Semua caleg${state.currentDapil ? ` ${dapilLabel}` : ''} - ${formatNumber(scopeData.visibleCaleg.length)} caleg, ${formatNumber(scopeData.totalParties)} partai`;
            }

            function renderSummaryCards(scopeData) {
                const totalPksCaleg = scopeData.visibleCaleg.filter((caleg) => caleg.partai === 'PKS');
                const totalPksVotes = totalPksCaleg.reduce((sum, caleg) => sum + caleg.scopedVotes, 0);
                const pksMale = totalPksCaleg.filter((caleg) => caleg.gender === 'L').length;
                const pksFemale = totalPksCaleg.filter((caleg) => caleg.gender === 'P').length;
                const avgAll = scopeData.visibleCaleg.length ? (scopeData.totalSuara / scopeData.visibleCaleg.length) : 0;
                const avgPks = totalPksCaleg.length ? (totalPksVotes / totalPksCaleg.length) : 0;

                dom.cardTotalCaleg.innerHTML = statCardHtml('Total Caleg', formatNumber(scopeData.visibleCaleg.length), `${formatNumber(scopeData.totalParties)} partai`);
                dom.cardTotalSuara.innerHTML = statCardHtml('Total Suara Caleg', formatNumber(scopeData.totalSuara), 'Suara individu caleg');
                dom.cardPksCaleg.innerHTML = statCardHtml('Caleg PKS', formatNumber(totalPksCaleg.length), `${formatNumber(pksMale)} L · ${formatNumber(pksFemale)} P`, true);
                dom.cardPksSuara.innerHTML = statCardHtml('Suara Caleg PKS', formatNumber(totalPksVotes), `${formatPercent(scopeData.pksShare)} dari total · rank ${scopeData.pksRank || '-'}`, true);
                dom.cardRataSuara.innerHTML = statCardHtml('Rata-rata Suara / Caleg', formatNumber(Math.round(avgAll)), `PKS: ${formatNumber(Math.round(avgPks))} vs rata-rata: ${formatNumber(Math.round(avgAll))}`);
            }

            function renderCalegRanking(scopeData) {
                const rows = scopeData.visibleCaleg.slice(0, 15).map((caleg, index) => {
                    const partyColor = getPartyColor(caleg.partai);
                    const isPks = caleg.partai === 'PKS';
                    const isElected = caleg.isElectedEstimate;
                    const background = isElected ? '#f0fdf4' : (isPks ? '#fff7f1' : 'white');
                    const border = isElected ? '#bbf7d0' : (isPks ? '#fce4ce' : '#e5e5e5');
                    const suffix = isElected ? '<div style="font-size:9px;color:#16a34a;font-weight:500;">TERPILIH</div>' : '';
                    return `
                        <button type="button" data-open-caleg="${escapeHtml(caleg.key)}" style="display:flex;align-items:center;gap:8px;padding:8px;background:${background};border-radius:7px;border:0.5px solid ${border};margin-bottom:0;cursor:pointer;text-align:left;">
                            <div style="width:24px;height:24px;border-radius:50%;background:${partyColor};color:white;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:500;flex-shrink:0;">${index + 1}</div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:12px;font-weight:500;color:#1a1a1a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escapeHtml(caleg.nama)}</div>
                                <div style="font-size:10px;color:#666;display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
                                    <span style="width:6px;height:6px;border-radius:50%;background:${partyColor};display:inline-block;"></span>
                                    ${escapeHtml(caleg.partai)} · No. ${escapeHtml(String(caleg.nomorUrut))} · ${escapeHtml(formatGender(caleg.gender))}
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:13px;font-weight:500;color:${isPks ? '#fe5000' : '#1a1a1a'};">${formatNumber(caleg.scopedVotes)}</div>
                                ${suffix}
                            </div>
                        </button>`;
                }).join('');
                dom.calegRankingWrap.innerHTML = rows || emptyState('Belum ada data caleg untuk filter ini.');
                bindOpenCalegButtons(dom.calegRankingWrap);
            }

            function renderPksSebaran(scopeData) {
                const topPks = scopeData.pksCaleg.slice(0, 5);
                if (!topPks.length) {
                    dom.pksSebaranWrap.innerHTML = emptyState('Belum ada caleg PKS pada scope atau filter aktif.');
                    return;
                }
                dom.pksSebaranWrap.innerHTML = topPks.map((caleg, index) => {
                    const kecamatanRows = Array.from(caleg.kecamatanMap.values()).sort((a, b) => b.suara - a.suara).slice(0, 6);
                    const maxVotes = Math.max(1, ...kecamatanRows.map((row) => row.suara));
                    const topVillages = getTopVillages(caleg, 3).map((row) => row.desa).join(', ');
                    return `
                        <div style="${index > 0 ? 'border-top:0.5px solid #f0f0f0;padding-top:10px;' : ''}">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:8px;">
                                <button type="button" data-open-caleg="${escapeHtml(caleg.key)}" style="background:none;border:none;padding:0;cursor:pointer;text-align:left;">
                                    <span style="font-size:13px;font-weight:500;color:#1a1a1a;">${escapeHtml(caleg.nama)}</span>
                                    <span style="font-size:10px;color:#666;margin-left:6px;">No. ${escapeHtml(String(caleg.nomorUrut))} · ${escapeHtml(formatGender(caleg.gender))} · ${formatNumber(caleg.scopedVotes)} suara</span>
                                </button>
                                ${caleg.isElectedEstimate ? '<div style="font-size:9px;color:#16a34a;font-weight:500;background:#f0fdf4;border:0.5px solid #bbf7d0;padding:3px 6px;border-radius:999px;">TERPILIH</div>' : ''}
                            </div>
                            <div style="display:flex;flex-direction:column;gap:4px;">
                                ${kecamatanRows.map((row) => `
                                    <div style="display:flex;align-items:center;gap:6px;font-size:11px;">
                                        <div style="width:85px;color:#666;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escapeHtml(row.kecamatan)}</div>
                                        <div style="flex:1;background:#f5f5f5;border-radius:3px;height:14px;">
                                            <div style="background:#fe5000;height:100%;width:${Math.max(10, (row.suara / maxVotes) * 100)}%;border-radius:3px;display:flex;align-items:center;padding-left:4px;color:white;font-size:9px;min-width:28px;">${formatNumber(row.suara)}</div>
                                        </div>
                                    </div>`).join('')}
                            </div>
                            <div style="font-size:10px;color:#888;margin-top:4px;">Basis kuat: ${escapeHtml(topVillages || 'Belum terbaca')}</div>
                        </div>`;
                }).join('');
                bindOpenCalegButtons(dom.pksSebaranWrap);
            }

            function renderHeadToHead(scopeData) {
                const rows = state.showAllHeadToHead ? scopeData.headToHead : scopeData.headToHead.slice(0, 20);
                dom.toggleHeadToHeadBtn.style.display = scopeData.headToHead.length > 20 ? 'inline-flex' : 'none';
                dom.toggleHeadToHeadBtn.textContent = state.showAllHeadToHead ? 'Ringkas tabel' : `Tampilkan semua (${formatNumber(scopeData.headToHead.length)})`;

                const body = rows.map((row) => {
                    const competitorOne = row.topCompetitors[0] || { partai: '-', suara: 0 };
                    const competitorTwo = row.topCompetitors[1] || { partai: '-', suara: 0 };
                    return `
                        <tr style="border-bottom:0.5px solid #eee;">
                            <td style="padding:9px 10px;font-size:12px;color:#1a1a1a;">${escapeHtml(row.desa)}</td>
                            <td style="padding:9px 10px;font-size:12px;color:#444;">${escapeHtml(row.kecamatan)}</td>
                            <td style="padding:9px 10px;font-size:12px;color:#fe5000;font-weight:500;">${formatNumber(row.pksSuara)}</td>
                            <td style="padding:9px 10px;font-size:12px;color:#444;"><span style="width:6px;height:6px;border-radius:50%;background:${getPartyColor(competitorOne.partai)};display:inline-block;margin-right:6px;"></span>${escapeHtml(competitorOne.partai)} ${formatNumber(competitorOne.suara)}</td>
                            <td style="padding:9px 10px;font-size:12px;color:#444;"><span style="width:6px;height:6px;border-radius:50%;background:${getPartyColor(competitorTwo.partai)};display:inline-block;margin-right:6px;"></span>${escapeHtml(competitorTwo.partai)} ${formatNumber(competitorTwo.suara)}</td>
                            <td style="padding:9px 10px;font-size:11px;">${renderRankPill(row.pksRank)}</td>
                        </tr>`;
                }).join('');

                dom.headToHeadWrap.innerHTML = `
                    <div style="overflow:auto;border:0.5px solid #e5e5e5;border-radius:10px;flex:1 1 auto;min-height:0;">
                        <table style="width:100%;border-collapse:collapse;background:white;">
                            <thead>
                                <tr style="background:#fafafa;">
                                    <th style="padding:9px 10px;text-align:left;font-size:11px;color:#666;font-weight:500;">Desa</th>
                                    <th style="padding:9px 10px;text-align:left;font-size:11px;color:#666;font-weight:500;">Kecamatan</th>
                                    <th style="padding:9px 10px;text-align:left;font-size:11px;color:#666;font-weight:500;">PKS</th>
                                    <th style="padding:9px 10px;text-align:left;font-size:11px;color:#666;font-weight:500;">Kompetitor 1</th>
                                    <th style="padding:9px 10px;text-align:left;font-size:11px;color:#666;font-weight:500;">Kompetitor 2</th>
                                    <th style="padding:9px 10px;text-align:left;font-size:11px;color:#666;font-weight:500;">Rank PKS</th>
                                </tr>
                            </thead>
                            <tbody>${body || `<tr><td colspan="6" style="padding:18px;text-align:center;font-size:12px;color:#888;">Belum ada data head-to-head.</td></tr>`}</tbody>
                        </table>
                    </div>`;
            }

            function renderPksRwSebaran(scopeData) {
                const topPks = scopeData.pksCaleg.slice(0, 5);
                if (!topPks.length) {
                    dom.pksRwWrap.innerHTML = emptyState('Belum ada caleg PKS pada scope atau filter aktif.');
                    return;
                }
                dom.pksRwWrap.innerHTML = topPks.map((caleg, index) => {
                    const rwRows = Array.from(caleg.rwMap.values()).sort((a, b) => b.suara - a.suara).slice(0, 6);
                    const maxVotes = Math.max(1, ...rwRows.map((row) => row.suara));
                    return `
                        <div style="${index > 0 ? 'border-top:0.5px solid #f0f0f0;padding-top:10px;' : ''}">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:8px;">
                                <button type="button" data-open-caleg="${escapeHtml(caleg.key)}" style="background:none;border:none;padding:0;cursor:pointer;text-align:left;">
                                    <span style="font-size:13px;font-weight:500;color:#1a1a1a;">${escapeHtml(caleg.nama)}</span>
                                    <span style="font-size:10px;color:#666;margin-left:6px;">No. ${escapeHtml(String(caleg.nomorUrut))} · ${escapeHtml(formatGender(caleg.gender))} · ${formatNumber(caleg.scopedVotes)} suara</span>
                                </button>
                                ${caleg.isElectedEstimate ? '<div style="font-size:9px;color:#16a34a;font-weight:500;background:#f0fdf4;border:0.5px solid #bbf7d0;padding:3px 6px;border-radius:999px;">TERPILIH</div>' : ''}
                            </div>
                            <div style="display:flex;flex-direction:column;gap:4px;">
                                ${rwRows.map((row) => `
                                    <div style="display:flex;align-items:center;gap:6px;font-size:11px;">
                                        <div style="width:115px;color:#666;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">RW ${escapeHtml(row.rw)} · ${escapeHtml(row.desa)}</div>
                                        <div style="flex:1;background:#f5f5f5;border-radius:3px;height:14px;">
                                            <div style="background:#fe5000;height:100%;width:${Math.max(10, (row.suara / maxVotes) * 100)}%;border-radius:3px;display:flex;align-items:center;padding-left:4px;color:white;font-size:9px;min-width:28px;">${formatNumber(row.suara)}</div>
                                        </div>
                                    </div>`).join('')}
                            </div>
                        </div>`;
                }).join('');
                bindOpenCalegButtons(dom.pksRwWrap);
            }

            function renderHeadToHeadRw(scopeData) {
                const rows = state.showAllHeadToHeadRw ? scopeData.headToHeadRw : scopeData.headToHeadRw.slice(0, 20);
                dom.toggleH2hRwBtn.style.display = scopeData.headToHeadRw.length > 20 ? 'inline-flex' : 'none';
                dom.toggleH2hRwBtn.textContent = state.showAllHeadToHeadRw ? 'Ringkas tabel' : `Tampilkan semua (${formatNumber(scopeData.headToHeadRw.length)})`;

                const body = rows.map((row) => {
                    const competitorOne = row.topCompetitors[0] || { partai: '-', suara: 0 };
                    const competitorTwo = row.topCompetitors[1] || { partai: '-', suara: 0 };
                    const maxVotes = Math.max(1, row.pksSuara, competitorOne.suara, competitorTwo.suara);
                    const isPksFirst = row.pksRank === 1;

                    return `
                        <tr style="border-bottom:0.5px solid #eee;">
                            <td style="padding:9px 10px;font-size:12px;font-weight:500;color:#1a1a1a;">RW ${escapeHtml(row.rw)}</td>
                            <td style="padding:9px 10px;font-size:12px;color:#444;">${escapeHtml(row.desa)}<div style="font-size:10px;color:#888;">${escapeHtml(row.kecamatan)}</div></td>
                            <td style="padding:9px 10px;font-size:11px;color:#666;text-align:center;">
                                <span style="display:inline-flex;padding:2px 6px;border-radius:999px;font-weight:500;font-size:10px;background:${isPksFirst ? '#f0fdf4' : '#fee2e2'};color:${isPksFirst ? '#16a34a' : '#991b1b'};">Rank ${row.pksRank}</span>
                            </td>
                            <td style="padding:9px 10px;">
                                <div style="display:flex;flex-direction:column;gap:3px;">
                                    <div style="display:flex;align-items:center;gap:6px;font-size:11px;">
                                        <div style="width:50px;font-weight:500;color:#fe5000;">PKS</div>
                                        <div style="flex:1;background:#f5f5f5;border-radius:3px;height:12px;">
                                            <div style="background:#fe5000;height:100%;width:${Math.max(5, (row.pksSuara / maxVotes) * 100)}%;border-radius:3px;display:flex;align-items:center;padding-left:4px;color:white;font-size:8px;min-width:20px;">${formatNumber(row.pksSuara)}</div>
                                        </div>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:6px;font-size:11px;">
                                        <div style="width:50px;color:#666;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${escapeHtml(competitorOne.partai)}</div>
                                        <div style="flex:1;background:#f5f5f5;border-radius:3px;height:12px;">
                                            <div style="background:#a1a1aa;height:100%;width:${Math.max(5, (competitorOne.suara / maxVotes) * 100)}%;border-radius:3px;display:flex;align-items:center;padding-left:4px;color:white;font-size:8px;min-width:20px;">${formatNumber(competitorOne.suara)}</div>
                                        </div>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:6px;font-size:11px;">
                                        <div style="width:50px;color:#666;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${escapeHtml(competitorTwo.partai)}</div>
                                        <div style="flex:1;background:#f5f5f5;border-radius:3px;height:12px;">
                                            <div style="background:#e4e4e7;height:100%;width:${Math.max(5, (competitorTwo.suara / maxVotes) * 100)}%;border-radius:3px;display:flex;align-items:center;padding-left:4px;color:#444;font-size:8px;min-width:20px;">${formatNumber(competitorTwo.suara)}</div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>`;
                }).join('');

                dom.h2hRwWrap.innerHTML = `
                    <div style="overflow:auto;border:0.5px solid #e5e5e5;border-radius:10px;height:100%;box-sizing:border-box;">
                        <table style="width:100%;border-collapse:collapse;background:white;">
                            <thead>
                                <tr style="background:#fafafa;position:sticky;top:0;z-index:5;box-shadow:0 1px 0 #eee;">
                                    <th style="padding:9px 10px;text-align:left;font-size:11px;color:#666;font-weight:500;">RW</th>
                                    <th style="padding:9px 10px;text-align:left;font-size:11px;color:#666;font-weight:500;">Desa</th>
                                    <th style="padding:9px 10px;text-align:center;font-size:11px;color:#666;font-weight:500;">PKS Rank</th>
                                    <th style="padding:9px 10px;text-align:left;font-size:11px;color:#666;font-weight:500;">Perbandingan Suara</th>
                                </tr>
                            </thead>
                            <tbody>${body || `<tr><td colspan="4" style="padding:18px;text-align:center;font-size:12px;color:#888;">Belum ada data RW untuk filter ini.</td></tr>`}</tbody>
                        </table>
                    </div>`;
            }

            function renderGenderAnalysis(scopeData) {
                const stats = scopeData.genderStats;
                const partyBars = stats.perParty.map((party) => {
                    const malePct = party.total ? (party.maleCount / party.total) * 100 : 0;
                    const femalePct = party.total ? (party.femaleCount / party.total) * 100 : 0;
                    return `
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;margin-bottom:6px;">
                            <div style="width:55px;color:#666;">${escapeHtml(party.partai)}</div>
                            <div style="flex:1;display:flex;height:16px;border-radius:3px;overflow:hidden;background:#f5f5f5;">
                                <div style="background:#3b82f6;width:${malePct}%;display:flex;align-items:center;justify-content:center;color:white;font-size:9px;min-width:${party.maleCount ? '26px' : '0'};">${party.maleCount ? `${party.maleCount} L` : ''}</div>
                                <div style="background:#ec4899;width:${femalePct}%;display:flex;align-items:center;justify-content:center;color:white;font-size:9px;min-width:${party.femaleCount ? '26px' : '0'};">${party.femaleCount ? `${party.femaleCount} P` : ''}</div>
                            </div>
                        </div>`;
                }).join('');

                dom.genderAnalysisWrap.innerHTML = `
                    <div>${partyBars || emptyState('Belum ada data komposisi partai.')}</div>
                    <div style="margin-top:14px;padding-top:12px;border-top:0.5px solid #f0f0f0;">
                        <div style="font-size:11px;color:#888;margin-bottom:8px;">Rata-rata suara per caleg</div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                            <div style="background:#eff6ff;border-radius:8px;padding:10px;text-align:center;">
                                <div style="font-size:10px;color:#3b82f6;">Laki-laki</div>
                                <div style="font-size:18px;font-weight:500;color:#1e40af;">${formatNumber(Math.round(stats.avgMale))}</div>
                                <div style="font-size:10px;color:#888;">${formatNumber(stats.totalMale)} caleg</div>
                            </div>
                            <div style="background:#fdf2f8;border-radius:8px;padding:10px;text-align:center;">
                                <div style="font-size:10px;color:#ec4899;">Perempuan</div>
                                <div style="font-size:18px;font-weight:500;color:#be185d;">${formatNumber(Math.round(stats.avgFemale))}</div>
                                <div style="font-size:10px;color:#888;">${formatNumber(stats.totalFemale)} caleg</div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:12px;font-size:11px;color:#444;line-height:1.6;padding:10px;background:#fafafa;border-radius:6px;">${escapeHtml(stats.insight)}</div>`;
            }

            function renderFullTable(scopeData) {
                const sortedRows = sortCaleg(scopeData.visibleCaleg, state.currentSort.column, state.currentSort.direction);
                const totalPages = Math.max(1, Math.ceil(sortedRows.length / 25));
                state.currentPage = Math.min(state.currentPage, totalPages);
                const currentRows = sortedRows.slice((state.currentPage - 1) * 25, state.currentPage * 25);

                const body = currentRows.map((caleg, index) => {
                    const isPks = caleg.partai === 'PKS';
                    const isElected = caleg.isElectedEstimate;
                    const background = isElected ? '#f0fdf4' : (isPks ? '#fff7f1' : 'white');
                    return `
                        <tr style="background:${background};border-bottom:0.5px solid #eee;">
                            <td style="padding:9px 10px;font-size:12px;color:#666;">${formatNumber(((state.currentPage - 1) * 25) + index + 1)}</td>
                            <td style="padding:9px 10px;font-size:12px;color:#1a1a1a;font-weight:500;">
                                <button type="button" data-open-caleg="${escapeHtml(caleg.key)}" style="background:none;border:none;padding:0;cursor:pointer;font:inherit;color:inherit;text-align:left;">${escapeHtml(caleg.nama)}</button>
                            </td>
                            <td style="padding:9px 10px;font-size:12px;color:#444;"><span style="width:6px;height:6px;border-radius:50%;background:${getPartyColor(caleg.partai)};display:inline-block;margin-right:6px;"></span>${escapeHtml(caleg.partai)}</td>
                            <td style="padding:9px 10px;font-size:12px;color:#444;text-align:center;">${escapeHtml(String(caleg.nomorUrut))}</td>
                            <td style="padding:9px 10px;font-size:12px;text-align:center;"><span style="display:inline-flex;padding:3px 8px;border-radius:999px;background:${caleg.gender === 'L' ? '#eff6ff' : '#fdf2f8'};color:${caleg.gender === 'L' ? '#2563eb' : '#ec4899'};">${escapeHtml(caleg.gender)}</span></td>
                            <td style="padding:9px 10px;font-size:12px;text-align:right;font-weight:500;color:${isPks ? '#fe5000' : '#1a1a1a'};">${formatNumber(caleg.scopedVotes)}</td>
                            <td style="padding:9px 10px;font-size:12px;color:#444;text-align:right;">${formatPercent(caleg.partyTotalSuara ? (caleg.scopedVotes / caleg.partyTotalSuara) : 0)}</td>
                            <td style="padding:9px 10px;font-size:10px;color:#666;">${escapeHtml(getTopVillages(caleg, 3).map((row) => row.desa).join(', ') || '-')}</td>
                        </tr>`;
                }).join('');

                dom.fullTableWrap.innerHTML = `
                    <div style="overflow:auto;border:0.5px solid #e5e5e5;border-radius:10px;flex:1 1 auto;min-height:0;">
                        <table style="width:100%;border-collapse:collapse;background:white;">
                            <thead>
                                <tr style="background:#fafafa;">
                                    ${renderSortHeader('#', null)}
                                    ${renderSortHeader('Nama', 'nama')}
                                    ${renderSortHeader('Partai', 'partai')}
                                    ${renderSortHeader('No. Urut', 'nomorUrut')}
                                    <th style="padding:9px 10px;text-align:center;font-size:11px;color:#666;font-weight:500;">L/P</th>
                                    ${renderSortHeader('Suara', 'suara', 'right')}
                                    <th style="padding:9px 10px;text-align:right;font-size:11px;color:#666;font-weight:500;">% dari Partai</th>
                                    <th style="padding:9px 10px;text-align:left;font-size:11px;color:#666;font-weight:500;">Basis Kuat</th>
                                </tr>
                            </thead>
                            <tbody>${body || `<tr><td colspan="8" style="padding:18px;text-align:center;font-size:12px;color:#888;">Belum ada data caleg untuk filter ini.</td></tr>`}</tbody>
                        </table>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:10px;flex-wrap:wrap;">
                        <div style="font-size:11px;color:#666;">Menampilkan ${formatNumber(currentRows.length)} dari ${formatNumber(sortedRows.length)} caleg.</div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <button type="button" data-page="prev" style="padding:5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:11px;background:white;color:#444;cursor:pointer;" ${state.currentPage <= 1 ? 'disabled' : ''}>Prev</button>
                            <div style="font-size:11px;color:#666;">Hal. ${formatNumber(state.currentPage)} / ${formatNumber(totalPages)}</div>
                            <button type="button" data-page="next" style="padding:5px 10px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:11px;background:white;color:#444;cursor:pointer;" ${state.currentPage >= totalPages ? 'disabled' : ''}>Next</button>
                        </div>
                    </div>`;

                dom.fullTableWrap.querySelectorAll('[data-open-caleg]').forEach((button) => {
                    button.addEventListener('click', () => openCalegDrawer(button.dataset.openCaleg));
                });
                dom.fullTableWrap.querySelectorAll('[data-sort]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const column = button.dataset.sort;
                        if (!column) return;
                        if (state.currentSort.column === column) {
                            state.currentSort.direction = state.currentSort.direction === 'asc' ? 'desc' : 'asc';
                        } else {
                            state.currentSort = { column, direction: column === 'nama' || column === 'partai' ? 'asc' : 'desc' };
                        }
                        render();
                    });
                });
                dom.fullTableWrap.querySelectorAll('[data-page]').forEach((button) => {
                    button.addEventListener('click', () => {
                        state.currentPage += button.dataset.page === 'next' ? 1 : -1;
                        render();
                    });
                });
            }

            function renderCalegDrawer(scopeData) {
                if (!state.selectedCalegKey) {
                    closeCalegDrawer(true);
                    return;
                }
                const caleg = scopeData.visibleCaleg.find((item) => item.key === state.selectedCalegKey) || scopeData.allCaleg.find((item) => item.key === state.selectedCalegKey);
                if (!caleg) {
                    closeCalegDrawer(true);
                    return;
                }

                dom.drawerTitle.textContent = caleg.nama;
                dom.drawerSubtitle.innerHTML = `<span style="width:6px;height:6px;border-radius:50%;background:${getPartyColor(caleg.partai)};display:inline-block;margin-right:6px;"></span>${escapeHtml(caleg.partai)} · No. ${escapeHtml(String(caleg.nomorUrut))} · ${escapeHtml(formatGender(caleg.gender))}${caleg.isElectedEstimate ? ' · TERPILIH' : ''}`;
                const topDistricts = Array.from(caleg.kecamatanMap.values()).sort((a, b) => b.suara - a.suara);
                const topVillages = getTopVillages(caleg, 10);
                const samePartyRows = scopeData.allCaleg.filter((item) => item.dapil === caleg.dapil && item.partai === caleg.partai).sort((a, b) => b.totalSuara - a.totalSuara);
                const heatRows = Array.from(caleg.desaMap.values()).sort((a, b) => b.suara - a.suara);
                const maxVillageVotes = Math.max(1, ...heatRows.map((row) => row.suara));

                dom.drawerContent.innerHTML = `
                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                        ${[
                            ['Total Suara', formatNumber(caleg.totalSuara)],
                            ['Ranking', `#${formatNumber(caleg.rank)}`],
                            ['% dari Partai', formatPercent(caleg.partyTotalSuara ? (caleg.totalSuara / caleg.partyTotalSuara) : 0)],
                            ['Jumlah TPS', formatNumber(caleg.tpsSet.size)],
                            ['Rank Internal', `#${formatNumber(caleg.partyRank)}`],
                            ['Gender', formatGender(caleg.gender)],
                        ].map((item) => `<div style="background:#fafafa;border-radius:8px;padding:10px;"><div style="font-size:10px;color:#888;text-transform:uppercase;">${item[0]}</div><div style="font-size:16px;font-weight:500;color:#1a1a1a;margin-top:4px;">${item[1]}</div></div>`).join('')}
                    </div>

                    <div style="margin-top:16px;">
                        <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Sebaran per Kecamatan</div>
                        <div style="margin-top:8px;display:grid;gap:4px;">
                            ${topDistricts.map((row) => `
                                <div style="display:flex;align-items:center;gap:6px;font-size:11px;">
                                    <div style="width:90px;color:#666;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escapeHtml(row.kecamatan)}</div>
                                    <div style="flex:1;background:#f5f5f5;border-radius:3px;height:14px;">
                                        <div style="background:${getPartyColor(caleg.partai)};height:100%;width:${Math.max(10, (row.suara / Math.max(1, topDistricts[0]?.suara || 1)) * 100)}%;border-radius:3px;display:flex;align-items:center;padding-left:4px;color:white;font-size:9px;min-width:28px;">${formatNumber(row.suara)}</div>
                                    </div>
                                </div>`).join('')}
                        </div>
                    </div>

                    <div style="margin-top:16px;">
                        <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Top 10 Desa</div>
                        <div style="overflow:auto;border:0.5px solid #e5e5e5;border-radius:10px;margin-top:8px;">
                            <table style="width:100%;border-collapse:collapse;background:white;">
                                <thead><tr style="background:#fafafa;"><th style="padding:8px 10px;text-align:left;font-size:11px;color:#666;font-weight:500;">Desa</th><th style="padding:8px 10px;text-align:left;font-size:11px;color:#666;font-weight:500;">Kecamatan</th><th style="padding:8px 10px;text-align:right;font-size:11px;color:#666;font-weight:500;">Suara</th><th style="padding:8px 10px;text-align:right;font-size:11px;color:#666;font-weight:500;">%</th></tr></thead>
                                <tbody>${topVillages.map((row) => `<tr><td style="padding:8px 10px;border-bottom:0.5px solid #eee;font-size:12px;color:#1a1a1a;">${escapeHtml(row.desa)}</td><td style="padding:8px 10px;border-bottom:0.5px solid #eee;font-size:12px;color:#444;">${escapeHtml(row.kecamatan)}</td><td style="padding:8px 10px;border-bottom:0.5px solid #eee;font-size:12px;text-align:right;font-weight:500;color:#1a1a1a;">${formatNumber(row.suara)}</td><td style="padding:8px 10px;border-bottom:0.5px solid #eee;font-size:12px;text-align:right;color:#666;">${formatPercent(caleg.totalSuara ? (row.suara / caleg.totalSuara) : 0)}</td></tr>`).join('')}</tbody>
                            </table>
                        </div>
                    </div>

                    <div style="margin-top:16px;">
                        <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Perbandingan Separtai</div>
                        <div style="margin-top:8px;display:grid;gap:5px;">
                            ${samePartyRows.map((item) => `
                                <div style="display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:7px;border:0.5px solid ${item.key === caleg.key ? '#fce4ce' : '#e5e5e5'};background:${item.key === caleg.key ? '#fff7f1' : 'white'};">
                                    <div style="width:20px;height:20px;border-radius:50%;background:${getPartyColor(item.partai)};color:white;display:flex;align-items:center;justify-content:center;font-size:10px;">${item.partyRank}</div>
                                    <div style="flex:1;min-width:0;font-size:12px;color:#1a1a1a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escapeHtml(item.nama)}</div>
                                    <div style="font-size:12px;font-weight:500;color:${item.partai === 'PKS' ? '#fe5000' : '#1a1a1a'};">${formatNumber(item.totalSuara)}</div>
                                </div>`).join('')}
                        </div>
                    </div>

                    <div style="margin-top:16px;">
                        <div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Heatmap Desa</div>
                        <div style="margin-top:8px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:6px;">
                            ${heatRows.slice(0, 20).map((row) => {
                                const intensity = row.suara / maxVillageVotes;
                                const bg = itemHeatColor(intensity, caleg.partai === 'PKS');
                                return `<div style="padding:8px;border-radius:6px;background:${bg};font-size:10px;color:#1a1a1a;"><div style="font-weight:500;">${escapeHtml(row.desa)}</div><div style="color:#666;margin-top:3px;">${formatNumber(row.suara)} suara · ${escapeHtml(row.kecamatan)}</div></div>`;
                            }).join('')}
                        </div>
                    </div>`;

                dom.calegDrawer.classList.remove('hidden', 'drawer-hidden');
                dom.calegDrawer.classList.add('drawer-visible');
                dom.calegDrawerBackdrop.classList.remove('hidden');
            }

            function openCalegDrawer(calegKey) {
                state.selectedCalegKey = calegKey;
                render();
            }

            function closeCalegDrawer(force = false) {
                if (!force) state.selectedCalegKey = '';
                dom.calegDrawer.classList.add('drawer-hidden');
                dom.calegDrawer.classList.remove('drawer-visible');
                dom.calegDrawerBackdrop.classList.add('hidden');
                window.setTimeout(() => {
                    if (!state.selectedCalegKey) dom.calegDrawer.classList.add('hidden');
                }, 180);
            }

            function bindOpenCalegButtons(container) {
                container.querySelectorAll('[data-open-caleg]').forEach((button) => {
                    button.addEventListener('click', () => openCalegDrawer(button.dataset.openCaleg));
                });
            }

            function exportVisibleCsv() {
                const scopeData = buildScopeData(state.currentDapil);
                const rows = sortCaleg(scopeData.visibleCaleg, state.currentSort.column, state.currentSort.direction);
                const csvRows = [
                    ['rank', 'nama', 'partai', 'nomor_urut', 'gender', 'suara', 'share_partai', 'basis_kuat'].join(';'),
                    ...rows.map((caleg, index) => [
                        index + 1,
                        csvEscape(caleg.nama),
                        csvEscape(caleg.partai),
                        caleg.nomorUrut,
                        caleg.gender,
                        caleg.scopedVotes,
                        (caleg.partyTotalSuara ? ((caleg.scopedVotes / caleg.partyTotalSuara) * 100).toFixed(2) : '0.00'),
                        csvEscape(getTopVillages(caleg, 3).map((row) => row.desa).join(', ')),
                    ].join(';')),
                ];
                const blob = new Blob([csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `analisa-caleg${state.currentDapil ? `-${state.currentDapil.toLowerCase().replace(/\s+/g, '-')}` : ''}.csv`;
                link.click();
                URL.revokeObjectURL(url);
            }

            function resetFilters() {
                state.currentDapil = '';
                state.currentKecamatan = '';
                state.currentDesa = '';
                state.currentPartai = '';
                state.currentGender = '';
                state.searchKeyword = '';
                state.currentSort = { column: 'suara', direction: 'desc' };
                state.currentPage = 1;
                state.selectedCalegKey = '';
                state.showAllHeadToHead = false;
                dom.dapilSelect.value = '';
                dom.kecamatanSelect.value = '';
                dom.desaSelect.value = '';
                dom.partaiSelect.value = '';
                dom.genderSelect.value = '';
                dom.searchInput.value = '';
                populateKecamatanOptions();
                populateDesaOptions();
                render();
            }

            function sortCaleg(calegList, column, direction) {
                return [...calegList].sort((a, b) => {
                    let valA;
                    let valB;
                    switch (column) {
                        case 'nama': valA = a.nama; valB = b.nama; break;
                        case 'partai': valA = a.partai; valB = b.partai; break;
                        case 'nomorUrut': valA = a.nomorUrut; valB = b.nomorUrut; break;
                        case 'suara':
                        default:
                            valA = a.scopedVotes;
                            valB = b.scopedVotes;
                            break;
                    }
                    if (typeof valA === 'string') {
                        return direction === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
                    }
                    return direction === 'asc' ? valA - valB : valB - valA;
                });
            }

            function statCardHtml(label, value, subtext, isLight = false) {
                return `
                    <div class="caleg-stat-label" style="color:${isLight ? 'rgba(255,255,255,0.85)' : '#666'};">${escapeHtml(label)}</div>
                    <div class="caleg-stat-value ${isLight ? 'is-light' : ''}">${escapeHtml(value)}</div>
                    <div class="caleg-stat-sub ${isLight ? 'is-light' : ''}">${escapeHtml(subtext)}</div>`;
            }

            function renderSortHeader(label, column, align = 'left') {
                if (!column) {
                    return `<th style="padding:9px 10px;text-align:${align};font-size:11px;color:#666;font-weight:500;">${label}</th>`;
                }
                const isActive = state.currentSort.column === column;
                const arrow = isActive ? (state.currentSort.direction === 'asc' ? '&#8593;' : '&#8595;') : '&#8597;';
                return `<th style="padding:9px 10px;text-align:${align};font-size:11px;color:#666;font-weight:500;"><button type="button" data-sort="${column}" class="sort-btn" style="background:none;border:none;padding:0;font:inherit;color:inherit;justify-content:${align === 'right' ? 'flex-end' : 'flex-start'};">${label}<span style="font-size:10px;color:#888;">${arrow}</span></button></th>`;
            }

            function renderRankPill(rank) {
                let bg = '#fee2e2';
                let color = '#991b1b';
                let label = `Rank ${rank}`;
                if (rank === 1) { bg = '#dcfce7'; color = '#14532d'; label = 'Rank 1'; }
                else if (rank === 2) { bg = '#dbeafe'; color = '#1e3a5f'; label = 'Rank 2'; }
                else if (rank === 3) { bg = '#fff7f1'; color = '#993c1d'; label = 'Rank 3'; }
                return `<span style="display:inline-flex;padding:4px 8px;border-radius:999px;background:${bg};color:${color};font-size:10px;font-weight:500;">${label}</span>`;
            }

            function emptyState(message) {
                return `<div style="padding:18px;border:0.5px dashed #d4d4d4;border-radius:8px;font-size:12px;color:#888;text-align:center;background:#fafafa;">${escapeHtml(message)}</div>`;
            }

            function getTopVillages(caleg, limit) {
                return Array.from(caleg.desaMap.values()).sort((a, b) => b.suara - a.suara).slice(0, limit);
            }

            function itemHeatColor(intensity, isPks) {
                if (isPks) {
                    const alpha = Math.min(0.9, 0.2 + (intensity * 0.6));
                    return `rgba(254, 80, 0, ${alpha})`;
                }
                const alpha = Math.min(0.8, 0.18 + (intensity * 0.45));
                return `rgba(26, 26, 26, ${alpha})`;
            }

            function estimatePartySeats(partyVotes, totalVotes, seatCount) {
                if (!partyVotes || !totalVotes) return 0;
                const seatEstimate = Math.round((partyVotes / totalVotes) * seatCount);
                if (seatEstimate <= 0 && (partyVotes / totalVotes) >= 0.08) return 1;
                return Math.max(0, seatEstimate);
            }

            function normalizePartyName(name) {
                const normalized = String(name || '').trim();
                if (normalized === 'NasDem') return 'Nasdem';
                return normalized;
            }

            function formatNumber(value) {
                return new Intl.NumberFormat('id-ID').format(Number.isFinite(Number(value)) ? Number(value) : 0);
            }

            function formatPercent(value) {
                return `${(Number(value || 0) * 100).toFixed(1)}%`;
            }

            function numberValue(value) {
                const normalized = String(value ?? '').replace(/\./g, '').replace(',', '.').trim();
                const parsed = Number(normalized);
                return Number.isFinite(parsed) ? parsed : 0;
            }

            function toTitleCase(value) {
                return String(value || '').toLowerCase().replace(/\b\w/g, (char) => char.toUpperCase());
            }

            function formatGender(value) {
                return value === 'P' ? 'Perempuan' : 'Laki-laki';
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function csvEscape(value) {
                return `"${String(value ?? '').replace(/"/g, '""')}"`;
            }

            function truncate(value, length) {
                const stringValue = String(value || '');
                return stringValue.length > length ? `${stringValue.slice(0, length)}...` : stringValue;
            }

            function compareNatural(a, b) {
                return String(a).localeCompare(String(b), 'id-ID', { numeric: true, sensitivity: 'base' });
            }

            function getPartyColor(partai) {
                return partyColors[partai] || '#64748b';
            }

            function getOrCreate(map, key, factory) {
                if (!map.has(key)) map.set(key, factory());
                return map.get(key);
            }

            const statusConfig = {
                'JAGA KUAT': { color: '#15803d', bg: '#dcfce7', text: '#14532d', dot: '#15803d', label: 'Jaga Kuat', criteria: 'PKS rank 1 & share ≥30%', description: 'PKS unggul jelas. Fokus menjaga basis, merawat struktur, dan mengunci loyalitas.' },
                'AMANKAN': { color: '#65a30d', bg: '#ecfccb', text: '#3f6212', dot: '#65a30d', label: 'Amankan', criteria: 'PKS rank 1, share <30%', description: 'PKS sudah unggul tetapi margin belum tebal. Perlu pengamanan suara dan penguatan tokoh lokal.' },
                'REBUT REALISTIS': { color: '#2563eb', bg: '#dbeafe', text: '#1e3a5f', dot: '#2563eb', label: 'Rebut Realistis', criteria: 'PKS rank 2 & gap ≤5%', description: 'PKS belum unggul, tetapi jaraknya tipis dan realistis untuk direbut dengan kerja terfokus.' },
                'GARAP INTENSIF': { color: '#d97706', bg: '#fff7f1', text: '#993c1d', dot: '#d97706', label: 'Garap Intensif', criteria: 'PKS rank ≤3 atau share ≥12%', description: 'Potensi ada, namun butuh kerja lapangan yang lebih rapat, terukur, dan konsisten.' },
                'ZONA BERAT': { color: '#b91c1c', bg: '#fee2e2', text: '#991b1b', dot: '#b91c1c', label: 'Zona Berat', criteria: 'PKS share <12% & rank >3', description: 'PKS masih lemah di wilayah ini. Prioritasnya membangun fondasi, jaringan, dan pengenalan.' }
            };

            const mapConfigs = {
                'BEKASI 1': { image: '/images/peta/dapil1.png', villages: [
                    { name: 'CIJENGKOL', district: 'SETU', x: 18.2, y: 18.6 }, { name: 'LUBANGBUAYA', district: 'SETU', x: 22.1, y: 9.2 }, { name: 'CIBENING', district: 'SETU', x: 29.8, y: 28.7 }, { name: 'BURANGKENG', district: 'SETU', x: 12.6, y: 26.7 }, { name: 'TAMAN SARI', district: 'SETU', x: 15.0, y: 39.0 }, { name: 'TAMAN RAHAYU', district: 'SETU', x: 4.5, y: 39.4 }, { name: 'CIKARAGEMAN', district: 'SETU', x: 14.6, y: 48.3 }, { name: 'RAGEMANUNGGAL', district: 'SETU', x: 9.8, y: 54.9 }, { name: 'MUKTIJAYA', district: 'SETU', x: 20.4, y: 56.1 }, { name: 'CILEDUG', district: 'SETU', x: 22.4, y: 30.5 }, { name: 'KERTARAHAYU', district: 'SETU', x: 27.0, y: 43.5 }, { name: 'JAYAMULYA', district: 'SERANG BARU', x: 32.8, y: 56.1 }, { name: 'JAYASAMPURNA', district: 'SERANG BARU', x: 35.0, y: 44.7 }, { name: 'SUKARAGAM', district: 'SERANG BARU', x: 44.0, y: 53.3 }, { name: 'SUKASARI', district: 'SERANG BARU', x: 46.5, y: 46.8 }, { name: 'SIRNAJAYA', district: 'SERANG BARU', x: 40.9, y: 58.4 }, { name: 'CILANGKARA', district: 'SERANG BARU', x: 55.1, y: 44.6 }, { name: 'NAGACIPTA', district: 'SERANG BARU', x: 51.1, y: 60.2 }, { name: 'NAGASARI', district: 'SERANG BARU', x: 51.9, y: 56.0 }, { name: 'CICAU', district: 'CIKARANG PUSAT', x: 61.6, y: 35.3 }, { name: 'SUKAMAHI', district: 'CIKARANG PUSAT', x: 70.8, y: 37.5 }, { name: 'JAYAMUKTI', district: 'CIKARANG PUSAT', x: 74.0, y: 15.8 }, { name: 'HEGARMUKTI', district: 'CIKARANG PUSAT', x: 75.5, y: 23.7 }, { name: 'PASIRANJI', district: 'CIKARANG PUSAT', x: 82.2, y: 31.0 }, { name: 'PASIRTANJUNG', district: 'CIKARANG PUSAT', x: 86.9, y: 24.2 }, { name: 'CIBATU', district: 'CIKARANG SELATAN', x: 74.1, y: 44.5 }, { name: 'CIANTRA', district: 'CIKARANG SELATAN', x: 64.9, y: 44.3 }, { name: 'SUKASEJATI', district: 'CIKARANG SELATAN', x: 82.8, y: 58.8 }, { name: 'SUKADAMI', district: 'CIKARANG SELATAN', x: 65.9, y: 61.0 }, { name: 'SUKARESMI', district: 'CIKARANG SELATAN', x: 74.8, y: 67.0 }, { name: 'SERANG', district: 'CIKARANG SELATAN', x: 74.0, y: 55.8 }, { name: 'PASIRSARI', district: 'CIKARANG SELATAN', x: 82.9, y: 44.4 }, { name: 'CIBARUSAH JAYA', district: 'CIBARUSAH', x: 31.3, y: 76.0 }, { name: 'CIBARUSAH KOTA', district: 'CIBARUSAH', x: 40.2, y: 75.8 }, { name: 'SINDANGMULYA', district: 'CIBARUSAH', x: 33.2, y: 67.8 }, { name: 'WIBAWAMULYA', district: 'CIBARUSAH', x: 43.8, y: 68.4 }, { name: 'RIDOGALIH', district: 'CIBARUSAH', x: 47.9, y: 85.6 }, { name: 'RIDOMANAH', district: 'CIBARUSAH', x: 58.8, y: 77.0 }, { name: 'SIRNAJATI', district: 'CIBARUSAH', x: 37.9, y: 83.6 }, { name: 'MEDALKRISNA', district: 'BOJONGMANGU', x: 64.8, y: 69.9 }, { name: 'SUKAMUKTI', district: 'BOJONGMANGU', x: 75.0, y: 71.1 }, { name: 'SUKABUNGAH', district: 'BOJONGMANGU', x: 84.1, y: 75.2 }, { name: 'KARANGINDAH', district: 'BOJONGMANGU', x: 62.0, y: 86.1 }, { name: 'BOJONGMANGU', district: 'BOJONGMANGU', x: 70.9, y: 80.2 }, { name: 'KARANGMULYA', district: 'BOJONGMANGU', x: 79.6, y: 91.7 }
                ]},
                'BEKASI 4': { image: '/images/peta/dapil4.png', villages: [
                    { name: 'SUKARINGIN', district: 'SUKAWANGI', x: 34.0, y: 8.8 }, { name: 'SUKATENANG', district: 'SUKAWANGI', x: 22.2, y: 17.2 }, { name: 'SUKAKERTA', district: 'SUKAWANGI', x: 51.8, y: 18.0 }, { name: 'SUKAWANGI', district: 'SUKAWANGI', x: 60.3, y: 29.4 }, { name: 'SUKABUDI', district: 'SUKAWANGI', x: 47.2, y: 37.2 }, { name: 'SUKADAYA', district: 'SUKAWANGI', x: 41.8, y: 41.5 }, { name: 'SUKAMEKAR', district: 'SUKAWANGI', x: 27.5, y: 38.7 }, { name: 'SUKABAKTI', district: 'TAMBELANG', x: 56.2, y: 50.0 }, { name: 'SUKAMAJU', district: 'TAMBELANG', x: 66.2, y: 45.2 }, { name: 'SUKAMANTRI', district: 'TAMBELANG', x: 68.8, y: 41.6 }, { name: 'SUKARAHAYU', district: 'TAMBELANG', x: 61.2, y: 44.7 }, { name: 'SUKARAJA', district: 'TAMBELANG', x: 62.8, y: 51.6 }, { name: 'SUKARAPIH', district: 'TAMBELANG', x: 66.7, y: 53.1 }, { name: 'SUKAWIJAYA', district: 'TAMBELANG', x: 74.1, y: 47.8 }, { name: 'SRIAMUR', district: 'TAMBUN UTARA', x: 13.7, y: 56.4 }, { name: 'SRIJAYA', district: 'TAMBUN UTARA', x: 28.1, y: 53.2 }, { name: 'SRIMAHI', district: 'TAMBUN UTARA', x: 21.7, y: 60.6 }, { name: 'SRIMUKTI', district: 'TAMBUN UTARA', x: 20.4, y: 49.1 }, { name: 'SATRIAMEKAR', district: 'TAMBUN UTARA', x: 14.0, y: 70.3 }, { name: 'JEJALENJAYA', district: 'TAMBUN UTARA', x: 23.6, y: 69.1 }, { name: 'SATRIAJAYA', district: 'TAMBUN UTARA', x: 18.6, y: 84.8 }, { name: 'KARANGSATRIA', district: 'TAMBUN UTARA', x: 10.7, y: 96.2 }, { name: 'BANJARSARI', district: 'SUKATANI', x: 67.2, y: 78.2 }, { name: 'SUKAASIH', district: 'SUKATANI', x: 61.1, y: 89.7 }, { name: 'SUKADARMA', district: 'SUKATANI', x: 83.6, y: 62.2 }, { name: 'SUKAHURIP', district: 'SUKATANI', x: 73.5, y: 57.7 }, { name: 'SUKAMANAH', district: 'SUKATANI', x: 65.8, y: 60.8 }, { name: 'SUKAMULYA', district: 'SUKATANI', x: 79.4, y: 72.6 }, { name: 'SUKARUKUN', district: 'SUKATANI', x: 70.2, y: 97.4 }
                ]}
            };

            function slugifyLabel(value) {
                return String(value || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
            }

            function getVisibleVillages() {
                if (!state.dataset || !state.currentDapil) return [];
                const dapilObj = state.dataset.dapils.get(state.currentDapil);
                if (!dapilObj) return [];
                
                return Array.from(dapilObj.villagePartyMap.entries()).map(([key, value]) => {
                    const parts = key.split('__');
                    const district = parts[1];
                    const village = parts[2];
                    
                    const pksVotes = value.partyTotals.get('PKS')?.suara || 0;
                    
                    const sortedParties = Array.from(value.partyTotals.values()).sort((a,b) => b.suara - a.suara);
                    const isPksFirst = sortedParties[0]?.partai === 'PKS';
                    const isPksSecond = sortedParties[1]?.partai === 'PKS';
                    let status = 'ZONA BERAT';
                    if (isPksFirst) {
                        status = sortedParties[0].suara > (sortedParties[1]?.suara || 0) * 1.5 ? 'JAGA KUAT' : 'AMANKAN';
                    } else if (isPksSecond) {
                        status = 'REBUT REALISTIS';
                    } else {
                        status = 'GARAP INTENSIF';
                    }
                    
                    return {
                        key,
                        district: district.toUpperCase(),
                        village: village.toUpperCase(),
                        label: value.desa,
                        analytics: {
                            pksVotes,
                            status
                        }
                    };
                });
            }

            function getMapState() {
                const level = state.currentDapil ? 'dapil' : 'kabupaten';
                if (level === 'kabupaten') return { title: 'Kabupaten Bekasi', image: '/images/peta/kabupaten-bekasi.png', hasImage: true, markers: [] };
                
                const config = mapConfigs[state.currentDapil];
                if (!config) return { title: `Dapil ${state.currentDapil.replace('BEKASI ', '')}`, image: `/images/peta/dapil${state.currentDapil.replace('BEKASI ', '')}.png`, hasImage: true, markers: [] };
                
                const visibleVillages = getVisibleVillages();
                const points = new Map(config.villages.map((point) => [`${normalizeKey(point.name)}|${normalizeKey(point.district)}`, point]));
                const maxVotes = Math.max(1, ...visibleVillages.map((village) => village.analytics.pksVotes));
                const markers = visibleVillages.map((village) => {
                    const point = points.get(`${normalizeKey(village.village)}|${normalizeKey(village.district)}`);
                    if (!point) return null;
                    return {
                        key: village.key,
                        label: `${village.label} · ${formatNumber(village.analytics.pksVotes)} suara PKS`,
                        x: point.x,
                        y: point.y,
                        size: 10 + Math.round((village.analytics.pksVotes / maxVotes) * 18),
                        color: statusConfig[village.analytics.status].dot
                    };
                }).filter(Boolean);
                
                return { title: `Dapil ${state.currentDapil.replace('BEKASI ', '')}`, image: config.image, hasImage: true, markers };
            }

            function renderMap() {
                const mapState = getMapState();
                const legend = Object.values(statusConfig).map((item) => `<div style="display:flex;align-items:center;gap:4px;"><span style="width:7px;height:7px;border-radius:50%;background:${item.dot};display:inline-block;"></span>${item.label}</div>`).join('');
                const markerHtml = mapState.markers.map((marker) => `<div title="${escapeHtml(marker.label)}" style="position:absolute;left:${marker.x}%;top:${marker.y}%;transform:translate(-50%,-50%);width:${marker.size}px;height:${marker.size}px;border-radius:50%;border:1.5px solid rgba(255,255,255,0.95);background:${marker.color};box-shadow:0 3px 8px rgba(0,0,0,0.18);"></div>`).join('');
                
                return `<div id="mapPanelCard" style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;display:flex;flex-direction:column;height:100%;box-sizing:border-box;"><div style="margin-bottom:10px;"><div style="font-size:11px;color:#fe5000;font-weight:500;letter-spacing:0.8px;text-transform:uppercase;">Peta Wilayah</div><div style="font-size:14px;color:#1a1a1a;font-weight:500;margin-top:2px;">${escapeHtml(mapState.title)}</div></div><div id="mapPanelMedia" style="position:relative;width:100%;flex:1 1 auto;min-height:280px;border-radius:8px;overflow:hidden;border:0.5px solid #d4d4d4;background:#e8efe0;"><img src="${escapeHtml(mapState.image)}" style="width:100%;height:100%;object-fit:contain;${mapState.hasImage ? '' : 'display:none;'}" alt="Peta" onerror="this.style.display='none'; this.parentElement.querySelector('[data-map-placeholder]').style.display='flex';"><div style="position:absolute;inset:0;">${markerHtml}</div><div data-map-placeholder style="position:absolute;inset:0;display:${mapState.hasImage ? 'none' : 'flex'};align-items:center;justify-content:center;color:#888;font-size:11px;"><div style="text-align:center;">Peta belum tersedia</div></div><div style="position:absolute;bottom:8px;left:8px;background:rgba(255,255,255,0.95);padding:5px 8px;border-radius:5px;font-size:9px;display:flex;gap:6px;border:0.5px solid #e5e5e5;flex-wrap:wrap;max-width:82%;">${legend}</div><div style="position:absolute;right:8px;bottom:8px;background:rgba(255,255,255,0.95);padding:5px 8px;border-radius:5px;font-size:9px;color:#666;border:0.5px solid #e5e5e5;">Ukuran = suara PKS</div></div></div>`;
            }

            window.initAnalisaCaleg = init;

            if (!window.analisaCalegListenerRegistered) {
                window.analisaCalegListenerRegistered = true;
                document.addEventListener('livewire:navigated', () => {
                    if (typeof window.initAnalisaCaleg === 'function' && document.getElementById('calegRankingWrap')) {
                        window.initAnalisaCaleg();
                    }
                });
            }

            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                init();
            } else {
                document.addEventListener('DOMContentLoaded', init);
            }
        })();
    </script>
    </flux:main>
</x-layouts.app.sidebar>
