<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('titik_rkis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('target_wilayah_id');
            $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->onDelete('cascade');
            $table->string('dapil');
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('nomor_rw');
            $table->string('nama_penggerak');
            $table->string('no_hp_penggerak')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('hari_kegiatan')->nullable();
            $table->string('jam_kegiatan')->nullable();
            $table->json('jenis_kegiatan')->nullable();
            $table->integer('avg_peserta')->default(0);
            $table->string('status')->default('pembentukan');
            $table->date('tanggal_aktif')->nullable();
            $table->text('catatan')->nullable();

            if (Schema::getColumnType('users', 'id') === 'bigint') {
                $table->unsignedBigInteger('created_by')->nullable();
            } else {
                $table->uuid('created_by')->nullable();
            }

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['target_wilayah_id', 'nomor_rw']);
            $table->index(['dapil', 'kecamatan', 'desa']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('titik_rkis');
    }
};
