@use('Carbon\Carbon')

<div class="min-h-screen bg-zinc-50/50 p-3 font-sans antialiased">
    <div class="max-w-7xl mx-auto flex flex-col gap-3">
        
        {{-- ZONE 1 — Header --}}
        <div class="bg-white border border-zinc-100 rounded-xl p-4 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/pks-logo.png') }}" alt="Logo PKS" class="h-10 md:h-12 w-auto object-contain flex-shrink-0">
                <div>
                    <h1 class="text-lg md:text-xl font-extrabold text-zinc-900 tracking-tight">Dashboard Pemenangan DPD PKS Kabupaten Bekasi</h1>
                    <p class="text-[11px] text-zinc-500 mt-0.5 flex items-center gap-1.5">
                        <span class="inline-block w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                        DPD PKS Kabupaten Bekasi &middot; Periode: <span class="font-semibold text-zinc-700">{{ $this->periodLabel }}</span>
                    </p>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-4 md:gap-6 lg:justify-end">
                @if (($this->accessScope['mode'] ?? 'global') === 'dapil')
                    <div class="flex flex-col gap-0.5 items-end mr-2">
                        <span class="text-[9px] text-zinc-500 font-bold uppercase tracking-wider">Scope Akses</span>
                        <div class="text-xs border border-orange-200 rounded-lg px-3 py-1.5 bg-orange-50 text-orange-700 font-bold shadow-sm whitespace-nowrap">
                            {{ $this->accessScope['locked_dapil'] ?: 'Scope Dapil' }}
                        </div>
                    </div>
                @endif
                
                {{-- Target Bupati --}}
                <div class="text-right">
                    <div class="text-[10px] text-zinc-700 font-black uppercase tracking-wider">Target Bupati</div>
                    <div class="text-2xl md:text-3xl font-black text-orange-600 mt-1 leading-none">1</div>
                </div>

                {{-- Kursi DPR RI --}}
                <div class="text-right">
                    <div class="text-[10px] text-zinc-700 font-black uppercase tracking-wider">Kursi DPR RI</div>
                    <div class="text-2xl md:text-3xl font-black text-orange-600 mt-1 leading-none">2</div>
                </div>

                {{-- Kursi DPRD Prov --}}
                <div class="text-right">
                    <div class="text-[10px] text-zinc-700 font-black uppercase tracking-wider">Kursi DPRD Prov</div>
                    <div class="text-2xl md:text-3xl font-black text-orange-600 mt-1 leading-none">3</div>
                </div>

                {{-- Kursi DPRD Kab --}}
                <div class="text-right">
                    <div class="text-[10px] text-zinc-700 font-black uppercase tracking-wider">Kursi DPRD Kab</div>
                    <div class="text-2xl md:text-3xl font-black text-orange-600 mt-1 leading-none">14</div>
                </div>

                {{-- Target Suara 2029 --}}
                <div class="text-right">
                    <div class="text-[10px] text-zinc-700 font-black uppercase tracking-wider">Target Suara 2029</div>
                    <div class="text-2xl md:text-3xl font-black text-orange-600 mt-1 leading-none">350.000</div>
                </div>
            </div>
        </div>

        {{-- ZONE 1.5 — KPI Utama --}}
        @php
            $totalProgram = 0;
            $programTerlaksana = 0;
            $programPct = 0;
            if (\Illuminate\Support\Facades\Schema::hasTable('program_kerjas')) {
                $totalProgram = (int) \App\Models\ProgramKerja::query()->where('tahun', $this->selectedTahun)->count();
                $programTerlaksana = (int) \App\Models\ProgramKerja::query()->where('tahun', $this->selectedTahun)->where('status', 'selesai')->count();
                $programPct = $totalProgram > 0 ? (int) round(($programTerlaksana / $totalProgram) * 100) : 0;
            }
        @endphp
        <div class="bg-white border border-zinc-100 rounded-xl p-4 shadow-sm">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                <div class="p-1.5 hover:bg-zinc-50/50 rounded-lg transition duration-200">
                    <div class="text-[10px] uppercase tracking-wider text-zinc-500 font-extrabold">Kader Aktif</div>
                    <div class="text-xl font-extrabold text-zinc-900 mt-0.5">{{ number_format($this->kpi['totalKader']) }}</div>
                    <div class="text-[10px] text-zinc-500 mt-0.5"><span class="text-orange-600 font-bold">+{{ number_format($this->kpi['kaderBulanIni']) }}</span> bulan ini</div>
                </div>
                <div class="p-1.5 hover:bg-zinc-50/50 rounded-lg transition duration-200">
                    <div class="text-[10px] uppercase tracking-wider text-zinc-500 font-extrabold">KORWE Terbentuk</div>
                    <div class="text-xl font-extrabold text-zinc-900 mt-0.5">{{ $this->kpi['korwePct'] }}%</div>
                    <div class="text-[10px] text-zinc-500 mt-0.5"><span class="text-orange-600 font-bold">{{ number_format($this->kpi['korweTerbentuk']) }}</span> dari {{ number_format($this->kpi['targetKorwe']) }} target</div>
                </div>
                <div class="p-1.5 hover:bg-zinc-50/50 rounded-lg transition duration-200">
                    <div class="text-[10px] uppercase tracking-wider text-zinc-500 font-extrabold">Sisir RW</div>
                    <div class="text-xl font-extrabold text-zinc-900 mt-0.5">{{ $this->kpi['sisirPct'] }}%</div>
                    <div class="text-[10px] text-zinc-500 mt-0.5"><span class="text-blue-600 font-bold">{{ number_format($this->kpi['rwTersisir']) }}</span> dari {{ number_format($this->kpi['totalRw']) }} RW</div>
                </div>
                <div class="p-1.5 hover:bg-zinc-50/50 rounded-lg transition duration-200">
                    <div class="text-[10px] uppercase tracking-wider text-zinc-500 font-extrabold">Aspirasi POKIR</div>
                    <div class="text-xl font-extrabold text-zinc-900 mt-0.5">50</div>
                    <div class="text-[10px] text-zinc-500 mt-0.5"><span class="text-emerald-600 font-bold">28</span> masuk SIPD</div>
                </div>
                <div class="p-1.5 hover:bg-zinc-50/50 rounded-lg transition duration-200">
                    <div class="text-[10px] uppercase tracking-wider text-zinc-500 font-extrabold">Program Bidang</div>
                    <div class="text-xl font-extrabold text-zinc-900 mt-0.5">{{ $programPct }}%</div>
                    <div class="text-[10px] text-zinc-500 mt-0.5"><span class="text-purple-600 font-bold">{{ number_format($programTerlaksana) }}</span> dari {{ number_format($totalProgram) }} program</div>
                </div>
            </div>
        </div>

        {{-- ZONE 2 ΓÇö Peta 7 Dapil --}}
        <div class="bg-white border border-zinc-100 rounded-xl p-4 shadow-sm">
            <div class="flex justify-between items-center gap-3 mb-3">
                <div class="text-[10px] uppercase tracking-wider text-zinc-500 font-extrabold">Peta Sebaran Dapil</div>
                
                {{-- Filter Bulan & Tahun --}}
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[9px] text-zinc-400 font-bold uppercase tracking-wider">Bulan:</span>
                        <select wire:model.live="selectedBulan" class="text-xs border border-zinc-200 rounded-lg px-2 py-1 bg-white text-zinc-700 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition min-w-[100px]">
                            @foreach (range(1, 12) as $bulan)
                                <option value="{{ $bulan }}">{{ Carbon::create(null, $bulan)->translatedFormat('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex items-center gap-1.5">
                        <span class="text-[9px] text-zinc-400 font-bold uppercase tracking-wider">Tahun:</span>
                        <select wire:model.live="selectedTahun" class="text-xs border border-zinc-200 rounded-lg px-2 py-1 bg-white text-zinc-700 shadow-sm focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition min-w-[80px]">
                            @foreach (range((int) now()->year - 1, (int) now()->year + 2) as $tahun)
                                <option value="{{ $tahun }}">{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto pb-1">
                <div style="display: grid; grid-template-columns: repeat(8, minmax(0, 1fr)); gap: 8px; width: 100%;" class="w-full min-w-[860px] lg:min-w-0">
                    {{-- 1. Peta Kab. Bekasi (Semua Dapil) --}}
                    <a href="#" wire:click.prevent="$set('selectedDapil', '')"
                       style="display: flex; flex-direction: column; justify-content: space-between; height: 100%;"
                       class="group rounded-lg border p-2.5 pb-3.5 text-center cursor-pointer transition-all duration-200 hover:shadow-md hover:scale-[1.02] active:scale-[0.98]
                       {{ empty($this->selectedDapil) ? 'border-orange-500 bg-orange-50/60 shadow-sm' : 'border-zinc-100 bg-zinc-50/60 hover:border-zinc-300' }}">
                        
                        <div>
                            {{-- Peta gambar --}}
                            <div class="h-28 flex items-center justify-center mb-1 bg-white rounded-md p-0.5">
                                <img src="{{ asset('images/peta/kabupaten-bekasi.png') }}" style="max-height: 100px; max-width: 100%; object-fit: contain; width: auto; height: auto;" class="transition-all duration-200 group-hover:scale-105" alt="Peta Kab. Bekasi">
                            </div>
                            <div class="text-xs font-extrabold tracking-tight transition {{ empty($this->selectedDapil) ? 'text-orange-600' : 'text-zinc-800' }}">
                                Kab. Bekasi
                            </div>
                            <div class="text-[10px] text-zinc-500 mt-0.5 font-bold">{{ $this->dapilMap->sum('desa_total') }} desa</div>
                        </div>
                        
                        <div style="padding-top: 8px; font-size: 9px; font-weight: 500; color: #71717a;" class="flex justify-between items-center mt-2.5 border-t border-zinc-200/80">
                            <span title="KORWE terbentuk">{{ $this->kpi['korwePct'] }}%KW</span>
                            <span title="Sisir RW">{{ $this->kpi['sisirPct'] }}%SR</span>
                            <span title="Profil RW">{{ $this->kpi['profilPct'] }}%PR</span>
                        </div>
                    </a>

                    {{-- 2. Peta Per Dapil --}}
                    @foreach ($this->dapilMap as $dapil)
                        <a href="#" wire:click.prevent="$set('selectedDapil', '{{ $dapil['dapil'] }}')"
                           style="display: flex; flex-direction: column; justify-content: space-between; height: 100%;"
                           class="group rounded-lg border p-2.5 pb-3.5 text-center cursor-pointer transition-all duration-200 hover:shadow-md hover:scale-[1.02] active:scale-[0.98]
                           {{ $dapil['active'] ? 'border-orange-500 bg-orange-50/60 shadow-sm' : 'border-zinc-100 bg-zinc-50/60 hover:border-zinc-300' }}">
                            
                            <div>
                                {{-- Peta gambar --}}
                                <div class="h-28 flex items-center justify-center mb-1 bg-white rounded-md p-0.5">
                                    @if($dapil['image'])
                                        <img src="{{ $dapil['image'] }}" style="max-height: 100px; max-width: 100%; object-fit: contain; width: auto; height: auto;" class="transition-all duration-200 group-hover:scale-105" alt="Peta {{ $dapil['dapil'] }}">
                                    @else
                                        <div class="h-full w-full bg-zinc-100 rounded-md"></div>
                                    @endif
                                </div>
                                <div class="text-xs font-extrabold tracking-tight transition {{ $dapil['active'] ? 'text-orange-600' : 'text-zinc-800' }}">
                                    Dapil {{ $dapil['dapil_number'] }}
                                </div>
                                <div class="text-[10px] text-zinc-500 mt-0.5 font-bold">{{ $dapil['desa_total'] }} desa</div>
                            </div>
                            
                            <div style="padding-top: 8px; font-size: 9px; font-weight: 500; color: #71717a;" class="flex justify-between items-center mt-2.5 border-t border-zinc-200/80">
                                <span title="KORWE terbentuk">{{ $dapil['korwe_pct'] }}%KW</span>
                                <span title="Sisir RW">{{ $dapil['sisir_pct'] }}%SR</span>
                                <span title="Profil RW">{{ $dapil['profil_pct'] }}%PR</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-4 mt-2.5 pt-2 border-t border-zinc-100 text-[10px] text-zinc-400 font-medium">
                <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>KW = KORWE</span>
                <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>SR = Sisir RW</span>
                <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>PR = Profil RW</span>
            </div>
        </div>

        {{-- ZONE 3 — Tab System --}}
        @php
            $customAlerts = $this->alerts;
            
            // Add Saksi TPS alert if there are missing saksi
            $missingSaksi = max(0, ($this->kpi['totalTps'] ?? 0) - ($this->kpi['saksiTps'] ?? 0));
            if ($missingSaksi > 0) {
                $totalTpsVal = max(1, $this->kpi['totalTps'] ?? 1);
                $pct = round(($missingSaksi / $totalTpsVal) * 100);
                $customAlerts[] = [
                    'type' => 'danger',
                    'icon' => 'circle-x',
                    'text' => '<strong>' . number_format($missingSaksi) . ' TPS</strong> belum memiliki Saksi terpilih (' . $pct . '% dari total)',
                    'link' => Route::has('saksi.index') ? route('saksi.index') : '#',
                    'link_text' => 'Lihat'
                ];
            }
            
            // Add KORWE alert if there are missing korwe
            $missingKorwe = max(0, ($this->kpi['targetKorwe'] ?? 0) - ($this->kpi['korweTerbentuk'] ?? 0));
            if ($missingKorwe > 0) {
                $targetKorweVal = max(1, $this->kpi['targetKorwe'] ?? 1);
                $pct = round(($missingKorwe / $targetKorweVal) * 100);
                $customAlerts[] = [
                    'type' => 'warning',
                    'icon' => 'alert-triangle',
                    'text' => '<strong>' . number_format($missingKorwe) . ' RW</strong> belum terbentuk KORWE (' . $pct . '% dari total)',
                    'link' => Route::has('kaderisasi.index') ? route('kaderisasi.index') : '#',
                    'link_text' => 'Lihat'
                ];
            }
            
            // Add Profil RW alert if there are missing profil
            $missingProfil = max(0, ($this->kpi['totalRw'] ?? 0) - ($this->kpi['profilTerisi'] ?? 0));
            if ($missingProfil > 0) {
                $totalRwVal = max(1, $this->kpi['totalRw'] ?? 1);
                $pct = round(($missingProfil / $totalRwVal) * 100);
                $customAlerts[] = [
                    'type' => 'info',
                    'icon' => 'file-info',
                    'text' => '<strong>' . number_format($missingProfil) . ' RW</strong> belum melengkapi data Profil RW (' . $pct . '% dari total)',
                    'link' => Route::has('rw.index') ? route('rw.index') : '#',
                    'link_text' => 'Lihat'
                ];
            }
        @endphp
        <div x-data="{ tab: 'kinerja' }" class="flex flex-col gap-3">
            
            {{-- Tab Navigation --}}
            <div class="flex gap-1.5 p-1 bg-zinc-100 rounded-lg self-start overflow-x-auto max-w-full">
                <button @click="tab='kinerja'"
                    :class="tab==='kinerja' ? 'bg-white text-zinc-900 shadow-sm font-bold' : 'text-zinc-500 hover:text-zinc-800'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold whitespace-nowrap transition-all duration-200">
                    <i class="ti ti-chart-line text-sm"></i> Kinerja & Trend
                </button>
                <button @click="tab='infra'"
                    :class="tab==='infra' ? 'bg-white text-zinc-900 shadow-sm font-bold' : 'text-zinc-500 hover:text-zinc-800'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold whitespace-nowrap transition-all duration-200">
                    <i class="ti ti-building text-sm"></i> Infrastruktur
                </button>
                <button @click="tab='program'"
                    :class="tab==='program' ? 'bg-white text-zinc-900 shadow-sm font-bold' : 'text-zinc-500 hover:text-zinc-800'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold whitespace-nowrap transition-all duration-200">
                    <i class="ti ti-calendar-event text-sm"></i> Program
                </button>
                <button @click="tab='aktivitas'"
                    :class="tab==='aktivitas' ? 'bg-white text-zinc-900 shadow-sm font-bold' : 'text-zinc-500 hover:text-zinc-800'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold whitespace-nowrap transition-all duration-200">
                    <i class="ti ti-activity text-sm"></i> Aktivitas Terbaru
                </button>
                <button @click="tab='prestasi'"
                    :class="tab==='prestasi' ? 'bg-white text-zinc-900 shadow-sm font-bold' : 'text-zinc-500 hover:text-zinc-800'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold whitespace-nowrap transition-all duration-200">
                    <i class="ti ti-award text-sm"></i> Prestasi & Capaian
                </button>
                <button @click="tab='alert'"
                    :class="tab==='alert' ? 'bg-white text-zinc-900 shadow-sm font-bold' : 'text-zinc-500 hover:text-zinc-800'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold whitespace-nowrap transition-all duration-200">
                    <i class="ti ti-bell text-sm"></i> Perlu Perhatian
                    @if(count($customAlerts) > 0)
                        <span class="bg-red-500 text-white rounded-full px-1.5 py-0.5 text-[9px] font-bold animate-pulse leading-none">{{ count($customAlerts) }}</span>
                    @endif
                </button>
            </div>

            {{-- ===== TAB INFRASTRUKTUR ===== --}}
            <div x-show="tab==='infra'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="w-full flex flex-col gap-3">
                @php
                    $penggalangPct = ($this->kpi['targetPenggalang'] ?? 0) > 0 ? (int) round((($this->kpi['penggalang'] ?? 0) / $this->kpi['targetPenggalang']) * 100) : 0;
                @endphp
                
                {{-- 3 Metric utama --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    {{-- KORWE --}}
                    <div class="bg-white border border-zinc-100 rounded-xl p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div class="flex items-start justify-between">
                            <span class="text-[10px] uppercase tracking-wider text-zinc-500 font-extrabold">KORWE TERBENTUK</span>
                            <div class="text-right leading-none shrink-0">
                                <div class="text-[13px] font-extrabold text-zinc-800">{{ number_format($this->kpi['korweTerbentuk']) }}</div>
                                <div class="text-[9px] font-bold text-zinc-400 mt-0.5">/{{ number_format($this->kpi['targetKorwe']) }}</div>
                            </div>
                        </div>
                        <div class="flex items-end justify-between mt-2">
                            <div class="text-3xl font-black text-zinc-900 flex items-baseline leading-none">
                                {{ $this->kpi['korwePct'] }}<span class="text-lg font-bold text-zinc-400 ml-0.5">%</span>
                            </div>
                            <div class="text-[10px] text-zinc-500 font-bold leading-none pb-0.5">{{ number_format(max(0, $this->kpi['targetKorwe'] - $this->kpi['korweTerbentuk'])) }} RW tersisa</div>
                        </div>
                    </div>

                    {{-- KORTE --}}
                    <div class="bg-white border border-zinc-100 rounded-xl p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div class="flex items-start justify-between">
                            <span class="text-[10px] uppercase tracking-wider text-zinc-500 font-extrabold">KORTE TERBENTUK</span>
                            <div class="text-right leading-none shrink-0">
                                <div class="text-[13px] font-extrabold text-zinc-800">{{ number_format($this->kpi['korteTerbentuk']) }}</div>
                                <div class="text-[9px] font-bold text-zinc-400 mt-0.5">/{{ number_format($this->kpi['targetKorte']) }}</div>
                            </div>
                        </div>
                        <div class="flex items-end justify-between mt-2">
                            <div class="text-3xl font-black text-zinc-900 flex items-baseline leading-none">
                                {{ $this->kpi['kortePct'] }}<span class="text-lg font-bold text-zinc-400 ml-0.5">%</span>
                            </div>
                            <div class="text-[10px] text-zinc-500 font-bold leading-none pb-0.5">{{ number_format(max(0, $this->kpi['targetKorte'] - $this->kpi['korteTerbentuk'])) }} korte tersisa</div>
                        </div>
                    </div>

                    {{-- PENGGALANG --}}
                    <div class="bg-white border border-zinc-100 rounded-xl p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div class="flex items-start justify-between">
                            <span class="text-[10px] uppercase tracking-wider text-zinc-500 font-extrabold">PENGGALANG AKTIF</span>
                            <div class="text-right leading-none shrink-0">
                                <div class="text-[13px] font-extrabold text-zinc-800">{{ number_format($this->kpi['penggalang']) }}</div>
                                <div class="text-[9px] font-bold text-zinc-400 mt-0.5">/{{ number_format($this->kpi['targetPenggalang']) }}</div>
                            </div>
                        </div>
                        <div class="flex items-end justify-between mt-2">
                            <div class="text-3xl font-black text-zinc-900 flex items-baseline leading-none">
                                {{ $penggalangPct }}<span class="text-lg font-bold text-zinc-400 ml-0.5">%</span>
                            </div>
                            <div class="text-[10px] text-zinc-500 font-bold leading-none pb-0.5">{{ number_format(max(0, ($this->kpi['targetPenggalang'] ?? 0) - ($this->kpi['penggalang'] ?? 0))) }} target tersisa</div>
                        </div>
                    </div>
                </div>

                {{-- 6 Mini Cards Pendukung --}}
                <div style="display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 8px; margin-top: 4px;">
                    @foreach ([
                        ['label' => 'Sisir RW', 'value' => $this->kpi['rwTersisir'], 'sub' => '/' . $this->kpi['totalRw'] . ' RW', 'icon' => 'ti-brush', 'color' => 'text-orange-500', 'bg' => 'bg-orange-50/30'],
                        ['label' => 'UPA RW', 'value' => $this->kpi['upaRw'], 'sub' => $this->kpi['upaRwFormal'] . ' formal', 'icon' => 'ti-building-community', 'color' => 'text-green-600', 'bg' => 'bg-green-50/30'],
                        ['label' => 'Profil RW', 'value' => $this->kpi['profilTerisi'], 'sub' => '/' . $this->kpi['totalRw'] . ' RW', 'icon' => 'ti-file-text', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50/30'],
                        ['label' => 'Relawan', 'value' => $this->kpi['relawan'], 'sub' => 'milenial', 'icon' => 'ti-heart-handshake', 'color' => 'text-purple-600', 'bg' => 'bg-purple-50/30'],
                        ['label' => 'Saksi TPS', 'value' => $this->kpi['saksiTps'], 'sub' => '/' . $this->kpi['totalTps'] . ' TPS', 'icon' => 'ti-checkup-list', 'color' => 'text-teal-600', 'bg' => 'bg-teal-50/30'],
                        ['label' => 'Event Aktif', 'value' => $this->kpi['eventAktif'], 'sub' => $this->kpi['eventMenunggu'] . ' pending', 'icon' => 'ti-calendar-event', 'color' => $this->kpi['eventMenunggu'] > 0 ? 'text-amber-600' : 'text-zinc-500', 'bg' => 'bg-zinc-50/50'],
                    ] as $m)
                        <div class="bg-white border border-zinc-100 rounded-lg p-2.5 flex items-center gap-2 hover:border-zinc-200 transition">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $m['bg'] }} shrink-0">
                                <i class="ti {{ $m['icon'] }} text-base {{ $m['color'] }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-[10px] text-zinc-600 font-extrabold uppercase tracking-wider truncate">{{ $m['label'] }}</div>
                                <div class="text-base font-black text-zinc-900 mt-0.5">{{ number_format($m['value']) }}</div>
                                <div class="text-[9px] text-zinc-500 font-bold truncate">{{ $m['sub'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ===== TAB PROGRAM ===== --}}
            <div x-show="tab==='program'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="w-full">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ([
                        ['label' => 'Event', 'icon' => 'ti-calendar-event', 'color' => '#2563eb', 'value' => $this->kpi['eventSelesai'] . ' selesai', 'right_val' => $this->kpi['eventMenunggu'], 'right_lbl' => 'pending', 'route' => route('events.index')],
                        ['label' => 'RKI (Bipeka)', 'icon' => 'ti-home-heart', 'color' => '#db2777', 'value' => $this->kpi['rki'] . ' aktif', 'sub' => 'Titik RKI terbentuk', 'route' => route('rki.index')],
                        ['label' => 'KSN / Senam', 'icon' => 'ti-stretching', 'color' => '#16a34a', 'value' => $this->kpi['ksn'] . ' aktif', 'sub' => 'Titik senam terbentuk', 'route' => route('ksn.index')],
                        ['label' => 'Kaderisasi', 'icon' => 'ti-users-group', 'color' => '#7c3aed', 'value' => number_format($this->kpi['totalKader']) . ' kader', 'right_val' => '+' . number_format($this->kpi['kaderBulanIni']), 'right_lbl' => 'bulan ini', 'route' => route('kaderisasi.index')],
                        ['label' => 'Sosial Media', 'icon' => 'ti-brand-instagram', 'color' => '#ea580c', 'value' => $this->kpi['kontenBulanIni'] . ' konten', 'right_val' => $this->kpi['avgPopularitas'], 'right_lbl' => 'avg pop', 'route' => route('sosial-media.index')],
                        ['label' => 'Aspirasi & POKIR', 'icon' => 'ti-message-chatbot', 'color' => '#0891b2', 'value' => '50 total', 'right_val' => '28', 'right_lbl' => 'SIPD', 'sub' => '7 realisasi', 'route' => route('aspirasi.index')],
                    ] as $p)
                        <a href="{{ $p['route'] }}" wire:navigate
                           class="group bg-white border border-zinc-100 rounded-xl p-4 hover:border-zinc-300 hover:shadow-md hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 block">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center group-hover:scale-110 transition" style="background:{{ $p['color'] }}14;">
                                        <i class="ti {{ $p['icon'] }} text-base" style="color:{{ $p['color'] }};"></i>
                                    </div>
                                    <div class="text-xs font-extrabold text-zinc-800">{{ $p['label'] }}</div>
                                </div>
                                @if(isset($p['right_val']))
                                    <div class="text-right leading-none shrink-0 mt-0.5">
                                        <div class="text-[13px] font-extrabold text-zinc-800">{{ $p['right_val'] }}</div>
                                        <div class="text-[9px] font-bold text-zinc-400 mt-0.5">{{ $p['right_lbl'] }}</div>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-baseline justify-between">
                                <div class="text-lg font-black text-zinc-900">{{ $p['value'] }}</div>
                                @if(isset($p['sub']))
                                    <div class="text-[10px] text-zinc-500 font-bold leading-none shrink-0">{{ $p['sub'] }}</div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ===== TAB KINERJA & TREND ===== --}}
            <div x-show="tab==='kinerja'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="w-full flex flex-col gap-3">
                @php
                    $penggalangPct = ($this->kpi['targetPenggalang'] ?? 0) > 0 ? (int) round((($this->kpi['penggalang'] ?? 0) / $this->kpi['targetPenggalang']) * 100) : 0;
                @endphp
                
                {{-- Trend infrastruktur 6 bulan --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @foreach ($this->infraTrends->take(3) as $trend)
                        <div class="bg-white border border-zinc-100 rounded-xl p-4 shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-xs font-extrabold text-zinc-700">{{ $trend['label'] }}</span>
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $trend['change_pct'] >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-500' }}">
                                    {{ $trend['change_pct'] >= 0 ? '+' : '' }}{{ $trend['change_pct'] }}%
                                </span>
                            </div>
                            <div class="text-xl font-black mb-2" style="color:{{ $trend['color'] }}">{{ $trend['current'] }}</div>
                            
                            {{-- Mini bar chart --}}
                            <div class="flex items-end gap-1.5 h-6 pt-0.5">
                                @foreach ($trend['months'] as $m)
                                    <div class="flex-1 rounded-t-sm transition-all duration-300 {{ $m['current'] ? 'brightness-95' : 'opacity-30' }}"
                                         title="{{ $m['label'] }}: {{ $m['count'] }}"
                                         style="height:{{ max(8, $m['pct']) }}%; background:{{ $trend['color'] }};"></div>
                                @endforeach
                            </div>
                            <div class="flex justify-between text-[9px] text-zinc-500 font-bold mt-1.5">
                                @foreach ($trend['months'] as $m)
                                    <span>{{ $m['label'] }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- 3 Metric utama --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    {{-- KORWE --}}
                    <div class="bg-white border border-zinc-100 rounded-xl p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div class="flex items-start justify-between">
                            <span class="text-[10px] uppercase tracking-wider text-zinc-500 font-extrabold">KORWE TERBENTUK</span>
                            <div class="text-right leading-none shrink-0">
                                <div class="text-[13px] font-extrabold text-zinc-800">{{ number_format($this->kpi['korweTerbentuk']) }}</div>
                                <div class="text-[9px] font-bold text-zinc-400 mt-0.5">/{{ number_format($this->kpi['targetKorwe']) }}</div>
                            </div>
                        </div>
                        <div class="flex items-end justify-between mt-2">
                            <div class="text-3xl font-black text-zinc-900 flex items-baseline leading-none">
                                {{ $this->kpi['korwePct'] }}<span class="text-lg font-bold text-zinc-400 ml-0.5">%</span>
                            </div>
                            <div class="text-[10px] text-zinc-500 font-bold leading-none pb-0.5">{{ number_format(max(0, $this->kpi['targetKorwe'] - $this->kpi['korweTerbentuk'])) }} RW tersisa</div>
                        </div>
                    </div>

                    {{-- KORTE --}}
                    <div class="bg-white border border-zinc-100 rounded-xl p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div class="flex items-start justify-between">
                            <span class="text-[10px] uppercase tracking-wider text-zinc-500 font-extrabold">KORTE TERBENTUK</span>
                            <div class="text-right leading-none shrink-0">
                                <div class="text-[13px] font-extrabold text-zinc-800">{{ number_format($this->kpi['korteTerbentuk']) }}</div>
                                <div class="text-[9px] font-bold text-zinc-400 mt-0.5">/{{ number_format($this->kpi['targetKorte']) }}</div>
                            </div>
                        </div>
                        <div class="flex items-end justify-between mt-2">
                            <div class="text-3xl font-black text-zinc-900 flex items-baseline leading-none">
                                {{ $this->kpi['kortePct'] }}<span class="text-lg font-bold text-zinc-400 ml-0.5">%</span>
                            </div>
                            <div class="text-[10px] text-zinc-500 font-bold leading-none pb-0.5">{{ number_format(max(0, $this->kpi['targetKorte'] - $this->kpi['korteTerbentuk'])) }} korte tersisa</div>
                        </div>
                    </div>

                    {{-- PENGGALANG --}}
                    <div class="bg-white border border-zinc-100 rounded-xl p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div class="flex items-start justify-between">
                            <span class="text-[10px] uppercase tracking-wider text-zinc-500 font-extrabold">PENGGALANG AKTIF</span>
                            <div class="text-right leading-none shrink-0">
                                <div class="text-[13px] font-extrabold text-zinc-800">{{ number_format($this->kpi['penggalang']) }}</div>
                                <div class="text-[9px] font-bold text-zinc-400 mt-0.5">/{{ number_format($this->kpi['targetPenggalang']) }}</div>
                            </div>
                        </div>
                        <div class="flex items-end justify-between mt-2">
                            <div class="text-3xl font-black text-zinc-900 flex items-baseline leading-none">
                                {{ $penggalangPct }}<span class="text-lg font-bold text-zinc-400 ml-0.5">%</span>
                            </div>
                            <div class="text-[10px] text-zinc-500 font-bold leading-none pb-0.5">{{ number_format(max(0, ($this->kpi['targetPenggalang'] ?? 0) - ($this->kpi['penggalang'] ?? 0))) }} target tersisa</div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ===== TAB PRESTASI & CAPAIAN ===== --}}
            <div x-show="tab==='prestasi'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="w-full">
                @php
                    $highlights = array_merge($this->operationalHighlights, [
                        [
                            'label' => 'Program Bidang Terlaksana',
                            'theme' => 'orange',
                            'value' => $programTerlaksana . ' program selesai',
                            'meta' => 'Dari total ' . $totalProgram . ' program',
                            'detail' => 'Persentase ketercapaian ' . $programPct . '% di tahun ' . $this->selectedTahun,
                        ],
                        [
                            'label' => 'Ketercapaian KORWE',
                            'theme' => 'blue',
                            'value' => number_format($this->kpi['korweTerbentuk']) . ' RW terbentuk',
                            'meta' => 'Target: ' . number_format($this->kpi['targetKorwe']) . ' RW',
                            'detail' => 'Persentase keterbentukan mencapai ' . $this->kpi['korwePct'] . '%',
                        ],
                        [
                            'label' => 'Penyisiran Sisir RW',
                            'theme' => 'orange',
                            'value' => number_format($this->kpi['rwTersisir']) . ' RW tersisir',
                            'meta' => 'Dari total ' . number_format($this->kpi['totalRw']) . ' RW',
                            'detail' => 'Terlaksana ' . number_format($this->kpi['kegiatanBulanIni']) . ' kegiatan lapangan bulan ini',
                        ],
                        [
                            'label' => 'Kader & Relawan',
                            'theme' => 'blue',
                            'value' => number_format($this->kpi['totalKader']) . ' kader aktif',
                            'meta' => '+' . number_format($this->kpi['kaderBulanIni']) . ' baru bulan ini',
                            'detail' => 'Relawan milenial dan saksi TPS siap bergerak',
                        ]
                    ]);
                @endphp
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach ($highlights as $h)
                        <div class="bg-white border-l-4 border-y border-r border-zinc-100 rounded-xl p-3 shadow-sm hover:border-zinc-200 transition"
                             style="border-left-color:{{ $h['theme'] === 'orange' ? '#ea580c' : '#2563eb' }};">
                            <div class="text-[9px] font-extrabold uppercase tracking-wider mb-1"
                                 style="color:{{ $h['theme'] === 'orange' ? '#ea580c' : '#2563eb' }};">{{ $h['label'] }}</div>
                            <div class="text-sm font-extrabold text-zinc-900 truncate" title="{{ $h['value'] }}">{{ $h['value'] }}</div>
                            <div class="text-[10px] text-zinc-600 font-bold mt-1">{{ $h['meta'] }}</div>
                            <div class="text-[10px] text-zinc-400 mt-0.5 leading-normal">{{ $h['detail'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ===== TAB AKTIVITAS TERBARU ===== --}}
            <div x-show="tab==='aktivitas'" 
                 x-data="{ page: 1, perPage: 3, total: {{ count($this->timeline) }} }" 
                 x-cloak 
                 x-transition:enter="transition ease-out duration-200" 
                 x-transition:enter-start="opacity-0 translate-y-1" 
                 x-transition:enter-end="opacity-100 translate-y-0" 
                 class="w-full flex flex-col gap-3">
                <div class="bg-white border border-zinc-100 rounded-xl p-4 shadow-sm">
                    <div class="flex justify-between items-center mb-3">
                        <div class="text-[10px] uppercase tracking-wider text-zinc-600 font-extrabold">Aktivitas Terbaru</div>
                        
                        {{-- Paging buttons in the top right corner --}}
                        <div class="flex items-center gap-1.5" x-show="total > perPage">
                            <button @click="if(page > 1) page--" 
                                    :disabled="page === 1"
                                    :class="page === 1 ? 'opacity-40 cursor-not-allowed text-zinc-400 bg-zinc-50' : 'text-zinc-700 bg-white hover:bg-zinc-50 border-zinc-200 active:scale-95'"
                                    class="w-6 h-6 rounded-lg flex items-center justify-center border transition shadow-sm">
                                <i class="ti ti-chevron-left text-xs font-bold"></i>
                            </button>
                            <span class="text-[10px] font-bold text-zinc-500 min-w-[70px] text-center">
                                Halaman <span x-text="page"></span> / <span x-text="Math.ceil(total / perPage)"></span>
                            </span>
                            <button @click="if(page < Math.ceil(total / perPage)) page++" 
                                    :disabled="page === Math.ceil(total / perPage)"
                                    :class="page === Math.ceil(total / perPage) ? 'opacity-40 cursor-not-allowed text-zinc-400 bg-zinc-50' : 'text-zinc-700 bg-white hover:bg-zinc-50 border-zinc-200 active:scale-95'"
                                    class="w-6 h-6 rounded-lg flex items-center justify-center border transition shadow-sm">
                                <i class="ti ti-chevron-right text-xs font-bold"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        @foreach ($this->timeline as $index => $item)
                            <div x-show="Math.floor({{ $index }} / perPage) + 1 === page"
                                 class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-0.5"
                                     style="background:{{ $item['color'] }}14;">
                                    <i class="ti ti-{{ $item['icon'] }} text-[10px]" style="color:{{ $item['color'] }};"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-bold text-zinc-900 truncate">{{ $item['title'] }}</div>
                                    <div class="text-[10px] text-zinc-500 font-semibold mt-0.5">{{ $item['desc'] }}</div>
                                </div>
                                <div class="text-[10px] text-zinc-400 whitespace-nowrap shrink-0 self-center">
                                    {{ $item['time'] ? Carbon::parse($item['time'])->diffForHumans() : '' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ===== TAB PERLU PERHATIAN ===== --}}
            <div x-show="tab==='alert'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="w-full flex flex-col gap-3">
                @if(count($customAlerts) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        @foreach ($customAlerts as $alert)
                            @php
                                $c = [
                                    'warning' => [
                                        'color' => '#d97706',
                                        'btn' => 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-100'
                                    ],
                                    'danger' => [
                                        'color' => '#dc2626',
                                        'btn' => 'bg-red-50 text-red-700 hover:bg-red-100 border border-red-100'
                                    ],
                                    'info' => [
                                        'color' => '#2563eb',
                                        'btn' => 'bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-100'
                                    ],
                                ][$alert['type']] ?? [
                                    'color' => '#2563eb',
                                    'btn' => 'bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-100'
                                ];
                            @endphp
                            <div class="group bg-white border border-zinc-100 rounded-xl p-4 hover:border-zinc-300 hover:shadow-md hover:scale-[1.02] transition-all duration-300 flex flex-col justify-between">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-110 transition" style="background:{{ $c['color'] }}14;">
                                        <i class="ti ti-{{ $alert['icon'] }} text-base" style="color:{{ $c['color'] }};"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs font-bold leading-relaxed text-zinc-800">
                                            {!! $alert['text'] !!}
                                        </div>
                                        @if(isset($alert['link']))
                                            <div class="mt-2.5 flex justify-start">
                                                <a href="{{ $alert['link'] }}" wire:navigate class="inline-flex items-center text-[10px] font-black px-2.5 py-1 rounded-md transition shadow-sm {{ $c['btn'] }}">
                                                    {{ $alert['link_text'] }} &rarr;
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white border border-zinc-100 rounded-xl p-8 text-center shadow-sm w-full">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto mb-2 shadow-inner">
                            <i class="ti ti-circle-check text-xl"></i>
                        </div>
                        <h3 class="text-xs font-bold text-zinc-800">Tidak ada alert saat ini</h3>
                        <p class="text-[11px] text-zinc-400 mt-0.5">Semua program berjalan optimal dan sesuai target.</p>
                    </div>
                @endif
            </div>

        </div>
        
    </div>
</div>
