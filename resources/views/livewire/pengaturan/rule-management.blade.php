<div>
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pengaturan Rule (Hak Akses)</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola hak akses menu untuk masing-masing jabatan/role.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold">
                    <tr>
                        <th class="px-6 py-4 sticky left-0 bg-gray-50 border-r border-gray-200 z-10 w-64 min-w-[250px]">Jabatan / Role</th>
                        @foreach ($menus as $slug => $label)
                            <th class="px-4 py-4 text-center whitespace-nowrap min-w-[120px]">
                                {{ $label }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($roles as $role)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 bg-white border-r border-gray-200 font-bold text-black whitespace-nowrap">
                                {{ ucwords(str_replace('_', ' ', $role['name'])) }}
                            </td>
                            @foreach ($menus as $slug => $label)
                                <td class="px-4 py-4 text-center">
                                    <input type="checkbox" 
                                        class="w-5 h-5 text-orange-500 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 cursor-pointer"
                                        wire:click="togglePermission({{ $role['id'] }}, '{{ $slug }}')"
                                        @if($rolePermissions[$role['id']][$slug] ?? false) checked @endif>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 text-xs text-gray-500">
            <i class="ti ti-info-circle mr-1"></i> Perubahan akan langsung tersimpan secara otomatis saat *toggle* ditekan. Admin DPD dan Super Admin memiliki akses penuh ke semua menu (tidak ditampilkan di tabel ini).
        </div>
    </div>
</div>
