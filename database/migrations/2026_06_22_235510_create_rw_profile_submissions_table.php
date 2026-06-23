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
        Schema::create('rw_profile_submissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('data_rw_id')->index();
            $table->string('dapil')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('desa')->nullable();
            $table->string('nomor_rw')->nullable();
            
            $table->string('nama_pengisi');
            $table->string('no_hp_pengisi');
            
            // Kolom update
            $table->integer('dpt')->nullable();
            $table->integer('dpt_laki')->nullable();
            $table->integer('dpt_perempuan')->nullable();
            $table->integer('jumlah_rt')->nullable();
            $table->integer('jumlah_tps')->nullable();
            $table->integer('estimasi_pks')->nullable();
            
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rw_profile_submissions');
    }
};
