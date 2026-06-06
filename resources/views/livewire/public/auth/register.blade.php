<div class="min-h-[calc(100vh-180px)] grid grid-cols-1 lg:grid-cols-2">
    <!-- Left Panel: Brand & Info (Hero Image) -->
    <div class="hidden lg:flex flex-col justify-between p-12 text-white relative overflow-hidden">
        <!-- Hero Background Image with Dark Linear Gradient Overlay -->
        <div class="absolute inset-0 z-0 bg-cover bg-center transition-all duration-500 scale-[1.02]" style="background-image: linear-gradient(135deg, rgba(8, 15, 30, 0.88) 0%, rgba(15, 30, 54, 0.78) 50%, rgba(30, 53, 92, 0.45) 100%), url('{{ asset('images/hero.jpg') }}'); background-size: cover; background-position: center center;"></div>
        
        <!-- Abstract Glow Background -->
        <div class="absolute inset-0 opacity-25 pointer-events-none z-0" style="background-image: radial-gradient(circle at 10% 20%, #fe5000 0%, transparent 50%), radial-gradient(circle at 90% 80%, #2563eb 0%, transparent 50%); filter: blur(60px);"></div>
        
        <!-- Top Section -->
        <div class="relative z-10 flex flex-col gap-4">
            <!-- Brand Logo (Removed by request) -->
            
            <div class="inline-flex items-center gap-2 bg-[#fe5000] px-3.5 py-1.5 rounded-full text-xs mb-2 border border-[#fe5000] self-start font-bold" style="box-shadow: 0 2px 8px rgba(254, 80, 0, 0.3);">
                <i class="ti ti-users" style="font-size: 14px;"></i>
                <span>Komunitas Bekasi Hebat</span>
            </div>
            
            <h2 class="text-3xl font-bold leading-tight mb-2 text-white" style="text-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                Bergabung dengan ribuan warga Bekasi yang berkembang bersama
            </h2>
            <p class="text-white/80 leading-relaxed text-sm">
                Dapatkan akses gratis ke berbagai program pelatihan, forum aspirasi, workshop keterampilan, dan program pemberdayaan masyarakat langsung di wilayah Anda.
            </p>
        </div>

        <!-- Bottom Section (Moved to the yellow mark area: bottom left using absolute positioning) -->
        <div class="absolute z-10" style="position: absolute; bottom: 120px; left: 48px; width: calc(100% - 96px);">
            <div class="bg-white/5 backdrop-blur-lg p-5 rounded-2xl border border-white/10 shadow-lg" style="box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);">
                <div class="flex items-center gap-3 mb-2 text-white">
                    <div style="background: #fe5000; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 10px;">
                        <i class="ti ti-id-badge-2" style="font-size: 20px; color: #ffffff;"></i>
                    </div>
                    <div class="font-bold text-sm">Kartu Anggota Digital</div>
                </div>
                <div class="text-xs text-white/70 leading-relaxed">Setelah mendaftar, Anda langsung mendapatkan KTA Digital dengan kode QR unik untuk check-in event dan pencatatan partisipasi program daerah.</div>
            </div>
        </div>
    </div>

    <!-- Right Panel: Form -->
    <div class="flex items-center justify-center p-6 lg:p-12 bg-white relative overflow-hidden register-form-container">
        <!-- Silhouette Watermark Background (Gedung Juang) - Higher opacity for visibility -->
        <div class="absolute bottom-0 right-0 w-full h-[380px] opacity-[0.18] pointer-events-none z-0" style="background-image: url('{{ asset('images/gedung-juang-siluet.png') }}'); background-size: contain; background-position: bottom right; background-repeat: no-repeat;"></div>
        
        <!-- Radial Glow behind form -->
        <div class="absolute bg-glow-accent bg-glow-orange" style="top: -200px; right: -200px; opacity: 0.3; pointer-events: none; z-index: 0;"></div>
        
        <div class="w-full max-w-md relative z-10">
            <h1 class="text-3xl font-extrabold text-zinc-900 mb-2">Gabung Komunitas</h1>
            <p class="text-sm text-zinc-500 mb-8">
                Sudah punya akun?
                <a href="{{ route('login') }}" wire:navigate class="font-bold hover:underline" style="color: #fe5000;">Masuk di sini <i class="ti ti-chevron-right" style="font-size: 11px; vertical-align: middle;"></i></a>
            </p>

            <form wire:submit="register" class="space-y-4">
                <flux:field>
                    <flux:label class="font-semibold text-zinc-700">Nama Lengkap</flux:label>
                    <flux:input wire:model="name" placeholder="Masukkan nama lengkap" autofocus />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label class="font-semibold text-zinc-700">Email</flux:label>
                    <flux:input wire:model="email" type="email" placeholder="email@contoh.com" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label class="font-semibold text-zinc-700">No. WhatsApp</flux:label>
                    <flux:input wire:model="phone" type="tel" placeholder="08xxxxxxxxxx" />
                    <flux:error name="phone" />
                </flux:field>

                <flux:field>
                    <flux:label class="font-semibold text-zinc-700">Password</flux:label>
                    <flux:input wire:model="password" type="password" placeholder="Minimal 8 karakter" />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label class="font-semibold text-zinc-700">Konfirmasi Password</flux:label>
                    <flux:input wire:model="password_confirmation" type="password" placeholder="Ulangi password" />
                    <flux:error name="password_confirmation" />
                </flux:field>

                <flux:checkbox wire:model="agree" label="Saya setuju dengan Ketentuan & Kebijakan Privasi" />
                <div class="text-xs text-zinc-500 -mt-2">
                    <a href="#" class="underline font-medium hover:text-orange-600" style="color: #fe5000;">Ketentuan Layanan</a>
                    ·
                    <a href="#" class="underline font-medium hover:text-orange-600" style="color: #fe5000;">Kebijakan Privasi</a>
                </div>

                <button type="submit" class="w-full rounded-xl text-white font-bold transition duration-200 btn-sliding-arrow" style="display: flex; align-items: center; justify-content: center; height: 50px; background: #fe5000; box-shadow: 0 4px 14px rgba(254, 80, 0, 0.25);" onmouseover="this.style.background='#d94000'; this.style.boxShadow='0 6px 20px rgba(254, 80, 0, 0.4)';" onmouseout="this.style.background='#fe5000'; this.style.boxShadow='0 4px 14px rgba(254, 80, 0, 0.25)';" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="register" class="flex items-center justify-center gap-1">
                        Gabung Sekarang <i class="ti ti-arrow-right" style="font-size: 16px;"></i>
                    </span>
                    <span wire:loading wire:target="register" class="flex items-center justify-center gap-2">
                        Memproses...
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* Enlarge registration form labels, inputs, and descriptions */
    .register-form-container label {
        font-size: 15px !important;
        font-weight: 700 !important;
        color: #27272a !important; /* text-zinc-800 */
        margin-bottom: 6px !important;
    }
    .register-form-container input {
        font-size: 16px !important;
        height: 50px !important;
        padding-top: 12px !important;
        padding-bottom: 12px !important;
        color: #09090b !important; /* text-zinc-950 */
    }
    .register-form-container input::placeholder {
        font-size: 15px !important;
        color: #a1a1aa !important; /* text-zinc-400 */
    }
    .register-form-container h1 {
        font-size: 2.25rem !important; /* text-4xl */
        font-weight: 800 !important;
    }
    .register-form-container p, 
    .register-form-container span, 
    .register-form-container a {
        font-size: 15px !important;
    }
    .register-form-container .text-xs, 
    .register-form-container .text-xs a {
        font-size: 13px !important;
    }
</style>
