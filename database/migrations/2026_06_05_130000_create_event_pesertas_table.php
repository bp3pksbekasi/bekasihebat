<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_pesertas', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('nama');
            $table->string('no_hp')->nullable();
            $table->string('no_wa')->nullable();
            $table->string('alamat')->nullable();
            $table->string('dapil')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('desa')->nullable();
            $table->string('nomor_rw')->nullable();
            $table->string('nomor_rt')->nullable();
            $table->uuid('target_wilayah_id')->nullable();
            $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->nullOnDelete();
            $table->uuid('kader_id')->nullable();
            $table->foreign('kader_id')->references('id')->on('kaders')->nullOnDelete();
            $table->string('metode')->default('manual');
            $table->boolean('synced_sapa_warga')->default(false);
            $table->uuid('kontak_warga_id')->nullable();
            $table->foreign('kontak_warga_id')->references('id')->on('kontak_wargas')->nullOnDelete();
            $table->text('catatan')->nullable();

            if (Schema::getColumnType('users', 'id') === 'bigint') {
                $table->unsignedBigInteger('created_by')->nullable();
            } else {
                $table->uuid('created_by')->nullable();
            }

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['event_id']);
            $table->index(['target_wilayah_id', 'nomor_rw']);
            $table->index(['synced_sapa_warga']);
            $table->unique(['event_id', 'no_hp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_pesertas');
    }
};
