# Prompt Trae — Mapping RW Detail + Prioritas KORWE per RW

```
Tambahkan data per-RW ke modul Infra RT/RW. Setiap desa harus menampilkan SEMUA RW-nya lengkap dengan DPT, demografi, estimasi kekuatan PKS, dan prioritas pembentukan KORWE.

Data berasal dari:
1. 7 file CSV DPT BARU (format per-pemilih, setiap row = 1 orang pemilih)
2. CSV TPS suara pemilu (tps_dprd.csv)

Semua file sudah ada di storage/app/private/import/. Proses server-side, simpan ke database.

Langsung kerjakan, JANGAN test, JANGAN tanya.

FILE DPT (SEMUA 7 DAPIL LENGKAP):
- dpt_pileg2024_bekasi_1.csv (351K rows)
- dpt_pileg2024_bekasi_2.csv (318K rows)
- dpt_pileg2024_bekasi_3.csv (322K rows)
- dpt_pileg2024_bekasi_4.csv (270K rows)
- dpt_pileg2024_bekasi_5.csv (296K rows)
- dpt_pileg2024_bekasi_6.csv (298K rows)
- dpt_pileg2024_bekasi_7.csv (342K rows)
Total: 2,2 juta rows

Format CSV DPT (separator KOMA):
provinsi,kabkota,dapil,kecamatan,desa,tps,pid,nama,usia,rw,rt,dpt_lk,dpt_pr,dpt_tot

PENTING: Ini data PER-ORANG. Setiap row = 1 pemilih terdaftar. Untuk dapat data per-RW, harus COUNT/AGGREGATE rows per group. Kolom dpt_lk, dpt_pr, dpt_tot adalah total level DESA (sama untuk semua row di desa yang sama).

FILE TPS (sudah ada):
- tps_dprd.csv / atau sudah ada di public/data/pemilu/tps_dprd.csv
- Format: separator SEMICOLON (;)
- Kolom: provinsi;kabkota;dapil;kecamatan;desa;tps;partai_id;partai;nomor_urut;nama;gender;suara

== 1. MIGRATION: Tabel data_rws ==

Buat migration: create_data_rws_table

```php
Schema::create('data_rws', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('target_wilayah_id');
    $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->onDelete('cascade');
    $table->string('dapil');
    $table->string('kecamatan');
    $table->string('desa');
    $table->string('nomor_rw');
    // DPT data (aggregated dari per-person CSV)
    $table->integer('dpt')->default(0);
    $table->integer('dpt_laki')->default(0);
    $table->integer('dpt_perempuan')->default(0);
    $table->integer('gen_z')->default(0);       // usia ≤27
    $table->integer('millennial')->default(0);  // usia 28-43
    $table->integer('gen_x')->default(0);       // usia 44-59
    $table->integer('boomer')->default(0);      // usia ≥60
    $table->integer('jumlah_rt')->default(0);
    $table->integer('jumlah_tps')->default(0);
    // Estimasi suara PKS (dari distribusi proporsi)
    $table->integer('estimasi_pks')->default(0);
    $table->decimal('estimasi_share', 8, 4)->default(0);
    $table->integer('estimasi_ranking')->default(0);
    // Status & prioritas
    $table->string('status_wilayah')->default('ZONA BERAT');
    $table->integer('prioritas_urutan')->default(5);
    // Target
    $table->integer('target_suara_per_rw')->default(0);
    $table->timestamps();

    $table->unique(['target_wilayah_id', 'nomor_rw']);
    $table->index(['dapil', 'kecamatan', 'desa']);
    $table->index(['status_wilayah']);
    $table->index(['prioritas_urutan']);
});
```

Jalankan: php artisan migrate

== 2. MODEL: DataRw ==

File: app/Models/DataRw.php

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataRw extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'target_wilayah_id', 'dapil', 'kecamatan', 'desa', 'nomor_rw',
        'dpt', 'dpt_laki', 'dpt_perempuan',
        'gen_z', 'millennial', 'gen_x', 'boomer',
        'jumlah_rt', 'jumlah_tps',
        'estimasi_pks', 'estimasi_share', 'estimasi_ranking',
        'status_wilayah', 'prioritas_urutan',
        'target_suara_per_rw',
    ];

    public function targetWilayah()
    {
        return $this->belongsTo(TargetWilayah::class);
    }

    public function getStatusConfigAttribute(): array
    {
        return TargetWilayah::STATUS_CONFIG[$this->status_wilayah]
            ?? TargetWilayah::STATUS_CONFIG['ZONA BERAT'];
    }

    public function scopeByDesa($query, $targetWilayahId)
    {
        return $query->where('target_wilayah_id', $targetWilayahId);
    }

    public function scopeOrderByPrioritas($query)
    {
        return $query->orderBy('prioritas_urutan')->orderByDesc('estimasi_pks');
    }
}
```

Tambahkan di TargetWilayah model:
```php
public function dataRws()
{
    return $this->hasMany(DataRw::class);
}
```

== 3. IMPORT COMMAND ==

File: app/Console/Commands/ImportDataRw.php
Signature: import:data-rw

LOGIKA (3 STEP):

STEP 1 — Aggregate DPT per RW dari CSV per-pemilih:

```php
$this->info('Step 1: Reading DPT files (per-person)...');

$rwAgg = []; // key: "KECAMATAN|DESA|RW" => { dpt, laki, prmn, gen_z, mill, gen_x, boom, rts, tps_set }

$dptFiles = glob(storage_path('app/private/import/dpt_pileg2024_bekasi_*.csv'));
if (empty($dptFiles)) {
    $this->error('Tidak ada file dpt_pileg2024_bekasi_*.csv di storage/app/private/import/');
    return;
}

foreach ($dptFiles as $file) {
    $this->info("  Reading: " . basename($file));
    $handle = fopen($file, 'r');
    $header = fgetcsv($handle); // skip header
    
    $lineCount = 0;
    while (($row = fgetcsv($handle)) !== false) {
        $lineCount++;
        
        $kecamatan = strtoupper(trim($row[3] ?? ''));
        $desa = strtoupper(trim($row[4] ?? ''));
        $tps = trim($row[5] ?? '');
        $usia = intval($row[8] ?? 0);
        $rw = trim($row[9] ?? '0');
        $rt = trim($row[10] ?? '0');
        $desaLk = intval($row[11] ?? 0);
        $desaPr = intval($row[12] ?? 0);
        $desaTot = intval($row[13] ?? 0);
        
        // Skip RW 0 (data artifact)
        if ($rw === '0' || $rw === '') continue;
        
        // Normalize RW/RT ke 3 digit
        $rwKey = str_pad($rw, 3, '0', STR_PAD_LEFT);
        $rtKey = str_pad($rt, 3, '0', STR_PAD_LEFT);
        
        $key = "{$kecamatan}|{$desa}|{$rwKey}";
        
        if (!isset($rwAgg[$key])) {
            $rwAgg[$key] = [
                'kecamatan' => $kecamatan,
                'desa' => $desa,
                'rw' => $rwKey,
                'dpt' => 0,
                'gen_z' => 0, 'millennial' => 0, 'gen_x' => 0, 'boomer' => 0,
                'rts' => [],     // set of unique RT
                'tps_set' => [], // set of unique TPS
                'desa_lk' => $desaLk, // untuk estimasi gender ratio
                'desa_pr' => $desaPr,
                'desa_tot' => $desaTot,
            ];
        }
        
        $rwAgg[$key]['dpt']++;
        $rwAgg[$key]['rts'][$rtKey] = true;
        $rwAgg[$key]['tps_set'][$tps] = true;
        
        // Generasi dari usia
        if ($usia <= 27) $rwAgg[$key]['gen_z']++;
        elseif ($usia <= 43) $rwAgg[$key]['millennial']++;
        elseif ($usia <= 59) $rwAgg[$key]['gen_x']++;
        else $rwAgg[$key]['boomer']++;
    }
    fclose($handle);
    $this->info("    → {$lineCount} rows processed");
}

$this->info("Total unique RW: " . count($rwAgg));
```

STEP 2 — Build suara PKS per TPS dari tps_dprd.csv:

```php
$this->info('Step 2: Reading TPS votes data...');

$tpsFile = storage_path('app/private/import/tps_dprd.csv');
if (!file_exists($tpsFile)) {
    $tpsFile = public_path('data/pemilu/tps_dprd.csv');
}

$tpsVotes = []; // key: "KECAMATAN|DESA|TPS" => [ partai => total_suara ]
$tpsTotalVotes = []; // key => total semua suara

$handle = fopen($tpsFile, 'r');
$header = fgetcsv($handle, 0, ';'); // separator semicolon!

while (($row = fgetcsv($handle, 0, ';')) !== false) {
    $kecamatan = strtoupper(trim($row[3] ?? ''));
    $desa = strtoupper(trim($row[4] ?? ''));
    $tps = trim($row[5] ?? '');
    $partai = trim($row[7] ?? '');
    $suara = intval($row[11] ?? 0);
    
    if (!$partai || !$tps) continue;
    
    // Normalize kecamatan
    $kecamatan = str_replace(['SERANGBARU'], ['SERANG BARU'], $kecamatan);
    
    $key = "{$kecamatan}|{$desa}|{$tps}";
    
    if (!isset($tpsVotes[$key])) $tpsVotes[$key] = [];
    $tpsVotes[$key][$partai] = ($tpsVotes[$key][$partai] ?? 0) + $suara;
    $tpsTotalVotes[$key] = ($tpsTotalVotes[$key] ?? 0) + $suara;
}
fclose($handle);

$this->info("Total unique TPS with votes: " . count($tpsVotes));
```

STEP 3 — Distribusi suara PKS ke RW + Insert ke database:

```php
$this->info('Step 3: Computing per-RW PKS estimates and inserting...');

// Build: per TPS, berapa total DPT dari semua RW yang dilayani
$tpsDptTotal = []; // "KECAMATAN|DESA|TPS" => total dpt across all RW
foreach ($rwAgg as $key => $rw) {
    foreach ($rw['tps_set'] as $tps => $_) {
        $tpsKey = "{$rw['kecamatan']}|{$rw['desa']}|{$tps}";
        // Hitung berapa pemilih dari RW ini yang ada di TPS ini
        // Untuk ini kita perlu count per TPS per RW dari raw data
        // Tapi kita sudah punya tps_set — ini set TPS yang dilayani RW ini
        // Kita perlu per-TPS-RW count
    }
}

// Actually, kita perlu re-read DPT untuk build tps-rw mapping yang presisi
// Lebih efisien: simpan count per TPS-RW pair saat Step 1

// REVISI: Di Step 1, tambahkan tracking per TPS-RW:
// Kembali ke Step 1 dan tambahkan:
// $tpsRwCount = []; // "KECAMATAN|DESA|TPS|RW" => count pemilih
// $tpsRwCount["{$kecamatan}|{$desa}|{$tps}|{$rwKey}"] = ($tpsRwCount[...] ?? 0) + 1;
// Dan: $tpsDptCount = []; // "KECAMATAN|DESA|TPS" => total count
// $tpsDptCount["{$kecamatan}|{$desa}|{$tps}"] = ($tpsDptCount[...] ?? 0) + 1;

// Setelah punya tpsRwCount dan tpsDptCount:
$bar = $this->output->createProgressBar(count($rwAgg));
$imported = 0;
$skipped = 0;

foreach ($rwAgg as $key => $rw) {
    $bar->advance();
    
    // Cari target_wilayah
    $tw = TargetWilayah::where('kecamatan', $rw['kecamatan'])
        ->where('desa', $rw['desa'])
        ->first();
    
    if (!$tw) {
        $skipped++;
        continue;
    }
    
    // Estimasi suara PKS untuk RW ini
    $estimasiPerPartai = [];
    
    foreach ($rw['tps_set'] as $tps => $_) {
        $tpsKey = "{$rw['kecamatan']}|{$rw['desa']}|{$tps}";
        $tpsRwKey = "{$rw['kecamatan']}|{$rw['desa']}|{$tps}|{$rw['rw']}";
        
        $dptRwDiTps = $tpsRwCount[$tpsRwKey] ?? 0;
        $dptTotalTps = $tpsDptCount[$tpsKey] ?? 1;
        $factor = $dptTotalTps > 0 ? $dptRwDiTps / $dptTotalTps : 0;
        
        // Distribusi suara setiap partai
        foreach ($tpsVotes[$tpsKey] ?? [] as $partai => $suara) {
            $estimasiPerPartai[$partai] = ($estimasiPerPartai[$partai] ?? 0) + round($suara * $factor);
        }
    }
    
    // PKS
    $estimasiPks = $estimasiPerPartai['PKS'] ?? 0;
    $totalEstimasi = array_sum($estimasiPerPartai);
    $share = $totalEstimasi > 0 ? $estimasiPks / $totalEstimasi : 0;
    
    // Ranking PKS
    arsort($estimasiPerPartai);
    $ranking = 1;
    foreach ($estimasiPerPartai as $partai => $suara) {
        if ($partai === 'PKS') break;
        $ranking++;
    }
    if (!isset($estimasiPerPartai['PKS'])) $ranking = 99;
    
    // Classify status
    $result = TargetWilayah::classifyStatus($estimasiPks, $ranking, $share);
    
    // Estimasi gender dari rasio desa
    $genderRatio = $rw['desa_tot'] > 0 ? $rw['desa_lk'] / $rw['desa_tot'] : 0.5;
    $dptLaki = round($rw['dpt'] * $genderRatio);
    $dptPerempuan = $rw['dpt'] - $dptLaki;
    
    DataRw::updateOrCreate(
        ['target_wilayah_id' => $tw->id, 'nomor_rw' => $rw['rw']],
        [
            'dapil' => $tw->dapil,
            'kecamatan' => $rw['kecamatan'],
            'desa' => $rw['desa'],
            'dpt' => $rw['dpt'],
            'dpt_laki' => $dptLaki,
            'dpt_perempuan' => $dptPerempuan,
            'gen_z' => $rw['gen_z'],
            'millennial' => $rw['millennial'],
            'gen_x' => $rw['gen_x'],
            'boomer' => $rw['boomer'],
            'jumlah_rt' => count($rw['rts']),
            'jumlah_tps' => count($rw['tps_set']),
            'estimasi_pks' => $estimasiPks,
            'estimasi_share' => round($share, 4),
            'estimasi_ranking' => $ranking,
            'status_wilayah' => $result['status'],
            'prioritas_urutan' => $result['prioritas'],
            'target_suara_per_rw' => round($tw->target_avg_per_rw),
        ]
    );
    $imported++;
}

$bar->finish();
$this->newLine();
$this->info("Imported: {$imported}, Skipped: {$skipped}");

// Summary per status
$summary = DataRw::selectRaw('status_wilayah, count(*) as total, sum(dpt) as total_dpt, sum(estimasi_pks) as total_pks')
    ->groupBy('status_wilayah')
    ->get();
$this->table(['Status', 'Jumlah RW', 'Total DPT', 'Est. PKS'], 
    $summary->map(fn($s) => [$s->status_wilayah, $s->total, number_format($s->total_dpt), number_format($s->total_pks)]));

// Summary per dapil
$dapilSummary = DataRw::selectRaw('dapil, count(*) as total_rw')
    ->groupBy('dapil')
    ->orderBy('dapil')
    ->get();
$this->table(['Dapil', 'Total RW'], $dapilSummary->map(fn($d) => [$d->dapil, $d->total_rw]));
```

PENTING untuk implementasi:
1. Step 1 harus JUGA track `$tpsRwCount` dan `$tpsDptCount`:
   ```php
   $tpsRwCount = [];  // "KEC|DESA|TPS|RW" => count pemilih
   $tpsDptCount = []; // "KEC|DESA|TPS" => total count
   
   // Di dalam loop:
   $tpsRwCountKey = "{$kecamatan}|{$desa}|{$tps}|{$rwKey}";
   $tpsDptCountKey = "{$kecamatan}|{$desa}|{$tps}";
   $tpsRwCount[$tpsRwCountKey] = ($tpsRwCount[$tpsRwCountKey] ?? 0) + 1;
   $tpsDptCount[$tpsDptCountKey] = ($tpsDptCount[$tpsDptCountKey] ?? 0) + 1;
   ```

2. File DPT besar (2.2 juta rows). Gunakan fgetcsv (bukan load semua ke memory). Tampilkan progress info per file.

3. Normalize nama kecamatan yang bisa beda format:
   ```php
   $normalizeMap = [
       'SERANGBARU' => 'SERANG BARU',
       'KARANGBAHAGIA' => 'KARANG BAHAGIA',
       'KEDUNGWARINGIN' => 'KEDUNG WARINGIN',
       'CIKARANG KOTA' => 'CIKARANGKOTA',
   ];
   $kecamatan = $normalizeMap[$kecamatan] ?? $kecamatan;
   // Lakukan juga untuk desa jika perlu
   ```

4. RW di CSV DPT baru pakai angka pendek (1, 2, 3...) bukan (001, 002, 003). Normalize ke 3 digit: str_pad($rw, 3, '0', STR_PAD_LEFT).

5. Setelah import selesai, jalankan juga compute:status-wilayah untuk update status desa (jika belum).

Jalankan:
php artisan import:data-rw
php artisan compute:status-wilayah

Verifikasi:
php artisan tinker
>>> DataRw::count()               // harus ~2000+ RW
>>> DataRw::distinct('dapil')->pluck('dapil')  // harus 7 dapil
>>> DataRw::groupBy('status_wilayah')->selectRaw('status_wilayah, count(*)')->get()

== 4. UPDATE DETAIL PAGE — Tampilkan semua RW dengan status ==

Update app/Livewire/InfraRtRw/Detail.php:

Tambahkan computed property:
```php
public function getRwListProperty()
{
    return DataRw::where('target_wilayah_id', $this->targetWilayah->id)
        ->orderBy('prioritas_urutan')
        ->orderByDesc('estimasi_pks')
        ->get()
        ->map(function ($rw) {
            $rw->korwe = Korwe::where('target_wilayah_id', $this->targetWilayah->id)
                ->where('nomor_rw', $rw->nomor_rw)
                ->first();
            return $rw;
        });
}
```

Update resources/views/livewire/infra-rtrw/detail.blade.php:

GANTI section KORWE list dengan mapping RW penuh:

```html
<div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 mb-4">
    <div class="flex items-center justify-between mb-3">
        <div>
            <div class="text-xs text-orange-600 uppercase tracking-wider font-medium">Mapping RW — {{ $targetWilayah->desa }}</div>
            <div class="text-sm font-medium mt-0.5">{{ $this->rwList->count() }} RW · urut berdasarkan prioritas pembentukan KORWE</div>
        </div>
        <div class="flex items-center gap-2 text-xs text-zinc-500">
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-600"></span> Terbentuk</span>
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Proses</span>
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-zinc-300"></span> Belum</span>
        </div>
    </div>

    @if($this->rwList->isEmpty())
        <div class="text-center py-8 text-sm text-zinc-400">
            Data RW belum tersedia. Jalankan <code class="bg-zinc-100 dark:bg-zinc-700 px-1.5 py-0.5 rounded text-xs">php artisan import:data-rw</code>
        </div>
    @else
        <div class="space-y-1.5">
            @foreach($this->rwList as $rw)
                @php
                    $cfg = $rw->status_config;
                    $korwe = $rw->korwe;
                    $hasKorwe = $korwe && $korwe->status === 'terbentuk';
                    $inProcess = $korwe && $korwe->status === 'proses';
                @endphp
                <div class="flex items-center gap-3 p-3 rounded-lg border transition-colors
                    {{ $hasKorwe 
                        ? 'bg-green-50 dark:bg-green-950/20 border-green-200 dark:border-green-800' 
                        : ($inProcess 
                            ? 'bg-amber-50 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800'
                            : ($rw->prioritas_urutan <= 2 
                                ? 'bg-blue-50/50 dark:bg-blue-950/10 border-blue-200 dark:border-blue-800' 
                                : 'bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700'))
                    }}">

                    {{-- Status icon --}}
                    @if($hasKorwe)
                        <div class="w-7 h-7 rounded-md bg-green-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    @elseif($inProcess)
                        <div class="w-7 h-7 rounded-md bg-amber-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    @else
                        <div class="w-7 h-7 rounded-md border-2 border-zinc-300 dark:border-zinc-600 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-medium text-zinc-400">#{{ $rw->prioritas_urutan }}</span>
                        </div>
                    @endif

                    {{-- RW info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">RW {{ $rw->nomor_rw }}</span>
                            <span style="display:inline-block;padding:1px 6px;border-radius:99px;font-size:10px;font-weight:500;background:{{ $cfg['bg'] }};color:{{ $cfg['text'] }};">{{ $cfg['label'] }}</span>
                            @if($rw->prioritas_urutan <= 2 && !$hasKorwe)
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 font-medium">PRIORITAS</span>
                            @endif
                        </div>
                        <div class="text-xs text-zinc-500 mt-0.5 flex items-center gap-3 flex-wrap">
                            <span>DPT: {{ number_format($rw->dpt) }}</span>
                            <span>{{ $rw->jumlah_rt }} RT</span>
                            <span>Est. PKS: ~{{ number_format($rw->estimasi_pks) }}</span>
                            <span>Share: {{ number_format($rw->estimasi_share * 100, 1) }}%</span>
                            <span>Rank: #{{ $rw->estimasi_ranking }}</span>
                        </div>
                        <div class="text-[10px] text-zinc-400 mt-0.5">
                            Z:{{ $rw->gen_z }} · Mil:{{ $rw->millennial }} · X:{{ $rw->gen_x }} · Boom:{{ $rw->boomer }}
                        </div>
                    </div>

                    {{-- KORWE info / Action --}}
                    <div class="text-right flex-shrink-0">
                        @if($hasKorwe)
                            <div class="text-xs font-medium text-green-700 dark:text-green-400">{{ $korwe->nama_koordinator }}</div>
                            <div class="text-[10px] text-green-600 dark:text-green-500">{{ $korwe->no_hp ?? '-' }}</div>
                        @elseif($inProcess)
                            <div class="text-xs font-medium text-amber-700">{{ $korwe->nama_koordinator ?? 'Dalam proses' }}</div>
                            <button wire:click="editKorwe('{{ $korwe->id }}')" class="text-[10px] text-orange-600 underline mt-0.5">Edit</button>
                        @else
                            <button wire:click="assignKorwe('{{ $rw->nomor_rw }}')" 
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs rounded-md font-medium
                                    {{ $rw->prioritas_urutan <= 2 
                                        ? 'bg-orange-600 text-white hover:bg-orange-700' 
                                        : 'border border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700' }}">
                                + Assign
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Summary bar --}}
        @php
            $terbentuk = $this->rwList->filter(fn($r) => $r->korwe && $r->korwe->status === 'terbentuk')->count();
            $targetField = 'target_korwe_' . ($selectedTahun ?? 2026);
            $target = $targetWilayah->$targetField ?? 0;
            $total = $this->rwList->count();
            $persen = $target > 0 ? round($terbentuk / $target * 100) : 0;
        @endphp
        <div class="mt-3 flex items-center gap-3 text-xs">
            <span class="text-zinc-500">KORWE terbentuk:</span>
            <div class="flex-1 h-2 bg-zinc-100 dark:bg-zinc-700 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 rounded-full" style="width:{{ min($persen, 100) }}%"></div>
            </div>
            <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $terbentuk }} / {{ $target }} target ({{ $persen }}%)</span>
            <span class="text-zinc-400">dari {{ $total }} RW total</span>
        </div>

        {{-- Rekomendasi --}}
        @php
            $prioritasRw = $this->rwList->filter(fn($r) => $r->prioritas_urutan <= 2 && (!$r->korwe || $r->korwe->status !== 'terbentuk'));
        @endphp
        @if($prioritasRw->isNotEmpty())
            <div class="mt-3 p-3 rounded-lg text-xs" style="background:#dbeafe;border-left:3px solid #2563eb;">
                <span class="font-medium" style="color:#1e3a5f;">💡 Fokus berikutnya:</span>
                <span style="color:#1e3a5f;">Bentuk KORWE di 
                    @foreach($prioritasRw->take(5) as $prw)
                        <strong>RW {{ $prw->nomor_rw }}</strong> (est. PKS ~{{ $prw->estimasi_pks }}, {{ number_format($prw->dpt) }} DPT){{ !$loop->last ? ', ' : '' }}
                    @endforeach
                    @if($prioritasRw->count() > 5)
                        dan {{ $prioritasRw->count() - 5 }} RW lainnya
                    @endif
                </span>
            </div>
        @endif
    @endif
</div>
```

== 5. UPDATE METHOD assignKorwe ==

Di Detail.php:
```php
public function assignKorwe($nomorRw)
{
    $this->formNomorRw = $nomorRw;
    $this->editId = null;
    $this->formStatus = 'proses';
    $this->showForm = true;
}
```

== 6. UPDATE INDEX — Kolom RW Prioritas ==

Di Index.php query, tambahkan:
```php
->withCount([
    'dataRws as rw_prioritas_count' => fn($q) => $q->where('prioritas_urutan', '<=', 2),
    'dataRws as rw_total' => fn($q) => $q,
])
```

Di index view tabel, tambah kolom:
```html
<th class="text-center py-2 px-2 text-xs text-zinc-500 uppercase tracking-wider font-medium">RW<br>Prioritas</th>
```

```html
<td class="text-center py-2 px-2">
    @if($tw->rw_prioritas_count > 0)
        <span class="inline-flex items-center justify-center min-w-[22px] h-[22px] rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 text-xs font-medium px-1">
            {{ $tw->rw_prioritas_count }}
        </span>
    @else
        <span class="text-xs text-zinc-300">-</span>
    @endif
</td>
```

Langsung kerjakan semua. Jangan test.
```
