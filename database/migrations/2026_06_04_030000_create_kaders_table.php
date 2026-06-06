<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kaders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('no_hp')->nullable();
            $table->string('no_wa')->nullable();
            $table->string('email')->nullable();
            $table->string('nik')->nullable();
            $table->string('no_kta')->nullable();
            $table->string('jenjang')->default('penggerak');
            $table->date('tanggal_jenjang')->nullable();
            $table->string('dapil')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('desa')->nullable();
            $table->string('nomor_rw')->nullable();
            $table->string('nomor_rt')->nullable();
            $table->uuid('target_wilayah_id')->nullable();
            $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->nullOnDelete();
            $table->boolean('is_korwe')->default(false);
            $table->boolean('is_korte')->default(false);
            $table->boolean('is_upa')->default(false);
            $table->string('jabatan_upa')->nullable();
            $table->boolean('is_penggalang')->default(false);
            $table->boolean('is_saksi')->default(false);
            $table->json('keahlian')->nullable();
            $table->boolean('bisa_deploy')->default(true);
            $table->string('status')->default('aktif');
            $table->text('catatan')->nullable();

            if (Schema::getColumnType('users', 'id') === 'bigint') {
                $table->unsignedBigInteger('created_by')->nullable();
            } else {
                $table->uuid('created_by')->nullable();
            }

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['dapil', 'kecamatan', 'desa']);
            $table->index(['jenjang']);
            $table->index(['status']);
            $table->index(['target_wilayah_id', 'nomor_rw']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kaders');
    }
};
