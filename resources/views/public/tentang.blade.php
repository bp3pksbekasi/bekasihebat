<x-layouts.public
    title="Tentang Kami"
    description="Pelajari visi, misi, struktur organisasi, tokoh penggerak, dan wilayah kerja Kabupaten Bekasi Hebat.">

    @php
        $tokoh = [
            ['nama' => 'Nama Tokoh 1', 'jabatan' => 'Ketua DPD', 'foto' => null],
            ['nama' => 'Nama Tokoh 2', 'jabatan' => 'Sekretaris DPD', 'foto' => null],
            ['nama' => 'Nama Tokoh 3', 'jabatan' => 'Bendahara DPD', 'foto' => null],
            ['nama' => 'Nama Tokoh 4', 'jabatan' => 'Ketua Dapil 1', 'foto' => null],
            ['nama' => 'Nama Tokoh 5', 'jabatan' => 'Ketua Dapil 2', 'foto' => null],
            ['nama' => 'Nama Tokoh 6', 'jabatan' => 'Ketua Dapil 3', 'foto' => null],
            ['nama' => 'Nama Tokoh 7', 'jabatan' => 'Ketua Dapil 4', 'foto' => null],
            ['nama' => 'Nama Tokoh 8', 'jabatan' => 'Ketua Dapil 5', 'foto' => null],
        ];

        $wilayahByDapil = \App\Models\TargetWilayah::query()
            ->select('dapil', 'kecamatan')
            ->distinct()
            ->orderBy('dapil')
            ->orderBy('kecamatan')
            ->get()
            ->groupBy('dapil');
    @endphp

    <section class="bg-zinc-50 py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <div class="mb-2 text-xs font-medium uppercase tracking-wider text-orange-600">Tentang Kami</div>
                <h1 class="text-3xl font-medium text-zinc-900">Tentang Kabupaten Bekasi Hebat</h1>
                <p class="mt-4 text-sm leading-relaxed text-zinc-600">
                    Kabupaten Bekasi Hebat adalah ruang kolaborasi warga yang berfokus pada penguatan komunitas, pemberdayaan masyarakat,
                    pengembangan kegiatan sosial, serta perluasan partisipasi publik di Kabupaten Bekasi. Narasi ini masih dapat diganti
                    kemudian dengan profil resmi yang lebih final, namun saat ini sudah menyiapkan struktur halaman publik yang rapi dan mudah dikembangkan.
                </p>
                <p class="mt-3 text-sm leading-relaxed text-zinc-600">
                    Kami percaya perubahan yang berdampak lahir dari gerakan yang dekat dengan warga. Karena itu, kegiatan komunitas dirancang
                    agar hadir di tingkat wilayah, menjangkau kebutuhan nyata, dan membuka ruang keterlibatan yang lebih luas bagi masyarakat.
                </p>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <div class="mb-1 text-xs font-medium uppercase tracking-wider text-orange-600">Visi & Misi</div>
                <h2 class="text-2xl font-medium text-zinc-900">Arah gerak komunitas</h2>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-zinc-200 bg-white p-6">
                    <div class="mb-3 text-lg font-medium text-zinc-900">Visi</div>
                    <p class="text-sm leading-relaxed text-zinc-600">
                        Menjadi komunitas yang aktif, inklusif, dan berdampak dalam membangun Kabupaten Bekasi yang lebih hebat, berdaya, dan berkelanjutan.
                    </p>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white p-6">
                    <div class="mb-3 text-lg font-medium text-zinc-900">Misi</div>
                    <p class="text-sm leading-relaxed text-zinc-600">
                        Menggerakkan kegiatan sosial dan edukatif, memperkuat jaringan warga hingga tingkat wilayah, serta membuka akses partisipasi komunitas
                        melalui program yang konsisten dan relevan dengan kebutuhan masyarakat.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-zinc-50 py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <div class="mb-1 text-xs font-medium uppercase tracking-wider text-orange-600">Struktur Organisasi</div>
                <h2 class="text-2xl font-medium text-zinc-900">Struktur gerak dari DPD sampai kecamatan</h2>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-zinc-200 bg-white p-6">
                    <div class="text-xs font-medium uppercase tracking-wider text-orange-600">Level 1</div>
                    <div class="mt-2 text-lg font-medium text-zinc-900">DPD Kabupaten</div>
                    <p class="mt-2 text-sm text-zinc-600">Pusat koordinasi strategi, konsolidasi program, dan arah gerak komunitas tingkat kabupaten.</p>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white p-6">
                    <div class="text-xs font-medium uppercase tracking-wider text-orange-600">Level 2</div>
                    <div class="mt-2 text-lg font-medium text-zinc-900">7 DPC / Dapil</div>
                    <p class="mt-2 text-sm text-zinc-600">Penghubung koordinasi wilayah dan pelaksana program lintas kecamatan sesuai area kerja masing-masing.</p>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white p-6">
                    <div class="text-xs font-medium uppercase tracking-wider text-orange-600">Level 3</div>
                    <div class="mt-2 text-lg font-medium text-zinc-900">Kecamatan & Desa</div>
                    <p class="mt-2 text-sm text-zinc-600">Lapisan pelaksana terdekat dengan warga untuk menjalankan kegiatan, pendataan, dan penguatan jaringan lokal.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <div class="mb-1 text-xs font-medium uppercase tracking-wider text-orange-600">Tokoh Bekasi</div>
                <h2 class="text-2xl font-medium text-zinc-900">Tokoh-tokoh Penggerak</h2>
            </div>
            <div class="grid grid-cols-2 gap-6 md:grid-cols-4">
                @foreach ($tokoh as $t)
                    <div class="text-center">
                        <div class="mx-auto mb-3 flex h-20 w-20 items-center justify-center rounded-full border-2 border-orange-100 bg-zinc-100">
                            @if ($t['foto'])
                                <img src="{{ $t['foto'] }}" class="h-full w-full rounded-full object-cover" alt="{{ $t['nama'] }}">
                            @else
                                <i class="ti ti-user text-2xl text-zinc-300"></i>
                            @endif
                        </div>
                        <div class="text-sm font-medium text-zinc-900">{{ $t['nama'] }}</div>
                        <div class="text-xs text-zinc-500">{{ $t['jabatan'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-zinc-50 py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <div class="mb-1 text-xs font-medium uppercase tracking-wider text-orange-600">Wilayah Kerja</div>
                <h2 class="text-2xl font-medium text-zinc-900">Jangkauan wilayah komunitas</h2>
                <p class="mt-2 text-sm text-zinc-600">7 dapil, 23 kecamatan, dan 187 desa/kelurahan menjadi ruang kerja kolaborasi Kabupaten Bekasi Hebat.</p>
            </div>

            <div class="mb-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-zinc-200 bg-white p-5 text-center">
                    <div class="text-2xl font-medium text-orange-600">7</div>
                    <div class="mt-1 text-xs text-zinc-500">Dapil</div>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white p-5 text-center">
                    <div class="text-2xl font-medium text-orange-600">23</div>
                    <div class="mt-1 text-xs text-zinc-500">Kecamatan</div>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white p-5 text-center">
                    <div class="text-2xl font-medium text-orange-600">187</div>
                    <div class="mt-1 text-xs text-zinc-500">Desa / Kelurahan</div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($wilayahByDapil as $dapil => $kecamatanList)
                    <div class="rounded-xl border border-zinc-200 bg-white p-5">
                        <div class="mb-3 text-sm font-medium text-zinc-900">{{ $dapil }}</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($kecamatanList as $item)
                                <span class="rounded-full bg-orange-50 px-3 py-1 text-xs text-orange-700">{{ $item->kecamatan }}</span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-zinc-300 bg-white p-8 text-sm text-zinc-500 md:col-span-2 xl:col-span-3">
                        Data wilayah belum tersedia.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

</x-layouts.public>
