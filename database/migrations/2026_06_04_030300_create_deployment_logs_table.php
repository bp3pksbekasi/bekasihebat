<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deployment_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kader_id');
            $table->foreign('kader_id')->references('id')->on('kaders')->onDelete('cascade');
            $table->string('dari_dapil')->nullable();
            $table->string('dari_kecamatan')->nullable();
            $table->string('dari_desa')->nullable();
            $table->string('dari_rw')->nullable();
            $table->string('ke_dapil');
            $table->string('ke_kecamatan');
            $table->string('ke_desa');
            $table->string('ke_rw');
            $table->string('alasan')->nullable();
            $table->date('tanggal_deploy');
            $table->string('status')->default('proses');
            $table->text('catatan')->nullable();

            if (Schema::getColumnType('users', 'id') === 'bigint') {
                $table->unsignedBigInteger('created_by')->nullable();
            } else {
                $table->uuid('created_by')->nullable();
            }

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['kader_id']);
            $table->index(['ke_dapil', 'ke_kecamatan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deployment_logs');
    }
};
