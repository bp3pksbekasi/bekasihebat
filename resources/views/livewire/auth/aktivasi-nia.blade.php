<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:32px 16px;background:radial-gradient(circle at top,#1f2937 0%,#09090b 55%,#030712 100%);">
    <div style="width:100%;max-width:560px;">
        <div style="display:flex;flex-direction:column;align-items:center;gap:12px;margin-bottom:24px;text-align:center;">
            <a href="{{ route('public.home') }}" wire:navigate>
                <img src="{{ asset('images/logoputih.png') }}" alt="Kabupaten Bekasi Hebat" style="height: 64px; width: auto; object-fit: contain;">
            </a>
            <div style="font-size:28px;font-weight:700;color:white;margin-top:4px;">Aktivasi Akun</div>
        </div>

        <div style="background:rgba(24,24,27,0.92);border:1px solid rgba(63,63,70,0.9);border-radius:24px;padding:24px;box-shadow:0 24px 60px rgba(0,0,0,0.35);backdrop-filter:blur(12px);">
            @if ($step === 1)
                <div style="text-align:center;margin-bottom:20px;">
                    <div style="font-size:14px;font-weight:600;color:#f4f4f5;">Masukkan NIA</div>
                    <div style="font-size:12px;color:#a1a1aa;margin-top:6px;">Gunakan format 32.16.06.10.0065 sesuai data kader.</div>
                </div>

                @if ($errorMsg !== '')
                    <div style="margin-bottom:16px;padding:12px 14px;border-radius:14px;background:rgba(127,29,29,0.22);border:1px solid rgba(248,113,113,0.35);color:#fca5a5;font-size:12px;">
                        {{ $errorMsg }}
                    </div>
                @endif

                <form wire:submit.prevent="verifikasiNia">
                    <div style="display:flex;justify-content:center;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
                        <input id="nia-1" type="text" inputmode="numeric" maxlength="2" wire:model.live="nia1" data-nia-len="2" class="nia-input" style="width:64px;height:64px;border-radius:16px;border:1px solid #3f3f46;background:#111113;color:white;text-align:center;font-size:22px;font-weight:700;outline:none;">
                        <input id="nia-2" type="text" inputmode="numeric" maxlength="2" wire:model.live="nia2" data-nia-len="2" class="nia-input" style="width:64px;height:64px;border-radius:16px;border:1px solid #3f3f46;background:#111113;color:white;text-align:center;font-size:22px;font-weight:700;outline:none;">
                        <input id="nia-3" type="text" inputmode="numeric" maxlength="2" wire:model.live="nia3" data-nia-len="2" class="nia-input" style="width:64px;height:64px;border-radius:16px;border:1px solid #3f3f46;background:#111113;color:white;text-align:center;font-size:22px;font-weight:700;outline:none;">
                        <input id="nia-4" type="text" inputmode="numeric" maxlength="2" wire:model.live="nia4" data-nia-len="2" class="nia-input" style="width:64px;height:64px;border-radius:16px;border:1px solid #3f3f46;background:#111113;color:white;text-align:center;font-size:22px;font-weight:700;outline:none;">
                        <input id="nia-5" type="text" inputmode="numeric" maxlength="4" wire:model.live="nia5" data-nia-len="4" class="nia-input" style="width:88px;height:64px;border-radius:16px;border:1px solid #3f3f46;background:#111113;color:white;text-align:center;font-size:22px;font-weight:700;outline:none;">
                    </div>

                    <button type="submit" style="width:100%;padding:12px 16px;border:none;border-radius:14px;background:#f97316;color:white;font-size:14px;font-weight:700;cursor:pointer;">
                        Verifikasi NIA
                    </button>
                </form>

                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="text-sm text-orange-600 hover:underline">
                        Sudah punya akun? Login →
                    </a>
                </div>
            @endif

            @if ($step === 2 && $kader)
                <div style="display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <span style="display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:999px;background:rgba(34,197,94,0.18);border:1px solid rgba(74,222,128,0.28);font-size:12px;font-weight:700;color:#86efac;">
                        <i class="ti ti-circle-check" aria-hidden="true"></i>
                        NIA ditemukan
                    </span>
                </div>

                <div style="border:1px solid rgba(63,63,70,0.9);background:#111113;border-radius:18px;padding:16px;margin-bottom:18px;">
                    <div style="display:flex;align-items:flex-start;gap:14px;">
                        <div style="width:60px;height:60px;border-radius:18px;background:linear-gradient(135deg,#7c3aed 0%,#4f46e5 100%);display:flex;align-items:center;justify-content:center;color:white;font-size:22px;font-weight:700;flex-shrink:0;">
                            {{ \Illuminate\Support\Str::of($kader->nama)->explode(' ')->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->take(2)->implode('') }}
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:17px;font-weight:700;color:#f4f4f5;">{{ $kader->nama }}</div>
                            <div style="font-size:12px;color:#a1a1aa;margin-top:3px;">NIA {{ $kader->nia }}</div>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;">
                                <span style="font-size:10px;padding:4px 8px;border-radius:999px;background:#27272a;color:#e4e4e7;">{{ $kader->jenjang_config['label'] ?? ucfirst($kader->jenjang) }}</span>
                                @foreach ($kader->roles as $role)
                                    <span style="font-size:10px;padding:4px 8px;border-radius:999px;background:rgba(124,58,237,0.16);color:#c4b5fd;">{{ $role }}</span>
                                @endforeach
                            </div>
                            <div style="font-size:11px;color:#d4d4d8;margin-top:10px;">
                                {{ $kader->dapil ?: '-' }} · {{ $kader->kecamatan ?: '-' }} · {{ $kader->desa ?: '-' }} · RW {{ $kader->nomor_rw ?: '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                @if ($errorMsg !== '')
                    <div style="margin-bottom:16px;padding:12px 14px;border-radius:14px;background:rgba(127,29,29,0.22);border:1px solid rgba(248,113,113,0.35);color:#fca5a5;font-size:12px;">
                        {{ $errorMsg }}
                    </div>
                @endif

                <form wire:submit.prevent="kirimOtp" style="display:grid;gap:14px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#d4d4d8;margin-bottom:6px;">Email</label>
                        <input type="email" wire:model.defer="email" style="width:100%;padding:12px 14px;border-radius:14px;border:1px solid #3f3f46;background:#09090b;color:white;font-size:13px;outline:none;">
                        @error('email') <div style="font-size:11px;color:#fca5a5;margin-top:5px;">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#d4d4d8;margin-bottom:6px;">Password</label>
                        <input type="password" wire:model.defer="password" style="width:100%;padding:12px 14px;border-radius:14px;border:1px solid #3f3f46;background:#09090b;color:white;font-size:13px;outline:none;">
                        @error('password') <div style="font-size:11px;color:#fca5a5;margin-top:5px;">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#d4d4d8;margin-bottom:6px;">Konfirmasi Password</label>
                        <input type="password" wire:model.defer="password_confirmation" style="width:100%;padding:12px 14px;border-radius:14px;border:1px solid #3f3f46;background:#09090b;color:white;font-size:13px;outline:none;">
                    </div>

                    <button type="submit" style="width:100%;padding:12px 16px;border:none;border-radius:14px;background:#16a34a;color:white;font-size:14px;font-weight:700;cursor:pointer;">
                        Kirim OTP via WhatsApp
                    </button>
                </form>
            @endif

            @if ($step === 3 && $kader)
                <div style="text-align:center;margin-bottom:20px;">
                    <div style="font-size:16px;font-weight:700;color:white;">Verifikasi Kode OTP</div>
                    <div style="font-size:12px;color:#a1a1aa;margin-top:6px;line-height:1.45;">
                        Kode OTP telah dikirim via WhatsApp ke nomor:<br>
                        <strong style="color:#f97316;font-size:14px;">{{ $this->getMaskedPhone() }}</strong>
                    </div>
                </div>

                @if (session()->has('successMsg'))
                    <div style="margin-bottom:16px;padding:12px 14px;border-radius:14px;background:rgba(22,163,74,0.18);border:1px solid rgba(74,222,128,0.28);color:#86efac;font-size:12px;">
                        {{ session('successMsg') }}
                    </div>
                @endif

                @if ($errorMsg !== '')
                    <div style="margin-bottom:16px;padding:12px 14px;border-radius:14px;background:rgba(127,29,29,0.22);border:1px solid rgba(248,113,113,0.35);color:#fca5a5;font-size:12px;">
                        {{ $errorMsg }}
                    </div>
                @endif

                <form wire:submit.prevent="verifikasiOtp" style="display:grid;gap:16px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#d4d4d8;margin-bottom:6px;text-align:center;text-transform:uppercase;letter-spacing:0.05em;">Masukkan 6 Digit Kode OTP</label>
                        <input type="text" inputmode="numeric" maxlength="6" wire:model.defer="otpInput" placeholder="------" style="width:100%;padding:14px;border-radius:14px;border:1px solid #3f3f46;background:#09090b;color:white;font-size:24px;font-weight:800;letter-spacing:8px;text-align:center;outline:none;">
                    </div>

                    <button type="submit" style="width:100%;padding:14px 16px;border:none;border-radius:14px;background:#f97316;color:white;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 8px 20px rgba(249,115,22,0.2);">
                        Verifikasi & Aktivasi
                    </button>
                </form>

                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:20px;font-size:12px;">
                    <button type="button" wire:click="resendOtp" style="background:none;border:none;color:#a1a1aa;cursor:pointer;font-weight:600;padding:4px 0;outline:none;">
                        Kirim Ulang OTP
                    </button>
                    <button type="button" wire:click="$set('step', 2)" style="background:none;border:none;color:#a1a1aa;cursor:pointer;font-weight:600;padding:4px 0;outline:none;">
                        ← Kembali ke Form
                    </button>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.nia-input').forEach((input, i, inputs) => {
                input.addEventListener('input', () => {
                    const maxLen = Number(input.dataset.niaLen || input.maxLength || 0);
                    input.value = input.value.replace(/\D/g, '').slice(0, maxLen);
                    if (input.value.length >= maxLen && i < inputs.length - 1) {
                        inputs[i + 1].focus();
                    }
                });

                input.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        input.form?.requestSubmit();
                        return;
                    }

                    if (event.key !== 'Backspace' || input.value.length > 0 || i === 0) {
                        return;
                    }

                    event.preventDefault();
                    inputs[i - 1].focus();

                    const prevInput = inputs[i - 1];
                    const prevLength = prevInput.value.length;
                    prevInput.setSelectionRange(prevLength, prevLength);
                });
            });
        });
    </script>
</div>
