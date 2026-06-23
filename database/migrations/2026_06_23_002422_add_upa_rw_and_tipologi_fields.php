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
        Schema::table('data_rws', function (Blueprint $table) {
            $table->string('upa_rw_terbentuk')->nullable();
            $table->string('nama_ketua_upa')->nullable();
            $table->string('no_hp_ketua_upa')->nullable();
            $table->string('tipologi_warga')->nullable();
        });

        Schema::table('rw_profile_submissions', function (Blueprint $table) {
            $table->string('upa_rw_terbentuk')->nullable();
            $table->string('nama_ketua_upa')->nullable();
            $table->string('no_hp_ketua_upa')->nullable();
            $table->string('tipologi_warga')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_rws', function (Blueprint $table) {
            $table->dropColumn(['upa_rw_terbentuk', 'nama_ketua_upa', 'no_hp_ketua_upa', 'tipologi_warga']);
        });

        Schema::table('rw_profile_submissions', function (Blueprint $table) {
            $table->dropColumn(['upa_rw_terbentuk', 'nama_ketua_upa', 'no_hp_ketua_upa', 'tipologi_warga']);
        });
    }
};
