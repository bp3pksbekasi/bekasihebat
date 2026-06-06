<x-layouts.app.sidebar>
    <flux:main>
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-zinc-200 bg-zinc-950 px-6 py-4 text-white dark:border-zinc-700">
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-500 text-sm font-medium text-white">
                                BD
                            </div>
                            <div>
                                <div class="text-sm font-medium">Bedah Dapil</div>
                                <div class="text-xs text-zinc-400">Analisa Wilayah</div>
                            </div>
                        </div>

                        <nav class="flex items-center gap-4 text-sm text-zinc-400">
                            <a href="{{ route('bedah-dapil.pemilu-dprd') }}" class="transition hover:text-white">Pemilu DPRD</a>
                            <a href="{{ route('bedah-dapil.analisa-caleg') }}" class="transition hover:text-white">Analisa Caleg</a>
                        </nav>
                    </div>

                    <div class="text-xs text-zinc-400">
                        Login: <span class="text-white">{{ auth()->user()->name }}</span>
                    </div>
                </div>

                <div class="space-y-6 px-6 py-8">
                    <div>
                        <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">Peta Wilayah</h1>
                        <p class="mt-2 max-w-3xl text-sm text-zinc-600 dark:text-zinc-400">
                            Halaman ini disiapkan sebagai ruang eksplorasi peta Kabupaten Bekasi, peta per dapil, dan peta per kecamatan.
                            Detail interaktifnya bisa dilanjutkan pada tahap berikutnya tanpa mengubah struktur route yang sudah ada.
                        </p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-700 dark:bg-zinc-800/60">
                            <div class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Kabupaten</div>
                            <div class="mt-2 text-sm font-medium text-zinc-900 dark:text-zinc-100">Peta Kabupaten Bekasi</div>
                            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Sumber: `public/images/peta/kabupaten-bekasi.png`</div>
                        </div>

                        <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-700 dark:bg-zinc-800/60">
                            <div class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Dapil</div>
                            <div class="mt-2 text-sm font-medium text-zinc-900 dark:text-zinc-100">Peta Dapil 1-7</div>
                            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Sumber: `public/images/peta/dapil1.png` s.d. `dapil7.png`</div>
                        </div>

                        <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-700 dark:bg-zinc-800/60">
                            <div class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Kecamatan</div>
                            <div class="mt-2 text-sm font-medium text-zinc-900 dark:text-zinc-100">Peta Kecamatan</div>
                            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Sumber: `public/images/peta/kecamatan/*.png`</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </flux:main>
</x-layouts.app.sidebar>
