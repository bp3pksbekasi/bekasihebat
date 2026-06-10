<div class="profile-complete-container">
    <style>
        .profile-complete-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        .profile-complete-grid {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        @media (min-width: 768px) {
            .profile-complete-grid {
                display: grid;
                grid-template-columns: 4.5fr 7.5fr;
                gap: 40px;
                align-items: start;
            }
            .profile-complete-sidebar {
                position: sticky;
                top: 30px;
            }
            .profile-complete-title-area {
                text-align: left !important;
            }
        }
        @media (max-width: 767px) {
            .profile-complete-title-area {
                text-align: center !important;
            }
        }
        .profile-complete-card-img {
            width: 100%;
            max-width: 340px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: 1px solid #ececea;
            transition: transform 0.3s, box-shadow 0.3s;
            margin: 0 auto 20px;
            display: block;
        }
        .profile-complete-card-img:hover {
            transform: scale(1.01);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
    </style>

    <div class="profile-complete-grid">
        
        <!-- Left Column: Card Preview & Instructions -->
        <div class="profile-complete-sidebar">
            <!-- User Info Panel -->
            <div style="background: white; border: 1px solid #e4e4e7; border-radius: 16px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-bottom: 20px; display: flex; align-items: center; gap: 14px;">
                <div style="background: linear-gradient(135deg, #fe5000 0%, #ff7830 100%); color: white; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(254, 80, 0, 0.2);">
                    {{ strtoupper(auth()->user()->initials()) }}
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="font-size: 14px; font-weight: 700; color: #18181b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px;">
                        {{ auth()->user()->name }}
                    </div>
                    <div style="font-size: 12px; color: #71717a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: flex; align-items: center; gap: 6px;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width: 14px; height: 14px; color: #a1a1aa; flex-shrink: 0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        {{ auth()->user()->email }}
                    </div>
                </div>
            </div>

            <!-- Image Card -->
            <div style="display: flex; justify-content: center;">
                <img src="{{ asset('images/kartu.png') }}" alt="Desain Kartu Anggota" class="profile-complete-card-img">
            </div>

            <!-- Title Area -->
            <div class="profile-complete-title-area" style="margin-bottom: 20px;">
                <h1 class="text-2xl font-bold text-zinc-900" style="margin: 0 0 6px; font-size: 24px; font-weight: 700; color: #18181b;">Lengkapi Profil Anda</h1>
                <p class="text-sm text-zinc-500" style="margin: 0; font-size: 14px; color: #71717a;">Satu langkah lagi untuk mengaktifkan keanggotaan Anda.</p>
            </div>

            <!-- Stand-out Info Section -->
            <div style="background: linear-gradient(135deg, #fff9f5 0%, #fff2e8 100%); border: 1.5px solid #ffe0cc; border-radius: 16px; padding: 18px; display: flex; gap: 14px; position: relative; overflow: hidden;">
                <div style="background: rgba(254, 80, 0, 0.1); color: #fe5000; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <h4 style="color: #fe5000; margin: 0 0 4px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Aktivasi Keanggotaan & Akses Event</h4>
                    <p style="margin: 0; font-size: 12px; color: #3f3f46; line-height: 1.6;">
                        Silakan lengkapi data diri Anda untuk <strong>mengklaim Kartu Anggota Digital</strong> secara resmi dan mendapatkan <strong>akses gratis</strong> ke seluruh program kegiatan, pelatihan, serta event pemberdayaan masyarakat.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Column: Form Inputs -->
        <form wire:submit="save" style="display: grid; gap: 20px; margin: 0;">
            <!-- Identitas Card -->
            <div style="background: white; border: 1px solid #e4e4e7; border-radius: 16px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; padding-bottom: 12px; border-b: 1px solid #f4f4f5; border-bottom: 1px solid #f4f4f5;">
                    <div style="background: #fff7f1; width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" style="color: #fe5000; width: 16px; height: 16px;">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size: 14px; font-weight: 600; color: #18181b;">Identitas</div>
                        <div style="font-size: 11px; color: #71717a;">Data dasar untuk profil anggota</div>
                    </div>
                </div>

                <div style="display: grid; gap: 16px;">
                    {{--
                    <!-- NIK -->
                    <flux:field>
                        <flux:label>No. KTP (NIK)</flux:label>
                        <flux:input wire:model="nik" type="text" maxlength="16" placeholder="16 digit NIK" />
                        <flux:error name="nik" />
                    </flux:field>
                    --}}

                    <!-- Tempat/Tanggal Lahir -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;">
                        {{--
                        <flux:field>
                            <flux:label>Tempat Lahir</flux:label>
                            <flux:input wire:model="ttl_tempat" type="text" placeholder="Bekasi" />
                            <flux:error name="ttl_tempat" />
                        </flux:field>
                        --}}

                        <flux:field>
                            <flux:label>Tanggal Lahir</flux:label>
                            <flux:input wire:model="ttl_tanggal" type="date" />
                            <flux:error name="ttl_tanggal" />
                        </flux:field>
                    </div>

                    <!-- Jenis Kelamin & Foto Profil -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; align-items: end;">
                        <flux:field>
                            <flux:label>Jenis Kelamin</flux:label>
                            <select wire:model="jenis_kelamin" class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200" style="height: 38px;">
                                <option value="">Pilih...</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <flux:error name="jenis_kelamin" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Foto Profil</flux:label>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <input type="file" wire:model="foto" accept="image/*" class="w-full text-xs text-zinc-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100" style="height: 38px; padding: 6px 0;" />
                                <div wire:loading wire:target="foto" class="text-xs text-zinc-400">Loading...</div>
                            </div>
                            <flux:error name="foto" />
                        </flux:field>
                    </div>
                </div>
            </div>

            <!-- Alamat & Wilayah Card -->
            <div style="background: white; border: 1px solid #e4e4e7; border-radius: 16px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f4f4f5;">
                    <div style="background: #fff7f1; width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" style="color: #fe5000; width: 16px; height: 16px;">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size: 14px; font-weight: 600; color: #18181b;">Alamat & Wilayah</div>
                        <div style="font-size: 11px; color: #71717a;">Kab. Bekasi, Jawa Barat</div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
                    <flux:field>
                        <flux:label>Kecamatan</flux:label>
                        <select wire:model.live="kecamatan_code" class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200" style="height: 38px;">
                            <option value="">Pilih kecamatan...</option>
                            @foreach ($this->kecamatanList as $kec)
                                <option value="{{ $kec->code }}">{{ Str::title(strtolower($kec->name)) }}</option>
                            @endforeach
                        </select>
                        <flux:error name="kecamatan_code" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Kelurahan</flux:label>
                        <select wire:model="kelurahan_code" @disabled(! $kecamatan_code) class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 disabled:bg-zinc-100 disabled:text-zinc-400 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200" style="height: 38px;">
                            <option value="">{{ $kecamatan_code ? 'Pilih kelurahan...' : 'Pilih kecamatan dulu' }}</option>
                            @foreach ($this->kelurahanList as $kel)
                                <option value="{{ $kel->code }}">{{ Str::title(strtolower($kel->name)) }}</option>
                            @endforeach
                        </select>
                        <flux:error name="kelurahan_code" />
                    </flux:field>
                </div>

                @if ($kelurahan_code)
                    <div style="margin-bottom: 16px; border-radius: 12px; border: 1px solid #ffe0cc; background: #fffaf7; padding: 12px 16px;">
                        <div style="font-size: 11px; font-weight: 600; color: #fe5000; text-transform: uppercase; letter-spacing: 0.5px;">Wilayah terpilih</div>
                        <div style="font-size: 13px; color: #4b5563; margin-top: 2px;">
                            {{ collect($this->kelurahanList)->firstWhere('code', $kelurahan_code)?->name ? Str::title(strtolower(collect($this->kelurahanList)->firstWhere('code', $kelurahan_code)?->name)) : '-' }},
                            {{ collect($this->kecamatanList)->firstWhere('code', $kecamatan_code)?->name ? Str::title(strtolower(collect($this->kecamatanList)->firstWhere('code', $kecamatan_code)?->name)) : '-' }},
                            Kabupaten Bekasi
                        </div>
                    </div>
                @endif

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
                    <flux:field>
                        <flux:label>RW</flux:label>
                        <select wire:model="rw" class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200" style="height: 38px;">
                            <option value="">Pilih RW...</option>
                            @foreach ($this->rwOptions() as $number)
                                <option value="{{ $number }}">{{ str_pad((string) $number, 3, '0', STR_PAD_LEFT) }}</option>
                            @endforeach
                        </select>
                        <flux:error name="rw" />
                    </flux:field>

                    <flux:field>
                        <flux:label>RT</flux:label>
                        <select wire:model="rt" class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200" style="height: 38px;">
                            <option value="">Pilih RT...</option>
                            @foreach ($this->rtOptions() as $number)
                                <option value="{{ $number }}">{{ str_pad((string) $number, 3, '0', STR_PAD_LEFT) }}</option>
                            @endforeach
                        </select>
                        <flux:error name="rt" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Alamat Lengkap</flux:label>
                    <flux:textarea wire:model="address_detail" rows="3" placeholder="Jl. Contoh No. 123, Blok A2, Perumahan..." />
                    <flux:error name="address_detail" />
                </flux:field>
            </div>

            <!-- Submit Button & Navigation Link -->
            <div style="margin-top: 8px;">
                <button
                    type="submit"
                    class="w-full py-3.5 rounded-xl text-white font-medium text-sm transition hover:opacity-95"
                    style="background: #fe5000; border: none; border-radius: 12px; height: 48px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                    wire:loading.attr="disabled"
                    wire:target="save"
                >
                    <span wire:loading.remove wire:target="save">Buat Kartu & Ikuti Event Gratis</span>
                    <span wire:loading wire:target="save">Menyimpan Perubahan...</span>
                </button>

                <div style="text-align: center; margin-top: 14px;">
                    <a href="{{ route('member.dashboard') }}" style="font-size: 13px; color: #71717a; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#18181b'" onmouseout="this.style.color='#71717a'">
                        Kembali ke dashboard
                    </a>
                </div>
            </div>
        </form>

    </div>
</div>
