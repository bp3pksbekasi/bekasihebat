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

    {{-- Modal Form (Drawer Model) --}}
    @if ($showForm)
        {{-- Backdrop --}}
        <div
            wire:click="closeForm"
            style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:40;backdrop-filter:blur(2px);"
        ></div>

        {{-- Drawer Panel --}}
        <div style="
            position:fixed;
            top:0;right:0;
            width:100%;
            max-width:520px;
            height:100%;
            background:#fff;
            box-shadow:-8px 0 32px rgba(0,0,0,0.18);
            z-index:50;
            overflow-y:auto;
            display:flex;
            flex-direction:column;
        ">
            {{-- Header --}}
            <div style="
                position:sticky;top:0;
                background:#fff;
                border-bottom:1px solid #e5e7eb;
                padding:18px 20px;
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:12px;
                z-index:51;
            ">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;border-radius:50%;background:#ffedd5;display:flex;align-items:center;justify-content:center;color:#ea580c;">
                        <i class="ti ti-{{ $isEdit ? 'edit' : 'plus' }} text-xl"></i>
                    </div>
                    <div>
                        <div style="font-size:17px;font-weight:700;color:#111827;letter-spacing:-0.2px;">
                            {{ $isEdit ? 'Edit Bidang' : 'Tambah Bidang Baru' }}
                        </div>
                        <div style="font-size:12px;color:#6b7280;margin-top:3px;">Lengkapi data form berikut</div>
                    </div>
                </div>
                <button
                    wire:click="closeForm"
                    type="button"
                    style="
                        width:36px;height:36px;
                        border-radius:8px;
                        border:1px solid #e5e7eb;
                        background:#f9fafb;
                        cursor:pointer;
                        display:flex;align-items:center;justify-content:center;
                        font-size:16px;color:#6b7280;
                        flex-shrink:0;
                    "
                >✕</button>
            </div>

            {{-- Form Body --}}
            <div style="padding:20px;display:grid;gap:16px;align-content:start;flex:1;">
                <div>
                    <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Nama Bidang <span style="color:#ef4444;">*</span></label>
                    <input
                        wire:model="fNama"
                        type="text"
                        style="
                            width:100%;height:48px;
                            border-radius:10px;
                            border:1.5px solid #d1d5db;
                            background:#fff;
                            padding:0 14px;
                            font-size:15px;
                            color:#111827;
                            box-sizing:border-box;
                        "
                    >
                    @error('fNama') <span style="color:#dc2626;font-size:12px;margin-top:5px;display:block;">{{ $message }}</span> @enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Ikon (Tabler Icons)</label>
                        <input
                            wire:model="fIcon"
                            type="text"
                            placeholder="misal: users"
                            style="
                                width:100%;height:48px;
                                border-radius:10px;
                                border:1.5px solid #d1d5db;
                                background:#fff;
                                padding:0 14px;
                                font-size:15px;
                                color:#111827;
                                box-sizing:border-box;
                            "
                        >
                        @error('fIcon') <span style="color:#dc2626;font-size:12px;margin-top:5px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Warna (Hex)</label>
                        <input
                            wire:model="fColor"
                            type="text"
                            placeholder="misal: #fe5000"
                            style="
                                width:100%;height:48px;
                                border-radius:10px;
                                border:1.5px solid #d1d5db;
                                background:#fff;
                                padding:0 14px;
                                font-size:15px;
                                color:#111827;
                                box-sizing:border-box;
                            "
                        >
                        @error('fColor') <span style="color:#dc2626;font-size:12px;margin-top:5px;display:block;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Nama PIC</label>
                        <input
                            wire:model="fPicNama"
                            type="text"
                            style="
                                width:100%;height:48px;
                                border-radius:10px;
                                border:1.5px solid #d1d5db;
                                background:#fff;
                                padding:0 14px;
                                font-size:15px;
                                color:#111827;
                                box-sizing:border-box;
                            "
                        >
                        @error('fPicNama') <span style="color:#dc2626;font-size:12px;margin-top:5px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">No. HP PIC</label>
                        <input
                            wire:model="fPicHp"
                            type="text"
                            style="
                                width:100%;height:48px;
                                border-radius:10px;
                                border:1.5px solid #d1d5db;
                                background:#fff;
                                padding:0 14px;
                                font-size:15px;
                                color:#111827;
                                box-sizing:border-box;
                            "
                        >
                        @error('fPicHp') <span style="color:#dc2626;font-size:12px;margin-top:5px;display:block;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Urutan Tampil <span style="color:#ef4444;">*</span></label>
                    <input
                        wire:model="fUrutan"
                        type="number"
                        min="0"
                        style="
                            width:100%;height:48px;
                            border-radius:10px;
                            border:1.5px solid #d1d5db;
                            background:#fff;
                            padding:0 14px;
                            font-size:15px;
                            color:#111827;
                            box-sizing:border-box;
                        "
                    >
                    @error('fUrutan') <span style="color:#dc2626;font-size:12px;margin-top:5px;display:block;">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Footer Buttons --}}
            <div style="
                position:sticky;bottom:0;
                background:#fff;
                border-top:1px solid #e5e7eb;
                padding:16px 20px;
                display:flex;
                gap:10px;
                z-index:51;
            ">
                <button
                    wire:click="simpanBidang"
                    type="button"
                    style="
                        flex:1;
                        height:50px;
                        border:none;
                        border-radius:12px;
                        background:#ea580c;
                        color:#fff;
                        font-size:15px;
                        font-weight:700;
                        cursor:pointer;
                        letter-spacing:0.2px;
                    "
                >
                    {{ $isEdit ? '💾 Update' : '✓ Simpan' }} Bidang
                </button>
                <button
                    wire:click="closeForm"
                    type="button"
                    style="
                        height:50px;
                        padding:0 20px;
                        border-radius:12px;
                        border:1.5px solid #e5e7eb;
                        background:#fff;
                        color:#6b7280;
                        font-size:15px;
                        font-weight:600;
                        cursor:pointer;
                    "
                >
                    Batal
                </button>
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
