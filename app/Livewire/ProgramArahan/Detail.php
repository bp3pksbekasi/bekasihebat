<?php

declare(strict_types=1);

namespace App\Livewire\ProgramArahan;

use App\Models\Korwe;
use App\Models\Korte;
use App\Models\PenggalangSuara;
use App\Models\ProgramArahan;
use App\Models\ProgramArahanApproval;
use App\Models\ProgramArahanBudgetItem;
use App\Models\ProgramArahanPersonel;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Detail extends Component
{
    public ProgramArahan $programArahan;

    public array $approvalNotes = [
        'dpra' => '',
        'dpc' => '',
        'dpd' => '',
    ];

    public ?string $budgetEditId = null;
    public string $budgetItem = '';
    public string $budgetKategori = '';
    public int $budgetQty = 1;
    public string $budgetSatuan = 'Pcs';
    public string $budgetHargaSatuan = '0';
    public string $budgetKeterangan = '';

    // Personnel
    public string $personelType = '';
    public string $personelId = '';

    public function mount(ProgramArahan $programArahan): void
    {
        $this->programArahan = $programArahan->load([
            'creator',
            'targetWilayah',
            'approvals',
            'budgetItems',
            'personel',
            'report',
        ]);
    }

    #[Computed]
    public function canEdit(): bool
    {
        return $this->programArahan->status !== 'selesai' && $this->programArahan->status !== 'tertunda';
    }

    private function refreshProgram(): void
    {
        $this->programArahan->refresh();
        $this->programArahan->load([
            'creator',
            'targetWilayah',
            'approvals',
            'budgetItems',
            'personel',
            'report',
        ]);
    }

    // --- Approval Logic ---

    public function canApproveLevel(string $level): bool
    {
        $user = auth()->user();
        
        if (! $user instanceof User) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return match ($level) {
            'dpra' => $user->isDpra() || $user->isDpc() || $user->isDpd(),
            'dpc' => $user->isDpc() || $user->isDpd(),
            'dpd' => $user->isDpd(),
            default => false,
        };
    }

    public function approve(string $level): void
    {
        if (! $this->canApproveLevel($level)) {
            abort(403);
        }

        if ($this->programArahan->status !== 'berjalan' || $this->programArahan->level_approval !== $level) {
            return;
        }

        $approval = $this->programArahan->approvals->firstWhere('level', $level);

        if (! $approval instanceof ProgramArahanApproval) {
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

        $this->programArahan->update([
            'level_approval' => $nextLevel,
        ]);

        $this->approvalNotes[$level] = '';
        $this->refreshProgram();
        session()->flash('message', 'Approval berhasil disimpan.');
    }

    public function reject(string $level): void
    {
        if (! $this->canApproveLevel($level)) {
            abort(403);
        }

        if ($this->programArahan->status !== 'berjalan' || $this->programArahan->level_approval !== $level) {
            return;
        }

        $approval = $this->programArahan->approvals->firstWhere('level', $level);

        if (! $approval instanceof ProgramArahanApproval) {
            return;
        }

        $approval->update([
            'status' => 'rejected',
            'approver_id' => auth()->id(),
            'catatan' => $this->approvalNotes[$level] !== '' ? $this->approvalNotes[$level] : 'Ditolak tanpa catatan.',
            'decided_at' => now(),
        ]);

        $this->programArahan->update([
            'status' => 'tertunda',
            'level_approval' => $level,
        ]);

        $this->approvalNotes[$level] = '';
        $this->refreshProgram();
        session()->flash('message', 'Program ditolak/ditunda.');
    }

    // --- Budget Logic ---

    public function editBudgetItem(string $id): void
    {
        $item = $this->programArahan->budgetItems->firstWhere('id', $id);

        if (! $item instanceof ProgramArahanBudgetItem) {
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

    public function cancelBudgetEdit(): void
    {
        $this->reset(['budgetEditId', 'budgetItem', 'budgetKategori', 'budgetQty', 'budgetSatuan', 'budgetHargaSatuan', 'budgetKeterangan']);
    }

    public function saveBudgetItem(): void
    {
        $this->validate([
            'budgetItem' => 'required|string|max:255',
            'budgetQty' => 'required|integer|min:1',
            'budgetSatuan' => 'required|string|max:50',
            'budgetHargaSatuan' => 'required|numeric|min:0',
        ]);

        $subtotal = $this->budgetQty * (float) $this->budgetHargaSatuan;

        if ($this->budgetEditId) {
            $item = $this->programArahan->budgetItems->firstWhere('id', $this->budgetEditId);
            if ($item instanceof ProgramArahanBudgetItem) {
                $item->update([
                    'item' => $this->budgetItem,
                    'kategori' => $this->budgetKategori !== '' ? $this->budgetKategori : null,
                    'qty' => $this->budgetQty,
                    'satuan' => $this->budgetSatuan,
                    'harga_satuan' => (float) $this->budgetHargaSatuan,
                    'subtotal' => $subtotal,
                    'keterangan' => $this->budgetKeterangan !== '' ? $this->budgetKeterangan : null,
                ]);
            }
        } else {
            $this->programArahan->budgetItems()->create([
                'item' => $this->budgetItem,
                'kategori' => $this->budgetKategori !== '' ? $this->budgetKategori : null,
                'qty' => $this->budgetQty,
                'satuan' => $this->budgetSatuan,
                'harga_satuan' => (float) $this->budgetHargaSatuan,
                'subtotal' => $subtotal,
                'keterangan' => $this->budgetKeterangan !== '' ? $this->budgetKeterangan : null,
            ]);
        }

        $this->cancelBudgetEdit();
        $this->refreshProgram();
        session()->flash('message', 'Item anggaran berhasil disimpan.');
    }

    public function removeBudgetItem(string $id): void
    {
        $item = $this->programArahan->budgetItems->firstWhere('id', $id);
        if ($item instanceof ProgramArahanBudgetItem) {
            $item->delete();
            $this->refreshProgram();
            session()->flash('message', 'Item anggaran berhasil dihapus.');
        }
    }

    // --- Personnel Logic ---
    
    #[Computed]
    public function availablePersonnel(): Collection
    {
        if (!$this->personelType || !$this->programArahan->target_wilayah_id || !$this->programArahan->nomor_rw) {
            return collect();
        }

        $query = null;
        if ($this->personelType === 'korwe') {
            $query = Korwe::where('target_wilayah_id', $this->programArahan->target_wilayah_id)
                ->whereRaw('TRIM(LEADING "0" FROM nomor_rw) = TRIM(LEADING "0" FROM ?)', [$this->programArahan->nomor_rw]);
        } elseif ($this->personelType === 'korte') {
            $query = Korte::where('target_wilayah_id', $this->programArahan->target_wilayah_id)
                ->whereRaw('TRIM(LEADING "0" FROM nomor_rw) = TRIM(LEADING "0" FROM ?)', [$this->programArahan->nomor_rw]);
        } elseif ($this->personelType === 'penggalang') {
            $query = PenggalangSuara::where('target_wilayah_id', $this->programArahan->target_wilayah_id)
                ->whereRaw('TRIM(LEADING "0" FROM nomor_rw) = TRIM(LEADING "0" FROM ?)', [$this->programArahan->nomor_rw]);
        }

        if (!$query) return collect();

        // Exclude those already attached
        $existingIds = $this->programArahan->personel->where('personel_type', $this->personelType)->pluck('personel_id')->toArray();
        return $query->whereNotIn('id', $existingIds)->get();
    }

    public function linkPersonel(): void
    {
        $this->validate([
            'personelType' => 'required|in:korwe,korte,penggalang',
            'personelId' => 'required',
        ]);

        $this->programArahan->personel()->create([
            'personel_type' => $this->personelType,
            'personel_id' => $this->personelId,
        ]);

        $this->personelType = '';
        $this->personelId = '';
        $this->refreshProgram();
        session()->flash('message', 'Personel berhasil ditautkan.');
    }

    public function unlinkPersonel(string $id): void
    {
        $item = $this->programArahan->personel->firstWhere('id', $id);
        if ($item instanceof ProgramArahanPersonel) {
            $item->delete();
            $this->refreshProgram();
            session()->flash('message', 'Tautan personel berhasil dilepas.');
        }
    }

    public function render()
    {
        return view('livewire.program-arahan.detail')
            ->layout('components.layouts.app-fullwidth', ['title' => 'Detail Program Arahan']);
    }
}
