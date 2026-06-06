<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konten_medsos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('anggota_dewan_id');
            $table->foreign('anggota_dewan_id')->references('id')->on('anggota_dewans')->onDelete('cascade');
            $table->string('platform');
            $table->string('jenis_konten');
            $table->text('caption')->nullable();
            $table->string('url')->nullable();
            $table->date('tanggal_posting');
            $table->integer('likes')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('shares')->default(0);
            $table->integer('views')->default(0);
            $table->string('topik')->nullable();
            $table->string('dapil_terkait')->nullable();
            $table->string('rw_terkait')->nullable();
            $table->string('desa_terkait')->nullable();
            $table->boolean('is_video_pelayanan')->default(false);
            $table->text('catatan')->nullable();

            if (Schema::getColumnType('users', 'id') === 'bigint') {
                $table->unsignedBigInteger('created_by')->nullable();
            } else {
                $table->uuid('created_by')->nullable();
            }

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['anggota_dewan_id', 'tanggal_posting']);
            $table->index(['platform']);
            $table->index(['is_video_pelayanan']);
            $table->index(['tanggal_posting']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konten_medsos');
    }
};
