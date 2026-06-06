<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\DataRw;
use App\Models\TargetWilayah;
use Illuminate\Console\Command;

class ComputeStatusWilayah extends Command
{
    protected $signature = 'compute:status-wilayah';

    protected $description = 'Recompute status wilayah dan prioritas untuk data RW berdasarkan estimasi PKS';

    public function handle(): int
    {
        $rows = DataRw::query()->get([
            'id',
            'estimasi_pks',
            'estimasi_ranking',
            'estimasi_share',
        ]);

        if ($rows->isEmpty()) {
            $this->warn('Tidak ada data RW. Jalankan import:data-rw terlebih dahulu.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($rows->count());
        $bar->start();

        foreach ($rows as $row) {
            $bar->advance();

            $result = TargetWilayah::classifyStatus(
                (int) $row->estimasi_pks,
                (int) $row->estimasi_ranking,
                (float) $row->estimasi_share,
            );

            $row->forceFill([
                'status_wilayah' => $result['status'],
                'prioritas_urutan' => $result['prioritas'],
            ])->save();
        }

        $bar->finish();
        $this->newLine(2);

        $summary = DataRw::query()
            ->selectRaw('status_wilayah, count(*) as total')
            ->groupBy('status_wilayah')
            ->orderBy('status_wilayah')
            ->get();

        $this->table(
            ['Status', 'Jumlah RW'],
            $summary->map(fn (DataRw $item): array => [$item->status_wilayah, (string) $item->total])->all()
        );

        return self::SUCCESS;
    }
}
