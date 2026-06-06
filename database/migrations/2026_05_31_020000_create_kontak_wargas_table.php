<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kontak_wargas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('target_wilayah_id');
            $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->onDelete('cascade');
            $table->string('dapil');
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('nomor_rw');
            $table->string('nama');
            $table->string('no_wa')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('rt')->nullable();
            $table->string('alamat')->nullable();
            $table->string('sumber')->default('manual');
            $table->uuid('penggalang_id')->nullable();
            $table->foreign('penggalang_id')->references('id')->on('penggalang_suaras')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->string('status')->default('aktif');

            if (Schema::getColumnType('users', 'id') === 'bigint') {
                $table->unsignedBigInteger('created_by')->nullable();
            } else {
                $table->uuid('created_by')->nullable();
            }

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['target_wilayah_id', 'nomor_rw']);
            $table->index(['dapil', 'kecamatan', 'desa']);
            $table->index(['no_wa']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kontak_wargas');
    }
};
