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
        Schema::table('rw_profile_submissions', function (Blueprint $table) {
            $table->dropColumn(['upa_rw_terbentuk', 'nama_ketua_upa', 'no_hp_ketua_upa', 'tipologi_warga']);

            $table->string('tipologi')->nullable();
            $table->string('ekonomi_dominan')->nullable();
            $table->text('profil_warga')->nullable();
            $table->integer('suara_pks_2019')->nullable();
            $table->text('faktor_penyebab')->nullable();
            $table->text('anggota_pks')->nullable();
            $table->integer('jumlah_kta')->nullable();
            $table->string('upa_rw_status')->nullable();
            $table->string('upa_rw_nama')->nullable();
            $table->string('rki_status')->nullable();
            $table->string('rki_nama')->nullable();
            $table->string('senam_status')->nullable();
            $table->string('senam_nama')->nullable();
            $table->string('relawan_milenial_status')->nullable();
            $table->string('relawan_milenial_nama')->nullable();
            $table->boolean('caleg_terpilih_ada')->nullable();
            $table->string('caleg_terpilih_nama')->nullable();
            $table->text('afiliasi_rw_rt')->nullable();
            $table->text('afiliasi_posyandu_dkm')->nullable();
            $table->string('kompetitor_status')->nullable();
            $table->string('kompetitor_detail')->nullable();
            $table->string('tim_sukses_status')->nullable();
            $table->string('tim_sukses_detail')->nullable();
            $table->text('strategi')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->text('keterangan_lain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rw_profile_submissions', function (Blueprint $table) {
            $table->string('upa_rw_terbentuk')->nullable();
            $table->string('nama_ketua_upa')->nullable();
            $table->string('no_hp_ketua_upa')->nullable();
            $table->string('tipologi_warga')->nullable();

            $table->dropColumn([
                'tipologi', 'ekonomi_dominan', 'profil_warga', 'suara_pks_2019', 'faktor_penyebab',
                'anggota_pks', 'jumlah_kta', 'upa_rw_status', 'upa_rw_nama', 'rki_status', 'rki_nama',
                'senam_status', 'senam_nama', 'relawan_milenial_status', 'relawan_milenial_nama',
                'caleg_terpilih_ada', 'caleg_terpilih_nama', 'afiliasi_rw_rt', 'afiliasi_posyandu_dkm',
                'kompetitor_status', 'kompetitor_detail', 'tim_sukses_status', 'tim_sukses_detail',
                'strategi', 'penanggung_jawab', 'keterangan_lain'
            ]);
        });
    }
};
