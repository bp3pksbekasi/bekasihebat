<?php

declare(strict_types=1);

namespace App\Livewire\Public\Profile;

use App\Services\WilayahService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.public')]
#[Title('Lengkapi Profil - Bekasi Hebat')]
class Complete extends Component
{
    use WithFileUploads;
    public ?string $birth_date = null;
    public ?string $gender = null;
    public ?string $nik = null;
    public ?string $ttl_tempat = null;
    public ?string $ttl_tanggal = null;
    public ?string $jenis_kelamin = null;
    public $foto = null;
    public ?string $kecamatan_code = null;
    public ?string $kelurahan_code = null;
    public ?string $address_detail = null;
    public ?string $rt = null;
    public ?string $rw = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->birth_date = $user->birth_date?->format('Y-m-d');
        $this->gender = $user->gender instanceof \BackedEnum ? $user->gender->value : $user->gender;
        $this->nik = $user->nik;
        $this->ttl_tempat = $user->ttl_tempat;
        $this->ttl_tanggal = $user->ttl_tanggal?->format('Y-m-d') ?: $user->birth_date?->format('Y-m-d');
        $this->jenis_kelamin = $user->jenis_kelamin ?: ($user->gender instanceof \BackedEnum ? $user->gender->value : $user->gender);
        $this->kelurahan_code = $user->kelurahan_code;

        if ($user->kelurahan_code) {
            $this->kecamatan_code = substr($user->kelurahan_code, 0, 6);
        }

        $this->fillAddressParts($user->address);

        // Prepopulate RT/RW from associated Kader record if not found in address
        $kader = $user->kader_id ? \App\Models\Kader::find($user->kader_id) : null;
        if (! $this->rt && $kader && $kader->nomor_rt) {
            $this->rt = ltrim((string) $kader->nomor_rt, '0') ?: '0';
        }
        if (! $this->rw && $user->nomor_rw) {
            $this->rw = ltrim((string) $user->nomor_rw, '0') ?: '0';
        } elseif (! $this->rw && $kader && $kader->nomor_rw) {
            $this->rw = ltrim((string) $kader->nomor_rw, '0') ?: '0';
        }
    }

    public function updatedTtlTanggal($value): void
    {
        $this->birth_date = $value;
    }

    public function updatedJenisKelamin($value): void
    {
        $this->gender = $value;
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
            'nik' => ['nullable', 'string', 'size:16', 'regex:/^[0-9]+$/'],
            'ttl_tempat' => ['nullable', 'string', 'max:100'],
            'ttl_tanggal' => ['required', 'date', 'before:today'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'foto' => ['nullable', 'image', 'max:2048'],
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
        $this->birth_date = $this->ttl_tanggal;
        $this->gender = $this->jenis_kelamin;

        $this->validate();

        $user = Auth::user();

        if ($this->foto) {
            $path = $this->foto->store('fotos', 'public');
            $user->update(['foto_path' => $path]);
        }

        $user->update([
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'nik' => $this->nik,
            'ttl_tempat' => $this->ttl_tempat,
            'ttl_tanggal' => $this->ttl_tanggal ?: null,
            'jenis_kelamin' => $this->jenis_kelamin,
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
