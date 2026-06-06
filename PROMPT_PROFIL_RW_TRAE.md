# Prompt Trae — Profil RW (Checklist Bedah Dapil per RW)

```
Tambahkan fitur Profil RW di modul Infra RT/RW. Setiap RW punya form profil yang setengah auto-fill dari database, setengah input manual oleh kader lapangan. Progress pengisian profil di-tracking.

Langsung kerjakan, JANGAN test, JANGAN tanya.

== 1. MIGRATION: create_profil_rws_table ==

```php
Schema::create('profil_rws', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('target_wilayah_id');
    $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->onDelete('cascade');
    $table->string('nomor_rw');
    $table->string('dapil');
    $table->string('kecamatan');
    $table->string('desa');

    // Profil Wilayah
    $table->string('tipologi')->nullable();              // kampung, perumahan, campuran, perkotaan, pesisir, industri
    $table->string('ekonomi_dominan')->nullable();       // pertanian, pabrik, informal, pedagang, pns, nelayan, campuran
    $table->text('profil_warga')->nullable();            // deskripsi umum
    $table->integer('suara_pks_2019')->default(0);
    $table->text('faktor_penyebab')->nullable();         // analisa menang/kalah

    // Infrastruktur Partai
    $table->text('anggota_pks')->nullable();             // nama + jenjang
    $table->integer('jumlah_kta')->default(0);
    $table->string('upa_rw_status')->default('belum');   // sudah/belum
    $table->string('upa_rw_nama')->nullable();           // nama pembina
    $table->string('rki_status')->default('belum');      // sudah/belum
    $table->string('rki_nama')->nullable();              // nama penggerak
    $table->string('senam_status')->default('belum');    // sudah/belum
    $table->string('senam_nama')->nullable();            // nama instruktur
    $table->string('relawan_milenial_status')->default('belum'); // sudah/belum
    $table->string('relawan_milenial_nama')->nullable();
    $table->boolean('caleg_terpilih_ada')->default(false);
    $table->string('caleg_terpilih_nama')->nullable();

    // Peta Politik Lokal
    $table->text('afiliasi_rw_rt')->nullable();          // ketua RW/RT + afiliasi partai
    $table->text('afiliasi_posyandu_dkm')->nullable();   // kader posyandu + DKM
    $table->string('kompetitor_status')->default('tidak_tahu'); // ada/tidak/tidak_tahu
    $table->text('kompetitor_detail')->nullable();
    $table->string('tim_sukses_status')->default('tidak_tahu');
    $table->text('tim_sukses_detail')->nullable();

    // Strategi
    $table->text('strategi')->nullable();
    $table->string('penanggung_jawab')->nullable();      // nama + jenjang
    $table->text('keterangan_lain')->nullable();

    // Meta
    $table->boolean('is_complete')->default(false);      // tandai profil lengkap
    $table->integer('completion_percent')->default(0);   // 0-100
    $table->uuid('filled_by')->nullable();
    $table->foreign('filled_by')->references('id')->on('users')->nullOnDelete();
    $table->timestamp('filled_at')->nullable();
    $table->timestamps();

    $table->unique(['target_wilayah_id', 'nomor_rw']);
    $table->index(['dapil', 'kecamatan', 'desa']);
    $table->index(['is_complete']);
});
```

Jalankan: php artisan migrate

== 2. MODEL: ProfilRw ==

File: app/Models/ProfilRw.php

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilRw extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'target_wilayah_id', 'nomor_rw', 'dapil', 'kecamatan', 'desa',
        'tipologi', 'ekonomi_dominan', 'profil_warga', 'suara_pks_2019', 'faktor_penyebab',
        'anggota_pks', 'jumlah_kta',
        'upa_rw_status', 'upa_rw_nama', 'rki_status', 'rki_nama',
        'senam_status', 'senam_nama', 'relawan_milenial_status', 'relawan_milenial_nama',
        'caleg_terpilih_ada', 'caleg_terpilih_nama',
        'afiliasi_rw_rt', 'afiliasi_posyandu_dkm',
        'kompetitor_status', 'kompetitor_detail', 'tim_sukses_status', 'tim_sukses_detail',
        'strategi', 'penanggung_jawab', 'keterangan_lain',
        'is_complete', 'completion_percent', 'filled_by', 'filled_at',
    ];

    protected $casts = [
        'caleg_terpilih_ada' => 'boolean',
        'is_complete' => 'boolean',
        'filled_at' => 'datetime',
        'suara_pks_2019' => 'integer',
        'jumlah_kta' => 'integer',
        'completion_percent' => 'integer',
    ];

    const TIPOLOGI_OPTIONS = [
        'perkampungan' => 'Perkampungan',
        'campuran' => 'Campuran (Kampung + Perumahan)',
        'perumahan' => 'Perumahan',
        'perkotaan' => 'Perkotaan',
        'pesisir' => 'Pesisir / Tambak',
        'industri' => 'Industri',
    ];

    const EKONOMI_OPTIONS = [
        'pertanian' => 'Pertanian',
        'pabrik' => 'Pekerja Pabrik / Industri',
        'informal' => 'Pekerja Informal (Ojol/Freelance)',
        'pedagang' => 'Pedagang / Wiraswasta',
        'pns' => 'PNS / Karyawan',
        'nelayan' => 'Nelayan',
        'campuran' => 'Campuran',
    ];

    public function targetWilayah()
    {
        return $this->belongsTo(TargetWilayah::class);
    }

    public function filledByUser()
    {
        return $this->belongsTo(User::class, 'filled_by');
    }

    // Hitung completion percentage
    public function calculateCompletion(): int
    {
        $fields = [
            'tipologi', 'ekonomi_dominan', 'profil_warga', 'faktor_penyebab',
            'anggota_pks', 'jumlah_kta',
            'upa_rw_status', 'rki_status', 'senam_status', 'relawan_milenial_status',
            'afiliasi_rw_rt', 'afiliasi_posyandu_dkm',
            'kompetitor_status', 'tim_sukses_status',
            'strategi', 'penanggung_jawab',
        ];

        $filled = 0;
        foreach ($fields as $f) {
            $val = $this->$f;
            if ($val !== null && $val !== '' && $val !== 'belum' && $val !== 'tidak_tahu' && $val !== 0) {
                $filled++;
            }
        }

        return (int) round($filled / count($fields) * 100);
    }

    public function scopeComplete($query)
    {
        return $query->where('is_complete', true);
    }

    public function scopeByDapil($query, $dapil)
    {
        return $query->where('dapil', $dapil);
    }
}
```

Tambahkan relationship di TargetWilayah:
```php
public function profilRws()
{
    return $this->hasMany(ProfilRw::class);
}
```

== 3. UPDATE LIVEWIRE DETAIL — Tambah Profil Button + Drawer ==

Update file: app/Livewire/InfraRtRw/Detail.php

Tambahkan properties:
```php
public $showProfilDrawer = false;
public $profilRwId = null;       // nomor RW yang sedang diedit profilnya
public $profilData = [];         // form data

// Auto-fill data (read-only, dari database)
public $autoFillData = [];
```

Tambahkan methods:

```php
public function openProfil($nomorRw)
{
    $this->profilRwId = $nomorRw;
    $this->showProfilDrawer = true;

    // Load existing profil or create empty
    $profil = ProfilRw::where('target_wilayah_id', $this->targetWilayah->id)
        ->where('nomor_rw', $nomorRw)
        ->first();

    if ($profil) {
        $this->profilData = $profil->toArray();
    } else {
        $this->profilData = [
            'tipologi' => '', 'ekonomi_dominan' => '', 'profil_warga' => '',
            'suara_pks_2019' => 0, 'faktor_penyebab' => '',
            'anggota_pks' => '', 'jumlah_kta' => 0,
            'upa_rw_status' => 'belum', 'upa_rw_nama' => '',
            'rki_status' => 'belum', 'rki_nama' => '',
            'senam_status' => 'belum', 'senam_nama' => '',
            'relawan_milenial_status' => 'belum', 'relawan_milenial_nama' => '',
            'caleg_terpilih_ada' => false, 'caleg_terpilih_nama' => '',
            'afiliasi_rw_rt' => '', 'afiliasi_posyandu_dkm' => '',
            'kompetitor_status' => 'tidak_tahu', 'kompetitor_detail' => '',
            'tim_sukses_status' => 'tidak_tahu', 'tim_sukses_detail' => '',
            'strategi' => '', 'penanggung_jawab' => '', 'keterangan_lain' => '',
        ];
    }

    // Auto-fill data dari database
    $this->loadAutoFillData($nomorRw);
}

protected function loadAutoFillData($nomorRw)
{
    $tw = $this->targetWilayah;
    $dataRw = \App\Models\DataRw::where('target_wilayah_id', $tw->id)
        ->where('nomor_rw', $nomorRw)->first();
    $korwe = \App\Models\Korwe::where('target_wilayah_id', $tw->id)
        ->where('nomor_rw', $nomorRw)->first();

    // PKS suara & ranking per desa (estimasi di level RW)
    $pksData = $dataRw ? [
        'estimasi_pks' => $dataRw->estimasi_pks,
        'estimasi_share' => $dataRw->estimasi_share,
        'estimasi_ranking' => $dataRw->estimasi_ranking,
        'dpt' => $dataRw->dpt,
        'jumlah_rt' => $dataRw->jumlah_rt,
    ] : [];

    // Top partai di desa ini
    $topPartai = \App\Models\RekapPartaiDesa::where('kecamatan', $tw->kecamatan)
        ->where('desa', $tw->desa)
        ->orderByDesc('total_suara')
        ->limit(3)->get();

    // Top caleg PKS di desa ini
    $topCalegPks = \App\Models\RekapCalegDesa::where('kecamatan', $tw->kecamatan)
        ->where('desa', $tw->desa)
        ->whereHas('caleg', fn($q) => $q->where('partai', 'PKS'))
        ->with('caleg')
        ->orderByDesc('suara')
        ->first();

    $this->autoFillData = [
        'jumlah_rt' => $pksData['jumlah_rt'] ?? $tw->jumlah_rt ?? 0,
        'dpt' => $pksData['dpt'] ?? 0,
        'estimasi_pks' => $pksData['estimasi_pks'] ?? 0,
        'estimasi_share' => $pksData['estimasi_share'] ?? 0,
        'partai_pemenang' => $topPartai->first() ? $topPartai->first()->partai . ' (' . number_format($topPartai->first()->total_suara) . ')' : '-',
        'top_3_partai' => $topPartai->map(fn($p) => $p->partai . ' ' . number_format($p->total_suara))->implode(', '),
        'caleg_pks_tertinggi' => $topCalegPks ? $topCalegPks->caleg->nama . ' (' . $topCalegPks->suara . ' suara)' : '-',
        'target_suara' => round($tw->target_avg_per_rw),
        'korwe_nama' => $korwe ? $korwe->nama_koordinator : null,
        'korwe_status' => $korwe ? $korwe->status : 'belum',
        'status_wilayah' => $dataRw->status_wilayah ?? $tw->status_wilayah ?? 'ZONA BERAT',
    ];
}

public function simpanProfil()
{
    $profil = ProfilRw::updateOrCreate(
        [
            'target_wilayah_id' => $this->targetWilayah->id,
            'nomor_rw' => $this->profilRwId,
        ],
        array_merge($this->profilData, [
            'dapil' => $this->targetWilayah->dapil,
            'kecamatan' => $this->targetWilayah->kecamatan,
            'desa' => $this->targetWilayah->desa,
            'filled_by' => auth()->id(),
            'filled_at' => now(),
        ])
    );

    // Hitung completion
    $completion = $profil->calculateCompletion();
    $profil->update([
        'completion_percent' => $completion,
        'is_complete' => $completion >= 80,
    ]);

    $this->showProfilDrawer = false;
    session()->flash('message', 'Profil RW ' . $this->profilRwId . ' berhasil disimpan.');
}

public function closeProfilDrawer()
{
    $this->showProfilDrawer = false;
    $this->profilRwId = null;
}
```

== 4. UPDATE VIEW DETAIL — Tombol Profil + Drawer + Progress Card ==

Update: resources/views/livewire/infra-rtrw/detail.blade.php

A) TAMBAH PROGRESS CARD PROFIL di summary cards section:

Tambahkan 1 card baru setelah card KORWE/KORTE yang sudah ada. Jadi total 5 cards (grid-5 atau grid tetap 4 tapi card baru di baris berikutnya):

```html
{{-- Card: Progress Profil RW --}}
@php
    $totalRw = $this->rwList->count();
    $profilTerisi = \App\Models\ProfilRw::where('target_wilayah_id', $targetWilayah->id)
        ->where('completion_percent', '>', 0)->count();
    $profilLengkap = \App\Models\ProfilRw::where('target_wilayah_id', $targetWilayah->id)
        ->where('is_complete', true)->count();
    $pctProfil = $totalRw > 0 ? round($profilTerisi / $totalRw * 100) : 0;
@endphp
<div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
    <div class="text-xs text-zinc-500 flex items-center gap-1 mb-1">
        <i class="ti ti-file-text" style="font-size:13px;" aria-hidden="true"></i> Profil RW
    </div>
    <div class="text-2xl font-medium text-zinc-900 dark:text-zinc-100">{{ $profilTerisi }} / {{ $totalRw }}</div>
    <div class="text-xs text-zinc-400 mt-0.5">{{ $profilLengkap }} lengkap · {{ $pctProfil }}% terisi</div>
    <div class="mt-2 h-1.5 bg-zinc-100 dark:bg-zinc-700 rounded-full overflow-hidden">
        <div class="h-full bg-blue-500 rounded-full" style="width:{{ $pctProfil }}%"></div>
    </div>
</div>
```

B) UPDATE SETIAP ROW RW — tambah tombol Profil:

Di bagian action buttons setiap RW row, tambahkan tombol Profil di samping Assign/Edit:

```html
<div class="flex gap-2 flex-shrink-0">
    {{-- Tombol KORWE (sudah ada) --}}
    @if($hasKorwe)
        <button wire:click="editKorwe('{{ $korwe->id }}')" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-md border border-green-500 text-green-600">
            <i class="ti ti-edit" style="font-size:11px;" aria-hidden="true"></i> Edit
        </button>
    @else
        <button wire:click="assignKorwe('{{ $rw->nomor_rw }}')" class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-md font-medium {{ $rw->prioritas_urutan <= 2 ? 'bg-orange-600 text-white' : 'border border-zinc-300 text-zinc-600' }}">
            + Assign
        </button>
    @endif

    {{-- Tombol PROFIL (BARU) --}}
    @php
        $profil = \App\Models\ProfilRw::where('target_wilayah_id', $targetWilayah->id)
            ->where('nomor_rw', $rw->nomor_rw)->first();
        $hasProfil = $profil && $profil->completion_percent > 0;
        $profilComplete = $profil && $profil->is_complete;
    @endphp
    <button wire:click="openProfil('{{ $rw->nomor_rw }}')"
        class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-md font-medium
        {{ $profilComplete
            ? 'border border-blue-500 text-blue-600 bg-blue-50 dark:bg-blue-950/20'
            : ($hasProfil
                ? 'border border-amber-500 text-amber-600 bg-amber-50 dark:bg-amber-950/20'
                : 'border border-zinc-300 dark:border-zinc-600 text-zinc-500')
        }}">
        <i class="ti ti-file-text" style="font-size:11px;" aria-hidden="true"></i>
        {{ $profilComplete ? 'Profil ✓' : ($hasProfil ? 'Profil ' . $profil->completion_percent . '%' : 'Profil') }}
    </button>
</div>
```

Dan tambahkan badge "Profil ✓" di info RW jika profil sudah lengkap:
```html
@if($profilComplete)
    <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-medium">Profil ✓</span>
@endif
```

C) TAMBAH PROGRESS BAR PROFIL di bawah list RW (setelah progress KORWE):

```html
{{-- Progress Profil --}}
<div class="mt-2 flex items-center gap-3 text-xs">
    <span class="text-zinc-500">Profil terisi:</span>
    <div class="flex-1 h-1.5 bg-zinc-100 dark:bg-zinc-700 rounded-full overflow-hidden">
        <div class="h-full bg-blue-500 rounded-full" style="width:{{ $pctProfil }}%"></div>
    </div>
    <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $profilTerisi }} / {{ $totalRw }} RW ({{ $pctProfil }}%)</span>
</div>
```

D) DRAWER PROFIL RW:

Tambahkan di akhir view (sebelum closing tag layout):

```html
{{-- Profil RW Drawer --}}
@if($showProfilDrawer && $profilRwId)
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/30 z-40" wire:click="closeProfilDrawer"></div>

    {{-- Drawer --}}
    <div class="fixed top-0 right-0 w-[440px] h-full bg-white dark:bg-zinc-800 shadow-xl z-50 overflow-y-auto">
        {{-- Header --}}
        <div class="sticky top-0 bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 p-4 z-10">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium">Profil RW {{ $profilRwId }} — {{ $targetWilayah->desa }}</div>
                    <div class="text-xs text-zinc-500 mt-0.5">{{ $targetWilayah->kecamatan }} · {{ $targetWilayah->dapil }}</div>
                </div>
                <div class="flex items-center gap-2">
                    @php $statusCfg = \App\Models\TargetWilayah::STATUS_CONFIG[$autoFillData['status_wilayah'] ?? 'ZONA BERAT'] ?? []; @endphp
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium" style="background:{{ $statusCfg['bg'] ?? '#fee2e2' }};color:{{ $statusCfg['text'] ?? '#991b1b' }};">
                        {{ $statusCfg['label'] ?? 'Zona Berat' }}
                    </span>
                    <button wire:click="closeProfilDrawer" class="w-7 h-7 rounded-md border border-zinc-200 dark:border-zinc-600 flex items-center justify-center text-zinc-400 hover:text-zinc-600">✕</button>
                </div>
            </div>
        </div>

        <div class="p-4 space-y-4">
            {{-- SECTION: Data Otomatis --}}
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-blue-600 mb-2">
                    <i class="ti ti-database" style="font-size:14px;" aria-hidden="true"></i>
                    Data otomatis
                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-900/30 text-blue-600">auto-fill</span>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <div class="bg-zinc-50 dark:bg-zinc-700/30 rounded-lg p-2">
                        <div class="text-[10px] text-zinc-500">Jumlah RT</div>
                        <div class="text-sm font-medium">{{ $autoFillData['jumlah_rt'] ?? 0 }}</div>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-700/30 rounded-lg p-2">
                        <div class="text-[10px] text-zinc-500">DPT</div>
                        <div class="text-sm font-medium">{{ number_format($autoFillData['dpt'] ?? 0) }}</div>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-700/30 rounded-lg p-2">
                        <div class="text-[10px] text-zinc-500">Est. Suara PKS</div>
                        <div class="text-sm font-medium text-orange-600">~{{ number_format($autoFillData['estimasi_pks'] ?? 0) }}</div>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-700/30 rounded-lg p-2">
                        <div class="text-[10px] text-zinc-500">Target 2029</div>
                        <div class="text-sm font-medium text-orange-600">{{ number_format($autoFillData['target_suara'] ?? 0) }}</div>
                    </div>
                </div>
                <div class="text-xs text-zinc-500 bg-zinc-50 dark:bg-zinc-700/30 rounded-lg p-2 leading-relaxed">
                    <strong>Caleg PKS tertinggi:</strong> {{ $autoFillData['caleg_pks_tertinggi'] ?? '-' }}<br>
                    <strong>Partai pemenang:</strong> {{ $autoFillData['partai_pemenang'] ?? '-' }}<br>
                    <strong>3 partai tertinggi:</strong> {{ $autoFillData['top_3_partai'] ?? '-' }}<br>
                    @if($autoFillData['korwe_nama'] ?? null)
                        <strong>KORWE:</strong> {{ $autoFillData['korwe_nama'] }} ({{ $autoFillData['korwe_status'] }})
                    @endif
                </div>
            </div>

            <hr class="border-zinc-200 dark:border-zinc-700">

            {{-- SECTION: Profil Wilayah --}}
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-amber-600 mb-2">
                    <i class="ti ti-map-pin" style="font-size:14px;" aria-hidden="true"></i>
                    Profil wilayah
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-zinc-500 mb-1 block">Tipologi RW</label>
                        <select wire:model="profilData.tipologi" class="w-full h-9 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3">
                            <option value="">— Pilih —</option>
                            @foreach(\App\Models\ProfilRw::TIPOLOGI_OPTIONS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500 mb-1 block">Sumber ekonomi dominan</label>
                        <select wire:model="profilData.ekonomi_dominan" class="w-full h-9 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3">
                            <option value="">— Pilih —</option>
                            @foreach(\App\Models\ProfilRw::EKONOMI_OPTIONS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500 mb-1 block">Profil umum warga</label>
                        <textarea wire:model="profilData.profil_warga" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm p-2 min-h-[50px]" rows="2" placeholder="Agama, kebiasaan, pragmatisme pemilih..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs text-zinc-500 mb-1 block">Suara PKS 2019</label>
                            <input wire:model="profilData.suara_pks_2019" type="number" class="w-full h-9 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3" placeholder="0">
                        </div>
                        <div>
                            <label class="text-xs text-zinc-500 mb-1 block">Jumlah KTA</label>
                            <input wire:model="profilData.jumlah_kta" type="number" class="w-full h-9 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3" placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500 mb-1 block">Faktor penyebab menang/kalah</label>
                        <textarea wire:model="profilData.faktor_penyebab" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm p-2 min-h-[40px]" rows="2" placeholder="Caleg lokal, tokoh kuat, pragmatisme..."></textarea>
                    </div>
                </div>
            </div>

            <hr class="border-zinc-200 dark:border-zinc-700">

            {{-- SECTION: Infrastruktur Partai --}}
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-green-600 mb-2">
                    <i class="ti ti-building" style="font-size:14px;" aria-hidden="true"></i>
                    Infrastruktur partai
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-zinc-500 mb-1 block">Anggota PKS di RW</label>
                        <textarea wire:model="profilData.anggota_pks" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm p-2 min-h-[36px]" rows="1" placeholder="Nama + jenjang keanggotaan"></textarea>
                    </div>

                    {{-- Checklist infra --}}
                    @php
                        $infraItems = [
                            ['field' => 'upa_rw', 'label' => 'UPA RW', 'name_field' => 'upa_rw_nama', 'placeholder' => 'Nama pembina'],
                            ['field' => 'rki', 'label' => 'RKI', 'name_field' => 'rki_nama', 'placeholder' => 'Nama penggerak'],
                            ['field' => 'senam', 'label' => 'Titik Senam PKS', 'name_field' => 'senam_nama', 'placeholder' => 'Nama instruktur'],
                            ['field' => 'relawan_milenial', 'label' => 'Relawan Milenial / Geka', 'name_field' => 'relawan_milenial_nama', 'placeholder' => 'Nama + jabatan'],
                        ];
                    @endphp
                    @foreach($infraItems as $item)
                        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-2">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-medium">{{ $item['label'] }}</span>
                                <select wire:model.live="profilData.{{ $item['field'] }}_status" class="h-7 text-xs rounded border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-2">
                                    <option value="belum">Belum</option>
                                    <option value="sudah">Sudah</option>
                                </select>
                            </div>
                            @if(($profilData[$item['field'] . '_status'] ?? 'belum') === 'sudah')
                                <input wire:model="profilData.{{ $item['name_field'] }}" class="w-full h-7 rounded border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-xs px-2 mt-1" placeholder="{{ $item['placeholder'] }}">
                            @endif
                        </div>
                    @endforeach

                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-2">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium">Caleg terpilih di RW?</span>
                            <select wire:model.live="profilData.caleg_terpilih_ada" class="h-7 text-xs rounded border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-2">
                                <option value="0">Tidak</option>
                                <option value="1">Ya</option>
                            </select>
                        </div>
                        @if($profilData['caleg_terpilih_ada'] ?? false)
                            <input wire:model="profilData.caleg_terpilih_nama" class="w-full h-7 rounded border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-xs px-2 mt-1" placeholder="Nama caleg">
                        @endif
                    </div>
                </div>
            </div>

            <hr class="border-zinc-200 dark:border-zinc-700">

            {{-- SECTION: Peta Politik Lokal --}}
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-red-600 mb-2">
                    <i class="ti ti-chess" style="font-size:14px;" aria-hidden="true"></i>
                    Peta politik lokal
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-zinc-500 mb-1 block">Afiliasi Ketua RW & RT</label>
                        <textarea wire:model="profilData.afiliasi_rw_rt" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm p-2 min-h-[50px]" rows="3" placeholder="Ketua RW: Nama - Partai&#10;RT 1: Nama - Partai&#10;RT 2: ..."></textarea>
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500 mb-1 block">Afiliasi Kader Posyandu & DKM</label>
                        <textarea wire:model="profilData.afiliasi_posyandu_dkm" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm p-2 min-h-[36px]" rows="1" placeholder="Nama - organisasi - partai"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs text-zinc-500 mb-1 block text-red-600">Pengurus kompetitor?</label>
                            <select wire:model.live="profilData.kompetitor_status" class="w-full h-8 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-xs px-2">
                                <option value="tidak_tahu">Tidak tahu</option>
                                <option value="ada">Ada</option>
                                <option value="tidak">Tidak ada</option>
                            </select>
                            @if(($profilData['kompetitor_status'] ?? '') === 'ada')
                                <input wire:model="profilData.kompetitor_detail" class="w-full h-7 rounded border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-xs px-2 mt-1" placeholder="Nama + partai">
                            @endif
                        </div>
                        <div>
                            <label class="text-xs text-zinc-500 mb-1 block text-red-600">Tim sukses lain?</label>
                            <select wire:model.live="profilData.tim_sukses_status" class="w-full h-8 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-xs px-2">
                                <option value="tidak_tahu">Tidak tahu</option>
                                <option value="ada">Ada</option>
                                <option value="tidak">Tidak ada</option>
                            </select>
                            @if(($profilData['tim_sukses_status'] ?? '') === 'ada')
                                <input wire:model="profilData.tim_sukses_detail" class="w-full h-7 rounded border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-xs px-2 mt-1" placeholder="Nama + partai">
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-zinc-200 dark:border-zinc-700">

            {{-- SECTION: Strategi --}}
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-orange-600 mb-2">
                    <i class="ti ti-target-arrow" style="font-size:14px;" aria-hidden="true"></i>
                    Strategi & penanggung jawab
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-zinc-500 mb-1 block">Strategi mencapai target suara</label>
                        <textarea wire:model="profilData.strategi" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm p-2 min-h-[60px]" rows="3" placeholder="Rencana aksi untuk meningkatkan suara"></textarea>
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500 mb-1 block">Penanggung jawab dakwah di RW</label>
                        <input wire:model="profilData.penanggung_jawab" class="w-full h-9 rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm px-3" placeholder="Nama + jenjang">
                    </div>
                    <div>
                        <label class="text-xs text-zinc-500 mb-1 block">Keterangan lain</label>
                        <textarea wire:model="profilData.keterangan_lain" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-sm p-2 min-h-[36px]" rows="1" placeholder="Catatan tambahan"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="sticky bottom-0 bg-white dark:bg-zinc-800 border-t border-zinc-200 dark:border-zinc-700 p-4 flex gap-2">
            <button wire:click="simpanProfil" class="flex-1 py-2 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700">
                Simpan Profil
            </button>
            <button wire:click="closeProfilDrawer" class="py-2 px-4 border border-zinc-300 dark:border-zinc-600 rounded-lg text-sm text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700">
                Batal
            </button>
        </div>
    </div>
@endif
```

== 5. UPDATE INDEX PAGE — Card Progress Profil ==

Di app/Livewire/InfraRtRw/Index.php, tambahkan ke getSummaryData():

```php
'profil_terisi' => \App\Models\ProfilRw::query()
    ->when($this->selectedDapil, fn($q, $v) => $q->where('dapil', $v))
    ->where('completion_percent', '>', 0)->count(),
'profil_lengkap' => \App\Models\ProfilRw::query()
    ->when($this->selectedDapil, fn($q, $v) => $q->where('dapil', $v))
    ->where('is_complete', true)->count(),
'total_rw_all' => \App\Models\DataRw::query()
    ->when($this->selectedDapil, fn($q, $v) => $q->where('dapil', $v))->count(),
```

Di index view, tambahkan CARD BARU di summary cards section (jadikan grid-5 atau tambah baris baru):

```html
{{-- Card: Progress Profil --}}
<div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
    <div class="text-xs text-zinc-500 flex items-center gap-1 mb-1">
        <i class="ti ti-file-text" style="font-size:13px;" aria-hidden="true"></i> Profil RW
    </div>
    <div class="text-2xl font-medium text-zinc-900 dark:text-zinc-100">
        {{ $summary['profil_terisi'] }} / {{ $summary['total_rw_all'] }}
    </div>
    <div class="text-xs text-zinc-400 mt-0.5">
        {{ $summary['profil_lengkap'] }} lengkap ·
        {{ $summary['total_rw_all'] > 0 ? round($summary['profil_terisi'] / $summary['total_rw_all'] * 100) : 0 }}% terisi
    </div>
    <div class="mt-2 h-1.5 bg-zinc-100 dark:bg-zinc-700 rounded-full overflow-hidden">
        <div class="h-full bg-blue-500 rounded-full" style="width:{{ $summary['total_rw_all'] > 0 ? round($summary['profil_terisi'] / $summary['total_rw_all'] * 100) : 0 }}%"></div>
    </div>
</div>
```

Juga tambahkan progress profil di progress per dapil section (bar biru di bawah KORWE/KORTE bars):

```html
<div class="flex items-center gap-3 mt-1">
    <div class="w-16 text-xs text-zinc-400">Profil</div>
    <div class="w-14 text-xs text-zinc-400 text-right">{{ $dp['profil_terisi'] }} / {{ $dp['total_rw'] }}</div>
    <div class="flex-1 h-3 bg-zinc-100 dark:bg-zinc-700 rounded overflow-hidden">
        <div class="h-full rounded" style="width:{{ $dp['profil_persen'] }}%;background:#3b82f6;min-width:{{ $dp['profil_persen'] > 0 ? '24px' : '0' }};">
        </div>
    </div>
    <div class="w-10 text-xs font-medium text-right {{ $dp['profil_persen'] > 0 ? 'text-blue-600' : 'text-zinc-400' }}">{{ $dp['profil_persen'] }}%</div>
</div>
```

Update dapilProgress query untuk include profil count per dapil.

== 6. UPDATE TABEL DESA (INDEX) — Kolom Profil ==

Tambahkan kolom "Profil" di tabel desa:

Header:
```html
<th class="text-center py-2 px-2 text-xs text-zinc-500 uppercase tracking-wider font-medium">Profil</th>
```

Cell:
```html
@php
    $profilCount = \App\Models\ProfilRw::where('target_wilayah_id', $tw->id)->where('completion_percent', '>', 0)->count();
    $profilTotal = $tw->jumlah_rw;
    $profilPct = $profilTotal > 0 ? round($profilCount / $profilTotal * 100) : 0;
@endphp
<td class="text-center py-2 px-2">
    <div class="flex items-center gap-1.5 justify-center">
        <div class="w-10 h-1.5 bg-zinc-100 dark:bg-zinc-700 rounded-full overflow-hidden">
            <div class="h-full bg-blue-500 rounded-full" style="width:{{ $profilPct }}%"></div>
        </div>
        <span class="text-[10px] {{ $profilPct > 0 ? 'text-blue-600 font-medium' : 'text-zinc-400' }}">
            {{ $profilCount }}/{{ $profilTotal }}
        </span>
    </div>
</td>
```

Langsung kerjakan semua. Jangan test.
```
