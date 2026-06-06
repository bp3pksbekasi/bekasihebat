<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_sesis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('loggable_type');
            $table->uuid('loggable_id');
            $table->dateTime('tanggal_sesi');
            $table->integer('jumlah_peserta')->default(0);
            $table->string('pelaksana')->nullable();
            $table->text('catatan')->nullable();
            $table->json('foto')->nullable();

            if (Schema::getColumnType('users', 'id') === 'bigint') {
                $table->unsignedBigInteger('created_by')->nullable();
            } else {
                $table->uuid('created_by')->nullable();
            }

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['loggable_type', 'loggable_id']);
            $table->index(['tanggal_sesi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_sesis');
    }
};
