<?php

declare(strict_types=1);

namespace App\Livewire\ProgramArahan;

use App\Models\DataRw;
use App\Models\ProgramArahan;
use App\Models\ProgramArahanApproval;
use App\Models\TargetWilayah;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public int $infoStep = 1; // 1 to 4 steps

    public string $orgLevel = 'dpra';
    public string $bidangDpdId = '';

    // Step 1: Wilayah & Jenis Program
    public string $lokasiDapil = '';
    public string $lokasiKecamatan = '';
    public string $lokasiDesa = '';
    public string $nomorRw = '';
    public string $jenisProgram = 'pembentukan_korwe';
    public int $targetAngka = 0;
    public string $satuan = 'Orang';

    // Step 2: Informasi Program
    public string $judul = '';
    public string $deskripsi = '';
    public string $tanggalMulai = '';
    public string $tanggalSelesai = '';
    public string $penyelenggara = '';
    public string $picNama = '';
    public string $picHp = '';

    // Step 3: Anggaran (Opsional)
    public array $budgetItems = [];
    public string $fundingSource = '';
    public string $budgetNotes = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->orgLevel = $user?->org_level ?? 'dpra';
        $this->tanggalMulai = now()->format('Y-m-d\TH:i');

        if (request()->has('target_wilayah_id') && request()->has('nomor_rw')) {
            $target = TargetWilayah::find(request()->query('target_wilayah_id'));
            if ($target) {
                $this->lokasiDapil = $target->dapil;
                $this->lokasiKecamatan = $target->kecamatan;
                $this->lokasiDesa = $target->desa;
                $this->nomorRw = request()->query('nomor_rw');
            }
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
    public function targetWilayahSelected()
    {
        if (!$this->lokasiDesa) return null;
        return TargetWilayah::where('desa', $this->lokasiDesa)->first();
    }

    #[Computed]
    public function rwOptions(): Collection
    {
        if (!$this->targetWilayahSelected) return collect([]);
        return DataRw::where('target_wilayah_id', $this->targetWilayahSelected->id)
            ->orderBy('nomor_rw')
            ->pluck('nomor_rw');
    }

    #[Computed]
    public function currentStatusWilayah()
    {
        if (!$this->targetWilayahSelected || !$this->nomorRw) return null;
        return DataRw::where('target_wilayah_id', $this->targetWilayahSelected->id)
            ->where('nomor_rw', $this->nomorRw)
            ->value('status_wilayah');
    }

    #[Computed]
    public function statusWilayahConfig()
    {
        $status = $this->currentStatusWilayah;
        if (!$status) return null;
        return TargetWilayah::STATUS_CONFIG[$status] ?? null;
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

        return [
            'dpra' => 'DPRa (Desa/Kelurahan)',
            'dpc'  => 'DPC (Kecamatan)',
            'dpd'  => 'DPD (Kabupaten Bekasi)',
        ];
    }

    public function updatedLokasiDapil(): void
    {
        $this->lokasiKecamatan = '';
        $this->lokasiDesa = '';
        $this->nomorRw = '';
    }

    public function updatedLokasiKecamatan(): void
    {
        $this->lokasiDesa = '';
        $this->nomorRw = '';
    }
    
    public function updatedLokasiDesa(): void
    {
        $this->nomorRw = '';
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

    public function nextStep()
    {
        if ($this->infoStep === 1) {
            $this->validate([
                'lokasiDesa' => 'required',
                'nomorRw' => 'nullable',
                'jenisProgram' => 'required',
                'targetAngka' => 'required|numeric|min:0',
                'satuan' => 'nullable|string',
            ]);
        } elseif ($this->infoStep === 2) {
            $this->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'tanggalMulai' => 'required|date',
                'tanggalSelesai' => 'nullable|date|after_or_equal:tanggalMulai',
                'penyelenggara' => 'nullable|string',
                'picNama' => 'nullable|string',
                'picHp' => 'nullable|string',
                'orgLevel' => 'required|in:dpd,dpc,dpra',
                'bidangDpdId' => 'nullable',
            ]);
        }
        $this->infoStep++;
    }

    public function prevStep()
    {
        $this->infoStep--;
    }

    public function simpanDraft()
    {
        $program = $this->persist('belum_mulai', 'dpra');
        session()->flash('message', 'Draft program berhasil disimpan.');
        // For now redirect to index or home since there is no detail page specified for program arahan except in RW detail
        return redirect()->route('buku-induk-rw.index');
    }

    public function simpanDanAjukan()
    {
        $status = ($this->orgLevel === 'dpd' && !empty($this->bidangDpdId)) ? 'berjalan' : 'berjalan'; 
        
        $program = $this->persist('berjalan', 'dpra');
        session()->flash('message', 'Program berhasil diajukan.');
        return redirect()->route('buku-induk-rw.index');
    }

    private function persist(string $status, string $levelApproval): ProgramArahan
    {
        $targetWilayahId = $this->targetWilayahSelected->id;
        $snapshot = $this->currentStatusWilayah;

        $program = new ProgramArahan();
        $program->forceFill([
            'org_level' => $this->orgLevel,
            'bidang_dpd_id' => $this->bidangDpdId ?: null,
            'target_wilayah_id' => $targetWilayahId,
            'nomor_rw' => $this->nomorRw ?: null,
            'status_wilayah_snapshot' => $snapshot,
            'judul' => $this->judul,
            'deskripsi' => $this->deskripsi ?: null,
            'jenis_program' => $this->jenisProgram,
            'target_angka' => $this->targetAngka,
            'satuan' => $this->satuan ?: null,
            'tanggal_mulai' => $this->tanggalMulai,
            'tanggal_selesai' => $this->tanggalSelesai ?: null,
            'penyelenggara' => $this->penyelenggara ?: null,
            'pic_nama' => $this->picNama ?: null,
            'pic_hp' => $this->picHp ?: null,
            'status' => $status,
            'level_approval' => $levelApproval,
            'funding_source' => $this->fundingSource ?: null,
            'budget_notes' => $this->budgetNotes ?: null,
            'created_by' => auth()->id(),
        ]);
        $program->save();

        foreach ($this->budgetItems as $item) {
            if (!empty($item['item'])) {
                $program->budgetItems()->create([
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

        if ($status !== 'belum_mulai') {
            collect(['dpra', 'dpc', 'dpd'])->each(function (string $level) use ($program): void {
                ProgramArahanApproval::query()->create([
                    'program_arahan_id' => $program->id,
                    'level' => $level,
                    'status' => 'pending',
                ]);
            });
        }

        return $program;
    }

    public function render()
    {
        return view('livewire.program-arahan.create')
            ->layout('components.layouts.app-fullwidth', ['title' => 'Buat Program Arahan']);
    }
}
