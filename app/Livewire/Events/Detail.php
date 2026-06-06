<?php

declare(strict_types=1);

namespace App\Livewire\Events;

use App\Models\Aspirasi;
use App\Models\Event;
use App\Models\EventApproval;
use App\Models\EventBudgetItem;
use App\Models\EventPeserta;
use App\Models\EventReport;
use App\Models\Kader;
use App\Models\KontakWarga;
use App\Models\TargetWilayah;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithFileUploads;

class Detail extends Component
{
    use WithFileUploads;

    public Event $event;

    public array $approvalNotes = [
        'dpra' => '',
        'dpc' => '',
        'dpd' => '',
    ];

    public ?string $budgetEditId = null;

    public string $budgetItem = '';

    public string $budgetKategori = '';

    public int $budgetQty = 1;

    public string $budgetSatuan = 'pcs';

    public string $budgetHargaSatuan = '0';

    public string $budgetKeterangan = '';

    public string $reportRingkasan = '';

    public int $reportPesertaHadir = 0;

    public string $reportEvaluasi = '';

    public string $reportTindakLanjut = '';

    public string $reportRealisasiAnggaran = '0';

    public array $reportExistingFoto = [];

    public array $reportFoto = [];

    public bool $showPesertaForm = false;

    public string $pesertaTab = 'bulk';

    public string $bulkPesertaText = '';

    public array $bulkPesertaParsed = [];

    public string $bulkDefaultDapil = '';

    public string $bulkDefaultKecamatan = '';

    public string $bulkDefaultDesa = '';

    public string $spNama = '';

    public string $spHp = '';

    public string $spRw = '';

    public string $spRt = '';

    public string $spDesaId = '';

    public string $spAspirasi = '';

    public function mount(Event $event): void
    {
        $this->event = $event->load([
            'creator',
            'kegiatanRw',
            'approvals.approver',
            'budgetItems',
            'report.creator',
            'registrations.user',
        ]);

        if ($this->event->report) {
            $this->fillReportForm();
        }

        $this->initializePesertaDefaults();
    }

    public function submitForApproval(): void
    {
        if (! in_array($this->event->status, [Event::STATUS_DRAFT, Event::STATUS_DITOLAK], true)) {
            return;
        }

        $this->ensureApprovalRows();

        $this->event->approvals()->update([
            'status' => 'pending',
            'approver_id' => null,
            'catatan' => null,
            'decided_at' => null,
        ]);

        $this->event->update([
            'status' => Event::STATUS_MENUNGGU,
            'level_approval' => 'dpra',
            'is_public' => false,
        ]);

        $this->refreshEvent();
        session()->flash('message', 'Event berhasil diajukan untuk approval.');
    }

    public function approve(string $level): void
    {
        if (! $this->canApproveLevel($level)) {
            abort(403);
        }

        if ($this->event->status !== Event::STATUS_MENUNGGU || $this->event->level_approval !== $level) {
            return;
        }

        $approval = $this->event->approvals->firstWhere('level', $level);

        if (! $approval instanceof EventApproval) {
            return;
        }

        $approval->update([
            'status' => 'approved',
            'approver_id' => auth()->id(),
            'catatan' => $this->approvalNotes[$level] !== '' ? $this->approvalNotes[$level] : null,
            'decided_at' => now(),
        ]);

        $nextLevel = match ($level) {
            'dpra' => 'dpc',
            'dpc' => 'dpd',
            default => 'selesai',
        };

        $this->event->update([
            'status' => $nextLevel === 'selesai' ? Event::STATUS_DISETUJUI : Event::STATUS_MENUNGGU,
            'level_approval' => $nextLevel,
        ]);

        $this->approvalNotes[$level] = '';
        $this->refreshEvent();
        session()->flash('message', 'Approval event berhasil disimpan.');
    }

    public function reject(string $level): void
    {
        if (! $this->canApproveLevel($level)) {
            abort(403);
        }

        if ($this->event->status !== Event::STATUS_MENUNGGU || $this->event->level_approval !== $level) {
            return;
        }

        $approval = $this->event->approvals->firstWhere('level', $level);

        if (! $approval instanceof EventApproval) {
            return;
        }

        $approval->update([
            'status' => 'rejected',
            'approver_id' => auth()->id(),
            'catatan' => $this->approvalNotes[$level] !== '' ? $this->approvalNotes[$level] : 'Ditolak tanpa catatan.',
            'decided_at' => now(),
        ]);

        $this->event->update([
            'status' => Event::STATUS_DITOLAK,
            'level_approval' => $level,
            'is_public' => false,
        ]);

        $this->approvalNotes[$level] = '';
        $this->refreshEvent();
        session()->flash('message', 'Event ditolak.');
    }

    public function setEventStatus(string $status): void
    {
        if (! in_array($status, [Event::STATUS_BERLANGSUNG, Event::STATUS_SELESAI, Event::STATUS_DIBATALKAN], true)) {
            return;
        }

        $canUpdate = match ($status) {
            Event::STATUS_BERLANGSUNG => $this->event->status === Event::STATUS_DISETUJUI,
            Event::STATUS_SELESAI => in_array($this->event->status, [Event::STATUS_DISETUJUI, Event::STATUS_BERLANGSUNG], true),
            Event::STATUS_DIBATALKAN => $this->event->status !== Event::STATUS_SELESAI,
            default => false,
        };

        if (! $canUpdate) {
            return;
        }

        $this->event->update(['status' => $status]);
        $this->refreshEvent();
        session()->flash('message', 'Status event berhasil diperbarui.');
    }

    public function togglePublic(): void
    {
        if ($this->event->status !== Event::STATUS_DISETUJUI) {
            session()->flash('message', 'Event hanya bisa ditampilkan publik jika sudah disetujui.');

            return;
        }

        $this->event->update(['is_public' => ! $this->event->is_public]);
        $this->refreshEvent();
        session()->flash('message', 'Visibilitas event berhasil diperbarui.');
    }

    public function editBudgetItem(string $id): void
    {
        $item = $this->event->budgetItems->firstWhere('id', $id);

        if (! $item instanceof EventBudgetItem) {
            return;
        }

        $this->budgetEditId = $item->id;
        $this->budgetItem = $item->item;
        $this->budgetKategori = (string) ($item->kategori ?? '');
        $this->budgetQty = (int) $item->qty;
        $this->budgetSatuan = $item->satuan;
        $this->budgetHargaSatuan = (string) $item->harga_satuan;
        $this->budgetKeterangan = (string) ($item->keterangan ?? '');
    }

    public function saveBudgetItem(): void
    {
        $validated = $this->validate([
            'budgetItem' => ['required', 'string', 'max:255'],
            'budgetKategori' => ['nullable', 'string', 'max:255'],
            'budgetQty' => ['required', 'integer', 'min:1'],
            'budgetSatuan' => ['required', 'string', 'max:50'],
            'budgetHargaSatuan' => ['required', 'numeric', 'min:0'],
            'budgetKeterangan' => ['nullable', 'string'],
        ]);

        $subtotal = (float) $validated['budgetQty'] * (float) $validated['budgetHargaSatuan'];

        EventBudgetItem::query()->updateOrCreate(
            ['id' => $this->budgetEditId],
            [
                'event_id' => $this->event->id,
                'item' => $validated['budgetItem'],
                'kategori' => $validated['budgetKategori'] !== '' ? $validated['budgetKategori'] : null,
                'qty' => (int) $validated['budgetQty'],
                'satuan' => $validated['budgetSatuan'],
                'harga_satuan' => (float) $validated['budgetHargaSatuan'],
                'subtotal' => $subtotal,
                'keterangan' => $validated['budgetKeterangan'] !== '' ? $validated['budgetKeterangan'] : null,
            ]
        );

        $this->resetBudgetForm();
        $this->refreshEvent();
        session()->flash('message', 'Item anggaran berhasil disimpan.');
    }

    public function removeBudgetItem(string $id): void
    {
        EventBudgetItem::query()->where('id', $id)->where('event_id', $this->event->id)->delete();
        $this->refreshEvent();
        session()->flash('message', 'Item anggaran berhasil dihapus.');
    }

    public function saveReport(): void
    {
        if ($this->event->status !== Event::STATUS_SELESAI) {
            return;
        }

        $validated = $this->validate([
            'reportRingkasan' => ['required', 'string'],
            'reportPesertaHadir' => ['required', 'integer', 'min:0'],
            'reportEvaluasi' => ['nullable', 'string'],
            'reportTindakLanjut' => ['nullable', 'string'],
            'reportRealisasiAnggaran' => ['required', 'numeric', 'min:0'],
            'reportFoto' => ['nullable', 'array', 'max:8'],
            'reportFoto.*' => ['image', 'max:4096'],
        ]);

        $foto = $this->reportExistingFoto;

        foreach ($this->reportFoto as $upload) {
            $foto[] = $upload->store('event-reports', 'public');
        }

        EventReport::query()->updateOrCreate(
            ['event_id' => $this->event->id],
            [
                'ringkasan' => $validated['reportRingkasan'],
                'peserta_hadir' => (int) $validated['reportPesertaHadir'],
                'evaluasi' => $validated['reportEvaluasi'] !== '' ? $validated['reportEvaluasi'] : null,
                'tindak_lanjut' => $validated['reportTindakLanjut'] !== '' ? $validated['reportTindakLanjut'] : null,
                'foto' => $foto !== [] ? array_values($foto) : null,
                'realisasi_anggaran' => (float) $validated['reportRealisasiAnggaran'],
                'created_by' => auth()->id(),
            ]
        );

        $this->reportFoto = [];
        $this->refreshEvent();
        $this->fillReportForm();
        session()->flash('message', 'Laporan kegiatan berhasil disimpan.');
    }

    public function updatedBulkDefaultDapil(): void
    {
        $this->bulkDefaultKecamatan = '';
        $this->bulkDefaultDesa = '';
    }

    public function updatedBulkDefaultKecamatan(): void
    {
        $this->bulkDefaultDesa = '';
    }

    public function updatedBulkPesertaText(): void
    {
        $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $this->bulkPesertaText) ?: []));
        $this->bulkPesertaParsed = [];

        foreach ($lines as $line) {
            $parts = preg_split('/[,\t]+/', $line) ?: [];
            $nama = trim((string) ($parts[0] ?? ''));
            $rw = '';
            $hp = '';
            $aspirasi = '';

            foreach (array_slice($parts, 1) as $part) {
                $segment = trim((string) $part);
                $normalizedPhone = $this->normalizePhone($segment);

                if ($rw === '' && preg_match('/^(?:RW\s*)?(\d{1,3})$/i', $segment, $match)) {
                    $rw = str_pad($match[1], 3, '0', STR_PAD_LEFT);
                    continue;
                }

                if ($hp === '' && $normalizedPhone !== '') {
                    $hp = $normalizedPhone;
                    continue;
                }

                if ($aspirasi === '' && $normalizedPhone === '' && ! preg_match('/^(?:RW\s*)?(\d{1,3})$/i', $segment)) {
                    $aspirasi = $segment;
                }
            }

            if ($nama !== '') {
                $this->bulkPesertaParsed[] = [
                    'nama' => $nama,
                    'rw' => $rw !== '' ? $rw : null,
                    'hp' => $hp !== '' ? $hp : null,
                    'aspirasi' => $aspirasi !== '' ? $aspirasi : null,
                ];
            }
        }
    }

    public function simpanBulkPeserta(): void
    {
        if ($this->bulkPesertaParsed === []) {
            return;
        }

        $event = $this->event;
        $targetWilayah = $this->resolveTargetWilayahById($this->bulkDefaultDesa) ?? $this->resolveEventTargetWilayah();
        $count = 0;
        $countSapa = 0;
        $countAspirasi = 0;

        foreach ($this->bulkPesertaParsed as $item) {
            $nama = trim((string) ($item['nama'] ?? ''));
            $hp = $this->normalizePhone((string) ($item['hp'] ?? ''));
            $rw = $this->normalizeRw((string) ($item['rw'] ?? ''));
            $aspirasiText = trim((string) ($item['aspirasi'] ?? ''));

            if ($nama === '') {
                continue;
            }

            if ($hp !== '' && EventPeserta::query()->where('event_id', $event->id)->where('no_hp', $hp)->exists()) {
                continue;
            }

            $peserta = EventPeserta::query()->create([
                'event_id' => $event->id,
                'nama' => $nama,
                'no_hp' => $hp !== '' ? $hp : null,
                'no_wa' => $hp !== '' ? $hp : null,
                'dapil' => $targetWilayah?->dapil ?? ($this->bulkDefaultDapil !== '' ? $this->bulkDefaultDapil : ($event->lokasi_dapil ?: null)),
                'kecamatan' => $targetWilayah?->kecamatan ?? ($this->bulkDefaultKecamatan !== '' ? $this->bulkDefaultKecamatan : ($event->lokasi_kecamatan ?: null)),
                'desa' => $targetWilayah?->desa ?? ($event->lokasi_desa ?: null),
                'nomor_rw' => $rw !== '' ? $rw : null,
                'target_wilayah_id' => $targetWilayah?->id,
                'kader_id' => $this->resolveKaderId($hp),
                'metode' => 'bulk',
                'aspirasi' => $aspirasiText !== '' ? $aspirasiText : null,
                'created_by' => auth()->id(),
            ]);
            $count++;

            if ($this->syncPesertaToSapaWarga($peserta, $event)) {
                $countSapa++;
            }

            if ($this->createAspirasiFromParticipant($peserta, $aspirasiText)) {
                $countAspirasi++;
            }
        }

        $this->bulkPesertaText = '';
        $this->bulkPesertaParsed = [];
        $this->showPesertaForm = false;

        $this->syncEventParticipantCount();
        $this->refreshEvent();

        session()->flash('message', "{$count} peserta dicatat. {$countSapa} baru masuk ke Sapa Warga. {$countAspirasi} aspirasi dibuat.");
    }

    public function simpanSinglePeserta(): void
    {
        $validated = $this->validate([
            'spNama' => ['required', 'string', 'max:255'],
            'spHp' => ['nullable', 'string', 'max:50'],
            'spRw' => ['nullable', 'string', 'max:10'],
            'spRt' => ['nullable', 'string', 'max:10'],
            'spDesaId' => ['nullable', 'string'],
            'spAspirasi' => ['nullable', 'string'],
        ]);

        $event = $this->event;
        $hp = $this->normalizePhone($validated['spHp']);

        if ($hp !== '' && EventPeserta::query()->where('event_id', $event->id)->where('no_hp', $hp)->exists()) {
            session()->flash('message', 'Nomor HP peserta sudah tercatat pada event ini.');

            return;
        }

        $targetWilayah = $this->resolveTargetWilayahById($validated['spDesaId']) ?? $this->resolveEventTargetWilayah();
        $rw = $this->normalizeRw($validated['spRw']);
        $rt = $this->normalizeRw($validated['spRt']);

        $peserta = EventPeserta::query()->create([
            'event_id' => $event->id,
            'nama' => trim($validated['spNama']),
            'no_hp' => $hp !== '' ? $hp : null,
            'no_wa' => $hp !== '' ? $hp : null,
            'nomor_rw' => $rw !== '' ? $rw : null,
            'nomor_rt' => $rt !== '' ? $rt : null,
            'dapil' => $targetWilayah?->dapil ?? ($event->lokasi_dapil ?: null),
            'kecamatan' => $targetWilayah?->kecamatan ?? ($event->lokasi_kecamatan ?: null),
            'desa' => $targetWilayah?->desa ?? ($event->lokasi_desa ?: null),
            'target_wilayah_id' => $targetWilayah?->id,
            'kader_id' => $this->resolveKaderId($hp),
            'metode' => 'manual',
            'aspirasi' => trim($validated['spAspirasi']) !== '' ? trim($validated['spAspirasi']) : null,
            'created_by' => auth()->id(),
        ]);

        $this->syncPesertaToSapaWarga($peserta, $event);
        $aspirasiCreated = $this->createAspirasiFromParticipant($peserta, $validated['spAspirasi']);
        $this->syncEventParticipantCount();
        $this->refreshEvent();
        $this->resetSinglePesertaForm();

        session()->flash('message', $aspirasiCreated ? 'Peserta dicatat dan aspirasi berhasil dibuat.' : 'Peserta dicatat.');
    }

    public function hapusPeserta(string $id): void
    {
        $peserta = EventPeserta::query()
            ->where('event_id', $this->event->id)
            ->findOrFail($id);

        $peserta->delete();

        $this->syncEventParticipantCount();
        $this->refreshEvent();
        session()->flash('message', 'Peserta berhasil dihapus.');
    }

    public function syncSemuaKeSapaWarga(): void
    {
        $count = 0;

        $unsynced = EventPeserta::query()
            ->where('event_id', $this->event->id)
            ->where('synced_sapa_warga', false)
            ->whereNotNull('nomor_rw')
            ->whereNotNull('no_hp')
            ->whereNotNull('target_wilayah_id')
            ->get();

        foreach ($unsynced as $peserta) {
            if ($this->syncPesertaToSapaWarga($peserta, $this->event)) {
                $count++;
            }
        }

        $this->refreshEvent();
        session()->flash('message', "{$count} peserta baru disync ke Sapa Warga.");
    }

    public function getPesertaListProperty(): Collection
    {
        return EventPeserta::query()
            ->where('event_id', $this->event->id)
            ->orderByRaw("CASE WHEN nomor_rw IS NULL OR nomor_rw = '' THEN 1 ELSE 0 END")
            ->orderBy('nomor_rw')
            ->orderBy('nama')
            ->get();
    }

    public function getPesertaSummaryProperty(): array
    {
        $list = $this->pesertaList;

        return [
            'total' => $list->count(),
            'synced' => $list->where('synced_sapa_warga', true)->count(),
            'unsynced' => $list->where('synced_sapa_warga', false)->count(),
            'per_rw' => $list->groupBy(fn (EventPeserta $row) => $row->nomor_rw ?: '?')->map->count()->sortKeys(),
            'unik_rw' => $list->pluck('nomor_rw')->filter()->unique()->count(),
        ];
    }

    public function getBulkDapilOptionsProperty(): Collection
    {
        return TargetWilayah::query()
            ->select('dapil')
            ->distinct()
            ->orderBy('dapil')
            ->pluck('dapil');
    }

    public function getBulkKecamatanOptionsProperty(): Collection
    {
        return TargetWilayah::query()
            ->when($this->bulkDefaultDapil !== '', fn ($query) => $query->where('dapil', $this->bulkDefaultDapil))
            ->select('kecamatan')
            ->distinct()
            ->orderBy('kecamatan')
            ->pluck('kecamatan');
    }

    public function getBulkDesaOptionsProperty(): Collection
    {
        return TargetWilayah::query()
            ->when($this->bulkDefaultDapil !== '', fn ($query) => $query->where('dapil', $this->bulkDefaultDapil))
            ->when($this->bulkDefaultKecamatan !== '', fn ($query) => $query->where('kecamatan', $this->bulkDefaultKecamatan))
            ->orderBy('desa')
            ->get(['id', 'desa', 'dapil', 'kecamatan']);
    }

    public function getSingleDesaOptionsProperty(): Collection
    {
        return TargetWilayah::query()
            ->when($this->event->lokasi_dapil, fn ($query, $dapil) => $query->where('dapil', $dapil))
            ->when($this->event->lokasi_kecamatan, fn ($query, $kecamatan) => $query->where('kecamatan', $kecamatan))
            ->orderBy('desa')
            ->get(['id', 'desa', 'dapil', 'kecamatan']);
    }

    public function render()
    {
        return view('livewire.events.detail')
            ->layout('components.layouts.app-fullwidth', ['title' => $this->event->judul]);
    }

    public function canApproveLevel(string $level): bool
    {
        $user = auth()->user();

        if ($user === null) {
            return false;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        return match ($level) {
            'dpd' => $user->hasRole('dpd'),
            'dpc' => $user->hasRole('dapil'),
            'dpra' => $user->hasRole('kecamatan'),
            default => false,
        };
    }

    private function resetBudgetForm(): void
    {
        $this->budgetEditId = null;
        $this->budgetItem = '';
        $this->budgetKategori = '';
        $this->budgetQty = 1;
        $this->budgetSatuan = 'pcs';
        $this->budgetHargaSatuan = '0';
        $this->budgetKeterangan = '';
    }

    private function ensureApprovalRows(): void
    {
        foreach (['dpra', 'dpc', 'dpd'] as $level) {
            EventApproval::query()->firstOrCreate(
                ['event_id' => $this->event->id, 'level' => $level],
                ['status' => 'pending']
            );
        }
    }

    private function refreshEvent(): void
    {
        $this->event = $this->event->fresh([
            'creator',
            'kegiatanRw',
            'approvals.approver',
            'budgetItems',
            'report.creator',
            'registrations.user',
        ]);
    }

    private function fillReportForm(): void
    {
        $report = $this->event->report;

        if (! $report instanceof EventReport) {
            return;
        }

        $this->reportRingkasan = $report->ringkasan;
        $this->reportPesertaHadir = (int) $report->peserta_hadir;
        $this->reportEvaluasi = (string) ($report->evaluasi ?? '');
        $this->reportTindakLanjut = (string) ($report->tindak_lanjut ?? '');
        $this->reportRealisasiAnggaran = (string) $report->realisasi_anggaran;
        $this->reportExistingFoto = $report->foto ?? [];
    }

    private function initializePesertaDefaults(): void
    {
        $targetWilayah = $this->resolveEventTargetWilayah();

        $this->bulkDefaultDapil = (string) ($targetWilayah?->dapil ?? ($this->event->lokasi_dapil ?? ''));
        $this->bulkDefaultKecamatan = (string) ($targetWilayah?->kecamatan ?? ($this->event->lokasi_kecamatan ?? ''));
        $this->bulkDefaultDesa = (string) ($targetWilayah?->id ?? '');
        $this->spDesaId = (string) ($targetWilayah?->id ?? '');

        if (! $this->event->report) {
            $this->reportPesertaHadir = $this->event->peserta_count;
        }
    }

    private function resolveEventTargetWilayah(): ?TargetWilayah
    {
        if ($this->event->kegiatanRw?->target_wilayah_id) {
            return TargetWilayah::query()->find($this->event->kegiatanRw->target_wilayah_id);
        }

        if (! $this->event->lokasi_dapil && ! $this->event->lokasi_kecamatan && ! $this->event->lokasi_desa) {
            return null;
        }

        return TargetWilayah::query()
            ->when($this->event->lokasi_dapil, fn ($query, $dapil) => $query->where('dapil', $dapil))
            ->when($this->event->lokasi_kecamatan, fn ($query, $kecamatan) => $query->where('kecamatan', $kecamatan))
            ->when($this->event->lokasi_desa, fn ($query, $desa) => $query->where('desa', $desa))
            ->orderBy('desa')
            ->first();
    }

    private function resolveTargetWilayahById(?string $id): ?TargetWilayah
    {
        if (! $id) {
            return null;
        }

        return TargetWilayah::query()->find($id);
    }

    private function resolveKaderId(string $phone): ?string
    {
        if ($phone === '') {
            return null;
        }

        $kader = Kader::query()
            ->where('no_hp', $phone)
            ->orWhere('no_wa', $phone)
            ->first();

        return $kader?->id;
    }

    private function syncPesertaToSapaWarga(EventPeserta $peserta, Event $event): bool
    {
        if (! $peserta->target_wilayah_id || ! $peserta->nomor_rw || ! $peserta->no_hp) {
            return false;
        }

        $existing = KontakWarga::query()
            ->where('target_wilayah_id', $peserta->target_wilayah_id)
            ->where('nomor_rw', $peserta->nomor_rw)
            ->where('no_wa', $peserta->no_hp)
            ->first();

        if ($existing instanceof KontakWarga) {
            $peserta->update([
                'synced_sapa_warga' => true,
                'kontak_warga_id' => $existing->id,
            ]);

            return false;
        }

        $kontak = KontakWarga::query()->create([
            'target_wilayah_id' => $peserta->target_wilayah_id,
            'dapil' => $peserta->dapil,
            'kecamatan' => $peserta->kecamatan,
            'desa' => $peserta->desa,
            'nomor_rw' => $peserta->nomor_rw,
            'nama' => $peserta->nama,
            'no_wa' => $peserta->no_hp,
            'no_hp' => $peserta->no_hp,
            'rt' => $peserta->nomor_rt,
            'alamat' => $peserta->alamat,
            'sumber' => 'event',
            'catatan' => 'Dari event: '.$event->judul,
            'created_by' => auth()->id(),
        ]);

        $peserta->update([
            'synced_sapa_warga' => true,
            'kontak_warga_id' => $kontak->id,
        ]);

        return true;
    }

    private function syncEventParticipantCount(): void
    {
        $count = $this->event->pesertas()->count();
        $this->reportPesertaHadir = $count;

        if ($this->event->report) {
            $this->event->report()->update(['peserta_hadir' => $count]);
        }
    }

    private function resetSinglePesertaForm(): void
    {
        $this->spNama = '';
        $this->spHp = '';
        $this->spRw = '';
        $this->spRt = '';
        $this->spDesaId = (string) ($this->resolveEventTargetWilayah()?->id ?? '');
        $this->spAspirasi = '';
        $this->showPesertaForm = false;
    }

    private function normalizePhone(string $value): string
    {
        $phone = preg_replace('/[\s\-]/', '', trim($value)) ?? '';

        if ($phone === '' || ! preg_match('/^0[87]\d{7,15}$/', $phone)) {
            return '';
        }

        return $phone;
    }

    private function normalizeRw(string $value): string
    {
        $clean = trim(str_ireplace('RW', '', $value));

        if ($clean === '' || ! ctype_digit($clean)) {
            return '';
        }

        return str_pad($clean, 3, '0', STR_PAD_LEFT);
    }

    private function createAspirasiFromParticipant(EventPeserta $peserta, string $aspirasiText): bool
    {
        $aspirasiText = trim($aspirasiText);

        if ($aspirasiText === '' || ! Schema::hasTable('aspirasis')) {
            return false;
        }

        if (Aspirasi::query()
            ->where('sumber', 'event')
            ->where('sumber_id', $this->event->id)
            ->where('nama_pelapor', $peserta->nama)
            ->where('deskripsi', $aspirasiText)
            ->exists()) {
            return false;
        }

        $aspirasi = Aspirasi::query()->create([
            'judul' => 'Aspirasi peserta '.$this->event->judul,
            'deskripsi' => $aspirasiText,
            'kategori' => 'sosial',
            'urgensi' => 'sedang',
            'dapil' => $peserta->dapil ?: ($this->event->lokasi_dapil ?? '-'),
            'kecamatan' => $peserta->kecamatan,
            'desa' => $peserta->desa,
            'nomor_rw' => $peserta->nomor_rw,
            'target_wilayah_id' => $peserta->target_wilayah_id,
            'nama_pelapor' => $peserta->nama,
            'hp_pelapor' => $peserta->no_hp,
            'sumber' => 'event',
            'sumber_id' => (string) $this->event->id,
            'created_by' => auth()->id(),
        ]);

        $aspirasi->update(['draft_pokir' => $aspirasi->generateDraftPokir()]);

        return true;
    }
}
