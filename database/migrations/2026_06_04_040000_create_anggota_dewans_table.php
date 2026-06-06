<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_dewans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('jabatan');
            $table->string('dapil')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('foto')->nullable();
            $table->string('instagram')->nullable();
            $table->integer('ig_followers')->default(0);
            $table->string('tiktok')->nullable();
            $table->integer('tt_followers')->default(0);
            $table->string('youtube')->nullable();
            $table->integer('yt_subscribers')->default(0);
            $table->string('twitter')->nullable();
            $table->integer('tw_followers')->default(0);
            $table->string('facebook')->nullable();
            $table->integer('fb_followers')->default(0);
            $table->integer('skor_popularitas')->default(0);
            $table->integer('target_popularitas')->default(70);
            $table->string('tim_media_nama')->nullable();
            $table->string('tim_media_hp')->nullable();
            $table->string('status')->default('aktif');
            $table->text('catatan')->nullable();

            if (Schema::getColumnType('users', 'id') === 'bigint') {
                $table->unsignedBigInteger('created_by')->nullable();
            } else {
                $table->uuid('created_by')->nullable();
            }

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['dapil']);
            $table->index(['skor_popularitas']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_dewans');
    }
};
