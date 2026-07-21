<?php

declare(strict_types=1);

namespace App\Livewire\ProgramArahan;

use App\Models\DataRw;
use App\Models\ProgramArahan;
use App\Models\ProgramArahanApproval;
use App\Models\TargetWilayah;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Edit extends Component
{
    public ProgramArahan $programArahan;

    public int $infoStep = 1; // 1 to 4 steps

    public bool $fieldsLocked = false;

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

    public function mount(ProgramArahan $programArahan): void
    {
        $this->programArahan = $programArahan->load('targetWilayah', 'budgetItems');
        
        $this->fieldsLocked = $this->programArahan->status !== 'belum_mulai';

        $this->orgLevel = $this->programArahan->org_level;
        $this->bidangDpdId = $this->programArahan->bidang_dpd_id ?? '';

        if ($this->programArahan->targetWilayah) {
            $this->lokasiDapil = $this->programArahan->targetWilayah->dapil;
            $this->lokasiKecamatan = $this->programArahan->targetWilayah->kecamatan;
            $this->lokasiDesa = $this->programArahan->targetWilayah->desa;
        }
        $this->nomorRw = $this->programArahan->nomor_rw ?? '';
        $this->jenisProgram = $this->programArahan->jenis_program;
        $this->targetAngka = $this->programArahan->target_angka;
        $this->satuan = $this->programArahan->satuan ?? '';

        $this->judul = $this->programArahan->judul;
        $this->deskripsi = $this->programArahan->deskripsi ?? '';
        $this->tanggalMulai = $this->programArahan->tanggal_mulai ? $this->programArahan->tanggal_mulai->format('Y-m-d\TH:i') : '';
        $this->tanggalSelesai = $this->programArahan->tanggal_selesai ? $this->programArahan->tanggal_selesai->format('Y-m-d\TH:i') : '';
        $this->penyelenggara = $this->programArahan->penyelenggara ?? '';
        $this->picNama = $this->programArahan->pic_nama ?? '';
        $this->picHp = $this->programArahan->pic_hp ?? '';

        $this->fundingSource = $this->programArahan->funding_source ?? '';
        $this->budgetNotes = $this->programArahan->budget_notes ?? '';

        foreach ($this->programArahan->budgetItems as $item) {
            $this->budgetItems[] = [
                'id' => $item->id,
                'item' => $item->item,
                'kategori' => $item->kategori,
                'qty' => $item->qty,
                'satuan' => $item->satuan,
                'harga_satuan' => $item->harga_satuan,
                'subtotal' => $item->subtotal,
                'keterangan' => $item->keterangan,
            ];
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
        if ($this->fieldsLocked) return;
        $this->lokasiKecamatan = '';
        $this->lokasiDesa = '';
        $this->nomorRw = '';
    }

    public function updatedLokasiKecamatan(): void
    {
        if ($this->fieldsLocked) return;
        $this->lokasiDesa = '';
        $this->nomorRw = '';
    }
    
    public function updatedLokasiDesa(): void
    {
        if ($this->fieldsLocked) return;
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
            if (!$this->fieldsLocked) {
                $this->validate([
                    'lokasiDesa' => 'required',
                    'nomorRw' => 'nullable',
                    'jenisProgram' => 'required',
                    'targetAngka' => 'required|numeric|min:0',
                    'satuan' => 'nullable|string',
                ]);
            }
        } elseif ($this->infoStep === 2) {
            if (!$this->fieldsLocked) {
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
            } else {
                $this->validate([
                    'deskripsi' => 'nullable|string',
                    'tanggalSelesai' => 'nullable|date|after_or_equal:tanggalMulai',
                    'penyelenggara' => 'nullable|string',
                    'picNama' => 'nullable|string',
                    'picHp' => 'nullable|string',
                ]);
            }
        }
        $this->infoStep++;
    }

    public function prevStep()
    {
        $this->infoStep--;
    }

    public function simpanDraft()
    {
        if ($this->programArahan->status !== 'belum_mulai') {
            return;
        }
        
        $this->persist('belum_mulai');
        session()->flash('message', 'Draft program berhasil diperbarui.');
        return redirect()->route('program-arahan.detail', $this->programArahan->id);
    }

    public function simpanDanAjukan()
    {
        $status = ($this->orgLevel === 'dpd' && !empty($this->bidangDpdId)) ? 'berjalan' : 'berjalan'; 
        
        $this->persist($this->programArahan->status === 'belum_mulai' ? 'berjalan' : $this->programArahan->status);
        session()->flash('message', 'Program berhasil diperbarui.');
        return redirect()->route('program-arahan.detail', $this->programArahan->id);
    }

    private function persist(string $status): void
    {
        $fillData = [
            'deskripsi' => $this->deskripsi ?: null,
            'tanggal_selesai' => $this->tanggalSelesai ?: null,
            'penyelenggara' => $this->penyelenggara ?: null,
            'pic_nama' => $this->picNama ?: null,
            'pic_hp' => $this->picHp ?: null,
            'funding_source' => $this->fundingSource ?: null,
            'budget_notes' => $this->budgetNotes ?: null,
            'status' => $status,
        ];

        // If not locked, allow updates to target area, program type, etc.
        if (!$this->fieldsLocked) {
            $fillData['judul'] = $this->judul;
            $fillData['tanggal_mulai'] = $this->tanggalMulai;
            $fillData['org_level'] = $this->orgLevel;
            $fillData['bidang_dpd_id'] = $this->bidangDpdId ?: null;
            $fillData['target_wilayah_id'] = $this->targetWilayahSelected->id ?? $this->programArahan->target_wilayah_id;
            $fillData['nomor_rw'] = $this->nomorRw ?: null;
            $fillData['status_wilayah_snapshot'] = $this->currentStatusWilayah ?? $this->programArahan->status_wilayah_snapshot;
            $fillData['jenis_program'] = $this->jenisProgram;
            $fillData['target_angka'] = $this->targetAngka;
            $fillData['satuan'] = $this->satuan ?: null;
        }

        $this->programArahan->update($fillData);

        // Sync budget items
        $this->programArahan->budgetItems()->delete();
        
        foreach ($this->budgetItems as $item) {
            if (!empty($item['item'])) {
                $this->programArahan->budgetItems()->create([
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

        if ($this->programArahan->status === 'belum_mulai' && $status !== 'belum_mulai') {
            collect(['dpra', 'dpc', 'dpd'])->each(function (string $level): void {
                ProgramArahanApproval::query()->create([
                    'program_arahan_id' => $this->programArahan->id,
                    'level' => $level,
                    'status' => 'pending',
                ]);
            });
        }
    }

    public function render()
    {
        return view('livewire.program-arahan.edit')
            ->layout('components.layouts.app-fullwidth', ['title' => 'Edit Program Arahan']);
    }
}
