<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TruncateTransactionData extends Command
{
    protected $signature = 'app:truncate-transaction-data';

    protected $description = 'Bersihkan data transaksi (ProfilRw, Korwe, Korte, PenggalangSuara, KegiatanRw) sebelum launching';

    public function handle(): int
    {
        $tables = [
            'profil_rws'        => 'Profil RW',
            'korwes'            => 'Korwe',
            'kortes'            => 'Korte',
            'penggalang_suaras' => 'Penggalang Suara',
            'kegiatan_rws'      => 'Kegiatan RW (Sisir RW)',
        ];

        $this->warn('⚠️  PERHATIAN: Tindakan ini akan MENGHAPUS SEMUA data dari tabel berikut secara permanen:');
        $this->newLine();

        foreach ($tables as $table => $label) {
            $count = DB::table($table)->count();
            $this->line("   • <fg=yellow>{$label}</> ({$table}) — <fg=cyan>{$count} baris</>");
        }

        $this->newLine();
        $this->error('   DATA YANG DIHAPUS TIDAK BISA DIKEMBALIKAN!');
        $this->newLine();

        if (! $this->confirm('Apakah Anda yakin ingin menghapus semua data di atas?', false)) {
            $this->info('❌ Dibatalkan. Tidak ada data yang dihapus.');
            return self::SUCCESS;
        }

        $this->newLine();
        $this->info('🔄 Mulai membersihkan data...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table => $label) {
            DB::table($table)->truncate();
            $this->line("   ✅ <fg=green>{$label}</> ({$table}) — berhasil dibersihkan");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->newLine();
        $this->info('✅ Selesai! Semua data transaksi berhasil dibersihkan.');

        return self::SUCCESS;
    }
}
