<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('program_arahan_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('program_arahan_id');
            $table->foreign('program_arahan_id')->references('id')->on('program_arahans')->cascadeOnDelete();
            $table->text('ringkasan')->nullable();
            $table->integer('jumlah_korwe_terbentuk')->default(0);
            $table->integer('jumlah_korte_terbentuk')->default(0);
            $table->integer('jumlah_penggalang_terekrut')->default(0);
            $table->text('evaluasi')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->json('foto')->nullable();
            $table->decimal('realisasi_anggaran', 15, 2)->nullable();
            $table->string('rating')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_arahan_reports');
    }
};
