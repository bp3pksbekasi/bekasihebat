<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rencana_aksis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('wilayah_key');
            $table->string('dapil');
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('rw');
            $table->string('status_wilayah');
            $table->integer('program_index');
            $table->string('program_nama');
            $table->string('program_kategori');
            $table->string('target');
            $table->string('deadline')->nullable();
            $table->string('pic')->nullable();
            $table->string('status_pelaksanaan')->default('belum');
            $table->text('catatan')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['wilayah_key', 'program_index']);
            $table->index(['dapil', 'status_wilayah']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rencana_aksis');
    }
};
