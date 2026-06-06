<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_kerjas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bidang_dpd_id');
            $table->foreign('bidang_dpd_id')->references('id')->on('bidang_dpds')->onDelete('cascade');
            $table->string('nama_program');
            $table->text('deskripsi')->nullable();
            $table->string('tahun', 4)->default('2026');
            $table->string('target_teks')->nullable();
            $table->integer('target_angka')->default(0);
            $table->integer('realisasi')->default(0);
            $table->string('satuan')->nullable();
            $table->string('periode')->nullable();
            $table->date('deadline')->nullable();
            $table->string('pic_nama')->nullable();
            $table->string('pic_hp')->nullable();
            $table->string('status')->default('belum_mulai');
            $table->integer('progress_pct')->default(0);
            $table->text('catatan')->nullable();

            if (Schema::getColumnType('users', 'id') === 'bigint') {
                $table->unsignedBigInteger('created_by')->nullable();
            } else {
                $table->uuid('created_by')->nullable();
            }

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['bidang_dpd_id', 'tahun']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_kerjas');
    }
};
