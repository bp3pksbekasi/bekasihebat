<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class MembershipCardService
{
    public function __construct(
        private readonly WilayahService $wilayahService,
    ) {}

    public function generateQrSvg(User $user): string
    {
        $content = $user->member_number ?? 'NO-MEMBER';

        $renderer = new ImageRenderer(
            new RendererStyle(200, 1),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $svg = $writer->writeString($content);

        return preg_replace('/<\?xml[^?]+\?>/', '', $svg);
    }

    public function getCardData(User $user): array
    {
        $wilayahName = 'Belum diisi';
        $wilayahFull = 'Belum diisi';

        if ($user->kelurahan_code) {
            $wilayah = $this->wilayahService->getWilayahDetailByKelurahan($user->kelurahan_code);

            if ($wilayah) {
                $kelurahan = ucwords(strtolower((string) $wilayah->kelurahan_name));
                $kecamatan = ucwords(strtolower((string) $wilayah->kecamatan_name));
                $kabupaten = ucwords(strtolower((string) $wilayah->kabupaten_name));

                $wilayahName = $kelurahan;
                $wilayahFull = sprintf('%s, %s, %s', $kelurahan, $kecamatan, $kabupaten);
            }
        }

        return [
            'member_number' => $user->member_number ?? '-',
            'name' => $user->name,
            'wilayah' => $wilayahName,
            'wilayah_full' => $wilayahFull,
            'joined_at' => $user->created_at->translatedFormat('M Y'),
            'joined_at_full' => $user->created_at->translatedFormat('d F Y'),
            'qr_svg' => $this->generateQrSvg($user),
            // Field baru:
            'nik' => $user->nik ?? '-',
            'ttl' => $user->ttl_lengkap,
            'tanggal_lahir' => $user->ttl_tanggal?->translatedFormat('d F Y') ?? '-',
            'jenis_kelamin' => $user->jenis_kelamin_label,
            'alamat' => $user->address ?? '-',
            'foto_path' => $user->foto_path,
            'dapil' => $user->dapil ?? '-',
            'nomor_rw' => $user->nomor_rw ?? '-',
        ];
    }

    public function getProfileCompletion(User $user): array
    {
        $sections = [
            'Identitas dasar' => ! empty($user->name) && ! empty($user->email) && ! empty($user->phone),
            'Data lahir' => ! empty($user->birth_date) && ! empty($user->gender),
            'Alamat lengkap' => ! empty($user->address) && ! empty($user->kelurahan_code),
        ];

        $completed = array_keys(array_filter($sections));
        $pending = array_keys(array_filter($sections, fn ($value) => ! $value));
        $percentage = (int) round((count($completed) / count($sections)) * 100);

        return [
            'percentage' => $percentage,
            'completed' => $completed,
            'pending' => $pending,
            'is_complete' => $user->profile_completed_at !== null,
        ];
    }
}
