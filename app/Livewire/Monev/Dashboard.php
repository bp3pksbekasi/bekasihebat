<?php

declare(strict_types=1);

namespace App\Livewire\Monev;

use App\Models\CatatanMonev;
use App\Models\TargetWilayah;
use App\Support\Monev\MonevScorecardService;
use App\Traits\WithWilayahScope;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    use WithWilayahScope;

    public string $filterStatus    = 'terbuka';
    public string $filterKecamatan = '';

    // Lazy loading state — data tidak dimuat saat render pertama
    public bool $loaded = false;

    // Form catat temuan
    public bool   $showCatatForm     = false;
    public array  $selectedFlag      = [];
    public string $formTemuan        = '';
    public string $formTindakLanjut  = '';
    public string $formPicNama       = '';
    public string $formLevel         = 'dpc';

    // Form tandai selesai
    public string $selesaiId           = '';
    public string $selesaiTindakLanjut = '';
    public bool   $showSelesaiForm     = false;

    public function mount(): void
    {
        $scope = $this->accessScope();
        if (! empty($scope['kecamatan'])) {
            // DPC/DPRA: kecamatan sudah fix, langsung load
            $this->filterKecamatan = $scope['kecamatan'];
            $this->loaded = true;
        }
        // DPD/admin: $loaded tetap false, tunggu user pilih kecamatan
    }

    // ─── Computed Properties (hanya dijalankan setelah $loaded = true) ────

    #[Computed]
    public function flags(): Collection
    {
        if (! $this->loaded) {
            return collect();
        }

        $service = app(MonevScorecardService::class);
        $scope   = $this->accessScope();

        $scopeFilter = [];
        if (! empty($scope['kecamatan'])) {
            $scopeFilter['kecamatan'] = $scope['kecamatan'];
        }
        if (! empty($scope['desa'])) {
            $scopeFilter['desa'] = $scope['desa'];
        }
        if ($this->filterKecamatan !== '' && empty($scope['kecamatan'])) {
            $scopeFilter['kecamatan'] = $this->filterKecamatan;
        }

        $flags = $service->semuaFlag($scopeFilter);

        // Tandai flag yang sudah punya catatan terbuka — satu query saja
        $openCatatans = CatatanMonev::query()
            ->where('status', CatatanMonev::STATUS_TERBUKA)
            ->get(['target_wilayah_id', 'nomor_rw', 'jenis_temuan'])
            ->groupBy(fn ($c) => $c->target_wilayah_id . '|' . $c->nomor_rw . '|' . $c->jenis_temuan);

        return $flags->map(function (array $flag) use ($openCatatans): array {
            $key = ($flag['target_wilayah_id'] ?? '') . '|' . ($flag['nomor_rw'] ?? '') . '|' . ($flag['jenis'] ?? '');
            $flag['has_open_catatan'] = $openCatatans->has($key);
            return $flag;
        });
    }

    #[Computed]
    public function flagsGrouped(): Collection
    {
        return $this->flags
            ->groupBy('kecamatan')
            ->map(fn ($items) => $items->groupBy('desa'));
    }

    #[Computed]
    public function catatanList(): Collection
    {
        if (! $this->loaded) {
            return collect();
        }

        $scope = $this->accessScope();

        $query = CatatanMonev::query()
            ->with(['targetWilayah', 'creator'])
            ->where('status', $this->filterStatus)
            ->latest();

        if (! empty($scope['kecamatan'])) {
            $query->whereHas('targetWilayah', fn ($q) => $q->where('kecamatan', $scope['kecamatan']));
        }
        if (! empty($scope['desa'])) {
            $query->whereHas('targetWilayah', fn ($q) => $q->where('desa', $scope['desa']));
        }

        return $query->get();
    }

    #[Computed]
    public function akuntabilitasDpc(): Collection
    {
        if (! $this->loaded) {
            return collect();
        }

        // Ambil semua flag (sudah di-cache)
        $semuaFlag     = app(MonevScorecardService::class)->semuaFlag();
        $kecamatanList = $semuaFlag->pluck('kecamatan')->unique()->filter()->sort()->values();

        if ($kecamatanList->isEmpty()) {
            return collect();
        }

        // Hitung total flag per kecamatan dari collection (tidak perlu query DB)
        $flagPerKec = $semuaFlag->groupBy('kecamatan')
            ->map(fn ($items) => $items->count());

        // Satu query untuk semua count catatan per kecamatan — tidak ada N+1
        $twIdsByKec = TargetWilayah::query()
            ->whereIn('kecamatan', $kecamatanList->toArray())
            ->get(['id', 'kecamatan'])
            ->groupBy('kecamatan')
            ->map(fn ($rows) => $rows->pluck('id'));

        // Semua catatan sekaligus, lalu group di PHP
        $allCatatanKec = CatatanMonev::query()
            ->whereIn(
                'target_wilayah_id',
                TargetWilayah::query()->whereIn('kecamatan', $kecamatanList->toArray())->pluck('id')
            )
            ->get(['target_wilayah_id', 'status', 'closed_at', 'created_at'])
            ->groupBy(function ($c) use ($twIdsByKec) {
                foreach ($twIdsByKec as $kec => $ids) {
                    if ($ids->contains($c->target_wilayah_id)) {
                        return $kec;
                    }
                }
                return null;
            })
            ->filter(fn ($_, $k) => $k !== null);

        return $kecamatanList->map(function (string $kec) use ($flagPerKec, $allCatatanKec): array {
            $totalFlag    = $flagPerKec->get($kec, 0);
            $catatanKec   = $allCatatanKec->get($kec, collect());
            $totalCatatan = $catatanKec->count();

            $pctTindakLanjut = $totalFlag > 0
                ? round(min(($totalCatatan / $totalFlag) * 100, 100))
                : 0;

            $avgUmurSelesai = $catatanKec
                ->where('status', CatatanMonev::STATUS_SELESAI)
                ->filter(fn ($c) => $c->closed_at !== null)
                ->avg(fn ($c) => Carbon::parse($c->created_at)->diffInDays(Carbon::parse($c->closed_at)));

            return [
                'kecamatan'         => $kec,
                'total_flag'        => $totalFlag,
                'pct_tindak_lanjut' => $pctTindakLanjut,
                'avg_umur_selesai'  => $avgUmurSelesai ? round((float) $avgUmurSelesai, 1) : null,
            ];
        })->sortBy('pct_tindak_lanjut')->values();
    }

    #[Computed]
    public function kecamatanOptions(): array
    {
        // Query ringan — tidak perlu lazy load
        return TargetWilayah::query()
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan')
            ->toArray();
    }

    #[Computed]
    public function ringkasan(): array
    {
        $flags = $this->flags;
        return [
            'total'         => $flags->count(),
            'merah'         => $flags->where('severity', 'merah')->count(),
            'kuning'        => $flags->where('severity', 'kuning')->count(),
            'sudah_dicatat' => $flags->where('has_open_catatan', true)->count(),
        ];
    }

    // ─── Form Catat Temuan ───────────────────────────────────────────

    public function bukaFormCatat(array $flag): void
    {
        $this->selectedFlag     = $flag;
        $this->formTemuan       = '';
        $this->formTindakLanjut = '';
        $this->formPicNama      = '';
        $this->formLevel        = 'dpc';
        $this->showCatatForm    = true;
    }

    public function tutupFormCatat(): void
    {
        $this->showCatatForm = false;
        $this->selectedFlag  = [];
    }

    public function simpanCatatan(): void
    {
        $this->validate([
            'formTemuan' => 'required|min:5',
            'formLevel'  => 'required|in:dpra,dpc,dpd',
        ], [
            'formTemuan.required' => 'Isi kolom temuan terlebih dahulu.',
            'formTemuan.min'      => 'Temuan minimal 5 karakter.',
        ]);

        $flag = $this->selectedFlag;

        CatatanMonev::create([
            'target_wilayah_id'      => $flag['target_wilayah_id'],
            'nomor_rw'               => $flag['nomor_rw'],
            'jenis_temuan'           => $flag['jenis'],
            'sumber'                 => CatatanMonev::SUMBER_OTOMATIS,
            'temuan'                 => $this->formTemuan ?: $flag['detail'],
            'tindak_lanjut'          => $this->formTindakLanjut ?: null,
            'status'                 => CatatanMonev::STATUS_TERBUKA,
            'level_penanggung_jawab' => $this->formLevel,
            'pic_nama'               => $this->formPicNama ?: null,
            'created_by'             => auth()->id(),
        ]);

        app(MonevScorecardService::class)->invalidateCache();
        $this->tutupFormCatat();
        unset($this->flags, $this->flagsGrouped, $this->catatanList, $this->ringkasan);
        $this->dispatch('notify', type: 'success', message: 'Catatan temuan berhasil disimpan.');
    }

    // ─── Tandai Selesai ──────────────────────────────────────────────

    public function bukaFormSelesai(string $id): void
    {
        $this->selesaiId           = $id;
        $this->selesaiTindakLanjut = '';
        $this->showSelesaiForm     = true;
    }

    public function tutupFormSelesai(): void
    {
        $this->showSelesaiForm = false;
        $this->selesaiId       = '';
    }

    public function tandaiSelesai(): void
    {
        $catatan = CatatanMonev::find($this->selesaiId);

        if (! $catatan) {
            $this->tutupFormSelesai();
            return;
        }

        $catatan->update([
            'status'        => CatatanMonev::STATUS_SELESAI,
            'tindak_lanjut' => $this->selesaiTindakLanjut ?: $catatan->tindak_lanjut,
            'closed_at'     => Carbon::now(),
        ]);

        app(MonevScorecardService::class)->invalidateCache();
        $this->tutupFormSelesai();
        unset($this->catatanList, $this->akuntabilitasDpc, $this->ringkasan);
        $this->dispatch('notify', type: 'success', message: 'Catatan berhasil ditandai selesai.');
    }

    public function updatedFilterStatus(): void
    {
        unset($this->catatanList);
    }

    public function updatedFilterKecamatan(): void
    {
        if ($this->filterKecamatan !== '') {
            $this->loaded = true;
        } else {
            $this->loaded = false;
        }
        unset($this->flags, $this->flagsGrouped, $this->ringkasan, $this->akuntabilitasDpc);
    }

    public function render()
    {
        return view('livewire.monev.dashboard', [
            'user'  => auth()->user(),
            'isDpd' => auth()->user()?->isAdmin() || auth()->user()?->isDpd(),
            'isDpc' => auth()->user()?->isDpc(),
        ])->layout('components.layouts.app');
    }
}
