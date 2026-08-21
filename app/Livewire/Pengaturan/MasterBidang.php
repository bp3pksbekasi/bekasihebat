<?php

declare(strict_types=1);

namespace App\Livewire\Pengaturan;

use App\Models\BidangDpd;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class MasterBidang extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search = '';

    public bool $showForm = false;
    public bool $isEdit = false;
    public ?string $editId = null;

    public string $fNama = '';
    public string $fSingkatan = '';
    public string $fIcon = '';
    public string $fColor = '';
    public string $fPicNama = '';
    public string $fPicHp = '';
    public string $fKabid = '';
    public string $fNohpkabid = '';
    public string $fSekbid = '';
    public string $fNohpsekbid = '';
    public string $fPeriode = '';
    public int $fUrutan = 0;

    public function mount(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function getBidangListProperty(): LengthAwarePaginator
    {
        return BidangDpd::query()
            ->where('is_active', true)
            ->when($this->search !== '', function (Builder $query): void {
                $query->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('kabid', 'like', '%' . $this->search . '%')
                    ->orWhere('sekbid', 'like', '%' . $this->search . '%');
            })
            ->orderBy('urutan')
            ->orderBy('nama')
            ->paginate(15);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openFormCreate(): void
    {
        $this->resetForm();
        $this->isEdit = false;
        
        $maxUrutan = BidangDpd::query()->max('urutan') ?? 0;
        $this->fUrutan = $maxUrutan + 1;
        
        $this->showForm = true;
    }

    public function openFormEdit(string $id): void
    {
        $this->resetForm();
        $this->isEdit = true;
        $this->editId = $id;

        $bidang = BidangDpd::query()->findOrFail($id);
        $this->fNama = $bidang->nama ?? '';
        $this->fSingkatan = $bidang->singkatan ?? '';
        $this->fIcon = $bidang->icon ?? '';
        $this->fColor = $bidang->color ?? '';
        $this->fPicNama = $bidang->pic_nama ?? '';
        $this->fPicHp = $bidang->pic_hp ?? '';
        $this->fKabid = $bidang->kabid ?? '';
        $this->fNohpkabid = $bidang->nohpkabid ?? '';
        $this->fSekbid = $bidang->sekbid ?? '';
        $this->fNohpsekbid = $bidang->nohpsekbid ?? '';
        $this->fPeriode = $bidang->periode ?? '';
        $this->fUrutan = $bidang->urutan ?? 0;

        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->editId = null;
        $this->isEdit = false;
        $this->fNama = '';
        $this->fSingkatan = '';
        $this->fIcon = '';
        $this->fColor = '';
        $this->fPicNama = '';
        $this->fPicHp = '';
        $this->fKabid = '';
        $this->fNohpkabid = '';
        $this->fSekbid = '';
        $this->fNohpsekbid = '';
        $this->fPeriode = '';
        $this->fUrutan = 0;
    }

    public function simpanBidang(): void
    {
        $validated = $this->validate([
            'fNama' => ['required', 'string', 'max:255', Rule::unique('bidang_dpds', 'nama')->ignore($this->editId)],
            'fSingkatan' => ['nullable', 'string', 'max:50'],
            'fIcon' => ['nullable', 'string', 'max:50'],
            'fColor' => ['nullable', 'string', 'max:50'],
            'fPicNama' => ['nullable', 'string', 'max:255'],
            'fPicHp' => ['nullable', 'string', 'max:50'],
            'fKabid' => ['nullable', 'string', 'max:255'],
            'fNohpkabid' => ['nullable', 'string', 'max:50'],
            'fSekbid' => ['nullable', 'string', 'max:255'],
            'fNohpsekbid' => ['nullable', 'string', 'max:50'],
            'fPeriode' => ['nullable', 'string', 'max:50'],
            'fUrutan' => ['required', 'integer', 'min:0'],
        ]);

        $payload = [
            'nama' => $validated['fNama'],
            'singkatan' => $validated['fSingkatan'],
            'slug' => Str::slug($validated['fNama']),
            'icon' => $validated['fIcon'],
            'color' => $validated['fColor'],
            'pic_nama' => $validated['fPicNama'],
            'pic_hp' => $validated['fPicHp'],
            'kabid' => $validated['fKabid'],
            'nohpkabid' => $validated['fNohpkabid'],
            'sekbid' => $validated['fSekbid'],
            'nohpsekbid' => $validated['fNohpsekbid'],
            'periode' => $validated['fPeriode'],
            'urutan' => $validated['fUrutan'],
        ];

        if ($this->isEdit && $this->editId) {
            BidangDpd::query()->findOrFail($this->editId)->update($payload);
            session()->flash('message', 'Data bidang berhasil diperbarui.');
        } else {
            BidangDpd::query()->create($payload);
            session()->flash('message', 'Data bidang berhasil ditambahkan.');
        }

        $this->closeForm();
    }

    public function toggleActive(string $id): void
    {
        $bidang = BidangDpd::query()->find($id);
        if ($bidang) {
            $bidang->update(['is_active' => !$bidang->is_active]);
            $status = $bidang->is_active ? 'diaktifkan' : 'dinonaktifkan';
            session()->flash('message', "Bidang berhasil $status.");
        }
    }

    public function render()
    {
        return view('livewire.pengaturan.master-bidang')
            ->layout('components.layouts.app.sidebar', ['title' => 'Master Bidang']);
    }
}
