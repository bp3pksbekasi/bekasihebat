@php
    $statusCfg = $event->status_config;
    $currentApprovalLevel = $event->level_approval;
    $registrations = $event->registrations;
    $registrationStats = [
        'total' => $registrations->count(),
        'confirmed' => $registrations->where('status', 'confirmed')->count(),
        'attended' => $registrations->where('status', 'attended')->count(),
    ];

    $isBidangDpd = $event->org_level === 'dpd' && ! empty($event->bidang_dpd_id);
    $approvalLevels = $isBidangDpd
        ? ['kesekretariatan' => 'Kesekretariatan', 'sekum' => 'Sekum', 'bendum' => 'Bendum', 'ketua_dpd' => 'Ketua DPD']
        : ['dpra' => 'DPRa', 'dpc' => 'DPC', 'dpd' => 'DPD'];

    $isMenunggu = $event->status === \App\Models\Event::STATUS_MENUNGGU;
    $isDraft = in_array($event->status, [\App\Models\Event::STATUS_DRAFT, \App\Models\Event::STATUS_DITOLAK, 'ditolak'], true);
    $isDitolak = in_array($event->status, [\App\Models\Event::STATUS_DITOLAK, 'ditolak'], true);
    $isSelesai = in_array($event->status, [\App\Models\Event::STATUS_SELESAI, 'selesai'], true);
    $isDibatalkan = in_array($event->status, [\App\Models\Event::STATUS_DIBATALKAN, 'dibatalkan'], true);
    $canFillReport = $this->canFillReport;

    $checklist = $this->reportChecklist;
    $checklistDone = collect($checklist)->where('done', true)->count();
    $checklistTotal = count($checklist);
    $reportComplete = $checklistDone === $checklistTotal;

    $currentStep = match (true) {
        $isSelesai => 5,
        $isDraft => 1,
        $isMenunggu => 2,
        $reportComplete => 4,
        default => 3,
    };
    $steps = [1 => 'Draft', 2 => 'Approval', 3 => 'Pelaksanaan', 4 => 'Laporan', 5 => 'Selesai'];

    $rejectedApproval = $event->approvals->where('status', 'rejected')->sortByDesc('decided_at')->first();
    $pesertaCount = $isMenunggu ? 0 : $this->pesertaSummary['total'];

    $fileSize = fn ($bytes) => $bytes >= 1048576
        ? number_format($bytes / 1048576, 1, ',', '.').' MB'
        : number_format(max(1, $bytes / 1024), 0, ',', '.').' KB';

    $card = 'border:0.5px solid #e5e7eb;border-radius:12px;background:white;padding:16px;';
    $eyebrow = 'font-size:11px;color:#fe5000;font-weight:600;letter-spacing:0.8px;text-transform:uppercase;';
    $fieldLabel = 'font-size:12px;color:#374151;font-weight:600;display:block;margin-bottom:4px;';
    $fieldHint = 'font-size:11px;color:#9ca3af;margin-top:3px;';
    $input = 'width:100%;border-radius:8px;border:0.5px solid #d4d4d8;padding:10px 12px;font-size:13px;background:white;box-sizing:border-box;';
    $btnPrimary = 'height:36px;padding:0 16px;border-radius:8px;border:none;background:#fe5000;color:white;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;';
    $btnGhost = 'height:36px;padding:0 14px;border-radius:8px;border:0.5px solid #d4d4d8;background:white;color:#444;font-size:12px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px;';
    $readonlyBox = 'padding:10px 12px;border-radius:8px;background:#f9fafb;border:0.5px solid #f1f5f9;font-size:13px;color:#1f2937;line-height:1.6;white-space:pre-line;';
@endphp

<div style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;box-sizing:border-box;">
    <div style="width:100%;margin:0;box-sizing:border-box;">

        {{-- Header --}}
        <div style="background:#1a1a1a;color:white;padding:16px 20px;border-radius:14px 14px 0 0;">
            <div style="font-size:11px;color:#a3a3a3;">
                <a href="{{ route('events.index') }}" wire:navigate style="color:#a3a3a3;text-decoration:none;">Program</a> &rsaquo; Detail
            </div>
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-top:6px;">
                <div style="min-width:0;">
                    <div style="font-size:19px;font-weight:600;line-height:1.3;">{{ $event->judul }}</div>
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:8px;">
                        <span style="padding:4px 10px;border-radius:999px;background:{{ $statusCfg['bg'] }};color:{{ $statusCfg['color'] }};font-size:11px;font-weight:600;">{{ $statusCfg['label'] }}</span>
                        <span style="padding:4px 10px;border-radius:999px;background:#2a2a2a;color:#fdba74;font-size:11px;font-weight:600;">{{ $event->jenis_label }}</span>
                        <span style="font-size:12px;color:#d4d4d4;"><i class="ti ti-calendar" aria-hidden="true"></i> {{ $event->tanggal_mulai?->format('d M Y, H:i') ?? '-' }}</span>
                        @if ($event->lokasi)
                            <span style="font-size:12px;color:#d4d4d4;"><i class="ti ti-map-pin" aria-hidden="true"></i> {{ $event->lokasi }}</span>
                        @endif
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    @if (\Illuminate\Support\Facades\Route::has('events.print-lpj') && (in_array($event->status, [\App\Models\Event::STATUS_DISETUJUI, \App\Models\Event::STATUS_BERLANGSUNG, \App\Models\Event::STATUS_SELESAI, 'disetujui', 'selesai', 'ongoing', 'berlangsung'], true) || auth()->user()?->isAdmin()))
                        <a href="{{ route('events.print-lpj', $event) }}" target="_blank" style="display:inline-flex;align-items:center;gap:6px;height:34px;padding:0 14px;border-radius:8px;border:0.5px solid #525252;background:#262626;color:white;text-decoration:none;font-size:12px;font-weight:600;">
                            <i class="ti ti-printer" aria-hidden="true"></i> Cetak LPJ
                        </a>
                    @endif
                    @if ($this->canManage)
                        @if (in_array($event->status, ['draft', 'ditolak', \App\Models\Event::STATUS_DITOLAK], true))
                            <a href="{{ route('events.edit', $event) }}" wire:navigate style="display:inline-flex;align-items:center;gap:6px;height:34px;padding:0 14px;border-radius:8px;border:0.5px solid #525252;background:#262626;color:white;text-decoration:none;font-size:12px;">
                                <i class="ti ti-pencil" aria-hidden="true"></i> Edit
                            </a>
                            <button wire:click="submitForApproval" type="button" style="height:34px;padding:0 14px;border-radius:8px;border:none;background:#fe5000;color:white;font-size:12px;font-weight:600;cursor:pointer;">
                                Ajukan Approval
                            </button>
                        @endif
                        @if ($event->status === \App\Models\Event::STATUS_DISETUJUI || $event->status === \App\Models\Event::STATUS_BERLANGSUNG)
                            <button wire:click="setEventStatus('{{ \App\Models\Event::STATUS_SELESAI }}')" wire:confirm="Tandai program ini selesai?" type="button" style="height:34px;padding:0 14px;border-radius:8px;border:none;background:#16a34a;color:white;font-size:12px;font-weight:600;cursor:pointer;">
                                <i class="ti ti-check" aria-hidden="true"></i> Tandai Selesai
                            </button>
                        @endif
                        @if (! $isDibatalkan && ! $isSelesai)
                            <button wire:click="setEventStatus('{{ \App\Models\Event::STATUS_DIBATALKAN }}')" wire:confirm="Batalkan program ini?" type="button" style="height:34px;padding:0 12px;border-radius:8px;border:0.5px solid #7f1d1d;background:transparent;color:#fca5a5;font-size:12px;cursor:pointer;">
                                Batalkan
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div style="background:#fafafa;border:0.5px solid #e5e5e5;border-top:none;padding:16px 20px 20px;border-radius:0 0 14px 14px;">
            @if (session('message'))
                <div style="margin-bottom:14px;padding:10px 12px;border-radius:8px;background:#ecfdf3;border:0.5px solid #bbf7d0;color:#166534;font-size:12px;">
                    {{ session('message') }}
                </div>
            @endif

            {{-- Alur program & langkah berikutnya --}}
            <div style="{{ $card }}margin-bottom:14px;">
                @if ($isDibatalkan)
                    <div style="font-size:13px;color:#6b7280;"><i class="ti ti-ban" aria-hidden="true"></i> Program ini <strong>dibatalkan</strong>.</div>
                @else
                    <div style="display:flex;align-items:center;gap:6px;" class="event-stepper">
                        @foreach ($steps as $number => $label)
                            @php
                                $isDone = $number < $currentStep || ($number === 5 && $isSelesai);
                                $isCurrent = $number === $currentStep && ! $isSelesai;
                            @endphp
                            <div style="display:flex;align-items:center;gap:8px;flex:0 0 auto;">
                                <span style="width:26px;height:26px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;background:{{ $isDone ? '#16a34a' : ($isCurrent ? '#fe5000' : '#f3f4f6') }};color:{{ $isDone || $isCurrent ? 'white' : '#9ca3af' }};">
                                    {!! $isDone ? '&#10003;' : $number !!}
                                </span>
                                <span style="font-size:12px;font-weight:{{ $isCurrent ? '700' : '500' }};color:{{ $isCurrent ? '#1a1a1a' : ($isDone ? '#374151' : '#9ca3af') }};">{{ $label }}</span>
                            </div>
                            @if (! $loop->last)
                                <div style="flex:1 1 20px;min-width:12px;height:2px;border-radius:2px;background:{{ $number < $currentStep ? '#16a34a' : '#e5e7eb' }};"></div>
                            @endif
                        @endforeach
                    </div>

                    <div style="margin-top:14px;padding:10px 12px;border-radius:10px;background:#fff7ed;border:0.5px solid #fed7aa;font-size:12px;color:#7c2d12;line-height:1.6;">
                        <strong>Langkah berikutnya:</strong>
                        @if ($isDitolak)
                            Program ditolak{{ $rejectedApproval?->catatan ? ' dengan catatan: "'.$rejectedApproval->catatan.'"' : '' }}. Perbaiki lewat tombol <strong>Edit</strong>, lalu klik <strong>Ajukan Approval</strong> lagi.
                        @elseif ($isDraft)
                            Periksa kembali data program dan dokumen persiapan, lalu klik <strong>Ajukan Approval</strong>.
                        @elseif ($isMenunggu)
                            Menunggu persetujuan <strong>{{ $approvalLevels[$currentApprovalLevel] ?? strtoupper((string) $currentApprovalLevel) }}</strong>.
                            @if ($isBidangDpd)
                                <button wire:click="$set('tab', 'approval')" type="button" style="border:none;background:none;padding:0;color:#c2410c;font-weight:600;cursor:pointer;font-size:12px;text-decoration:underline;">Lihat status approval</button>
                            @endif
                        @elseif ($isSelesai)
                            Program selesai. Laporan masih bisa dilengkapi, dan LPJ bisa dicetak lewat tombol <strong>Cetak LPJ</strong>.
                        @elseif ($reportComplete)
                            Laporan sudah lengkap. Cetak LPJ untuk diperiksa, lalu klik <strong>Tandai Selesai</strong>.
                        @else
                            Catat peserta, lalu lengkapi laporan kegiatan: ringkasan, foto, serta upload kwitansi dan lembar absensi
                            ({{ $checklistDone }}/{{ $checklistTotal }} lengkap).
                            <button wire:click="$set('tab', 'laporan')" type="button" style="border:none;background:none;padding:0;color:#c2410c;font-weight:600;cursor:pointer;font-size:12px;text-decoration:underline;">Isi laporan</button>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Tab navigasi --}}
            @php
                $tabs = ['ringkasan' => ['Ringkasan', 'ti-info-circle', null]];
                if ($isBidangDpd) {
                    $tabs['approval'] = ['Approval', 'ti-checklist', null];
                }
                $tabs['peserta'] = ['Peserta', 'ti-users', $isMenunggu ? null : number_format($pesertaCount)];
                $tabs['laporan'] = ['Laporan & LPJ', 'ti-file-description', $canFillReport ? $checklistDone.'/'.$checklistTotal : null];
                $activeTab = array_key_exists($tab, $tabs) ? $tab : 'ringkasan';
            @endphp
            <div style="display:flex;gap:4px;border-bottom:1px solid #e5e7eb;margin-bottom:14px;overflow-x:auto;">
                @foreach ($tabs as $key => $tabInfo)
                    @php [$label, $icon, $badge] = $tabInfo; @endphp
                    <button wire:click="$set('tab', '{{ $key }}')" type="button" style="padding:10px 14px;border:none;background:none;cursor:pointer;font-size:13px;white-space:nowrap;display:inline-flex;align-items:center;gap:6px;margin-bottom:-1px;border-bottom:2px solid {{ $activeTab === $key ? '#fe5000' : 'transparent' }};color:{{ $activeTab === $key ? '#1a1a1a' : '#6b7280' }};font-weight:{{ $activeTab === $key ? '600' : '500' }};">
                        <i class="ti {{ $icon }}" aria-hidden="true"></i> {{ $label }}
                        @if ($badge !== null)
                            <span style="padding:1px 7px;border-radius:999px;background:{{ $activeTab === $key ? '#fff7ed' : '#f3f4f6' }};color:{{ $activeTab === $key ? '#c2410c' : '#6b7280' }};font-size:11px;font-weight:600;">{{ $badge }}</span>
                        @endif
                    </button>
                @endforeach
            </div>

            {{-- TAB: Ringkasan --}}
            @if ($activeTab === 'ringkasan')
                <div style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:14px;" class="event-detail-summary-grid">
                    <div style="{{ $card }}">
                        <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:0.6px;">Penyelenggara</div>
                        <div style="font-size:15px;font-weight:600;color:#1a1a1a;margin-top:6px;">{{ $event->penyelenggara ?: '-' }}</div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:4px;">Dibuat oleh {{ $event->creator?->name ?? '-' }}</div>
                    </div>
                    <div style="{{ $card }}">
                        <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:0.6px;">Penanggung Jawab</div>
                        <div style="font-size:15px;font-weight:600;color:#1a1a1a;margin-top:6px;">{{ $event->pic_nama ?: '-' }}</div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:4px;">{{ $event->pic_hp ?: 'No HP belum diisi' }}</div>
                    </div>
                    <div style="{{ $card }}">
                        <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:0.6px;">Waktu</div>
                        <div style="font-size:15px;font-weight:600;color:#1a1a1a;margin-top:6px;">{{ $event->tanggal_mulai?->format('d M Y') ?? '-' }}</div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:4px;">
                            {{ $event->tanggal_mulai?->format('H:i') ?? '' }}
                            @if ($event->tanggal_selesai)
                                s.d. {{ $event->tanggal_selesai->format('d M Y, H:i') }}
                            @endif
                        </div>
                    </div>
                    <div style="{{ $card }}">
                        <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:0.6px;">Anggaran</div>
                        <div style="font-size:15px;font-weight:600;color:#1a1a1a;margin-top:6px;">Rp {{ number_format((float) $event->total_anggaran, 0, ',', '.') }}</div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:4px;">{{ $event->funding_source ? 'Sumber: '.$event->funding_source : 'Sumber dana belum diisi' }}</div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:minmax(0,1.4fr) minmax(0,1fr);gap:14px;" class="event-report-grid">
                    <div style="{{ $card }}">
                        <div style="{{ $eyebrow }}margin-bottom:12px;">Informasi Program</div>
                        @php
                            $infoRows = array_filter([
                                'Deskripsi' => $event->deskripsi,
                                'Lokasi' => collect([$event->lokasi, $event->lokasi_desa, $event->lokasi_kecamatan])->filter()->unique()->implode(', '),
                                'Target Program' => $event->target_program ? \Illuminate\Support\Str::headline($event->target_program) : null,
                                'Sasaran / Syarat Peserta' => $event->requirements,
                                'Kapasitas' => $event->kapasitas > 0 ? number_format($event->kapasitas).' peserta' : 'Tidak dibatasi',
                                'Narasumber / Pihak Terlibat' => $event->speakers,
                                'Catatan Anggaran' => $event->budget_notes,
                            ], fn ($value) => filled($value));
                        @endphp
                        <div style="display:grid;gap:0;">
                            @foreach ($infoRows as $label => $value)
                                <div style="display:grid;grid-template-columns:170px minmax(0,1fr);gap:12px;padding:9px 0;border-bottom:0.5px solid #f3f4f6;" class="event-info-row">
                                    <div style="font-size:12px;color:#6b7280;">{{ $label }}</div>
                                    <div style="font-size:13px;color:#1a1a1a;line-height:1.6;">{{ $value }}</div>
                                </div>
                            @endforeach
                        </div>
                        @if ($event->kegiatanRw)
                            <div style="margin-top:12px;padding:10px 12px;border-radius:10px;background:#eff6ff;border:0.5px solid #bfdbfe;color:#1d4ed8;font-size:12px;">
                                Dibuat dari kegiatan Sisir RW di {{ $event->kegiatanRw->desa }} RW {{ $event->kegiatanRw->nomor_rw }}.
                                <a href="{{ route('sisir-rw.index') }}" wire:navigate style="color:#1d4ed8;font-weight:600;text-decoration:none;">Lihat kegiatan asal</a>
                            </div>
                        @endif
                    </div>

                    <div style="{{ $card }}">
                        <div style="{{ $eyebrow }}margin-bottom:4px;">Dokumen Persiapan</div>
                        <div style="font-size:11px;color:#9ca3af;margin-bottom:12px;">Dilampirkan saat program dibuat. Ubah lewat tombol Edit (saat draft).</div>
                        @php
                            $dokumenPersiapan = [
                                'Surat Permohonan Pencairan' => $event->dokumen_permohonan_pencairan,
                                'Proposal' => $event->dokumen_proposal,
                                'DPA' => $event->dokumen_dpa,
                                'LPJ Sebelumnya' => $event->dokumen_lpj_sebelumnya,
                            ];
                        @endphp
                        <div style="display:grid;gap:6px;">
                            @foreach ($dokumenPersiapan as $label => $path)
                                @if ($path)
                                    <a href="{{ asset('storage/'.$path) }}" target="_blank" style="display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;border:0.5px solid #e5e7eb;background:white;color:#1a1a1a;text-decoration:none;font-size:12px;">
                                        <i class="ti ti-file-text" style="color:#fe5000;font-size:16px;" aria-hidden="true"></i>
                                        <span style="flex:1;">{{ $label }}</span>
                                        <span style="font-size:11px;color:#2563eb;">Lihat</span>
                                    </a>
                                @else
                                    <div style="display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;border:0.5px dashed #e5e7eb;color:#9ca3af;font-size:12px;">
                                        <i class="ti ti-file-off" style="font-size:16px;" aria-hidden="true"></i>
                                        <span style="flex:1;">{{ $label }}</span>
                                        <span style="font-size:11px;">Tidak ada</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- TAB: Approval --}}
            @if ($activeTab === 'approval' && $isBidangDpd)
                <div style="{{ $card }}">
                    <div style="{{ $eyebrow }}">Approval Tracker</div>
                    <div style="font-size:11px;color:#9ca3af;margin-top:2px;">Urutan persetujuan program bidang DPD.</div>
                    <div style="margin-top:16px;display:flex;align-items:flex-start;gap:10px;flex-wrap:wrap;">
                        @foreach ($approvalLevels as $level => $label)
                            @php
                                $approval = $event->approvals->firstWhere('level', $level);
                                $isCurrent = $event->level_approval === $level && $isMenunggu;
                            @endphp
                            <div style="flex:1;min-width:120px;text-align:center;">
                                <div style="width:42px;height:42px;border-radius:50%;margin:0 auto;display:flex;align-items:center;justify-content:center;background:{{ $approval?->status === 'approved' ? '#22c55e' : ($approval?->status === 'rejected' ? '#ef4444' : ($isCurrent ? '#f97316' : '#e5e7eb')) }};color:white;font-size:16px;font-weight:700;">
                                    {{ $approval?->status === 'approved' ? '✓' : ($approval?->status === 'rejected' ? '✕' : ($isCurrent ? '●' : '○')) }}
                                </div>
                                <div style="font-size:12px;font-weight:600;color:#1a1a1a;margin-top:8px;">{{ $label }}</div>
                                <div style="font-size:11px;color:#888;margin-top:2px;">{{ $approval?->approver?->name ?? ($isCurrent ? 'Sedang diproses' : ($approval?->status === 'pending' ? 'Menunggu' : '-')) }}</div>
                                @if ($approval?->decided_at)
                                    <div style="font-size:10px;color:#9ca3af;margin-top:2px;">{{ $approval->decided_at->format('d M Y H:i') }}</div>
                                @endif
                                @if ($approval?->catatan)
                                    <div style="font-size:11px;color:#4b5563;line-height:1.5;margin-top:6px;padding:6px 8px;border-radius:8px;background:#f9fafb;">"{{ $approval->catatan }}"</div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if ($isMenunggu)
                        @php
                            $user = auth()->user();
                            $isKesekretariatan = $user && $user->role === \App\Models\User::ROLE_KESEKRETARIATAN;

                            $levelsToApprove = [];
                            if ($isKesekretariatan) {
                                foreach (array_keys($approvalLevels) as $lvl) {
                                    if ($event->approvals->firstWhere('level', $lvl)?->status === 'pending') {
                                        $levelsToApprove[] = $lvl;
                                    }
                                }
                            } elseif ($this->canApproveLevel($currentApprovalLevel)) {
                                $levelsToApprove[] = $currentApprovalLevel;
                            }
                        @endphp

                        @foreach ($levelsToApprove as $lvl)
                            <div style="margin-top:16px;padding:14px;border-radius:10px;border:0.5px solid #e5e7eb;background:#fafafa;">
                                <div style="font-size:13px;font-weight:600;color:#1a1a1a;">Keputusan {{ $approvalLevels[$lvl] ?? strtoupper($lvl) }}</div>
                                <div style="{{ $fieldHint }}">Catatan wajib diisi bila menolak, agar pengaju tahu apa yang harus diperbaiki.</div>
                                <textarea wire:model="approvalNotes.{{ $lvl }}" rows="3" placeholder="Catatan persetujuan / alasan penolakan" style="{{ $input }}margin-top:8px;resize:vertical;"></textarea>
                                <div style="margin-top:10px;display:flex;gap:8px;">
                                    <button wire:click="approve('{{ $lvl }}')" type="button" style="height:36px;padding:0 16px;border-radius:8px;border:none;background:#16a34a;color:white;font-size:12px;font-weight:600;cursor:pointer;">Setujui</button>
                                    <button wire:click="reject('{{ $lvl }}')" wire:confirm="Tolak program ini?" type="button" style="height:36px;padding:0 16px;border-radius:8px;border:none;background:#dc2626;color:white;font-size:12px;font-weight:600;cursor:pointer;">Tolak</button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endif

            {{-- TAB: Peserta --}}
            @if ($activeTab === 'peserta')
                @if ($isMenunggu)
                    <div style="{{ $card }}text-align:center;color:#6b7280;font-size:13px;padding:28px;">
                        <i class="ti ti-lock" style="font-size:22px;color:#d4d4d8;" aria-hidden="true"></i>
                        <div style="margin-top:6px;">Input peserta tersedia setelah program selesai diproses approval.</div>
                    </div>
                @else
                    @php $pSum = $this->pesertaSummary; @endphp
                    <div style="{{ $card }}">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                            <div>
                                <div style="{{ $eyebrow }}">Peserta Program</div>
                                <div style="font-size:15px;color:#1a1a1a;font-weight:600;margin-top:2px;">
                                    {{ number_format($pSum['total']) }} peserta
                                    <span style="font-size:12px;color:#888;font-weight:400;">· {{ number_format($pSum['unik_rw']) }} RW berbeda</span>
                                </div>
                                <div style="{{ $fieldHint }}">Peserta yang punya RW + No HP otomatis masuk ke Sapa Warga. Jumlahnya juga dipakai sebagai "peserta hadir" di laporan.</div>
                            </div>
                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                @if ($pSum['unsynced'] > 0)
                                    <button wire:click="syncSemuaKeSapaWarga" type="button" style="height:34px;padding:0 12px;border-radius:8px;border:0.5px solid #86efac;background:#f0fdf4;color:#166534;font-size:12px;font-weight:600;cursor:pointer;">
                                        Sync {{ number_format($pSum['unsynced']) }} ke Sapa Warga
                                    </button>
                                @endif
                                @if ($this->canManage && ! $showPesertaForm)
                                    <button wire:click="$set('showPesertaForm', true)" type="button" style="{{ $btnPrimary }}">
                                        <i class="ti ti-user-plus" aria-hidden="true"></i> Input Peserta
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if ($pSum['per_rw']->isNotEmpty())
                            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:12px;">
                                @foreach ($pSum['per_rw'] as $rw => $count)
                                    @php
                                        $badgeBg = $count >= 10 ? '#dcfce7' : ($count >= 5 ? '#fef3c7' : '#f4f4f5');
                                        $badgeText = $count >= 10 ? '#166534' : ($count >= 5 ? '#b45309' : '#71717a');
                                    @endphp
                                    <span style="padding:4px 8px;border-radius:999px;background:{{ $badgeBg }};color:{{ $badgeText }};font-size:10px;font-weight:600;">
                                        RW {{ $rw === '?' ? '?' : $rw }}: {{ number_format($count) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if ($showPesertaForm)
                            <div style="margin-top:14px;padding:14px;border-radius:12px;border:0.5px solid #fed7aa;background:#fff7ed;">
                                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                                    <div style="display:inline-flex;gap:6px;padding:4px;border-radius:10px;background:white;border:0.5px solid #fed7aa;">
                                        <button wire:click="$set('pesertaTab', 'bulk')" type="button" style="padding:7px 12px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;background:{{ $pesertaTab === 'bulk' ? '#fe5000' : 'transparent' }};color:{{ $pesertaTab === 'bulk' ? 'white' : '#71717a' }};">
                                            Banyak sekaligus (paste)
                                        </button>
                                        <button wire:click="$set('pesertaTab', 'single')" type="button" style="padding:7px 12px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;background:{{ $pesertaTab === 'single' ? '#fe5000' : 'transparent' }};color:{{ $pesertaTab === 'single' ? 'white' : '#71717a' }};">
                                            Satu per satu
                                        </button>
                                    </div>
                                    <button wire:click="$set('showPesertaForm', false)" type="button" style="border:none;background:none;color:#9a3412;font-size:12px;cursor:pointer;">Tutup</button>
                                </div>

                                @if ($pesertaTab === 'bulk')
                                    <div style="font-size:12px;color:#7c2d12;margin-top:12px;line-height:1.6;">
                                        <strong>Cara:</strong> pilih wilayah default, lalu paste daftar peserta (misalnya dari Excel atau WhatsApp). Satu baris untuk satu orang, dengan format
                                        <code style="background:white;padding:1px 5px;border-radius:4px;">nama, RW, no HP, aspirasi</code>. RW, no HP, dan aspirasi boleh dikosongkan.
                                    </div>
                                    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-top:10px;" class="event-peserta-grid">
                                        <div>
                                            <label style="{{ $fieldLabel }}">Dapil</label>
                                            <select wire:model.live="bulkDefaultDapil" style="{{ $input }}height:38px;padding:0 10px;">
                                                <option value="">Semua dapil</option>
                                                @foreach ($this->bulkDapilOptions as $dapil)
                                                    <option value="{{ $dapil }}">{{ $dapil }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label style="{{ $fieldLabel }}">Kecamatan</label>
                                            <select wire:model.live="bulkDefaultKecamatan" style="{{ $input }}height:38px;padding:0 10px;">
                                                <option value="">Pilih kecamatan</option>
                                                @foreach ($this->bulkKecamatanOptions as $kecamatan)
                                                    <option value="{{ $kecamatan }}">{{ $kecamatan }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label style="{{ $fieldLabel }}">Desa</label>
                                            <select wire:model.live="bulkDefaultDesa" style="{{ $input }}height:38px;padding:0 10px;">
                                                <option value="">Pilih desa</option>
                                                @foreach ($this->bulkDesaOptions as $desaOption)
                                                    <option value="{{ $desaOption->id }}">{{ $desaOption->desa }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <label style="{{ $fieldLabel }}margin-top:10px;">Daftar peserta</label>
                                    <textarea wire:model.live.debounce.500ms="bulkPesertaText" rows="6" placeholder="Ahmad Fauzi, 003, 08123456789, Perbaikan drainase gang utama&#10;Siti Nurhaliza, 008, 08567891234&#10;Pak Rohman, 012, 08789012345, Penerangan jalan lingkungan" style="{{ $input }}border-color:#fdba74;line-height:1.6;resize:vertical;"></textarea>

                                    @if (count($bulkPesertaParsed) > 0)
                                        <div style="margin-top:10px;padding:10px;border-radius:10px;border:0.5px solid #e5e7eb;background:white;max-height:200px;overflow-y:auto;">
                                            <div style="font-size:11px;color:#888;margin-bottom:6px;">Pratinjau: {{ number_format(count($bulkPesertaParsed)) }} peserta terdeteksi</div>
                                            @foreach ($bulkPesertaParsed as $i => $peserta)
                                                <div style="padding:4px 6px;border-radius:6px;font-size:12px;background:{{ $i % 2 === 1 ? '#fafafa' : 'transparent' }};">
                                                    <strong>{{ $peserta['nama'] }}</strong>
                                                    <span style="color:#888;">· RW {{ $peserta['rw'] ?: '?' }} · {{ $peserta['hp'] ?: 'tanpa HP' }}</span>
                                                    @if (! empty($peserta['aspirasi']))
                                                        <div style="color:#444;margin-top:2px;">{{ \Illuminate\Support\Str::limit($peserta['aspirasi'], 80) }}</div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div style="margin-top:10px;display:flex;justify-content:flex-end;">
                                        <button wire:click="simpanBulkPeserta" type="button" style="{{ $btnPrimary }}{{ count($bulkPesertaParsed) === 0 ? 'opacity:.5;pointer-events:none;' : '' }}">
                                            Simpan {{ number_format(count($bulkPesertaParsed)) }} peserta
                                        </button>
                                    </div>
                                @else
                                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:12px;" class="event-peserta-grid">
                                        <div>
                                            <label style="{{ $fieldLabel }}">Nama <span style="color:#dc2626;">*</span></label>
                                            <input wire:model="spNama" type="text" placeholder="Nama lengkap" style="{{ $input }}height:38px;">
                                            @error('spNama') <span style="color:#ef4444;font-size:11px;">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label style="{{ $fieldLabel }}">No HP / WA</label>
                                            <input wire:model="spHp" type="text" placeholder="08xxxxxxxxxx" style="{{ $input }}height:38px;">
                                        </div>
                                    </div>
                                    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-top:8px;" class="event-peserta-grid">
                                        <div>
                                            <label style="{{ $fieldLabel }}">Desa</label>
                                            <select wire:model="spDesaId" style="{{ $input }}height:38px;padding:0 10px;">
                                                <option value="">Pilih desa</option>
                                                @foreach ($this->singleDesaOptions as $desaOption)
                                                    <option value="{{ $desaOption->id }}">{{ $desaOption->desa }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label style="{{ $fieldLabel }}">RW</label>
                                            <input wire:model="spRw" type="text" placeholder="contoh: 003" style="{{ $input }}height:38px;">
                                        </div>
                                        <div>
                                            <label style="{{ $fieldLabel }}">RT <span style="color:#9ca3af;font-weight:400;">(opsional)</span></label>
                                            <input wire:model="spRt" type="text" placeholder="contoh: 001" style="{{ $input }}height:38px;">
                                        </div>
                                    </div>
                                    <label style="{{ $fieldLabel }}margin-top:8px;">Aspirasi <span style="color:#9ca3af;font-weight:400;">(opsional, otomatis dibuat di menu Aspirasi)</span></label>
                                    <textarea wire:model="spAspirasi" rows="3" placeholder="Aspirasi atau usulan peserta" style="{{ $input }}resize:vertical;"></textarea>
                                    <div style="margin-top:10px;display:flex;justify-content:flex-end;">
                                        <button wire:click="simpanSinglePeserta" type="button" style="{{ $btnPrimary }}">Simpan Peserta</button>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div style="margin-top:14px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;font-size:11px;color:#888;">
                            <span style="font-weight:600;color:#374151;font-size:12px;">Daftar peserta</span>
                            <span>
                                <span style="display:inline-flex;align-items:center;gap:5px;"><span style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;"></span> sudah di Sapa Warga</span>
                                <span style="display:inline-flex;align-items:center;gap:5px;margin-left:12px;"><span style="width:8px;height:8px;border-radius:50%;background:#d4d4d8;display:inline-block;"></span> belum</span>
                            </span>
                        </div>

                        <div style="display:grid;gap:10px;margin-top:10px;">
                            @forelse ($this->pesertaList->groupBy(fn ($row) => $row->nomor_rw ?: '?') as $rw => $members)
                                <div>
                                    <div style="font-size:11px;color:#666;font-weight:600;margin-bottom:6px;">
                                        {{ $rw === '?' ? 'RW belum diketahui' : 'RW '.$rw }} ({{ number_format($members->count()) }} orang)
                                    </div>
                                    <div style="display:grid;gap:4px;">
                                        @foreach ($members as $peserta)
                                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:8px 10px;border-radius:10px;background:{{ $loop->even ? '#fafafa' : 'white' }};border:0.5px solid #f1f5f9;">
                                                <span style="width:8px;height:8px;border-radius:50%;background:{{ $peserta->synced_sapa_warga ? '#22c55e' : '#d4d4d8' }};display:inline-block;flex:0 0 auto;"></span>
                                                <span style="font-size:12px;font-weight:600;color:#1a1a1a;flex:1 1 180px;">{{ $peserta->nama }}</span>
                                                <span style="font-size:11px;color:#888;">{{ $peserta->no_hp ?? '-' }}</span>
                                                @if ($peserta->nomor_rt)
                                                    <span style="padding:3px 6px;border-radius:999px;background:#f4f4f5;color:#666;font-size:10px;">RT {{ $peserta->nomor_rt }}</span>
                                                @endif
                                                @if ($peserta->aspirasi)
                                                    <span style="padding:3px 6px;border-radius:999px;background:#ecfeff;color:#0369a1;font-size:10px;">Ada aspirasi</span>
                                                @endif
                                                <span style="font-size:10px;color:#999;text-transform:uppercase;">{{ $peserta->metode }}</span>
                                                @if ($this->canManage)
                                                    <button wire:click="hapusPeserta('{{ $peserta->id }}')" wire:confirm="Hapus peserta {{ $peserta->nama }}?" type="button" title="Hapus peserta" style="padding:4px 6px;border:none;background:transparent;color:#a3a3a3;cursor:pointer;">
                                                        <i class="ti ti-x" style="font-size:12px;" aria-hidden="true"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div style="padding:22px;border-radius:10px;border:0.5px dashed #d4d4d8;background:#fafafa;text-align:center;font-size:12px;color:#888;">
                                    Belum ada peserta. @if ($this->canManage) Klik <strong>Input Peserta</strong> untuk mulai mencatat.@endif
                                </div>
                            @endforelse
                        </div>
                    </div>

                    @if ($event->is_public)
                        <div style="{{ $card }}margin-top:14px;">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                                <div>
                                    <div style="{{ $eyebrow }}">Pendaftaran Online</div>
                                    <div style="font-size:13px;color:#1a1a1a;font-weight:600;margin-top:2px;">Peserta yang mendaftar lewat website publik</div>
                                </div>
                                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                    <span style="padding:4px 8px;border-radius:999px;background:#fafafa;font-size:11px;color:#666;">Daftar {{ $registrationStats['total'] }}</span>
                                    <span style="padding:4px 8px;border-radius:999px;background:#eff6ff;font-size:11px;color:#2563eb;">Confirmed {{ $registrationStats['confirmed'] }}</span>
                                    <span style="padding:4px 8px;border-radius:999px;background:#ecfdf3;font-size:11px;color:#166534;">Hadir {{ $registrationStats['attended'] }}</span>
                                </div>
                            </div>
                            <div style="margin-top:12px;overflow-x:auto;">
                                <table style="width:100%;border-collapse:collapse;font-size:12px;">
                                    <thead style="background:#fafafa;">
                                        <tr style="border-bottom:0.5px solid #e5e7eb;">
                                            <th style="padding:10px 12px;text-align:left;font-size:10px;color:#666;text-transform:uppercase;">Nama</th>
                                            <th style="padding:10px 12px;text-align:left;font-size:10px;color:#666;text-transform:uppercase;">Email</th>
                                            <th style="padding:10px 12px;text-align:center;font-size:10px;color:#666;text-transform:uppercase;">Status</th>
                                            <th style="padding:10px 12px;text-align:left;font-size:10px;color:#666;text-transform:uppercase;">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($registrations as $registration)
                                            <tr style="border-bottom:0.5px solid #f1f5f9;">
                                                <td style="padding:10px 12px;color:#1a1a1a;">{{ $registration->user?->name ?? '-' }}</td>
                                                <td style="padding:10px 12px;color:#666;">{{ $registration->user?->email ?? '-' }}</td>
                                                <td style="padding:10px 12px;text-align:center;color:#666;">{{ ucfirst($registration->status) }}</td>
                                                <td style="padding:10px 12px;color:#666;">{{ $registration->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" style="padding:18px;text-align:center;font-size:12px;color:#888;background:#fafafa;">Belum ada peserta terdaftar.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endif
            @endif

            {{-- TAB: Laporan & LPJ --}}
            @if ($activeTab === 'laporan')
                @if ($isMenunggu || ! $canFillReport)
                    <div style="{{ $card }}text-align:center;color:#6b7280;font-size:13px;padding:28px;">
                        <i class="ti ti-lock" style="font-size:22px;color:#d4d4d8;" aria-hidden="true"></i>
                        <div style="margin-top:6px;">Laporan kegiatan bisa diisi setelah program <strong>disetujui</strong>.</div>
                    </div>
                @else
                    @php $canEdit = $this->canManage; @endphp
                    <div style="display:grid;grid-template-columns:minmax(0,1fr) 280px;gap:14px;align-items:start;" class="event-report-grid">
                        <div style="display:grid;gap:14px;">

                            {{-- 1. Ringkasan --}}
                            <div style="{{ $card }}">
                                <div style="{{ $eyebrow }}">1 · Ringkasan Pelaksanaan</div>
                                <div style="{{ $fieldHint }}margin-bottom:12px;">Ceritakan singkat jalannya kegiatan. Teks ini masuk ke bagian B di LPJ.</div>
                                <div style="display:grid;gap:12px;">
                                    <div>
                                        <label style="{{ $fieldLabel }}">Ringkasan kegiatan @if ($canEdit)<span style="color:#dc2626;">*</span>@endif</label>
                                        @if ($canEdit)
                                            <textarea wire:model="reportRingkasan" rows="4" placeholder="Contoh: Kegiatan dihadiri 60 peserta dari 5 DPC, dibuka oleh Ketua DPD, dilanjutkan pertandingan babak penyisihan hingga final." style="{{ $input }}resize:vertical;"></textarea>
                                            @error('reportRingkasan') <span style="color:#ef4444;font-size:11px;">{{ $message }}</span> @enderror
                                        @else
                                            <div style="{{ $readonlyBox }}">{{ $reportRingkasan ?: '-' }}</div>
                                        @endif
                                    </div>
                                    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;" class="event-peserta-grid">
                                        <div>
                                            <label style="{{ $fieldLabel }}">Evaluasi</label>
                                            @if ($canEdit)
                                                <textarea wire:model="reportEvaluasi" rows="3" placeholder="Kendala dan hal yang perlu diperbaiki" style="{{ $input }}resize:vertical;"></textarea>
                                            @else
                                                <div style="{{ $readonlyBox }}">{{ $reportEvaluasi ?: '-' }}</div>
                                            @endif
                                        </div>
                                        <div>
                                            <label style="{{ $fieldLabel }}">Tindak lanjut</label>
                                            @if ($canEdit)
                                                <textarea wire:model="reportTindakLanjut" rows="3" placeholder="Rencana setelah kegiatan" style="{{ $input }}resize:vertical;"></textarea>
                                            @else
                                                <div style="{{ $readonlyBox }}">{{ $reportTindakLanjut ?: '-' }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 2. Angka --}}
                            <div style="{{ $card }}">
                                <div style="{{ $eyebrow }}">2 · Peserta &amp; Anggaran</div>
                                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:12px;" class="event-peserta-grid">
                                    <div>
                                        <label style="{{ $fieldLabel }}">Jumlah peserta hadir</label>
                                        @if ($canEdit)
                                            <input wire:model="reportPesertaHadir" type="number" min="0" style="{{ $input }}height:38px;">
                                            @error('reportPesertaHadir') <span style="color:#ef4444;font-size:11px;">{{ $message }}</span> @enderror
                                        @else
                                            <div style="{{ $readonlyBox }}">{{ number_format($reportPesertaHadir) }} orang</div>
                                        @endif
                                        <div style="{{ $fieldHint }}">Terisi otomatis dari tab Peserta ({{ number_format($pesertaCount) }} tercatat). Boleh diubah.</div>
                                    </div>
                                    <div>
                                        <label style="{{ $fieldLabel }}">Realisasi anggaran</label>
                                        @if ($canEdit)
                                            <div x-data="{
                                                raw: @entangle('reportRealisasiAnggaran'),
                                                formatted: '',
                                                init() {
                                                    this.format();
                                                    $watch('raw', val => {
                                                        if (val != this.parse(this.formatted)) this.format();
                                                    });
                                                },
                                                parse(val) {
                                                    return val ? parseInt(val.replace(/\D/g, '')) : null;
                                                },
                                                format() {
                                                    if (!this.raw && this.raw !== 0) { this.formatted = ''; return; }
                                                    this.formatted = 'Rp' + new Intl.NumberFormat('id-ID').format(this.raw);
                                                },
                                                update(e) {
                                                    let val = e.target.value.replace(/\D/g, '');
                                                    this.raw = val ? parseInt(val) : null;

                                                    let prevFormat = this.formatted;
                                                    this.format();

                                                    if (this.formatted !== prevFormat) {
                                                        e.target.value = this.formatted;
                                                    }
                                                }
                                            }">
                                                <input type="text" x-model="formatted" @input="update" placeholder="Rp0" style="{{ $input }}height:38px;">
                                            </div>
                                            @error('reportRealisasiAnggaran') <span style="color:#ef4444;font-size:11px;display:block;margin-top:2px;">{{ $message }}</span> @enderror
                                        @else
                                            <div style="{{ $readonlyBox }}">Rp {{ number_format((float) $reportRealisasiAnggaran, 0, ',', '.') }}</div>
                                        @endif
                                        <div style="{{ $fieldHint }}">Anggaran direncanakan: Rp {{ number_format((float) $event->total_anggaran, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- 3. Foto --}}
                            <div style="{{ $card }}">
                                <div style="{{ $eyebrow }}">3 · Foto Dokumentasi</div>
                                <div style="{{ $fieldHint }}margin-bottom:12px;">Maksimal 8 foto, masing-masing paling besar 4 MB. Foto baru tersimpan setelah klik <strong>Simpan Laporan</strong>.</div>

                                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                    @foreach ($reportExistingFoto as $index => $foto)
                                        <div style="position:relative;width:96px;height:96px;border-radius:10px;overflow:hidden;background:#f4f4f5;">
                                            <a href="{{ asset('storage/'.$foto) }}" target="_blank"><img src="{{ asset('storage/'.$foto) }}" alt="Foto dokumentasi" style="width:100%;height:100%;object-fit:cover;"></a>
                                            @if ($canEdit)
                                                <button wire:click="removeReportFoto({{ $index }})" wire:confirm="Hapus foto ini?" type="button" title="Hapus foto" style="position:absolute;top:4px;right:4px;width:22px;height:22px;border-radius:50%;border:none;background:rgba(0,0,0,.6);color:white;cursor:pointer;font-size:12px;line-height:22px;padding:0;">&times;</button>
                                            @endif
                                        </div>
                                    @endforeach
                                    @foreach ($reportFoto as $upload)
                                        @if (method_exists($upload, 'isPreviewable') && $upload->isPreviewable())
                                            <div style="position:relative;width:96px;height:96px;border-radius:10px;overflow:hidden;background:#f4f4f5;outline:2px dashed #fdba74;">
                                                <img src="{{ $upload->temporaryUrl() }}" alt="Foto baru" style="width:100%;height:100%;object-fit:cover;opacity:.85;">
                                                <span style="position:absolute;bottom:4px;left:4px;padding:1px 6px;border-radius:999px;background:#fe5000;color:white;font-size:9px;font-weight:600;">Baru</span>
                                            </div>
                                        @endif
                                    @endforeach
                                    @if ($reportExistingFoto === [] && $reportFoto === [] && ! $canEdit)
                                        <div style="font-size:12px;color:#9ca3af;">Belum ada foto.</div>
                                    @endif
                                </div>

                                @if ($canEdit)
                                    <label style="margin-top:10px;display:flex;align-items:center;justify-content:center;gap:8px;padding:14px;border-radius:10px;border:1px dashed #d4d4d8;background:#fafafa;color:#6b7280;font-size:12px;cursor:pointer;">
                                        <i class="ti ti-photo-plus" style="font-size:18px;" aria-hidden="true"></i>
                                        <span wire:loading.remove wire:target="reportFoto">Klik untuk memilih foto</span>
                                        <span wire:loading wire:target="reportFoto">Memuat foto...</span>
                                        <input wire:model="reportFoto" type="file" multiple accept="image/*" style="display:none;">
                                    </label>
                                    @error('reportFoto.*') <span style="color:#ef4444;font-size:11px;display:block;margin-top:4px;">{{ $message }}</span> @enderror
                                    @error('reportFoto') <span style="color:#ef4444;font-size:11px;display:block;margin-top:4px;">{{ $message }}</span> @enderror
                                @endif
                            </div>

                            {{-- 4. Dokumen pendukung --}}
                            <div style="{{ $card }}">
                                <div style="{{ $eyebrow }}">4 · Dokumen Pendukung</div>
                                <div style="{{ $fieldHint }}margin-bottom:12px;">LPJ manual buatan bidang, kwitansi atau nota, lembar absensi, berita acara, bukti transfer, dan lainnya. Format PDF, foto, Word, atau Excel, maksimal 10 MB per file. Dokumen <strong>langsung tersimpan</strong> setelah klik Unggah.</div>

                                @if ($canEdit)
                                    <div style="padding:12px;border-radius:10px;background:#fafafa;border:0.5px solid #e5e7eb;">
                                        <div style="display:grid;grid-template-columns:220px minmax(0,1fr) auto;gap:8px;align-items:end;" class="event-dokumen-grid">
                                            <div>
                                                <label style="{{ $fieldLabel }}">Jenis dokumen</label>
                                                <select wire:model="reportDokumenJenis" style="{{ $input }}height:38px;padding:0 10px;">
                                                    @foreach (\App\Models\EventReport::DOKUMEN_JENIS as $jenisKey => $jenisLabel)
                                                        <option value="{{ $jenisKey }}">{{ $jenisLabel }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label style="{{ $fieldLabel }}">File</label>
                                                <input wire:model="reportDokumenFiles" type="file" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx,.xls,.xlsx" style="{{ $input }}height:38px;padding:7px 10px;font-size:12px;">
                                            </div>
                                            <button wire:click="uploadReportDokumen" wire:loading.attr="disabled" wire:target="reportDokumenFiles,uploadReportDokumen" type="button" style="{{ $btnPrimary }}height:38px;">
                                                <i class="ti ti-upload" aria-hidden="true"></i>
                                                <span wire:loading.remove wire:target="reportDokumenFiles,uploadReportDokumen">Unggah</span>
                                                <span wire:loading wire:target="reportDokumenFiles,uploadReportDokumen">Memproses...</span>
                                            </button>
                                        </div>
                                        @error('reportDokumenFiles') <span style="color:#ef4444;font-size:11px;display:block;margin-top:4px;">{{ $message }}</span> @enderror
                                        @error('reportDokumenFiles.*') <span style="color:#ef4444;font-size:11px;display:block;margin-top:4px;">{{ $message }}</span> @enderror
                                    </div>
                                @endif

                                @php
                                    $dokumenByJenis = collect($reportExistingDokumen)
                                        ->map(fn ($doc, $index) => $doc + ['index' => $index])
                                        ->groupBy('jenis');
                                @endphp
                                <div style="display:grid;gap:12px;margin-top:12px;">
                                    @foreach (\App\Models\EventReport::DOKUMEN_JENIS as $jenisKey => $jenisLabel)
                                        @php
                                            $docs = $dokumenByJenis->get($jenisKey, collect());
                                            $isWajib = in_array($jenisKey, \App\Models\EventReport::DOKUMEN_WAJIB, true);
                                        @endphp
                                        @if ($docs->isNotEmpty() || in_array($jenisKey, \App\Models\EventReport::DOKUMEN_SELALU_TAMPIL, true))
                                            <div>
                                                <div style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;">
                                                    <i class="ti {{ $docs->isNotEmpty() ? 'ti-circle-check' : 'ti-circle-dashed' }}" style="color:{{ $docs->isNotEmpty() ? '#16a34a' : ($isWajib ? '#d97706' : '#9ca3af') }};font-size:15px;" aria-hidden="true"></i>
                                                    {{ $jenisLabel }}
                                                    @if ($isWajib && $docs->isEmpty())
                                                        <span style="padding:1px 7px;border-radius:999px;background:#fef3c7;color:#b45309;font-size:10px;font-weight:600;">Perlu diunggah</span>
                                                    @elseif ($jenisKey === 'lpj_manual' && $docs->isEmpty())
                                                        <span style="font-size:10px;color:#9ca3af;font-weight:400;">Opsional: LPJ yang disusun sendiri oleh bidang (PDF/Word)</span>
                                                    @endif
                                                </div>
                                                <div style="display:grid;gap:4px;">
                                                    @forelse ($docs as $doc)
                                                        @php
                                                            $ext = strtolower(pathinfo($doc['nama'] ?? $doc['path'], PATHINFO_EXTENSION));
                                                            $icon = match (true) {
                                                                $ext === 'pdf' => 'ti-file-type-pdf',
                                                                in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) => 'ti-photo',
                                                                in_array($ext, ['xls', 'xlsx'], true) => 'ti-file-spreadsheet',
                                                                default => 'ti-file-text',
                                                            };
                                                        @endphp
                                                        <div style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;border:0.5px solid #e5e7eb;background:white;">
                                                            <i class="ti {{ $icon }}" style="color:#fe5000;font-size:18px;flex:0 0 auto;" aria-hidden="true"></i>
                                                            <div style="flex:1;min-width:0;">
                                                                <a href="{{ asset('storage/'.$doc['path']) }}" target="_blank" style="font-size:12px;color:#1a1a1a;text-decoration:none;font-weight:500;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $doc['nama'] ?? basename($doc['path']) }}</a>
                                                                <div style="font-size:10px;color:#9ca3af;">
                                                                    {{ isset($doc['ukuran']) ? $fileSize((int) $doc['ukuran']) : '' }}
                                                                    @if (! empty($doc['uploaded_at']))
                                                                        · {{ \Illuminate\Support\Carbon::parse($doc['uploaded_at'])->format('d M Y H:i') }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <a href="{{ asset('storage/'.$doc['path']) }}" target="_blank" style="font-size:11px;color:#2563eb;text-decoration:none;">Lihat</a>
                                                            @if ($canEdit)
                                                                <button wire:click="removeReportDokumen({{ $doc['index'] }})" wire:confirm="Hapus dokumen {{ $doc['nama'] ?? '' }}?" type="button" title="Hapus dokumen" style="padding:4px 6px;border:none;background:transparent;color:#a3a3a3;cursor:pointer;">
                                                                    <i class="ti ti-trash" style="font-size:14px;" aria-hidden="true"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @empty
                                                        <div style="padding:8px 10px;border-radius:8px;border:0.5px dashed #e5e7eb;font-size:11px;color:#9ca3af;">Belum ada file.</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Panel samping: kelengkapan & aksi --}}
                        <div style="display:grid;gap:14px;position:sticky;top:16px;" class="event-report-aside">
                            <div style="{{ $card }}">
                                <div style="{{ $eyebrow }}">Kelengkapan Laporan</div>
                                <div style="display:flex;align-items:baseline;gap:6px;margin-top:8px;">
                                    <span style="font-size:24px;font-weight:700;color:{{ $reportComplete ? '#16a34a' : '#1a1a1a' }};">{{ $checklistDone }}/{{ $checklistTotal }}</span>
                                    <span style="font-size:12px;color:#6b7280;">{{ $reportComplete ? 'lengkap' : 'terisi' }}</span>
                                </div>
                                <div style="height:6px;border-radius:999px;background:#f3f4f6;margin-top:8px;overflow:hidden;">
                                    <div style="height:100%;width:{{ $checklistTotal > 0 ? round($checklistDone / $checklistTotal * 100) : 0 }}%;background:{{ $reportComplete ? '#16a34a' : '#fe5000' }};"></div>
                                </div>
                                <div style="display:grid;gap:6px;margin-top:12px;">
                                    @foreach ($checklist as $item)
                                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:{{ $item['done'] ? '#374151' : '#9ca3af' }};">
                                            <i class="ti {{ $item['done'] ? 'ti-circle-check-filled' : 'ti-circle' }}" style="color:{{ $item['done'] ? '#16a34a' : '#d4d4d8' }};font-size:15px;" aria-hidden="true"></i>
                                            {{ $item['label'] }}
                                        </div>
                                    @endforeach
                                </div>
                                <div style="{{ $fieldHint }}margin-top:10px;">Kelengkapan dihitung dari data yang sudah tersimpan.</div>
                            </div>

                            <div style="{{ $card }}display:grid;gap:8px;">
                                @if ($canEdit)
                                    <button wire:click="saveReport" wire:loading.attr="disabled" wire:target="saveReport,reportFoto" type="button" style="{{ $btnPrimary }}justify-content:center;height:40px;">
                                        <i class="ti ti-device-floppy" aria-hidden="true"></i>
                                        <span wire:loading.remove wire:target="saveReport">Simpan Laporan</span>
                                        <span wire:loading wire:target="saveReport">Menyimpan...</span>
                                    </button>
                                    <div style="{{ $fieldHint }}text-align:center;margin-top:0;">Menyimpan ringkasan, angka, dan foto baru.</div>
                                @else
                                    <div style="font-size:12px;color:#6b7280;text-align:center;">Mode lihat saja</div>
                                @endif
                                @if (\Illuminate\Support\Facades\Route::has('events.print-lpj'))
                                    <a href="{{ route('events.print-lpj', $event) }}" target="_blank" style="{{ $btnGhost }}justify-content:center;height:40px;">
                                        <i class="ti ti-printer" aria-hidden="true"></i> Cetak LPJ Sistem
                                    </a>
                                @endif
                                @foreach (collect($reportExistingDokumen)->where('jenis', 'lpj_manual') as $lpjManual)
                                    <a href="{{ asset('storage/'.$lpjManual['path']) }}" target="_blank" title="{{ $lpjManual['nama'] ?? '' }}" style="{{ $btnGhost }}justify-content:center;height:40px;">
                                        <i class="ti ti-file-download" aria-hidden="true"></i> LPJ Manual{{ $loop->count > 1 ? ' '.$loop->iteration : '' }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <style>
        @media (max-width: 1100px) {
            .event-detail-summary-grid,
            .event-peserta-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }

            .event-report-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }

            .event-report-aside {
                position: static !important;
            }
        }

        @media (max-width: 760px) {
            .event-detail-summary-grid,
            .event-peserta-grid,
            .event-dokumen-grid,
            .event-info-row {
                grid-template-columns: minmax(0, 1fr) !important;
            }

            .event-stepper span + span {
                display: none;
            }
        }
    </style>
</div>
