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
        Schema::table('profil_rws', function (Blueprint $table) {
            $table->string('partai_dominan')->nullable();
            $table->string('afiliasi_ketua_rw')->nullable();
            $table->string('afiliasi_mayoritas_rt')->nullable();
            $table->string('afiliasi_tomas')->nullable();
            $table->string('afiliasi_toga')->nullable();
            $table->string('afiliasi_pemuda')->nullable();
        });

        Schema::table('rw_profile_submissions', function (Blueprint $table) {
            $table->string('partai_dominan')->nullable();
            $table->string('afiliasi_ketua_rw')->nullable();
            $table->string('afiliasi_mayoritas_rt')->nullable();
            $table->string('afiliasi_tomas')->nullable();
            $table->string('afiliasi_toga')->nullable();
            $table->string('afiliasi_pemuda')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_rws', function (Blueprint $table) {
            $table->dropColumn([
                'partai_dominan',
                'afiliasi_ketua_rw',
                'afiliasi_mayoritas_rt',
                'afiliasi_tomas',
                'afiliasi_toga',
                'afiliasi_pemuda',
            ]);
        });

        Schema::table('rw_profile_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'partai_dominan',
                'afiliasi_ketua_rw',
                'afiliasi_mayoritas_rt',
                'afiliasi_tomas',
                'afiliasi_toga',
                'afiliasi_pemuda',
            ]);
        });
    }
};
