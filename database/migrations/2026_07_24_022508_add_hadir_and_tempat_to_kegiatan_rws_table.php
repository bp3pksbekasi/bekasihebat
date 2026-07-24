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
        Schema::table('kegiatan_rws', function (Blueprint $table) {
            $table->string('dpr_ri_hadir')->nullable();
            $table->string('dprd_prov_hadir')->nullable();
            $table->string('dprd_kab_hadir')->nullable();
            $table->string('tempat_kegiatan')->nullable();
            $table->text('keterangan_tambahan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatan_rws', function (Blueprint $table) {
            $table->dropColumn([
                'dpr_ri_hadir',
                'dprd_prov_hadir',
                'dprd_kab_hadir',
                'tempat_kegiatan',
                'keterangan_tambahan'
            ]);
        });
    }
};
