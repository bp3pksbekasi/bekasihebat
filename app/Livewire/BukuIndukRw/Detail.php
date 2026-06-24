<?php

namespace App\Livewire\BukuIndukRw;

use App\Models\DataRw;
use App\Models\ProfilRw;
use App\Models\Korwe;
use App\Models\Korte;
use App\Models\PenggalangSuara;
use Livewire\Component;

class Detail extends Component
{
    public DataRw $dataRw;
    public ?ProfilRw $profilRw = null;
    public string $activeTab = 'profil_wilayah'; // profil_wilayah, peta_politik, strategi, struktur, realisasi
    
    public bool $showProfilDrawer = false;
    public array $profilData = [];
    public array $autoFillData = [];
    public ?string $profilRwId = null;

    public array $partyRows = [];
    public array $tpsRows = [];
    public array $rtRows = [];

    // Form Infrastruktur
    public ?string $infraId = null;
    public string $infraType = 'korwe';
    public ?string $infraNama = '';
    public ?string $infraHp = '';
    public ?string $infraRt = '';
    public ?string $infraTarget = '';

    public function mount(DataRw $dataRw)
    {
        $this->dataRw = $dataRw->load(['targetWilayah']);
        $this->profilRwId = $this->dataRw->nomor_rw;
        $this->profilRw = ProfilRw::where('target_wilayah_id', $this->dataRw->target_wilayah_id)
                                  ->where('nomor_rw', $this->profilRwId)
                                  ->first() ?? new ProfilRw();

        // Fetch party rows from PemiluDesaSummary
        $period = \App\Models\PemiluPeriod::query()->where('is_default', true)->first();
        if ($period && $this->dataRw->targetWilayah) {
            $summary = \App\Models\PemiluDesaSummary::query()
                ->where('pemilu_period_id', $period->id)
                ->where('kecamatan', $this->dataRw->targetWilayah->kecamatan)
                ->where('desa', $this->dataRw->targetWilayah->desa)
                ->first();
            
            $rwRows = $summary?->rw_rows ?? [];
            foreach ($rwRows as $row) {
                if (isset($row['rw']) && ltrim($row['rw'], '0') === ltrim($this->profilRwId, '0')) {
                    $this->partyRows = $row['party_rows'] ?? [];
                    
                    $tpsList = $row['tps_list'] ?? [];
                    $allTpsRows = $summary->tps_rows ?? [];
                    $this->tpsRows = array_filter($allTpsRows, function($tpsRow) use ($tpsList) {
                        return in_array($tpsRow['label'], $tpsList);
                    });
                    
                    $allRtRows = $summary->rt_rows ?? [];
                    $rtRowsRaw = array_filter($allRtRows, function($rtRow) {
                        return isset($rtRow['rw']) && ltrim($rtRow['rw'], '0') === ltrim($this->profilRwId, '0');
                    });
                    
                    $this->rtRows = array_map(function($rtRow) {
                        $rivalCandidates = [];
                        $rivalPartyName = trim((string)($rtRow['rival_party'] ?? ''));

                        foreach ($rtRow['party_rows'] ?? [] as $pr) {
                            $partyName = trim((string)($pr['party_name'] ?? ''));
                            if (strcasecmp($partyName, $rivalPartyName) === 0) {
                                foreach ($pr['candidates'] ?? [] as $cand) {
                                    $rivalCandidates[] = [
                                        'name' => $cand['name'],
                                        'votes' => $cand['votes'],
                                        'party' => $partyName
                                    ];
                                }
                            }
                        }
                        
                        usort($rivalCandidates, fn($a, $b) => $b['votes'] <=> $a['votes']);
                        $rtRow['top_rival_candidates'] = array_slice($rivalCandidates, 0, 5);
                        $rtRow['top_parties'] = array_slice($rtRow['party_rows'] ?? [], 0, 5);
                        return $rtRow;
                    }, array_values($rtRowsRaw));
                    
                    break;
                }
            }
        }
    }

    public function openProfilDrawer(): void
    {
        $this->profilRwId = $this->dataRw->nomor_rw;
        $this->showProfilDrawer = true;

        $profil = ProfilRw::query()
            ->where('target_wilayah_id', $this->dataRw->target_wilayah_id)
            ->where('nomor_rw', $this->profilRwId)
            ->first();

        if ($profil) {
            $this->profilData = $profil->toArray();
            
            $wargaArray = [];
            foreach(\App\Models\ProfilRw::PROFIL_OPTIONS as $label) {
                if ($profil->profil_warga && str_contains($profil->profil_warga, $label)) {
                    $wargaArray[] = $label;
                }
            }
            $this->profilData['profil_warga'] = $wargaArray;
            
        } else {
            $this->emptyProfilData();
            $this->loadAutoFillData($this->profilRwId);
        }
    }

    public function closeProfilDrawer(): void
    {
        $this->showProfilDrawer = false;
    }

    public function simpanProfil(): void
    {
        $tw = $this->dataRw->targetWilayah;

        $isComplete = true;
        $requiredFields = ['tipologi', 'ekonomi_dominan', 'suara_pks_2019', 'kompetitor_status', 'tim_sukses_status'];
        foreach ($requiredFields as $field) {
            if (empty($this->profilData[$field])) {
                $isComplete = false;
                break;
            }
        }
        
        $profilDataToSave = $this->profilData;
        if (is_array($profilDataToSave['profil_warga'])) {
            $profilDataToSave['profil_warga'] = implode(', ', $profilDataToSave['profil_warga']);
        }

        $dataToSave = array_merge($profilDataToSave, [
            'target_wilayah_id' => $tw->id,
            'nomor_rw' => $this->profilRwId,
            'dapil' => $tw->dapil,
            'kecamatan' => $tw->kecamatan,
            'desa' => $tw->desa,
            'is_complete' => $isComplete,
        ]);
        
        $dataToSave['suara_pks_2019'] = (int) ($dataToSave['suara_pks_2019'] ?? 0);
        $dataToSave['jumlah_kta'] = (int) ($dataToSave['jumlah_kta'] ?? 0);

        ProfilRw::updateOrCreate(
            [
                'target_wilayah_id' => $tw->id,
                'nomor_rw' => $this->profilRwId,
            ],
            $dataToSave
        );

        $this->showProfilDrawer = false;
        
        // Reload profil data
        $this->profilRw = ProfilRw::where('target_wilayah_id', $this->dataRw->target_wilayah_id)
                                  ->where('nomor_rw', $this->profilRwId)
                                  ->first();
                                  
        $this->dispatch('saved');
    }

    private function emptyProfilData(): void
    {
        $this->profilData = [
            'tipologi' => '', 'ekonomi_dominan' => '', 'profil_warga' => [],
            'suara_pks_2019' => 0, 'jumlah_kta' => 0, 'faktor_penyebab' => '',
            'afiliasi_rw_rt' => '', 'afiliasi_posyandu_dkm' => '',
            'kompetitor_status' => 'tidak_tahu', 'kompetitor_detail' => '',
            'tim_sukses_status' => 'tidak_tahu', 'tim_sukses_detail' => '',
            'strategi' => '', 'penanggung_jawab' => '', 'keterangan_lain' => '',
        ];
    }

    private function loadAutoFillData(string $nomorRw): void
    {
        $tw = $this->dataRw->targetWilayah;
        $dataRw = DataRw::query()
            ->where('target_wilayah_id', $tw->id)
            ->where('nomor_rw', $this->dataRw->nomor_rw)
            ->first();
        $korwe = Korwe::query()
            ->where('target_wilayah_id', $tw->id)
            ->where('nomor_rw', $this->dataRw->nomor_rw)
            ->first();

        $this->autoFillData = [
            'jumlah_rt' => $dataRw?->jumlah_rt ?? 0,
            'dpt' => $dataRw?->dpt ?? 0,
            'estimasi_pks' => $dataRw?->estimasi_pks ?? 0,
            'estimasi_share' => $dataRw?->estimasi_share ?? 0,
            'estimasi_ranking' => $dataRw?->estimasi_ranking ?? 0,
            'partai_pemenang' => '-',
            'top_3_partai' => '-',
            'caleg_pks_tertinggi' => '-',
            'target_suara' => (int) round((float) $tw->target_avg_per_rw),
            'korwe_nama' => $korwe?->nama_koordinator,
            'korwe_status' => $korwe?->status,
            'status_wilayah' => $dataRw?->status_wilayah ?? 'ZONA BERAT',
        ];
    }

    public function setActiveTab(string $tab)
    {
        if (in_array($tab, ['profil_wilayah', 'peta_politik', 'strategi', 'struktur', 'realisasi'])) {
            $this->activeTab = $tab;
        }
    }

    public function simpanInfrastruktur(): void
    {
        if ($this->infraTarget === '') {
            $this->infraTarget = null;
        }
        if ($this->infraRt === '') {
            $this->infraRt = null;
        }
        if ($this->infraHp === '') {
            $this->infraHp = null;
        }

        $this->validate([
            'infraType' => 'required|in:korwe,korte,penggalang',
            'infraNama' => 'required|string|max:255',
            'infraHp' => 'nullable|string|max:20',
            'infraRt' => 'nullable|string|max:3',
            'infraTarget' => 'nullable|numeric|min:0',
        ]);

        $baseData = [
            'target_wilayah_id' => $this->dataRw->target_wilayah_id,
            'nomor_rw' => $this->profilRwId,
            'nama_koordinator' => $this->infraNama,
            'no_hp' => $this->infraHp,
        ];

        if ($this->infraType === 'korwe') {
            if (!$this->infraId) {
                $existing = Korwe::where('target_wilayah_id', $this->dataRw->target_wilayah_id)
                                 ->where('nomor_rw', $this->profilRwId)
                                 ->exists();
                if ($existing) {
                    $this->addError('infraType', 'Korwe untuk RW ini sudah terdaftar. Silakan edit data yang sudah ada.');
                    return;
                }
            }
            if ($this->infraId) {
                Korwe::where('id', $this->infraId)->update($baseData);
            } else {
                $baseData['status'] = 'aktif';
                $baseData['tanggal_terbentuk'] = now();
                $baseData['created_by'] = auth()->id();
                Korwe::create($baseData);
            }
        } elseif ($this->infraType === 'korte') {
            $nomorRt = str_pad($this->infraRt ?: '000', 3, '0', STR_PAD_LEFT);
            $baseData['nomor_rt'] = $nomorRt;
            
            if (!$this->infraId) {
                $existing = Korte::where('target_wilayah_id', $this->dataRw->target_wilayah_id)
                                 ->where('nomor_rw', $this->profilRwId)
                                 ->where('nomor_rt', $nomorRt)
                                 ->exists();
                if ($existing) {
                    $this->addError('infraRt', "Korte untuk RT $nomorRt sudah terdaftar. Silakan edit data yang sudah ada.");
                    return;
                }
            }

            if ($this->infraId) {
                Korte::where('id', $this->infraId)->update($baseData);
            } else {
                $baseData['status'] = 'aktif';
                $baseData['tanggal_terbentuk'] = now();
                $baseData['created_by'] = auth()->id();
                Korte::create($baseData);
            }
        } elseif ($this->infraType === 'penggalang') {
            $pengData = [
                'target_wilayah_id' => $this->dataRw->target_wilayah_id,
                'dapil' => $this->dataRw->targetWilayah->dapil,
                'kecamatan' => $this->dataRw->targetWilayah->kecamatan,
                'desa' => $this->dataRw->targetWilayah->desa,
                'nomor_rw' => $this->profilRwId,
                'rt' => str_pad($this->infraRt ?: '000', 3, '0', STR_PAD_LEFT),
                'nama' => $this->infraNama,
                'no_hp' => $this->infraHp,
                'no_wa' => $this->infraHp,
                'sumber' => 'warga',
                'target_jangkauan' => (int) $this->infraTarget,
            ];
            
            if ($this->infraId) {
                PenggalangSuara::where('id', $this->infraId)->update($pengData);
            } else {
                $pengData['status'] = 'aktif';
                $pengData['created_by'] = auth()->id();
                PenggalangSuara::create($pengData);
            }
        }

        $this->infraId = null;
        $this->infraNama = '';
        $this->infraHp = '';
        $this->infraRt = '';
        $this->infraTarget = '';

        session()->flash('success', 'Data infrastruktur berhasil disimpan.');
        $this->dispatch('close-infra-drawer');
    }

    public function tambahInfrastruktur()
    {
        $this->infraId = null;
        $this->infraNama = '';
        $this->infraHp = '';
        $this->infraRt = '';
        $this->infraTarget = '';
        $this->dispatch('open-infra-drawer');
    }

    public function editInfrastruktur(string $type, string $id)
    {
        $this->infraType = $type;
        $this->infraId = $id;

        if ($type === 'korwe') {
            $model = Korwe::find($id);
            $this->infraNama = $model->nama_koordinator;
            $this->infraHp = $model->no_hp;
            $this->infraRt = '';
            $this->infraTarget = '';
        } elseif ($type === 'korte') {
            $model = Korte::find($id);
            $this->infraNama = $model->nama_koordinator;
            $this->infraHp = $model->no_hp;
            $this->infraRt = ltrim($model->nomor_rt ?? '', '0');
            $this->infraTarget = '';
        } elseif ($type === 'penggalang') {
            $model = PenggalangSuara::find($id);
            $this->infraNama = $model->nama;
            $this->infraHp = $model->no_hp;
            $this->infraRt = ltrim($model->rt ?? '', '0');
            $this->infraTarget = (string) $model->target_jangkauan;
        }

        $this->dispatch('open-infra-drawer');
    }

    public function hapusInfrastruktur(string $type, string $id)
    {
        if ($type === 'korwe') {
            Korwe::destroy($id);
        } elseif ($type === 'korte') {
            Korte::destroy($id);
        } elseif ($type === 'penggalang') {
            PenggalangSuara::destroy($id);
        }
        
        session()->flash('success', 'Data berhasil dihapus.');
    }

    public function render()
    {
        $korwes = [];
        $kortes = [];
        $penggalangs = [];

        $korweCount = Korwe::where('target_wilayah_id', $this->dataRw->target_wilayah_id)
                           ->where('nomor_rw', $this->dataRw->nomor_rw)
                           ->count();

        if ($this->activeTab === 'struktur' || $this->activeTab === 'realisasi') {
            $korwes = Korwe::where('target_wilayah_id', $this->dataRw->target_wilayah_id)
                           ->where('nomor_rw', $this->dataRw->nomor_rw)
                           ->get();
            $kortes = Korte::where('target_wilayah_id', $this->dataRw->target_wilayah_id)
                           ->where('nomor_rw', $this->dataRw->nomor_rw)
                           ->get();
            $penggalangs = PenggalangSuara::where('target_wilayah_id', $this->dataRw->target_wilayah_id)
                                          ->where('nomor_rw', $this->dataRw->nomor_rw)
                                          ->orderBy('created_at', 'desc')
                                          ->get();
        }

        return view('livewire.buku-induk-rw.detail', [
            'korweCount' => $korweCount,
            'korwes' => $korwes,
            'kortes' => $kortes,
            'penggalangs' => $penggalangs,
            'targetWilayah' => $this->dataRw->targetWilayah,
        ])->layout('components.layouts.app', ['title' => 'Peta Kekuatan RW - Detail']);
    }
}
