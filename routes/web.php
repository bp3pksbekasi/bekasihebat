<?php

use App\Livewire\BedahDapil\SisirRw as BedahDapilSisirRw;
use App\Livewire\Aspirasi\Index as AspirasiIndex;
use App\Livewire\Auth\AktivasiNia;
use App\Livewire\Dashboard as AdminDashboard;
use App\Livewire\Events\Create as EventsCreate;
use App\Livewire\Events\Detail as EventsDetail;
use App\Livewire\Events\Edit as EventsEdit;
use App\Livewire\Events\Index as EventsIndex;
use App\Livewire\InfraRtRw\Detail as InfraRtRwDetail;
use App\Livewire\InfraRtRw\Index as InfraRtRwIndex;
use App\Livewire\KartuAnggota\Register as KartuAnggotaRegister;
use App\Livewire\Kaderisasi\Index as KaderisasiIndex;
use App\Livewire\Pengaturan\UserManagement as UserManagementIndex;
use App\Livewire\Public\Auth\Login;
use App\Livewire\Public\Dashboard as MemberDashboard;
use App\Livewire\Public\Profile\Complete;
use App\Livewire\PublicSite\Berita as PublicBerita;
use App\Livewire\PublicSite\BeritaDetail as PublicBeritaDetail;
use App\Livewire\PublicSite\AspirasiWarga as PublicAspirasiWarga;
use App\Livewire\PublicSite\EventDetail as PublicEventDetail;
use App\Livewire\PublicSite\Events as PublicEvents;
use App\Livewire\PublicSite\Galeri as PublicGaleri;
use App\Livewire\PublicSite\Home as PublicHome;
use App\Livewire\PublicSite\Tentang as PublicTentang;
use App\Livewire\RkiKsn\Index as RkiKsnIndex;
use App\Livewire\SapaWarga\Index as SapaWargaIndex;
use App\Livewire\SosialMedia\Index as SosialMediaIndex;
use App\Models\AuditLog;
use App\Models\AspirasiReminder;
use App\Models\Event;
use App\Models\PemiluPeriod;
use App\Support\BedahDapil\PemiluSummaryPayload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Public pages
Route::get('/', PublicHome::class)->name('public.home');
Route::get('/events', PublicEvents::class)->name('public.events');
Route::get('/events/{slug}', PublicEventDetail::class)->name('public.events.show');
Route::get('/berita', PublicBerita::class)->name('public.berita');
Route::get('/berita/{slug}', PublicBeritaDetail::class)->name('public.berita.show');
Route::get('/galeri', PublicGaleri::class)->name('public.galeri');
Route::get('/tentang', PublicTentang::class)->name('public.tentang');
Route::get('/aspirasi-warga', PublicAspirasiWarga::class)->name('public.aspirasi');
Route::get('/profil-rw', \App\Livewire\Public\RwProfileForm::class)->name('public.rw-profile');
Route::get('/input-infrastruktur', \App\Livewire\Public\InputInfrastruktur::class)->name('public.input-infrastruktur');
Route::get('/daftar', KartuAnggotaRegister::class)->name('member.register');
Route::get('/ref/{code}', KartuAnggotaRegister::class)->name('member.register.referral');
Route::redirect('/tentang-kami', '/tentang');
Route::redirect('/kegiatan', '/events');
Route::redirect('/home', '/')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::get('/aktivasi', AktivasiNia::class)->name('aktivasi');

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        $userName = auth()->user()?->name;
        AuditLog::log('logout', 'Logout: '.$userName);
        
        // Clear session variable untuk Filament
        session()->forget('logged_in_via_admin');
        
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');

    Route::get('/profile/complete', Complete::class)->name('profile.complete');

    Route::get('/dashboard', AdminDashboard::class)
        ->name('dashboard');

    Route::get('/member/dashboard', MemberDashboard::class)
        ->middleware(['profile.completed', 'role:kader'])
        ->name('member.dashboard');

    Route::get('/bedah-dapil', function () {
        return view('bedah-dapil.index');
    })->middleware('menu:bedah-dapil')->name('bedah-dapil.index');

    Route::get('/bedah-dapil/pemilu-dprd', function (Request $request, PemiluSummaryPayload $payloadBuilder) {
        if (app()->environment('local') && (bool) config('app.debug')) {
            (function () {
                $u = 'http://127.0.0.1:7777/event';
                $s = 'bedah-dapil-redirect';
                $p = base_path('.dbg/bedah-dapil-redirect.env');
                if (is_file($p)) {
                    $e = @file_get_contents($p) ?: '';
                    preg_match('/DEBUG_SERVER_URL=(.+)/', $e, $m1);
                    preg_match('/DEBUG_SESSION_ID=(.+)/', $e, $m2);
                    $u = $m1[1] ?? $u;
                    $s = $m2[1] ?? $s;
                }
                @file_get_contents($u, false, stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'timeout' => 0.25,
                        'ignore_errors' => true,
                        'header' => "Content-Type: application/json\r\n",
                        'content' => json_encode([
                            'sessionId' => $s,
                            'runId' => 'pre-fix',
                            'hypothesisId' => 'D',
                            'location' => 'routes/web.php bedah-dapil.pemilu-dprd',
                            'msg' => '[DEBUG] pemilu dprd route closure reached',
                            'data' => [
                                'session_id' => request()->session()->getId(),
                                'user_id' => optional(auth()->user())->id,
                                'url' => url()->current(),
                            ],
                            'ts' => (int) round(microtime(true) * 1000),
                        ]),
                    ],
                ]));
            })();
        }
        ini_set('memory_limit', '512M');
        $periods = PemiluPeriod::query()
            ->forJenis('dprd')
            ->ordered()
            ->get();

        $selectedPeriod = null;
        if ($periods->isNotEmpty()) {
            $selectedPeriod = $request->filled('period')
                ? $periods->firstWhere('id', $request->string('period')->toString())
                : null;

            $selectedPeriod ??= $periods->firstWhere('tahun', 2024);
            $selectedPeriod ??= $periods->firstWhere('is_default', true);
            $selectedPeriod ??= $periods->first();
        }

        $periodOptions = $periods->map(fn (PemiluPeriod $period): array => [
                'id' => $period->id,
                'tahun' => (int) $period->tahun,
                'label' => $period->label,
                'is_default' => (bool) $period->is_default,
            ])->values()->all();

        if ($periodOptions === []) {
            $periodOptions = [[
                'id' => 'fallback-2024',
                'tahun' => 2024,
                'label' => 'Pemilu DPRD 2024',
                'is_default' => true,
            ]];
        }

        $profilRwMap = \App\Models\ProfilRw::query()
            ->get(['kecamatan', 'desa', 'nomor_rw', 'completion_percent'])
            ->mapWithKeys(function ($item) {
                $key = strtoupper(trim($item->kecamatan) . '|' . trim($item->desa) . '|' . trim($item->nomor_rw));
                return [$key => $item->completion_percent];
            })
            ->toArray();

        $scopeClass = new class {
            use \App\Traits\WithWilayahScope;
        };
        $userScope = $scopeClass->accessScope();

        return view('bedah-dapil.pemilu-dprd', [
            'periodOptions' => $periodOptions,
            'selectedPeriodId' => $selectedPeriod?->id ?? $periodOptions[0]['id'],
            'compiledPayloadJson' => $selectedPeriod ? $payloadBuilder->buildJson($selectedPeriod) : 'null',
            'profilRwMap' => $profilRwMap,
            'userScope' => $userScope,
        ]);
    })->middleware('menu:bedah-dapil')->name('bedah-dapil.pemilu-dprd');

    Route::get('/bedah-dapil/pemilu-dprd/get-profil-rw', function (Request $request) {
        $kecamatan = $request->string('kecamatan')->toString();
        $desa = $request->string('desa')->toString();
        $nomorRw = $request->string('nomor_rw')->toString();
        
        $wilayah = \App\Models\TargetWilayah::query()
            ->where('kecamatan', $kecamatan)
            ->where('desa', $desa)
            ->first();
            
        if (!$wilayah) {
            return response()->json(['error' => 'Wilayah tidak ditemukan'], 404);
        }
        
        $profil = \App\Models\ProfilRw::query()
            ->where('target_wilayah_id', $wilayah->id)
            ->where('nomor_rw', $nomorRw)
            ->first();
            
        return response()->json([
            'profil' => $profil,
            'wilayah_id' => $wilayah->id
        ]);
    })->middleware('menu:bedah-dapil');

    Route::post('/bedah-dapil/pemilu-dprd/save-profil-rw', function (Request $request) {
        $wilayahId = $request->string('wilayah_id')->toString();
        $nomorRw = $request->string('nomor_rw')->toString();
        
        $wilayah = \App\Models\TargetWilayah::find($wilayahId);
        if (!$wilayah) {
            return response()->json(['error' => 'Wilayah tidak ditemukan'], 404);
        }
        
        $data = $request->all();
        unset($data['wilayah_id'], $data['nomor_rw']);
        
        if (isset($data['caleg_terpilih_ada'])) {
            $data['caleg_terpilih_ada'] = filter_var($data['caleg_terpilih_ada'], FILTER_VALIDATE_BOOLEAN);
        }
        
        $payload = array_merge([
            'target_wilayah_id' => $wilayah->id,
            'nomor_rw' => $nomorRw,
            'dapil' => $wilayah->dapil,
            'kecamatan' => $wilayah->kecamatan,
            'desa' => $wilayah->desa,
            'filled_by' => auth()->id(),
            'filled_at' => now(),
            'suara_pks_2019' => 0,
            'jumlah_kta' => 0,
            'caleg_terpilih_ada' => false,
        ], $data);
        
        $profil = \App\Models\ProfilRw::query()->updateOrCreate(
            [
                'target_wilayah_id' => $wilayah->id,
                'nomor_rw' => $nomorRw,
            ],
            $payload
        );
        
        $completion = $profil->calculateCompletion();
        $profil->update([
            'completion_percent' => $completion,
            'is_complete' => $completion >= 80,
        ]);
        
        return response()->json([
            'success' => true,
            'profil' => $profil
        ]);
    })->middleware('menu:bedah-dapil');

    Route::get('/bedah-dapil/peta-wilayah', function () {
        return view('bedah-dapil.peta-wilayah');
    })->middleware('menu:bedah-dapil')->name('bedah-dapil.peta-wilayah');

    Route::get('/bedah-dapil/analisa-caleg', function (Request $request, PemiluSummaryPayload $payloadBuilder) {
        $periods = PemiluPeriod::query()
            ->forJenis('dprd')
            ->ordered()
            ->get();

        $selectedPeriod = null;
        if ($periods->isNotEmpty()) {
            $selectedPeriod = $request->filled('period')
                ? $periods->firstWhere('id', $request->string('period')->toString())
                : null;

            $selectedPeriod ??= $periods->firstWhere('tahun', 2024);
            $selectedPeriod ??= $periods->firstWhere('is_default', true);
            $selectedPeriod ??= $periods->first();
        }

        $periodOptions = $periods->map(fn (PemiluPeriod $period): array => [
            'id' => $period->id,
            'tahun' => (int) $period->tahun,
            'label' => $period->label,
            'is_default' => (bool) $period->is_default,
        ])->values()->all();

        if ($periodOptions === []) {
            $periodOptions = [[
                'id' => 'fallback-2024',
                'tahun' => 2024,
                'label' => 'Pemilu DPRD 2024',
                'is_default' => true,
            ]];
        }

        $rawPayload = $selectedPeriod?->caleg_summary_payload;
        $compiledCalegPayload = is_string($rawPayload) ? json_decode($rawPayload, true) : $rawPayload;
        $compiledCalegPayload ??= [
            'totalRows' => 0,
            'allPartyNames' => [],
            'dapils' => [],
        ];

        // Filter based on user scope
        $scopeClass = new class {
            use \App\Traits\WithWilayahScope;
        };
        $userScope = $scopeClass->accessScope();
        
        if (($userScope['mode'] ?? 'global') === 'dapil' && !empty($userScope['locked_dapil']) && !empty($compiledCalegPayload['dapils'])) {
            $lockedDapilStr = strtoupper((string) $userScope['locked_dapil']);
            if (!str_starts_with($lockedDapilStr, 'BEKASI')) {
                $lockedDapilStr = trim("BEKASI " . $lockedDapilStr);
            }
            $compiledCalegPayload['dapils'] = array_values(array_filter(
                $compiledCalegPayload['dapils'],
                fn($d) => strtoupper((string) $d['dapil']) === $lockedDapilStr
            ));
        }

        $tpsToRwMap = [];
        if ($selectedPeriod) {
            $payload = $payloadBuilder->build($selectedPeriod);
            foreach ($payload['villages'] ?? [] as $v) {
                $dapil = trim(preg_replace('/[^a-zA-Z0-9]+/', ' ', mb_strtoupper((string) ($v['dapil'] ?? ''))));
                $kec = trim(preg_replace('/[^a-zA-Z0-9]+/', ' ', mb_strtoupper((string) ($v['kecamatan'] ?? ''))));
                $desa = trim(preg_replace('/[^a-zA-Z0-9]+/', ' ', mb_strtoupper((string) ($v['desa'] ?? ''))));
                $villageKey = "{$dapil}__{$kec}__{$desa}";
                $tpsMap = [];
                foreach ($v['rw_rows'] ?? [] as $rwRow) {
                    foreach ($rwRow['tps_list'] ?? [] as $tpsName) {
                        $num = preg_replace('/[^\d]/', '', (string) $tpsName);
                        if ($num !== '') {
                            $tpsMap[$num] = $rwRow['rw'];
                        }
                    }
                }
                if ($tpsMap !== []) {
                    $tpsToRwMap[$villageKey] = $tpsMap;
                }
            }
            unset($payload);
        }

        return view('bedah-dapil.analisa-caleg', [
            'periodOptions' => $periodOptions,
            'selectedPeriodId' => $selectedPeriod?->id ?? $periodOptions[0]['id'],
            'compiledCalegPayload' => $compiledCalegPayload,
            'tpsToRwMap' => $tpsToRwMap,
            'userScope' => $userScope,
        ]);
    })->middleware('menu:bedah-dapil')->name('bedah-dapil.analisa-caleg');

    Route::get('/bedah-dapil/rencana-aksi', function () {
        return view('bedah-dapil.rencana-aksi');
    })->middleware('menu:bedah-dapil')->name('bedah-dapil.rencana-aksi');

    Route::get('/sisir-rw', BedahDapilSisirRw::class)->middleware('menu:sisir-rw')->name('sisir-rw.index');
    Route::get('/kaderisasi', KaderisasiIndex::class)->middleware('menu:kaderisasi')->name('kaderisasi.index');
    Route::get('/sapa-warga', SapaWargaIndex::class)->middleware('menu:sapa-warga')->name('sapa-warga.index');
    Route::get('/sosial-media', SosialMediaIndex::class)->middleware('menu:sosial-media')->name('sosial-media.index');
    Route::get('/rki', RkiKsnIndex::class)->defaults('mode', 'rki')->middleware('menu:rki')->name('rki.index');
    Route::get('/ksn', RkiKsnIndex::class)->defaults('mode', 'ksn')->middleware('menu:ksn')->name('ksn.index');
    Route::get('/rki-ksn', function () {
        $user = auth()->user();

        if ($user?->canAccessMenu('rki')) {
            return redirect()->route('rki.index');
        }

        if ($user?->canAccessMenu('ksn')) {
            return redirect()->route('ksn.index');
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    })->middleware('auth')->name('rki-ksn.index');
    Route::redirect('/program-kerja', '/admin/events')->name('program-kerja.index');
    Route::get('/aspirasi', AspirasiIndex::class)->middleware('menu:aspirasi')->name('aspirasi.index');
    Route::get('/aspirasi/reminders/{reminder}/read', function (AspirasiReminder $reminder) {
        abort_unless($reminder->target_user_id === auth()->id() || auth()->user()?->isAdmin(), 403);

        $reminder->update(['is_read' => true]);

        return redirect()->route('aspirasi.index', ['aspirasi' => $reminder->aspirasi_id]);
    })->name('aspirasi.reminders.read');
    Route::redirect('/bedah-dapil/sisir-rw', '/sisir-rw');

    Route::prefix('admin/events')->group(function () {
        Route::get('/', EventsIndex::class)->middleware('menu:event,event-view')->name('events.index');
        Route::get('/create', EventsCreate::class)->middleware('menu:event')->name('events.create');
        Route::get('/{event}', EventsDetail::class)->middleware('menu:event,event-view')->name('events.detail');
        Route::get('/{event}/edit', EventsEdit::class)->middleware('menu:event')->name('events.edit');
    });

    Route::prefix('infra-rtrw')->middleware(['auth'])->group(function () {
        Route::get('/', InfraRtRwIndex::class)->middleware('menu:infra-rtrw')->name('infra-rtrw.index');
        Route::get('/{targetWilayah}', InfraRtRwDetail::class)->middleware('menu:infra-rtrw')->name('infra-rtrw.detail');
    });

    Route::prefix('buku-induk-rw')->middleware(['auth'])->group(function () {
        Route::get('/', \App\Livewire\BukuIndukRw\Index::class)->middleware('menu:infra-rtrw')->name('buku-induk-rw.index');
        Route::get('/{dataRw}', \App\Livewire\BukuIndukRw\Detail::class)->middleware('menu:infra-rtrw')->name('buku-induk-rw.detail');
    });

    Route::get('/pengaturan/users', UserManagementIndex::class)
        ->middleware('role:admin_dpd')
        ->name('pengaturan.users');

    Route::get('/approval-rw', \App\Livewire\ApprovalRw\Index::class)
        ->middleware('role:admin_dpd')
        ->name('approval-rw.index');

    Route::get('/pengaturan/whatsapp', \App\Livewire\Pengaturan\WhapifySettings::class)
        ->middleware('role:admin_dpd')
        ->name('pengaturan.whatsapp');

    Route::get('/pengaturan/rule', \App\Livewire\Pengaturan\RuleManagement::class)
        ->middleware('role:admin_dpd')
        ->name('pengaturan.rule');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->middleware('menu:profil')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->middleware('menu:profil')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->middleware('menu:profil')->name('settings.appearance');
});

require __DIR__.'/auth.php';
