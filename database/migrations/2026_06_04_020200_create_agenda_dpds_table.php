<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_dpds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bidang_dpd_id')->nullable();
            $table->foreign('bidang_dpd_id')->references('id')->on('bidang_dpds')->nullOnDelete();
            $table->uuid('program_kerja_id')->nullable();
            $table->foreign('program_kerja_id')->references('id')->on('program_kerjas')->nullOnDelete();
            $table->string('judul');
            $table->string('jenis');
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('dapil_terkait')->nullable();
            $table->integer('peserta_target')->default(0);
            $table->integer('peserta_hadir')->default(0);
            $table->string('status')->default('dijadwalkan');
            $table->text('catatan')->nullable();
            $table->text('hasil')->nullable();
            $table->json('foto')->nullable();

            if (Schema::getColumnType('users', 'id') === 'bigint') {
                $table->unsignedBigInteger('created_by')->nullable();
            } else {
                $table->uuid('created_by')->nullable();
            }

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tanggal_mulai']);
            $table->index(['bidang_dpd_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_dpds');
    }
};
