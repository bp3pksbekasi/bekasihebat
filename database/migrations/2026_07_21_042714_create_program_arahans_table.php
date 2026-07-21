<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('program_arahans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('org_level'); // dpra | dpc | dpd
            $table->uuid('bidang_dpd_id')->nullable();
            $table->foreign('bidang_dpd_id')->references('id')->on('bidang_dpds')->nullOnDelete();
            $table->uuid('target_wilayah_id');
            $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->cascadeOnDelete();
            $table->string('nomor_rw')->nullable();
            $table->string('status_wilayah_snapshot')->nullable();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('jenis_program');
            $table->integer('target_angka')->default(0);
            $table->string('satuan')->nullable();
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->string('pic_nama')->nullable();
            $table->string('pic_hp')->nullable();
            $table->string('status')->default('belum_mulai');
            $table->string('level_approval')->default('dpra');
            $table->string('funding_source')->nullable();
            $table->text('budget_notes')->nullable();
            $table->string('cover_image')->nullable();

            if (Schema::getColumnType('users', 'id') === 'bigint') {
                $table->unsignedBigInteger('created_by')->nullable();
            } else {
                $table->uuid('created_by')->nullable();
            }
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['target_wilayah_id', 'nomor_rw']);
            $table->index(['status_wilayah_snapshot']);
            $table->index(['status']);
            $table->index(['jenis_program']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_arahans');
    }
};
