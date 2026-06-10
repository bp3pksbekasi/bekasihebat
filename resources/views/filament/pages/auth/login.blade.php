<div class="login-wrap">
    <!-- Load Tailwind CSS, Fonts, and Tabler Icons -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        .login-wrap{min-height:100vh;position:relative;overflow:hidden;background:linear-gradient(135deg,#0a0e1a 0%,#0d1424 40%,#111827 100%);font-family:'Plus Jakarta Sans',sans-serif;}
        .login-grid-bg{position:absolute;inset:0;opacity:.04;background-image:linear-gradient(rgba(255,255,255,.5) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.5) 1px,transparent 1px);background-size:40px 40px}
        .login-glow1{position:absolute;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(234,88,12,.08) 0%,transparent 70%);top:-150px;right:-150px}
        .login-glow2{position:absolute;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(37,99,235,.06) 0%,transparent 70%);bottom:-100px;left:-100px}
        .login-content{position:relative;z-index:2;display:grid;grid-template-columns:1.35fr 0.65fr;min-height:100vh}
        .login-left{padding:48px;display:flex;flex-direction:column;justify-content:center}
        .login-right{padding:48px;display:flex;align-items:center;justify-content:center}
        .mono{font-family:'JetBrains Mono',monospace}
        .stat-card{background:rgba(255,255,255,.03);border:0.5px solid rgba(255,255,255,.06);border-radius:10px;padding:14px}
        .stat-dot{width:6px;height:6px;border-radius:50%}
        
        /* Filament form design system overrides to match custom theme */
        .filament-form-container .fi-input-wrp {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 0.5px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 10px !important;
            box-shadow: none !important;
            transition: border-color .2s !important;
            overflow: hidden !important;
        }
        .filament-form-container .fi-input-wrp:focus-within {
            border-color: rgba(234, 88, 12, 0.5) !important;
            box-shadow: none !important;
        }
        .filament-form-container .fi-input-wrp input {
            background: transparent !important;
            border: none !important;
            color: #ffffff !important;
            height: 44px !important;
            font-size: 14px !important;
            box-shadow: none !important;
            outline: none !important;
            padding-left: 14px !important;
        }
        .filament-form-container label {
            color: #a1a1aa !important; /* slate-400 */
            font-size: 12px !important;
            font-weight: 500 !important;
            display: block !important;
            margin-bottom: 5px !important;
        }
        .filament-form-container label span,
        .filament-form-container .fi-checkbox-label {
            color: #a1a1aa !important;
            font-size: 12px !important;
            font-weight: 500 !important;
        }
        .filament-form-container .fi-checkbox-input {
            width: 15px !important;
            height: 15px !important;
            accent-color: #ea580c !important;
            border-radius: 4px !important;
        }
        .filament-form-container .fi-fo-field-wrp-error-message {
            color: #f87171 !important; /* red-400 */
            font-size: 11px !important;
            margin-top: 4px !important;
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
        }
        
        .login-btn-filament {
            width: 100%;
            padding: 12px;
            background: #ea580c;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background .2s, transform 0.1s;
        }
        .login-btn-filament:hover {
            background: #dc4b06;
        }
        .login-btn-filament:active {
            transform: scale(.99);
        }
        @media(max-width:768px){
            .login-content{grid-template-columns:1fr}
            .login-left{display:none}
        }
    </style>
    <div class="login-grid-bg"></div>
    <div class="login-glow1"></div>
    <div class="login-glow2"></div>

    <div class="login-content">

        {{-- ======= KIRI: Branding + Live Stats ======= --}}
        <div class="login-left">
            <div style="width:100%;max-width:860px;margin:0 auto;">

                {{-- Logo --}}
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:36px;">
                    <img src="{{ asset('images/logo-hebat.png') }}" alt="Bekasi Hebat" style="height:48px;width:auto;object-fit:contain;">
                </div>

                {{-- Tagline --}}
                <div style="margin-bottom:32px;">
                    <div class="mono" style="font-size:11px;letter-spacing:3px;text-transform:uppercase;color:#ea580c;margin-bottom:8px;">Command center</div>
                    <div style="font-size:28px;font-weight:700;color:white;line-height:1.2;">Pusat kendali<br>pemenangan <span style="color:#ea580c;">2029</span></div>
                    <div style="font-size:14px;color:#71717a;margin-top:10px;line-height:1.7;">Dashboard terintegrasi untuk monitoring, strategi, dan koordinasi seluruh program DPD PKS Kabupaten Bekasi.</div>
                </div>

                {{-- Live Stats --}}
                @php
                    $korweCount = \App\Models\Korwe::count();
                    $kontakCount = \App\Models\KontakWarga::count();
                    $kaderCount = \App\Models\Kader::where('status', 'aktif')->count();
                    $aspirasiCount = 0;
                    try { $aspirasiCount = \App\Models\Aspirasi::count(); } catch (\Exception $e) {}
                    $eventCount = \App\Models\Event::where('status', 'disetujui')->count();
                @endphp

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;width:100%;">
                    <div class="stat-card">
                        <div style="display:flex;align-items:center;gap:5px;margin-bottom:4px;">
                            <div class="stat-dot" style="background:#22c55e;box-shadow:0 0 6px #22c55e;"></div>
                            <span class="mono" style="font-size:10px;color:#71717a;">KORWE aktif</span>
                        </div>
                        <div style="font-size:22px;font-weight:700;color:white;">{{ number_format($korweCount) }}</div>
                        <div class="mono" style="font-size:10px;color:#4ade80;">target 1.248</div>
                    </div>
                    <div class="stat-card">
                        <div style="display:flex;align-items:center;gap:5px;margin-bottom:4px;">
                            <div class="stat-dot" style="background:#ea580c;box-shadow:0 0 6px #ea580c;"></div>
                            <span class="mono" style="font-size:10px;color:#71717a;">Sapa Warga</span>
                        </div>
                        <div style="font-size:22px;font-weight:700;color:white;">{{ number_format($kontakCount) }}</div>
                        <div class="mono" style="font-size:10px;color:#fb923c;">target 446.200</div>
                    </div>
                    <div class="stat-card">
                        <div style="display:flex;align-items:center;gap:5px;margin-bottom:4px;">
                            <div class="stat-dot" style="background:#3b82f6;box-shadow:0 0 6px #3b82f6;"></div>
                            <span class="mono" style="font-size:10px;color:#71717a;">Kader aktif</span>
                        </div>
                        <div style="font-size:22px;font-weight:700;color:white;">{{ number_format($kaderCount) }}</div>
                        <div class="mono" style="font-size:10px;color:#60a5fa;">tersebar 7 dapil</div>
                    </div>
                    <div class="stat-card">
                        <div style="display:flex;align-items:center;gap:5px;margin-bottom:4px;">
                            <div class="stat-dot" style="background:#a855f7;box-shadow:0 0 6px #a855f7;"></div>
                            <span class="mono" style="font-size:10px;color:#71717a;">Event aktif</span>
                        </div>
                        <div style="font-size:22px;font-weight:700;color:white;">{{ $eventCount }}</div>
                        <div class="mono" style="font-size:10px;color:#c084fc;">menunggu peserta</div>
                    </div>
                </div>

                {{-- Target bar --}}
                <div style="width:100%;margin-top:16px;">
                    @php
                        $targetSuara = 350000;
                        $currentProgress = $kontakCount;
                        $pct = min(100, round($currentProgress / max($targetSuara, 1) * 100));
                    @endphp
                    <div style="display:flex;justify-content:space-between;font-size:10px;margin-bottom:4px;">
                        <span class="mono" style="color:#71717a;">Target 350.000 suara</span>
                        <span class="mono" style="color:#ea580c;font-weight:500;">{{ $pct }}%</span>
                    </div>
                    <div style="height:4px;border-radius:2px;background:rgba(255,255,255,.06);overflow:hidden;">
                        <div style="width:{{ $pct }}%;height:100%;border-radius:2px;background:linear-gradient(90deg,#ea580c,#f97316);"></div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ======= KANAN: Login Form ======= --}}
        <div class="login-right">
            <div style="width:100%;max-width:340px;">

                {{-- Header --}}
                <div style="text-align:center;margin-bottom:28px;">
                    <div style="width:56px;height:56px;border-radius:14px;background:rgba(234,88,12,.1);border:0.5px solid rgba(234,88,12,.2);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                        <i class="ti ti-lock" style="font-size:26px;color:#ea580c;"></i>
                    </div>
                    <div style="font-size:18px;font-weight:600;color:white;">Masuk ke dashboard</div>
                    <div style="font-size:13px;color:#71717a;margin-top:4px;">Gunakan email dan password Anda</div>
                </div>

                {{-- Filament Form Wrapper --}}
                <div class="filament-form-container">
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

                    @if ($errors->any())
                        <div style="margin-bottom:16px;padding:12px;border-radius:10px;background:rgba(239,68,68,0.1);border:0.5px solid rgba(239,68,68,0.3);color:#f87171;font-size:12px;display:flex;align-items:flex-start;gap:8px;">
                            <i class="ti ti-alert-circle" style="font-size:16px;margin-top:2px;"></i>
                            <div>
                                <strong style="display:block;margin-bottom:2px;">Gagal Masuk:</strong>
                                <ul style="margin:0;padding-left:16px;list-style-type:disc;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form wire:submit="authenticate" class="space-y-6">
                        {{ $this->form }}

                        <button type="submit" class="login-btn-filament" style="margin-top:4px;" wire:loading.attr="disabled">
                            <span wire:loading.remove class="flex items-center justify-center gap-1.5">
                                <i class="ti ti-login" style="font-size:16px;"></i> Masuk ke command center
                            </span>
                            <span wire:loading class="flex items-center justify-center gap-2">
                                <i class="ti ti-loader-2" style="font-size:16px;animation:spin 1s linear infinite;"></i> Memverifikasi...
                            </span>
                        </button>
                    </form>

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
                </div>

                {{-- Divider --}}
                <div style="display:flex;align-items:center;gap:12px;margin:22px 0;">
                    <div style="flex:1;height:0.5px;background:rgba(255,255,255,.08);"></div>
                    <span style="font-size:11px;color:#52525b;">atau</span>
                    <div style="flex:1;height:0.5px;background:rgba(255,255,255,.08);"></div>
                </div>

                {{-- NIA Activation --}}
                <a href="{{ route('aktivasi') }}" style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:11px;background:rgba(255,255,255,.03);border:0.5px solid rgba(255,255,255,.08);border-radius:10px;font-size:13px;color:#a1a1aa;text-decoration:none;transition:border-color .2s;" onmouseover="this.style.borderColor='rgba(234,88,12,.3)'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)'">
                    <i class="ti ti-id-badge" style="font-size:16px;color:#ea580c;"></i>
                    Aktivasi akun dengan NIA
                </a>

                {{-- Security note --}}
                <div style="text-align:center;margin-top:18px;">
                    <div style="display:flex;align-items:center;justify-content:center;gap:5px;font-size:11px;color:#3f3f46;">
                        <i class="ti ti-shield-check" style="font-size:13px;color:#22c55e;"></i>
                        <span>Akses terbatas untuk kader terverifikasi</span>
                    </div>
                </div>

                {{-- Links --}}
                <div style="display:flex;justify-content:center;gap:20px;margin-top:14px;">
                    <a href="{{ route('public.home') }}" style="font-size:12px;color:#52525b;text-decoration:none;display:flex;align-items:center;gap:3px;">
                        <i class="ti ti-arrow-left" style="font-size:12px;"></i> Kembali ke website
                    </a>
                </div>

            </div>
        </div>

    </div>

    {{-- ======= Bottom Status Bar ======= --}}
    <div style="position:fixed;bottom:0;left:0;right:0;z-index:10;display:flex;align-items:center;justify-content:space-between;padding:8px 24px;background:rgba(0,0,0,.4);border-top:0.5px solid rgba(255,255,255,.05);backdrop-filter:blur(8px);">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="display:flex;align-items:center;gap:5px;">
                <div style="width:5px;height:5px;border-radius:50%;background:#22c55e;animation:pulse 2s infinite;"></div>
                <span class="mono" style="font-size:10px;color:#52525b;">System online</span>
            </div>
            <span class="mono" style="font-size:10px;color:#3f3f46;">v2.0</span>
        </div>
        <div style="display:flex;align-items:center;gap:14px;">
            <span class="mono" style="font-size:10px;color:#3f3f46;">7 dapil · 23 kecamatan · 187 desa · 2.231 RW</span>
            <span class="mono" style="font-size:10px;color:#3f3f46;">|</span>
            <span class="mono" style="font-size:10px;color:#3f3f46;" id="liveClock">--:--:-- WIB</span>
        </div>
    </div>
</div>

{{-- Animations --}}
<style>
@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
</style>

<script>
function updateClock() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    const el = document.getElementById('liveClock');
    if (el) el.textContent = h + ':' + m + ':' + s + ' WIB';
}
updateClock();
setInterval(updateClock, 1000);
</script>
