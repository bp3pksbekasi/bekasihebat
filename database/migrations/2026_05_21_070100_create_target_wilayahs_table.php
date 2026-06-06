<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target_wilayahs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('dapil');
            $table->string('kecamatan');
            $table->string('desa');
            $table->integer('jumlah_rw')->default(0);
            $table->integer('jumlah_rt')->default(0);
            $table->integer('jumlah_tps')->default(0);
            $table->integer('jumlah_dpt')->default(0);
            $table->integer('suara_pks_2024')->default(0);
            $table->integer('ranking_pks')->default(0);
            $table->decimal('persentase_pks', 8, 4)->default(0);
            $table->integer('target_suara_2029')->default(0);
            $table->integer('kekurangan_suara')->default(0);
            $table->integer('target_korwe_2026')->default(0);
            $table->integer('target_korwe_2027')->default(0);
            $table->integer('target_korwe_2028')->default(0);
            $table->integer('target_korwe_2029')->default(0);
            $table->integer('target_korte_2026')->default(0);
            $table->integer('target_korte_2027')->default(0);
            $table->integer('target_korte_2028')->default(0);
            $table->integer('target_korte_2029')->default(0);
            $table->decimal('target_avg_per_rw', 10, 2)->default(0);
            $table->decimal('target_avg_per_rt', 10, 2)->default(0);
            $table->decimal('target_avg_per_tps', 10, 2)->default(0);
            $table->decimal('target_avg_per_rumah', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['dapil', 'kecamatan']);
            $table->unique(['dapil', 'kecamatan', 'desa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_wilayahs');
    }
};
