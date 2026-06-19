<?php

declare(strict_types=1);

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\EventApproval;
use App\Models\KegiatanRw;
use App\Models\TargetWilayah;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public string $judul = '';

    public string $deskripsi = '';

    public string $jenis = 'baksos';

    public string $tanggalMulai = '';

    public string $tanggalSelesai = '';

    public string $lokasi = '';

    public string $lokasiDesa = '';

    public string $lokasiKecamatan = '';

    public string $lokasiDapil = '';

    public int $kapasitas = 0;

    public bool $isPublic = false;

    public string $penyelenggara = '';

    public string $picNama = '';

    public string $picHp = '';

    public $coverImage;

    public ?string $fromKegiatanRwId = null;

    public string $orgLevel = 'dpra';
    
    public string $bidangDpdId = '';

    public array $sourceKegiatan = [];

    public string $speakers = '';

    public string $fundingSource = '';

    public string $targetProgram = '';

    public string $requirements = '';

    public string $budgetNotes = '';

    public array $budgetItems = [];

    // Tab Pelaksanaan
    public int $pesertaHadir = 0;
    public array $dokFoto = [];

    // Tab Evaluasi
    public string $evaluasiRingkasan = '';
    public string $evaluasiCatatan = '';
    public string $evaluasiRealisasiAnggaran = '0';
    public string $evaluasiRating = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->orgLevel = $user?->org_level ?? 'dpra';
        $this->tanggalMulai = now()->format('Y-m-d\TH:i');
        $this->fromKegiatanRwId = request()->query('from_kegiatan');

        if ($this->fromKegiatanRwId !== null) {
            $this->prefillFromKegiatan($this->fromKegiatanRwId);
        }

        $this->budgetItems = [['item' => '', 'qty' => 1, 'satuan' => 'Pcs', 'harga_satuan' => 0, 'subtotal' => 0, 'keterangan' => '']];
    }

    public function addBudgetItem(): void
    {
        $this->budgetItems[] = ['item' => '', 'qty' => 1, 'satuan' => 'Pcs', 'harga_satuan' => 0, 'subtotal' => 0, 'keterangan' => ''];
    }

    public function removeBudgetItem(int $index): void
    {
        unset($this->budgetItems[$index]);
        $this->budgetItems = array_values($this->budgetItems);
    }

    #[Computed]
    public function totalBudget(): float
    {
        return collect($this->budgetItems)->sum(fn($item) =>
            (float)($item['qty'] ?? 1) * (float)($item['harga_satuan'] ?? 0)
        );
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
            ->when($this->lokasiDapil !== '', fn ($query) => $query->where('dapil', $this->lokasiDapil))
            ->select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
    }

    #[Computed]
    public function desaOptions(): Collection
    {
        return TargetWilayah::query()
            ->when($this->lokasiDapil !== '', fn ($query) => $query->where('dapil', $this->lokasiDapil))
            ->when($this->lokasiKecamatan !== '', fn ($query) => $query->where('kecamatan', $this->lokasiKecamatan))
            ->select('desa')
            ->distinct()
            ->orderBy('desa')
            ->pluck('desa');
    }

    #[Computed]
    public function bidangOptions(): \Illuminate\Support\Collection
    {
        return \App\Models\BidangDpd::query()->orderBy('urutan')->get();
    }

    #[Computed]
    public function orgLevelOptions(): array
    {
        $user = auth()->user();

        if ($user?->isDpra()) {
            return ['dpra' => 'DPRa (Desa/Kelurahan)'];
        }

        if ($user?->isDpc()) {
            return [
                'dpra' => 'DPRa (Desa/Kelurahan)',
                'dpc'  => 'DPC (Kecamatan)',
            ];
        }

        return \App\Models\Event::ORG_LEVEL_OPTIONS;
    }

    public function updatedLokasiDapil(): void
    {
        $this->lokasiKecamatan = '';
        $this->lokasiDesa = '';
    }

    public function updatedLokasiKecamatan(): void
    {
        $this->lokasiDesa = '';
    }

    public function simpanDraft()
    {
        $event = $this->persist(Event::STATUS_DRAFT, 'dpra');
        session()->flash('message', 'Draft event berhasil disimpan.');

        return redirect()->route('events.detail', $event);
    }

    public function saveAndSubmit()
    {
        $event = $this->persist(Event::STATUS_MENUNGGU, 'dpra');
        session()->flash('message', 'Event berhasil dibuat dan diajukan untuk approval.');

        return redirect()->route('events.detail', $event);
    }

    public function ajukanApproval()
    {
        return $this->saveAndSubmit();
    }

    public function render()
    {
        return view('livewire.events.create')
            ->layout('components.layouts.app-fullwidth', ['title' => 'Buat Program Baru']);
    }

    private function persist(string $status, string $levelApproval): Event
    {
        $validated = $this->validate([
            'orgLevel'    => ['required', 'in:dpd,dpc,dpra'],
            'bidangDpdId' => ['nullable', 'exists:bidang_dpds,id'],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'jenis' => ['required', 'string'],
            'tanggalMulai' => ['required', 'date'],
            'tanggalSelesai' => ['nullable', 'date', 'after_or_equal:tanggalMulai'],
            'lokasi' => ['required', 'string', 'max:255'],
            'lokasiDesa' => ['nullable', 'string', 'max:255'],
            'lokasiKecamatan' => ['nullable', 'string', 'max:255'],
            'lokasiDapil' => ['nullable', 'string', 'max:255'],
            'kapasitas' => ['nullable', 'integer', 'min:0'],
            'penyelenggara' => ['nullable', 'string', 'max:255'],
            'picNama' => ['nullable', 'string', 'max:255'],
            'picHp' => ['nullable', 'string', 'max:255'],
            'coverImage' => ['nullable', 'image', 'max:4096'],
            'speakers' => ['nullable', 'string'],
            'fundingSource' => ['nullable', 'string'],
            'targetProgram' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'budgetNotes' => ['nullable', 'string'],
        ]);

        $coverPath = $this->coverImage ? $this->coverImage->store('events', 'public') : null;

        $judul = $validated['judul'];
        $deskripsi = $validated['deskripsi'] !== '' ? $validated['deskripsi'] : null;
        $tanggalSelesai = $validated['tanggalSelesai'] !== '' ? $validated['tanggalSelesai'] : null;
        $lokasiDesa = $validated['lokasiDesa'] !== '' ? $validated['lokasiDesa'] : null;
        $lokasiKecamatan = $validated['lokasiKecamatan'] !== '' ? $validated['lokasiKecamatan'] : null;
        $lokasiDapil = $validated['lokasiDapil'] !== '' ? $validated['lokasiDapil'] : null;
        $penyelenggara = $validated['penyelenggara'] !== '' ? $validated['penyelenggara'] : null;
        $picNama = $validated['picNama'] !== '' ? $validated['picNama'] : null;
        $picHp = $validated['picHp'] !== '' ? $validated['picHp'] : null;
        $slugBase = Str::slug($judul) ?: 'event';

        $organizerDpraId = (int) (DB::table('dpra')->orderBy('id')->value('id') ?? 0);

        $user = auth()->user();
        $orgKecamatan = $user?->kecamatan ?? null;
        $orgDesa = $user?->desa ?? null;

        // DPD tidak perlu filter kecamatan/desa
        if ($this->orgLevel === 'dpd') {
            $orgKecamatan = null;
            $orgDesa = null;
        }

        // Draft tetap draft. Jika diajukan, DPD Bidang perlu approval, selebihnya langsung approved
        $finalStatus = $status;
        if ($status !== Event::STATUS_DRAFT) {
            $finalStatus = ($this->orgLevel === 'dpd' && !empty($this->bidangDpdId))
                ? Event::STATUS_MENUNGGU
                : Event::STATUS_DISETUJUI;
        }

        $event = new Event();
        $event->forceFill([
            'org_level'     => $this->orgLevel,
            'bidang_dpd_id' => $this->bidangDpdId ?: null,
            'org_kecamatan' => $orgKecamatan,
            'org_desa'      => $orgDesa,
            'status'        => $finalStatus,
            'judul' => $judul,
            'deskripsi' => $deskripsi,
            'jenis' => array_key_exists($validated['jenis'], Event::JENIS_EVENT) ? $validated['jenis'] : 'lainnya',
            'tanggal_mulai' => $validated['tanggalMulai'],
            'tanggal_selesai' => $tanggalSelesai,
            'lokasi' => $validated['lokasi'],
            'lokasi_desa' => $lokasiDesa,
            'lokasi_kecamatan' => $lokasiKecamatan,
            'lokasi_dapil' => $lokasiDapil,
            'kapasitas' => (int) ($validated['kapasitas'] ?? 0),
            'is_public' => false,
            'cover_image' => $coverPath,
            'level_approval' => $levelApproval,
            'penyelenggara' => $penyelenggara,
            'pic_nama' => $picNama,
            'pic_hp' => $picHp,
            'kegiatan_rw_id' => $this->fromKegiatanRwId,
            'created_by' => auth()->id(),
            'title' => $judul,
            'slug' => $slugBase . '-' . Str::lower(Str::random(6)),
            'description' => $deskripsi ?? $judul,
            'starts_at' => $validated['tanggalMulai'],
            'ends_at' => $tanggalSelesai,
            'location_name' => $validated['lokasi'],
            'location_address' => $validated['lokasi'],
            'visibility' => 'internal',
            'organizer_dpra_id' => $organizerDpraId,
            'max_participants' => (int) ($validated['kapasitas'] ?? 0),
            'speakers' => $this->speakers !== '' ? $this->speakers : null,
            'funding_source' => $this->fundingSource !== '' ? $this->fundingSource : null,
            'target_program' => $this->targetProgram !== '' ? $this->targetProgram : null,
            'requirements' => $this->requirements !== '' ? $this->requirements : null,
            'budget_notes' => $this->budgetNotes !== '' ? $this->budgetNotes : null,
        ]);
        $event->save();

        foreach ($this->budgetItems as $item) {
            if (!empty($item['item'])) {
                $event->budgetItems()->create([
                    'item' => $item['item'],
                    'kategori' => $item['kategori'] ?? null,
                    'qty' => (int) ($item['qty'] ?? 1),
                    'satuan' => $item['satuan'] ?: 'Pcs',
                    'harga_satuan' => (float) ($item['harga_satuan'] ?? 0),
                    'subtotal' => (int) ($item['qty'] ?? 1) * (float) ($item['harga_satuan'] ?? 0),
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }
        }

        if ($this->pesertaHadir > 0 || $this->evaluasiRingkasan !== '') {
            \App\Models\EventReport::create([
                'event_id' => $event->id,
                'ringkasan' => $this->evaluasiRingkasan ?: $judul,
                'peserta_hadir' => $this->pesertaHadir,
                'evaluasi' => $this->evaluasiCatatan ?: null,
                'realisasi_anggaran' => (float)$this->evaluasiRealisasiAnggaran,
                'created_by' => auth()->id(),
            ]);
        }

        collect(['dpra', 'dpc', 'dpd'])->each(function (string $level) use ($event): void {
            EventApproval::query()->create([
                'event_id' => $event->id,
                'level' => $level,
                'status' => 'pending',
            ]);
        });

        return $event;
    }

    private function prefillFromKegiatan(string $id): void
    {
        $kegiatan = KegiatanRw::query()->findOrFail($id);
        $mappedJenis = array_key_exists($kegiatan->jenis_kegiatan, Event::JENIS_EVENT)
            ? $kegiatan->jenis_kegiatan
            : 'lainnya';

        $this->jenis = $mappedJenis;
        $this->judul = 'Event ' . ($kegiatan->jenis_config['label'] ?? 'Kegiatan') . ' RW ' . $kegiatan->nomor_rw;
        $this->deskripsi = (string) ($kegiatan->catatan ?? '');
        $this->tanggalMulai = $kegiatan->tanggal_kegiatan?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i');
        $this->lokasi = trim($kegiatan->desa . ', ' . $kegiatan->kecamatan);
        $this->lokasiDesa = $kegiatan->desa;
        $this->lokasiKecamatan = $kegiatan->kecamatan;
        $this->lokasiDapil = $kegiatan->dapil;
        $this->picNama = (string) ($kegiatan->pelaksana ?? '');
        $this->penyelenggara = 'Sisir RW';
        $this->sourceKegiatan = [
            'desa' => $kegiatan->desa,
            'kecamatan' => $kegiatan->kecamatan,
            'rw' => $kegiatan->nomor_rw,
            'tanggal' => $kegiatan->tanggal_kegiatan?->format('d M Y H:i'),
        ];
    }
}
