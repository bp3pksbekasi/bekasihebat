# Prompt Trae — Extend Event: Input Peserta + Auto-Distribute ke Sapa Warga

Tambahkan fitur input peserta di halaman detail event. Peserta otomatis masuk ke database Sapa Warga per RW. Langsung kerjakan, JANGAN test, JANGAN tanya.

---

```
== 1. MIGRATION: create_event_pesertas_table ==

Peserta yang hadir di event (bisa dari QR scan atau input manual).

```php
Schema::create('event_pesertas', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('event_id');
    $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
    $table->string('nama');
    $table->string('no_hp')->nullable();
    $table->string('no_wa')->nullable();
    $table->string('alamat')->nullable();
    $table->string('dapil')->nullable();
    $table->string('kecamatan')->nullable();
    $table->string('desa')->nullable();
    $table->string('nomor_rw')->nullable();
    $table->string('nomor_rt')->nullable();
    $table->uuid('target_wilayah_id')->nullable();
    $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->nullOnDelete();
    // Link ke member (jika sudah terdaftar)
    $table->uuid('member_id')->nullable();
    $table->foreign('member_id')->references('id')->on('members')->nullOnDelete();
    // Sumber input
    $table->string('metode')->default('manual');  // manual, bulk, qr_scan, form_online
    $table->boolean('synced_sapa_warga')->default(false); // sudah masuk ke kontak_wargas atau belum
    $table->uuid('kontak_warga_id')->nullable();  // link ke record di sapa warga
    $table->foreign('kontak_warga_id')->references('id')->on('kontak_wargas')->nullOnDelete();
    $table->text('catatan')->nullable();
    $table->uuid('created_by')->nullable();
    $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
    $table->timestamps();

    $table->index(['event_id']);
    $table->index(['target_wilayah_id', 'nomor_rw']);
    $table->index(['synced_sapa_warga']);
    $table->unique(['event_id', 'no_hp']); // 1 nomor HP hanya 1x per event
});
```

== 2. MODEL: EventPeserta ==

File: app/Models/EventPeserta.php

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPeserta extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'event_id', 'nama', 'no_hp', 'no_wa', 'alamat',
        'dapil', 'kecamatan', 'desa', 'nomor_rw', 'nomor_rt', 'target_wilayah_id',
        'member_id', 'metode', 'synced_sapa_warga', 'kontak_warga_id',
        'catatan', 'created_by',
    ];

    protected $casts = ['synced_sapa_warga' => 'boolean'];

    public function event() { return $this->belongsTo(Event::class); }
    public function targetWilayah() { return $this->belongsTo(TargetWilayah::class); }
    public function member() { return $this->belongsTo(Member::class); }
    public function kontakWarga() { return $this->belongsTo(KontakWarga::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
```

Tambah relationship di Event model:
```php
public function pesertas() { return $this->hasMany(EventPeserta::class); }
public function getPesertaCountAttribute() { return $this->pesertas()->count(); }
```

== 3. LIVEWIRE: Tambahkan ke halaman detail event ==

Di Livewire component Event Detail (atau buat partial baru), tambahkan:

```php
// Properties
public $showPesertaForm = false;
public $pesertaTab = 'bulk'; // bulk, single, scan
public $bulkPesertaText = '';
public $bulkPesertaParsed = [];
public $bulkDefaultDapil = '';
public $bulkDefaultKecamatan = '';
public $bulkDefaultDesa = '';

// Single form
public $spNama = '';
public $spHp = '';
public $spRw = '';
public $spRt = '';
public $spDesaId = '';

// === BULK PARSE ===
public function updatedBulkPesertaText()
{
    $lines = array_filter(array_map('trim', explode("\n", $this->bulkPesertaText)));
    $this->bulkPesertaParsed = [];

    foreach ($lines as $line) {
        // Format: nama, RW, no HP
        // Atau: nama, no HP (tanpa RW)
        // Atau: nama, RW xxx, no HP
        $parts = preg_split('/[,\t]+/', $line);

        $nama = trim($parts[0] ?? '');
        $rw = null;
        $hp = null;

        foreach (array_slice($parts, 1) as $part) {
            $part = trim($part);
            // Deteksi RW
            if (preg_match('/^(RW\s*)?(\d{1,3})$/i', $part, $m) && strlen($m[2]) <= 3 && !preg_match('/^08/', $part)) {
                $rw = str_pad($m[2], 3, '0', STR_PAD_LEFT);
            }
            // Deteksi nomor HP
            elseif (preg_match('/^0[87]\d[\d\-\s]{7,15}$/', preg_replace('/[\s\-]/', '', $part))) {
                $hp = preg_replace('/[\s\-]/', '', $part);
            }
        }

        if ($nama) {
            $this->bulkPesertaParsed[] = [
                'nama' => $nama,
                'rw' => $rw,
                'hp' => $hp,
            ];
        }
    }
}

// === SIMPAN BULK PESERTA ===
public function simpanBulkPeserta()
{
    if (empty($this->bulkPesertaParsed)) return;

    $event = $this->event; // Event model dari parent
    $count = 0;
    $countSapa = 0;

    foreach ($this->bulkPesertaParsed as $item) {
        if (empty($item['nama'])) continue;

        // Skip duplikat (by no_hp di event yang sama)
        if ($item['hp'] && EventPeserta::where('event_id', $event->id)->where('no_hp', $item['hp'])->exists()) continue;

        // Cari target_wilayah berdasarkan event location atau default
        $tw = null;
        if ($this->bulkDefaultDesa) {
            $tw = TargetWilayah::find($this->bulkDefaultDesa);
        } elseif ($event->target_wilayah_id) {
            $tw = $event->targetWilayah;
        }

        $peserta = EventPeserta::create([
            'event_id' => $event->id,
            'nama' => $item['nama'],
            'no_hp' => $item['hp'],
            'no_wa' => $item['hp'],
            'nomor_rw' => $item['rw'],
            'dapil' => $tw?->dapil ?? $this->bulkDefaultDapil,
            'kecamatan' => $tw?->kecamatan ?? $this->bulkDefaultKecamatan,
            'desa' => $tw?->desa,
            'target_wilayah_id' => $tw?->id,
            'metode' => 'bulk',
            'created_by' => auth()->id(),
        ]);
        $count++;

        // === AUTO-DISTRIBUTE KE SAPA WARGA ===
        if ($tw && $item['rw'] && $item['hp']) {
            // Cek duplikat di sapa warga (no_wa + RW yang sama)
            $existingKontak = KontakWarga::where('target_wilayah_id', $tw->id)
                ->where('nomor_rw', $item['rw'])
                ->where('no_wa', $item['hp'])->first();

            if (!$existingKontak) {
                $kontak = KontakWarga::create([
                    'target_wilayah_id' => $tw->id,
                    'dapil' => $tw->dapil,
                    'kecamatan' => $tw->kecamatan,
                    'desa' => $tw->desa,
                    'nomor_rw' => $item['rw'],
                    'nama' => $item['nama'],
                    'no_wa' => $item['hp'],
                    'sumber' => 'event',
                    'catatan' => 'Dari event: ' . $event->nama,
                    'created_by' => auth()->id(),
                ]);
                $peserta->update([
                    'synced_sapa_warga' => true,
                    'kontak_warga_id' => $kontak->id,
                ]);
                $countSapa++;
            } else {
                // Sudah ada di sapa warga, link saja
                $peserta->update([
                    'synced_sapa_warga' => true,
                    'kontak_warga_id' => $existingKontak->id,
                ]);
            }
        }
    }

    // Update event peserta count
    $event->update(['peserta_hadir' => $event->pesertas()->count()]);

    $this->bulkPesertaText = '';
    $this->bulkPesertaParsed = [];
    $this->showPesertaForm = false;

    session()->flash('message', "{$count} peserta dicatat. {$countSapa} baru masuk ke Sapa Warga.");
}

// === SIMPAN SINGLE ===
public function simpanSinglePeserta()
{
    $this->validate(['spNama' => 'required']);

    $event = $this->event;
    $tw = $this->spDesaId ? TargetWilayah::find($this->spDesaId) : $event->targetWilayah;

    $peserta = EventPeserta::create([
        'event_id' => $event->id,
        'nama' => $this->spNama,
        'no_hp' => $this->spHp,
        'no_wa' => $this->spHp,
        'nomor_rw' => $this->spRw,
        'nomor_rt' => $this->spRt,
        'dapil' => $tw?->dapil,
        'kecamatan' => $tw?->kecamatan,
        'desa' => $tw?->desa,
        'target_wilayah_id' => $tw?->id,
        'metode' => 'manual',
        'created_by' => auth()->id(),
    ]);

    // Auto-distribute
    if ($tw && $this->spRw && $this->spHp) {
        $existing = KontakWarga::where('target_wilayah_id', $tw->id)
            ->where('nomor_rw', $this->spRw)
            ->where('no_wa', $this->spHp)->first();

        if (!$existing) {
            $kontak = KontakWarga::create([
                'target_wilayah_id' => $tw->id,
                'dapil' => $tw->dapil,
                'kecamatan' => $tw->kecamatan,
                'desa' => $tw->desa,
                'nomor_rw' => $this->spRw,
                'nama' => $this->spNama,
                'no_wa' => $this->spHp,
                'sumber' => 'event',
                'catatan' => 'Dari event: ' . $event->nama,
                'created_by' => auth()->id(),
            ]);
            $peserta->update(['synced_sapa_warga' => true, 'kontak_warga_id' => $kontak->id]);
        }
    }

    $event->update(['peserta_hadir' => $event->pesertas()->count()]);
    $this->spNama = $this->spHp = $this->spRw = $this->spRt = '';
    session()->flash('message', 'Peserta dicatat.');
}

// === HAPUS PESERTA ===
public function hapusPeserta($id)
{
    $peserta = EventPeserta::findOrFail($id);
    // Tidak hapus dari sapa warga (data tetap)
    $peserta->delete();
    $this->event->update(['peserta_hadir' => $this->event->pesertas()->count()]);
}

// === SYNC MANUAL (untuk peserta yang belum auto-sync) ===
public function syncSemuaKeSapaWarga()
{
    $unsynced = EventPeserta::where('event_id', $this->event->id)
        ->where('synced_sapa_warga', false)
        ->whereNotNull('nomor_rw')
        ->whereNotNull('no_hp')
        ->whereNotNull('target_wilayah_id')
        ->get();

    $count = 0;
    foreach ($unsynced as $peserta) {
        $existing = KontakWarga::where('target_wilayah_id', $peserta->target_wilayah_id)
            ->where('nomor_rw', $peserta->nomor_rw)
            ->where('no_wa', $peserta->no_hp)->first();

        if (!$existing) {
            $kontak = KontakWarga::create([
                'target_wilayah_id' => $peserta->target_wilayah_id,
                'dapil' => $peserta->dapil,
                'kecamatan' => $peserta->kecamatan,
                'desa' => $peserta->desa,
                'nomor_rw' => $peserta->nomor_rw,
                'nama' => $peserta->nama,
                'no_wa' => $peserta->no_hp,
                'sumber' => 'event',
                'created_by' => auth()->id(),
            ]);
            $peserta->update(['synced_sapa_warga' => true, 'kontak_warga_id' => $kontak->id]);
            $count++;
        } else {
            $peserta->update(['synced_sapa_warga' => true, 'kontak_warga_id' => $existing->id]);
        }
    }

    session()->flash('message', "{$count} peserta baru disync ke Sapa Warga.");
}

// === COMPUTED ===
public function getPesertaListProperty()
{
    return EventPeserta::where('event_id', $this->event->id)
        ->orderBy('nomor_rw')->orderBy('nama')
        ->get();
}

public function getPesertaSummaryProperty()
{
    $list = $this->pesertaList;
    return [
        'total' => $list->count(),
        'synced' => $list->where('synced_sapa_warga', true)->count(),
        'unsynced' => $list->where('synced_sapa_warga', false)->count(),
        'per_rw' => $list->groupBy('nomor_rw')->map->count()->sortKeys(),
        'unik_rw' => $list->pluck('nomor_rw')->filter()->unique()->count(),
    ];
}
```

== 4. VIEW: Section Peserta di halaman detail event ==

Tambahkan section baru di bawah detail event:

```html
{{-- Section: Peserta Event --}}
<div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-3 mt-3">
    @php $pSum = $this->pesertaSummary; @endphp

    {{-- Header --}}
    <div class="flex items-center justify-between mb-3">
        <div>
            <div class="text-[10px] text-orange-600 uppercase tracking-wider font-medium">Peserta event</div>
            <div class="text-sm font-medium">
                {{ $pSum['total'] }} peserta
                <span class="text-xs text-zinc-400">· {{ $pSum['unik_rw'] }} RW berbeda</span>
            </div>
        </div>
        <div class="flex gap-2">
            @if($pSum['unsynced'] > 0)
                <button wire:click="syncSemuaKeSapaWarga" class="text-[10px] px-2 py-1 border border-green-300 text-green-600 rounded-md">
                    Sync {{ $pSum['unsynced'] }} ke Sapa Warga
                </button>
            @endif
            <button wire:click="$set('showPesertaForm', true)" class="text-[10px] px-2 py-1 bg-orange-600 text-white rounded-md font-medium">
                + Input Peserta
            </button>
        </div>
    </div>

    {{-- Summary per RW --}}
    @if($pSum['per_rw']->isNotEmpty())
    <div class="flex gap-1 flex-wrap mb-3">
        @foreach($pSum['per_rw'] as $rw => $count)
            <span class="text-[9px] px-2 py-0.5 rounded font-medium {{ $count >= 10 ? 'bg-green-100 text-green-700' : ($count >= 5 ? 'bg-amber-100 text-amber-700' : 'bg-zinc-100 text-zinc-500') }}">
                RW {{ $rw ?: '?' }}: {{ $count }}
            </span>
        @endforeach
    </div>
    @endif

    {{-- INPUT FORM (conditional) --}}
    @if($showPesertaForm)
    <div class="border border-orange-200 dark:border-orange-800 rounded-lg p-3 mb-3" style="background:#fff7f1;">
        {{-- Tab: Bulk / Single --}}
        <div class="flex gap-1 mb-2 bg-white rounded-md p-0.5 w-fit">
            <button wire:click="$set('pesertaTab', 'bulk')"
                class="px-3 py-1 rounded text-xs font-medium {{ $pesertaTab === 'bulk' ? 'bg-orange-600 text-white' : 'text-zinc-500' }}">
                Bulk paste
            </button>
            <button wire:click="$set('pesertaTab', 'single')"
                class="px-3 py-1 rounded text-xs font-medium {{ $pesertaTab === 'single' ? 'bg-orange-600 text-white' : 'text-zinc-500' }}">
                Satu-satu
            </button>
        </div>

        @if($pesertaTab === 'bulk')
            {{-- Default lokasi (jika event lintas desa) --}}
            <div class="grid grid-cols-3 gap-2 mb-2">
                <select wire:model="bulkDefaultDapil" class="h-7 rounded-md border border-zinc-300 text-[10px] px-2">
                    <option value="">Dapil (opsional)</option>
                    {{-- dapil options --}}
                </select>
                <select wire:model="bulkDefaultKecamatan" class="h-7 rounded-md border border-zinc-300 text-[10px] px-2">
                    <option value="">Kecamatan</option>
                </select>
                <select wire:model="bulkDefaultDesa" class="h-7 rounded-md border border-zinc-300 text-[10px] px-2">
                    <option value="">Desa</option>
                </select>
            </div>

            <div class="text-[10px] text-zinc-500 mb-1">Paste format: <strong>nama, RW, no HP</strong> (per baris)</div>
            <textarea wire:model.live.debounce.500ms="bulkPesertaText" rows="4"
                class="w-full rounded-md border border-orange-200 text-xs p-2 font-mono bg-white"
                placeholder="Ahmad Fauzi, 003, 08123456789&#10;Siti Nurhaliza, 008, 08567891234&#10;Pak Rohman, 012, 08789012345"></textarea>

            {{-- Preview parsed --}}
            @if(count($bulkPesertaParsed) > 0)
            <div class="mt-2 p-2 bg-white rounded-md border border-zinc-200 max-h-32 overflow-y-auto">
                <div class="text-[10px] text-zinc-400 mb-1">Preview: {{ count($bulkPesertaParsed) }} peserta terdeteksi</div>
                @foreach($bulkPesertaParsed as $i => $p)
                    <div class="text-[10px] py-0.5 {{ $i % 2 ? 'bg-zinc-50' : '' }}">
                        <strong>{{ $p['nama'] }}</strong>
                        @if($p['rw']) <span class="text-zinc-400">RW {{ $p['rw'] }}</span> @endif
                        @if($p['hp']) <span class="text-zinc-400">{{ $p['hp'] }}</span> @endif
                    </div>
                @endforeach
            </div>
            @endif

            <div class="flex items-center justify-between mt-2">
                <div class="text-[10px] text-zinc-500">
                    {{ count($bulkPesertaParsed) }} peserta · auto-sync ke Sapa Warga jika ada RW + HP
                </div>
                <div class="flex gap-2">
                    <button wire:click="$set('showPesertaForm', false)" class="text-xs px-3 py-1 border border-zinc-300 rounded-md text-zinc-500">Batal</button>
                    <button wire:click="simpanBulkPeserta" class="text-xs px-3 py-1 bg-orange-600 text-white rounded-md font-medium"
                        {{ empty($bulkPesertaParsed) ? 'disabled' : '' }}>
                        Simpan {{ count($bulkPesertaParsed) }} peserta
                    </button>
                </div>
            </div>
        @else
            {{-- Single input --}}
            <div class="grid grid-cols-2 gap-2 mb-2">
                <input wire:model="spNama" placeholder="Nama *" class="h-7 rounded-md border border-zinc-300 text-xs px-2">
                <input wire:model="spHp" placeholder="No HP/WA" class="h-7 rounded-md border border-zinc-300 text-xs px-2">
            </div>
            <div class="grid grid-cols-3 gap-2 mb-2">
                <select wire:model="spDesaId" class="h-7 rounded-md border border-zinc-300 text-[10px] px-2">
                    <option value="">Desa</option>
                </select>
                <input wire:model="spRw" placeholder="RW" class="h-7 rounded-md border border-zinc-300 text-xs px-2">
                <input wire:model="spRt" placeholder="RT (opsional)" class="h-7 rounded-md border border-zinc-300 text-xs px-2">
            </div>
            <div class="flex gap-2">
                <button wire:click="simpanSinglePeserta" class="text-xs px-3 py-1 bg-orange-600 text-white rounded-md font-medium">Simpan</button>
                <button wire:click="$set('showPesertaForm', false)" class="text-xs px-3 py-1 border border-zinc-300 rounded-md text-zinc-500">Batal</button>
            </div>
        @endif
    </div>
    @endif

    {{-- DAFTAR PESERTA --}}
    <div class="text-[10px] text-zinc-400 mb-1 flex items-center justify-between">
        <span>Daftar peserta</span>
        <span>
            <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> synced</span>
            <span class="inline-flex items-center gap-1 ml-2"><span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span> belum</span>
        </span>
    </div>

    @foreach($this->pesertaList->groupBy('nomor_rw') as $rw => $members)
        <div class="mb-2">
            <div class="text-[10px] text-zinc-400 font-medium mb-1">
                {{ $rw ? 'RW '.$rw : 'RW belum diketahui' }} ({{ $members->count() }} orang)
            </div>
            @foreach($members as $p)
                <div class="flex items-center gap-2 py-1 px-2 rounded-md text-xs {{ $loop->even ? 'bg-zinc-50 dark:bg-zinc-700/10' : '' }}">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $p->synced_sapa_warga ? 'bg-green-500' : 'bg-zinc-300' }}"></span>
                    <span class="font-medium flex-1">{{ $p->nama }}</span>
                    <span class="text-zinc-400">{{ $p->no_hp ?? '-' }}</span>
                    @if($p->nomor_rt) <span class="text-[9px] px-1 py-0.5 rounded bg-zinc-100 text-zinc-500">RT {{ $p->nomor_rt }}</span> @endif
                    <span class="text-[9px] text-zinc-400">{{ $p->metode }}</span>
                    <button wire:click="hapusPeserta('{{ $p->id }}')" wire:confirm="Hapus peserta?" class="text-zinc-300 hover:text-red-500">
                        <i class="ti ti-x" style="font-size:11px;" aria-hidden="true"></i>
                    </button>
                </div>
            @endforeach
        </div>
    @endforeach

    @if($this->pesertaList->isEmpty())
        <div class="text-center py-4 text-xs text-zinc-400">Belum ada peserta. Klik "+ Input Peserta" untuk mulai.</div>
    @endif
</div>
```

== 5. FLASH MESSAGE ==

Setelah simpan bulk, tampilkan:
```
"25 peserta dicatat. 18 baru masuk ke Sapa Warga."
```

Artinya: 25 orang dicatat kehadirannya di event, dan 18 yang punya RW + HP otomatis masuk ke database Sapa Warga (7 lainnya mungkin sudah ada atau data RW kosong).

== 6. CHECKLIST ==

1. Migration event_pesertas berhasil
2. Halaman detail event: section "Peserta event" muncul
3. Tombol "+ Input Peserta" → form muncul (tab bulk/single)
4. Bulk paste: format "nama, RW, HP" per baris → auto-parse
5. Preview parsed muncul (nama, RW, HP ter-detect)
6. Simpan bulk → peserta masuk ke event_pesertas
7. Auto-distribute: peserta dengan RW + HP → otomatis masuk kontak_wargas (sumber='event')
8. Duplikat check: no_hp sama di event yang sama → skip
9. Duplikat check: no_wa + RW sama di sapa warga → skip (link saja)
10. Dot indicator: hijau = synced ke Sapa Warga, abu = belum
11. Tombol "Sync X ke Sapa Warga" untuk yang belum sync
12. Summary per RW: badge warna (hijau ≥10, amber ≥5, abu <5)
13. Peserta grouped by RW di daftar
14. Hapus peserta berfungsi (tidak hapus dari sapa warga)
15. Event peserta_hadir count auto-update setelah input/hapus
16. Single input form berfungsi

Langsung kerjakan semua. Jangan test.
```
