<div data-flux-main style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 tracking-tight">Mapping Level Bidang</h1>
            <p class="text-sm text-zinc-500 mt-1">Atur ketersediaan bidang untuk masing-masing level kepengurusan.</p>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200">
                <thead class="bg-zinc-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-zinc-900 sm:pl-6 w-1/3">Nama Bidang</th>
                        <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-zinc-900">Tersedia di DPD</th>
                        <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-zinc-900">Tersedia di DPC</th>
                        <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-zinc-900">Tersedia di DPRa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 bg-white">
                    @forelse ($bidangs as $bidang)
                        <tr class="hover:bg-zinc-50 transition-colors">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-zinc-900 sm:pl-6">
                                <div class="flex items-center gap-3">
                                    @if($bidang->icon)
                                        <div class="flex items-center justify-center w-8 h-8 rounded-lg" style="background:{{ $bidang->color ?: '#f3f4f6' }}; color:{{ $bidang->color ? '#fff' : '#6b7280' }}">
                                            <i class="ti ti-{{ $bidang->icon }} text-lg"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold">{{ $bidang->nama }}</div>
                                        <div class="text-xs text-zinc-500 font-normal mt-0.5">Urutan: {{ $bidang->urutan }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            {{-- Kolom DPD --}}
                            <td class="whitespace-nowrap px-3 py-4 text-center">
                                <button 
                                    wire:click="toggleLevel('{{ $bidang->id }}', 'dpd')" 
                                    type="button" 
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-offset-2 {{ $bidang->is_dpd ? 'bg-orange-500' : 'bg-zinc-200' }}" 
                                    role="switch" 
                                    aria-checked="{{ $bidang->is_dpd ? 'true' : 'false' }}">
                                    <span class="sr-only">Tersedia di DPD</span>
                                    <span class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $bidang->is_dpd ? 'translate-x-5' : 'translate-x-0' }}">
                                        <span class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity {{ $bidang->is_dpd ? 'opacity-0 duration-100 ease-out' : 'opacity-100 duration-200 ease-in' }}" aria-hidden="true">
                                            <svg class="h-3 w-3 text-zinc-400" fill="none" viewBox="0 0 12 12"><path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        </span>
                                        <span class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity {{ $bidang->is_dpd ? 'opacity-100 duration-200 ease-in' : 'opacity-0 duration-100 ease-out' }}" aria-hidden="true">
                                            <svg class="h-3 w-3 text-orange-500" fill="currentColor" viewBox="0 0 12 12"><path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z" /></svg>
                                        </span>
                                    </span>
                                </button>
                            </td>

                            {{-- Kolom DPC --}}
                            <td class="whitespace-nowrap px-3 py-4 text-center">
                                <button 
                                    wire:click="toggleLevel('{{ $bidang->id }}', 'dpc')" 
                                    type="button" 
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-offset-2 {{ $bidang->is_dpc ? 'bg-orange-500' : 'bg-zinc-200' }}" 
                                    role="switch" 
                                    aria-checked="{{ $bidang->is_dpc ? 'true' : 'false' }}">
                                    <span class="sr-only">Tersedia di DPC</span>
                                    <span class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $bidang->is_dpc ? 'translate-x-5' : 'translate-x-0' }}">
                                        <span class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity {{ $bidang->is_dpc ? 'opacity-0 duration-100 ease-out' : 'opacity-100 duration-200 ease-in' }}" aria-hidden="true">
                                            <svg class="h-3 w-3 text-zinc-400" fill="none" viewBox="0 0 12 12"><path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        </span>
                                        <span class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity {{ $bidang->is_dpc ? 'opacity-100 duration-200 ease-in' : 'opacity-0 duration-100 ease-out' }}" aria-hidden="true">
                                            <svg class="h-3 w-3 text-orange-500" fill="currentColor" viewBox="0 0 12 12"><path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z" /></svg>
                                        </span>
                                    </span>
                                </button>
                            </td>

                            {{-- Kolom DPRa --}}
                            <td class="whitespace-nowrap px-3 py-4 text-center">
                                <button 
                                    wire:click="toggleLevel('{{ $bidang->id }}', 'dpra')" 
                                    type="button" 
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-offset-2 {{ $bidang->is_dpra ? 'bg-orange-500' : 'bg-zinc-200' }}" 
                                    role="switch" 
                                    aria-checked="{{ $bidang->is_dpra ? 'true' : 'false' }}">
                                    <span class="sr-only">Tersedia di DPRa</span>
                                    <span class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $bidang->is_dpra ? 'translate-x-5' : 'translate-x-0' }}">
                                        <span class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity {{ $bidang->is_dpra ? 'opacity-0 duration-100 ease-out' : 'opacity-100 duration-200 ease-in' }}" aria-hidden="true">
                                            <svg class="h-3 w-3 text-zinc-400" fill="none" viewBox="0 0 12 12"><path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        </span>
                                        <span class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity {{ $bidang->is_dpra ? 'opacity-100 duration-200 ease-in' : 'opacity-0 duration-100 ease-out' }}" aria-hidden="true">
                                            <svg class="h-3 w-3 text-orange-500" fill="currentColor" viewBox="0 0 12 12"><path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z" /></svg>
                                        </span>
                                    </span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-sm text-zinc-500">
                                Belum ada data bidang. Silakan tambahkan di Master Bidang terlebih dahulu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
