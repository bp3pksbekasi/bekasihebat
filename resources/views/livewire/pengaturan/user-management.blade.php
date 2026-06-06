@php
    $kpi = $this->kpi;
    $roleCounts = [
        \App\Models\User::ROLE_ADMIN => (int) ($kpi['perRole'][\App\Models\User::ROLE_ADMIN] ?? 0),
        \App\Models\User::ROLE_BIDANG => (int) ($kpi['perRole'][\App\Models\User::ROLE_BIDANG] ?? 0),
        \App\Models\User::ROLE_KADER => (int) ($kpi['perRole'][\App\Models\User::ROLE_KADER] ?? 0),
    ];
    $roleLabels = [
        \App\Models\User::ROLE_ADMIN => 'Admin DPD',
        \App\Models\User::ROLE_BIDANG => 'Pengurus Bidang',
        \App\Models\User::ROLE_KADER => 'Kader',
    ];
    $roleColors = [
        \App\Models\User::ROLE_ADMIN => '#2563eb',
        \App\Models\User::ROLE_BIDANG => '#7c3aed',
        \App\Models\User::ROLE_KADER => '#64748b',
    ];
    $roleMax = max(max($roleCounts), 1);
@endphp

<div data-flux-main style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;">
    <div style="background:#1a1a1a;color:white;padding:12px 20px;border-radius:14px 14px 0 0;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;flex:1 1 auto;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:30px;height:30px;border-radius:8px;background:#7c3aed;display:flex;align-items:center;justify-content:center;">
                    <i class="ti ti-users-cog" style="font-size:16px;color:white;" aria-hidden="true"></i>
                </div>
                <div style="font-size:14px;font-weight:600;">Kelola User</div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;flex:1 1 auto;">
                <div style="font-size:12px;color:#d4d4d8;font-weight:500;">Filter :</div>
                <select wire:model.live="filterRole" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#f5f3ff;color:#6d28d9;font-weight:500;">
                    <option value="">Semua role</option>
                    <option value="{{ \App\Models\User::ROLE_ADMIN }}">Admin DPD</option>
                    <option value="{{ \App\Models\User::ROLE_BIDANG }}">Pengurus Bidang</option>
                    <option value="{{ \App\Models\User::ROLE_KADER }}">Kader</option>
                </select>
                <select wire:model.live="filterBidang" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;">
                    <option value="">Semua bidang</option>
                    @foreach ($this->bidangOptions as $bidang)
                        <option value="{{ $bidang['slug'] }}">{{ $bidang['label'] }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterStatus" style="padding:5px 28px 5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;">
                    <option value="">Semua status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari NIA atau nama..." style="min-width:240px;padding:5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#27272a;color:#f4f4f5;">
                <button type="button" wire:click="resetFilters" style="padding:5px 10px;border:0.5px solid #3f3f46;border-radius:6px;font-size:12px;background:#18181b;color:#f4f4f5;cursor:pointer;">Reset</button>
            </div>
        </div>
        <div style="width:26px;height:26px;background:#7c3aed;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex:0 0 auto;">US</div>
    </div>

    <div style="background:white;border:0.5px solid #e5e5e5;border-top:none;border-radius:0 0 14px 14px;overflow:hidden;">
        @if (session()->has('message'))
            <div style="margin:14px 20px 0;padding:10px 12px;border-radius:10px;background:#f5f3ff;border:0.5px solid #ddd6fe;color:#6d28d9;font-size:12px;">
                {{ session('message') }}
            </div>
        @endif

        <div style="padding:18px 20px 0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <h1 style="font-size:20px;font-weight:500;color:#1a1a1a;margin:0;">Kelola User</h1>
                <div style="font-size:12px;color:#666;">Manajemen akun, role, bidang, dan akses modul untuk seluruh pengguna sistem.</div>
            </div>
            <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;flex-wrap:wrap;">
                <div style="font-size:11px;color:#888;">Monitoring aktivasi akun dan pengaturan hak akses user</div>
                <button type="button" wire:click="openForm" style="padding:6px 12px;border:none;border-radius:8px;font-size:12px;font-weight:600;background:#7c3aed;color:white;cursor:pointer;">
                    + Tambah user
                </button>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;padding:18px 20px 0;" class="user-kpi-grid">
            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Total Kader</div>
                <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($kpi['totalKader']) }}</div>
                <div style="font-size:11px;color:#888;margin-top:4px;">Data aktif di database kader</div>
            </div>
            <div style="background:linear-gradient(135deg,#7c3aed,#6d28d9);border-radius:12px;padding:14px;color:white;">
                <div style="font-size:10px;text-transform:uppercase;letter-spacing:0.8px;opacity:.9;">Sudah Aktivasi</div>
                <div style="font-size:28px;font-weight:700;margin-top:6px;">{{ number_format($kpi['sudahAktivasi']) }}</div>
                <div style="font-size:11px;margin-top:4px;opacity:.85;">User aktif yang sudah punya akun</div>
            </div>
            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Belum Aktivasi</div>
                <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($kpi['belumAktivasi']) }}</div>
                <div style="font-size:11px;color:#888;margin-top:4px;">Kader aktif yang belum membuat akun</div>
            </div>
            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                <div style="font-size:10px;color:#888;text-transform:uppercase;letter-spacing:0.8px;">Online Hari Ini</div>
                <div style="font-size:28px;font-weight:700;color:#111827;margin-top:6px;">{{ number_format($kpi['onlineHariIni']) }}</div>
                <div style="font-size:11px;color:#888;margin-top:4px;">User login pada hari ini</div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:minmax(0,1fr);gap:14px;padding:14px 20px 20px;" class="user-main-grid">
            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;align-content:start;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
                    <div>
                        <div style="font-size:10px;color:#7c3aed;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Breakdown Role</div>
                        <div style="font-size:13px;color:#111827;font-weight:600;margin-top:2px;">Komposisi user berdasarkan role utama</div>
                    </div>
                    <button type="button" wire:click="toggleBidangBreakdown" style="border:none;background:none;color:#7c3aed;font-size:11px;font-weight:700;cursor:pointer;">
                        {{ $showBidangBreakdown ? 'Sembunyikan bidang' : 'Lihat per bidang' }}
                    </button>
                </div>

                <div style="display:grid;gap:12px;">
                    @foreach ($roleCounts as $roleKey => $total)
                        <div>
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:6px;">
                                <div style="font-size:12px;font-weight:600;color:#111827;">{{ $roleLabels[$roleKey] }}</div>
                                <div style="font-size:12px;color:#71717a;">{{ number_format($total) }}</div>
                            </div>
                            <div style="height:10px;background:#f3f4f6;border-radius:999px;overflow:hidden;">
                                <div style="height:10px;width:{{ ($total / $roleMax) * 100 }}%;background:{{ $roleColors[$roleKey] }};border-radius:999px;"></div>
                            </div>
                        </div>
                    @endforeach

                    @if ($showBidangBreakdown)
                        <div style="margin-top:8px;padding-top:12px;border-top:0.5px solid #e5e7eb;display:grid;gap:8px;">
                            @foreach ($kpi['perBidangPengurus'] as $slug => $total)
                                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;font-size:12px;">
                                    <div style="color:#111827;">{{ \App\Models\User::BIDANG_OPTIONS[$slug] ?? $slug }}</div>
                                    <div style="color:#7c3aed;font-weight:700;">{{ number_format($total) }}</div>
                                </div>
                            @endforeach
                            @if (count($kpi['perBidangPengurus']) === 0)
                                <div style="font-size:12px;color:#9ca3af;">Belum ada pengurus bidang.</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div style="background:white;border:0.5px solid #e5e5e5;border-radius:12px;padding:14px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
                    <div>
                        <div style="font-size:10px;color:#7c3aed;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Daftar User</div>
                        <div style="font-size:13px;color:#111827;font-weight:600;margin-top:2px;">Aktivasi, role, bidang, dan status akun</div>
                    </div>
                    <div style="font-size:10px;color:#888;">{{ number_format($this->userList->total()) }} user</div>
                </div>

                <div style="overflow:auto;">
                    <table style="width:100%;border-collapse:collapse;min-width:1280px;">
                        <thead>
                            <tr style="border-bottom:0.5px solid #e5e7eb;">
                                <th style="text-align:left;padding:10px 12px;font-size:10px;text-transform:uppercase;color:#71717a;">Status</th>
                                <th style="text-align:left;padding:10px 12px;font-size:10px;text-transform:uppercase;color:#71717a;">NIA</th>
                                <th style="text-align:left;padding:10px 12px;font-size:10px;text-transform:uppercase;color:#71717a;">Nama</th>
                                <th style="text-align:left;padding:10px 12px;font-size:10px;text-transform:uppercase;color:#71717a;">Role</th>
                                <th style="text-align:left;padding:10px 12px;font-size:10px;text-transform:uppercase;color:#71717a;">Bidang</th>
                                <th style="text-align:left;padding:10px 12px;font-size:10px;text-transform:uppercase;color:#71717a;">Dapil</th>
                                <th style="text-align:left;padding:10px 12px;font-size:10px;text-transform:uppercase;color:#71717a;">Jenjang</th>
                                <th style="text-align:left;padding:10px 12px;font-size:10px;text-transform:uppercase;color:#71717a;">Last Login</th>
                                <th style="text-align:left;padding:10px 12px;font-size:10px;text-transform:uppercase;color:#71717a;">Status Akun</th>
                                <th style="text-align:left;padding:10px 12px;font-size:10px;text-transform:uppercase;color:#71717a;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->userList as $user)
                                @php
                                    $onlineToday = $user->last_login_at?->isToday();
                                    $hasLoggedIn = ! is_null($user->last_login_at);
                                    $isActivated = (bool) ($user->kader_is_activated ?? false) || ! empty($user->nia);
                                    $dotColor = $onlineToday ? '#22c55e' : ($hasLoggedIn ? '#f59e0b' : ($isActivated ? '#a3a3a3' : '#9ca3af'));
                                    $roleBadge = match ($user->role) {
                                        \App\Models\User::ROLE_ADMIN => ['bg' => '#dbeafe', 'text' => '#1d4ed8', 'label' => 'Admin DPD'],
                                        \App\Models\User::ROLE_BIDANG => ['bg' => '#ede9fe', 'text' => '#7c3aed', 'label' => 'Pengurus Bidang'],
                                        default => ['bg' => '#f3f4f6', 'text' => '#4b5563', 'label' => 'Kader'],
                                    };
                                    $nia = $user->nia ?: ($user->kader_nia ?? '-');
                                    $jenjangLabel = $user->kader_jenjang ? (\App\Models\Kader::JENJANG_OPTIONS[$user->kader_jenjang]['label'] ?? ucfirst($user->kader_jenjang)) : '-';
                                @endphp
                                <tr style="border-bottom:0.5px solid #f1f5f9;">
                                    <td style="padding:12px;">
                                        <div style="display:flex;align-items:center;gap:8px;">
                                            <span style="display:inline-flex;width:10px;height:10px;border-radius:999px;background:{{ $dotColor }};"></span>
                                            <span style="font-size:11px;color:#71717a;">
                                                {{ $onlineToday ? 'Online hari ini' : ($hasLoggedIn ? 'Pernah login' : ($isActivated ? 'Belum login' : 'Belum aktivasi')) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td style="padding:12px;font-size:12px;font-family:Consolas,Monaco,monospace;color:#111827;">{{ $nia }}</td>
                                    <td style="padding:12px;">
                                        <div style="font-size:12px;font-weight:700;color:#111827;">{{ $user->name }}</div>
                                        <div style="font-size:10px;color:#71717a;margin-top:4px;">{{ $user->email }}</div>
                                    </td>
                                    <td style="padding:12px;">
                                        <span style="display:inline-flex;padding:5px 9px;border-radius:999px;background:{{ $roleBadge['bg'] }};color:{{ $roleBadge['text'] }};font-size:10px;font-weight:700;">
                                            {{ $roleBadge['label'] }}
                                        </span>
                                    </td>
                                    <td style="padding:12px;font-size:11px;color:#525252;">{{ $user->bidang_label }}</td>
                                    <td style="padding:12px;font-size:11px;color:#525252;">{{ $user->dapil ?: '-' }}</td>
                                    <td style="padding:12px;font-size:11px;color:#525252;">{{ $jenjangLabel }}</td>
                                    <td style="padding:12px;font-size:11px;color:#525252;">
                                        {{ $user->last_login_at?->diffForHumans() ?: 'Belum pernah login' }}
                                    </td>
                                    <td style="padding:12px;">
                                        <span style="display:inline-flex;padding:5px 9px;border-radius:999px;background:{{ $user->status === 'aktif' ? '#dcfce7' : '#fee2e2' }};color:{{ $user->status === 'aktif' ? '#166534' : '#b91c1c' }};font-size:10px;font-weight:700;">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td style="padding:12px;">
                                        <div style="display:grid;gap:8px;">
                                            <div style="display:grid;gap:6px;">
                                                <div style="font-size:10px;color:#71717a;text-transform:uppercase;">Akses Modul</div>
                                                @if ($user->isAdmin())
                                                    <div style="font-size:11px;color:#2563eb;font-weight:600;">Semua menu aktif</div>
                                                @else
                                                    <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                                                        @foreach ($this->splitMenuOptions as $menuAccess)
                                                            @php
                                                                $hasMenuAccess = $user->hasMenuPermission($menuAccess['slug']);
                                                            @endphp
                                                            <button
                                                                type="button"
                                                                wire:click="toggleMenuAccess({{ $user->id }}, '{{ $menuAccess['slug'] }}')"
                                                                style="padding:5px 9px;border-radius:999px;border:0.5px solid {{ $hasMenuAccess ? '#fdba74' : '#d4d4d8' }};background:{{ $hasMenuAccess ? '#fff7ed' : 'white' }};color:{{ $hasMenuAccess ? '#c2410c' : '#525252' }};font-size:10px;font-weight:700;cursor:pointer;"
                                                            >
                                                                {{ $menuAccess['label'] }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                                                <select wire:change="ubahRole({{ $user->id }}, $event.target.value)" style="padding:6px 8px;border-radius:8px;border:0.5px solid #d4d4d8;font-size:11px;">
                                                    <option value="{{ \App\Models\User::ROLE_ADMIN }}" @selected($user->role === \App\Models\User::ROLE_ADMIN)>Admin DPD</option>
                                                    <option value="{{ \App\Models\User::ROLE_BIDANG }}" @selected($user->role === \App\Models\User::ROLE_BIDANG)>Pengurus Bidang</option>
                                                    <option value="{{ \App\Models\User::ROLE_KADER }}" @selected($user->role === \App\Models\User::ROLE_KADER)>Kader</option>
                                                </select>

                                                <select wire:change="assignBidang({{ $user->id }}, $event.target.value)" style="padding:6px 8px;border-radius:8px;border:0.5px solid #d4d4d8;font-size:11px;" @disabled($user->role !== \App\Models\User::ROLE_BIDANG)>
                                                    <option value="">Tanpa bidang</option>
                                                    @foreach ($this->bidangOptions as $bidang)
                                                        <option value="{{ $bidang['slug'] }}" @selected($user->bidang_slug === $bidang['slug'])>{{ $bidang['label'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                                                @if ($user->status === 'aktif')
                                                    <button type="button" wire:click="nonaktifkanUser({{ $user->id }})" style="padding:6px 10px;border:none;border-radius:8px;background:#fee2e2;color:#b91c1c;font-size:11px;font-weight:700;cursor:pointer;">
                                                        Nonaktifkan
                                                    </button>
                                                @else
                                                    <button type="button" wire:click="aktifkanUser({{ $user->id }})" style="padding:6px 10px;border:none;border-radius:8px;background:#dcfce7;color:#166534;font-size:11px;font-weight:700;cursor:pointer;">
                                                        Aktifkan
                                                    </button>
                                                @endif

                                                <button type="button" wire:click="resetPassword({{ $user->id }})" style="padding:6px 10px;border:none;border-radius:8px;background:#f5f3ff;color:#6d28d9;font-size:11px;font-weight:700;cursor:pointer;">
                                                    Reset password
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" style="padding:32px;text-align:center;color:#9ca3af;font-size:12px;">Belum ada data user yang sesuai filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:14px;">
                    {{ $this->userList->links('livewire::simple-tailwind') }}
                </div>
            </div>
        </div>
        <div style="margin:0 20px 20px;background:white;border:0.5px solid #e5e7eb;border-radius:12px;overflow:hidden;">
        <button type="button" wire:click="toggleAuditLog" style="width:100%;padding:14px 16px;border:none;background:white;display:flex;align-items:center;justify-content:space-between;gap:12px;cursor:pointer;">
            <div style="text-align:left;">
                <div style="font-size:10px;color:#7c3aed;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Audit Log</div>
                <div style="font-size:13px;color:#111827;font-weight:600;margin-top:2px;">Aktivitas terbaru login, aktivasi, dan perubahan akses</div>
            </div>
            <i class="ti ti-chevron-{{ $showAuditLog ? 'up' : 'down' }}" style="font-size:18px;color:#71717a;" aria-hidden="true"></i>
        </button>

        @if ($showAuditLog)
            <div style="padding:0 16px 16px;">
                <div style="display:flex;justify-content:flex-end;margin-bottom:10px;">
                    <select wire:model.live="filterAction" style="padding:8px 10px;border-radius:10px;border:0.5px solid #d4d4d8;font-size:12px;">
                        <option value="">Semua action</option>
                        @foreach ($this->auditActionOptions as $action)
                            <option value="{{ $action }}">{{ $action }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="overflow:auto;">
                    <table style="width:100%;border-collapse:collapse;min-width:860px;">
                        <thead>
                            <tr style="border-bottom:0.5px solid #e5e7eb;">
                                <th style="text-align:left;padding:10px 12px;font-size:10px;text-transform:uppercase;color:#71717a;">Waktu</th>
                                <th style="text-align:left;padding:10px 12px;font-size:10px;text-transform:uppercase;color:#71717a;">User</th>
                                <th style="text-align:left;padding:10px 12px;font-size:10px;text-transform:uppercase;color:#71717a;">Action</th>
                                <th style="text-align:left;padding:10px 12px;font-size:10px;text-transform:uppercase;color:#71717a;">Deskripsi</th>
                                <th style="text-align:left;padding:10px 12px;font-size:10px;text-transform:uppercase;color:#71717a;">IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->auditLog as $log)
                                <tr style="border-bottom:0.5px solid #f1f5f9;">
                                    <td style="padding:10px 12px;font-size:11px;color:#525252;">{{ $log->created_at?->format('d M Y H:i') }}</td>
                                    <td style="padding:10px 12px;font-size:11px;color:#111827;">{{ $log->user?->name ?: 'Sistem' }}</td>
                                    <td style="padding:10px 12px;font-size:11px;color:#7c3aed;font-weight:700;">{{ $log->action }}</td>
                                    <td style="padding:10px 12px;font-size:11px;color:#525252;">{{ $log->description ?: '-' }}</td>
                                    <td style="padding:10px 12px;font-size:11px;color:#525252;">{{ $log->ip_address ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding:28px;text-align:center;color:#9ca3af;font-size:12px;">Belum ada audit log.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
    </div>

    @if ($showForm)
        <div style="position:fixed;inset:0;background:rgba(15,23,42,0.4);z-index:70;" wire:click="closeForm"></div>
        <div style="position:fixed;top:0;right:0;height:100vh;width:min(440px,100vw);background:white;z-index:71;box-shadow:-12px 0 32px rgba(15,23,42,0.16);display:flex;flex-direction:column;">
            <div style="padding:18px 18px 14px;border-bottom:0.5px solid #e5e7eb;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                <div>
                    <div style="font-size:16px;font-weight:700;color:#111827;">Tambah User</div>
                    <div style="font-size:12px;color:#71717a;margin-top:4px;">Buat akun baru untuk admin, pengurus bidang, atau kader.</div>
                </div>
                <button type="button" wire:click="closeForm" style="width:32px;height:32px;border:none;border-radius:999px;background:#f3f4f6;color:#6b7280;cursor:pointer;">
                    <i class="ti ti-x" aria-hidden="true"></i>
                </button>
            </div>

            <form wire:submit="simpanUser" style="display:flex;flex-direction:column;flex:1;min-height:0;">
                <div style="padding:16px 18px;display:grid;gap:14px;overflow:auto;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#374151;margin-bottom:6px;">Nama</label>
                        <input type="text" wire:model.defer="fName" placeholder="Nama lengkap user" style="width:100%;padding:10px 12px;border:0.5px solid #d1d5db;border-radius:10px;font-size:13px;">
                        @error('fName') <div style="font-size:11px;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;" class="user-form-grid">
                        <div>
                            <label style="display:block;font-size:11px;font-weight:700;color:#374151;margin-bottom:6px;">Email</label>
                            <input type="email" wire:model.defer="fEmail" placeholder="nama@domain.com" style="width:100%;padding:10px 12px;border:0.5px solid #d1d5db;border-radius:10px;font-size:13px;">
                            @error('fEmail') <div style="font-size:11px;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:700;color:#374151;margin-bottom:6px;">No. HP</label>
                            <input type="text" wire:model.defer="fPhone" placeholder="08xxxxxxxxxx" style="width:100%;padding:10px 12px;border:0.5px solid #d1d5db;border-radius:10px;font-size:13px;">
                            @error('fPhone') <div style="font-size:11px;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;" class="user-form-grid">
                        <div>
                            <label style="display:block;font-size:11px;font-weight:700;color:#374151;margin-bottom:6px;">Password awal</label>
                            <input type="password" wire:model.defer="fPassword" placeholder="Minimal 6 karakter" style="width:100%;padding:10px 12px;border:0.5px solid #d1d5db;border-radius:10px;font-size:13px;">
                            @error('fPassword') <div style="font-size:11px;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:700;color:#374151;margin-bottom:6px;">Konfirmasi password</label>
                            <input type="password" wire:model.defer="fPasswordConfirmation" placeholder="Ulangi password" style="width:100%;padding:10px 12px;border:0.5px solid #d1d5db;border-radius:10px;font-size:13px;">
                            @error('fPasswordConfirmation') <div style="font-size:11px;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;" class="user-form-grid">
                        <div>
                            <label style="display:block;font-size:11px;font-weight:700;color:#374151;margin-bottom:6px;">Role</label>
                            <select wire:model.live="fRole" style="width:100%;padding:10px 12px;border:0.5px solid #d1d5db;border-radius:10px;font-size:13px;">
                                <option value="{{ \App\Models\User::ROLE_ADMIN }}">Admin DPD</option>
                                <option value="{{ \App\Models\User::ROLE_BIDANG }}">Pengurus Bidang</option>
                                <option value="{{ \App\Models\User::ROLE_KADER }}">Kader</option>
                            </select>
                            @error('fRole') <div style="font-size:11px;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:11px;font-weight:700;color:#374151;margin-bottom:6px;">Status</label>
                            <select wire:model.defer="fStatus" style="width:100%;padding:10px 12px;border:0.5px solid #d1d5db;border-radius:10px;font-size:13px;">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                            @error('fStatus') <div style="font-size:11px;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#374151;margin-bottom:6px;">Bidang</label>
                        <select wire:model.defer="fBidangSlug" style="width:100%;padding:10px 12px;border:0.5px solid #d1d5db;border-radius:10px;font-size:13px;background:{{ $fRole === \App\Models\User::ROLE_BIDANG ? 'white' : '#f9fafb' }};" @disabled($fRole !== \App\Models\User::ROLE_BIDANG)>
                            <option value="">Tanpa bidang</option>
                            @foreach ($this->bidangOptions as $bidang)
                                <option value="{{ $bidang['slug'] }}">{{ $bidang['label'] }}</option>
                            @endforeach
                        </select>
                        <div style="font-size:11px;color:#9ca3af;margin-top:5px;">Wajib dipilih jika role adalah Pengurus Bidang.</div>
                        @error('fBidangSlug') <div style="font-size:11px;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#374151;margin-bottom:6px;">NIA (opsional)</label>
                        <input type="text" wire:model.defer="fNia" placeholder="32.16.06.10.0065" style="width:100%;padding:10px 12px;border:0.5px solid #d1d5db;border-radius:10px;font-size:13px;">
                        <div style="font-size:11px;color:#9ca3af;margin-top:5px;">Isi jika akun ini sudah punya NIA kader.</div>
                        @error('fNia') <div style="font-size:11px;color:#dc2626;margin-top:5px;">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div style="padding:14px 18px;border-top:0.5px solid #e5e7eb;display:flex;justify-content:flex-end;gap:10px;">
                    <button type="button" wire:click="closeForm" style="padding:10px 14px;border:0.5px solid #d1d5db;border-radius:10px;background:white;color:#374151;font-size:12px;font-weight:600;cursor:pointer;">
                        Batal
                    </button>
                    <button type="submit" style="padding:10px 14px;border:none;border-radius:10px;background:#7c3aed;color:white;font-size:12px;font-weight:700;cursor:pointer;">
                        Simpan user
                    </button>
                </div>
            </form>
        </div>
    @endif

    <style>
        @media (max-width: 1180px) {
            .user-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 820px) {
            .user-form-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }

        @media (max-width: 680px) {
            .user-kpi-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }
    </style>
</div>
