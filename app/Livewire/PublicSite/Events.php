<?php

declare(strict_types=1);

namespace App\Livewire\PublicSite;

use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.public')]
#[Title('Event Publik - Bekasi Hebat')]
class Events extends Component
{
    use WithPagination;

    public string $filter = 'mendatang';

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function getSummaryProperty(): array
    {
        $baseQuery = $this->basePublicEventsQuery();

        return [
            'total' => (clone $baseQuery)->count(),
            'mendatang' => (clone $baseQuery)->where('tanggal_mulai', '>=', now())->count(),
            'selesai' => (clone $baseQuery)
                ->where(function (Builder $query): void {
                    $query
                        ->where('status', Event::STATUS_SELESAI)
                        ->orWhere('tanggal_mulai', '<', now());
                })
                ->count(),
        ];
    }

    public function getFeaturedEventProperty(): ?Event
    {
        return $this->baseFilteredEventsQuery()
            ->orderBy('tanggal_mulai')
            ->first();
    }

    public function getUpcomingHighlightsProperty(): Collection
    {
        return $this->basePublicEventsQuery()
            ->where('tanggal_mulai', '>=', now())
            ->orderBy('tanggal_mulai')
            ->limit(3)
            ->get();
    }

    public function getEventsProperty(): LengthAwarePaginator
    {
        $featuredId = $this->featuredEvent?->id;

        return $this->baseFilteredEventsQuery()
            ->when($featuredId, fn (Builder $query) => $query->whereKeyNot($featuredId))
            ->orderBy('tanggal_mulai')
            ->paginate(9);
    }

    public function render()
    {
        return view('livewire.public-site.events');
    }

    private function basePublicEventsQuery(): Builder
    {
        return Event::query()
            ->where('is_public', true)
            ->whereIn('status', [Event::STATUS_DISETUJUI, Event::STATUS_SELESAI])
            ->withCount('registrations');
    }

    private function baseFilteredEventsQuery(): Builder
    {
        return $this->basePublicEventsQuery()
            ->when($this->filter === 'mendatang', fn (Builder $query) => $query->where('tanggal_mulai', '>=', now()))
            ->when($this->filter === 'selesai', function (Builder $query): void {
                $query->where(function (Builder $nested): void {
                    $nested
                        ->where('status', Event::STATUS_SELESAI)
                        ->orWhere('tanggal_mulai', '<', now());
                });
            });
    }
}
