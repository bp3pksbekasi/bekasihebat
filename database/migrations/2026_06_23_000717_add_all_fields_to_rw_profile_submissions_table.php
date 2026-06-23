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
            $table->integer('gen_z')->nullable();
            $table->integer('millennial')->nullable();
            $table->integer('gen_x')->nullable();
            $table->integer('boomer')->nullable();
            $table->string('estimasi_share')->nullable();
            $table->integer('estimasi_ranking')->nullable();
            $table->string('status_wilayah')->nullable();
            $table->integer('prioritas_urutan')->nullable();
            $table->integer('target_suara_per_rw')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rw_profile_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'gen_z',
                'millennial',
                'gen_x',
                'boomer',
                'estimasi_share',
                'estimasi_ranking',
                'status_wilayah',
                'prioritas_urutan',
                'target_suara_per_rw',
            ]);
        });
    }
};
