<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventReport extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * Jenis dokumen pendukung laporan kegiatan.
     *
     * @var array<string, string>
     */
    public const DOKUMEN_JENIS = [
        'lpj_manual' => 'LPJ Manual (dibuat bidang)',
        'kwitansi' => 'Kwitansi / Nota',
        'absensi' => 'Lembar Absensi / Daftar Hadir',
        'berita_acara' => 'Berita Acara',
        'bukti_transfer' => 'Bukti Transfer',
        'surat' => 'Surat Tugas / Undangan',
        'lainnya' => 'Dokumen Lainnya',
    ];

    /**
     * Jenis dokumen yang wajib ada sebelum laporan dianggap lengkap.
     *
     * @var list<string>
     */
    public const DOKUMEN_WAJIB = ['kwitansi', 'absensi'];

    /**
     * Jenis dokumen yang slotnya selalu ditampilkan walau belum ada file.
     *
     * @var list<string>
     */
    public const DOKUMEN_SELALU_TAMPIL = ['lpj_manual', 'kwitansi', 'absensi'];

    protected $fillable = [
        'event_id',
        'ringkasan',
        'peserta_hadir',
        'evaluasi',
        'tindak_lanjut',
        'foto',
        'dokumen',
        'realisasi_anggaran',
        'rating',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'event_id' => 'integer',
            'peserta_hadir' => 'integer',
            'realisasi_anggaran' => 'decimal:2',
            'foto' => 'array',
            'dokumen' => 'array',
            'created_by' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
