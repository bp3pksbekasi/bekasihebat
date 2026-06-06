<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('korwes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('target_wilayah_id');
            $table->foreign('target_wilayah_id')->references('id')->on('target_wilayahs')->onDelete('cascade');
            $table->string('nomor_rw');
            $table->string('nama_koordinator')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('status')->default('belum');
            $table->text('catatan')->nullable();
            $table->date('tanggal_terbentuk')->nullable();

            if (Schema::getColumnType('users', 'id') === 'bigint') {
                $table->unsignedBigInteger('created_by')->nullable();
            } else {
                $table->uuid('created_by')->nullable();
            }

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['target_wilayah_id', 'nomor_rw']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('korwes');
    }
};
