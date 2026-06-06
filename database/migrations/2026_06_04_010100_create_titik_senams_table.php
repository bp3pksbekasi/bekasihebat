<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('titik_senams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('target_wilayah_id')->nullable();
            $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->nullOnDelete();
            $table->string('dapil');
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('nama_titik');
            $table->string('instruktur');
            $table->string('no_hp_instruktur')->nullable();
            $table->string('instruktur_2')->nullable();
            $table->string('hari_senam')->nullable();
            $table->string('jam_senam')->nullable();
            $table->string('lokasi_rw')->nullable();
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

            $table->index(['dapil', 'kecamatan', 'desa']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('titik_senams');
    }
};
