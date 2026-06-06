<?php

declare(strict_types=1);

namespace App\Livewire\Public\Profile;

use App\Services\WilayahService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.public')]
#[Title('Lengkapi Profil - Bekasi Hebat')]
class Complete extends Component
{
    public ?string $birth_date = null;
    public ?string $gender = null;
    public ?string $kecamatan_code = null;
    public ?string $kelurahan_code = null;
    public ?string $address_detail = null;
    public ?string $rt = null;
    public ?string $rw = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->birth_date = $user->birth_date?->format('Y-m-d');
        $this->gender = $user->gender?->value;
        $this->kelurahan_code = $user->kelurahan_code;

        if ($user->kelurahan_code) {
            $this->kecamatan_code = substr($user->kelurahan_code, 0, 7);
        }

        $this->fillAddressParts($user->address);
    }

    #[Computed]
    public function kecamatanList()
    {
        return app(WilayahService::class)->getKabBekasiKecamatan();
    }

    #[Computed]
    public function kelurahanList()
    {
        return app(WilayahService::class)->getKelurahanByKecamatan($this->kecamatan_code);
    }

    public function updatedKecamatanCode(): void
    {
        $this->kelurahan_code = null;
    }

    public function rules(): array
    {
        return [
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:L,P'],
            'kecamatan_code' => ['nullable', 'string'],
            'kelurahan_code' => ['required', 'string', 'size:10', 'exists:indonesia_villages,code'],
            'address_detail' => ['required', 'string', 'min:10', 'max:450'],
            'rt' => ['required', 'digits_between:1,3'],
            'rw' => ['required', 'digits_between:1,3'],
        ];
    }

    public function rtOptions(): array
    {
        return range(1, 50);
    }

    public function rwOptions(): array
    {
        return range(1, 50);
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();
        $user->update([
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'kelurahan_code' => $this->kelurahan_code,
            'address' => $this->composeAddress(),
            'profile_completed_at' => now(),
        ]);

        session()->flash('success', 'Profil berhasil dilengkapi.');

        return redirect()->route('member.dashboard');
    }

    public function render()
    {
        return view('livewire.public.profile.complete');
    }

    private function fillAddressParts(?string $address): void
    {
        if (! $address) {
            return;
        }

        if (preg_match('/^(.*?),\s*RT\s*(\d{1,3})\s*\/\s*RW\s*(\d{1,3})$/i', $address, $matches)) {
            $this->address_detail = trim($matches[1]);
            $this->rt = ltrim($matches[2], '0') ?: '0';
            $this->rw = ltrim($matches[3], '0') ?: '0';

            return;
        }

        $this->address_detail = $address;
    }

    private function composeAddress(): string
    {
        return sprintf(
            '%s, RT %03d/RW %03d',
            trim((string) $this->address_detail),
            (int) $this->rt,
            (int) $this->rw,
        );
    }
}
