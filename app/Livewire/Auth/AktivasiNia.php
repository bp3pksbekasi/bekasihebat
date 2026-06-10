<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Models\AuditLog;
use App\Models\Kader;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('Aktivasi NIA - Bekasi Hebat')]
class AktivasiNia extends Component
{
    public int $step = 1;

    public string $nia1 = '';
    public string $nia2 = '';
    public string $nia3 = '';
    public string $nia4 = '';
    public string $nia5 = '';

    public ?Kader $kader = null;
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $errorMsg = '';

    public string $otpCode = '';
    public string $otpInput = '';
    public ?string $otpSentAt = null;

    public function getMaskedPhone(): string
    {
        $phone = $this->resolveRawPhoneNumber();
        if (strlen($phone) < 7) {
            return $phone;
        }
        return substr($phone, 0, 4) . '****' . substr($phone, -4);
    }

    public function verifikasiNia(): void
    {
        $nia = trim($this->nia1).'.'.trim($this->nia2).'.'.trim($this->nia3).'.'.trim($this->nia4).'.'.trim($this->nia5);

        if (! preg_match('/^\d{2}\.\d{2}\.\d{2}\.\d{2}\.\d{4}$/', $nia)) {
            $this->errorMsg = 'Format NIA harus seperti 32.16.06.10.0065';

            return;
        }

        $kader = Kader::query()
            ->where('nia', $nia)
            ->aktif()
            ->first();

        if (! $kader) {
            $this->errorMsg = 'NIA tidak ditemukan. Pastikan anda sudah terdaftar sebagai kader.';

            return;
        }

        if ($kader->is_activated) {
            $this->errorMsg = 'Sudah diaktivasi, silakan login';

            return;
        }

        if (User::query()->where('nia', $nia)->exists()) {
            $this->errorMsg = 'Akun sudah ada. Silakan login.';

            return;
        }

        $this->kader = $kader;
        $this->step = 2;
        $this->errorMsg = '';
    }

    public function aktivasiAkun()
    {
        $this->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        if (! $this->kader) {
            $this->errorMsg = 'Data kader tidak ditemukan. Silakan ulangi verifikasi NIA.';

            return null;
        }

        $nia = $this->kader->nia;
        $role = $this->kader->bidang_slug ? User::ROLE_BIDANG : User::ROLE_KADER;
        $phone = $this->resolvePhoneNumber();

        // Resolve Kelurahan Code based on Kader's desa and kecamatan
        $kelurahanCode = null;
        if ($this->kader->desa && $this->kader->kecamatan) {
            $village = \Illuminate\Support\Facades\DB::table('indonesia_villages as village')
                ->join('indonesia_districts as district', \Illuminate\Support\Facades\DB::raw('LEFT(village.code, 6)'), '=', 'district.code')
                ->where('district.code', 'LIKE', '3216%')
                ->whereRaw('UPPER(district.name) = ?', [mb_strtoupper(trim($this->kader->kecamatan))])
                ->whereRaw('UPPER(village.name) = ?', [mb_strtoupper(trim($this->kader->desa))])
                ->select('village.code')
                ->first();
            if ($village) {
                $kelurahanCode = $village->code;
            }
        }

        $user = User::query()->create([
            'name' => $this->kader->nama,
            'email' => $this->email,
            'phone' => $phone,
            'password' => Hash::make($this->password),
            'nia' => $nia,
            'kader_id' => $this->kader->id,
            'role' => $role,
            'bidang_slug' => $this->kader->bidang_slug,
            'dapil' => $this->kader->dapil,
            'kecamatan' => $this->kader->kecamatan,
            'desa' => $this->kader->desa,
            'nomor_rw' => $this->kader->nomor_rw,
            'kelurahan_code' => $kelurahanCode,
            'status' => 'aktif',
            'email_verified_at' => now(),
            'profile_completed_at' => now(),
        ]);

        $this->kader->update([
            'is_activated' => true,
            'email' => $this->kader->email ?: $this->email,
        ]);

        AuditLog::query()->create([
            'user_id' => $user->id,
            'action' => 'aktivasi',
            'description' => "Aktivasi akun NIA: {$nia}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        auth()->login($user);
        request()->session()->regenerate();

        return redirect()->route('profile.complete');
    }

    private function resolveRawPhoneNumber(): string
    {
        $candidate = preg_replace('/\D+/', '', (string) ($this->kader?->no_wa ?: $this->kader?->no_hp ?: ''));

        if ($candidate === '') {
            $niaDigits = preg_replace('/\D+/', '', (string) ($this->kader?->nia ?? ''));
            $candidate = '08'.substr(str_pad($niaDigits, 10, '0', STR_PAD_LEFT), -10);
        }

        return $candidate;
    }

    private function resolvePhoneNumber(): string
    {
        $candidate = $this->resolveRawPhoneNumber();

        $phone = $candidate;
        $counter = 1;

        while (User::query()->where('phone', $phone)->exists()) {
            $phone = $candidate.$counter;
            $counter++;
        }

        return $phone;
    }

    public function kirimOtp(): void
    {
        $this->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        if (! $this->kader) {
            $this->errorMsg = 'Data kader tidak ditemukan. Silakan ulangi verifikasi NIA.';
            return;
        }

        $this->otpCode = (string) rand(100000, 999999);
        $this->otpSentAt = now()->toDateTimeString();
        $this->otpInput = '';

        $candidate = $this->resolveRawPhoneNumber();
        $recipient = str_starts_with($candidate, '0') ? '62' . substr($candidate, 1) : $candidate;

        \Illuminate\Support\Facades\Log::info("Aktivasi OTP generated for NIA {$this->kader->nia}: {$this->otpCode}");

        $response = \App\Services\WhapifyService::sendMessage($recipient, "Halo {$this->kader->nama}, kode OTP Anda untuk aktivasi akun Bekasi Hebat adalah: {$this->otpCode}. Kode ini berlaku selama 5 menit. Harap tidak membagikan kode ini kepada siapapun.");

        if (!$response['success']) {
            $this->errorMsg = 'Gagal mengirim OTP via WhatsApp: ' . $response['message'];
            return;
        }

        $this->step = 3;
        $this->errorMsg = '';
    }

    public function resendOtp(): void
    {
        if (! $this->kader) {
            $this->errorMsg = 'Data kader tidak ditemukan. Silakan ulangi verifikasi NIA.';
            return;
        }

        if ($this->otpSentAt && now()->diffInSeconds(\Carbon\Carbon::parse($this->otpSentAt)) < 30) {
            $this->errorMsg = 'Harap tunggu beberapa saat sebelum mengirim ulang OTP.';
            return;
        }

        $this->otpCode = (string) rand(100000, 999999);
        $this->otpSentAt = now()->toDateTimeString();
        $this->otpInput = '';

        $candidate = $this->resolveRawPhoneNumber();
        $recipient = str_starts_with($candidate, '0') ? '62' . substr($candidate, 1) : $candidate;

        \Illuminate\Support\Facades\Log::info("Aktivasi OTP generated (resend) for NIA {$this->kader->nia}: {$this->otpCode}");

        $response = \App\Services\WhapifyService::sendMessage($recipient, "Halo {$this->kader->nama}, kode OTP Anda untuk aktivasi akun Bekasi Hebat adalah: {$this->otpCode}. Kode ini berlaku selama 5 menit. Harap tidak membagikan kode ini kepada siapapun.");

        if (!$response['success']) {
            $this->errorMsg = 'Gagal mengirim ulang OTP via WhatsApp: ' . $response['message'];
            return;
        }

        $this->errorMsg = '';
        session()->flash('successMsg', 'Kode OTP baru telah dikirim.');
    }

    public function verifikasiOtp()
    {
        if (empty($this->otpInput)) {
            $this->errorMsg = 'Silakan masukkan kode OTP.';
            return null;
        }

        if ($this->otpInput !== $this->otpCode) {
            $this->errorMsg = 'Kode OTP yang Anda masukkan salah.';
            return null;
        }

        if ($this->otpSentAt && now()->diffInMinutes(\Carbon\Carbon::parse($this->otpSentAt)) >= 5) {
            $this->errorMsg = 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang OTP.';
            return null;
        }

        $this->errorMsg = '';

        return $this->aktivasiAkun();
    }

    public function render()
    {
        return view('livewire.auth.aktivasi-nia')
            ->layout('components.layouts.guest');
    }
}
