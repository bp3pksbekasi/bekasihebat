<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\BedahDapil\PemiluSummaryCompiler;
use Illuminate\Console\Command;
use RuntimeException;

class CompilePemiluSummary extends Command
{
    protected $signature = 'pemilu:compile-summary
        {tahun : Tahun pemilu, contoh 2024 atau 2029}
        {--jenis=dprd : Jenis pemilu}
        {--label= : Label periode}
        {--tps= : Path file TPS CSV}
        {--dpt=* : Path file DPT CSV, bisa dipakai lebih dari satu}
        {--set-default : Jadikan periode ini default di UI}';

    protected $description = 'Compile data pemilu mentah menjadi summary database per periode';

    public function handle(PemiluSummaryCompiler $compiler): int
    {
        try {
            $result = $compiler->compile(
                (int) $this->argument('tahun'),
                (string) $this->option('jenis'),
                $this->option('label') ?: null,
                $this->option('tps') ?: null,
                array_values(array_filter((array) $this->option('dpt'))),
                (bool) $this->option('set-default')
            );
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $period = $result['period'];

        $this->info('Summary pemilu berhasil dikompilasi.');
        $this->newLine();
        $this->table(
            ['Field', 'Value'],
            [
                ['Periode', $period->label],
                ['Tahun', (string) $period->tahun],
                ['Jenis', $period->jenis],
                ['Default UI', $period->is_default ? 'Ya' : 'Tidak'],
                ['Total desa', number_format((int) $result['total_desa'])],
                ['TPS source', $result['tps_file']],
                ['Jumlah file DPT', number_format(count($result['dpt_files']))],
            ]
        );

        return self::SUCCESS;
    }
}
