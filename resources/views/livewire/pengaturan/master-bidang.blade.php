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
                        <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold text-zinc-900 w-16">Urutan</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-zinc-900">Nama Bidang</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-zinc-900">Kabid</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-zinc-900">Sekbid</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-zinc-900">Periode</th>
                        <th scope="col" class="px-6 py-3.5 text-center text-xs font-semibold text-zinc-900 w-24">Status</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-6 w-24">
                            <span class="sr-only">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 bg-white">
                    @forelse ($this->bidangList as $bidang)
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm font-medium text-zinc-900 text-center">
                                {{ $bidang->urutan }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-900">
                                <div class="font-medium">{{ $bidang->nama }}</div>
                                <div class="text-xs text-zinc-500 mt-0.5">{{ $bidang->slug }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-500">
                                @if($bidang->kabid)
                                    <div class="font-medium text-zinc-900">{{ $bidang->kabid }}</div>
                                    <div class="text-xs">{{ $bidang->nohpkabid ?? '-' }}</div>
                                @else
                                    <span class="text-zinc-400 italic">Belum diatur</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-500">
                                @if($bidang->sekbid)
                                    <div class="font-medium text-zinc-900">{{ $bidang->sekbid }}</div>
                                    <div class="text-xs">{{ $bidang->nohpsekbid ?? '-' }}</div>
                                @else
                                    <span class="text-zinc-400 italic">-</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-500">
                                {{ $bidang->periode ?: '-' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-center">
                                @if($bidang->is_active)
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Aktif</span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Inaktif</span>
                                @endif
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openFormEdit('{{ $bidang->id }}')" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-md hover:bg-indigo-50" title="Edit">
                                        <i class="ti ti-edit text-lg"></i>
                                    </button>
                                    @if($bidang->is_active)
                                        <button
                                            wire:click="toggleActive('{{ $bidang->id }}')"
                                            wire:confirm="Nonaktifkan bidang '{{ addslashes($bidang->nama) }}'?\n\nBidang akan dinonaktifkan."
                                            class="text-red-600 hover:text-red-900 p-1 rounded-md hover:bg-red-50"
                                            title="Nonaktifkan"
                                        >
                                            <i class="ti ti-trash text-lg"></i>
                                        </button>
                                    @else
                                        <button
                                            wire:click="toggleActive('{{ $bidang->id }}')"
                                            wire:confirm="Aktifkan kembali bidang '{{ addslashes($bidang->nama) }}'?"
                                            class="text-green-600 hover:text-green-900 p-1 rounded-md hover:bg-green-50"
                                            title="Aktifkan"
                                        >
                                            <i class="ti ti-refresh text-lg"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-sm text-zinc-500">
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

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Nama Kabid</label>
                        <input
                            wire:model="fKabid"
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
                        @error('fKabid') <span style="color:#dc2626;font-size:12px;margin-top:5px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">No. HP Kabid</label>
                        <input
                            wire:model="fNohpkabid"
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
                        @error('fNohpkabid') <span style="color:#dc2626;font-size:12px;margin-top:5px;display:block;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Nama Sekbid</label>
                        <input
                            wire:model="fSekbid"
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
                        @error('fSekbid') <span style="color:#dc2626;font-size:12px;margin-top:5px;display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">No. HP Sekbid</label>
                        <input
                            wire:model="fNohpsekbid"
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
                        @error('fNohpsekbid') <span style="color:#dc2626;font-size:12px;margin-top:5px;display:block;">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">Periode</label>
                    <input
                        wire:model="fPeriode"
                        type="text"
                        placeholder="misal: 2024-2029"
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
                    @error('fPeriode') <span style="color:#dc2626;font-size:12px;margin-top:5px;display:block;">{{ $message }}</span> @enderror
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

</div>
