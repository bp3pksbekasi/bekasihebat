const statusConfig = {
                'JAGA KUAT': { color: '#15803d', bg: '#dcfce7', text: '#14532d', label: 'Jaga Kuat' },
                'AMANKAN': { color: '#65a30d', bg: '#ecfccb', text: '#3f6212', label: 'Amankan' },
                'REBUT REALISTIS': { color: '#2563eb', bg: '#dbeafe', text: '#1e3a5f', label: 'Rebut Realistis' },
                'GARAP INTENSIF': { color: '#d97706', bg: '#fff7f1', text: '#993c1d', label: 'Garap Intensif' },
                'ZONA BERAT': { color: '#b91c1c', bg: '#fee2e2', text: '#991b1b', label: 'Zona Berat' },
            };

            const aksiConfig = {
                'JAGA KUAT': {
                    prioritas: 'PERTAHANKAN',
                    warna: '#15803d',
                    bg: '#dcfce7',
                    text: '#14532d',
                    frekuensi: 'Rutin bulanan',
                    pic: 'Ketua Ranting + Kader Senior',
                    targetUtama: 'Pertahankan share >=30%, zero penurunan suara di TPS manapun',
                    pesan: 'Wilayah sudah kuat. Fokus menjaga loyalitas, merawat struktur, jangan sampai lengah.',
                    programs: [
                        { nama: 'Konsolidasi kader aktif per RT', kategori: 'Organisasi', target: '1 kader aktif per RT', deadline: 'Bulan 1-2' },
                        { nama: 'Pendataan pemilih loyal by name & address', kategori: 'Data', target: '80% pemilih loyal terdata', deadline: 'Bulan 1-3' },
                        { nama: 'Silaturahmi rutin tokoh masyarakat', kategori: 'Jaringan', target: '2x per bulan', deadline: 'Rutin' },
                        { nama: 'Program bantuan sosial berkelanjutan', kategori: 'Sosial', target: '1 kegiatan per bulan', deadline: 'Rutin' },
                        { nama: 'Monitoring ancaman kompetitor masuk', kategori: 'Intelijen', target: 'Laporan bulanan', deadline: 'Rutin' },
                        { nama: 'Pengajian/diskusi rutin warga', kategori: 'Keagamaan', target: '2x per bulan', deadline: 'Rutin' },
                    ],
                },
                'AMANKAN': {
                    prioritas: 'PERKUAT MARGIN',
                    warna: '#65a30d',
                    bg: '#ecfccb',
                    text: '#3f6212',
                    frekuensi: '2x sebulan',
                    pic: 'Ketua Ranting + Koordinator RW',
                    targetUtama: 'Naikkan share ke >=30% (upgrade ke JAGA KUAT)',
                    pesan: 'Sudah unggul tapi margin tipis. Perkuat basis dan ekspansi ke RT yang belum tergarap.',
                    programs: [
                        { nama: 'Penguatan tokoh RT/RW sebagai influencer lokal', kategori: 'Jaringan', target: '1 tokoh per RT aktif', deadline: 'Bulan 1-2' },
                        { nama: 'Ekspansi ke RT yang masih lemah', kategori: 'Ekspansi', target: '50% RT lemah terjangkau', deadline: 'Bulan 2-4' },
                        { nama: 'Rekrutmen relawan baru per RT', kategori: 'Organisasi', target: '2 relawan baru per RT', deadline: 'Bulan 1-3' },
                        { nama: 'Program bantuan spesifik komunitas', kategori: 'Sosial', target: '1 program per 2 bulan', deadline: 'Bulan 2+' },
                        { nama: 'Door-to-door di area swing voters', kategori: 'Kampanye', target: 'Jangkau 100 KK per bulan', deadline: 'Bulan 3+' },
                        { nama: 'Pendataan pemilih potensial', kategori: 'Data', target: 'Database 60% pemilih', deadline: 'Bulan 1-4' },
                    ],
                },
                'REBUT REALISTIS': {
                    prioritas: 'REBUT & MENANGKAN',
                    warna: '#2563eb',
                    bg: '#dbeafe',
                    text: '#1e3a5f',
                    frekuensi: '3-4x sebulan',
                    pic: 'Tim Khusus Dapil + Caleg PKS',
                    targetUtama: 'Tutup gap suara, targetkan rank 1 (upgrade ke AMANKAN/JAGA KUAT)',
                    pesan: 'Jarak tipis dan realistis direbut. Ini wilayah dengan ROI tertinggi untuk alokasi resources.',
                    programs: [
                        { nama: 'Identifikasi & mapping swing voters per RT', kategori: 'Data', target: 'Data 200 swing voters', deadline: 'Bulan 1' },
                        { nama: 'Kampanye door-to-door intensif', kategori: 'Kampanye', target: '150 KK per minggu', deadline: 'Bulan 1-6' },
                        { nama: 'Program populis quick-win (bantuan langsung)', kategori: 'Sosial', target: '1 program per bulan', deadline: 'Bulan 1+' },
                        { nama: 'Kolaborasi tokoh masyarakat netral', kategori: 'Jaringan', target: '3 tokoh netral per RW', deadline: 'Bulan 1-3' },
                        { nama: 'Counter narasi kompetitor', kategori: 'Komunikasi', target: '1 konten per minggu', deadline: 'Rutin' },
                        { nama: 'Mobilisasi pemilih (transport, reminder)', kategori: 'Logistik', target: 'Rencana mobilisasi 100% TPS', deadline: 'Bulan 6' },
                        { nama: 'Rekrutmen saksi TPS yang solid', kategori: 'Organisasi', target: '2 saksi per TPS', deadline: 'Bulan 4-6' },
                    ],
                },
                'GARAP INTENSIF': {
                    prioritas: 'GARAP & BANGUN',
                    warna: '#d97706',
                    bg: '#fff7f1',
                    text: '#993c1d',
                    frekuensi: '2x sebulan',
                    pic: 'Koordinator Kecamatan',
                    targetUtama: 'Naikkan share ke >=15% dalam 1 tahun (upgrade ke REBUT REALISTIS)',
                    pesan: 'Potensi ada tapi butuh kerja lapangan konsisten dan terukur. Fokus bangun relasi, bukan kampanye.',
                    programs: [
                        { nama: 'Bangun jaringan tokoh lokal (RT/RW/ulama)', kategori: 'Jaringan', target: '1 tokoh per RW', deadline: 'Bulan 1-3' },
                        { nama: 'Bakti sosial targeted (kesehatan/pendidikan)', kategori: 'Sosial', target: '1 baksos per kuartal', deadline: 'Kuartal' },
                        { nama: 'Diskusi warga rutin di musholla/pos RT', kategori: 'Keagamaan', target: '1x per bulan', deadline: 'Rutin' },
                        { nama: 'Rekrut kader dari komunitas lokal', kategori: 'Organisasi', target: '5 kader per RW', deadline: 'Bulan 3-6' },
                        { nama: 'Branding PKS via kegiatan nyata (bukan banner)', kategori: 'Komunikasi', target: '1 kegiatan berdampak per bulan', deadline: 'Rutin' },
                    ],
                },
                'ZONA BERAT': {
                    prioritas: 'BANGUN FONDASI',
                    warna: '#b91c1c',
                    bg: '#fee2e2',
                    text: '#991b1b',
                    frekuensi: '1x sebulan',
                    pic: 'DPC + Kader Sukarela',
                    targetUtama: 'Minimal 1 kontak person per RW, target share 5% dalam 2 tahun',
                    pesan: 'Investasi jangka panjang. Jangan target tinggi dulu, fokus bangun fondasi dan kenal warga.',
                    programs: [
                        { nama: 'Survey awal: kenapa PKS lemah di sini?', kategori: 'Data', target: '1 laporan survey', deadline: 'Bulan 1' },
                        { nama: 'Cari 1 pintu masuk (tokoh/isu lokal)', kategori: 'Jaringan', target: '1 kontak person per RW', deadline: 'Bulan 1-2' },
                        { nama: 'Kegiatan keagamaan/sosial sederhana', kategori: 'Sosial', target: '1 kegiatan per kuartal', deadline: 'Kuartal' },
                        { nama: 'Pengenalan wajah caleg/tokoh PKS ke warga', kategori: 'Komunikasi', target: '1 kegiatan perkenalan', deadline: 'Bulan 2-3' },
                        { nama: 'Identifikasi 3 isu lokal yang bisa digarap PKS', kategori: 'Data', target: '3 isu teridentifikasi', deadline: 'Bulan 1-2' },
                    ],
                },
            };

            const dptFileCandidates = {
                'BEKASI 1': ['/data/pemilu/dpt_pileg2024_bekasi%201.csv', '/data/pemilu/dpt_dapil1_rt_rw.csv'],
                'BEKASI 2': ['/data/pemilu/dpt_pileg2024_bekasi%202.csv', '/data/pemilu/dpt_dapil2_rt_rw.csv'],
                'BEKASI 3': ['/data/pemilu/dpt_pileg2024_bekasi%203.csv', '/data/pemilu/dpt_dapil3_rt_rw.csv'],
                'BEKASI 4': ['/data/pemilu/dpt_pileg2024_bekasi%204.csv', '/data/pemilu/dpt_dapil4_rt_rw.csv'],
                'BEKASI 5': ['/data/pemilu/dpt_pileg2024_bekasi%205.csv', '/data/pemilu/dpt_dapil5_rt_rw.csv'],
                'BEKASI 6': ['/data/pemilu/dpt_pileg2024_bekasi%206.csv', '/data/pemilu/dpt_dapil6_rt_rw.csv'],
                'BEKASI 7': ['/data/pemilu/dpt_pileg2024_bekasi%207.csv', '/data/pemilu/dpt_dapil7_rt_rw.csv'],
            };

            const aksiState = {};
            const groupState = {};
            const appState = {
                tpsDataset: null,
                dptDatasets: {},
                currentDapil: '',
                currentKecamatan: '',
                currentStatus: '',
                searchKeyword: '',
                selectedRwKey: '',
                searchDebounceId: null,
            };

            const dom = {};

            document.addEventListener('DOMContentLoaded', async () => {
                cacheDom();
                bindEvents();
                await autoLoadData();
            });

            function cacheDom() {
                [
                    'dapilSelect', 'kecamatanSelect', 'statusSelect', 'searchInput', 'exportAllBtn',
                    'pageHeading', 'pageSubheading', 'infoBannerWrap',
                    'cardTotalRw', 'cardTotalProgram', 'cardProgress', 'cardKendala',
                    'progressBarsWrap', 'rwTableTitle', 'rwTableWrap', 'printAllBtn',
                    'aksiDrawerBackdrop', 'aksiDrawer', 'drawerTitle', 'drawerSubtitle', 'drawerStatusBadge', 'drawerContent', 'drawerCloseBtn',
                ].forEach((id) => { dom[id] = document.getElementById(id); });
            }

            function bindEvents() {
                dom.dapilSelect.addEventListener('change', async () => {
                    appState.currentDapil = dom.dapilSelect.value;
                    appState.currentKecamatan = '';
                    appState.currentStatus = '';
                    appState.searchKeyword = '';
                    dom.kecamatanSelect.value = '';
                    dom.statusSelect.value = '';
                    dom.searchInput.value = '';
                    closeAksiDrawer(true);
                    if (appState.currentDapil && !appState.dptDatasets[appState.currentDapil]) {
                        await loadSingleDpt(appState.currentDapil);
                    }
                    render();
                });

                dom.kecamatanSelect.addEventListener('change', () => {
                    appState.currentKecamatan = dom.kecamatanSelect.value;
                    render();
                });

                dom.statusSelect.addEventListener('change', () => {
                    appState.currentStatus = dom.statusSelect.value;
                    render();
                });

                dom.searchInput.addEventListener('input', (event) => {
                    clearTimeout(appState.searchDebounceId);
                    appState.searchDebounceId = window.setTimeout(() => {
                        appState.searchKeyword = event.target.value.trim();
                        render();
                    }, 300);
                });

                dom.exportAllBtn.addEventListener('click', () => exportAksiExcel(null));
                dom.printAllBtn.addEventListener('click', () => printAksi(null));
                dom.drawerCloseBtn.addEventListener('click', () => closeAksiDrawer());
                dom.aksiDrawerBackdrop.addEventListener('click', () => closeAksiDrawer());
            }

            async function autoLoadData() {
                const tpsText = await fetchText('/data/pemilu/tps_dprd.csv');
                appState.tpsDataset = buildDataset(parseSemicolonCsv(tpsText || ''));
                const dapilKeys = Array.from(appState.tpsDataset?.dapils.keys() || []).sort(compareNatural);
                await Promise.all(dapilKeys.map((dapil) => loadSingleDpt(dapil)));
                const availableDapils = dapilKeys.filter((dapil) => appState.dptDatasets[dapil]?.available);
                appState.currentDapil = availableDapils[0] || dapilKeys[0] || '';
                populateDapilSelect(dapilKeys, availableDapils);
                render();
            }

            async function loadSingleDpt(dapil) {
                if (appState.dptDatasets[dapil]) return;
                const urls = dptFileCandidates[dapil] || [];
                for (const url of urls) {
                    const text = await fetchText(url);
                    if (text) {
                        appState.dptDatasets[dapil] = buildDptDataset(parseFlexibleCsv(text));
                        appState.dptDatasets[dapil].available = true;
                        appState.dptDatasets[dapil].source = url;
                        return;
                    }
                }
                appState.dptDatasets[dapil] = { available: false, villageMap: new Map(), totalDpt: 0, totalTps: 0, totalVillages: 0, generation: {} };
            }

            async function fetchText(url) {
                try {
                    const response = await fetch(url);
                    if (!response.ok) return null;
                    return await response.text();
                } catch (error) {
                    return null;
                }
            }

            function populateDapilSelect(dapils, availableDapils) {
                dom.dapilSelect.innerHTML = dapils.map((dapil) => {
                    const disabled = availableDapils.includes(dapil) ? '' : ' data-no-dpt="1"';
                    const label = availableDapils.includes(dapil) ? toTitleCase(dapil) : `${toTitleCase(dapil)} (tanpa DPT)`;
                    return `<option value="${escapeHtml(dapil)}"${disabled}>${escapeHtml(label)}</option>`;
                }).join('');
                dom.dapilSelect.value = appState.currentDapil;
            }

            function parseSemicolonCsv(text) {
                return parseDelimitedCsv(text, ';');
            }

            function parseFlexibleCsv(text) {
                const normalized = String(text || '').replace(/^\uFEFF/, '').trim();
                if (!normalized) return [];
                const firstLine = normalized.split(/\r?\n/)[0] || '';
                const delimiter = firstLine.includes(';') ? ';' : ',';
                return parseDelimitedCsv(normalized, delimiter);
            }

            function parseDelimitedCsv(text, delimiter) {
                const normalized = String(text || '').replace(/^\uFEFF/, '').trim();
                if (!normalized) return [];
                const lines = normalized.split(/\r?\n/).filter(Boolean);
                if (!lines.length) return [];
                const headers = splitCsvLine(lines.shift(), delimiter).map((header) => normalizeHeader(header));
                return lines.map((line) => {
                    const values = splitCsvLine(line, delimiter);
                    return headers.reduce((acc, header, index) => {
                        acc[header] = String(values[index] ?? '').trim();
                        return acc;
                    }, {});
                });
            }

            function splitCsvLine(line, delimiter) {
                const values = [];
                let current = '';
                let inQuotes = false;
                for (let index = 0; index < line.length; index += 1) {
                    const char = line[index];
                    const next = line[index + 1];
                    if (char === '"') {
                        if (inQuotes && next === '"') {
                            current += '"';
                            index += 1;
                        } else {
                            inQuotes = !inQuotes;
                        }
                    } else if (char === delimiter && !inQuotes) {
                        values.push(current);
                        current = '';
                    } else {
                        current += char;
                    }
                }
                values.push(current);
                return values;
            }

            function normalizeHeader(value) {
                return String(value || '').trim().toLowerCase().replace(/\s+/g, '_');
            }

            function normalizeKey(value) {
                return String(value || '')
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-zA-Z0-9]+/g, ' ')
                    .trim()
                    .toUpperCase();
            }

            function buildDataset(rows) {
                const dapils = new Map();
                rows.forEach((row) => {
                    const nomorUrut = numberValue(row.nomor_urut);
                    const tps = normalizeKey(row.tps);
                    const isAggregate = tps === 'TPS 000' || nomorUrut === 0;
                    if (isAggregate) return;
                    if (nomorUrut > 0) return;
                    const dapil = normalizeKey(row.dapil);
                    const kecamatan = normalizeKey(row.kecamatan);
                    const tps = normalizeKey(row.tps);
                    const partyName = normalizePartyName(row.partai);
                    const partyId = String(row.partai_id || partyName);
                    const dapilObj = getOrCreate(dapils, dapil, () => ({ dapil, desaMap: new Map() }));
                    const villageKey = `${dapil}__${kecamatan}__${desa}`;
                    const village = getOrCreate(dapilObj.desaMap, villageKey, () => ({
                        key: villageKey,
                        dapil,
                        district: kecamatan,
                        districtLabel: toTitleCase(kecamatan),
                        village: desa,
                        label: toTitleCase(desa),
                        totalTps: 0,
                        partyMap: new Map(),
                        tpsMap: new Map(),
                    }));
                    const tpsObj = getOrCreate(village.tpsMap, tps, () => ({ key: tps, partyMap: new Map() }));
                    if (tpsObj.partyMap.size === 0) village.totalTps += 1;
                    mergePartyVotes(tpsObj.partyMap, partyId, partyName, suara);
                    mergePartyVotes(village.partyMap, partyId, partyName, suara);
                });
                return { dapils };
            }

            function buildDptDataset(rows) {
                const villageMap = new Map();
                let totalDpt = 0;
                rows.forEach((row) => {
                    const dapil = normalizeKey(row.dapil || row.scope || row.dapil_name || '');
                    const kecamatan = normalizeKey(row.kecamatan || row.district);
                    const desa = normalizeKey(row.desa || row.village);
                    if (!kecamatan || !desa) return;
                    const tps = normalizeKey(row.tps || row.tps_no || 'TPS TANPA NAMA');
                    const rw = padRwRt(row.rw);
                    const hasExplicitGender = String(row.gender || '').trim() !== '';
                    const male = hasExplicitGender ? numberValue(row.male || (String(row.gender).toUpperCase() === 'L' ? 1 : 0)) : 0;
                    const female = hasExplicitGender ? numberValue(row.female || (String(row.gender).toUpperCase() === 'P' ? 1 : 0)) : 0;
                    const isVoterLevelRow = Boolean(String(row.pid || row.nama || '').trim());
                    const rowDpt = isVoterLevelRow ? 1 : Math.max(1, numberValue(row.dpt || row.dpt_tot || row.total || 1));
                    const villageKey = `${dapil || appState.currentDapil || 'UNKNOWN'}__${kecamatan}__${desa}`;
                    const village = getOrCreate(villageMap, villageKey, () => ({
                        key: villageKey,
                        dapil,
                        district: kecamatan,
                        village: desa,
                        totalDpt: 0,
                        totalTps: 0,
                        rwMap: new Map(),
                        tpsMap: new Map(),
                    }));
                    village.totalDpt += rowDpt;
                    totalDpt += rowDpt;
                    const tpsObj = getOrCreate(village.tpsMap, tps, () => ({ key: tps, totalDpt: 0, rows: [], rwMap: new Map() }));
                    if (tpsObj.rows.length === 0) village.totalTps += 1;
                    tpsObj.totalDpt += rowDpt;
                    tpsObj.rows.push({ district: kecamatan, village: desa, tps, rw, dpt: rowDpt, male, female });
                    const rwObj = getOrCreate(village.rwMap, rw, () => ({ rw, totalDpt: 0, male: 0, female: 0, tpsSet: new Set() }));
                    rwObj.totalDpt += rowDpt;
                    rwObj.male += male;
                    rwObj.female += female;
                    rwObj.tpsSet.add(tps);
                    const tpsRw = getOrCreate(tpsObj.rwMap, rw, () => ({ rw, totalDpt: 0, male: 0, female: 0, tpsSet: new Set() }));
                    tpsRw.totalDpt += rowDpt;
                    tpsRw.male += male;
                    tpsRw.female += female;
                    tpsRw.tpsSet.add(tps);
                });
                return { available: rows.length > 0, totalDpt, totalTps: sumMap(villageMap, (village) => village.totalTps), totalVillages: villageMap.size, generation: {}, villageMap };
            }

            function analyzePks(partyMap, totalTps) {
                const parties = Array.from(partyMap.values()).sort((a, b) => b.partyVotes - a.partyVotes);
                const totalVotes = parties.reduce((sum, party) => sum + party.partyVotes, 0);
                const pks = parties.find((party) => party.partyName === 'PKS');
                const pksVotes = pks?.partyVotes || 0;
                const share = totalVotes ? (pksVotes / totalVotes) : 0;
                const rank = pks ? (parties.findIndex((party) => party.partyName === 'PKS') + 1) : (parties.length + 1);
                const gapShare = totalVotes && parties[0] ? ((parties[0].partyVotes - pksVotes) / totalVotes) : 1;
                const status = classifyPriority({ pksVotes, share, rank, gapShare });
                return { parties, totalVotes, pksVotes, share, rank, gapShare, status, totalTps };
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

            function buildDptScopeData(targetVillages) {
                const rwMap = new Map();
                const result = {
                    available: false,
                    totalScopeTps: 0,
                    matchedTps: 0,
                    missingTps: 0,
                    totalDpt: 0,
                    totalMale: 0,
                    totalFemale: 0,
                    generation: {},
                    missingVillages: [],
                    rwRows: [],
                };

                targetVillages.forEach((village) => {
                    const dptVillage = appState.dptDatasets[appState.currentDapil]?.villageMap.get(village.key);
                    if (!dptVillage) {
                        result.missingVillages.push(village.label);
                        return;
                    }
                    result.available = true;
                    result.totalDpt += dptVillage.totalDpt;

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
                            const rowKey = `${village.key}__RW__${rwKey}`;
                            const rwRow = getOrCreate(rwMap, rowKey, () => ({
                                key: rowKey,
                                dapil: village.dapil,
                                district: village.district,
                                districtLabel: village.districtLabel,
                                village: village.village,
                                villageLabel: village.label,
                                rw: rwKey,
                                totalDpt: 0,
                                male: 0,
                                female: 0,
                                tpsSet: new Set(),
                                partyMap: new Map(),
                            }));
                            rwRow.totalDpt += rwEntry.totalDpt;
                            rwRow.male += rwEntry.male;
                            rwRow.female += rwEntry.female;
                            rwRow.tpsSet.add(tpsKey);
                            addScaledPartyMap(rwRow.partyMap, voteTps.partyMap, share);
                        });
                    });
                });

                result.rwRows = transformEstimatedRows(rwMap);
                return result;
            }

            function transformEstimatedRows(groupMap) {
                return Array.from(groupMap.values()).map((row) => {
                    const analytics = analyzePks(row.partyMap, row.tpsSet.size);
                    return {
                        ...row,
                        analytics,
                        totalVotes: analytics.totalVotes,
                        estPks: analytics.pksVotes,
                        share: analytics.share,
                        status: analytics.status,
                    };
                }).sort((a, b) => b.estPks - a.estPks || compareNatural(a.key, b.key));
            }

            function addScaledPartyMap(targetMap, sourceMap, factor) {
                sourceMap.forEach((entry, key) => {
                    const target = getOrCreate(targetMap, key, () => ({ partyId: entry.partyId, partyName: entry.partyName, partyVotes: 0 }));
                    target.partyVotes += entry.partyVotes * factor;
                });
            }

            function buildAksiScopeData(dapilKey) {
                const dapilObj = appState.tpsDataset?.dapils.get(dapilKey);
                const dptDataset = appState.dptDatasets[dapilKey];
                if (!dapilObj || !dptDataset?.available) {
                    return {
                        hasDpt: false,
                        rwRows: [],
                        groupedRows: [],
                        statusSummary: createStatusSummary([]),
                        totalPrograms: 0,
                        completedPrograms: 0,
                        kendalaCount: 0,
                        totalRw: 0,
                        totalVillages: 0,
                        totalDistricts: 0,
                    };
                }

                const targetVillages = Array.from(dapilObj.desaMap.values()).filter((village) => {
                    if (appState.currentKecamatan && village.district !== appState.currentKecamatan) return false;
                    const dptVillage = dptDataset.villageMap.get(village.key);
                    if (!dptVillage) return false;
                    const analytics = analyzePks(village.partyMap, village.totalTps);
                    if (appState.currentStatus && analytics.status !== appState.currentStatus) return false;
                    if (appState.searchKeyword) {
                        const keyword = normalizeKey(appState.searchKeyword);
                        if (!(village.village.includes(keyword) || village.district.includes(keyword))) return false;
                    }
                    return true;
                });

                const dptScope = buildDptScopeData(targetVillages);
                let totalPrograms = 0;
                let completedPrograms = 0;
                let kendalaCount = 0;

                const rwRows = dptScope.rwRows.filter((row) => {
                    if (appState.currentKecamatan && row.district !== appState.currentKecamatan) return false;
                    if (appState.currentStatus && row.status !== appState.currentStatus) return false;
                    if (appState.searchKeyword) {
                        const keyword = normalizeKey(appState.searchKeyword);
                        if (!(row.village.includes(keyword) || row.rw.includes(keyword))) return false;
                    }
                    return true;
                }).map((row) => {
                    const aksi = aksiConfig[row.status] || aksiConfig['ZONA BERAT'];
                    const state = getAksiState(row.key, row.status);
                    totalPrograms += state.programs.length;
                    completedPrograms += state.programs.filter((program) => program.status === 'selesai').length;
                    kendalaCount += state.programs.filter((program) => program.status === 'kendala').length;
                    const progress = summarizePrograms(state.programs);
                    return {
                        ...row,
                        aksi,
                        aksiState: state,
                        progress,
                    };
                });

                const groupedRows = buildGroupedRows(rwRows);
                return {
                    hasDpt: true,
                    rwRows,
                    groupedRows,
                    statusSummary: createStatusSummary(rwRows),
                    totalPrograms,
                    completedPrograms,
                    kendalaCount,
                    totalRw: rwRows.length,
                    totalVillages: new Set(rwRows.map((row) => row.village)).size,
                    totalDistricts: new Set(rwRows.map((row) => row.district)).size,
                };
            }

            function getAksiState(rwKey, statusKey) {
                if (!aksiState[rwKey]) {
                    const programs = (aksiConfig[statusKey] || aksiConfig['ZONA BERAT']).programs.map((program) => ({
                        ...program,
                        status: 'belum',
                        catatan: '',
                        expanded: false,
                    }));
                    aksiState[rwKey] = { programs };
                }
                return aksiState[rwKey];
            }

            function updateProgramStatus(rwKey, programIndex) {
                const state = aksiState[rwKey];
                if (!state?.programs[programIndex]) return;
                const cycle = { belum: 'berjalan', berjalan: 'selesai', selesai: 'kendala', kendala: 'belum' };
                state.programs[programIndex].status = cycle[state.programs[programIndex].status] || 'belum';
                render();
                if (appState.selectedRwKey === rwKey) openAksiDrawer(rwKey);
            }

            function updateProgramNote(rwKey, programIndex, note) {
                const state = aksiState[rwKey];
                if (!state?.programs[programIndex]) return;
                state.programs[programIndex].catatan = note;
            }

            function toggleProgramNote(rwKey, programIndex) {
                const state = aksiState[rwKey];
                if (!state?.programs[programIndex]) return;
                state.programs[programIndex].expanded = !state.programs[programIndex].expanded;
                if (appState.selectedRwKey === rwKey) openAksiDrawer(rwKey);
            }

            function exportAksiExcel(rwKey) {
                const scopeData = buildAksiScopeData(appState.currentDapil);
                const targetRows = rwKey ? scopeData.rwRows.filter((row) => row.key === rwKey) : scopeData.rwRows;
                const rows = [
                    ['Dapil', 'Kecamatan', 'Desa', 'RW', 'Status Wilayah', 'DPT', 'Est.PKS', 'No', 'Program', 'Kategori', 'Target', 'Deadline', 'PIC', 'Status Pelaksanaan', 'Catatan'].join(';'),
                ];
                targetRows.forEach((row) => {
                    row.aksiState.programs.forEach((program, index) => {
                        rows.push([
                            csvEscape(toTitleCase(row.dapil)),
                            csvEscape(row.districtLabel),
                            csvEscape(row.villageLabel),
                            csvEscape(`RW ${row.rw}`),
                            csvEscape(row.status),
                            row.totalDpt,
                            Math.round(row.estPks),
                            index + 1,
                            csvEscape(program.nama),
                            csvEscape(program.kategori),
                            csvEscape(program.target),
                            csvEscape(program.deadline || ''),
                            csvEscape(row.aksi.pic),
                            csvEscape(program.status),
                            csvEscape(program.catatan || ''),
                        ].join(';'));
                    });
                });
                const blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = `Rencana_Aksi_${(rwKey ? 'rw' : appState.currentDapil || 'scope').replace(/\s+/g, '_')}_${formatDateFile(new Date())}.csv`;
                link.click();
                URL.revokeObjectURL(link.href);
            }

            function printAksi(rwKey) {
                const scopeData = buildAksiScopeData(appState.currentDapil);
                const targetRows = rwKey ? scopeData.rwRows.filter((row) => row.key === rwKey) : scopeData.rwRows;
                const html = targetRows.map((row) => `
                    <div style="margin-bottom:24px;page-break-inside:avoid;">
                        <h2 style="margin:0 0 6px;font-size:16px;">Rencana Aksi - RW ${escapeHtml(row.rw)} - ${escapeHtml(row.villageLabel)}, ${escapeHtml(row.districtLabel)}</h2>
                        <div style="font-size:12px;color:#444;margin-bottom:8px;">Status: ${escapeHtml(row.status)} | DPT: ${formatNumber(row.totalDpt)} | Est. PKS: ~${formatNumber(Math.round(row.estPks))} | Tanggal cetak: ${formatDateDisplay(new Date())}</div>
                        <div style="font-size:12px;color:#444;margin-bottom:10px;">Prioritas: ${escapeHtml(row.aksi.prioritas)} | PIC: ${escapeHtml(row.aksi.pic)} | Frekuensi: ${escapeHtml(row.aksi.frekuensi)} | Target: ${escapeHtml(row.aksi.targetUtama)}</div>
                        <table style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th style="border:1px solid #ccc;padding:6px;text-align:left;font-size:11px;">O</th>
                                    <th style="border:1px solid #ccc;padding:6px;text-align:left;font-size:11px;">No</th>
                                    <th style="border:1px solid #ccc;padding:6px;text-align:left;font-size:11px;">Program</th>
                                    <th style="border:1px solid #ccc;padding:6px;text-align:left;font-size:11px;">Kategori</th>
                                    <th style="border:1px solid #ccc;padding:6px;text-align:left;font-size:11px;">Target</th>
                                    <th style="border:1px solid #ccc;padding:6px;text-align:left;font-size:11px;">Deadline</th>
                                    <th style="border:1px solid #ccc;padding:6px;text-align:left;font-size:11px;">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${row.aksiState.programs.map((program, index) => `
                                    <tr>
                                        <td style="border:1px solid #ccc;padding:6px;font-size:11px;">&#9633;</td>
                                        <td style="border:1px solid #ccc;padding:6px;font-size:11px;">${index + 1}</td>
                                        <td style="border:1px solid #ccc;padding:6px;font-size:11px;">${escapeHtml(program.nama)}</td>
                                        <td style="border:1px solid #ccc;padding:6px;font-size:11px;">${escapeHtml(program.kategori)}</td>
                                        <td style="border:1px solid #ccc;padding:6px;font-size:11px;">${escapeHtml(program.target)}</td>
                                        <td style="border:1px solid #ccc;padding:6px;font-size:11px;">${escapeHtml(program.deadline || '')}</td>
                                        <td style="border:1px solid #ccc;padding:6px;font-size:11px;">${escapeHtml(program.catatan || '')}</td>
                                    </tr>`).join('')}
                            </tbody>
                        </table>
                        <div style="margin-top:8px;font-size:11px;color:#666;">${escapeHtml(row.aksi.pesan)}</div>
                    </div>`).join('');
                const printWindow = window.open('', '_blank', 'width=900,height=700');
                if (!printWindow) return;
                printWindow.document.write(`<html><head><title>Rencana Aksi</title></head><body style="font-family:Arial,sans-serif;padding:20px;">${html}</body></html>`);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
            }

            function render() {
                if (!appState.currentDapil) return;
                const scopeData = buildAksiScopeData(appState.currentDapil);
                populateKecamatan(scopeData.rwRows);
                renderHeader(scopeData);
                renderInfoBanner(scopeData);
                renderSummaryCards(scopeData);
                renderProgressBars(scopeData);
                renderRwTable(scopeData);
                renderAksiDrawer(appState.selectedRwKey, scopeData.rwRows.find((row) => row.key === appState.selectedRwKey));
            }

            function renderHeader(scopeData) {
                dom.pageHeading.textContent = `Rencana Aksi - ${toTitleCase(appState.currentDapil)}`;
                dom.pageSubheading.textContent = scopeData.hasDpt
                    ? `Program kerja lapangan per RW berdasarkan status wilayah Pemilu 2024 Â· Strategi pemenangan 2029`
                    : `Data DPT belum tersedia untuk ${toTitleCase(appState.currentDapil)}.`;
                dom.rwTableTitle.textContent = `${formatNumber(scopeData.totalRw)} RW Â· Klik baris untuk lihat & kelola program`;
            }

            function renderInfoBanner(scopeData) {
                if (scopeData.hasDpt) {
                    dom.infoBannerWrap.innerHTML = '';
                    return;
                }
                dom.infoBannerWrap.innerHTML = `<div style="background:#fff7f1;border:0.5px solid #fce4ce;border-radius:10px;padding:12px;font-size:12px;color:#993c1d;">Data DPT belum tersedia untuk dapil ini. Tab RW/RT memerlukan data DPT untuk menentukan program aksi per RW. Upload file DPT via menu Sumber Data atau minta admin menyediakan.</div>`;
            }

            function renderSummaryCards(scopeData) {
                const avgPrograms = scopeData.totalRw ? (scopeData.totalPrograms / scopeData.totalRw) : 0;
                const progressPct = scopeData.totalPrograms ? Math.round((scopeData.completedPrograms / scopeData.totalPrograms) * 100) : 0;
                dom.cardTotalRw.innerHTML = statCardHtml('Total RW', formatNumber(scopeData.totalRw), `${formatNumber(scopeData.totalVillages)} desa Â· ${formatNumber(scopeData.totalDistricts)} kecamatan`);
                dom.cardTotalProgram.innerHTML = statCardHtml('Total Program', formatNumber(scopeData.totalPrograms), `Rata-rata ${avgPrograms.toFixed(1)} program/RW`);
                dom.cardProgress.innerHTML = statCardHtml('Progress', `${progressPct}%`, `${formatNumber(scopeData.completedPrograms)} / ${formatNumber(scopeData.totalPrograms)} selesai`, true);
                dom.cardKendala.innerHTML = statCardHtml('Terkendala', formatNumber(scopeData.kendalaCount), 'Program perlu perhatian', false, '#b91c1c');
            }

            function renderProgressBars(scopeData) {
                dom.progressBarsWrap.innerHTML = Object.entries(statusConfig).map(([key, config]) => {
                    const item = scopeData.statusSummary[key];
                    const percent = item.totalPrograms ? Math.round((item.completedPrograms / item.totalPrograms) * 100) : 0;
                    return `<div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                        <div style="width:120px;display:flex;align-items:center;gap:6px;">
                            <div style="width:8px;height:8px;border-radius:50%;background:${config.color};"></div>
                            <span style="font-size:12px;font-weight:500;color:#1a1a1a;">${config.label}</span>
                        </div>
                        <div style="width:45px;font-size:11px;color:#888;text-align:right;">${formatNumber(item.rwCount)} RW</div>
                        <div style="flex:1;height:20px;background:#f0f0f0;border-radius:4px;overflow:hidden;">
                            <div style="height:100%;width:${percent}%;background:${config.color};border-radius:4px;display:flex;align-items:center;padding-left:8px;">
                                <span style="color:white;font-size:10px;font-weight:500;">${percent}%</span>
                            </div>
                        </div>
                        <div style="width:70px;font-size:11px;color:#666;text-align:right;">${formatNumber(item.completedPrograms)} / ${formatNumber(item.totalPrograms)}</div>
                    </div>`;
                }).join('');
            }

            function renderRwTable(scopeData) {
                if (!scopeData.hasDpt) {
                    dom.rwTableWrap.innerHTML = `<div style="padding:18px;border:0.5px dashed #d4d4d4;border-radius:10px;font-size:12px;color:#888;text-align:center;background:#fafafa;">Belum ada data RW untuk scope ini.</div>`;
                    return;
                }

                const body = scopeData.groupedRows.map((group) => {
                    const expanded = groupState[group.groupKey] !== false;
                    const header = `<tr data-group-toggle="${escapeHtml(group.groupKey)}" style="background:#fafafa;cursor:pointer;"><td colspan="7" style="padding:8px;font-weight:500;font-size:12px;color:#1a1a1a;"><span style="display:inline-block;width:12px;">${expanded ? '&#9662;' : '&#9656;'}</span> ${escapeHtml(group.districtLabel)} Â· ${escapeHtml(group.villageLabel)} <span style="font-size:10px;color:#888;margin-left:8px;">(${formatNumber(group.rows.length)} RW)</span></td></tr>`;
                    const rows = expanded ? group.rows.map((row) => {
                        const actionLabel = row.progress.percent >= 100 ? 'âœ“ Selesai' : (row.progress.percent > 0 ? 'Kelola' : 'Mulai');
                        const borderColor = row.progress.percent >= 100 ? '#15803d' : (row.progress.percent > 0 ? '#d4d4d4' : '#fe5000');
                        const textColor = row.progress.percent >= 100 ? '#15803d' : (row.progress.percent > 0 ? '#666' : '#fe5000');
                        const fontWeight = row.progress.percent > 0 ? '400' : '500';
                        return `<tr data-rw-row="${escapeHtml(row.key)}" style="cursor:pointer;border-bottom:0.5px solid #eee;">
                            <td style="padding:8px 8px 8px 32px;color:#444;font-size:12px;">RW ${escapeHtml(row.rw)}</td>
                            <td style="text-align:center;padding:8px;">${renderStatusPill(row.status)}</td>
                            <td style="text-align:right;padding:8px;font-size:12px;color:#444;">${formatNumber(row.totalDpt)}</td>
                            <td style="text-align:right;padding:8px;font-size:12px;color:#fe5000;font-weight:500;">~${formatNumber(Math.round(row.estPks))}</td>
                            <td style="text-align:center;padding:8px;font-size:12px;color:#444;">${formatNumber(row.aksiState.programs.length)}</td>
                            <td style="text-align:center;padding:8px;">
                                <div style="display:flex;align-items:center;gap:6px;justify-content:center;">
                                    <div style="width:50px;height:6px;background:#f0f0f0;border-radius:3px;overflow:hidden;">
                                        <div style="width:${row.progress.percent}%;height:100%;background:${row.aksi.warna};border-radius:3px;"></div>
                                    </div>
                                    <span style="font-size:10px;color:${row.aksi.warna};font-weight:500;">${row.progress.done}/${row.progress.total}</span>
                                </div>
                            </td>
                            <td style="text-align:center;padding:8px;">
                                <button type="button" data-open-aksi="${escapeHtml(row.key)}" style="padding:3px 8px;border:0.5px solid ${borderColor};border-radius:4px;font-size:10px;background:white;cursor:pointer;color:${textColor};font-weight:${fontWeight};">${actionLabel}</button>
                            </td>
                        </tr>`;
                    }).join('') : '';
                    return header + rows;
                }).join('');

                dom.rwTableWrap.innerHTML = `<div style="overflow:auto;border:0.5px solid #e5e5e5;border-radius:10px;"><table style="width:100%;border-collapse:collapse;background:white;"><thead><tr style="background:#fafafa;"><th style="padding:8px;text-align:left;font-size:11px;color:#666;font-weight:500;">Kecamatan / Desa / RW</th><th style="padding:8px;text-align:center;font-size:11px;color:#666;font-weight:500;">Status</th><th style="padding:8px;text-align:right;font-size:11px;color:#666;font-weight:500;">DPT</th><th style="padding:8px;text-align:right;font-size:11px;color:#666;font-weight:500;">Est. PKS</th><th style="padding:8px;text-align:center;font-size:11px;color:#666;font-weight:500;">Program</th><th style="padding:8px;text-align:center;font-size:11px;color:#666;font-weight:500;">Progress</th><th style="padding:8px;text-align:center;font-size:11px;color:#666;font-weight:500;">Aksi</th></tr></thead><tbody>${body || `<tr><td colspan="7" style="padding:18px;text-align:center;font-size:12px;color:#888;">Belum ada RW sesuai filter.</td></tr>`}</tbody></table></div>`;

                dom.rwTableWrap.querySelectorAll('[data-group-toggle]').forEach((row) => {
                    row.addEventListener('click', () => {
                        const key = row.dataset.groupToggle;
                        groupState[key] = !(groupState[key] !== false);
                        render();
                    });
                });

                dom.rwTableWrap.querySelectorAll('[data-open-aksi]').forEach((button) => {
                    button.addEventListener('click', (event) => {
                        event.stopPropagation();
                        openAksiDrawer(button.dataset.openAksi);
                    });
                });

                dom.rwTableWrap.querySelectorAll('[data-rw-row]').forEach((row) => {
                    row.addEventListener('click', () => openAksiDrawer(row.dataset.rwRow));
                });
            }

            function renderAksiDrawer(rwKey, rwData) {
                if (!rwKey || !rwData) {
                    closeAksiDrawer(true);
                    return;
                }
                const badge = statusConfig[rwData.status] || statusConfig['ZONA BERAT'];
                dom.drawerTitle.textContent = `RW ${rwData.rw} - ${rwData.villageLabel}, ${rwData.districtLabel}`;
                dom.drawerSubtitle.textContent = `DPT: ${formatNumber(rwData.totalDpt)} Â· Est. PKS: ~${formatNumber(Math.round(rwData.estPks))} Â· ${formatNumber(rwData.tpsSet.size)} TPS`;
                dom.drawerStatusBadge.style.background = badge.bg;
                dom.drawerStatusBadge.style.color = badge.text;
                dom.drawerStatusBadge.querySelector('i').style.background = badge.color;
                dom.drawerStatusBadge.querySelector('span').textContent = badge.label;

                dom.drawerContent.innerHTML = `
                    <div style="background:${rwData.aksi.bg};border:0.5px solid ${rwData.aksi.warna}30;border-radius:8px;padding:10px;margin-bottom:10px;">
                        <div style="font-size:11px;color:${rwData.aksi.warna};font-weight:500;">${escapeHtml(rwData.aksi.prioritas)} Â· ${escapeHtml(rwData.aksi.frekuensi)} Â· PIC: ${escapeHtml(rwData.aksi.pic)}</div>
                        <div style="font-size:11px;color:#444;margin-top:4px;">${escapeHtml(rwData.aksi.pesan)}</div>
                    </div>
                    ${renderDrawerChecklist(rwKey, rwData.aksiState.programs, rwData)}
                    <div style="display:flex;gap:6px;margin-top:10px;">
                        <button type="button" id="drawerExportBtn" style="flex:1;padding:6px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:11px;background:white;cursor:pointer;">&#128229; Excel</button>
                        <button type="button" id="drawerPrintBtn" style="flex:1;padding:6px;border:0.5px solid #d4d4d4;border-radius:6px;font-size:11px;background:white;cursor:pointer;">&#128424; Print</button>
                    </div>`;

                dom.aksiDrawer.classList.remove('hidden');
                dom.aksiDrawerBackdrop.classList.remove('hidden');
                dom.aksiDrawer.style.transform = 'translateX(0)';

                dom.drawerContent.querySelectorAll('[data-program-index]').forEach((row) => {
                    row.addEventListener('click', () => updateProgramStatus(rwKey, Number(row.dataset.programIndex)));
                    row.addEventListener('dblclick', (event) => {
                        event.stopPropagation();
                        toggleProgramNote(rwKey, Number(row.dataset.programIndex));
                    });
                    row.addEventListener('contextmenu', (event) => {
                        event.preventDefault();
                        toggleProgramNote(rwKey, Number(row.dataset.programIndex));
                    });
                });

                dom.drawerContent.querySelectorAll('[data-program-note]').forEach((textarea) => {
                    textarea.addEventListener('input', (event) => updateProgramNote(rwKey, Number(event.target.dataset.programNote), event.target.value));
                });

                document.getElementById('drawerExportBtn')?.addEventListener('click', () => exportAksiExcel(rwKey));
                document.getElementById('drawerPrintBtn')?.addEventListener('click', () => printAksi(rwKey));
            }

            function renderDrawerChecklist(rwKey, programs, rwData) {
                const progress = summarizePrograms(programs);
                const color = rwData.aksi.warna;
                return `${programs.map((program, index) => {
                    const states = {
                        selesai: { bg: '#f0fdf4', border: '#bbf7d0', iconBg: '#15803d', label: 'Selesai', labelColor: '#16a34a', icon: '&#10003;', strike: true },
                        berjalan: { bg: '#fff7ed', border: '#fed7aa', iconBg: '#d97706', label: 'Berjalan', labelColor: '#d97706', icon: '&#10227;', strike: false },
                        kendala: { bg: '#fef2f2', border: '#fecaca', iconBg: '#dc2626', label: 'Kendala', labelColor: '#dc2626', icon: '!', strike: false },
                        belum: { bg: 'white', border: '#e5e5e5', iconBg: 'transparent', label: 'Belum', labelColor: '#888', icon: '', strike: false },
                    };
                    const current = states[program.status] || states.belum;
                    const icon = program.status === 'belum'
                        ? `<div style="width:14px;height:14px;border-radius:3px;border:1.5px solid #d4d4d4;flex-shrink:0;"></div>`
                        : `<div style="width:14px;height:14px;border-radius:3px;background:${current.iconBg};display:flex;align-items:center;justify-content:center;flex-shrink:0;"><span style="color:white;font-size:8px;">${current.icon}</span></div>`;
                    return `<div style="margin-bottom:4px;">
                        <div data-program-index="${index}" style="display:flex;align-items:center;gap:8px;padding:6px 8px;background:${current.bg};border-radius:6px;border:0.5px solid ${current.border};cursor:pointer;">
                            ${icon}
                            <span style="font-size:11px;flex:1;color:${current.strike ? '#888' : '#1a1a1a'};${current.strike ? 'text-decoration:line-through;' : ''}">${escapeHtml(program.nama)}</span>
                            <span style="font-size:9px;color:${current.labelColor};">${current.label}</span>
                        </div>
                        ${program.expanded ? `<div style="padding:6px 8px 6px 30px;background:#fafafa;border-top:0.5px solid #e5e5e5;"><textarea data-program-note="${index}" placeholder="Catatan pelaksanaan..." style="width:100%;border:0.5px solid #d4d4d4;border-radius:4px;padding:4px 6px;font-size:10px;resize:vertical;min-height:30px;font-family:inherit;">${escapeHtml(program.catatan || '')}</textarea></div>` : ''}
                    </div>`;
                }).join('')}
                <div style="display:flex;align-items:center;gap:8px;margin-top:10px;">
                    <div style="flex:1;height:6px;background:#f0f0f0;border-radius:3px;overflow:hidden;">
                        <div style="width:${progress.percent}%;height:100%;background:${color};border-radius:3px;"></div>
                    </div>
                    <span style="font-size:10px;color:#888;">${progress.done}/${progress.total} (${progress.percent}%)</span>
                </div>`;
            }

            function buildGroupedRows(rwRows) {
                const groups = new Map();
                rwRows.forEach((row) => {
                    const groupKey = `${row.district}__${row.village}`;
                    const group = getOrCreate(groups, groupKey, () => ({
                        groupKey,
                        districtLabel: row.districtLabel,
                        villageLabel: row.villageLabel,
                        rows: [],
                    }));
                    group.rows.push(row);
                });
                return Array.from(groups.values()).sort((a, b) => compareNatural(`${a.districtLabel} ${a.villageLabel}`, `${b.districtLabel} ${b.villageLabel}`));
            }

            function createStatusSummary(rwRows) {
                const summary = Object.keys(statusConfig).reduce((acc, key) => {
                    acc[key] = { rwCount: 0, totalPrograms: 0, completedPrograms: 0 };
                    return acc;
                }, {});
                rwRows.forEach((row) => {
                    summary[row.status].rwCount += 1;
                    summary[row.status].totalPrograms += row.aksiState.programs.length;
                    summary[row.status].completedPrograms += row.aksiState.programs.filter((program) => program.status === 'selesai').length;
                });
                return summary;
            }

            function summarizePrograms(programs) {
                const total = programs.length;
                const done = programs.filter((program) => program.status === 'selesai').length;
                return { total, done, percent: total ? Math.round((done / total) * 100) : 0 };
            }

            function openAksiDrawer(rwKey) {
                appState.selectedRwKey = rwKey;
                render();
            }

            function closeAksiDrawer(force = false) {
                if (!force) appState.selectedRwKey = '';
                dom.aksiDrawer.style.transform = 'translateX(100%)';
                dom.aksiDrawerBackdrop.classList.add('hidden');
                window.setTimeout(() => {
                    if (!appState.selectedRwKey) dom.aksiDrawer.classList.add('hidden');
                }, 180);
            }

            function populateKecamatan(rwRows) {
                const currentValue = appState.currentKecamatan;
                const options = Array.from(new Set(rwRows.map((row) => row.district))).sort(compareNatural);
                dom.kecamatanSelect.innerHTML = `<option value="">Semua kecamatan</option>${options.map((option) => `<option value="${escapeHtml(option)}">${escapeHtml(toTitleCase(option))}</option>`).join('')}`;
                dom.kecamatanSelect.value = options.includes(currentValue) ? currentValue : '';
                appState.currentKecamatan = dom.kecamatanSelect.value;
            }

            function mergePartyVotes(partyMap, partyId, partyName, suara) {
                const party = getOrCreate(partyMap, partyId, () => ({ partyId, partyName, partyVotes: 0 }));
                party.partyVotes += suara;
            }

            function statCardHtml(label, value, subtext, light = false, valueColor = '#1a1a1a') {
                return `<div class="aksi-stat-label" style="color:${light ? 'rgba(255,255,255,0.85)' : '#666'};">${escapeHtml(label)}</div><div class="aksi-stat-value ${light ? 'light' : ''}" style="color:${light ? 'white' : valueColor};">${escapeHtml(value)}</div><div class="aksi-stat-sub ${light ? 'light' : ''}">${escapeHtml(subtext)}</div>`;
            }

            function renderStatusPill(statusKey) {
                const config = statusConfig[statusKey] || statusConfig['ZONA BERAT'];
                return `<span style="display:inline-flex;align-items:center;gap:6px;padding:4px 8px;border-radius:999px;background:${config.bg};color:${config.text};font-size:10px;font-weight:500;"><span style="width:6px;height:6px;border-radius:50%;background:${config.color};display:inline-block;"></span>${config.label}</span>`;
            }

            function sumMap(map, accessor) {
                let total = 0;
                map.forEach((value) => { total += accessor(value); });
                return total;
            }

            function padRwRt(value) {
                const digits = String(value || '').replace(/\D+/g, '');
                if (!digits) return '000';
                return digits.padStart(3, '0');
            }

            function normalizePartyName(name) {
                const normalized = String(name || '').trim();
                return normalized === 'NasDem' ? 'Nasdem' : normalized;
            }

            function numberValue(value) {
                const normalized = String(value ?? '').replace(/\./g, '').replace(',', '.').trim();
                const parsed = Number(normalized);
                return Number.isFinite(parsed) ? parsed : 0;
            }

            function toTitleCase(value) {
                return String(value || '').toLowerCase().replace(/\b\w/g, (char) => char.toUpperCase());
            }

            function formatNumber(value) {
                return new Intl.NumberFormat('id-ID').format(Math.round(Number(value || 0)));
            }

            function compareNatural(a, b) {
                return String(a).localeCompare(String(b), 'id-ID', { numeric: true, sensitivity: 'base' });
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

            function formatDateFile(date) {
                return `${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}`;
            }

            function formatDateDisplay(date) {
                return new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(date);
            }

            function getOrCreate(map, key, factory) {
                if (!map.has(key)) map.set(key, factory());
                return map.get(key);
            }
