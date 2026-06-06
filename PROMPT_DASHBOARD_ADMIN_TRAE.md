# Prompt Trae — Dashboard Admin Kontrol Pencapaian

```
Redesign halaman dashboard BACKEND ADMIN (/dashboard) menjadi pusat kontrol pencapaian semua modul. Halaman ini menggunakan layout SIDEBAR (admin), BUKAN layout publik. Hanya bisa diakses user yang sudah login sebagai admin/pengurus.

PENTING: Ini BUKAN dashboard member website publik. Dashboard member publik akan dibuat terpisah nanti di route /member/dashboard dengan layout public (navbar+footer). Jangan campur.

Data realtime dari database. Langsung kerjakan, JANGAN test, JANGAN tanya.

== 1. UPDATE ROUTE ==

Pastikan route dashboard admin:

Route::get('/dashboard', App\Livewire\Dashboard::class)->middleware(['auth'])->name('dashboard');

JANGAN GANGGU route publik di '/' — itu tetap homepage website public.

== 2. MIGRATION TAMBAHAN ==

CATATAN: KSN (Kelompok Senam Nusantara) = titik Senam PKS. Data ini sudah ada di kolom `senam_status` dan `senam_nama` di tabel profil_rws. TIDAK perlu migration baru. Di UI, tampilkan label "KSN / Senam PKS".

== 3. LIVEWIRE COMPONENT: app/Livewire/Dashboard.php ==

```php
<?php
namespace App\Livewire;

use App\Models\Event;
use App\Models\KegiatanRw;
use App\Models\Korwe;
use App\Models\Korte;
use App\Models\ProfilRw;
use App\Models\TargetWilayah;
use App\Models\DataRw;
use App\Models\User;
use Livewire\Component;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $selectedDapil = '';
    public $selectedBulan;
    public $selectedTahun;

    public function mount()
    {
        $this->selectedBulan = now()->month;
        $this->selectedTahun = now()->year;
    }

    // === KPI UTAMA ===

    public function getKpiProperty()
    {
        $dapilFilter = fn($q) => $this->selectedDapil ? $q->where('dapil', $this->selectedDapil) : $q;

        // Target tahun aktif
        $tahunField = $this->selectedTahun <= 2029 ? $this->selectedTahun : 2029;

        // KORWE
        $targetKorwe = TargetWilayah::when($this->selectedDapil, $dapilFilter)->sum("target_korwe_{$tahunField}");
        $korweTerbentuk = Korwe::whereHas('targetWilayah', $dapilFilter)->where('status', 'terbentuk')->count();

        // KORTE
        $targetKorte = TargetWilayah::when($this->selectedDapil, $dapilFilter)->sum("target_korte_{$tahunField}");
        $korteTerbentuk = Korte::whereHas('targetWilayah', $dapilFilter)->where('status', 'terbentuk')->count();

        // RW tersisir bulan ini
        $totalRw = DataRw::when($this->selectedDapil, $dapilFilter)->count();
        $rwTersisir = KegiatanRw::when($this->selectedDapil, $dapilFilter)
            ->whereMonth('tanggal_kegiatan', $this->selectedBulan)
            ->whereYear('tanggal_kegiatan', $this->selectedTahun)
            ->selectRaw('DISTINCT CONCAT(target_wilayah_id, nomor_rw)')->get()->count();
        $kegiatanBulanIni = KegiatanRw::when($this->selectedDapil, $dapilFilter)
            ->whereMonth('tanggal_kegiatan', $this->selectedBulan)
            ->whereYear('tanggal_kegiatan', $this->selectedTahun)->count();
        $wargaTerjangkau = KegiatanRw::when($this->selectedDapil, $dapilFilter)
            ->whereMonth('tanggal_kegiatan', $this->selectedBulan)
            ->whereYear('tanggal_kegiatan', $this->selectedTahun)->sum('jumlah_warga');

        // Profil RW
        $profilTerisi = ProfilRw::when($this->selectedDapil, $dapilFilter)->where('completion_percent', '>', 0)->count();
        $profilLengkap = ProfilRw::when($this->selectedDapil, $dapilFilter)->where('is_complete', true)->count();

        // UPA RW
        $upaRw = ProfilRw::when($this->selectedDapil, $dapilFilter)->where('upa_rw_status', 'sudah')->count();

        // RKI
        $rki = ProfilRw::when($this->selectedDapil, $dapilFilter)->where('rki_status', 'sudah')->count();

        // KSN / Senam PKS (KSN = Kelompok Senam Nusantara = titik senam PKS)
        $ksn = ProfilRw::when($this->selectedDapil, $dapilFilter)->where('senam_status', 'sudah')->count();

        // Event
        $eventAktif = Event::when($this->selectedDapil, fn($q) => $q->where('lokasi_dapil', $this->selectedDapil))
            ->whereIn('status', ['disetujui', 'berlangsung'])->count();
        $eventMenunggu = Event::when($this->selectedDapil, fn($q) => $q->where('lokasi_dapil', $this->selectedDapil))
            ->where('status', 'menunggu_approval')->count();
        $eventSelesai = Event::when($this->selectedDapil, fn($q) => $q->where('lokasi_dapil', $this->selectedDapil))
            ->where('status', 'selesai')->count();

        // Member website publik
        $totalMember = User::count();
        $memberBulanIni = User::whereMonth('created_at', $this->selectedBulan)
            ->whereYear('created_at', $this->selectedTahun)->count();

        // Relawan Milenial
        $relawan = ProfilRw::when($this->selectedDapil, $dapilFilter)->where('relawan_milenial_status', 'sudah')->count();

        return compact(
            'targetKorwe', 'korweTerbentuk',
            'targetKorte', 'korteTerbentuk',
            'totalRw', 'rwTersisir', 'kegiatanBulanIni', 'wargaTerjangkau',
            'profilTerisi', 'profilLengkap',
            'upaRw', 'rki', 'ksn', 'relawan',
            'eventAktif', 'eventMenunggu', 'eventSelesai',
            'totalMember', 'memberBulanIni'
        );
    }

    // === ALERTS ===

    public function getAlertsProperty()
    {
        $alerts = [];

        // Event menunggu approval > 7 hari
        $staleEvents = Event::where('status', 'menunggu_approval')
            ->where('updated_at', '<', now()->subDays(7))->count();
        if ($staleEvents > 0) {
            $alerts[] = ['type' => 'warning', 'icon' => 'alert-circle',
                'text' => "<strong>{$staleEvents} event</strong> menunggu approval lebih dari 7 hari",
                'link' => route('events.index', ['status' => 'menunggu_approval']), 'link_text' => 'Review'];
        }

        // RW belum pernah dikunjungi
        $totalRw = DataRw::count();
        $everVisited = KegiatanRw::selectRaw('DISTINCT CONCAT(target_wilayah_id, nomor_rw)')->get()->count();
        $neverVisited = $totalRw - $everVisited;
        if ($neverVisited > 0) {
            $pct = $totalRw > 0 ? round($neverVisited / $totalRw * 100) : 0;
            $alerts[] = ['type' => 'danger', 'icon' => 'map-pin-off',
                'text' => "<strong>{$neverVisited} RW</strong> belum pernah dikunjungi ({$pct}% dari total)",
                'link' => route('bedah-dapil.sisir-rw'), 'link_text' => 'Lihat'];
        }

        // Desa Rebut Realistis tanpa KORWE
        $rebutTanpaKorwe = TargetWilayah::where('status_wilayah', 'REBUT REALISTIS')
            ->whereDoesntHave('korwes', fn($q) => $q->where('status', 'terbentuk'))->count();
        if ($rebutTanpaKorwe > 0) {
            $alerts[] = ['type' => 'info', 'icon' => 'target-arrow',
                'text' => "<strong>{$rebutTanpaKorwe} desa Rebut Realistis</strong> belum ada KORWE — prioritas tinggi",
                'link' => route('infra-rtrw.index', ['status' => 'REBUT REALISTIS']), 'link_text' => 'Lihat'];
        }

        return $alerts;
    }

    // === PROGRESS PER DAPIL ===

    public function getDapilProgressProperty()
    {
        $dapils = TargetWilayah::distinct()->orderBy('dapil')->pluck('dapil');
        $tahunField = min($this->selectedTahun, 2029);

        return $dapils->map(function ($dapil) use ($tahunField) {
            $targetKorwe = TargetWilayah::where('dapil', $dapil)->sum("target_korwe_{$tahunField}");
            $korweTerbentuk = Korwe::whereHas('targetWilayah', fn($q) => $q->where('dapil', $dapil))->where('status', 'terbentuk')->count();

            $totalRw = DataRw::where('dapil', $dapil)->count();
            $rwTersisir = KegiatanRw::where('dapil', $dapil)
                ->whereMonth('tanggal_kegiatan', $this->selectedBulan)
                ->whereYear('tanggal_kegiatan', $this->selectedTahun)
                ->selectRaw('DISTINCT CONCAT(target_wilayah_id, nomor_rw)')->get()->count();

            $profilTerisi = ProfilRw::where('dapil', $dapil)->where('completion_percent', '>', 0)->count();
            $upaRw = ProfilRw::where('dapil', $dapil)->where('upa_rw_status', 'sudah')->count();
            $rki = ProfilRw::where('dapil', $dapil)->where('rki_status', 'sudah')->count();
            $ksn = ProfilRw::where('dapil', $dapil)->where('senam_status', 'sudah')->count();

            return [
                'dapil' => $dapil,
                'korwe_pct' => $targetKorwe > 0 ? round($korweTerbentuk / $targetKorwe * 100) : 0,
                'sisir_pct' => $totalRw > 0 ? round($rwTersisir / $totalRw * 100) : 0,
                'profil_pct' => $totalRw > 0 ? round($profilTerisi / $totalRw * 100) : 0,
                'upa_rw' => $upaRw,
                'rki' => $rki,
                'ksn' => $ksn,
                'korwe_detail' => "{$korweTerbentuk}/{$targetKorwe}",
                'sisir_detail' => "{$rwTersisir}/{$totalRw}",
                'profil_detail' => "{$profilTerisi}/{$totalRw}",
            ];
        });
    }

    // === TIMELINE AKTIVITAS ===

    public function getTimelineProperty()
    {
        // Gabungkan aktivitas terbaru dari berbagai sumber
        $items = collect();

        // Kegiatan Sisir RW terbaru
        KegiatanRw::with('creator')->orderByDesc('created_at')->limit(5)->get()
            ->each(function ($k) use (&$items) {
                $items->push([
                    'time' => $k->created_at,
                    'color' => $k->jenis_config['color'] ?? '#d97706',
                    'title' => $k->jenis_config['label'] . ' — RW ' . $k->nomor_rw . ' ' . $k->desa,
                    'desc' => $k->jumlah_warga . ' warga · ' . $k->pelaksana,
                    'icon' => 'walk',
                ]);
            });

        // KORWE terbaru
        Korwe::where('status', 'terbentuk')->with('targetWilayah')->orderByDesc('updated_at')->limit(3)->get()
            ->each(function ($k) use (&$items) {
                $items->push([
                    'time' => $k->updated_at,
                    'color' => '#16a34a',
                    'title' => 'KORWE terbentuk — RW ' . $k->nomor_rw . ' ' . ($k->targetWilayah->desa ?? ''),
                    'desc' => $k->nama_koordinator,
                    'icon' => 'user-check',
                ]);
            });

        // Event approval terbaru
        Event::whereIn('status', ['disetujui', 'menunggu_approval'])->orderByDesc('updated_at')->limit(3)->get()
            ->each(function ($e) use (&$items) {
                $items->push([
                    'time' => $e->updated_at,
                    'color' => '#2563eb',
                    'title' => 'Event ' . $e->status_config['label'] . ' — ' . $e->judul,
                    'desc' => 'Level: ' . strtoupper($e->level_approval),
                    'icon' => 'calendar-event',
                ]);
            });

        // Member baru
        User::orderByDesc('created_at')->limit(3)->get()
            ->each(function ($u) use (&$items) {
                $items->push([
                    'time' => $u->created_at,
                    'color' => '#7c3aed',
                    'title' => 'Member baru terdaftar',
                    'desc' => $u->name . ' · ' . $u->email,
                    'icon' => 'user-plus',
                ]);
            });

        return $items->sortByDesc('time')->take(8)->values();
    }

    // === TREND BULANAN ===

    public function getTrendProperty()
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = KegiatanRw::whereMonth('tanggal_kegiatan', $date->month)
                ->whereYear('tanggal_kegiatan', $date->year)->count();
            $months->push([
                'label' => $date->translatedFormat('M'),
                'count' => $count,
                'current' => $i === 0,
            ]);
        }
        $max = max($months->max('count'), 1);
        return $months->map(fn($m) => array_merge($m, ['pct' => round($m['count'] / $max * 100)]));
    }

    // === STATUS WILAYAH ===

    public function getStatusWilayahProperty()
    {
        return TargetWilayah::when($this->selectedDapil, fn($q, $v) => $q->where('dapil', $v))
            ->selectRaw('status_wilayah, count(*) as total')
            ->groupBy('status_wilayah')
            ->pluck('total', 'status_wilayah');
    }

    // === LEADERBOARD ===

    public function getLeaderboardProperty()
    {
        return $this->dapilProgress->sortByDesc(function ($d) {
            // Skor gabungan: KORWE 40% + Sisir 30% + Profil 20% + UPA/RKI 10%
            return ($d['korwe_pct'] * 0.4) + ($d['sisir_pct'] * 0.3) + ($d['profil_pct'] * 0.2) + (($d['upa_rw'] + $d['rki']) * 0.1);
        })->values();
    }

    public function getDapilOptionsProperty()
    {
        return TargetWilayah::distinct()->orderBy('dapil')->pluck('dapil');
    }

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('components.layouts.app.sidebar');
    }
}
```

== 4. VIEW: resources/views/livewire/dashboard.blade.php ==

HAPUS file resources/views/dashboard.blade.php yang lama. Buat Livewire view baru.

Layout: SIDEBAR ADMIN (components.layouts.app.sidebar) — BUKAN layout public.
Halaman ini hanya bisa diakses setelah login admin.
Route: /dashboard (bukan / yang merupakan homepage public).

Design: accent #fe5000, card style konsisten dengan modul admin lainnya.

=== STRUKTUR HALAMAN ===

HEADER:
```html
<div class="flex items-center justify-between mb-4">
    <div>
        <h1 class="text-xl font-medium">Dashboard Kontrol</h1>
        <p class="text-sm text-zinc-500 mt-0.5">Ringkasan pencapaian semua modul — Kabupaten Bekasi</p>
    </div>
    <div class="flex gap-2">
        <select wire:model.live="selectedDapil" class="h-9 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3">
            <option value="">Semua dapil</option>
            @foreach($this->dapilOptions as $d)
                <option value="{{ $d }}">{{ $d }}</option>
            @endforeach
        </select>
        <select wire:model.live="selectedBulan" class="h-9 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3">
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}">{{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
            @endfor
        </select>
    </div>
</div>
```

ROW 1 — KPI Cards Utama (grid 5 kolom):
- KORWE terbentuk: {korweTerbentuk} / {targetKorwe} + progress bar + persen
- KORTE terbentuk: {korteTerbentuk} / {targetKorte} + progress bar
- RW tersisir bulan ini: GRADIENT CARD — {rwTersisir} / {totalRw} + kegiatan + warga
- Profil RW: {profilTerisi} / {totalRw} + {profilLengkap} lengkap
- Member website: {totalMember} + {memberBulanIni} baru bulan ini

ROW 2 — KPI Cards Infrastruktur (grid 4 kolom, ukuran lebih kecil):
- UPA RW terbentuk: {upaRw} RW
- RKI aktif: {rki} RW
- KSN / Senam PKS: {ksn} titik (label: "KSN / Senam PKS", data dari senam_status di profil_rws)
- Relawan Milenial: {relawan} RW

Styling row 2 lebih compact, angka lebih kecil (text-lg bukan text-2xl), background secondary.

ROW 3 — Alerts (full width card, conditional):
```html
@if(count($this->alerts) > 0)
<div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-3 mb-3">
    <div class="text-xs text-amber-600 uppercase tracking-wider font-medium mb-2">⚡ Perlu perhatian</div>
    @foreach($this->alerts as $alert)
        @php
            $colors = ['warning' => ['bg' => '#fff7f1', 'border' => '#fed7aa', 'text' => '#854d0e', 'icon' => '#d97706'],
                        'danger' => ['bg' => '#fee2e2', 'border' => '#fecaca', 'text' => '#991b1b', 'icon' => '#dc2626'],
                        'info' => ['bg' => '#dbeafe', 'border' => '#93c5fd', 'text' => '#1e3a5f', 'icon' => '#2563eb']];
            $c = $colors[$alert['type']] ?? $colors['info'];
        @endphp
        <div class="flex items-center gap-2 p-2 rounded-lg mb-1.5" style="background:{{ $c['bg'] }};border:0.5px solid {{ $c['border'] }};">
            <i class="ti ti-{{ $alert['icon'] }}" style="font-size:14px;color:{{ $c['icon'] }};"></i>
            <span class="flex-1 text-xs" style="color:{{ $c['text'] }};">{!! $alert['text'] !!}</span>
            <a href="{{ $alert['link'] }}" class="text-[10px] text-orange-600 font-medium" wire:navigate>{{ $alert['link_text'] }} →</a>
        </div>
    @endforeach
</div>
@endif
```

ROW 4 — Layout 2 kolom (2fr 1fr):

KOLOM KIRI: Progress per Dapil
Card: header "PROGRESS PER DAPIL" + title "Capaian gabungan — {bulan} {tahun}"
Tabel per dapil:
Kolom: Dapil | KORWE | Sisir RW | Profil | UPA | RKI | KSN
Setiap cell: mini progress bar + persentase atau angka
Highlight dapil terbaik (row pertama di leaderboard).

```html
<table class="w-full text-xs">
    <thead>
        <tr class="border-b border-zinc-200 dark:border-zinc-700">
            <th class="text-left py-2 px-2 text-[10px] text-zinc-500 uppercase">Dapil</th>
            <th class="text-center py-2 px-2 text-[10px] text-zinc-500 uppercase">KORWE</th>
            <th class="text-center py-2 px-2 text-[10px] text-zinc-500 uppercase">Sisir</th>
            <th class="text-center py-2 px-2 text-[10px] text-zinc-500 uppercase">Profil</th>
            <th class="text-center py-2 px-2 text-[10px] text-zinc-500 uppercase">UPA</th>
            <th class="text-center py-2 px-2 text-[10px] text-zinc-500 uppercase">RKI</th>
            <th class="text-center py-2 px-2 text-[10px] text-zinc-500 uppercase">KSN</th>
        </tr>
    </thead>
    <tbody>
        @foreach($this->dapilProgress as $dp)
            <tr class="border-b border-zinc-100 dark:border-zinc-700/50 hover:bg-zinc-50 dark:hover:bg-zinc-700/20">
                <td class="py-2 px-2 font-medium">{{ $dp['dapil'] }}</td>
                <td class="py-2 px-2 text-center">
                    <div class="flex items-center gap-1 justify-center">
                        <div class="w-10 h-1.5 bg-zinc-100 dark:bg-zinc-700 rounded-full overflow-hidden">
                            <div class="h-full bg-orange-500 rounded-full" style="width:{{ $dp['korwe_pct'] }}%"></div>
                        </div>
                        <span class="text-[10px] {{ $dp['korwe_pct'] > 0 ? 'text-orange-600 font-medium' : 'text-zinc-400' }}">{{ $dp['korwe_pct'] }}%</span>
                    </div>
                </td>
                {{-- Sisir, Profil sama pattern --}}
                <td class="py-2 px-2 text-center">
                    <span class="text-[10px] {{ $dp['sisir_pct'] > 0 ? 'text-amber-600 font-medium' : 'text-zinc-400' }}">{{ $dp['sisir_pct'] }}%</span>
                </td>
                <td class="py-2 px-2 text-center">
                    <span class="text-[10px] {{ $dp['profil_pct'] > 0 ? 'text-blue-600 font-medium' : 'text-zinc-400' }}">{{ $dp['profil_pct'] }}%</span>
                </td>
                <td class="py-2 px-2 text-center text-[10px]">{{ $dp['upa_rw'] }}</td>
                <td class="py-2 px-2 text-center text-[10px]">{{ $dp['rki'] }}</td>
                <td class="py-2 px-2 text-center text-[10px]">{{ $dp['ksn'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
```

KOLOM KANAN: Activity Feed + Event Pipeline
- Timeline aktivitas (8 item terbaru, mixed source)
- Pipeline event (menunggu / disetujui / selesai) — 3 stat cards inline
- Tombol "Lihat semua aktivitas →"

ROW 5 — Layout 2 kolom:

KIRI: Status Wilayah
5 card berwarna (Jaga Kuat / Amankan / Rebut / Garap / Zona Berat) dengan jumlah desa.

KANAN: Trend Bulanan
Bar chart kegiatan Sisir RW per bulan (6 bulan terakhir). Bar bulan ini di-highlight. Persentase perubahan vs bulan lalu.

ROW 6 — Leaderboard Dapil (full width):
Ranking pencapaian gabungan per dapil.
Skor = KORWE (40%) + Sisir RW (30%) + Profil (20%) + Infra/UPA/RKI (10%)
Kolom: Rank | Dapil | Skor Bar | KORWE | Sisir | Profil | UPA | RKI | KSN | Skor Total
Dapil #1: highlight orange, emoji 🥇. #2: 🥈. #3: 🥉.

ROW 7 — Quick Access Cards (grid 5):
Link cepat ke modul: Bedah Dapil, Infra RT/RW, Sisir RW, Event, Website
Setiap card: icon + label + 1 angka kunci + link

PENTING:
- Semua data dari database via computed properties
- Filter dapil + bulan reactive (wire:model.live)
- Angka pakai number_format()
- Progress bar: height 4-6px, rounded-full, warna sesuai modul (orange=KORWE, amber=Sisir, blue=Profil, green=UPA/RKI)
- Dark mode support
- Card style konsisten dengan modul lain

Langsung buat semua. Jangan test.
```

---

## PROMPT FIX:

```
Cek dan fix dashboard /dashboard. Langsung fix, JANGAN test.

CHECKLIST:
1. Halaman /dashboard load tanpa error
2. KPI cards row 1 (5 card): angka muncul dari database
3. KPI cards row 2 (5 card infrastruktur): UPA, RKI, KSN, Senam, Relawan — angka dari profil_rws
4. Alerts muncul jika ada kondisi yang perlu perhatian
5. Tabel progress per dapil: 7 dapil, semua kolom terisi
6. Timeline aktivitas: mixed source (Sisir RW, KORWE, Event, Member)
7. Event pipeline: 3 angka (menunggu/disetujui/selesai)
8. Status wilayah: 5 card berwarna dengan jumlah desa
9. Trend bulanan: bar chart 6 bulan
10. Leaderboard: 7 dapil urut skor, top 3 highlight
11. Quick access: 5 card link ke modul
12. Filter dapil reactive — semua section update
13. Filter bulan reactive — sisir RW dan trend update
14. Member website: count dari users table
15. Dark mode: semua elemen support

JIKA ada query yang lambat: cache dengan computed property (Livewire auto-cache per render).
JIKA ada N+1: gunakan eager loading.
CATATAN: KSN = Senam PKS. Data dari kolom senam_status di profil_rws. Tidak ada kolom ksn_status terpisah.

Langsung fix. Jangan test.
```
