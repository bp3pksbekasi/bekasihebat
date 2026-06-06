<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AnggotaDewan;
use App\Models\Aspirasi;
use App\Models\AspirasiLog;
use App\Models\AspirasiReminder;
use App\Models\TargetWilayah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class AspirasiPokirSeeder extends Seeder
{
    private const SOURCE_PREFIX = 'ASP-POKIR-2026-';

    public function run(): void
    {
        $creatorId = User::query()->where('email', 'admin1@bekasihebat.id')->value('id')
            ?? User::query()->value('id');

        $wilayahs = TargetWilayah::query()
            ->orderBy('dapil')
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get();

        if ($wilayahs->isEmpty()) {
            $this->command?->warn('AspirasiPokirSeeder dilewati karena data target wilayah belum tersedia.');

            return;
        }

        $dewanByDapil = AnggotaDewan::query()
            ->aktif()
            ->orderBy('dapil')
            ->orderBy('nama')
            ->get()
            ->groupBy(fn (AnggotaDewan $dewan): string => (string) $dewan->dapil);

        $statuses = $this->statusDistribution();
        $templates = $this->aspirasiTemplates();
        $pelapors = $this->pelaporList();
        $sources = array_keys(Aspirasi::SUMBER_OPTIONS);

        foreach (range(1, 50) as $index) {
            $loopIndex = $index - 1;
            $wilayah = $wilayahs[$loopIndex % $wilayahs->count()];
            $template = $templates[$loopIndex % count($templates)];
            $pelapor = $pelapors[$loopIndex % count($pelapors)];
            $status = $statuses[$loopIndex];
            $rw = $this->resolveRwNumber($wilayah, $loopIndex);
            $dewan = $this->resolveDewanForDapil($dewanByDapil, (string) $wilayah->dapil, $loopIndex);
            $createdAt = now()->subDays(160 - ($loopIndex * 2))->setTime(9 + ($loopIndex % 6), 15);
            $assignedAt = $this->resolveAssignedAt($status, $createdAt);
            $inputSipdAt = $this->resolveInputSipdAt($status, $assignedAt);
            $verifiedAt = $this->resolveVerifiedAt($status, $inputSipdAt);
            $dianggarkanAt = $this->resolveDianggarkanAt($status, $verifiedAt);
            $realisasiAt = $this->resolveRealisasiAt($status, $dianggarkanAt);
            $sumber = $sources[$loopIndex % count($sources)];
            $sourceId = self::SOURCE_PREFIX.sprintf('%03d', $index);
            $judul = $template['judul'].' di RW '.$rw.' '.$wilayah->desa;
            $deskripsi = sprintf(
                $template['deskripsi'],
                $wilayah->desa,
                $wilayah->kecamatan,
                $rw
            );
            $alamat = sprintf(
                'Lingkungan RT %02d/RW %s, Desa %s, Kecamatan %s',
                ($loopIndex % 9) + 1,
                $rw,
                $wilayah->desa,
                $wilayah->kecamatan
            );

            $aspirasi = Aspirasi::query()->firstOrNew(['sumber_id' => $sourceId]);
            $aspirasi->fill([
                'judul' => $judul,
                'deskripsi' => $deskripsi,
                'kategori' => $template['kategori'],
                'urgensi' => $template['urgensi'],
                'dapil' => $wilayah->dapil,
                'kecamatan' => $wilayah->kecamatan,
                'desa' => $wilayah->desa,
                'nomor_rw' => $rw,
                'alamat_detail' => $alamat,
                'target_wilayah_id' => $wilayah->id,
                'nama_pelapor' => $pelapor['nama'],
                'hp_pelapor' => $this->phoneNumber($index),
                'sumber' => $sumber,
                'sumber_id' => $sourceId,
                'assigned_dewan_id' => $assignedAt ? $dewan?->id : null,
                'assigned_at' => $assignedAt,
                'nomor_pokir' => $inputSipdAt ? sprintf('POKIR/%s/%03d', now()->format('Y'), $index) : null,
                'input_sipd_at' => $inputSipdAt,
                'screenshot_sipd' => $inputSipdAt ? 'aspirasi/sipd/berkas-pokir-'.$index.'.jpg' : null,
                'status' => $status,
                'verified_at' => $verifiedAt,
                'dianggarkan_at' => $dianggarkanAt,
                'anggaran_nominal' => $dianggarkanAt ? $this->anggaranNominal($template['kategori'], $loopIndex) : null,
                'tahun_anggaran' => $dianggarkanAt ? (string) now()->year : null,
                'realisasi_at' => $realisasiAt,
                'foto_realisasi' => $realisasiAt ? 'aspirasi/realisasi/foto-'.$index.'.jpg' : null,
                'feedback_warga' => $realisasiAt ? 'Warga menyampaikan bahwa tindak lanjut sudah dirasakan dan lokasi jauh lebih tertata.' : null,
                'notif_warga_sent' => $realisasiAt !== null,
                'catatan_internal' => $this->internalNote($status, $pelapor['nama']),
                'created_by' => $creatorId,
            ]);
            $aspirasi->save();
            $aspirasi->forceFill([
                'draft_pokir' => $aspirasi->generateDraftPokir(),
                'created_at' => $createdAt,
                'updated_at' => $realisasiAt ?? $dianggarkanAt ?? $verifiedAt ?? $inputSipdAt ?? $assignedAt ?? $createdAt,
            ])->saveQuietly();

            $this->syncLogs($aspirasi, $status, $createdAt, $assignedAt, $inputSipdAt, $verifiedAt, $dianggarkanAt, $realisasiAt, $dewan?->nama, $creatorId);
            $this->syncReminders($aspirasi, $status, $creatorId, $assignedAt);
        }

        $seededCount = Aspirasi::query()
            ->where('sumber_id', 'like', self::SOURCE_PREFIX.'%')
            ->count();

        $this->command?->info("AspirasiPokirSeeder selesai: {$seededCount} data aspirasi siap digunakan.");
    }

    /**
     * @return array<int, string>
     */
    private function statusDistribution(): array
    {
        return [
            ...array_fill(0, 8, 'diterima'),
            ...array_fill(0, 10, 'assigned'),
            ...array_fill(0, 10, 'input_sipd'),
            ...array_fill(0, 8, 'verifikasi_bappeda'),
            ...array_fill(0, 8, 'dianggarkan'),
            ...array_fill(0, 6, 'terealisasi'),
        ];
    }

    /**
     * @return array<int, array{judul:string,deskripsi:string,kategori:string,urgensi:string}>
     */
    private function aspirasiTemplates(): array
    {
        return [
            [
                'judul' => 'Normalisasi saluran drainase lingkungan',
                'deskripsi' => 'Warga Desa %s Kecamatan %s mengusulkan normalisasi saluran drainase di sekitar RW %s karena aliran air tersumbat saat hujan deras dan mengganggu akses rumah warga.',
                'kategori' => 'infrastruktur',
                'urgensi' => 'tinggi',
            ],
            [
                'judul' => 'Perbaikan jalan lingkungan menuju permukiman',
                'deskripsi' => 'Jalan lingkungan di Desa %s Kecamatan %s pada RW %s mengalami retak dan berlubang sehingga mobilitas warga, anak sekolah, dan kendaraan layanan kesehatan menjadi kurang aman.',
                'kategori' => 'infrastruktur',
                'urgensi' => 'mendesak',
            ],
            [
                'judul' => 'Pembangunan PJU pada gang penghubung warga',
                'deskripsi' => 'Masyarakat Desa %s Kecamatan %s meminta tambahan penerangan jalan umum di area RW %s karena kondisi malam hari masih gelap dan rawan untuk aktivitas warga sepulang kerja.',
                'kategori' => 'lingkungan',
                'urgensi' => 'sedang',
            ],
            [
                'judul' => 'Pengadaan alat kesehatan untuk posyandu',
                'deskripsi' => 'Kader posyandu di Desa %s Kecamatan %s membutuhkan alat timbang bayi, alat ukur tekanan darah, dan sarana pendukung pelayanan dasar di RW %s agar layanan berjalan lebih baik.',
                'kategori' => 'kesehatan',
                'urgensi' => 'tinggi',
            ],
            [
                'judul' => 'Renovasi ruang belajar dan sanitasi PAUD',
                'deskripsi' => 'Orang tua dan pengelola PAUD di Desa %s Kecamatan %s mengusulkan renovasi ruang belajar serta pembenahan sanitasi pada RW %s demi kenyamanan dan keamanan anak-anak.',
                'kategori' => 'pendidikan',
                'urgensi' => 'sedang',
            ],
            [
                'judul' => 'Pelatihan usaha rumahan bagi ibu keluarga',
                'deskripsi' => 'Kelompok warga di Desa %s Kecamatan %s mendorong pelatihan usaha rumahan untuk warga RW %s agar pendapatan keluarga meningkat melalui produksi makanan dan kerajinan lokal.',
                'kategori' => 'ekonomi',
                'urgensi' => 'sedang',
            ],
            [
                'judul' => 'Perbaikan talud dan penahan longsor bantaran',
                'deskripsi' => 'Permukiman di Desa %s Kecamatan %s pada RW %s membutuhkan penguatan talud bantaran karena tanah mulai terkikis dan mengancam keamanan rumah terdekat.',
                'kategori' => 'infrastruktur',
                'urgensi' => 'mendesak',
            ],
            [
                'judul' => 'Penyediaan armada angkut sampah lingkungan',
                'deskripsi' => 'Warga Desa %s Kecamatan %s mengajukan dukungan armada angkut sampah untuk wilayah RW %s agar pengelolaan sampah rumah tangga lebih tertib dan tidak menumpuk di titik pembuangan sementara.',
                'kategori' => 'lingkungan',
                'urgensi' => 'tinggi',
            ],
            [
                'judul' => 'Penambahan meubelair untuk madrasah dan TPQ',
                'deskripsi' => 'Pengurus madrasah dan TPQ di Desa %s Kecamatan %s meminta tambahan meja, kursi, dan lemari penyimpanan bagi santri di RW %s untuk mendukung kegiatan belajar yang lebih tertib.',
                'kategori' => 'pendidikan',
                'urgensi' => 'sedang',
            ],
            [
                'judul' => 'Pembangunan sumur resapan area padat penduduk',
                'deskripsi' => 'Lingkungan padat di Desa %s Kecamatan %s membutuhkan sumur resapan pada kawasan RW %s untuk mengurangi genangan saat puncak musim hujan dan menjaga kualitas air tanah.',
                'kategori' => 'lingkungan',
                'urgensi' => 'tinggi',
            ],
            [
                'judul' => 'Revitalisasi lapangan warga dan sarana olahraga',
                'deskripsi' => 'Karang taruna Desa %s Kecamatan %s mengusulkan perbaikan lapangan serbaguna di RW %s agar kegiatan olahraga, pembinaan remaja, dan acara kemasyarakatan dapat berjalan lebih aktif.',
                'kategori' => 'sosial',
                'urgensi' => 'sedang',
            ],
            [
                'judul' => 'Bantuan gerobak usaha untuk pelaku UMKM',
                'deskripsi' => 'Pelaku usaha kecil di Desa %s Kecamatan %s memerlukan dukungan gerobak usaha dan perlengkapan dasar pada RW %s untuk memperluas penjualan makanan siap saji dan minuman rumahan.',
                'kategori' => 'ekonomi',
                'urgensi' => 'sedang',
            ],
            [
                'judul' => 'Penyediaan kursi roda dan alat bantu lansia',
                'deskripsi' => 'Keluarga lansia di Desa %s Kecamatan %s mengusulkan ketersediaan kursi roda dan alat bantu mobilitas bagi warga RW %s agar pelayanan sosial dan kesehatan lebih terjangkau.',
                'kategori' => 'kesehatan',
                'urgensi' => 'tinggi',
            ],
        ];
    }

    /**
     * @return array<int, array{nama:string}>
     */
    private function pelaporList(): array
    {
        return [
            ['nama' => 'Ahmad Fauzi'],
            ['nama' => 'Siti Nurhayati'],
            ['nama' => 'Rohmat Hidayat'],
            ['nama' => 'Dewi Kartika'],
            ['nama' => 'M. Ilham Maulana'],
            ['nama' => 'Aisyah Fitriani'],
            ['nama' => 'Nana Supriatna'],
            ['nama' => 'Rina Marlina'],
            ['nama' => 'Agus Salim'],
            ['nama' => 'Nur Aeni'],
        ];
    }

    private function resolveRwNumber(TargetWilayah $wilayah, int $loopIndex): string
    {
        $jumlahRw = max((int) ($wilayah->jumlah_rw ?? 0), 1);

        return str_pad((string) (($loopIndex % $jumlahRw) + 1), 3, '0', STR_PAD_LEFT);
    }

    /**
     * @param  Collection<string, Collection<int, AnggotaDewan>>  $dewanByDapil
     */
    private function resolveDewanForDapil(Collection $dewanByDapil, string $dapil, int $loopIndex): ?AnggotaDewan
    {
        $list = $dewanByDapil->get($dapil, collect());

        if ($list->isEmpty()) {
            $list = $dewanByDapil->flatten(1);
        }

        if ($list->isEmpty()) {
            return null;
        }

        return $list->values()->get($loopIndex % $list->count());
    }

    private function resolveAssignedAt(string $status, Carbon $createdAt): ?Carbon
    {
        if ($status === 'diterima') {
            return null;
        }

        return (clone $createdAt)->addDays(3)->addHours(4);
    }

    private function resolveInputSipdAt(string $status, ?Carbon $assignedAt): ?Carbon
    {
        if (! in_array($status, ['input_sipd', 'verifikasi_bappeda', 'dianggarkan', 'terealisasi'], true) || ! $assignedAt) {
            return null;
        }

        return (clone $assignedAt)->addDays(6);
    }

    private function resolveVerifiedAt(string $status, ?Carbon $inputSipdAt): ?Carbon
    {
        if (! in_array($status, ['verifikasi_bappeda', 'dianggarkan', 'terealisasi'], true) || ! $inputSipdAt) {
            return null;
        }

        return (clone $inputSipdAt)->addDays(5);
    }

    private function resolveDianggarkanAt(string $status, ?Carbon $verifiedAt): ?Carbon
    {
        if (! in_array($status, ['dianggarkan', 'terealisasi'], true) || ! $verifiedAt) {
            return null;
        }

        return (clone $verifiedAt)->addDays(9);
    }

    private function resolveRealisasiAt(string $status, ?Carbon $dianggarkanAt): ?Carbon
    {
        if ($status !== 'terealisasi' || ! $dianggarkanAt) {
            return null;
        }

        return (clone $dianggarkanAt)->addDays(21);
    }

    private function anggaranNominal(string $kategori, int $loopIndex): int
    {
        $base = match ($kategori) {
            'infrastruktur' => 180000000,
            'kesehatan' => 85000000,
            'pendidikan' => 95000000,
            'ekonomi' => 65000000,
            'lingkungan' => 120000000,
            default => 70000000,
        };

        return $base + (($loopIndex % 5) * 12500000);
    }

    private function internalNote(string $status, string $pelapor): string
    {
        return match ($status) {
            'diterima' => 'Aspirasi baru diterima dari warga dan menunggu proses penelaahan awal.',
            'assigned' => 'Sudah diverifikasi awal dan menunggu tindak lanjut penugasan lapangan.',
            'input_sipd' => 'Data pendukung sedang dilengkapi untuk penyelarasan dengan dokumen SIPD.',
            'verifikasi_bappeda' => 'Berkas usulan sedang dalam tahap verifikasi antar perangkat daerah.',
            'dianggarkan' => 'Usulan telah masuk prioritas pembiayaan dan menunggu pelaksanaan kegiatan.',
            'terealisasi' => 'Pelaksanaan kegiatan sudah berlangsung dan warga pelapor atas nama '.$pelapor.' menyampaikan apresiasi.',
            default => 'Aspirasi sedang diproses.',
        };
    }

    private function phoneNumber(int $index): string
    {
        return '0812'.str_pad((string) (7315000 + ($index * 37)), 7, '0', STR_PAD_LEFT);
    }

    private function syncLogs(
        Aspirasi $aspirasi,
        string $status,
        Carbon $createdAt,
        ?Carbon $assignedAt,
        ?Carbon $inputSipdAt,
        ?Carbon $verifiedAt,
        ?Carbon $dianggarkanAt,
        ?Carbon $realisasiAt,
        ?string $dewanName,
        ?int $creatorId,
    ): void {
        AspirasiLog::query()->where('aspirasi_id', $aspirasi->id)->delete();

        $entries = [
            [
                'dari_status' => null,
                'ke_status' => 'diterima',
                'aksi' => 'created',
                'catatan' => 'Aspirasi dicatat melalui kanal '.$aspirasi->sumber.'.',
                'time' => $createdAt,
            ],
        ];

        if ($assignedAt) {
            $entries[] = [
                'dari_status' => 'diterima',
                'ke_status' => 'assigned',
                'aksi' => 'assigned',
                'catatan' => 'Aspirasi ditugaskan kepada '.($dewanName ?: 'tim dewan wilayah terkait').'.',
                'time' => $assignedAt,
            ];
        }

        if ($inputSipdAt) {
            $entries[] = [
                'dari_status' => 'assigned',
                'ke_status' => 'input_sipd',
                'aksi' => 'input_sipd',
                'catatan' => 'Nomor POKIR '.$aspirasi->nomor_pokir.' sudah dicatat untuk proses SIPD.',
                'time' => $inputSipdAt,
            ];
        }

        if ($verifiedAt) {
            $entries[] = [
                'dari_status' => 'input_sipd',
                'ke_status' => 'verifikasi_bappeda',
                'aksi' => 'verifikasi_bappeda',
                'catatan' => 'Usulan dinyatakan lengkap dan masuk tahap verifikasi perangkat daerah.',
                'time' => $verifiedAt,
            ];
        }

        if ($dianggarkanAt) {
            $entries[] = [
                'dari_status' => 'verifikasi_bappeda',
                'ke_status' => 'dianggarkan',
                'aksi' => 'dianggarkan',
                'catatan' => 'Usulan tercatat pada rencana anggaran tahun berjalan senilai Rp '.number_format((float) $aspirasi->anggaran_nominal, 0, ',', '.').'.',
                'time' => $dianggarkanAt,
            ];
        }

        if ($realisasiAt) {
            $entries[] = [
                'dari_status' => 'dianggarkan',
                'ke_status' => 'terealisasi',
                'aksi' => 'terealisasi',
                'catatan' => 'Pekerjaan lapangan selesai dan warga menyampaikan hasil sudah dirasakan langsung.',
                'time' => $realisasiAt,
            ];
        }

        foreach ($entries as $entry) {
            $log = AspirasiLog::query()->create([
                'aspirasi_id' => $aspirasi->id,
                'dari_status' => $entry['dari_status'],
                'ke_status' => $entry['ke_status'],
                'aksi' => $entry['aksi'],
                'catatan' => $entry['catatan'],
                'user_id' => $creatorId,
            ]);

            $log->forceFill([
                'created_at' => $entry['time'],
                'updated_at' => $entry['time'],
            ])->saveQuietly();
        }
    }

    private function syncReminders(Aspirasi $aspirasi, string $status, ?int $creatorId, ?Carbon $assignedAt): void
    {
        AspirasiReminder::query()->where('aspirasi_id', $aspirasi->id)->delete();

        if (! in_array($status, ['assigned', 'input_sipd'], true) || ! $assignedAt) {
            return;
        }

        $targetUserId = $aspirasi->resolveReminderTargetUserId() ?? $creatorId;

        if (! $targetUserId) {
            return;
        }

        $days = $assignedAt->diffInDays(now());
        $time = (clone $assignedAt)->addDays(min(max($days - 5, 2), 18));

        $reminder = AspirasiReminder::query()->create([
            'aspirasi_id' => $aspirasi->id,
            'target_user_id' => $targetUserId,
            'channel' => 'system',
            'pesan' => "Tindak lanjuti aspirasi '{$aspirasi->judul}' yang sudah {$days} hari berada pada tahap penugasan/input SIPD.",
            'is_read' => $status === 'input_sipd',
        ]);

        $reminder->forceFill([
            'created_at' => $time,
            'updated_at' => $time,
        ])->saveQuietly();
    }
}
