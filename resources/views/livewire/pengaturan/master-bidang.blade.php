<div data-flux-main style="min-height:100vh;padding:20px;background:#f5f5f5;position:relative;">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 tracking-tight">Master Bidang</h1>
            <p class="text-sm text-zinc-500 mt-1">Kelola data master bidang DPD beserta PIC-nya.</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="openFormCreate" type="button" class="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600 transition">
                <i class="ti ti-plus text-base" aria-hidden="true"></i>
                Tambah Bidang
            </button>
        </div>
    </div>

    @if (session('message'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 border border-green-200">
            <div class="flex items-center gap-3 text-sm text-green-800">
                <i class="ti ti-check text-lg" aria-hidden="true"></i>
                {{ session('message') }}
            </div>
        </div>
    @endif
    
    @error('deleteError')
        <div class="mb-6 rounded-lg bg-red-50 p-4 border border-red-200">
            <div class="flex items-center gap-3 text-sm text-red-800">
                <i class="ti ti-alert-circle text-lg" aria-hidden="true"></i>
                {{ $message }}
            </div>
        </div>
    @enderror

    <div class="rounded-xl border border-zinc-200 bg-white shadow-sm">
        <div class="border-b border-zinc-200 p-4 flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="relative flex-1 max-w-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="ti ti-search text-zinc-400" aria-hidden="true"></i>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" class="block w-full rounded-lg border-0 py-2 pl-10 pr-3 text-sm text-zinc-900 ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-orange-600 sm:leading-6" placeholder="Cari nama bidang atau PIC...">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200">
                <thead class="bg-zinc-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-zinc-900 sm:pl-6 w-16">Urutan</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-zinc-900">Nama Bidang</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-zinc-900">Icon / Warna</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-zinc-900">PIC / Ketua</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 w-24">
                            <span class="sr-only">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 bg-white">
                    @forelse ($this->bidangList as $bidang)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-zinc-900 sm:pl-6 text-center">
                                {{ $bidang->urutan }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-zinc-900">
                                <div class="font-medium">{{ $bidang->nama }}</div>
                                <div class="text-xs text-zinc-500 mt-0.5">{{ $bidang->slug }}</div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-zinc-500">
                                <div class="flex items-center gap-2">
                                    @if($bidang->icon)
                                        <div class="flex items-center justify-center w-8 h-8 rounded-lg" style="background:{{ $bidang->color ?: '#f3f4f6' }}; color:{{ $bidang->color ? '#fff' : '#6b7280' }}">
                                            <i class="ti ti-{{ $bidang->icon }} text-lg"></i>
                                        </div>
                                    @else
                                        -
                                    @endif
                                    <span class="text-xs">{{ $bidang->color ?: '-' }}</span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-zinc-500">
                                @if($bidang->pic_nama)
                                    <div class="font-medium text-zinc-900">{{ $bidang->pic_nama }}</div>
                                    <div class="text-xs">{{ $bidang->pic_hp ?? '-' }}</div>
                                @else
                                    <span class="text-zinc-400 italic">Belum diatur</span>
                                @endif
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openFormEdit('{{ $bidang->id }}')" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-md hover:bg-indigo-50" title="Edit">
                                        <i class="ti ti-edit text-lg"></i>
                                    </button>
                                    <button wire:click="confirmDelete('{{ $bidang->id }}')" class="text-red-600 hover:text-red-900 p-1 rounded-md hover:bg-red-50" title="Hapus">
                                        <i class="ti ti-trash text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-sm text-zinc-500">
                                Belum ada data bidang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($this->bidangList->hasPages())
            <div class="border-t border-zinc-200 px-4 py-3 sm:px-6">
                {{ $this->bidangList->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Form --}}
    @if ($showForm)
        <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-zinc-900/50 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="ti ti-{{ $isEdit ? 'edit' : 'plus' }} text-xl text-orange-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-base font-semibold leading-6 text-zinc-900" id="modal-title">{{ $isEdit ? 'Edit Bidang' : 'Tambah Bidang Baru' }}</h3>
                                    <div class="mt-4 space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium leading-6 text-zinc-900">Nama Bidang <span class="text-red-500">*</span></label>
                                            <div class="mt-1">
                                                <input wire:model="fNama" type="text" class="block w-full rounded-lg border-0 py-2 text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-orange-600 sm:text-sm sm:leading-6">
                                            </div>
                                            @error('fNama') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium leading-6 text-zinc-900">Ikon (Tabler Icons)</label>
                                                <div class="mt-1">
                                                    <input wire:model="fIcon" type="text" placeholder="misal: users" class="block w-full rounded-lg border-0 py-2 text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-orange-600 sm:text-sm sm:leading-6">
                                                </div>
                                                @error('fIcon') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium leading-6 text-zinc-900">Warna (Hex)</label>
                                                <div class="mt-1">
                                                    <input wire:model="fColor" type="text" placeholder="misal: #fe5000" class="block w-full rounded-lg border-0 py-2 text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-orange-600 sm:text-sm sm:leading-6">
                                                </div>
                                                @error('fColor') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium leading-6 text-zinc-900">Nama PIC</label>
                                                <div class="mt-1">
                                                    <input wire:model="fPicNama" type="text" class="block w-full rounded-lg border-0 py-2 text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-orange-600 sm:text-sm sm:leading-6">
                                                </div>
                                                @error('fPicNama') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium leading-6 text-zinc-900">No. HP PIC</label>
                                                <div class="mt-1">
                                                    <input wire:model="fPicHp" type="text" class="block w-full rounded-lg border-0 py-2 text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-orange-600 sm:text-sm sm:leading-6">
                                                </div>
                                                @error('fPicHp') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium leading-6 text-zinc-900">Urutan Tampil <span class="text-red-500">*</span></label>
                                            <div class="mt-1">
                                                <input wire:model="fUrutan" type="number" min="0" class="block w-full rounded-lg border-0 py-2 text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 focus:ring-2 focus:ring-inset focus:ring-orange-600 sm:text-sm sm:leading-6">
                                            </div>
                                            @error('fUrutan') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-zinc-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button wire:click="simpanBidang" type="button" class="inline-flex w-full justify-center rounded-lg bg-orange-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 sm:ml-3 sm:w-auto transition">
                                Simpan
                            </button>
                            <button wire:click="closeForm" type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 hover:bg-zinc-50 sm:mt-0 sm:w-auto transition">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Hapus --}}
    @if ($showDeleteConfirm)
        <div class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-zinc-900/50 backdrop-blur-sm transition-opacity" wire:click="cancelDelete"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="ti ti-alert-triangle text-xl text-red-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-base font-semibold leading-6 text-zinc-900" id="modal-title">Hapus Data Bidang?</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-zinc-500">Apakah Anda yakin ingin menghapus bidang ini? Tindakan ini tidak dapat dibatalkan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-zinc-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button wire:click="deleteBidang" type="button" class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition">
                                Hapus
                            </button>
                            <button wire:click="cancelDelete" type="button" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 hover:bg-zinc-50 sm:mt-0 sm:w-auto transition">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
