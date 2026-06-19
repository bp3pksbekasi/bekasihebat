<?php

declare(strict_types=1);

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\TargetWilayah;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public Event $event;

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

    public ?string $existingCover = null;

    public string $orgLevel = 'dpra';
    public string $bidangDpdId = '';

    public string $speakers = '';
    public string $fundingSource = '';
    public string $targetProgram = '';
    public string $requirements = '';
    public string $budgetNotes = '';
    public array $budgetItems = [];
    public int $pesertaHadir = 0;
    public array $dokFoto = [];
    public string $evaluasiRingkasan = '';
    public string $evaluasiCatatan = '';
    public string $evaluasiRealisasiAnggaran = '0';
    public string $evaluasiRating = '';

    public function mount(Event $event): void
    {
        abort_unless(in_array($event->status, [Event::STATUS_DRAFT, Event::STATUS_DITOLAK], true), 403);

        $this->event = $event->load('approvals');
        $this->judul = (string) $event->judul;
        $this->deskripsi = (string) ($event->deskripsi ?? '');
        $this->jenis = (string) $event->jenis;
        $this->tanggalMulai = $event->tanggal_mulai?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i');
        $this->tanggalSelesai = $event->tanggal_selesai?->format('Y-m-d\TH:i') ?? '';
        $this->lokasi = (string) $event->lokasi;
        $this->lokasiDesa = (string) ($event->lokasi_desa ?? '');
        $this->lokasiKecamatan = (string) ($event->lokasi_kecamatan ?? '');
        $this->lokasiDapil = (string) ($event->lokasi_dapil ?? '');
        $this->kapasitas = (int) $event->kapasitas;
        $this->isPublic = (bool) $event->is_public;
        $this->penyelenggara = (string) ($event->penyelenggara ?? '');
        $this->picNama = (string) ($event->pic_nama ?? '');
        $this->picHp = (string) ($event->pic_hp ?? '');
        $this->existingCover = $event->cover_image;
        $this->orgLevel = (string)($event->org_level ?? 'dpra');
        $this->bidangDpdId = (string)($event->bidang_dpd_id ?? '');

        $this->speakers = (string)($event->speakers ?? '');
        $this->fundingSource = (string)($event->funding_source ?? '');
        $this->targetProgram = (string)($event->target_program ?? '');
        $this->requirements = (string)($event->requirements ?? '');
        $this->budgetNotes = (string)($event->budget_notes ?? '');

        $this->budgetItems = $event->budgetItems->map(fn($b) => [
            'item' => $b->item,
            'kategori' => $b->kategori ?? '',
            'qty' => $b->qty,
            'satuan' => $b->satuan,
            'harga_satuan' => $b->harga_satuan,
            'keterangan' => $b->keterangan ?? '',
        ])->toArray();

        if ($event->report) {
            $this->pesertaHadir = $event->report->peserta_hadir;
            $this->evaluasiRingkasan = $event->report->ringkasan;
            $this->evaluasiCatatan = $event->report->evaluasi ?? '';
            $this->evaluasiRealisasiAnggaran = (string)$event->report->realisasi_anggaran;
        }
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

    public function addBudgetItem(): void
    {
        $this->budgetItems[] = [
            'item' => '',
            'kategori' => '',
            'qty' => 1,
            'satuan' => 'pcs',
            'harga_satuan' => 0,
            'keterangan' => '',
        ];
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

    public function updatedLokasiKecamatan(): void
    {
        $this->lokasiDesa = '';
    }

    public function simpanDraft()
    {
        $event = $this->persist(Event::STATUS_DRAFT, $this->event->level_approval);
        session()->flash('message', 'Draft event berhasil diperbarui.');

        return redirect()->route('events.detail', $event);
    }

    public function saveAndSubmit()
    {
        $event = $this->persist(Event::STATUS_MENUNGGU, 'dpra');
        $event->approvals()->update([
            'status' => 'pending',
            'approver_id' => null,
            'catatan' => null,
            'decided_at' => null,
        ]);
        session()->flash('message', 'Event berhasil direvisi dan diajukan ulang.');

        return redirect()->route('events.detail', $event->fresh('approvals'));
    }

    public function render()
    {
        return view('livewire.events.edit')
            ->layout('components.layouts.app-fullwidth', ['title' => 'Edit Program']);
    }

    private function persist(string $status, string $levelApproval): Event
    {
        $finalStatus = $status;
        if ($status !== Event::STATUS_DRAFT) {
            $finalStatus = ($this->orgLevel === 'dpd' && !empty($this->bidangDpdId))
                ? Event::STATUS_MENUNGGU
                : Event::STATUS_DISETUJUI;
        }

        $validated = $this->validate([
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
        ]);

        $coverPath = $this->existingCover;

        if ($this->coverImage) {
            if ($this->existingCover !== null) {
                Storage::disk('public')->delete($this->existingCover);
            }

            $coverPath = $this->coverImage->store('events', 'public');
        }

        $judul = $validated['judul'];
        $deskripsi = $validated['deskripsi'] !== '' ? $validated['deskripsi'] : null;
        $tanggalSelesai = $validated['tanggalSelesai'] !== '' ? $validated['tanggalSelesai'] : null;
        $lokasiDesa = $validated['lokasiDesa'] !== '' ? $validated['lokasiDesa'] : null;
        $lokasiKecamatan = $validated['lokasiKecamatan'] !== '' ? $validated['lokasiKecamatan'] : null;
        $lokasiDapil = $validated['lokasiDapil'] !== '' ? $validated['lokasiDapil'] : null;
        $penyelenggara = $validated['penyelenggara'] !== '' ? $validated['penyelenggara'] : null;
        $picNama = $validated['picNama'] !== '' ? $validated['picNama'] : null;
        $picHp = $validated['picHp'] !== '' ? $validated['picHp'] : null;

        $organizerDpraId = (int) (DB::table('dpra')->orderBy('id')->value('id') ?? 0);

        $this->event->forceFill([
            'org_level'     => $this->orgLevel,
            'bidang_dpd_id' => $this->bidangDpdId ?: null,
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
            'is_public' => $finalStatus === Event::STATUS_DISETUJUI ? $this->isPublic : false,
            'cover_image' => $coverPath,
            'status' => $finalStatus,
            'level_approval' => $levelApproval,
            'penyelenggara' => $penyelenggara,
            'pic_nama' => $picNama,
            'pic_hp' => $picHp,
            'title' => $judul,
            'slug' => ($this->event->slug && $this->event->slug !== '') ? $this->event->slug : ((Str::slug($judul) ?: 'event') . '-' . Str::lower(Str::random(6))),
            'description' => $deskripsi ?? $judul,
            'starts_at' => $validated['tanggalMulai'],
            'ends_at' => $tanggalSelesai,
            'location_name' => $validated['lokasi'],
            'location_address' => $validated['lokasi'],
            'visibility' => ($finalStatus === Event::STATUS_DISETUJUI && $this->isPublic) ? 'public' : 'internal',
            'organizer_dpra_id' => $this->event->organizer_dpra_id ?: $organizerDpraId,
            'max_participants' => (int) ($validated['kapasitas'] ?? 0),
            'speakers' => $this->speakers !== '' ? $this->speakers : null,
            'funding_source' => $this->fundingSource !== '' ? $this->fundingSource : null,
            'target_program' => $this->targetProgram !== '' ? $this->targetProgram : null,
            'requirements' => $this->requirements !== '' ? $this->requirements : null,
            'budget_notes' => $this->budgetNotes !== '' ? $this->budgetNotes : null,
        ]);
        $this->event->save();

        $this->event->budgetItems()->delete();
        if (!empty($this->budgetItems)) {
            foreach ($this->budgetItems as $item) {
                if (empty($item['item'])) continue;
                $qty = (int)($item['qty'] ?? 1);
                $harga = (float)str_replace(['.', ','], ['', '.'], $item['harga_satuan'] ?? '0');
                \App\Models\EventBudgetItem::create([
                    'event_id' => $this->event->id,
                    'item' => $item['item'],
                    'kategori' => $item['kategori'] ?? null,
                    'qty' => $qty,
                    'satuan' => $item['satuan'] ?? 'pcs',
                    'harga_satuan' => $harga,
                    'subtotal' => $qty * $harga,
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }
        }

        if ($this->pesertaHadir > 0 || $this->evaluasiRingkasan !== '') {
            \App\Models\EventReport::updateOrCreate(
                ['event_id' => $this->event->id],
                [
                    'ringkasan' => $this->evaluasiRingkasan ?: $judul,
                    'peserta_hadir' => $this->pesertaHadir,
                    'evaluasi' => $this->evaluasiCatatan ?: null,
                    'realisasi_anggaran' => (float)$this->evaluasiRealisasiAnggaran,
                    'created_by' => auth()->id() ?? 1,
                ]
            );
        }

        return $this->event->fresh();
    }
}
