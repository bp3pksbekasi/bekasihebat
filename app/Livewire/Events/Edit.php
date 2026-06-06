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
            ->layout('components.layouts.app-fullwidth', ['title' => 'Edit Event']);
    }

    private function persist(string $status, string $levelApproval): Event
    {
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
            'is_public' => $status === Event::STATUS_DISETUJUI ? $this->isPublic : false,
            'cover_image' => $coverPath,
            'status' => $status,
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
            'visibility' => ($status === Event::STATUS_DISETUJUI && $this->isPublic) ? 'public' : 'internal',
            'organizer_dpra_id' => $this->event->organizer_dpra_id ?: $organizerDpraId,
            'max_participants' => (int) ($validated['kapasitas'] ?? 0),
        ]);
        $this->event->save();

        return $this->event->fresh();
    }
}
