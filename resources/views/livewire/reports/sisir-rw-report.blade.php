<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Sisir RW</h1>
            <p class="text-gray-500 text-sm mt-1">Laporan strategis cakupan teritorial dan kinerja anggota dewan.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Cakupan Teritorial Dapil -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="font-semibold text-gray-800">Cakupan Teritorial per Dapil</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($this->cakupanTeritorial['dapils'] as $dapil => $data)
                        @php
                            $percentage = $data['total_rw'] > 0 ? round(($data['tersisir'] / $data['total_rw']) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-end mb-1">
                                <span class="font-medium text-gray-700">{{ $dapil }}</span>
                                <span class="text-sm text-gray-500">{{ $data['tersisir'] }} / {{ $data['total_rw'] }} RW ({{ $percentage }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-orange-500 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Blank Spots -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="bg-red-50 border-b border-red-100 px-6 py-4">
                <h3 class="font-semibold text-red-800">Blank Spot (Desa Belum Tersisir)</h3>
            </div>
            <div class="p-0 max-h-80 overflow-y-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0">
                        <tr>
                            <th scope="col" class="px-6 py-3">Dapil</th>
                            <th scope="col" class="px-6 py-3">Kecamatan</th>
                            <th scope="col" class="px-6 py-3">Desa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->cakupanTeritorial['blankSpots'] as $spot)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-3">{{ $spot->dapil }}</td>
                            <td class="px-6 py-3">{{ $spot->kecamatan }}</td>
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $spot->desa }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                Luar biasa! Seluruh desa sudah ada kegiatan Sisir RW.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Kehadiran Dewan -->
    <h2 class="text-xl font-bold text-gray-900 mb-4">Peringkat Kehadiran Dewan</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- DPR RI -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="bg-blue-50 border-b border-blue-100 px-6 py-4">
                <h3 class="font-semibold text-blue-800">DPR RI</h3>
            </div>
            <div class="p-0">
                <ul class="divide-y divide-gray-200">
                    @forelse($this->kehadiranDewan['dpr'] as $nama => $jumlah)
                    <li class="px-6 py-4 flex justify-between items-center">
                        <span class="font-medium text-gray-800 text-sm">{{ $nama }}</span>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $jumlah }}x hadir</span>
                    </li>
                    @empty
                    <li class="px-6 py-4 text-sm text-gray-500 text-center">Belum ada data kehadiran.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- DPRD Provinsi -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="bg-emerald-50 border-b border-emerald-100 px-6 py-4">
                <h3 class="font-semibold text-emerald-800">DPRD Provinsi</h3>
            </div>
            <div class="p-0">
                <ul class="divide-y divide-gray-200">
                    @forelse($this->kehadiranDewan['prov'] as $nama => $jumlah)
                    <li class="px-6 py-4 flex justify-between items-center">
                        <span class="font-medium text-gray-800 text-sm">{{ $nama }}</span>
                        <span class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $jumlah }}x hadir</span>
                    </li>
                    @empty
                    <li class="px-6 py-4 text-sm text-gray-500 text-center">Belum ada data kehadiran.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- DPRD Kabupaten -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="bg-purple-50 border-b border-purple-100 px-6 py-4">
                <h3 class="font-semibold text-purple-800">DPRD Kabupaten</h3>
            </div>
            <div class="p-0">
                <ul class="divide-y divide-gray-200">
                    @forelse($this->kehadiranDewan['kab'] as $nama => $jumlah)
                    <li class="px-6 py-4 flex justify-between items-center">
                        <span class="font-medium text-gray-800 text-sm">{{ $nama }}</span>
                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $jumlah }}x hadir</span>
                    </li>
                    @empty
                    <li class="px-6 py-4 text-sm text-gray-500 text-center">Belum ada data kehadiran.</li>
                    @endforelse
                </ul>
            </div>
        </div>

    </div>
</div>
