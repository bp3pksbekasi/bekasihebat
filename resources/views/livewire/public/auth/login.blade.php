<div class="min-h-[calc(100vh-180px)] grid grid-cols-1 lg:grid-cols-2">
    <div class="hidden lg:flex flex-col justify-between p-12 text-white" style="background: linear-gradient(135deg, #fe5000 0%, #d94400 100%);">
        <div>
            <div class="inline-flex items-center gap-2 bg-white/20 px-3 py-1.5 rounded-full text-xs mb-6">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1z"/></svg>
                Bekasi Hebat!
            </div>
            <h2 class="text-3xl font-medium leading-tight mb-4">Selamat datang kembali</h2>
            <p class="text-white/90 leading-relaxed">
                Masuk untuk melihat dashboard komunitas dan informasi kegiatan di wilayah Anda.
            </p>
        </div>

        <div class="bg-white/15 backdrop-blur p-4 rounded-xl">
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/></svg>
                <div class="font-medium">Akses Kegiatan</div>
            </div>
            <div class="text-sm text-white/85">Ikuti kegiatan komunitas, pelatihan, dan program pengembangan potensi.</div>
        </div>
    </div>

    <div class="flex items-center justify-center p-6 lg:p-12">
        <div class="w-full max-w-[400px]">
            <h1 class="text-2xl font-semibold mb-1">Masuk</h1>
            <p class="text-sm text-zinc-500 mb-8">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-medium hover:underline" style="color: #fe5000;">Gabung di sini</a>
            </p>

            <form wire:submit="login" class="space-y-4">
                <flux:field>
                    <flux:label>Email / No. WhatsApp / NIA</flux:label>
                    <flux:input wire:model="identifier" name="identifier" autocomplete="username" placeholder="email@contoh.com, 08xxxxxxxxxx, atau 123.456.789" autofocus />
                    <flux:error name="identifier" />
                </flux:field>

                <flux:field>
                    <flux:label>Password</flux:label>
                    <flux:input wire:model="password" name="password" autocomplete="current-password" type="password" />
                    <flux:error name="password" />
                </flux:field>

                <div class="flex items-center justify-between">
                    <flux:checkbox wire:model="remember" label="Ingat saya" />
                    <a href="#" class="text-sm text-zinc-600 hover:text-zinc-900 hover:underline">
                        Lupa password?
                    </a>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-lg text-white font-medium hover:opacity-90 transition" style="background: #fe5000;" wire:loading.attr="disabled" wire:target="login">
                    <span wire:loading.remove wire:target="login">Masuk</span>
                    <span wire:loading wire:target="login">Memproses...</span>
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('aktivasi') }}" class="text-sm text-orange-600 hover:underline">
                    Belum punya akun? Aktivasi dengan NIA →
                </a>
            </div>
        </div>
    </div>
</div>
