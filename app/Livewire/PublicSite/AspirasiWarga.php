<?php

declare(strict_types=1);

namespace App\Livewire\PublicSite;

use App\Models\Aspirasi;
use App\Models\TargetWilayah;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.public')]
#[Title('Aspirasi Warga - Bekasi Hebat')]
class AspirasiWarga extends Component
{
    public string $publicNama = '';

    public string $publicHp = '';

    public string $publicDapil = '';

    public string $publicDesaId = '';

    public string $publicRw = '';

    public string $publicKategori = 'infrastruktur';

    public string $publicDeskripsi = '';

    public function updatedPublicDapil(): void
    {
        $this->publicDesaId = '';
    }

    public function getTotalAspirasiProperty(): int
    {
        if (! Schema::hasTable('aspirasis')) {
            return 0;
        }

        return Aspirasi::query()->count();
    }

    public function getInputSipdProperty(): int
    {
        if (! Schema::hasTable('aspirasis')) {
            return 0;
        }

        return Aspirasi::query()->whereIn('status', ['input_sipd', 'verifikasi_bappeda', 'dianggarkan', 'terealisasi'])->count();
    }

    public function getDianggarkanProperty(): int
    {
        if (! Schema::hasTable('aspirasis')) {
            return 0;
        }

        return Aspirasi::query()->whereIn('status', ['dianggarkan', 'terealisasi'])->count();
    }

    public function getTerealisasiProperty(): int
    {
        if (! Schema::hasTable('aspirasis')) {
            return 0;
        }

        return Aspirasi::query()->where('status', 'terealisasi')->count();
    }

    public function getSuccessStoriesProperty()
    {
        if (! Schema::hasTable('aspirasis')) {
            return collect();
        }

        return Aspirasi::query()
            ->with('assignedDewan')
            ->where('status', 'terealisasi')
            ->latest('realisasi_at')
            ->limit(5)
            ->get();
    }

    public function getPerKategoriProperty()
    {
        if (! Schema::hasTable('aspirasis')) {
            return collect(Aspirasi::KATEGORI_OPTIONS)
                ->map(fn (string $label, string $key): array => ['key' => $key, 'label' => $label, 'count' => 0])
                ->values();
        }

        return collect(Aspirasi::KATEGORI_OPTIONS)
            ->map(fn (string $label, string $key): array => [
                'key' => $key,
                'label' => $label,
                'count' => (int) Aspirasi::query()->where('kategori', $key)->count(),
            ])
            ->values();
    }

    public function getDapilOptionsProperty()
    {
        return TargetWilayah::query()
            ->select('dapil')
            ->distinct()
            ->orderBy('dapil')
            ->pluck('dapil');
    }

    public function getDesaOptionsProperty()
    {
        return TargetWilayah::query()
            ->when($this->publicDapil !== '', fn ($query) => $query->where('dapil', $this->publicDapil))
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get(['id', 'desa', 'kecamatan', 'dapil']);
    }

    public function submitAspirasi(): void
    {
        if (! Schema::hasTable('aspirasis')) {
            session()->flash('message', 'Kanal aspirasi sedang disiapkan. Silakan coba lagi setelah migrasi selesai.');

            return;
        }

        $validated = $this->validate([
            'publicNama' => ['required', 'string', 'max:255'],
            'publicHp' => ['nullable', 'string', 'max:50'],
            'publicDapil' => ['required', 'string', 'max:50'],
            'publicDesaId' => ['nullable', 'string'],
            'publicRw' => ['nullable', 'string', 'max:10'],
            'publicKategori' => ['required', 'string'],
            'publicDeskripsi' => ['required', 'string', 'min:10'],
        ]);

        $targetWilayah = $validated['publicDesaId'] !== ''
            ? TargetWilayah::query()->find($validated['publicDesaId'])
            : null;

        $aspirasi = Aspirasi::query()->create([
            'judul' => $this->generateJudul($validated['publicKategori'], $targetWilayah?->desa),
            'deskripsi' => trim($validated['publicDeskripsi']),
            'kategori' => $validated['publicKategori'],
            'urgensi' => 'sedang',
            'dapil' => $validated['publicDapil'],
            'kecamatan' => $targetWilayah?->kecamatan,
            'desa' => $targetWilayah?->desa,
            'nomor_rw' => $validated['publicRw'] !== '' ? $this->normalizeRw($validated['publicRw']) : null,
            'target_wilayah_id' => $targetWilayah?->id,
            'nama_pelapor' => trim($validated['publicNama']),
            'hp_pelapor' => trim($validated['publicHp']) !== '' ? trim($validated['publicHp']) : null,
            'sumber' => 'website',
            'status' => 'diterima',
        ]);

        $aspirasi->update(['draft_pokir' => $aspirasi->generateDraftPokir()]);

        $this->reset([
            'publicNama',
            'publicHp',
            'publicDapil',
            'publicDesaId',
            'publicRw',
            'publicKategori',
            'publicDeskripsi',
        ]);

        $this->publicKategori = 'infrastruktur';

        session()->flash('message', 'Terima kasih. Aspirasi Anda sudah kami terima dan akan ditindaklanjuti.');
    }

    public function render()
    {
        return view('livewire.public-site.aspirasi-warga');
    }

    private function generateJudul(string $kategori, ?string $desa): string
    {
        $kategoriLabel = Aspirasi::KATEGORI_OPTIONS[$kategori] ?? ucfirst($kategori);

        return $desa
            ? $kategoriLabel.' untuk '.$desa
            : 'Aspirasi '.$kategoriLabel.' dari Warga';
    }

    private function normalizeRw(string $value): string
    {
        $clean = trim(str_ireplace(['RW', 'rw'], '', $value));

        if ($clean === '' || ! ctype_digit($clean)) {
            return trim($value);
        }

        return str_pad($clean, 3, '0', STR_PAD_LEFT);
    }
}
