<?php

declare(strict_types=1);

namespace App\Livewire\PublicSite;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\TargetWilayah;
use App\Models\User;
use App\Services\MembershipService;
use App\Support\PhoneNormalizer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class EventDetail extends Component
{
    public Event $event;

    public string $regNama = '';

    public string $regHp = '';

    public string $regEmail = '';

    public string $regDapil = '';

    public string $regDesa = '';

    public string $regRw = '';

    public bool $registered = false;

    public function mount(string $slug): void
    {
        $this->event = Event::query()
            ->where('slug', $slug)
            ->where('is_public', true)
            ->whereIn('status', [Event::STATUS_DISETUJUI, Event::STATUS_SELESAI])
            ->withCount('registrations')
            ->firstOrFail();

        // Autofill if user is logged in
        if (auth()->check()) {
            $user = auth()->user();
            $this->regNama = $user->name ?? '';
            $this->regHp = $user->phone ?? '';
            $this->regEmail = $user->email ?? '';
            $this->regDapil = $user->dapil ?? '';
            $this->regDesa = $user->desa ?? '';
            $this->regRw = $user->nomor_rw ?? '';

            // Check if already registered
            $isAlreadyRegistered = EventRegistration::query()
                ->where('event_id', $this->event->id)
                ->where('user_id', $user->id)
                ->exists();
            if ($isAlreadyRegistered) {
                $this->registered = true;
            }
        }
    }

    public function register(MembershipService $membershipService): void
    {
        $validated = $this->validate([
            'regNama' => ['required', 'string', 'max:255'],
            'regHp' => ['required', 'string', 'max:30'],
            'regEmail' => ['nullable', 'email', 'max:255'],
            'regDapil' => ['nullable', 'string', 'max:255'],
            'regDesa' => ['nullable', 'string', 'max:255'],
            'regRw' => ['nullable', 'string', 'max:10'],
        ]);

        $phone = PhoneNormalizer::normalize($validated['regHp']);

        $existingUser = $this->findUserByPhone($phone);
        $duplicate = $existingUser
            ? EventRegistration::query()
                ->where('event_id', $this->event->id)
                ->where('user_id', $existingUser->id)
                ->exists()
            : false;

        if ($duplicate) {
            $this->registered = true;
            session()->flash('message', 'Nomor HP ini sudah terdaftar pada event ini.');

            return;
        }

        $user = $existingUser ?? new User();
        $isNewUser = ! $user->exists;

        if ($isNewUser) {
            $user->fill([
                'name' => $validated['regNama'],
                'email' => $this->resolveRegistrationEmail($validated['regEmail'], $phone),
                'phone' => $phone,
                'password' => Hash::make(Str::password(12)),
                'role' => User::ROLE_KADER,
                'status' => 'aktif',
                'dapil' => $validated['regDapil'] !== '' ? $validated['regDapil'] : null,
                'desa' => $validated['regDesa'] !== '' ? $validated['regDesa'] : null,
                'nomor_rw' => $validated['regRw'] !== '' ? $this->normalizeRw($validated['regRw']) : null,
            ]);
            $user->save();
            $user->assignRole('community_member');
            $membershipService->assignMemberNumber($user);
        } else {
            $user->update([
                'name' => $user->name ?: $validated['regNama'],
                'email' => $user->email ?: $this->resolveRegistrationEmail($validated['regEmail'], $phone),
                'phone' => $user->phone ?: $phone,
                'dapil' => $user->dapil ?: ($validated['regDapil'] !== '' ? $validated['regDapil'] : null),
                'desa' => $user->desa ?: ($validated['regDesa'] !== '' ? $validated['regDesa'] : null),
                'nomor_rw' => $user->nomor_rw ?: ($validated['regRw'] !== '' ? $this->normalizeRw($validated['regRw']) : null),
            ]);

            if (! $user->member_number) {
                $membershipService->assignMemberNumber($user);
            }
        }

        EventRegistration::query()->create([
            'uuid' => (string) Str::uuid(),
            'event_id' => $this->event->id,
            'user_id' => $user->id,
            'ticket_code' => sprintf('EVT-%04d-U%05d', $this->event->id, $user->id),
            'status' => 'registered',
        ]);

        $this->event = $this->event->fresh()->loadCount('registrations');
        $this->registered = true;

        session()->flash('message', 'Pendaftaran event berhasil disimpan.');
    }

    public function getDesaOptionsProperty()
    {
        return TargetWilayah::query()
            ->when($this->regDapil !== '', fn ($query) => $query->where('dapil', $this->regDapil))
            ->orderBy('desa')
            ->get(['id', 'desa', 'dapil']);
    }

    public function render()
    {
        return view('livewire.public-site.event-detail')
            ->title($this->event->judul);
    }

    private function findUserByPhone(string $phone): ?User
    {
        return User::query()
            ->where('phone', $phone)
            ->orWhere('phone', $this->regHp)
            ->first();
    }

    private function resolveRegistrationEmail(?string $email, string $phone): string
    {
        $candidate = trim((string) $email);

        if ($candidate !== '' && ! User::query()->where('email', $candidate)->exists()) {
            return $candidate;
        }

        $base = ($phone !== '' ? $phone : 'member').'.event@bekasihebat.local';
        $resolved = $base;
        $suffix = 1;

        while (User::query()->where('email', $resolved)->exists()) {
            $suffix++;
            $resolved = ($phone !== '' ? $phone : 'member').'.event'.$suffix.'@bekasihebat.local';
        }

        return $resolved;
    }

    private function normalizeRw(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if ($digits === '') {
            return '';
        }

        return str_pad($digits, 3, '0', STR_PAD_LEFT);
    }
}
