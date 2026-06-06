<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelatihans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_pelatihan');
            $table->string('jenjang_target');
            $table->string('jenis');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('dapil_terkait')->nullable();
            $table->string('instruktur')->nullable();
            $table->integer('kapasitas')->default(0);
            $table->integer('peserta_hadir')->default(0);
            $table->string('status')->default('dijadwalkan');
            $table->text('materi')->nullable();
            $table->text('catatan')->nullable();
            $table->json('foto')->nullable();

            if (Schema::getColumnType('users', 'id') === 'bigint') {
                $table->unsignedBigInteger('created_by')->nullable();
            } else {
                $table->uuid('created_by')->nullable();
            }

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['jenjang_target']);
            $table->index(['tanggal_mulai']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelatihans');
    }
};
