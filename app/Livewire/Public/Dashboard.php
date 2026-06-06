<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Models\Event;
use App\Services\MembershipCardService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.public')]
#[Title('Dashboard - Bekasi Hebat')]
class Dashboard extends Component
{
    public function render(MembershipCardService $cardService)
    {
        $user = Auth::user();
        $myEvents = collect();

        if ($user && method_exists($user, 'registrations') && class_exists(\App\Models\EventRegistration::class)) {
            /** @var Collection<int, mixed> $myEvents */
            $myEvents = $user->registrations()
                ->with('event')
                ->latest()
                ->take(5)
                ->get();
        }

        $upcomingEvents = class_exists(Event::class)
            ? Event::query()
                ->approved()
                ->where('tanggal_mulai', '>=', now())
                ->orderBy('tanggal_mulai')
                ->limit(3)
                ->get()
            : collect();

        $pendingApproval = class_exists(Event::class)
            ? Event::query()->where('status', Event::STATUS_MENUNGGU)->count()
            : 0;

        return view('livewire.public.dashboard', [
            'cardData' => $cardService->getCardData($user),
            'profile' => $cardService->getProfileCompletion($user),
            'myEvents' => $myEvents,
            'upcomingEvents' => $upcomingEvents,
            'pendingApproval' => $pendingApproval,
        ]);
    }
}
