# Prompt Bertahap Trae — Modul Sisir RW

---

## PROMPT 1: Migration, Model, Route

```
Buat modul "Sisir RW" — tracker kegiatan lapangan per RW. Langsung buat, JANGAN test, JANGAN tanya.

KONTEKS: Laravel 12, Livewire, UUID (HasUuids). Modul ini database-driven, pakai Livewire components.

== 1. MIGRATION: create_kegiatan_rws_table ==

```php
Schema::create('kegiatan_rws', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('target_wilayah_id');
    $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->onDelete('cascade');
    $table->string('dapil');
    $table->string('kecamatan');
    $table->string('desa');
    $table->string('nomor_rw');
    // Kegiatan
    $table->string('jenis_kegiatan');       // silaturahmi, door_to_door, baksos, pengajian, senam, diskusi, bedah_rumah, pendidikan, kesehatan, rekrutmen, konsolidasi, lainnya
    $table->dateTime('tanggal_kegiatan');
    $table->string('pelaksana');             // nama PIC
    $table->integer('jumlah_warga')->default(0);  // warga hadir/terjangkau
    $table->text('catatan')->nullable();     // deskripsi hasil kegiatan
    $table->json('foto')->nullable();        // array path foto ["foto1.jpg", "foto2.jpg"]
    // Tokoh yang ditemui
    $table->text('tokoh_ditemui')->nullable(); // nama tokoh + catatan
    // Follow-up
    $table->text('tindak_lanjut')->nullable();
    $table->date('jadwal_berikutnya')->nullable();
    // Meta
    $table->uuid('created_by')->nullable();
    $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
    $table->timestamps();

    $table->index(['target_wilayah_id', 'nomor_rw']);
    $table->index(['dapil', 'kecamatan', 'desa']);
    $table->index(['jenis_kegiatan']);
    $table->index(['tanggal_kegiatan']);
    $table->index(['created_at']);
});
```

== 2. MODEL: KegiatanRw ==

File: app/Models/KegiatanRw.php

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanRw extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'target_wilayah_id', 'dapil', 'kecamatan', 'desa', 'nomor_rw',
        'jenis_kegiatan', 'tanggal_kegiatan', 'pelaksana', 'jumlah_warga',
        'catatan', 'foto', 'tokoh_ditemui', 'tindak_lanjut', 'jadwal_berikutnya',
        'created_by',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'datetime',
        'jadwal_berikutnya' => 'date',
        'foto' => 'array',
        'jumlah_warga' => 'integer',
    ];

    const JENIS_KEGIATAN = [
        'silaturahmi' => ['label' => 'Silaturahmi tokoh', 'icon' => 'heart-handshake', 'color' => '#16a34a'],
        'door_to_door' => ['label' => 'Door-to-door', 'icon' => 'door', 'color' => '#2563eb'],
        'baksos' => ['label' => 'Bakti sosial', 'icon' => 'heart', 'color' => '#2563eb'],
        'pengajian' => ['label' => 'Pengajian / kajian', 'icon' => 'book', 'color' => '#d97706'],
        'senam' => ['label' => 'Senam PKS', 'icon' => 'stretching', 'color' => '#ec4899'],
        'diskusi' => ['label' => 'Diskusi warga', 'icon' => 'messages', 'color' => '#8b5cf6'],
        'bedah_rumah' => ['label' => 'Bedah rumah', 'icon' => 'home-cog', 'color' => '#0891b2'],
        'pendidikan' => ['label' => 'Bantuan pendidikan', 'icon' => 'school', 'color' => '#0d9488'],
        'kesehatan' => ['label' => 'Layanan kesehatan', 'icon' => 'stethoscope', 'color' => '#dc2626'],
        'rekrutmen' => ['label' => 'Rekrutmen kader', 'icon' => 'user-plus', 'color' => '#fe5000'],
        'konsolidasi' => ['label' => 'Konsolidasi internal', 'icon' => 'users-group', 'color' => '#64748b'],
        'lainnya' => ['label' => 'Lainnya', 'icon' => 'dots', 'color' => '#888'],
    ];

    public function targetWilayah()
    {
        return $this->belongsTo(TargetWilayah::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getJenisConfigAttribute(): array
    {
        return self::JENIS_KEGIATAN[$this->jenis_kegiatan] ?? self::JENIS_KEGIATAN['lainnya'];
    }

    public function scopeByDapil($query, $dapil)
    {
        return $query->where('dapil', $dapil);
    }

    public function scopeByDesa($query, $kecamatan, $desa)
    {
        return $query->where('kecamatan', $kecamatan)->where('desa', $desa);
    }

    public function scopeByRw($query, $targetWilayahId, $nomorRw)
    {
        return $query->where('target_wilayah_id', $targetWilayahId)->where('nomor_rw', $nomorRw);
    }

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal_kegiatan', now()->month)
                     ->whereYear('tanggal_kegiatan', now()->year);
    }

    public function scopePeriode($query, $bulan, $tahun)
    {
        return $query->whereMonth('tanggal_kegiatan', $bulan)
                     ->whereYear('tanggal_kegiatan', $tahun);
    }
}
```

Tambahkan relationship di TargetWilayah:
```php
public function kegiatanRws()
{
    return $this->hasMany(KegiatanRw::class);
}
```

== 3. ROUTE ==

Di routes/web.php, dalam middleware auth group:

Route::get('/bedah-dapil/sisir-rw', App\Livewire\BedahDapil\SisirRw::class)->name('bedah-dapil.sisir-rw');

== 4. UPDATE SIDEBAR ==

Di sidebar.blade.php, pastikan menu "Sisir RW" di sub-menu Bedah Dapil mengarah ke route yang benar. Hapus badge "Segera" jika masih ada untuk Sisir RW.

Jalankan: php artisan migrate
Jangan buat test.
```

---

## PROMPT 2: Livewire Component + View Dashboard Sisir RW

```
Buat Livewire component dan view untuk halaman Sisir RW. Langsung buat, JANGAN test.

== File: app/Livewire/BedahDapil/SisirRw.php ==

```php
<?php
namespace App\Livewire\BedahDapil;

use App\Models\KegiatanRw;
use App\Models\TargetWilayah;
use App\Models\DataRw;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class SisirRw extends Component
{
    use WithPagination, WithFileUploads;

    // Filters
    public $selectedDapil = '';
    public $selectedKecamatan = '';
    public $selectedBulan;
    public $selectedTahun;

    // Form catat kegiatan
    public $showForm = false;
    public $formDesaId = '';        // target_wilayah_id
    public $formRw = '';
    public $formJenis = 'silaturahmi';
    public $formTanggal;
    public $formPelaksana = '';
    public $formJumlahWarga = 0;
    public $formCatatan = '';
    public $formTokoh = '';
    public $formTindakLanjut = '';
    public $formJadwalBerikutnya = '';
    public $formFoto = [];
    public $editId = null;

    protected $queryString = ['selectedDapil', 'selectedKecamatan'];

    public function mount()
    {
        $this->selectedBulan = now()->month;
        $this->selectedTahun = now()->year;
        $this->formTanggal = now()->format('Y-m-d\TH:i');

        // Default dapil pertama yang punya data
        $firstDapil = TargetWilayah::distinct()->orderBy('dapil')->value('dapil');
        if ($firstDapil) $this->selectedDapil = $firstDapil;
    }

    // === COMPUTED PROPERTIES ===

    public function getDapilOptionsProperty()
    {
        return TargetWilayah::distinct()->orderBy('dapil')->pluck('dapil');
    }

    public function getKecamatanOptionsProperty()
    {
        return TargetWilayah::when($this->selectedDapil, fn($q, $v) => $q->where('dapil', $v))
            ->distinct()->orderBy('kecamatan')->pluck('kecamatan');
    }

    public function getSummaryProperty()
    {
        $baseKegiatan = KegiatanRw::query()
            ->when($this->selectedDapil, fn($q, $v) => $q->where('dapil', $v))
            ->when($this->selectedKecamatan, fn($q, $v) => $q->where('kecamatan', $v))
            ->periode($this->selectedBulan, $this->selectedTahun);

        $totalRw = DataRw::query()
            ->when($this->selectedDapil, fn($q, $v) => $q->where('dapil', $v))
            ->when($this->selectedKecamatan, fn($q, $v) => $q->where('kecamatan', $v))
            ->count();

        $rwTersisir = $baseKegiatan->clone()
            ->selectRaw('DISTINCT CONCAT(target_wilayah_id, nomor_rw) as rw_key')
            ->get()->count();

        return [
            'total_rw' => $totalRw,
            'rw_tersisir' => $rwTersisir,
            'pct_tersisir' => $totalRw > 0 ? round($rwTersisir / $totalRw * 100) : 0,
            'kegiatan_bulan_ini' => $baseKegiatan->clone()->count(),
            'warga_terjangkau' => $baseKegiatan->clone()->sum('jumlah_warga'),
            'rw_belum' => $totalRw - $rwTersisir,
        ];
    }

    public function getHeatmapDataProperty()
    {
        // Per desa, ambil semua RW + hitung kegiatan bulan ini
        $desaList = TargetWilayah::query()
            ->when($this->selectedDapil, fn($q, $v) => $q->where('dapil', $v))
            ->when($this->selectedKecamatan, fn($q, $v) => $q->where('kecamatan', $v))
            ->orderBy('kecamatan')->orderBy('desa')
            ->get();

        $result = [];
        foreach ($desaList as $tw) {
            $rwList = DataRw::where('target_wilayah_id', $tw->id)
                ->orderBy('nomor_rw')->get();

            if ($rwList->isEmpty()) continue;

            // Hitung kegiatan per RW bulan ini
            $kegiatanCounts = KegiatanRw::where('target_wilayah_id', $tw->id)
                ->periode($this->selectedBulan, $this->selectedTahun)
                ->selectRaw('nomor_rw, count(*) as total')
                ->groupBy('nomor_rw')
                ->pluck('total', 'nomor_rw');

            $rwData = $rwList->map(fn($rw) => [
                'nomor_rw' => $rw->nomor_rw,
                'dpt' => $rw->dpt,
                'estimasi_pks' => $rw->estimasi_pks,
                'status' => $rw->status_wilayah,
                'kegiatan_count' => $kegiatanCounts[$rw->nomor_rw] ?? 0,
            ]);

            $result[] = [
                'target_wilayah_id' => $tw->id,
                'kecamatan' => $tw->kecamatan,
                'desa' => $tw->desa,
                'rw_list' => $rwData,
            ];
        }

        return $result;
    }

    public function getTimelineProperty()
    {
        return KegiatanRw::query()
            ->when($this->selectedDapil, fn($q, $v) => $q->where('dapil', $v))
            ->when($this->selectedKecamatan, fn($q, $v) => $q->where('kecamatan', $v))
            ->with('creator')
            ->orderByDesc('tanggal_kegiatan')
            ->limit(10)
            ->get();
    }

    public function getRwBelumTersisirProperty()
    {
        // RW yang belum ada kegiatan bulan ini, urut by prioritas
        $rwDenganKegiatan = KegiatanRw::query()
            ->when($this->selectedDapil, fn($q, $v) => $q->where('dapil', $v))
            ->when($this->selectedKecamatan, fn($q, $v) => $q->where('kecamatan', $v))
            ->periode($this->selectedBulan, $this->selectedTahun)
            ->selectRaw('DISTINCT CONCAT(target_wilayah_id, ":", nomor_rw) as rw_key')
            ->pluck('rw_key');

        return DataRw::query()
            ->when($this->selectedDapil, fn($q, $v) => $q->where('dapil', $v))
            ->when($this->selectedKecamatan, fn($q, $v) => $q->where('kecamatan', $v))
            ->orderBy('prioritas_urutan')
            ->orderByDesc('estimasi_pks')
            ->get()
            ->filter(function($rw) use ($rwDenganKegiatan) {
                $key = $rw->target_wilayah_id . ':' . $rw->nomor_rw;
                return !$rwDenganKegiatan->contains($key);
            })
            ->take(20);
    }

    public function getDesaOptionsProperty()
    {
        return TargetWilayah::query()
            ->when($this->selectedDapil, fn($q, $v) => $q->where('dapil', $v))
            ->orderBy('kecamatan')->orderBy('desa')
            ->get()
            ->map(fn($tw) => ['id' => $tw->id, 'label' => $tw->desa . ' — ' . $tw->kecamatan]);
    }

    public function getRwOptionsProperty()
    {
        if (!$this->formDesaId) return collect();
        return DataRw::where('target_wilayah_id', $this->formDesaId)
            ->orderBy('nomor_rw')
            ->pluck('nomor_rw');
    }

    // === ACTIONS ===

    public function openForm($targetWilayahId = null, $nomorRw = null)
    {
        $this->resetForm();
        if ($targetWilayahId) $this->formDesaId = $targetWilayahId;
        if ($nomorRw) $this->formRw = $nomorRw;
        $this->showForm = true;
    }

    public function openFormForRw($targetWilayahId, $nomorRw)
    {
        $this->openForm($targetWilayahId, $nomorRw);
    }

    public function simpanKegiatan()
    {
        $this->validate([
            'formDesaId' => 'required',
            'formRw' => 'required',
            'formJenis' => 'required',
            'formTanggal' => 'required',
            'formPelaksana' => 'required',
        ]);

        $tw = TargetWilayah::findOrFail($this->formDesaId);

        // Handle foto upload
        $fotoPaths = [];
        if (!empty($this->formFoto)) {
            foreach ($this->formFoto as $foto) {
                $fotoPaths[] = $foto->store('kegiatan-rw', 'public');
            }
        }

        $data = [
            'target_wilayah_id' => $tw->id,
            'dapil' => $tw->dapil,
            'kecamatan' => $tw->kecamatan,
            'desa' => $tw->desa,
            'nomor_rw' => $this->formRw,
            'jenis_kegiatan' => $this->formJenis,
            'tanggal_kegiatan' => $this->formTanggal,
            'pelaksana' => $this->formPelaksana,
            'jumlah_warga' => $this->formJumlahWarga,
            'catatan' => $this->formCatatan,
            'tokoh_ditemui' => $this->formTokoh,
            'tindak_lanjut' => $this->formTindakLanjut,
            'jadwal_berikutnya' => $this->formJadwalBerikutnya ?: null,
            'foto' => !empty($fotoPaths) ? $fotoPaths : null,
            'created_by' => auth()->id(),
        ];

        if ($this->editId) {
            KegiatanRw::findOrFail($this->editId)->update($data);
            session()->flash('message', 'Kegiatan berhasil diupdate.');
        } else {
            KegiatanRw::create($data);
            session()->flash('message', 'Kegiatan berhasil dicatat.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function editKegiatan($id)
    {
        $kegiatan = KegiatanRw::findOrFail($id);
        $this->editId = $id;
        $this->formDesaId = $kegiatan->target_wilayah_id;
        $this->formRw = $kegiatan->nomor_rw;
        $this->formJenis = $kegiatan->jenis_kegiatan;
        $this->formTanggal = $kegiatan->tanggal_kegiatan->format('Y-m-d\TH:i');
        $this->formPelaksana = $kegiatan->pelaksana;
        $this->formJumlahWarga = $kegiatan->jumlah_warga;
        $this->formCatatan = $kegiatan->catatan;
        $this->formTokoh = $kegiatan->tokoh_ditemui;
        $this->formTindakLanjut = $kegiatan->tindak_lanjut;
        $this->formJadwalBerikutnya = $kegiatan->jadwal_berikutnya?->format('Y-m-d');
        $this->showForm = true;
    }

    public function hapusKegiatan($id)
    {
        KegiatanRw::findOrFail($id)->delete();
        session()->flash('message', 'Kegiatan dihapus.');
    }

    public function resetForm()
    {
        $this->editId = null;
        $this->formDesaId = '';
        $this->formRw = '';
        $this->formJenis = 'silaturahmi';
        $this->formTanggal = now()->format('Y-m-d\TH:i');
        $this->formPelaksana = '';
        $this->formJumlahWarga = 0;
        $this->formCatatan = '';
        $this->formTokoh = '';
        $this->formTindakLanjut = '';
        $this->formJadwalBerikutnya = '';
        $this->formFoto = [];
    }

    public function updatingSelectedDapil()
    {
        $this->selectedKecamatan = '';
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.bedah-dapil.sisir-rw')
            ->layout('components.layouts.app.sidebar');
    }
}
```

== File: resources/views/livewire/bedah-dapil/sisir-rw.blade.php ==

Layout: sidebar layout. Design: accent #fe5000, sama dengan modul lain.

=== STRUKTUR HALAMAN ===

SECTION 1: Header
```html
<div class="mb-4">
    <h1 class="text-xl font-medium">Sisir RW</h1>
    <p class="text-sm text-zinc-500 mt-0.5">Tracker kegiatan lapangan per RW — catat setiap kunjungan dan aktivitas di wilayah</p>
</div>
```

SECTION 2: Filter bar (horizontal)
- Dropdown Dapil (wire:model.live)
- Dropdown Kecamatan (wire:model.live)
- Dropdown Periode: bulan + tahun (wire:model.live selectedBulan, selectedTahun)
- Tombol "+ Catat kegiatan" (wire:click="openForm" — bg orange, text white)

SECTION 3: Summary cards — grid 5 kolom
- Total RW ($summary['total_rw'])
- RW Tersisir — GRADIENT CARD ($summary['rw_tersisir'], sub: "{pct}% dari total")
- Kegiatan bulan ini ($summary['kegiatan_bulan_ini'])
- Warga terjangkau ($summary['warga_terjangkau'])
- RW belum tersisir ($summary['rw_belum'] — text merah)

SECTION 4: 2 kolom (1fr 1fr)

KOLOM KIRI: Heatmap Kunjungan
- Card: section label "HEATMAP KUNJUNGAN", title "Frekuensi sisir per RW — {bulan} {tahun}"
- Legend: 0x (merah) / 1x (kuning) / 2x (hijau muda) / 3x+ (hijau tua)
- Per desa: label "{Desa} ({n} RW)" lalu grid kotak RW

```html
@foreach($this->heatmapData as $desa)
    <div class="mb-3">
        <div class="text-xs text-zinc-600 dark:text-zinc-400 font-medium mb-1">
            {{ $desa['desa'] }} <span class="text-zinc-400">({{ $desa['rw_list']->count() }} RW)</span>
        </div>
        <div class="flex flex-wrap gap-1">
            @foreach($desa['rw_list'] as $rw)
                @php
                    $count = $rw['kegiatan_count'];
                    $bgColor = match(true) {
                        $count >= 3 => 'background:#16a34a;color:white;',
                        $count == 2 => 'background:#bbf7d0;color:#14532d;',
                        $count == 1 => 'background:#fed7aa;color:#854d0e;',
                        default => 'background:#fee2e2;color:#991b1b;',
                    };
                @endphp
                <div wire:click="openFormForRw('{{ $desa['target_wilayah_id'] }}', '{{ $rw['nomor_rw'] }}')"
                     class="w-7 h-7 rounded flex items-center justify-center text-[9px] cursor-pointer hover:opacity-80"
                     style="{{ $bgColor }}"
                     title="RW {{ $rw['nomor_rw'] }} · {{ $count }}x kegiatan · DPT {{ $rw['dpt'] }} · Est.PKS ~{{ $rw['estimasi_pks'] }}">
                    {{ ltrim($rw['nomor_rw'], '0') ?: '0' }}
                </div>
            @endforeach
        </div>
    </div>
@endforeach
```

KOLOM KANAN: Timeline Kegiatan Terbaru
- Card: section label "KEGIATAN TERBARU", title "Log aktivitas lapangan"
- Link "Lihat semua →" (nanti)
- Timeline vertical: garis kiri, dot warna per jenis kegiatan

```html
@forelse($this->timeline as $kegiatan)
    @php $cfg = $kegiatan->jenis_config; @endphp
    <div class="relative pl-6 pb-4">
        {{-- Dot --}}
        <div class="absolute left-0 top-1 w-2.5 h-2.5 rounded-full" style="background:{{ $cfg['color'] }};"></div>
        {{-- Line --}}
        @if(!$loop->last)
            <div class="absolute left-[4px] top-3 bottom-0 w-px bg-zinc-200 dark:bg-zinc-700"></div>
        @endif
        {{-- Content --}}
        <div class="text-[10px] text-zinc-400">{{ $kegiatan->tanggal_kegiatan->diffForHumans() }}</div>
        <div class="text-xs font-medium mt-0.5">{{ $cfg['label'] }} — RW {{ $kegiatan->nomor_rw }} {{ $kegiatan->desa }}</div>
        <div class="text-xs text-zinc-500 mt-0.5 line-clamp-2">{{ $kegiatan->catatan }}</div>
        <div class="flex gap-3 mt-1 text-[10px] text-zinc-400">
            <span class="flex items-center gap-1"><i class="ti ti-user" style="font-size:11px;" aria-hidden="true"></i> {{ $kegiatan->pelaksana }}</span>
            <span class="flex items-center gap-1"><i class="ti ti-users" style="font-size:11px;" aria-hidden="true"></i> {{ $kegiatan->jumlah_warga }} warga</span>
            @if($kegiatan->foto)
                <span class="flex items-center gap-1"><i class="ti ti-photo" style="font-size:11px;" aria-hidden="true"></i> {{ count($kegiatan->foto) }} foto</span>
            @endif
        </div>
    </div>
@empty
    <div class="text-center py-6 text-sm text-zinc-400">Belum ada kegiatan tercatat</div>
@endforelse
```

SECTION 5: RW Belum Tersisir (full width card)
- Section label merah: "RW BELUM TERSISIR BULAN INI"
- Title: "Prioritaskan RW berikut — urut by prioritas wilayah"
- Badge: "{n} RW belum dikunjungi"

Per RW row:
```html
@foreach($this->rwBelumTersisir as $rw)
    @php $cfg = $rw->status_config; @endphp
    <div class="flex items-center gap-3 p-2 rounded-lg border border-zinc-200 dark:border-zinc-700 mb-1.5 hover:bg-zinc-50 dark:hover:bg-zinc-700/30
        {{ $rw->prioritas_urutan <= 2 ? 'border-blue-200 dark:border-blue-800 bg-blue-50/30 dark:bg-blue-950/10' : '' }}">
        <div class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:{{ $cfg['warna'] }};"></div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-medium">RW {{ $rw->nomor_rw }}</span>
                <span class="text-[10px] text-zinc-400">{{ $rw->desa }}, {{ $rw->kecamatan }}</span>
                <span class="text-[10px] px-1.5 py-0.5 rounded-full font-medium" style="background:{{ $cfg['bg'] }};color:{{ $cfg['text'] }};">{{ $cfg['label'] }}</span>
                @if($rw->prioritas_urutan <= 2)
                    <span class="text-[9px] px-1 py-0.5 rounded bg-orange-100 dark:bg-orange-900/30 text-orange-700 font-medium">PRIORITAS</span>
                @endif
            </div>
            <div class="text-[10px] text-zinc-400 mt-0.5">
                DPT {{ number_format($rw->dpt) }} · Est. PKS ~{{ number_format($rw->estimasi_pks) }} ·
                @php
                    $lastVisit = \App\Models\KegiatanRw::where('target_wilayah_id', $rw->target_wilayah_id)
                        ->where('nomor_rw', $rw->nomor_rw)
                        ->orderByDesc('tanggal_kegiatan')
                        ->value('tanggal_kegiatan');
                @endphp
                Terakhir: <strong class="{{ $lastVisit ? 'text-zinc-600' : 'text-red-600' }}">
                    {{ $lastVisit ? \Carbon\Carbon::parse($lastVisit)->diffForHumans() : 'belum pernah' }}
                </strong>
            </div>
        </div>
        <button wire:click="openFormForRw('{{ $rw->target_wilayah_id }}', '{{ $rw->nomor_rw }}')"
            class="px-3 py-1 rounded-md text-[10px] font-medium flex-shrink-0
            {{ $rw->prioritas_urutan <= 2
                ? 'bg-orange-600 text-white hover:bg-orange-700'
                : 'border border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700' }}">
            Catat
        </button>
    </div>
@endforeach
```

SECTION 6: Modal/Drawer Form Catat Kegiatan
Muncul saat $showForm = true.

```html
@if($showForm)
    <div class="fixed inset-0 bg-black/30 z-40" wire:click="$set('showForm', false)"></div>
    <div class="fixed top-0 right-0 w-[480px] h-full bg-white dark:bg-zinc-800 shadow-xl z-50 overflow-y-auto">
        {{-- Header --}}
        <div class="sticky top-0 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 p-4 z-10">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium">{{ $editId ? 'Edit' : 'Catat' }} kegiatan</div>
                    <div class="text-xs text-zinc-500 mt-0.5">Rekam aktivitas sisir RW</div>
                </div>
                <button wire:click="$set('showForm', false)" class="w-7 h-7 rounded-md border border-zinc-200 dark:border-zinc-600 flex items-center justify-center text-zinc-400">✕</button>
            </div>
        </div>

        <div class="p-4 space-y-3">
            {{-- Tanggal --}}
            <div>
                <label class="text-xs text-zinc-500 mb-1 block">Tanggal & waktu</label>
                <input wire:model="formTanggal" type="datetime-local" class="w-full h-9 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3">
            </div>

            {{-- Lokasi --}}
            <div class="grid grid-cols-3 gap-2">
                <div class="col-span-2">
                    <label class="text-xs text-zinc-500 mb-1 block">Desa</label>
                    <select wire:model.live="formDesaId" class="w-full h-9 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3">
                        <option value="">— Pilih desa —</option>
                        @foreach($this->desaOptions as $desa)
                            <option value="{{ $desa['id'] }}">{{ $desa['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-zinc-500 mb-1 block">RW</label>
                    <select wire:model="formRw" class="w-full h-9 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3">
                        <option value="">— RW —</option>
                        @foreach($this->rwOptions as $rw)
                            <option value="{{ $rw }}">RW {{ $rw }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Jenis kegiatan --}}
            <div>
                <label class="text-xs text-zinc-500 mb-1 block">Jenis kegiatan</label>
                <select wire:model="formJenis" class="w-full h-9 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3">
                    @foreach(\App\Models\KegiatanRw::JENIS_KEGIATAN as $key => $cfg)
                        <option value="{{ $key }}">{{ $cfg['label'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Pelaksana + Jumlah warga --}}
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-xs text-zinc-500 mb-1 block">Pelaksana / PIC</label>
                    <input wire:model="formPelaksana" class="w-full h-9 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3" placeholder="Nama pelaksana">
                </div>
                <div>
                    <label class="text-xs text-zinc-500 mb-1 block">Jumlah warga</label>
                    <input wire:model="formJumlahWarga" type="number" class="w-full h-9 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3" placeholder="0">
                </div>
            </div>

            {{-- Catatan --}}
            <div>
                <label class="text-xs text-zinc-500 mb-1 block">Catatan / hasil kegiatan</label>
                <textarea wire:model="formCatatan" rows="3" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm p-2" placeholder="Apa yang terjadi, siapa yang ditemui, hasil diskusi..."></textarea>
            </div>

            {{-- Tokoh --}}
            <div>
                <label class="text-xs text-zinc-500 mb-1 block">Tokoh yang ditemui</label>
                <input wire:model="formTokoh" class="w-full h-9 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3" placeholder="Nama tokoh + catatan singkat">
            </div>

            {{-- Tindak lanjut --}}
            <div>
                <label class="text-xs text-zinc-500 mb-1 block">Tindak lanjut</label>
                <textarea wire:model="formTindakLanjut" rows="2" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm p-2" placeholder="Apa yang harus dilakukan selanjutnya"></textarea>
            </div>

            {{-- Jadwal berikutnya --}}
            <div>
                <label class="text-xs text-zinc-500 mb-1 block">Jadwal kunjungan berikutnya (opsional)</label>
                <input wire:model="formJadwalBerikutnya" type="date" class="w-full h-9 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3">
            </div>

            {{-- Foto --}}
            <div>
                <label class="text-xs text-zinc-500 mb-1 block">Foto kegiatan (maks 5)</label>
                <input wire:model="formFoto" type="file" multiple accept="image/*" class="w-full text-sm text-zinc-500 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:bg-zinc-100 dark:file:bg-zinc-700 file:text-zinc-700 dark:file:text-zinc-300">
                @if(!empty($formFoto))
                    <div class="flex gap-2 mt-2">
                        @foreach($formFoto as $foto)
                            <div class="w-16 h-16 rounded-md bg-zinc-100 dark:bg-zinc-700 overflow-hidden">
                                <img src="{{ $foto->temporaryUrl() }}" class="w-full h-full object-cover" alt="Preview">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Footer --}}
        <div class="sticky bottom-0 bg-white dark:bg-zinc-800 border-t border-zinc-200 dark:border-zinc-700 p-4 flex gap-2">
            <button wire:click="simpanKegiatan" class="flex-1 py-2 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700">
                {{ $editId ? 'Update' : 'Simpan' }} kegiatan
            </button>
            <button wire:click="$set('showForm', false)" class="py-2 px-4 border border-zinc-300 dark:border-zinc-600 rounded-lg text-sm text-zinc-600 dark:text-zinc-300">
                Batal
            </button>
        </div>
    </div>
@endif
```

FLASH MESSAGE:
```html
@if(session('message'))
    <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-sm rounded-lg border border-green-200 dark:border-green-800">
        {{ session('message') }}
    </div>
@endif
```

Langsung buat semua. Jangan test.
```

---

## PROMPT 3: Fix & Polish

```
Cek dan fix modul Sisir RW. Langsung fix, JANGAN test.

CHECKLIST:

1. Halaman /bedah-dapil/sisir-rw load tanpa error
2. Filter dapil/kecamatan/bulan berfungsi (wire:model.live)
3. Summary cards menampilkan angka (awal semua 0, normal)
4. Heatmap muncul — kotak RW per desa, semua merah awalnya (belum ada kegiatan)
5. Timeline kosong tapi tidak error: "Belum ada kegiatan tercatat"
6. RW belum tersisir: list RW muncul urut by prioritas
7. Klik kotak RW di heatmap → form terbuka, desa+RW ter-prefill
8. Klik "Catat" di row RW → form terbuka, desa+RW ter-prefill
9. Klik "+ Catat kegiatan" → form terbuka kosong
10. Isi form → simpan → flash message muncul → heatmap update (kotak jadi kuning)
11. Timeline update setelah simpan kegiatan baru
12. Upload foto berfungsi (preview muncul, tersimpan di storage/app/public/kegiatan-rw/)
13. Pastikan storage:link sudah jalan (php artisan storage:link)
14. Summary cards update setelah simpan
15. Sidebar menu Sisir RW active saat di halaman ini

PERFORMA:
- Jika heatmap lambat (banyak desa), batch query per kecamatan
- Jika timeline N+1, eager load creator relationship
- Jika RW belum tersisir lambat, cache query

Langsung fix. Jangan test.
```
