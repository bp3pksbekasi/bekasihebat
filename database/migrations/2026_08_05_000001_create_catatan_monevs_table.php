<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catatan_monevs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('target_wilayah_id');
            $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->cascadeOnDelete();
            $table->string('nomor_rw')->nullable();
            $table->string('jenis_temuan'); // sisir_stagnan | korwe_korte_stagnan | penggalang_pasif | profil_belum_lengkap | lainnya
            $table->string('sumber')->default('manual'); // otomatis | manual
            $table->text('temuan');
            $table->text('tindak_lanjut')->nullable();
            $table->string('status')->default('terbuka'); // terbuka | selesai
            $table->string('level_penanggung_jawab')->nullable(); // dpra | dpc | dpd
            $table->string('pic_nama')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['target_wilayah_id', 'nomor_rw']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_monevs');
    }
};
