<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_rws', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('target_wilayah_id');
            $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->onDelete('cascade');
            $table->string('nomor_rw');
            $table->string('dapil');
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('tipologi')->nullable();
            $table->string('ekonomi_dominan')->nullable();
            $table->text('profil_warga')->nullable();
            $table->integer('suara_pks_2019')->default(0);
            $table->text('faktor_penyebab')->nullable();
            $table->text('anggota_pks')->nullable();
            $table->integer('jumlah_kta')->default(0);
            $table->string('upa_rw_status')->default('belum');
            $table->string('upa_rw_nama')->nullable();
            $table->string('rki_status')->default('belum');
            $table->string('rki_nama')->nullable();
            $table->string('senam_status')->default('belum');
            $table->string('senam_nama')->nullable();
            $table->string('relawan_milenial_status')->default('belum');
            $table->string('relawan_milenial_nama')->nullable();
            $table->boolean('caleg_terpilih_ada')->default(false);
            $table->string('caleg_terpilih_nama')->nullable();
            $table->text('afiliasi_rw_rt')->nullable();
            $table->text('afiliasi_posyandu_dkm')->nullable();
            $table->string('kompetitor_status')->default('tidak_tahu');
            $table->text('kompetitor_detail')->nullable();
            $table->string('tim_sukses_status')->default('tidak_tahu');
            $table->text('tim_sukses_detail')->nullable();
            $table->text('strategi')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->text('keterangan_lain')->nullable();
            $table->boolean('is_complete')->default(false);
            $table->integer('completion_percent')->default(0);
            $table->foreignId('filled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('filled_at')->nullable();
            $table->timestamps();

            $table->unique(['target_wilayah_id', 'nomor_rw']);
            $table->index(['dapil', 'kecamatan', 'desa']);
            $table->index(['is_complete']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_rws');
    }
};
