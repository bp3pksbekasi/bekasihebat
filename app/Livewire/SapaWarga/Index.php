<?php

declare(strict_types=1);

namespace App\Livewire\SapaWarga;

use App\Models\DataRw;
use App\Models\KontakWarga;
use App\Models\PenggalangSuara;
use App\Models\TargetWilayah;
use App\Traits\WithWilayahScope;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use WithWilayahScope;

    public string $selectedDapil = '';

    public string $selectedKecamatan = '';

    public string $selectedDesa = '';

    public ?string $selectedTargetWilayahId = null;

    public string $selectedRw = '';

    public string $detailSearch = '';

    public bool $showBulkForm = false;

    public string $bulkText = '';

    public string $bulkCatatan = '';

    public function mount(): void
    {
        $this->enforceScope();
        $this->ensureSelectedDesa();
        $this->ensureSelectedRw();
    }

    public function updatingSelectedDapil(): void
    {
        $this->selectedKecamatan = '';
        $this->selectedDesa = '';
        $this->selectedTargetWilayahId = null;
        $this->selectedRw = '';
        $this->resetPage('contactsPage');
    }

    public function updatingSelectedKecamatan(): void
    {
        $this->selectedDesa = '';
        $this->selectedTargetWilayahId = null;
        $this->selectedRw = '';
        $this->resetPage('contactsPage');
    }

    public function updatingSelectedDesa(): void
    {
        $this->selectedTargetWilayahId = null;
        $this->selectedRw = '';
        $this->resetPage('contactsPage');
    }

    public function updatedSelectedDapil(): void
    {
        if ($this->isKaderScope()) {
            $this->enforceScope();
            return;
        }

        $this->selectedKecamatan = '';
        $this->selectedDesa = '';
        $this->resetDesaSelection();
    }

    public function updatedSelectedKecamatan(): void
    {
        if ($this->isKaderScope()) {
            $this->enforceScope();
            return;
        }

        $this->selectedDesa = '';
        $this->resetDesaSelection();
    }

    public function updatedSelectedDesa(): void
    {
        if ($this->isKaderScope()) {
            $this->enforceScope();
            return;
        }

        $this->resetDesaSelection();
    }

    public function updatedDetailSearch(): void
    {
        $this->resetPage('contactsPage');
    }

    public function selectDesa(string $targetWilayahId): void
    {
        if ($this->isKaderScope()) {
            $allowedTargetId = $this->currentUserTargetWilayah()?->id;

            if ($allowedTargetId !== $targetWilayahId) {
                return;
            }
        }

        $this->selectedTargetWilayahId = $this->selectedTargetWilayahId === $targetWilayahId ? null : $targetWilayahId;
        if ($this->selectedTargetWilayahId) {
            $w = TargetWilayah::find($this->selectedTargetWilayahId);
            $this->selectedDesa = $w ? $w->desa : '';
        } else {
            $this->selectedDesa = '';
        }

        $this->selectedRw = '';
        $this->showBulkForm = false;
        $this->bulkText = '';
        $this->bulkCatatan = '';
        $this->detailSearch = '';
        $this->resetPage('contactsPage');
        $this->ensureSelectedRw();
    }

    public function closeVillageDetail(): void
    {
        $this->selectedTargetWilayahId = null;
        $this->selectedDesa = '';
        $this->selectedRw = '';
        $this->showBulkForm = false;
        $this->bulkText = '';
        $this->bulkCatatan = '';
        $this->detailSearch = '';
        $this->resetPage('contactsPage');
    }

    public function selectRw(string $rw): void
    {
        if ($this->isKaderScope() && $this->normalizeNumber($rw) !== $this->userRw()) {
            return;
        }

        $this->selectedRw = $this->normalizeNumber($rw);
        $this->showBulkForm = false;
        $this->bulkText = '';
        $this->bulkCatatan = '';
        $this->detailSearch = '';
        $this->resetPage('contactsPage');
    }

    public function toggleBulkForm(): void
    {
        if (! $this->hasSelectedRw()) {
            return;
        }

        $this->showBulkForm = ! $this->showBulkForm;

        if (! $this->showBulkForm) {
            $this->bulkText = '';
            $this->bulkCatatan = '';
            $this->resetErrorBag();
        }
    }

    public function saveBulk(): void
    {
        $this->validate([
            'bulkText' => ['required', 'string'],
            'bulkCatatan' => ['nullable', 'string', 'max:65535'],
        ], [], [
            'bulkText' => 'bulk text kontak',
            'bulkCatatan' => 'catatan',
        ]);

        $targetWilayah = $this->selectedTargetWilayah;

        if (! $targetWilayah instanceof TargetWilayah || ! $this->hasSelectedRw()) {
            return;
        }

        $parsed = $this->parseBulkInput($this->bulkText);

        if ($parsed['valid_rows']->isEmpty()) {
            $this->addError('bulkText', 'Format bulk belum valid. Gunakan format "nama, nomor" per baris.');

            return;
        }

        $existingNumbers = KontakWarga::query()
            ->where('target_wilayah_id', $targetWilayah->id)
            ->where('nomor_rw', $this->selectedRw)
            ->whereNotNull('no_wa')
            ->pluck('no_wa')
            ->map(fn ($value): string => $this->normalizePhone((string) $value))
            ->filter()
            ->values()
            ->all();

        $existingLookup = array_fill_keys($existingNumbers, true);
        $saved = 0;
        $skipped = 0;

        foreach ($parsed['valid_rows'] as $row) {
            $phone = $row['phone'];

            if ($phone !== '' && array_key_exists($phone, $existingLookup)) {
                $skipped++;
                continue;
            }

            KontakWarga::query()->create([
                'target_wilayah_id' => $targetWilayah->id,
                'dapil' => $targetWilayah->dapil,
                'kecamatan' => $targetWilayah->kecamatan,
                'desa' => $targetWilayah->desa,
                'nomor_rw' => $this->selectedRw,
                'nama' => $row['name'],
                'no_wa' => $phone !== '' ? $phone : null,
                'no_hp' => $phone !== '' ? $phone : null,
                'rt' => null,
                'alamat' => null,
                'sumber' => 'bulk',
                'penggalang_id' => null,
                'catatan' => $this->bulkCatatan !== '' ? trim($this->bulkCatatan) : null,
                'status' => 'aktif',
                'created_by' => auth()->id(),
            ]);

            if ($phone !== '') {
                $existingLookup[$phone] = true;
            }

            $saved++;
        }

        $this->showBulkForm = false;
        $this->bulkText = '';
        $this->bulkCatatan = '';
        $this->resetErrorBag();
        session()->flash('success', 'Bulk kontak diproses: ' . number_format($saved) . ' tersimpan, ' . number_format($skipped) . ' duplikat dilewati.');
    }

    public function deactivateContact(string $kontakId): void
    {
        $targetWilayah = $this->selectedTargetWilayah;

        if (! $targetWilayah instanceof TargetWilayah || ! $this->hasSelectedRw()) {
            return;
        }

        $kontak = KontakWarga::query()
            ->whereKey($kontakId)
            ->where('target_wilayah_id', $targetWilayah->id)
            ->where('nomor_rw', $this->selectedRw)
            ->first();

        if (! $kontak instanceof KontakWarga) {
            return;
        }

        $kontak->update([
            'status' => 'nonaktif',
        ]);

        session()->flash('success', 'Kontak warga dinonaktifkan.');
    }

    #[Computed]
    public function dapilOptions(): Collection
    {
        return TargetWilayah::query()
            ->select('dapil')
            ->distinct()
            ->orderBy('dapil')
            ->pluck('dapil');
    }

    #[Computed]
    public function kecamatanOptions(): Collection
    {
        return TargetWilayah::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
    }

    #[Computed]
    public function desaOptions(): Collection
    {
        return TargetWilayah::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->select('desa')
            ->distinct()
            ->orderBy('desa')
            ->pluck('desa');
    }

    #[Computed]
    public function mapImage(): string
    {
        if ($this->selectedKecamatan !== '') {
            $slug = str_replace(' ', '-', strtolower($this->selectedKecamatan));
            return "/images/peta/kecamatan/{$slug}.png";
        }

        if ($this->selectedDapil !== '') {
            $num = str_replace('BEKASI ', '', strtoupper($this->selectedDapil));
            return "/images/peta/dapil{$num}.png";
        }

        return "/images/peta/kabupaten-bekasi.png";
    }

    #[Computed]
    public function mapMarkers(): array
    {
        $configs = (new \App\Livewire\Kaderisasi\Index())->getMapConfigs();
        $config = null;

        if ($this->selectedKecamatan !== '') {
            $config = $configs[strtoupper($this->selectedKecamatan)] ?? null;
        } elseif ($this->selectedDapil !== '') {
            $config = $configs[strtoupper($this->selectedDapil)] ?? null;
        }

        if (!$config) {
            return [];
        }

        $wilayahs = TargetWilayah::query()
            ->when($this->isKaderScope(), function (Builder $query): void {
                $user = auth()->user();
                $query->where('dapil', $user?->dapil)
                    ->where('kecamatan', $user?->kecamatan)
                    ->where('desa', $user?->desa);
            })
            ->when($this->selectedDapil !== '', fn ($q) => $q->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn ($q) => $q->where('kecamatan', $this->selectedKecamatan))
            ->get();

        if ($wilayahs->isEmpty()) {
            return [];
        }

        $targetIds = $wilayahs->pluck('id');

        $rwCounts = DataRw::query()
            ->whereIn('target_wilayah_id', $targetIds)
            ->selectRaw('target_wilayah_id, COUNT(*) as total')
            ->groupBy('target_wilayah_id')
            ->pluck('total', 'target_wilayah_id');

        $rwTerisiCounts = KontakWarga::query()
            ->whereIn('target_wilayah_id', $targetIds)
            ->where('status', 'aktif')
            ->selectRaw('target_wilayah_id, COUNT(DISTINCT nomor_rw) as total')
            ->groupBy('target_wilayah_id')
            ->pluck('total', 'target_wilayah_id');

        $markers = [];
        foreach ($wilayahs as $w) {
            $desaUpper = strtoupper($w->desa);
            if (isset($config[$desaUpper])) {
                $totalRw = (int) ($rwCounts[$w->id] ?? 0);
                $rwTerisi = (int) ($rwTerisiCounts[$w->id] ?? 0);

                if ($totalRw > 0) {
                    if ($rwTerisi >= $totalRw) {
                        $color = '#22c55e'; // Green
                    } elseif ($rwTerisi > 0) {
                        $color = '#eab308'; // Yellow
                    } else {
                        $color = '#ef4444'; // Red
                    }
                } else {
                    $color = '#22c55e'; // Green if total RW is 0
                }

                $size = 12 + ($totalRw > 0 ? (int) round(($rwTerisi / $totalRw) * 12) : 12);

                $markers[] = [
                    'id' => $w->id,
                    'key' => $w->id,
                    'label' => "{$w->desa} · {$rwTerisi}/{$totalRw} RW Terisi",
                    'x' => $config[$desaUpper]['x'],
                    'y' => $config[$desaUpper]['y'],
                    'size' => $size,
                    'color' => $color,
                    'count' => $rwTerisi,
                    'target' => $totalRw,
                    'desa' => $w->desa,
                    'kecamatan' => $w->kecamatan
                ];
            }
        }

        return $markers;
    }

    #[Computed]
    public function villageList(): Collection
    {
        $wilayahs = TargetWilayah::query()
            ->when($this->isKaderScope(), function (Builder $query): void {
                $user = auth()->user();
                $query->where('dapil', $user?->dapil)
                    ->where('kecamatan', $user?->kecamatan)
                    ->where('desa', $user?->desa);
            })
            ->when($this->selectedDapil !== '', fn ($q) => $q->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn ($q) => $q->where('kecamatan', $this->selectedKecamatan))
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get(['id', 'kecamatan', 'desa']);

        if ($wilayahs->isEmpty()) {
            return collect();
        }

        $targetIds = $wilayahs->pluck('id');

        $rwCounts = DataRw::query()
            ->whereIn('target_wilayah_id', $targetIds)
            ->selectRaw('target_wilayah_id, COUNT(*) as total')
            ->groupBy('target_wilayah_id')
            ->pluck('total', 'target_wilayah_id');

        $rwTerisiCounts = KontakWarga::query()
            ->whereIn('target_wilayah_id', $targetIds)
            ->where('status', 'aktif')
            ->selectRaw('target_wilayah_id, COUNT(DISTINCT nomor_rw) as total')
            ->groupBy('target_wilayah_id')
            ->pluck('total', 'target_wilayah_id');

        return $wilayahs->map(function (TargetWilayah $w) use ($rwCounts, $rwTerisiCounts): array {
            $total = (int) ($rwCounts[$w->id] ?? 0);
            $terisi = (int) ($rwTerisiCounts[$w->id] ?? 0);
            $pct = $total > 0 ? (int) round(($terisi / $total) * 100) : 0;

            return [
                'id' => $w->id,
                'desa' => $w->desa,
                'kecamatan' => $w->kecamatan,
                'total_rw' => $total,
                'rw_terisi' => $terisi,
                'pct_terisi' => $pct,
            ];
        });
    }

    #[Computed]
    public function selectedVillageDetail(): ?array
    {
        if ($this->selectedTargetWilayahId === null) {
            return null;
        }

        $w = TargetWilayah::find($this->selectedTargetWilayahId);
        if (!$w) {
            return null;
        }

        $totalRw = DataRw::query()->where('target_wilayah_id', $w->id)->count();
        $totalRt = DataRw::query()->where('target_wilayah_id', $w->id)->sum('jumlah_rt');

        $totalKontak = KontakWarga::query()
            ->where('target_wilayah_id', $w->id)
            ->where('status', 'aktif')
            ->count();

        $targetKontak = $totalRw * KontakWarga::TARGET_PER_RW;
        $pct = $targetKontak > 0 ? (int) min(100, round(($totalKontak / $targetKontak) * 100)) : 0;

        return [
            'id' => $w->id,
            'desa' => $w->desa,
            'kecamatan' => $w->kecamatan,
            'dapil' => $w->dapil,
            'total_rw' => $totalRw,
            'total_rt' => $totalRt,
            'total_kontak' => $totalKontak,
            'target_kontak' => $targetKontak,
            'pct_tersisir' => $pct,
            'pct_progress' => $pct,
        ];
    }

    #[Computed]
    public function summary(): array
    {
        if ($this->isKaderScope()) {
            $targetWilayah = $this->selectedTargetWilayah;
            $totalKontak = $this->hasSelectedRw()
                ? (int) $this->selectedRwKontakQuery()->where('status', 'aktif')->count()
                : 0;
            $penggalangAktif = $targetWilayah instanceof TargetWilayah && $this->hasSelectedRw()
                ? (int) PenggalangSuara::query()
                    ->where('target_wilayah_id', $targetWilayah->id)
                    ->where('nomor_rw', $this->selectedRw)
                    ->where('status', 'aktif')
                    ->count()
                : 0;

            return [
                'total_kontak' => $totalKontak,
                'target_kontak' => KontakWarga::TARGET_PER_RW,
                'desa_count' => $targetWilayah instanceof TargetWilayah ? 1 : 0,
                'rw_terisi' => $totalKontak > 0 ? 1 : 0,
                'progress_pct' => (int) min(100, round(($totalKontak / max(1, KontakWarga::TARGET_PER_RW)) * 100)),
                'penggalang_aktif' => $penggalangAktif,
            ];
        }

        $targetQuery = $this->filteredTargetQuery();
        $kontakQuery = $this->filteredKontakQuery()->where('status', 'aktif');
        $targetRw = (int) (clone $targetQuery)->sum('jumlah_rw');
        $targetKontak = $targetRw * KontakWarga::TARGET_PER_RW;
        $totalKontak = (int) (clone $kontakQuery)->count();
        $desaCount = (int) (clone $targetQuery)->count();
        $rwTerisi = (int) ((clone $kontakQuery)
            ->selectRaw('COUNT(DISTINCT CONCAT(target_wilayah_id, ":", nomor_rw)) as total')
            ->value('total') ?? 0);
        $penggalangAktif = (int) PenggalangSuara::query()
            ->where('status', 'aktif')
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->when($this->selectedDesa !== '', fn (Builder $query) => $query->where('desa', $this->selectedDesa))
            ->count();

        return [
            'total_kontak' => $totalKontak,
            'target_kontak' => $targetKontak,
            'desa_count' => $desaCount,
            'rw_terisi' => $rwTerisi,
            'progress_pct' => $targetKontak > 0 ? (int) round(($totalKontak / $targetKontak) * 100) : 0,
            'penggalang_aktif' => $penggalangAktif,
        ];
    }

    #[Computed]
    public function desaRows(): LengthAwarePaginator
    {
        return $this->targetTableQuery()
            ->paginate(12, ['*'], 'desaPage');
    }

    #[Computed]
    public function selectedTargetWilayah(): ?TargetWilayah
    {
        if ($this->selectedTargetWilayahId === null) {
            return null;
        }

        return TargetWilayah::query()->find($this->selectedTargetWilayahId);
    }

    #[Computed]
    public function rwRows(): Collection
    {
        $targetWilayah = $this->selectedTargetWilayah;

        if (! $targetWilayah instanceof TargetWilayah) {
            return collect();
        }

        $kontakCounts = KontakWarga::query()
            ->where('target_wilayah_id', $targetWilayah->id)
            ->where('status', 'aktif')
            ->selectRaw('nomor_rw, COUNT(*) as total')
            ->groupBy('nomor_rw')
            ->pluck('total', 'nomor_rw');

        $penggalangCounts = PenggalangSuara::query()
            ->where('target_wilayah_id', $targetWilayah->id)
            ->where('status', 'aktif')
            ->selectRaw('nomor_rw, COUNT(*) as total')
            ->groupBy('nomor_rw')
            ->pluck('total', 'nomor_rw');

        $rows = DataRw::query()
            ->where('target_wilayah_id', $targetWilayah->id)
            ->orderByPrioritas()
            ->get();

        if ($rows->isNotEmpty()) {
            $collection = $rows->map(function (DataRw $row) use ($kontakCounts, $penggalangCounts) {
                $rw = $this->normalizeNumber((string) $row->nomor_rw);
                $kontak = (int) ($kontakCounts[$rw] ?? 0);
                $penggalang = (int) ($penggalangCounts[$rw] ?? 0);

                return [
                    'nomor_rw' => $rw,
                    'label_rw' => $rw,
                    'jumlah_rt' => (int) $row->jumlah_rt,
                    'kontak_count' => $kontak,
                    'penggalang_count' => $penggalang,
                    'progress_pct' => (int) min(100, round(($kontak / max(1, KontakWarga::TARGET_PER_RW)) * 100)),
                    'status_wilayah' => $row->status_wilayah,
                    'status_config' => $row->status_config,
                ];
            });

            return $this->isKaderScope()
                ? $collection->where('nomor_rw', $this->userRw())->values()
                : $collection;
        }

        $fallback = collect(range(1, max(0, (int) $targetWilayah->jumlah_rw)))
            ->map(function (int $index) use ($kontakCounts, $penggalangCounts) {
                $rw = $this->normalizeNumber((string) $index);
                $kontak = (int) ($kontakCounts[$rw] ?? 0);
                $penggalang = (int) ($penggalangCounts[$rw] ?? 0);

                return [
                    'nomor_rw' => $rw,
                    'label_rw' => $rw,
                    'jumlah_rt' => 0,
                    'kontak_count' => $kontak,
                    'penggalang_count' => $penggalang,
                    'progress_pct' => (int) min(100, round(($kontak / max(1, KontakWarga::TARGET_PER_RW)) * 100)),
                    'status_wilayah' => null,
                    'status_config' => [
                        'label' => 'Belum dipetakan',
                        'bg' => '#f4f4f5',
                        'text' => '#525252',
                    ],
                ];
            });

        return $this->isKaderScope()
            ? $fallback->where('nomor_rw', $this->userRw())->values()
            : $fallback;
    }

    #[Computed]
    public function rwSummary(): array
    {
        $targetWilayah = $this->selectedTargetWilayah;

        if (! $targetWilayah instanceof TargetWilayah || ! $this->hasSelectedRw()) {
            return [
                'total_kontak' => 0,
                'penggalang_aktif' => 0,
                'rt_terisi' => 0,
                'target_kontak' => KontakWarga::TARGET_PER_RW,
                'progress_pct' => 0,
            ];
        }

        $query = $this->selectedRwKontakQuery()->where('status', 'aktif');
        $totalKontak = (int) (clone $query)->count();
        $rtTerisi = (int) ((clone $query)
            ->whereNotNull('rt')
            ->where('rt', '!=', '')
            ->selectRaw('COUNT(DISTINCT rt) as total')
            ->value('total') ?? 0);
        $penggalangAktif = (int) PenggalangSuara::query()
            ->where('target_wilayah_id', $targetWilayah->id)
            ->where('nomor_rw', $this->selectedRw)
            ->where('status', 'aktif')
            ->count();

        return [
            'total_kontak' => $totalKontak,
            'penggalang_aktif' => $penggalangAktif,
            'rt_terisi' => $rtTerisi,
            'target_kontak' => KontakWarga::TARGET_PER_RW,
            'progress_pct' => (int) min(100, round(($totalKontak / max(1, KontakWarga::TARGET_PER_RW)) * 100)),
        ];
    }

    #[Computed]
    public function rtBadges(): Collection
    {
        if (! $this->hasSelectedRw()) {
            return collect();
        }

        return $this->selectedRwKontakQuery()
            ->where('status', 'aktif')
            ->whereNotNull('rt')
            ->where('rt', '!=', '')
            ->selectRaw('rt, COUNT(*) as total')
            ->groupBy('rt')
            ->orderBy('rt')
            ->get()
            ->map(function (KontakWarga $row) {
                $count = (int) $row->total;

                if ($count >= 20) {
                    return [
                        'rt' => (string) $row->rt,
                        'total' => $count,
                        'bg' => '#dcfce7',
                        'text' => '#166534',
                    ];
                }

                if ($count >= 10) {
                    return [
                        'rt' => (string) $row->rt,
                        'total' => $count,
                        'bg' => '#fef3c7',
                        'text' => '#b45309',
                    ];
                }

                return [
                    'rt' => (string) $row->rt,
                    'total' => $count,
                    'bg' => '#fee2e2',
                    'text' => '#b91c1c',
                ];
            });
    }

    #[Computed]
    public function rwPenggalang(): Collection
    {
        $targetWilayah = $this->selectedTargetWilayah;

        if (! $targetWilayah instanceof TargetWilayah || ! $this->hasSelectedRw()) {
            return collect();
        }

        return PenggalangSuara::query()
            ->where('target_wilayah_id', $targetWilayah->id)
            ->where('nomor_rw', $this->selectedRw)
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();
    }

    #[Computed]
    public function kontakRows(): LengthAwarePaginator
    {
        if (! $this->hasSelectedRw()) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20, 1, [
                'path' => request()->url(),
                'pageName' => 'contactsPage',
            ]);
        }

        return $this->selectedRwKontakQuery()
            ->where('status', 'aktif')
            ->when(trim($this->detailSearch) !== '', function (Builder $query): void {
                $search = trim($this->detailSearch);
                $query->where(function (Builder $inner) use ($search): void {
                    $inner->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('no_wa', 'like', '%' . $search . '%')
                        ->orWhere('no_hp', 'like', '%' . $search . '%')
                        ->orWhere('rt', 'like', '%' . $search . '%')
                        ->orWhere('alamat', 'like', '%' . $search . '%');
                });
            })
            ->orderByDesc('created_at')
            ->orderBy('nama')
            ->paginate(20, ['*'], 'contactsPage');
    }

    #[Computed]
    public function bulkPreview(): array
    {
        $parsed = $this->parseBulkInput($this->bulkText);

        if (! $this->hasSelectedRw()) {
            return [
                ...$parsed,
                'duplicate_existing' => 0,
                'ready_to_save' => 0,
            ];
        }

        $existingNumbers = KontakWarga::query()
            ->where('target_wilayah_id', $this->selectedTargetWilayahId)
            ->where('nomor_rw', $this->selectedRw)
            ->whereNotNull('no_wa')
            ->pluck('no_wa')
            ->map(fn ($value): string => $this->normalizePhone((string) $value))
            ->filter()
            ->values()
            ->all();

        $existingLookup = array_fill_keys($existingNumbers, true);
        $duplicateExisting = 0;

        foreach ($parsed['valid_rows'] as $row) {
            if ($row['phone'] !== '' && array_key_exists($row['phone'], $existingLookup)) {
                $duplicateExisting++;
            }
        }

        return [
            ...$parsed,
            'duplicate_existing' => $duplicateExisting,
            'ready_to_save' => max(0, $parsed['valid_count'] - $duplicateExisting),
        ];
    }

    public function render()
    {
        $this->enforceScope();
        $this->ensureSelectedDesa();
        $this->ensureSelectedRw();

        return view('livewire.sapa-warga.index')
            ->layout('components.layouts.app-fullwidth', ['title' => 'Sapa Warga']);
    }

    private function resetDesaSelection(): void
    {
        $this->selectedTargetWilayahId = null;
        $this->selectedRw = '';
        $this->detailSearch = '';
        $this->showBulkForm = false;
        $this->bulkText = '';
        $this->bulkCatatan = '';
        $this->resetPage('desaPage');
        $this->resetPage('contactsPage');
        $this->ensureSelectedDesa();
        $this->ensureSelectedRw();
    }

    private function ensureSelectedDesa(): void
    {
        if ($this->selectedTargetWilayahId !== null && $this->filteredTargetQuery()->whereKey($this->selectedTargetWilayahId)->exists()) {
            return;
        }

        $this->selectedTargetWilayahId = $this->isKaderScope()
            ? $this->currentUserTargetWilayah()?->id
            : $this->firstTargetWilayahId();
    }

    private function ensureSelectedRw(): void
    {
        $rwRows = $this->rwRows;

        if ($rwRows->isEmpty()) {
            $this->selectedRw = '';
            return;
        }

        $exists = $rwRows->contains(fn (array $row): bool => $row['nomor_rw'] === $this->selectedRw);

        if (! $exists) {
            $this->selectedRw = $this->isKaderScope()
                ? $this->userRw()
                : (string) $rwRows->first()['nomor_rw'];
        }
    }

    private function firstTargetWilayahId(): ?string
    {
        $row = $this->targetTableQuery()->first();

        return $row instanceof TargetWilayah ? $row->id : null;
    }

    private function targetTableQuery(): Builder
    {
        return $this->filteredTargetQuery()
            ->select('target_wilayahs.*')
            ->withCount([
                'kontakWargas as kontak_count' => fn (Builder $query) => $query->where('status', 'aktif'),
                'penggalangSuaras as penggalang_count' => fn (Builder $query) => $query->where('status', 'aktif'),
            ])
            ->selectSub(
                KontakWarga::query()
                    ->selectRaw('COUNT(DISTINCT nomor_rw)')
                    ->whereColumn('kontak_wargas.target_wilayah_id', 'target_wilayahs.id')
                    ->where('status', 'aktif'),
                'rw_terisi_count'
            )
            ->orderByDesc('kontak_count')
            ->orderBy('dapil')
            ->orderBy('kecamatan')
            ->orderBy('desa');
    }

    private function filteredTargetQuery(): Builder
    {
        return TargetWilayah::query()
            ->when($this->isKaderScope(), function (Builder $query): void {
                $user = auth()->user();
                $query->where('dapil', $user?->dapil)
                    ->where('kecamatan', $user?->kecamatan)
                    ->where('desa', $user?->desa);
            })
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->when($this->selectedDesa !== '', fn (Builder $query) => $query->where('desa', $this->selectedDesa));
    }

    private function filteredKontakQuery(): Builder
    {
        return KontakWarga::query()
            ->when($this->isKaderScope(), function (Builder $query): void {
                $user = auth()->user();
                $query->where('dapil', $user?->dapil)
                    ->where('kecamatan', $user?->kecamatan)
                    ->where('desa', $user?->desa)
                    ->where('nomor_rw', $this->userRw());
            })
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan))
            ->when($this->selectedDesa !== '', fn (Builder $query) => $query->where('desa', $this->selectedDesa));
    }

    private function selectedRwKontakQuery(): Builder
    {
        return KontakWarga::query()
            ->where('target_wilayah_id', $this->selectedTargetWilayahId)
            ->where('nomor_rw', $this->isKaderScope() ? $this->userRw() : $this->selectedRw)
            ->with('penggalang');
    }

    private function enforceScope(): void
    {
        $user = auth()->user();
        if (! $user) {
            return;
        }

        $scope = $this->accessScope;
        if (($scope['mode'] ?? 'global') === 'dapil') {
            $this->selectedDapil = (string) ($scope['locked_dapil'] ?? '');
            $this->selectedKecamatan = (string) ($scope['kecamatan'] ?? '');
            if (!empty($scope['desa'])) {
                $this->selectedDesa = $scope['desa'];
                $desa = TargetWilayah::where('dapil', $this->selectedDapil)
                    ->where('kecamatan', $this->selectedKecamatan)
                    ->where('desa', $this->selectedDesa)
                    ->first();
                if ($desa) {
                    $this->selectedTargetWilayahId = (string) $desa->id;
                }
            }
        }

        if ($this->isKaderScope()) {
            $this->selectedDapil = (string) ($user->dapil ?? '');
            $this->selectedKecamatan = (string) ($user->kecamatan ?? '');
            $this->selectedDesa = (string) ($user->desa ?? '');
            $this->selectedRw = $this->userRw();
        }
    }

    private function currentUserTargetWilayah(): ?TargetWilayah
    {
        if (! $this->isKaderScope()) {
            return null;
        }

        $user = auth()->user();

        return TargetWilayah::query()
            ->where('dapil', $user?->dapil)
            ->where('kecamatan', $user?->kecamatan)
            ->where('desa', $user?->desa)
            ->first();
    }

    private function isKaderScope(): bool
    {
        return (bool) auth()->user()?->isKader();
    }

    private function userRw(): string
    {
        $rw = trim((string) (auth()->user()?->nomor_rw ?? ''));

        return $rw !== '' ? $this->normalizeNumber($rw) : '';
    }

    /**
     * @return array{
     *     total_lines:int,
     *     empty_lines:int,
     *     invalid_lines:int,
     *     duplicate_in_input:int,
     *     valid_count:int,
     *     valid_rows:\Illuminate\Support\Collection<int, array{name:string, phone:string}>
     * }
     */
    private function parseBulkInput(string $input): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $input) ?: [];
        $validRows = collect();
        $seenPhones = [];
        $emptyLines = 0;
        $invalidLines = 0;
        $duplicateInInput = 0;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '') {
                $emptyLines++;
                continue;
            }

            $parts = preg_split('/\s*[,;\t]\s*/', $trimmed, 2);

            if (! is_array($parts) || count($parts) < 2) {
                $invalidLines++;
                continue;
            }

            $name = trim((string) $parts[0]);
            $phone = $this->normalizePhone((string) $parts[1]);

            if ($name === '' || $phone === '') {
                $invalidLines++;
                continue;
            }

            if (array_key_exists($phone, $seenPhones)) {
                $duplicateInInput++;
                continue;
            }

            $seenPhones[$phone] = true;
            $validRows->push([
                'name' => $name,
                'phone' => $phone,
            ]);
        }

        return [
            'total_lines' => count($lines),
            'empty_lines' => $emptyLines,
            'invalid_lines' => $invalidLines,
            'duplicate_in_input' => $duplicateInInput,
            'valid_count' => $validRows->count(),
            'valid_rows' => $validRows,
        ];
    }

    private function hasSelectedRw(): bool
    {
        return $this->selectedTargetWilayahId !== null && $this->selectedRw !== '';
    }

    private function normalizeNumber(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';
        $number = $digits !== '' ? (int) $digits : 0;

        return str_pad((string) $number, 3, '0', STR_PAD_LEFT);
    }

    private function normalizePhone(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        return $digits;
    }
}
