@php($coverage = $this->coverage)

<section class="min-h-screen bg-zinc-50 pb-16">
    <div class="container">
        <div style="background:white; border-radius:16px; padding:24px; margin-top:24px; border:1px solid #f4f4f5;">
            <h1 style="font-size:30px; font-weight:700; color:#18181b; line-height:1.2;">DPD PKS Kabupaten Bekasi</h1>
            <p style="max-width:680px; font-size:17px; line-height:1.8; color:#71717a; margin-top:8px;">
                Bekasi Hebat adalah wajah publik untuk gerakan pelayanan, penguatan komunitas, dan konsolidasi wilayah yang dijalankan dari tingkat kabupaten hingga RW.
            </p>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-2">
            <!-- Visi Card -->
            <div class="relative overflow-hidden rounded-2xl border border-zinc-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md" style="border-left: 4px solid #ea580c;">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                        <i class="ti ti-eye" style="font-size: 24px;"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600">Visi Gerakan</span>
                </div>
                <h3 class="mt-4 text-2xl font-bold text-zinc-900 leading-snug">Kabupaten Bekasi yang lebih kuat, terlayani, dan terhubung.</h3>
                <p class="mt-4 text-zinc-500 text-[16px] leading-relaxed">
                    Gerakan ini diarahkan untuk memperluas pelayanan warga, memperkuat jaringan komunitas, dan menumbuhkan partisipasi publik yang sehat dan produktif.
                </p>
            </div>

            <!-- Misi Card -->
            <div class="relative overflow-hidden rounded-2xl border border-zinc-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md" style="border-left: 4px solid #2563eb;">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="ti ti-target" style="font-size: 24px;"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Misi Gerakan</span>
                </div>
                <ul class="mt-6 space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                            <i class="ti ti-circle-check" style="font-size: 14px;"></i>
                        </div>
                        <span class="text-zinc-600 text-[16px] leading-relaxed">Memperkuat kegiatan pelayanan dan pengorganisasian warga.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                            <i class="ti ti-circle-check" style="font-size: 14px;"></i>
                        </div>
                        <span class="text-zinc-600 text-[16px] leading-relaxed">Menyediakan kanal partisipasi melalui event, program, dan kartu anggota.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                            <i class="ti ti-circle-check" style="font-size: 14px;"></i>
                        </div>
                        <span class="text-zinc-600 text-[16px] leading-relaxed">Menghubungkan data lapangan dengan dashboard internal untuk keputusan yang lebih cepat.</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-3">
            <!-- Dapil -->
            <div class="relative overflow-hidden rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-orange-50 text-orange-600">
                        <i class="ti ti-map-2" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-zinc-900">{{ $coverage['dapil'] }}</div>
                        <div class="text-sm font-medium text-zinc-500">Daerah Pemilihan (Dapil)</div>
                    </div>
                </div>
            </div>

            <!-- Kecamatan -->
            <div class="relative overflow-hidden rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-orange-50 text-orange-600">
                        <i class="ti ti-building-community" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-zinc-900">{{ $coverage['kecamatan'] }}</div>
                        <div class="text-sm font-medium text-zinc-500">Kecamatan Resmi</div>
                    </div>
                </div>
            </div>

            <!-- Desa -->
            <div class="relative overflow-hidden rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-orange-50 text-orange-600">
                        <i class="ti ti-home" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-zinc-900">{{ $coverage['desa'] }}</div>
                        <div class="text-sm font-medium text-zinc-500">Desa / Kelurahan</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-2">
            <!-- Cakupan Data Card -->
            <div class="relative overflow-hidden rounded-2xl border border-zinc-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-600">
                        <i class="ti ti-database" style="font-size: 20px;"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600">Cakupan Data</span>
                </div>
                
                <div class="mt-6 space-y-4">
                    <div class="relative rounded-xl bg-zinc-50 p-4 border border-zinc-100 transition-all duration-200 hover:bg-white hover:border-zinc-200">
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-600 font-medium">Kecamatan terpetakan</span>
                            <span class="text-xl font-extrabold text-zinc-900">{{ number_format($coverage['db_kecamatan']) }}</span>
                        </div>
                        <div class="mt-2 h-1.5 w-full rounded-full bg-zinc-200 overflow-hidden">
                            <div class="h-full bg-orange-500 rounded-full" style="width: 100%;"></div>
                        </div>
                    </div>
                    
                    <div class="relative rounded-xl bg-zinc-50 p-4 border border-zinc-100 transition-all duration-200 hover:bg-white hover:border-zinc-200">
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-600 font-medium">Desa/Kelurahan terpetakan</span>
                            <span class="text-xl font-extrabold text-zinc-900">{{ number_format($coverage['db_desa']) }}</span>
                        </div>
                        <div class="mt-2 h-1.5 w-full rounded-full bg-zinc-200 overflow-hidden">
                            <div class="h-full bg-orange-500 rounded-full" style="width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kontak Card -->
            <div class="relative overflow-hidden rounded-2xl border border-zinc-100 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 text-orange-600">
                        <i class="ti ti-address-book" style="font-size: 20px;"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-600">Kontak Resmi</span>
                </div>
                
                <div class="mt-6 space-y-4">
                    <a href="https://maps.google.com/?q=Jl.+Rw.+Jaya+No.3,+Bojong+Rawalumbu,+Bekasi" target="_blank" class="flex items-start gap-4 rounded-xl bg-zinc-50 p-4 border border-zinc-100 text-zinc-600 hover:text-zinc-900 transition-all duration-200 hover:bg-white hover:border-zinc-200 hover:shadow-sm">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-50 text-orange-600 shrink-0">
                            <i class="ti ti-map-pin" style="font-size: 16px;"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-zinc-400 uppercase">Alamat Kantor</div>
                            <div class="text-sm font-medium mt-0.5">Jl. Rw. Jaya No.3, Bojong Rawalumbu, Bekasi</div>
                        </div>
                    </a>
                    
                    <a href="mailto:info@bekasihebat.local" class="flex items-start gap-4 rounded-xl bg-zinc-50 p-4 border border-zinc-100 text-zinc-600 hover:text-zinc-900 transition-all duration-200 hover:bg-white hover:border-zinc-200 hover:shadow-sm">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-50 text-orange-600 shrink-0">
                            <i class="ti ti-mail" style="font-size: 16px;"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-zinc-400 uppercase">E-mail</div>
                            <div class="text-sm font-medium mt-0.5">info@bekasihebat.local</div>
                        </div>
                    </a>
                    
                    <a href="tel:+628135631291" class="flex items-start gap-4 rounded-xl bg-zinc-50 p-4 border border-zinc-100 text-zinc-600 hover:text-zinc-900 transition-all duration-200 hover:bg-white hover:border-zinc-200 hover:shadow-sm">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-50 text-orange-600 shrink-0">
                            <i class="ti ti-phone" style="font-size: 16px;"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-zinc-400 uppercase">Telepon</div>
                            <div class="text-sm font-medium mt-0.5">(+62) 813 5631 291</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
