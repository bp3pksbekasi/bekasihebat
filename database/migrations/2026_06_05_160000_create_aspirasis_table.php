<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aspirasis', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('kategori');
            $table->string('urgensi')->default('sedang');
            $table->string('dapil');
            $table->string('kecamatan')->nullable();
            $table->string('desa')->nullable();
            $table->string('nomor_rw')->nullable();
            $table->string('alamat_detail')->nullable();
            $table->uuid('target_wilayah_id')->nullable();
            $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->nullOnDelete();
            $table->string('nama_pelapor');
            $table->string('hp_pelapor')->nullable();
            $table->string('sumber');
            $table->string('sumber_id')->nullable();
            $table->uuid('assigned_dewan_id')->nullable();
            $table->foreign('assigned_dewan_id')->references('id')->on('anggota_dewans')->nullOnDelete();
            $table->dateTime('assigned_at')->nullable();
            $table->string('nomor_pokir')->nullable();
            $table->dateTime('input_sipd_at')->nullable();
            $table->string('screenshot_sipd')->nullable();
            $table->string('status')->default('diterima');
            $table->dateTime('verified_at')->nullable();
            $table->dateTime('dianggarkan_at')->nullable();
            $table->decimal('anggaran_nominal', 15, 0)->nullable();
            $table->string('tahun_anggaran')->nullable();
            $table->dateTime('realisasi_at')->nullable();
            $table->string('foto_realisasi')->nullable();
            $table->text('draft_pokir')->nullable();
            $table->text('feedback_warga')->nullable();
            $table->boolean('notif_warga_sent')->default(false);
            $table->text('catatan_internal')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status']);
            $table->index(['dapil', 'status']);
            $table->index(['assigned_dewan_id']);
            $table->index(['kategori']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aspirasis');
    }
};
