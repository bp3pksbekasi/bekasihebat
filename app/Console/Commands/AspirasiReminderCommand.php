<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Aspirasi;
use App\Models\AspirasiReminder;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class AspirasiReminderCommand extends Command
{
    protected $signature = 'aspirasi:reminder';

    protected $description = 'Kirim reminder otomatis untuk aspirasi yang stuck pada pipeline POKIR';

    public function handle(): int
    {
        $this->sendAssignedReminders();
        $this->sendAdminMonitoringReminder(
            status: 'input_sipd',
            olderThanDays: 30,
            messagePrefix: 'Cek SIPD'
        );
        $this->sendAdminMonitoringReminder(
            status: 'dianggarkan',
            olderThanDays: 90,
            messagePrefix: 'Cek realisasi'
        );

        $this->info('Reminder aspirasi selesai diproses.');

        return self::SUCCESS;
    }

    private function sendAssignedReminders(): void
    {
        $aspirasis = Aspirasi::query()
            ->with('assignedDewan')
            ->where('status', 'assigned')
            ->where('assigned_at', '<=', now()->subDays(7))
            ->get()
            ->filter(function (Aspirasi $aspirasi): bool {
                return ! AspirasiReminder::query()
                    ->where('aspirasi_id', $aspirasi->id)
                    ->where('created_at', '>=', now()->subDays(7))
                    ->exists();
            })
            ->groupBy(fn (Aspirasi $aspirasi) => $aspirasi->resolveReminderTargetUserId() ?: 'none');

        foreach ($aspirasis as $targetUserId => $rows) {
            if ($targetUserId === 'none' || ! $rows instanceof Collection || $rows->isEmpty()) {
                continue;
            }

            $oldest = $rows->sortBy('assigned_at')->first();

            if (! $oldest instanceof Aspirasi) {
                continue;
            }

            $days = $oldest->assigned_at?->diffInDays(now()) ?? 0;
            $dewanNama = $oldest->assignedDewan?->nama ?? 'Tim penanggung jawab';

            AspirasiReminder::query()->create([
                'aspirasi_id' => $oldest->id,
                'target_user_id' => (int) $targetUserId,
                'channel' => 'system',
                'pesan' => "Bapak/Ibu {$dewanNama}, ada {$rows->count()} aspirasi yang belum diinput ke SIPD. Tertua: {$oldest->judul} ({$days} hari lalu). Mohon segera diinput.",
            ]);
        }
    }

    private function sendAdminMonitoringReminder(string $status, int $olderThanDays, string $messagePrefix): void
    {
        $aspirasis = Aspirasi::query()
            ->where('status', $status)
            ->where('updated_at', '<=', now()->subDays($olderThanDays))
            ->get();

        if ($aspirasis->isEmpty()) {
            return;
        }

        $adminId = User::query()
            ->where('status', 'aktif')
            ->whereIn('role', ['admin_dpd', 'admin', 'dpd'])
            ->orderBy('id')
            ->value('id');

        if (! $adminId) {
            return;
        }

        $oldest = $aspirasis->sortBy('updated_at')->first();

        if (! $oldest instanceof Aspirasi) {
            return;
        }

        $alreadySent = AspirasiReminder::query()
            ->where('aspirasi_id', $oldest->id)
            ->where('target_user_id', $adminId)
            ->where('created_at', '>=', now()->subDays(7))
            ->exists();

        if ($alreadySent) {
            return;
        }

        $message = $messagePrefix === 'Cek SIPD'
            ? "Cek SIPD: {$aspirasis->count()} aspirasi belum diverifikasi BAPPEDA."
            : "Cek realisasi: {$aspirasis->count()} aspirasi sudah dianggarkan tapi belum terealisasi.";

        AspirasiReminder::query()->create([
            'aspirasi_id' => $oldest->id,
            'target_user_id' => (int) $adminId,
            'channel' => 'system',
            'pesan' => $message,
        ]);
    }
}
