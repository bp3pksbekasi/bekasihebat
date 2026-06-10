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
use App\Livewire\ProgramKerja\Index as ProgramKerjaIndex;
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
        AuditLog::log('logout', 'Logout: '.auth()->user()?->name);
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
        // #region debug-point D:pemilu-dprd-route-hit
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
            @file_get_contents($u, false, stream_context_create(['http' => ['method' => 'POST', 'header' => "Content-Type: application/json\r\n", 'content' => json_encode(['sessionId' => $s, 'runId' => 'pre-fix', 'hypothesisId' => 'D', 'location' => 'routes/web.php bedah-dapil.pemilu-dprd', 'msg' => '[DEBUG] pemilu dprd route closure reached', 'data' => ['session_id' => request()->session()->getId(), 'user_id' => optional(auth()->user())->id, 'url' => url()->current()], 'ts' => (int) round(microtime(true) * 1000)])]]));
        })();
        // #endregion

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

        return view('bedah-dapil.pemilu-dprd', [
            'periodOptions' => $periodOptions,
            'selectedPeriodId' => $selectedPeriod?->id ?? $periodOptions[0]['id'],
            'compiledPayload' => $selectedPeriod ? $payloadBuilder->build($selectedPeriod) : null,
        ]);
    })->middleware('menu:bedah-dapil')->name('bedah-dapil.pemilu-dprd');

    Route::get('/bedah-dapil/peta-wilayah', function () {
        return view('bedah-dapil.peta-wilayah');
    })->middleware('menu:bedah-dapil')->name('bedah-dapil.peta-wilayah');

    Route::get('/bedah-dapil/analisa-caleg', function (Request $request) {
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

        return view('bedah-dapil.analisa-caleg', [
            'periodOptions' => $periodOptions,
            'selectedPeriodId' => $selectedPeriod?->id ?? $periodOptions[0]['id'],
            'compiledCalegPayload' => $selectedPeriod?->caleg_summary_payload ?? [
                'totalRows' => 0,
                'allPartyNames' => [],
                'dapils' => [],
            ],
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
    Route::get('/program-kerja', ProgramKerjaIndex::class)->middleware('menu:program-kerja')->name('program-kerja.index');
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

    Route::get('/pengaturan/users', UserManagementIndex::class)
        ->middleware('role:admin_dpd')
        ->name('pengaturan.users');

    Route::get('/pengaturan/whatsapp', \App\Livewire\Pengaturan\WhapifySettings::class)
        ->middleware('role:admin_dpd')
        ->name('pengaturan.whatsapp');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->middleware('menu:profil')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->middleware('menu:profil')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->middleware('menu:profil')->name('settings.appearance');
});

require __DIR__.'/auth.php';
