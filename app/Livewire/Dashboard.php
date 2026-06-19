<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\DataRw;
use App\Models\AnggotaDewan;
use App\Models\Aspirasi;
use App\Models\KontenMedsos;
use App\Models\Event;
use App\Models\KegiatanRw;
use App\Models\Kader;
use App\Models\Korwe;
use App\Models\Korte;
use App\Models\PenggalangSuara;
use App\Models\ProfilRw;
use App\Models\TargetWilayah;
use App\Models\TitikRki;
use App\Models\TitikSenam;
use App\Models\UpaRwMember;
use App\Models\User;
use App\Services\WilayahService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Traits\WithWilayahScope;

class Dashboard extends Component
{
    public string $selectedDapil = '';

    public string $selectedKecamatan = '';

    public bool $showSidebar = false;

    public bool $showTopPanel = true;

    public int $selectedBulan;

    public int $selectedTahun;

    public function mount()
    {
        if (! $this->hasAdminAccess()) {
            if (auth()->check() && session('logged_in_via_admin')) {
                return redirect()->to('/sisir-rw');
            }
            abort(403);
        }

        $this->showSidebar = request()->has('sidebar') ? request()->boolean('sidebar') : true;
        $this->selectedBulan = (int) now()->month;
        $this->selectedTahun = (int) now()->year;

        if (($this->accessScope['mode'] ?? 'global') === 'dapil' && ($this->accessScope['locked_dapil'] ?? '') !== '') {
            $this->selectedDapil = (string) $this->accessScope['locked_dapil'];
            $this->selectedKecamatan = (string) ($this->accessScope['kecamatan'] ?? '');
        }
    }

    public function hideTopPanel(): void
    {
        $this->showTopPanel = false;
    }

    public function showTopPanel(): void
    {
        $this->showTopPanel = true;
    }

    public function updatedSelectedDapil(string $value): void
    {
        if (($this->accessScope['mode'] ?? 'global') === 'dapil' && ($this->accessScope['locked_dapil'] ?? '') !== '') {
            $this->selectedDapil = (string) $this->accessScope['locked_dapil'];
            $this->selectedKecamatan = (string) ($this->accessScope['kecamatan'] ?? '');
            return;
        }

        $this->selectedKecamatan = '';
    }

    public function selectKecamatan(string $kecamatan): void
    {
        $this->selectedKecamatan = $this->selectedKecamatan === $kecamatan ? '' : $kecamatan;
    }

    use WithWilayahScope;

    #[Computed]
    public function kpi(): array
    {
        $tahunField = $this->activeTargetYear();
        $periodeKegiatan = $this->filteredKegiatanQuery()->periode($this->selectedBulan, $this->selectedTahun);

        $targetKorwe = (int) $this->filteredTargetQuery()->sum("target_korwe_{$tahunField}");
        $korweTerbentuk = (int) $this->filteredKorweQuery()->where('status', 'terbentuk')->count();

        $targetKorte = (int) $this->filteredTargetQuery()->sum("target_korte_{$tahunField}");
        $korteTerbentuk = (int) $this->filteredKorteQuery()->where('status', 'terbentuk')->count();

        $totalRw = (int) $this->filteredRwQuery()->count();
        $rwTersisir = (int) ((clone $periodeKegiatan)
            ->selectRaw("COUNT(DISTINCT CONCAT(target_wilayah_id, ':', nomor_rw)) as total")
            ->value('total') ?? 0);
        $kegiatanBulanIni = (int) (clone $periodeKegiatan)->count();
        $wargaTerjangkau = (int) ((clone $periodeKegiatan)->sum('jumlah_warga') ?? 0);

        $profilTerisi = (int) $this->filteredProfilQuery()->where('completion_percent', '>', 0)->count();
        $profilLengkap = (int) $this->filteredProfilQuery()->where('is_complete', true)->count();

        $upaRw = (int) $this->filteredProfilQuery()->where('upa_rw_status', 'sudah')->count();
        $upaRwFormal = (int) $this->filteredUpaRwMemberQuery()
            ->selectRaw("COUNT(DISTINCT CONCAT(target_wilayah_id, ':', nomor_rw)) as total")
            ->value('total');
        $rki = (int) $this->filteredTitikRkiQuery()->aktif()->count();
        $ksn = (int) $this->filteredTitikSenamQuery()->aktif()->count();
        $relawan = (int) $this->filteredProfilQuery()->where('relawan_milenial_status', 'sudah')->count();
        $penggalang = (int) $this->filteredPenggalangQuery()->where('status', 'aktif')->count();
        $targetPenggalang = (int) $this->filteredTargetQuery()->sum('target_penggalang');
        $saksiTps = (int) $this->filteredKorteQuery()
            ->where('is_saksi_tps', true)
            ->where('status_saksi', 'terkonfirmasi')
            ->count();
        $totalTps = (int) $this->filteredTargetQuery()->sum('jumlah_tps');

        $eventAktif = (int) $this->filteredEventQuery()->whereIn('status', $this->eventActiveStatuses())->count();
        $eventMenunggu = (int) $this->filteredEventQuery()->whereIn('status', $this->eventPendingStatuses())->count();
        $eventSelesai = (int) $this->filteredEventQuery()->whereIn('status', $this->eventDoneStatuses())->count();

        $totalKader = (int) $this->filteredKaderQuery()->aktif()->count();
        $kaderBulanIni = (int) $this->filteredKaderQuery()
            ->where('status', 'aktif')
            ->whereMonth('created_at', $this->selectedBulan)
            ->whereYear('created_at', $this->selectedTahun)
            ->count();
        $avgPopularitas = round((float) (AnggotaDewan::query()->aktif()->avg('skor_popularitas') ?? 0), 1);
        $kontenBulanIni = (int) KontenMedsos::query()->bulanIni()->count();
        $aspirasiTotal = 0;
        $aspirasiSipd = 0;
        $aspirasiRealisasi = 0;
        $aspirasiStuck = 0;

        if (Schema::hasTable('aspirasis')) {
            $aspirasiTotal = (int) Aspirasi::query()->count();
            $aspirasiSipd = (int) Aspirasi::query()->whereIn('status', ['input_sipd', 'verifikasi_bappeda', 'dianggarkan', 'terealisasi'])->count();
            $aspirasiRealisasi = (int) Aspirasi::query()->where('status', 'terealisasi')->count();
            $aspirasiStuck = (int) Aspirasi::query()->stuck(14)->count();
        }

        return [
            'targetKorwe' => $targetKorwe,
            'korweTerbentuk' => $korweTerbentuk,
            'korwePct' => $targetKorwe > 0 ? (int) round(($korweTerbentuk / $targetKorwe) * 100) : 0,
            'targetKorte' => $targetKorte,
            'korteTerbentuk' => $korteTerbentuk,
            'kortePct' => $targetKorte > 0 ? (int) round(($korteTerbentuk / $targetKorte) * 100) : 0,
            'totalRw' => $totalRw,
            'rwTersisir' => $rwTersisir,
            'sisirPct' => $totalRw > 0 ? (int) round(($rwTersisir / $totalRw) * 100) : 0,
            'kegiatanBulanIni' => $kegiatanBulanIni,
            'wargaTerjangkau' => $wargaTerjangkau,
            'profilTerisi' => $profilTerisi,
            'profilLengkap' => $profilLengkap,
            'profilPct' => $totalRw > 0 ? (int) round(($profilTerisi / $totalRw) * 100) : 0,
            'upaRw' => $upaRw,
            'upaRwFormal' => $upaRwFormal,
            'rki' => $rki,
            'ksn' => $ksn,
            'relawan' => $relawan,
            'penggalang' => $penggalang,
            'targetPenggalang' => $targetPenggalang,
            'saksiTps' => $saksiTps,
            'totalTps' => $totalTps,
            'eventAktif' => $eventAktif,
            'eventMenunggu' => $eventMenunggu,
            'eventSelesai' => $eventSelesai,
            'totalKader' => $totalKader,
            'kaderBulanIni' => $kaderBulanIni,
            'avgPopularitas' => $avgPopularitas,
            'kontenBulanIni' => $kontenBulanIni,
            'aspirasiTotal' => $aspirasiTotal,
            'aspirasiSipd' => $aspirasiSipd,
            'aspirasiRealisasi' => $aspirasiRealisasi,
            'aspirasiStuck' => $aspirasiStuck,
        ];
    }

    #[Computed]
    public function alerts(): array
    {
        $alerts = [];

        $staleEvents = $this->filteredEventQuery()
            ->whereIn('status', $this->eventPendingStatuses())
            ->where('updated_at', '<', now()->subDays(7))
            ->count();

        if ($staleEvents > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'alert-circle',
                'text' => "<strong>" . number_format($staleEvents) . " event</strong> menunggu approval lebih dari 7 hari",
                'link' => route('events.index', ['status' => Event::STATUS_MENUNGGU]),
                'link_text' => 'Review',
            ];
        }

        $totalRw = (int) $this->filteredRwQuery()->count();
        $everVisited = (int) ($this->filteredKegiatanQuery()
            ->selectRaw("COUNT(DISTINCT CONCAT(target_wilayah_id, ':', nomor_rw)) as total")
            ->value('total') ?? 0);
        $neverVisited = max(0, $totalRw - $everVisited);

        if ($neverVisited > 0) {
            $pct = $totalRw > 0 ? (int) round(($neverVisited / $totalRw) * 100) : 0;
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'map-pin-off',
                'text' => "<strong>" . number_format($neverVisited) . " RW</strong> belum pernah dikunjungi ({$pct}% dari total)",
                'link' => route('sisir-rw.index'),
                'link_text' => 'Lihat',
            ];
        }

        $priorityTargetIds = $this->filteredRwQuery()
            ->whereIn('status_wilayah', ['POTENSIAL', 'RAWAN'])
            ->distinct()
            ->pluck('target_wilayah_id');

        $desaPrioritasTanpaKorwe = $priorityTargetIds->isEmpty()
            ? 0
            : TargetWilayah::query()
                ->whereIn('id', $priorityTargetIds)
                ->whereDoesntHave('korwes', fn (Builder $query) => $query->where('status', 'terbentuk'))
                ->count();

        if ($desaPrioritasTanpaKorwe > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'target-arrow',
                'text' => "<strong>" . number_format($desaPrioritasTanpaKorwe) . " desa prioritas</strong> belum ada KORWE terbentuk",
                'link' => route('infra-rtrw.index'),
                'link_text' => 'Lihat',
            ];
        }

        return $alerts;
    }

    #[Computed]
    public function operationalHighlights(): array
    {
        $periodStart = Carbon::create($this->selectedTahun, $this->selectedBulan, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $topDpc = $this->dapilProgress
            ->sortByDesc('score')
            ->first();

        $topKader = (clone $this->filteredKegiatanQuery())
            ->whereNotNull('created_by')
            ->whereBetween('tanggal_kegiatan', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->selectRaw('created_by, COUNT(*) as total_kegiatan, COALESCE(SUM(jumlah_warga), 0) as total_warga')
            ->groupBy('created_by')
            ->orderByDesc('total_kegiatan')
            ->orderByDesc('total_warga')
            ->first();

        $topKaderName = '-';
        $topKaderMeta = 'Belum ada kegiatan pada periode ini';
        $topKaderDetail = 'Pilih bulan lain untuk melihat produktivitas';

        if ($topKader !== null) {
            $topKaderName = User::query()->whereKey($topKader->created_by)->value('name') ?? 'Kader lapangan';
            $topKaderMeta = number_format((int) $topKader->total_kegiatan) . ' kegiatan dicatat';
            $topKaderDetail = number_format((int) $topKader->total_warga) . ' warga terjangkau';
        }

        $largestActivity = (clone $this->filteredKegiatanQuery())
            ->whereBetween('tanggal_kegiatan', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->orderByDesc('jumlah_warga')
            ->orderByDesc('tanggal_kegiatan')
            ->first();

        $newKorwe = $this->filteredKorweQuery()
            ->with('targetWilayah')
            ->where('status', 'terbentuk')
            ->where(function (Builder $query) use ($weekStart, $weekEnd): void {
                $query->whereBetween('tanggal_terbentuk', [$weekStart->toDateString(), $weekEnd->toDateString()])
                    ->orWhere(function (Builder $fallback) use ($weekStart, $weekEnd): void {
                        $fallback->whereNull('tanggal_terbentuk')
                            ->whereBetween('created_at', [$weekStart, $weekEnd]);
                    });
            })
            ->orderByDesc('tanggal_terbentuk')
            ->orderByDesc('created_at')
            ->first();

        return [
            [
                'label' => 'DPC paling aktif',
                'icon' => 'building-community',
                'theme' => 'orange',
                'value' => $topDpc['dapil'] ?? '-',
                'meta' => $topDpc !== null
                    ? 'Skor gabungan ' . number_format((float) $topDpc['score'], 1)
                    : 'Belum ada skor gabungan',
                'detail' => $topDpc !== null
                    ? 'Korwe ' . $topDpc['korwe_pct'] . '% · Sisir ' . $topDpc['sisir_pct'] . '% · Profil ' . $topDpc['profil_pct'] . '%'
                    : 'Data progress dapil belum tersedia',
            ],
            [
                'label' => 'Kader paling produktif',
                'icon' => 'user-star',
                'theme' => 'blue',
                'value' => $topKaderName,
                'meta' => $topKaderMeta,
                'detail' => $topKaderDetail,
            ],
            [
                'label' => 'Kegiatan peserta terbanyak',
                'icon' => 'users-group',
                'theme' => 'orange',
                'value' => $largestActivity?->jenis_config['label'] ?? '-',
                'meta' => $largestActivity !== null
                    ? number_format((int) $largestActivity->jumlah_warga) . ' peserta'
                    : 'Belum ada kegiatan pada periode ini',
                'detail' => $largestActivity !== null
                    ? $largestActivity->desa . ' · RW ' . $largestActivity->nomor_rw
                    : 'Periode ini belum memiliki kegiatan',
            ],
            [
                'label' => 'KORWE baru minggu ini',
                'icon' => 'user-check',
                'theme' => 'blue',
                'value' => $newKorwe?->nama_koordinator ?? '-',
                'meta' => $newKorwe !== null
                    ? ($newKorwe->targetWilayah->desa ?? '-') . ' · RW ' . $newKorwe->nomor_rw
                    : 'Belum ada KORWE baru minggu ini',
                'detail' => $newKorwe !== null
                    ? 'Terbentuk ' . ($newKorwe->tanggal_terbentuk?->translatedFormat('d M Y') ?? $newKorwe->created_at?->format('d M Y'))
                    : 'Pantau update pembentukan KORWE',
            ],
        ];
    }

    #[Computed]
    public function dapilProgress(): Collection
    {
        $tahunField = $this->activeTargetYear();
        $dapils = $this->selectedDapil !== ''
            ? collect([$this->selectedDapil])
            : TargetWilayah::query()->distinct()->orderBy('dapil')->pluck('dapil');

        return $dapils->map(function (string $dapil) use ($tahunField): array {
            $targetKorwe = (int) TargetWilayah::query()->where('dapil', $dapil)->sum("target_korwe_{$tahunField}");
            $korweTerbentuk = (int) Korwe::query()
                ->whereHas('targetWilayah', fn (Builder $query) => $query->where('dapil', $dapil))
                ->where('status', 'terbentuk')
                ->count();

            $totalRw = (int) DataRw::query()->where('dapil', $dapil)->count();
            $rwTersisir = (int) (KegiatanRw::query()
                ->where('dapil', $dapil)
                ->whereMonth('tanggal_kegiatan', $this->selectedBulan)
                ->whereYear('tanggal_kegiatan', $this->selectedTahun)
                ->selectRaw("COUNT(DISTINCT CONCAT(target_wilayah_id, ':', nomor_rw)) as total")
                ->value('total') ?? 0);

            $profilTerisi = (int) ProfilRw::query()->where('dapil', $dapil)->where('completion_percent', '>', 0)->count();
            $upaRw = (int) ProfilRw::query()->where('dapil', $dapil)->where('upa_rw_status', 'sudah')->count();
            $rki = (int) TitikRki::query()->where('dapil', $dapil)->where('status', 'aktif')->count();
            $ksn = (int) TitikSenam::query()->where('dapil', $dapil)->where('status', 'aktif')->count();

            $korwePct = $targetKorwe > 0 ? (int) round(($korweTerbentuk / $targetKorwe) * 100) : 0;
            $sisirPct = $totalRw > 0 ? (int) round(($rwTersisir / $totalRw) * 100) : 0;
            $profilPct = $totalRw > 0 ? (int) round(($profilTerisi / $totalRw) * 100) : 0;
            $infraPct = $totalRw > 0 ? (int) round((($upaRw + $rki + $ksn) / max($totalRw * 3, 1)) * 100) : 0;
            $score = round(($korwePct * 0.4) + ($sisirPct * 0.3) + ($profilPct * 0.2) + ($infraPct * 0.1), 1);

            return [
                'dapil' => $dapil,
                'korwe_pct' => $korwePct,
                'sisir_pct' => $sisirPct,
                'profil_pct' => $profilPct,
                'infra_pct' => $infraPct,
                'upa_rw' => $upaRw,
                'rki' => $rki,
                'ksn' => $ksn,
                'korwe_detail' => "{$korweTerbentuk}/{$targetKorwe}",
                'sisir_detail' => "{$rwTersisir}/{$totalRw}",
                'profil_detail' => "{$profilTerisi}/{$totalRw}",
                'score' => $score,
            ];
        })->values();
    }

    #[Computed]
    public function dapilMap(): Collection
    {
        $tahunField = $this->activeTargetYear();
        $dapils = TargetWilayah::query()
            ->distinct()
            ->orderBy('dapil')
            ->pluck('dapil');

        return $dapils->map(function (string $dapil) use ($tahunField): array {
            $desaTotal = (int) TargetWilayah::query()->where('dapil', $dapil)->count();
            $targetKorwe = (int) TargetWilayah::query()->where('dapil', $dapil)->sum("target_korwe_{$tahunField}");
            $korweTerbentuk = (int) Korwe::query()
                ->whereHas('targetWilayah', fn (Builder $query) => $query->where('dapil', $dapil))
                ->where('status', 'terbentuk')
                ->count();

            $totalRw = (int) DataRw::query()->where('dapil', $dapil)->count();
            $rwTersisir = (int) (KegiatanRw::query()
                ->where('dapil', $dapil)
                ->whereMonth('tanggal_kegiatan', $this->selectedBulan)
                ->whereYear('tanggal_kegiatan', $this->selectedTahun)
                ->selectRaw("COUNT(DISTINCT CONCAT(target_wilayah_id, ':', nomor_rw)) as total")
                ->value('total') ?? 0);

            $profilTerisi = (int) ProfilRw::query()
                ->where('dapil', $dapil)
                ->where('completion_percent', '>', 0)
                ->count();

            $korwePct = $targetKorwe > 0 ? (int) round(($korweTerbentuk / $targetKorwe) * 100) : 0;
            $sisirPct = $totalRw > 0 ? (int) round(($rwTersisir / $totalRw) * 100) : 0;
            $profilPct = $totalRw > 0 ? (int) round(($profilTerisi / $totalRw) * 100) : 0;
            $score = (int) round(($korwePct * 0.4) + ($sisirPct * 0.35) + ($profilPct * 0.25));
            $dapilNumber = (string) preg_replace('/[^0-9]/', '', $dapil);

            return [
                'dapil' => $dapil,
                'dapil_number' => $dapilNumber,
                'desa_total' => $desaTotal,
                'total_rw' => $totalRw,
                'rw_tersisir' => $rwTersisir,
                'korwe_pct' => $korwePct,
                'sisir_pct' => $sisirPct,
                'profil_pct' => $profilPct,
                'score' => $score,
                'active' => $this->selectedDapil === $dapil,
                'image' => $dapilNumber !== '' ? asset("images/peta/dapil{$dapilNumber}.png") : null,
            ];
        })->values();
    }

    #[Computed]
    public function kecamatanMap(): Collection
    {
        if ($this->selectedDapil === '') {
            return collect();
        }

        $tahunField = $this->activeTargetYear();
        $userKecamatan = mb_strtoupper((string) ($this->accessScope['kecamatan'] ?? ''));
        $kecamatans = TargetWilayah::query()
            ->where('dapil', $this->selectedDapil)
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');

        return $kecamatans->map(function (string $kecamatan) use ($tahunField, $userKecamatan): array {
            $desaTotal = (int) TargetWilayah::query()
                ->where('dapil', $this->selectedDapil)
                ->where('kecamatan', $kecamatan)
                ->count();

            $targetKorwe = (int) TargetWilayah::query()
                ->where('dapil', $this->selectedDapil)
                ->where('kecamatan', $kecamatan)
                ->sum("target_korwe_{$tahunField}");

            $korweTerbentuk = (int) Korwe::query()
                ->whereHas('targetWilayah', fn (Builder $query) => $query
                    ->where('dapil', $this->selectedDapil)
                    ->where('kecamatan', $kecamatan))
                ->where('status', 'terbentuk')
                ->count();

            $totalRw = (int) DataRw::query()
                ->where('dapil', $this->selectedDapil)
                ->where('kecamatan', $kecamatan)
                ->count();

            $rwTersisir = (int) (KegiatanRw::query()
                ->where('dapil', $this->selectedDapil)
                ->where('kecamatan', $kecamatan)
                ->whereMonth('tanggal_kegiatan', $this->selectedBulan)
                ->whereYear('tanggal_kegiatan', $this->selectedTahun)
                ->selectRaw("COUNT(DISTINCT CONCAT(target_wilayah_id, ':', nomor_rw)) as total")
                ->value('total') ?? 0);

            $profilTerisi = (int) ProfilRw::query()
                ->where('dapil', $this->selectedDapil)
                ->where('kecamatan', $kecamatan)
                ->where('completion_percent', '>', 0)
                ->count();

            $korwePct = $targetKorwe > 0 ? (int) round(($korweTerbentuk / $targetKorwe) * 100) : 0;
            $sisirPct = $totalRw > 0 ? (int) round(($rwTersisir / $totalRw) * 100) : 0;
            $profilPct = $totalRw > 0 ? (int) round(($profilTerisi / $totalRw) * 100) : 0;
            $score = (int) round(($korwePct * 0.4) + ($sisirPct * 0.35) + ($profilPct * 0.25));

            return [
                'kecamatan' => $kecamatan,
                'desa_total' => $desaTotal,
                'total_rw' => $totalRw,
                'korwe_pct' => $korwePct,
                'sisir_pct' => $sisirPct,
                'profil_pct' => $profilPct,
                'score' => $score,
                'active' => $this->selectedKecamatan !== ''
                    ? $this->selectedKecamatan === $kecamatan
                    : ($userKecamatan !== '' && $userKecamatan === mb_strtoupper($kecamatan)),
                'image' => asset('images/peta/kecamatan/' . $this->kecamatanImageSlug($kecamatan) . '.png'),
            ];
        })->values();
    }

    #[Computed]
    public function timeline(): Collection
    {
        $items = collect();

        $this->filteredKegiatanQuery()
            ->with('creator')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->each(function (KegiatanRw $kegiatan) use ($items): void {
                $items->push([
                    'time' => $kegiatan->created_at ?? $kegiatan->tanggal_kegiatan,
                    'color' => $kegiatan->jenis_config['color'] ?? '#d97706',
                    'title' => $kegiatan->jenis_config['label'] . ' — RW ' . $kegiatan->nomor_rw . ' ' . $kegiatan->desa,
                    'desc' => number_format((int) $kegiatan->jumlah_warga) . ' warga · ' . $kegiatan->pelaksana,
                    'icon' => 'walk',
                ]);
            });

        $this->filteredKorweQuery()
            ->with('targetWilayah')
            ->where('status', 'terbentuk')
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get()
            ->each(function (Korwe $korwe) use ($items): void {
                $items->push([
                    'time' => $korwe->updated_at,
                    'color' => '#16a34a',
                    'title' => 'KORWE terbentuk — RW ' . $korwe->nomor_rw . ' ' . ($korwe->targetWilayah->desa ?? ''),
                    'desc' => (string) $korwe->nama_koordinator,
                    'icon' => 'user-check',
                ]);
            });

        $this->filteredEventQuery()
            ->orderByDesc('updated_at')
            ->whereIn('status', array_unique(array_merge($this->eventPendingStatuses(), $this->eventActiveStatuses())))
            ->limit(3)
            ->get()
            ->each(function (Event $event) use ($items): void {
                $items->push([
                    'time' => $event->updated_at,
                    'color' => '#2563eb',
                    'title' => 'Event ' . ($event->status_config['label'] ?? 'Update') . ' — ' . $event->judul,
                    'desc' => 'Level: ' . strtoupper((string) ($event->level_approval ?: '-')),
                    'icon' => 'calendar-event',
                ]);
            });

        User::query()
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->each(function (User $user) use ($items): void {
                $items->push([
                    'time' => $user->created_at,
                    'color' => '#7c3aed',
                    'title' => 'Member baru terdaftar',
                    'desc' => $user->name . ' · ' . $user->email,
                    'icon' => 'user-plus',
                ]);
            });

        return $items
            ->sortByDesc('time')
            ->take(8)
            ->values();
    }

    #[Computed]
    public function trend(): array
    {
        $anchor = Carbon::create($this->selectedTahun, $this->selectedBulan, 1)->startOfMonth();
        $months = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = $anchor->copy()->subMonths($i);
            $count = (int) $this->filteredKegiatanQuery()
                ->whereMonth('tanggal_kegiatan', $date->month)
                ->whereYear('tanggal_kegiatan', $date->year)
                ->count();

            $months->push([
                'label' => $date->translatedFormat('M'),
                'count' => $count,
                'current' => $date->isSameMonth($anchor),
            ]);
        }

        $max = max((int) $months->max('count'), 1);
        $normalized = $months->map(fn (array $month): array => array_merge($month, [
            'pct' => (int) round(($month['count'] / $max) * 100),
        ]));

        $current = (int) ($normalized->last()['count'] ?? 0);
        $previous = (int) ($normalized->slice(-2, 1)->first()['count'] ?? 0);
        $change = $previous > 0 ? (int) round((($current - $previous) / $previous) * 100) : ($current > 0 ? 100 : 0);

        return [
            'months' => $normalized,
            'change_pct' => $change,
            'current' => $current,
            'previous' => $previous,
        ];
    }

    #[Computed]
    public function infraTrends(): Collection
    {
        return collect([
            [
                'label' => 'Korwe',
                'color' => '#f97316',
                'bg' => '#fff7ed',
                'date_column' => 'updated_at',
                'query' => $this->filteredKorweQuery()->where('status', 'terbentuk'),
            ],
            [
                'label' => 'UPA RW',
                'color' => '#10b981',
                'bg' => '#ecfdf5',
                'date_column' => 'updated_at',
                'query' => $this->filteredProfilQuery()->where('upa_rw_status', 'sudah'),
            ],
            [
                'label' => 'RKI Aktif',
                'color' => '#2563eb',
                'bg' => '#eff6ff',
                'date_column' => 'updated_at',
                'query' => $this->filteredTitikRkiQuery()->where('status', 'aktif'),
            ],
            [
                'label' => 'KSN / Senam',
                'color' => '#d97706',
                'bg' => '#fffbeb',
                'date_column' => 'updated_at',
                'query' => $this->filteredTitikSenamQuery()->where('status', 'aktif'),
            ],
            [
                'label' => 'Relawan Milenial',
                'color' => '#7c3aed',
                'bg' => '#faf5ff',
                'date_column' => 'updated_at',
                'query' => $this->filteredProfilQuery()->where('relawan_milenial_status', 'sudah'),
            ],
        ])->map(function (array $item): array {
            return array_merge($item, $this->buildMonthlyTrend(
                query: $item['query'],
                dateColumn: $item['date_column'],
            ));
        });
    }

    #[Computed]
    public function statusWilayah(): Collection
    {
        $counts = $this->filteredRwQuery()
            ->selectRaw('status_wilayah, COUNT(DISTINCT target_wilayah_id) as total')
            ->groupBy('status_wilayah')
            ->pluck('total', 'status_wilayah');

        $cards = [
            ['key' => 'SANGAT KUAT', 'label' => 'Jaga Kuat', 'bg' => '#dcfce7', 'text' => '#166534'],
            ['key' => 'KUAT', 'label' => 'Amankan', 'bg' => '#dbeafe', 'text' => '#1d4ed8'],
            ['key' => 'POTENSIAL', 'label' => 'Rebut', 'bg' => '#fef3c7', 'text' => '#b45309'],
            ['key' => 'RAWAN', 'label' => 'Garap', 'bg' => '#ffedd5', 'text' => '#c2410c'],
            ['key' => 'ZONA BERAT', 'label' => 'Zona Berat', 'bg' => '#e5e7eb', 'text' => '#52525b'],
        ];

        return collect($cards)->map(function (array $card) use ($counts): array {
            return array_merge($card, [
                'total' => (int) ($counts[$card['key']] ?? 0),
            ]);
        });
    }

    #[Computed]
    public function leaderboard(): Collection
    {
        return $this->dapilProgress
            ->sortByDesc('score')
            ->values();
    }

    #[Computed]
    public function quickAccess(): array
    {
        $kpi = $this->kpi;

        return [
            [
                'icon' => 'map-search',
                'label' => 'Bedah Dapil',
                'value' => number_format($this->statusWilayah->sum('total')) . ' desa',
                'hint' => 'Status wilayah terpetakan',
                'route' => route('bedah-dapil.index'),
                'color' => '#fe5000',
            ],
            [
                'icon' => 'building-community',
                'label' => 'Infra RT/RW',
                'value' => number_format($kpi['korweTerbentuk']) . ' KORWE',
                'hint' => 'KORWE terbentuk',
                'route' => route('infra-rtrw.index'),
                'color' => '#16a34a',
            ],
            [
                'icon' => 'walk',
                'label' => 'Sisir RW',
                'value' => number_format($kpi['rwTersisir']) . ' RW',
                'hint' => 'Tersisir bulan ini',
                'route' => route('sisir-rw.index'),
                'color' => '#d97706',
            ],
            [
                'icon' => 'calendar-event',
                'label' => 'Event',
                'value' => number_format($kpi['eventMenunggu']) . ' pending',
                'hint' => 'Perlu approval',
                'route' => route('events.index'),
                'color' => '#2563eb',
            ],
            [
                'icon' => 'world-www',
                'label' => 'Kaderisasi',
                'value' => number_format($kpi['totalKader']) . ' kader',
                'hint' => '+' . number_format($kpi['kaderBulanIni']) . ' kader bulan ini',
                'route' => route('kaderisasi.index'),
                'color' => '#7c3aed',
            ],
        ];
    }

    #[Computed]
    public function dapilOptions(): Collection
    {
        return TargetWilayah::query()
            ->distinct()
            ->orderBy('dapil')
            ->pluck('dapil');
    }

    #[Computed]
    public function periodLabel(): string
    {
        return Carbon::create($this->selectedTahun, $this->selectedBulan, 1)->translatedFormat('F Y');
    }

    public function render(): View
    {
        $layout = $this->showSidebar
            ? 'components.layouts.app-fullwidth'
            : 'components.layouts.app-dashboard-fullscreen';

        return view('livewire.dashboard')
            ->layout($layout, [
                'title' => 'Dashboard Admin',
            ]);
    }

    private function hasAdminAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        $isAdminRole = (method_exists($user, 'isAdmin') && $user->isAdmin()) || 
                       (method_exists($user, 'isBidang') && $user->isBidang()) || 
                       (method_exists($user, 'isDapil') && $user->isDapil()) ||
                       (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'pengurus', 'pengurus_dpd', 'dpd', 'super-admin', 'super admin', 'dapil']));

        if ($isAdminRole) {
            if (session('logged_in_via_admin') !== true) {
                session(['logged_in_via_admin' => true]);
            }
            return true;
        }

        return false;
    }

    private function activeTargetYear(): int
    {
        return min(max($this->selectedTahun, 2026), 2029);
    }

    private function buildMonthlyTrend(Builder $query, string $dateColumn = 'updated_at'): array
    {
        $anchor = Carbon::create($this->selectedTahun, $this->selectedBulan, 1)->startOfMonth();
        $months = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = $anchor->copy()->subMonths($i);
            $count = (int) (clone $query)
                ->whereMonth($dateColumn, $date->month)
                ->whereYear($dateColumn, $date->year)
                ->count();

            $months->push([
                'label' => $date->translatedFormat('M'),
                'count' => $count,
                'current' => $date->isSameMonth($anchor),
            ]);
        }

        $max = max((int) $months->max('count'), 1);
        $normalized = $months->map(fn (array $month): array => array_merge($month, [
            'pct' => (int) round(($month['count'] / $max) * 100),
        ]));

        $current = (int) ($normalized->last()['count'] ?? 0);
        $previous = (int) ($normalized->slice(-2, 1)->first()['count'] ?? 0);
        $change = $previous > 0 ? (int) round((($current - $previous) / $previous) * 100) : ($current > 0 ? 100 : 0);

        return [
            'months' => $normalized,
            'change_pct' => $change,
            'current' => $current,
            'previous' => $previous,
            'max' => $max,
        ];
    }

    private function filteredTargetQuery(): Builder
    {
        return TargetWilayah::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan));
    }

    private function filteredRwQuery(): Builder
    {
        return DataRw::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan));
    }

    private function filteredKegiatanQuery(): Builder
    {
        return KegiatanRw::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan));
    }

    private function filteredKorweQuery(): Builder
    {
        return Korwe::query()
            ->when(
                $this->selectedDapil !== '' || $this->selectedKecamatan !== '',
                fn (Builder $query) => $query->whereHas('targetWilayah', function (Builder $target): void {
                    $target
                        ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
                        ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan));
                })
            );
    }

    private function filteredKorteQuery(): Builder
    {
        return Korte::query()
            ->when(
                $this->selectedDapil !== '' || $this->selectedKecamatan !== '',
                fn (Builder $query) => $query->whereHas('targetWilayah', function (Builder $target): void {
                    $target
                        ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
                        ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan));
                })
            );
    }

    private function filteredProfilQuery(): Builder
    {
        return ProfilRw::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan));
    }

    private function filteredPenggalangQuery(): Builder
    {
        return PenggalangSuara::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan));
    }

    private function filteredTitikRkiQuery(): Builder
    {
        return TitikRki::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan));
    }

    private function filteredTitikSenamQuery(): Builder
    {
        return TitikSenam::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan));
    }

    private function filteredUpaRwMemberQuery(): Builder
    {
        return UpaRwMember::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan));
    }

    private function filteredEventQuery(): Builder
    {
        return Event::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('lokasi_dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('lokasi_kecamatan', $this->selectedKecamatan));
    }

    private function filteredUserQuery(): Builder
    {
        return User::query()
            ->when(
                $this->selectedDapil !== '' || $this->selectedKecamatan !== '',
                fn (Builder $query) => $query->whereIn('kelurahan_code', $this->activeKelurahanCodes())
            );
    }

    private function filteredKaderQuery(): Builder
    {
        return Kader::query()
            ->when($this->selectedDapil !== '', fn (Builder $query) => $query->where('dapil', $this->selectedDapil))
            ->when($this->selectedKecamatan !== '', fn (Builder $query) => $query->where('kecamatan', $this->selectedKecamatan));
    }

    private function activeKelurahanCodes(): Collection
    {
        $query = DB::table('indonesia_villages as village')
            ->join('indonesia_districts as district', DB::raw('LEFT(village.code, 6)'), '=', 'district.code');

        if ($this->selectedKecamatan !== '') {
            $query->whereRaw('UPPER(district.name) = ?', [mb_strtoupper($this->selectedKecamatan)]);
        } elseif ($this->selectedDapil !== '') {
            $kecamatans = TargetWilayah::query()
                ->where('dapil', $this->selectedDapil)
                ->distinct()
                ->pluck('kecamatan')
                ->map(fn ($kecamatan) => mb_strtoupper((string) $kecamatan))
                ->filter()
                ->values();

            if ($kecamatans->isEmpty()) {
                return collect([-1]);
            }

            $query->whereIn(DB::raw('UPPER(district.name)'), $kecamatans);
        }

        return $query->pluck('village.code');
    }

    /**
     * @return array<int, string>
     */
    private function eventActiveStatuses(): array
    {
        return array_values(array_unique([
            Event::STATUS_DISETUJUI,
            Event::STATUS_BERLANGSUNG,
            'disetujui',
            'approved',
            'berlangsung',
        ]));
    }

    /**
     * @return array<int, string>
     */
    private function eventPendingStatuses(): array
    {
        return array_values(array_unique([
            Event::STATUS_MENUNGGU,
            'menunggu_approval',
            'pending_approval',
        ]));
    }

    /**
     * @return array<int, string>
     */
    private function eventDoneStatuses(): array
    {
        return array_values(array_unique([
            Event::STATUS_SELESAI,
            'selesai',
            'completed',
        ]));
    }

    private function kecamatanImageSlug(string $kecamatan): string
    {
        return [
            'BABELAN' => 'babelan',
            'BOJONGMANGU' => 'bojongmangu',
            'CABANGBUNGIN' => 'cabangbungin',
            'CIBARUSAH' => 'cibarusah',
            'CIBITUNG' => 'cibitung',
            'CIKARANG BARAT' => 'cikarang-barat',
            'CIKARANG PUSAT' => 'cikarang-pusat',
            'CIKARANG SELATAN' => 'cikarang-selatan',
            'CIKARANG TIMUR' => 'cikarang-timur',
            'CIKARANG UTARA' => 'cikarang-utara',
            'KARANG BAHAGIA' => 'karang-bahagia',
            'KEDUNG WARINGIN' => 'kedungwaringin',
            'MUARAGEMBONG' => 'muaragembong',
            'PEBAYURAN' => 'pebayuran',
            'SERANG BARU' => 'serangbaru',
            'SETU' => 'setu',
            'SUKAKARYA' => 'sukakarya',
            'SUKATANI' => 'sukatani',
            'SUKAWANGI' => 'sukawangi',
            'TAMBELANG' => 'tambelang',
            'TAMBUN SELATAN' => 'tambun-selatan',
            'TAMBUN UTARA' => 'tambun-utara',
            'TARUMAJAYA' => 'tarumajaya',
        ][mb_strtoupper($kecamatan)] ?? str_replace(' ', '-', strtolower($kecamatan));
    }
}
