<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upa_rw_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('target_wilayah_id');
            $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->onDelete('cascade');
            $table->string('dapil');
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('nomor_rw');
            $table->string('nama');
            $table->string('no_hp')->nullable();
            $table->string('jabatan');
            $table->string('asal');
            $table->uuid('korwe_id')->nullable();
            $table->foreign('korwe_id')->references('id')->on('korwes')->nullOnDelete();
            $table->uuid('korte_id')->nullable();
            $table->foreign('korte_id')->references('id')->on('kortes')->nullOnDelete();
            $table->string('status')->default('aktif');
            $table->text('catatan')->nullable();

            if (Schema::getColumnType('users', 'id') === 'bigint') {
                $table->unsignedBigInteger('created_by')->nullable();
            } else {
                $table->uuid('created_by')->nullable();
            }

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['target_wilayah_id', 'nomor_rw']);
            $table->index(['dapil']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upa_rw_members');
    }
};
