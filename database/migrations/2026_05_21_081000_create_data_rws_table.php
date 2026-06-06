<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_rws', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('target_wilayah_id');
            $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->onDelete('cascade');
            $table->string('dapil');
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('nomor_rw');
            $table->integer('dpt')->default(0);
            $table->integer('dpt_laki')->default(0);
            $table->integer('dpt_perempuan')->default(0);
            $table->integer('gen_z')->default(0);
            $table->integer('millennial')->default(0);
            $table->integer('gen_x')->default(0);
            $table->integer('boomer')->default(0);
            $table->integer('jumlah_rt')->default(0);
            $table->integer('jumlah_tps')->default(0);
            $table->integer('estimasi_pks')->default(0);
            $table->decimal('estimasi_share', 8, 4)->default(0);
            $table->integer('estimasi_ranking')->default(0);
            $table->string('status_wilayah')->default('ZONA BERAT');
            $table->integer('prioritas_urutan')->default(5);
            $table->integer('target_suara_per_rw')->default(0);
            $table->timestamps();

            $table->unique(['target_wilayah_id', 'nomor_rw']);
            $table->index(['dapil', 'kecamatan', 'desa']);
            $table->index(['status_wilayah']);
            $table->index(['prioritas_urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_rws');
    }
};
