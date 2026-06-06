<x-layouts.app.sidebar>
    <flux:main>
        <div class="mx-auto max-w-5xl space-y-8 px-4 py-8">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Bedah Dapil</h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Modul Analisis & Strategi Pemenangan Pemilu</p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <a href="#" class="group rounded-xl border border-zinc-200 bg-white p-6 transition hover:border-amber-300 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-amber-600">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Peta Interaktif Dapil</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Mapping desa/kelurahan per dapil dengan hotspot interaktif</p>
                </a>

                <a href="{{ route('bedah-dapil.pemilu-dprd') }}" class="group rounded-xl border border-zinc-200 bg-white p-6 transition hover:border-amber-300 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-amber-600">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Mapping Perolehan Suara DPRD</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Analisis perolehan suara DPRD Kab. Bekasi berdasarkan data TPS Pemilu 2024</p>
                </a>
            </div>
        </div>
    </flux:main>
</x-layouts.app.sidebar>
