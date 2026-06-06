<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribusi_materis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('materi_digital_id');
            $table->foreign('materi_digital_id')->references('id')->on('materi_digitals')->onDelete('cascade');
            $table->string('channel');
            $table->string('target_dapil')->nullable();
            $table->integer('target_rw_count')->default(0);
            $table->integer('terkirim')->default(0);
            $table->integer('terbaca')->default(0);
            $table->date('tanggal_distribusi');
            $table->text('catatan')->nullable();

            if (Schema::getColumnType('users', 'id') === 'bigint') {
                $table->unsignedBigInteger('created_by')->nullable();
            } else {
                $table->uuid('created_by')->nullable();
            }

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['materi_digital_id']);
            $table->index(['tanggal_distribusi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribusi_materis');
    }
};
