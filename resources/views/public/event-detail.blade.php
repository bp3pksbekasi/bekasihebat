<x-layouts.public
    :title="$event->judul"
    :description="Str::limit(strip_tags((string) $event->deskripsi), 150)">

    <div class="h-64 w-full bg-gradient-to-br from-orange-100 to-orange-200">
        @if ($event->cover_image)
            <img src="{{ Storage::url($event->cover_image) }}" alt="{{ $event->judul }}" class="h-full w-full object-cover">
        @else
            <div class="flex h-full items-center justify-center">
                <i class="ti ti-calendar-event text-5xl text-orange-300"></i>
            </div>
        @endif
    </div>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-4 flex flex-wrap gap-2">
            <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-medium text-orange-600">
                {{ $event->jenis_label }}
            </span>
            @if ($event->tanggal_mulai && $event->tanggal_mulai->isFuture())
                <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-600">Pendaftaran Dibuka</span>
            @else
                <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-500">Selesai</span>
            @endif
        </div>

        <h1 class="mb-4 text-3xl font-medium text-zinc-900">{{ $event->judul }}</h1>

        <div class="mb-6 grid gap-3 rounded-xl border border-zinc-200 bg-white p-5 sm:grid-cols-2">
            <div class="flex items-center gap-2 text-sm text-zinc-600">
                <i class="ti ti-calendar text-orange-500"></i>
                {{ $event->tanggal_mulai?->translatedFormat('l, d F Y') }}
            </div>
            <div class="flex items-center gap-2 text-sm text-zinc-600">
                <i class="ti ti-clock text-orange-500"></i>
                {{ $event->tanggal_mulai?->format('H:i') }} WIB
            </div>
            <div class="flex items-center gap-2 text-sm text-zinc-600">
                <i class="ti ti-map-pin text-orange-500"></i>
                {{ $event->lokasi }}
            </div>
            <div class="flex items-center gap-2 text-sm text-zinc-600">
                <i class="ti ti-users text-orange-500"></i>
                @if ($event->kapasitas > 0)
                    {{ $event->registration_count }} / {{ $event->kapasitas }} peserta
                @else
                    Tidak terbatas
                @endif
            </div>
            <div class="flex items-center gap-2 text-sm text-zinc-600 sm:col-span-2">
                <i class="ti ti-building-community text-orange-500"></i>
                {{ $event->penyelenggara ?: 'Kabupaten Bekasi Hebat' }}
            </div>
        </div>

        <div class="mb-8 rounded-xl border border-zinc-200 bg-white p-6">
            <div class="mb-3 text-sm font-medium text-zinc-900">Deskripsi</div>
            <div class="prose prose-sm max-w-none text-zinc-600">
                {!! nl2br(e((string) $event->deskripsi)) !!}
            </div>
        </div>

        @if ($event->tanggal_mulai && $event->tanggal_mulai->isFuture())
            @auth
                @php
                    $sudahDaftar = $event->registrations()->where('user_id', auth()->id())->exists();
                @endphp

                @if ($sudahDaftar)
                    <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-center">
                        <i class="ti ti-check text-xl text-green-600"></i>
                        <div class="mt-1 text-sm font-medium text-green-700">Anda sudah terdaftar</div>
                    </div>
                @else
                    <form method="POST" action="{{ route('public.event-register', $event) }}">
                        @csrf
                        <button type="submit" class="w-full rounded-xl bg-orange-600 py-3 text-sm font-medium text-white hover:bg-orange-700">
                            Daftar Event Ini
                        </button>
                    </form>
                @endif
            @else
                <a
                    href="{{ route('register', ['redirect' => route('public.event-detail', $event)]) }}"
                    class="block w-full rounded-xl bg-orange-600 py-3 text-center text-sm font-medium text-white hover:bg-orange-700">
                    Daftar & Ikuti Event
                </a>
                <p class="mt-2 text-center text-xs text-zinc-400">Belum punya akun? Daftar gratis untuk mengikuti event.</p>
            @endauth
        @endif
    </div>

</x-layouts.public>
