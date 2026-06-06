<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatan_rws', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('target_wilayah_id');
            $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->onDelete('cascade');
            $table->string('dapil');
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('nomor_rw');
            $table->string('jenis_kegiatan');
            $table->dateTime('tanggal_kegiatan');
            $table->string('pelaksana');
            $table->integer('jumlah_warga')->default(0);
            $table->text('catatan')->nullable();
            $table->json('foto')->nullable();
            $table->text('tokoh_ditemui')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->date('jadwal_berikutnya')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['target_wilayah_id', 'nomor_rw']);
            $table->index(['dapil', 'kecamatan', 'desa']);
            $table->index(['jenis_kegiatan']);
            $table->index(['tanggal_kegiatan']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan_rws');
    }
};
