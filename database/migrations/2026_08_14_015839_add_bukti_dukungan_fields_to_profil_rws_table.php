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
            $table->string('afiliasi_pilkades_bukti')->nullable();
            $table->string('afiliasi_pkk_bukti')->nullable();
            $table->string('afiliasi_karang_taruna_bukti')->nullable();
            $table->string('afiliasi_dkm_bukti')->nullable();
            $table->string('afiliasi_tokoh_bukti')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_rws', function (Blueprint $table) {
            $table->dropColumn([
                'afiliasi_pilkades_bukti',
                'afiliasi_pkk_bukti',
                'afiliasi_karang_taruna_bukti',
                'afiliasi_dkm_bukti',
                'afiliasi_tokoh_bukti',
            ]);
        });
    }
};
