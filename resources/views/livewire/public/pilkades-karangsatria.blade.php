<div class="h-screen w-full flex flex-col bg-white overflow-hidden">
    <!-- Header -->
    <div class="flex-none bg-emerald-700 text-white p-4 shadow-md z-20 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Peta Strategi Pilkades - Desa Karangsatria
            </h1>
            <p class="text-emerald-100 text-sm mt-1">Dapil 4 &bull; Kecamatan Tambun Utara &bull; Data update terkini</p>
        </div>
        <div class="hidden sm:flex gap-3">
            <div class="bg-emerald-800/50 px-3 py-1.5 rounded-lg text-xs font-semibold border border-emerald-600">
                Total RW: {{ count($rwData) }}
            </div>
            <a href="/" class="bg-emerald-600 hover:bg-emerald-500 transition-colors px-3 py-1.5 rounded-lg text-xs font-semibold border border-emerald-500 flex items-center">
                Kembali ke Beranda
            </a>
        </div>
    </div>

    @if(!$targetWilayah)
        <div class="flex-1 flex items-center justify-center bg-gray-50 min-h-0">
            <div class="text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h2 class="text-xl font-bold text-gray-700">Data Wilayah Tidak Ditemukan</h2>
                <p class="text-gray-500 mt-2">Pastikan master data Desa Karangsatria sudah tersedia.</p>
            </div>
        </div>
    @else
        <!-- Spreadsheet Table Container -->
        <div class="flex-1 min-h-0 overflow-auto custom-scrollbar relative bg-gray-50">
            <table class="w-full text-left border-collapse table-fixed min-w-[2000px]">
                <thead class="sticky top-0 z-10 bg-white shadow-sm">
                    <tr>
                        <th class="sticky left-0 z-20 w-16 bg-gray-100 border-b-2 border-r-2 border-gray-300 p-3 text-xs font-bold text-gray-700 uppercase text-center shadow-[2px_0_5px_rgba(0,0,0,0.05)]">RW</th>
                        <th class="sticky left-16 z-20 w-64 bg-gray-100 border-b-2 border-r border-gray-300 p-3 text-xs font-bold text-gray-700 uppercase shadow-[2px_0_5px_rgba(0,0,0,0.05)]">Nama Perumahan / Kampung</th>
                        <th class="w-20 bg-gray-100 border-b-2 border-r border-gray-300 p-3 text-xs font-bold text-gray-700 uppercase text-center">Jml RT</th>
                        <th class="w-24 bg-gray-100 border-b-2 border-r border-gray-300 p-3 text-xs font-bold text-gray-700 uppercase text-right">Est. DPT</th>
                        <th class="w-28 bg-emerald-50 border-b-2 border-r border-emerald-200 p-3 text-xs font-bold text-emerald-800 uppercase text-center">Korwe</th>
                        <th class="w-28 bg-emerald-50 border-b-2 border-r border-emerald-200 p-3 text-xs font-bold text-emerald-800 uppercase text-center">Korte</th>
                        <th class="w-48 bg-gray-100 border-b-2 border-r border-gray-300 p-3 text-xs font-bold text-gray-700 uppercase">Tipologi Wilayah</th>
                        <th class="w-56 bg-gray-100 border-b-2 border-r border-gray-300 p-3 text-xs font-bold text-gray-700 uppercase">Ekonomi Dominan</th>
                        <th class="w-64 bg-gray-100 border-b-2 border-r border-gray-300 p-3 text-xs font-bold text-gray-700 uppercase">Karakteristik Warga</th>
                        <th class="w-48 bg-blue-50 border-b-2 border-r border-blue-200 p-3 text-xs font-bold text-blue-800 uppercase">Ketokohan (Ketua RW)</th>
                        <th class="w-48 bg-blue-50 border-b-2 border-r border-blue-200 p-3 text-xs font-bold text-blue-800 uppercase">Ketokohan (Tomas/Toga)</th>
                        <th class="w-64 bg-gray-100 border-b-2 border-r border-gray-300 p-3 text-xs font-bold text-gray-700 uppercase">Faktor Pendorong/Penghambat</th>
                        <th class="w-32 bg-orange-50 border-b-2 border-r border-orange-200 p-3 text-xs font-bold text-orange-800 uppercase text-right">Suara PKS '19</th>
                        <th class="w-32 bg-gray-100 border-b-2 border-r border-gray-300 p-3 text-xs font-bold text-gray-700 uppercase text-center">Status Profil</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach($rwData as $index => $row)
                        <tr class="hover:bg-blue-50/50 transition-colors group">
                            <!-- Fixed Columns -->
                            <td class="sticky left-0 z-10 bg-white group-hover:bg-blue-50/50 border-b border-r-2 border-gray-200 p-2.5 text-center font-bold text-gray-900 shadow-[2px_0_5px_rgba(0,0,0,0.02)]">
                                {{ $row['nomor_rw'] }}
                            </td>
                            <td class="sticky left-16 z-10 bg-white group-hover:bg-blue-50/50 border-b border-r border-gray-200 p-2.5 text-sm font-semibold text-gray-800 shadow-[2px_0_5px_rgba(0,0,0,0.02)] truncate">
                                {{ $row['nama_wilayah'] }}
                            </td>
                            
                            <!-- Scrollable Columns -->
                            <td class="border-b border-r border-gray-200 p-2.5 text-sm text-center text-gray-700">
                                {{ $row['jumlah_rt'] > 0 ? $row['jumlah_rt'] : '-' }}
                            </td>
                            <td class="border-b border-r border-gray-200 p-2.5 text-sm text-right font-medium text-gray-900">
                                {{ $row['estimasi_dpt'] > 0 ? number_format($row['estimasi_dpt'], 0, ',', '.') : '-' }}
                            </td>
                            
                            <!-- Infrastruktur -->
                            <td class="border-b border-r border-gray-200 p-2.5 text-center">
                                @if($row['korwe_count'] > 0)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs">{{ $row['korwe_count'] }}</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="border-b border-r border-gray-200 p-2.5 text-center">
                                @if($row['korte_count'] > 0)
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200">{{ $row['korte_count'] }} RT</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            
                            <!-- Profil Sosiologis -->
                            <td class="border-b border-r border-gray-200 p-2.5 text-xs text-gray-700 truncate" title="{{ $row['tipologi'] }}">
                                {{ $row['tipologi'] }}
                            </td>
                            <td class="border-b border-r border-gray-200 p-2.5 text-xs text-gray-700 truncate" title="{{ $row['ekonomi_dominan'] }}">
                                {{ $row['ekonomi_dominan'] }}
                            </td>
                            <td class="border-b border-r border-gray-200 p-2.5 text-xs text-gray-700 truncate" title="{{ $row['profil_warga'] }}">
                                {{ $row['profil_warga'] }}
                            </td>
                            
                            <!-- Ketokohan -->
                            <td class="border-b border-r border-gray-200 p-2.5 text-xs font-medium text-gray-800 truncate" title="{{ $row['afiliasi_ketua_rw'] }}">
                                {{ $row['afiliasi_ketua_rw'] }}
                            </td>
                            <td class="border-b border-r border-gray-200 p-2.5 text-xs font-medium text-gray-800 truncate" title="{{ $row['afiliasi_tomas'] }}">
                                {{ $row['afiliasi_tomas'] }}
                            </td>
                            
                            <!-- Analisa -->
                            <td class="border-b border-r border-gray-200 p-2.5 text-xs text-gray-700 truncate" title="{{ $row['faktor_penyebab'] }}">
                                {{ $row['faktor_penyebab'] }}
                            </td>
                            <td class="border-b border-r border-gray-200 p-2.5 text-sm text-right font-bold text-orange-600 bg-orange-50/30">
                                {{ $row['suara_pks_2019'] > 0 ? number_format($row['suara_pks_2019'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="border-b border-r border-gray-200 p-2.5 text-center">
                                @if($row['is_complete'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800 border border-green-200">
                                        Lengkap
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                        Belum Lengkap
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Status Bar -->
        <div class="flex-none bg-white border-t border-gray-200 p-2 px-4 text-xs text-gray-500 flex justify-between items-center z-20">
            <div>
                Gunakan scroll horizontal (Shift + Scroll) untuk melihat kolom lainnya.
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-sm bg-emerald-100 border border-emerald-200"></span>
                    <span>Indikator Infrastruktur</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-sm bg-blue-50 border border-blue-200"></span>
                    <span>Indikator Ketokohan</span>
                </div>
            </div>
        </div>
    @endif
</div>
