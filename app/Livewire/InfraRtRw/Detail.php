<?php

declare(strict_types=1);

namespace App\Livewire\InfraRtRw;

use App\Models\DataRw;
use App\Models\Korte;
use App\Models\Korwe;
use App\Models\PenggalangSuara;
use App\Models\ProfilRw;
use App\Models\TargetWilayah;
use App\Models\UpaRwMember;
use Illuminate\Support\Collection;
use Livewire\Component;

class Detail extends Component
{
    public TargetWilayah $targetWilayah;

    public string $activeMainTab = 'korwe';

    public string $activeTab = 'korwe';

    public bool $showForm = false;

    public string $formNomorRw = '';

    public string $formParentRw = '';

    public string $formNamaKoordinator = '';

    public string $formNoHp = '';

    public string $formStatus = 'belum';

    public string $formCatatan = '';

    public ?string $formTanggal = null;

    public ?string $editId = null;

    public bool $showProfilDrawer = false;

    public bool $showPenggalangForm = false;

    public string $pgNama = '';

    public string $pgHp = '';

    public string $pgWa = '';

    public string $pgRw = '';

    public string $pgRt = '';

    public string $pgSumber = 'warga';

    public int $pgTarget = 10;

    public ?string $pgEditId = null;

    public bool $showUpaForm = false;

    public string $upaNama = '';

    public string $upaHp = '';

    public string $upaRw = '';

    public string $upaJabatan = 'anggota';

    public string $upaAsal = 'korwe';

    public ?string $upaKorweId = null;

    public ?string $upaKorteId = null;

    public ?string $upaEditId = null;

    public ?string $profilRwId = null;

    public string $rwStatusFilter = '';

    /**
     * @var array<string, mixed>
     */
    public array $profilData = [];

    /**
     * @var array<string, mixed>
     */
    public array $autoFillData = [];

    public function mount(TargetWilayah $targetWilayah): void
    {
        $this->loadTargetWilayah($targetWilayah);

        $tab = request()->query('tab');
        if (in_array($tab, ['korwe', 'korte', 'penggalang'], true)) {
            if ($tab === 'penggalang') {
                $this->activeMainTab = 'penggalang';
                if (request()->query('action') === 'create') {
                    $this->openPenggalangForm();
                }
            } else {
                $this->activeMainTab = 'korwe';
                $this->activeTab = $tab;
                if (request()->query('action') === 'create') {
                    $this->openCreateForm();
                }
            }
        }

        // #region debug-point A:detail-mount
        $this->reportDebug('A', 'Detail@mount', '[DEBUG] Infra RT/RW detail mounted', [
            'targetWilayahId' => $this->targetWilayah->id,
            'desa' => $this->targetWilayah->desa,
            'kecamatan' => $this->targetWilayah->kecamatan,
            'dapil' => $this->targetWilayah->dapil,
        ]);
        // #endregion
    }

    public function setActiveTab(string $tab): void
    {
        if (in_array($tab, ['korwe', 'korte'], true)) {
            $this->activeTab = $tab;
            $this->closeForm();
            // #region debug-point C:detail-tab
            $this->reportDebug('C', 'Detail@setActiveTab', '[DEBUG] Detail active tab updated', [
                'targetWilayahId' => $this->targetWilayah->id,
                'activeTab' => $this->activeTab,
            ]);
            // #endregion
        }
    }

    public function openCreateForm(?string $nomor = null, ?string $parentRw = null): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->formNomorRw = $nomor ?? '';
        $this->formParentRw = $parentRw ?? '';
        // #region debug-point C:detail-open-create
        $this->reportDebug('C', 'Detail@openCreateForm', '[DEBUG] Detail create form opened', [
            'targetWilayahId' => $this->targetWilayah->id,
            'activeTab' => $this->activeTab,
            'formNomorRw' => $this->formNomorRw,
            'formParentRw' => $this->formParentRw,
        ]);
        // #endregion
    }

    public function assignKorwe(string $nomorRw): void
    {
        $this->activeTab = 'korwe';
        $this->resetForm();
        $this->formNomorRw = $this->normalizeNumber($nomorRw);
        $this->formStatus = 'proses';
        $this->showForm = true;
    }

    public function editKorwe(string $id): void
    {
        $this->activeTab = 'korwe';
        $this->openEditForm($id);
    }

    public function openProfil(string $nomorRw): void
    {
        $this->profilRwId = $this->normalizeNumber($nomorRw);
        $this->showProfilDrawer = true;

        $profil = ProfilRw::query()
            ->where('target_wilayah_id', $this->targetWilayah->id)
            ->where('nomor_rw', $this->profilRwId)
            ->first();

        if ($profil instanceof ProfilRw) {
            $this->profilData = $profil->only([
                'tipologi',
                'ekonomi_dominan',
                'profil_warga',
                'suara_pks_2019',
                'faktor_penyebab',
                'anggota_pks',
                'jumlah_kta',
                'upa_rw_status',
                'upa_rw_nama',
                'rki_status',
                'rki_nama',
                'senam_status',
                'senam_nama',
                'relawan_milenial_status',
                'relawan_milenial_nama',
                'caleg_terpilih_ada',
                'caleg_terpilih_nama',
                'afiliasi_rw_rt',
                'afiliasi_posyandu_dkm',
                'kompetitor_status',
                'kompetitor_detail',
                'tim_sukses_status',
                'tim_sukses_detail',
                'strategi',
                'penanggung_jawab',
                'keterangan_lain',
            ]);
        } else {
            $this->profilData = $this->emptyProfilData();
        }

        $this->loadAutoFillData($this->profilRwId);
    }

    public function simpanProfil(): void
    {
        if ($this->profilRwId === null) {
            return;
        }

        $payload = array_merge($this->emptyProfilData(), $this->profilData, [
            'target_wilayah_id' => $this->targetWilayah->id,
            'nomor_rw' => $this->profilRwId,
            'dapil' => $this->targetWilayah->dapil,
            'kecamatan' => $this->targetWilayah->kecamatan,
            'desa' => $this->targetWilayah->desa,
            'filled_by' => auth()->id(),
            'filled_at' => now(),
            'suara_pks_2019' => (int) ($this->profilData['suara_pks_2019'] ?? 0),
            'jumlah_kta' => (int) ($this->profilData['jumlah_kta'] ?? 0),
            'caleg_terpilih_ada' => filter_var($this->profilData['caleg_terpilih_ada'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ]);

        $profil = ProfilRw::query()->updateOrCreate(
            [
                'target_wilayah_id' => $this->targetWilayah->id,
                'nomor_rw' => $this->profilRwId,
            ],
            $payload
        );

        $completion = $profil->calculateCompletion();

        $profil->update([
            'completion_percent' => $completion,
            'is_complete' => $completion >= 80,
        ]);

        $this->loadTargetWilayah($this->targetWilayah->fresh());
        $this->showProfilDrawer = false;
        session()->flash('success', 'Profil RW ' . $this->profilRwId . ' berhasil disimpan.');
        $this->closeProfilDrawer();
    }

    public function closeProfilDrawer(): void
    {
        $this->showProfilDrawer = false;
        $this->profilRwId = null;
        $this->profilData = [];
        $this->autoFillData = [];
    }

    public function setRwStatusFilter(string $status = ''): void
    {
        if ($status !== '' && ! array_key_exists($status, TargetWilayah::STATUS_CONFIG)) {
            return;
        }

        $this->rwStatusFilter = $status;
    }

    public function openEditForm(string $id): void
    {
        $record = $this->activeTab === 'korwe'
            ? $this->targetWilayah->korwes->firstWhere('id', $id)
            : $this->targetWilayah->kortes->firstWhere('id', $id);

        if (! $record) {
            // #region debug-point C:detail-open-edit-miss
            $this->reportDebug('C', 'Detail@openEditForm', '[DEBUG] Detail edit record not found', [
                'targetWilayahId' => $this->targetWilayah->id,
                'activeTab' => $this->activeTab,
                'editId' => $id,
            ]);
            // #endregion
            return;
        }

        $this->resetForm();
        $this->showForm = true;
        $this->editId = $record->id;
        $this->formNamaKoordinator = (string) ($record->nama_koordinator ?? '');
        $this->formNoHp = (string) ($record->no_hp ?? '');
        $this->formStatus = (string) $record->status;
        $this->formCatatan = (string) ($record->catatan ?? '');
        $this->formTanggal = optional($record->tanggal_terbentuk)?->format('Y-m-d');

        if ($this->activeTab === 'korwe') {
            $this->formNomorRw = (string) $record->nomor_rw;
            $this->formParentRw = '';
        } else {
            $this->formNomorRw = (string) $record->nomor_rt;
            $this->formParentRw = (string) $record->nomor_rw;
        }
        // #region debug-point C:detail-open-edit
        $this->reportDebug('C', 'Detail@openEditForm', '[DEBUG] Detail edit form opened', [
            'targetWilayahId' => $this->targetWilayah->id,
            'activeTab' => $this->activeTab,
            'editId' => $this->editId,
            'formNomorRw' => $this->formNomorRw,
            'formParentRw' => $this->formParentRw,
            'formStatus' => $this->formStatus,
        ]);
        // #endregion
    }

    public function closeForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
        // #region debug-point C:detail-close-form
        $this->reportDebug('C', 'Detail@closeForm', '[DEBUG] Detail form closed', [
            'targetWilayahId' => $this->targetWilayah->id,
            'activeTab' => $this->activeTab,
        ]);
        // #endregion
    }

    public function simpan(): void
    {
        $validated = $this->validate($this->rules(), [], $this->validationAttributes());
        $normalizedNomor = $this->normalizeNumber($validated['formNomorRw']);
        $normalizedParentRw = $this->activeTab === 'korte'
            ? $this->normalizeNumber($validated['formParentRw'])
            : null;

        $payload = [
            'nama_koordinator' => $validated['formNamaKoordinator'] !== '' ? $validated['formNamaKoordinator'] : null,
            'no_hp' => $validated['formNoHp'] !== '' ? $validated['formNoHp'] : null,
            'status' => strtolower($validated['formStatus']),
            'catatan' => $validated['formCatatan'] !== '' ? $validated['formCatatan'] : null,
            'tanggal_terbentuk' => strtolower($validated['formStatus']) === 'terbentuk' ? $validated['formTanggal'] : null,
            'created_by' => auth()->id(),
        ];

        if ($this->activeTab === 'korwe') {
            $duplicate = Korwe::query()
                ->where('target_wilayah_id', $this->targetWilayah->id)
                ->where('nomor_rw', $normalizedNomor)
                ->when($this->editId, fn ($query) => $query->where('id', '!=', $this->editId))
                ->exists();

            if ($duplicate) {
                $this->addError('formNomorRw', 'Nomor RW sudah digunakan.');
                return;
            }

            if ($this->editId) {
                Korwe::query()
                    ->where('target_wilayah_id', $this->targetWilayah->id)
                    ->findOrFail($this->editId)
                    ->update([
                        'nomor_rw' => $normalizedNomor,
                        ...$payload,
                    ]);
            } else {
                Korwe::query()->create([
                    'target_wilayah_id' => $this->targetWilayah->id,
                    'nomor_rw' => $normalizedNomor,
                    ...$payload,
                ]);
            }

            session()->flash('success', 'Data KORWE berhasil disimpan.');
        } else {
            $duplicate = Korte::query()
                ->where('target_wilayah_id', $this->targetWilayah->id)
                ->where('nomor_rw', $normalizedParentRw)
                ->where('nomor_rt', $normalizedNomor)
                ->when($this->editId, fn ($query) => $query->where('id', '!=', $this->editId))
                ->exists();

            if ($duplicate) {
                $this->addError('formNomorRw', 'Nomor RT pada RW tersebut sudah digunakan.');
                return;
            }

            if ($this->editId) {
                Korte::query()
                    ->where('target_wilayah_id', $this->targetWilayah->id)
                    ->findOrFail($this->editId)
                    ->update([
                        'nomor_rw' => $normalizedParentRw,
                        'nomor_rt' => $normalizedNomor,
                        ...$payload,
                    ]);
            } else {
                Korte::query()->create([
                    'target_wilayah_id' => $this->targetWilayah->id,
                    'nomor_rw' => $normalizedParentRw,
                    'nomor_rt' => $normalizedNomor,
                    ...$payload,
                ]);
            }

            session()->flash('success', 'Data KORTE berhasil disimpan.');
        }

        // #region debug-point C:detail-save
        $this->reportDebug('C', 'Detail@simpan', '[DEBUG] Detail data saved', [
            'targetWilayahId' => $this->targetWilayah->id,
            'activeTab' => $this->activeTab,
            'editId' => $this->editId,
            'nomor' => $this->formNomorRw,
            'parentRw' => $this->formParentRw,
            'status' => $this->formStatus,
        ]);
        // #endregion

        $this->loadTargetWilayah($this->targetWilayah->fresh());
        $this->closeForm();
    }

    public function hapus(string $id): void
    {
        $model = $this->activeTab === 'korwe' ? Korwe::class : Korte::class;

        $record = $model::query()
            ->where('target_wilayah_id', $this->targetWilayah->id)
            ->find($id);

        if (! $record) {
            // #region debug-point C:detail-delete-miss
            $this->reportDebug('C', 'Detail@hapus', '[DEBUG] Detail delete record not found', [
                'targetWilayahId' => $this->targetWilayah->id,
                'activeTab' => $this->activeTab,
                'deleteId' => $id,
            ]);
            // #endregion
            return;
        }

        $record->delete();

        // #region debug-point C:detail-delete
        $this->reportDebug('C', 'Detail@hapus', '[DEBUG] Detail data deleted', [
            'targetWilayahId' => $this->targetWilayah->id,
            'activeTab' => $this->activeTab,
            'deleteId' => $id,
        ]);
        // #endregion

        session()->flash('success', $this->activeTab === 'korwe'
            ? 'Data KORWE berhasil dihapus.'
            : 'Data KORTE berhasil dihapus.');

        $this->loadTargetWilayah($this->targetWilayah->fresh());
        $this->closeForm();
    }

    public function getActiveYearProperty(): int
    {
        return max(2026, min(2029, (int) now()->format('Y')));
    }

    public function getSummaryDataProperty(): array
    {
        $korweTarget = (int) $this->targetWilayah->{'target_korwe_' . $this->activeYear};
        $korteTarget = (int) $this->targetWilayah->{'target_korte_' . $this->activeYear};
        $penggalangTarget = (int) $this->targetWilayah->{'target_penggalang_' . $this->activeYear};
        $korweFormed = $this->targetWilayah->korwes->where('status', 'terbentuk')->count();
        $korteFormed = $this->targetWilayah->kortes->where('status', 'terbentuk')->count();
        $penggalangFormed = $this->targetWilayah->penggalangSuaras->where('status', 'aktif')->count();

        // #region debug-point B:detail-summary
        $this->reportDebug('B', 'Detail@summaryData', '[DEBUG] Detail summary calculated', [
            'targetWilayahId' => $this->targetWilayah->id,
            'activeYear' => $this->activeYear,
            'korweTarget' => $korweTarget,
            'korteTarget' => $korteTarget,
            'penggalangTarget' => $penggalangTarget,
            'korweFormed' => $korweFormed,
            'korteFormed' => $korteFormed,
            'penggalangFormed' => $penggalangFormed,
        ]);
        // #endregion

        return [
            'korwe_target' => $korweTarget,
            'korte_target' => $korteTarget,
            'penggalang_target' => $penggalangTarget,
            'korwe_formed' => $korweFormed,
            'korte_formed' => $korteFormed,
            'penggalang_formed' => $penggalangFormed,
            'korwe_percent' => $korweTarget > 0 ? min(100, round(($korweFormed / $korweTarget) * 100, 1)) : 0,
            'korte_percent' => $korteTarget > 0 ? min(100, round(($korteFormed / $korteTarget) * 100, 1)) : 0,
            'penggalang_percent' => $penggalangTarget > 0 ? min(100, round(($penggalangFormed / $penggalangTarget) * 100, 1)) : 0,
        ];
    }

    public function getYearlyTargetsProperty(): array
    {
        $korweFormed = $this->targetWilayah->korwes->where('status', 'terbentuk')->count();
        $korteFormed = $this->targetWilayah->kortes->where('status', 'terbentuk')->count();
        $penggalangFormed = $this->targetWilayah->penggalangSuaras->where('status', 'aktif')->count();
        $rows = [];

        foreach ([2026, 2027, 2028, 2029] as $year) {
            $rows[] = [
                'year' => $year,
                'korwe_target' => (int) $this->targetWilayah->{'target_korwe_' . $year},
                'korwe_formed' => $korweFormed,
                'korte_target' => (int) $this->targetWilayah->{'target_korte_' . $year},
                'korte_formed' => $korteFormed,
                'penggalang_target' => (int) $this->targetWilayah->{'target_penggalang_' . $year},
                'penggalang_formed' => $penggalangFormed,
                'active' => $this->activeYear === $year,
            ];
        }

        return $rows;
    }

    public function getKorweRowsProperty(): array
    {
        $existing = $this->targetWilayah->korwes->keyBy(fn (Korwe $item) => $this->normalizeNumber($item->nomor_rw));
        $rows = [];

        for ($i = 1; $i <= max(1, (int) $this->targetWilayah->jumlah_rw); $i++) {
            $nomor = $this->normalizeNumber((string) $i);
            $record = $existing->get($nomor);

            $rows[] = $this->buildRowState(
                $nomor,
                $record?->status ?? 'belum',
                $record?->nama_koordinator,
                $record?->no_hp,
                $record?->catatan,
                $record?->tanggal_terbentuk?->format('d M Y'),
                $record?->id
            );
        }

        return $rows;
    }

    public function getRwListProperty(): Collection
    {
        $korweMap = $this->targetWilayah->korwes
            ->keyBy(fn (Korwe $item): string => $this->normalizeNumber($item->nomor_rw));

        return DataRw::query()
            ->byDesa($this->targetWilayah->id)
            ->orderByPrioritas()
            ->get()
            ->map(function (DataRw $rw) use ($korweMap): DataRw {
                $rw->setRelation('korwe', $korweMap->get($this->normalizeNumber($rw->nomor_rw)));

                return $rw;
            });
    }

    public function getFilteredRwListProperty(): Collection
    {
        if ($this->rwStatusFilter === '') {
            return $this->rwList;
        }

        return $this->rwList
            ->filter(fn (DataRw $rw): bool => $rw->status_wilayah === $this->rwStatusFilter)
            ->values();
    }

    public function getRwStatusFiltersProperty(): array
    {
        $filters = [[
            'key' => '',
            'label' => 'Semua',
            'count' => $this->rwList->count(),
            'active' => $this->rwStatusFilter === '',
            'bg' => '#f5f5f5',
            'text' => '#525252',
            'border' => '#d4d4d8',
        ]];

        foreach (TargetWilayah::STATUS_CONFIG as $key => $config) {
            $filters[] = [
                'key' => $key,
                'label' => $config['label'],
                'count' => $this->rwList->where('status_wilayah', $key)->count(),
                'active' => $this->rwStatusFilter === $key,
                'bg' => $config['bg'],
                'text' => $config['text'],
                'border' => $config['text'],
            ];
        }

        return $filters;
    }

    public function getProfilRwMapProperty(): Collection
    {
        return $this->targetWilayah->profilRws
            ->keyBy(fn (ProfilRw $item): string => $this->normalizeNumber($item->nomor_rw));
    }

    /**
     * @return array{total_rw:int,profil_terisi:int,profil_lengkap:int,pct_profil:int}
     */
    public function getProfilStatsProperty(): array
    {
        $totalRw = $this->rwList->count();
        $profilTerisi = $this->targetWilayah->profilRws
            ->where('completion_percent', '>', 0)
            ->count();
        $profilLengkap = $this->targetWilayah->profilRws
            ->where('is_complete', true)
            ->count();

        return [
            'total_rw' => $totalRw,
            'profil_terisi' => $profilTerisi,
            'profil_lengkap' => $profilLengkap,
            'pct_profil' => $totalRw > 0 ? (int) round(($profilTerisi / $totalRw) * 100) : 0,
        ];
    }

    public function getKorteGroupsProperty(): array
    {
        $existingByRw = $this->targetWilayah->kortes->groupBy(fn (Korte $item) => $this->normalizeNumber($item->nomor_rw));
        $totalRw = max(1, (int) $this->targetWilayah->jumlah_rw);
        $totalRt = max(0, (int) $this->targetWilayah->jumlah_rt);
        $base = $totalRt > 0 ? intdiv($totalRt, $totalRw) : 0;
        $remainder = $totalRt > 0 ? $totalRt % $totalRw : 0;
        $groups = [];

        for ($rwIndex = 1; $rwIndex <= $totalRw; $rwIndex++) {
            $nomorRw = $this->normalizeNumber((string) $rwIndex);
            /** @var Collection<int, Korte> $existingRows */
            $existingRows = $existingByRw->get($nomorRw, collect())->keyBy(fn (Korte $item) => $this->normalizeNumber($item->nomor_rt));
            $targetCount = $base + ($rwIndex <= $remainder ? 1 : 0);
            $targetCount = max($targetCount, $existingRows->count(), $existingRows->keys()->map(fn (string $value) => (int) $value)->max() ?? 0);
            $rows = [];

            if ($targetCount === 0 && $existingRows->isEmpty()) {
                $rows[] = $this->buildRowState('001', 'belum', null, null, null, null, null);
            } else {
                for ($rtIndex = 1; $rtIndex <= max(1, $targetCount); $rtIndex++) {
                    $nomorRt = $this->normalizeNumber((string) $rtIndex);
                    $record = $existingRows->get($nomorRt);

                    $rows[] = $this->buildRowState(
                        $nomorRt,
                        $record?->status ?? 'belum',
                        $record?->nama_koordinator,
                        $record?->no_hp,
                        $record?->catatan,
                        $record?->tanggal_terbentuk?->format('d M Y'),
                        $record?->id
                    );
                }
            }

            $groups[] = [
                'rw' => $nomorRw,
                'rows' => $rows,
            ];
        }

        return $groups;
    }

    public function getPenggalangListProperty(): Collection
    {
        return PenggalangSuara::query()
            ->where('target_wilayah_id', $this->targetWilayah->id)
            ->orderBy('nomor_rw')
            ->orderBy('nama')
            ->get();
    }

    /**
     * @return array{total:int,target:int,aktif:int,jangkauan:int,targetJangkauan:int}
     */
    public function getPenggalangSummaryProperty(): array
    {
        $total = $this->penggalangList->count();
        $target = (int) $this->targetWilayah->{'target_penggalang_' . $this->activeYear};
        $aktif = $this->penggalangList->where('status', 'aktif')->count();
        $jangkauan = (int) $this->penggalangList->sum('realisasi_jangkauan');
        $targetJangkauan = (int) $this->penggalangList->sum('target_jangkauan');

        return compact('total', 'target', 'aktif', 'jangkauan', 'targetJangkauan');
    }

    public function simpanPenggalang(): void
    {
        $validated = $this->validate([
            'pgNama' => ['required', 'string', 'max:255'],
            'pgRw' => ['required', 'string', 'max:10'],
            'pgHp' => ['nullable', 'string', 'max:50'],
            'pgWa' => ['nullable', 'string', 'max:50'],
            'pgRt' => ['nullable', 'string', 'max:10'],
            'pgSumber' => ['required', 'string', 'max:50'],
            'pgTarget' => ['required', 'integer', 'min:0'],
        ], [], [
            'pgNama' => 'nama penggalang',
            'pgRw' => 'nomor RW',
        ]);

        $data = [
            'target_wilayah_id' => $this->targetWilayah->id,
            'dapil' => $this->targetWilayah->dapil,
            'kecamatan' => $this->targetWilayah->kecamatan,
            'desa' => $this->targetWilayah->desa,
            'nomor_rw' => $this->normalizeNumber($validated['pgRw']),
            'nama' => $validated['pgNama'],
            'no_hp' => $validated['pgHp'] !== '' ? $validated['pgHp'] : null,
            'no_wa' => $validated['pgWa'] !== '' ? $validated['pgWa'] : null,
            'rt' => $validated['pgRt'] !== '' ? $this->normalizeNumber($validated['pgRt']) : null,
            'sumber' => $validated['pgSumber'],
            'target_jangkauan' => (int) $validated['pgTarget'],
            'created_by' => auth()->id(),
        ];

        if ($this->pgEditId !== null) {
            PenggalangSuara::query()
                ->where('target_wilayah_id', $this->targetWilayah->id)
                ->findOrFail($this->pgEditId)
                ->update($data);
        } else {
            PenggalangSuara::query()->create($data);
        }

        $this->resetPenggalangForm();
        session()->flash('success', 'Penggalang suara berhasil disimpan.');
    }

    public function resetPenggalangForm(): void
    {
        $this->resetErrorBag();
        $this->showPenggalangForm = false;
        $this->pgEditId = null;
        $this->pgNama = '';
        $this->pgHp = '';
        $this->pgWa = '';
        $this->pgRw = '';
        $this->pgRt = '';
        $this->pgSumber = 'warga';
        $this->pgTarget = 10;
    }

    public function openPenggalangForm(): void
    {
        $this->resetPenggalangForm();
        $this->showPenggalangForm = true;
    }

    public function getUpaListProperty(): Collection
    {
        return UpaRwMember::query()
            ->where('target_wilayah_id', $this->targetWilayah->id)
            ->orderBy('nomor_rw')
            ->orderBy('jabatan')
            ->get();
    }

    /**
     * @return array{total_member:int,rw_dengan_upa:int,total_rw:int,pct:int}
     */
    public function getUpaSummaryProperty(): array
    {
        $totalRw = (int) $this->targetWilayah->jumlah_rw;
        $rwDenganUpa = $this->upaList->pluck('nomor_rw')->unique()->count();

        return [
            'total_member' => $this->upaList->count(),
            'rw_dengan_upa' => $rwDenganUpa,
            'total_rw' => $totalRw,
            'pct' => $totalRw > 0 ? (int) round(($rwDenganUpa / $totalRw) * 100) : 0,
        ];
    }

    public function simpanUpa(): void
    {
        $validated = $this->validate([
            'upaNama' => ['required', 'string', 'max:255'],
            'upaRw' => ['required', 'string', 'max:10'],
            'upaJabatan' => ['required', 'string', 'max:50'],
            'upaHp' => ['nullable', 'string', 'max:50'],
            'upaAsal' => ['required', 'string', 'max:50'],
        ], [], [
            'upaNama' => 'nama anggota UPA',
            'upaRw' => 'nomor RW',
            'upaJabatan' => 'jabatan',
        ]);

        $data = [
            'target_wilayah_id' => $this->targetWilayah->id,
            'dapil' => $this->targetWilayah->dapil,
            'kecamatan' => $this->targetWilayah->kecamatan,
            'desa' => $this->targetWilayah->desa,
            'nomor_rw' => $this->normalizeNumber($validated['upaRw']),
            'nama' => $validated['upaNama'],
            'no_hp' => $validated['upaHp'] !== '' ? $validated['upaHp'] : null,
            'jabatan' => $validated['upaJabatan'],
            'asal' => $validated['upaAsal'],
            'korwe_id' => $this->upaKorweId,
            'korte_id' => $this->upaKorteId,
            'created_by' => auth()->id(),
        ];

        if ($this->upaEditId !== null) {
            UpaRwMember::query()
                ->where('target_wilayah_id', $this->targetWilayah->id)
                ->findOrFail($this->upaEditId)
                ->update($data);
        } else {
            UpaRwMember::query()->create($data);
        }

        $this->resetUpaForm();
        session()->flash('success', 'Anggota UPA berhasil disimpan.');
    }

    public function resetUpaForm(): void
    {
        $this->resetErrorBag();
        $this->showUpaForm = false;
        $this->upaEditId = null;
        $this->upaNama = '';
        $this->upaHp = '';
        $this->upaRw = '';
        $this->upaJabatan = 'anggota';
        $this->upaAsal = 'korwe';
        $this->upaKorweId = null;
        $this->upaKorteId = null;
    }

    public function openUpaForm(): void
    {
        $this->resetUpaForm();
        $this->showUpaForm = true;
    }

    public function getSaksiListProperty(): Collection
    {
        return Korte::query()
            ->where('target_wilayah_id', $this->targetWilayah->id)
            ->where('is_saksi_tps', true)
            ->orderBy('assigned_tps')
            ->get();
    }

    /**
     * @return array{total_tps:int,terkonfirmasi:int,siap:int,total_saksi:int,pct:int}
     */
    public function getSaksiSummaryProperty(): array
    {
        $totalTps = (int) $this->targetWilayah->jumlah_tps;
        $saksiTerkonfirmasi = $this->saksiList->where('status_saksi', 'terkonfirmasi')->count();
        $saksiSiap = $this->saksiList->where('status_saksi', 'siap')->count();

        return [
            'total_tps' => $totalTps,
            'terkonfirmasi' => $saksiTerkonfirmasi,
            'siap' => $saksiSiap,
            'total_saksi' => $this->saksiList->count(),
            'pct' => $totalTps > 0 ? (int) round(($saksiTerkonfirmasi / $totalTps) * 100) : 0,
        ];
    }

    public function toggleSaksi(string $korteId): void
    {
        $korte = Korte::query()
            ->where('target_wilayah_id', $this->targetWilayah->id)
            ->findOrFail($korteId);

        $isSaksi = ! $korte->is_saksi_tps;

        $korte->update([
            'is_saksi_tps' => $isSaksi,
            'assigned_tps' => $isSaksi ? $korte->assigned_tps : null,
            'status_saksi' => $isSaksi ? 'siap' : 'belum',
        ]);

        $this->loadTargetWilayah($this->targetWilayah->fresh());
    }

    public function assignTps(string $korteId, string $tps): void
    {
        $korte = Korte::query()
            ->where('target_wilayah_id', $this->targetWilayah->id)
            ->findOrFail($korteId);

        $korte->update([
            'assigned_tps' => $tps !== '' ? $tps : null,
            'status_saksi' => $tps !== '' ? 'terkonfirmasi' : 'siap',
        ]);

        $this->loadTargetWilayah($this->targetWilayah->fresh());
    }

    public function render()
    {
        // #region debug-point A:detail-render
        $this->reportDebug('A', 'Detail@render', '[DEBUG] Infra RT/RW detail render', [
            'targetWilayahId' => $this->targetWilayah->id,
            'activeTab' => $this->activeTab,
            'showForm' => $this->showForm,
            'showProfilDrawer' => $this->showProfilDrawer,
            'korweCount' => $this->targetWilayah->korwes->count(),
            'korteCount' => $this->targetWilayah->kortes->count(),
        ]);
        // #endregion

        return view('livewire.infra-rtrw.detail')
            ->layout('components.layouts.app-fullwidth', ['title' => 'Detail Infrastruktur']);
    }

    private function loadTargetWilayah(TargetWilayah $targetWilayah): void
    {
        $this->targetWilayah = $targetWilayah->load([
            'korwes' => fn ($query) => $query->orderBy('nomor_rw'),
            'kortes' => fn ($query) => $query->orderBy('nomor_rw')->orderBy('nomor_rt'),
            'dataRws' => fn ($query) => $query->orderBy('prioritas_urutan')->orderByDesc('estimasi_pks'),
            'profilRws' => fn ($query) => $query->orderBy('nomor_rw'),
        ]);
    }

    private function resetForm(): void
    {
        $this->editId = null;
        $this->formNomorRw = '';
        $this->formParentRw = '';
        $this->formNamaKoordinator = '';
        $this->formNoHp = '';
        $this->formStatus = 'belum';
        $this->formCatatan = '';
        $this->formTanggal = null;
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyProfilData(): array
    {
        return [
            'tipologi' => '',
            'ekonomi_dominan' => '',
            'profil_warga' => '',
            'suara_pks_2019' => 0,
            'faktor_penyebab' => '',
            'anggota_pks' => '',
            'jumlah_kta' => 0,
            'upa_rw_status' => 'belum',
            'upa_rw_nama' => '',
            'rki_status' => 'belum',
            'rki_nama' => '',
            'senam_status' => 'belum',
            'senam_nama' => '',
            'relawan_milenial_status' => 'belum',
            'relawan_milenial_nama' => '',
            'caleg_terpilih_ada' => false,
            'caleg_terpilih_nama' => '',
            'afiliasi_rw_rt' => '',
            'afiliasi_posyandu_dkm' => '',
            'kompetitor_status' => 'tidak_tahu',
            'kompetitor_detail' => '',
            'tim_sukses_status' => 'tidak_tahu',
            'tim_sukses_detail' => '',
            'strategi' => '',
            'penanggung_jawab' => '',
            'keterangan_lain' => '',
        ];
    }

    private function loadAutoFillData(string $nomorRw): void
    {
        $tw = $this->targetWilayah;
        $dataRw = DataRw::query()
            ->where('target_wilayah_id', $tw->id)
            ->where('nomor_rw', $nomorRw)
            ->first();
        $korwe = Korwe::query()
            ->where('target_wilayah_id', $tw->id)
            ->where('nomor_rw', $nomorRw)
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
            'korwe_status' => $korwe?->status ?? 'belum',
            'status_wilayah' => $dataRw?->status_wilayah ?? 'ZONA BERAT',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        $rules = [
            'formNomorRw' => ['required', 'string', 'max:10'],
            'formNamaKoordinator' => ['nullable', 'string', 'max:255'],
            'formNoHp' => ['nullable', 'string', 'max:50'],
            'formStatus' => ['required', 'in:belum,proses,terbentuk'],
            'formCatatan' => ['nullable', 'string'],
            'formTanggal' => ['nullable', 'date'],
        ];

        if ($this->activeTab === 'korte') {
            $rules['formParentRw'] = ['required', 'string', 'max:10'];
        }

        if ($this->formStatus === 'terbentuk') {
            $rules['formTanggal'] = ['required', 'date'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'formNomorRw' => $this->activeTab === 'korwe' ? 'nomor RW' : 'nomor RT',
            'formParentRw' => 'RW induk',
            'formNamaKoordinator' => 'nama koordinator',
            'formNoHp' => 'no HP',
            'formStatus' => 'status',
            'formCatatan' => 'catatan',
            'formTanggal' => 'tanggal terbentuk',
        ];
    }

    private function normalizeNumber(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';
        $number = $digits !== '' ? (int) $digits : 0;

        return str_pad((string) $number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildRowState(
        string $nomor,
        string $status,
        ?string $nama,
        ?string $noHp,
        ?string $catatan,
        ?string $tanggal,
        ?string $id,
    ): array {
        return [
            'nomor' => $nomor,
            'status' => strtolower($status),
            'nama' => $nama,
            'no_hp' => $noHp,
            'catatan' => $catatan,
            'tanggal' => $tanggal,
            'id' => $id,
        ];
    }

    // #region debug-point A:detail-debug-helper
    private function reportDebug(string $hypothesisId, string $location, string $msg, array $data = []): void
    {
        $envPath = base_path('.dbg/infra-rtrw-fixes.env');
        $url = 'http://127.0.0.1:7777/event';
        $sessionId = 'infra-rtrw-fixes';

        if (is_file($envPath)) {
            $env = (string) file_get_contents($envPath);
            if (preg_match('/^DEBUG_SERVER_URL=(.+)$/m', $env, $matches) === 1) {
                $url = trim($matches[1]);
            }
            if (preg_match('/^DEBUG_SESSION_ID=(.+)$/m', $env, $matches) === 1) {
                $sessionId = trim($matches[1]);
            }
        }

        $payload = json_encode([
            'sessionId' => $sessionId,
            'runId' => 'post-fix',
            'hypothesisId' => $hypothesisId,
            'location' => $location,
            'msg' => $msg,
            'data' => $data,
            'ts' => (int) round(microtime(true) * 1000),
        ]);

        if ($payload === false) {
            return;
        }

        @file_get_contents($url, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => $payload,
                'timeout' => 1,
            ],
        ]));
    }
    // #endregion
}
