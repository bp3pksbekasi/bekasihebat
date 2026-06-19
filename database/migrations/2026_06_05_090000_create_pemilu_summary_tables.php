<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pemilu_periods')) {
            Schema::create('pemilu_periods', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->unsignedSmallInteger('tahun');
                $table->string('label');
                $table->string('slug')->unique();
                $table->string('jenis', 50)->default('dprd');
                $table->string('status', 30)->default('published');
                $table->boolean('is_default')->default(false);
                $table->json('source_meta')->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->unique(['tahun', 'jenis']);
                $table->index(['jenis', 'is_default']);
            });
        }

        if (! Schema::hasTable('pemilu_desa_summaries')) {
            Schema::create('pemilu_desa_summaries', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->uuid('pemilu_period_id');
                $table->string('dapil', 40);
                $table->string('kecamatan', 120);
                $table->string('desa', 120);
                $table->string('scope_key');
                $table->unsignedInteger('total_dpt')->default(0);
                $table->unsignedInteger('total_laki')->default(0);
                $table->unsignedInteger('total_perempuan')->default(0);
                $table->unsignedInteger('gen_z')->default(0);
                $table->unsignedInteger('millennial')->default(0);
                $table->unsignedInteger('gen_x')->default(0);
                $table->unsignedInteger('boomer')->default(0);
                $table->unsignedInteger('age_unknown')->default(0);
                $table->unsignedSmallInteger('total_tps')->default(0);
                $table->unsignedSmallInteger('total_rw')->default(0);
                $table->unsignedSmallInteger('total_rt')->default(0);
                $table->unsignedInteger('total_votes')->default(0);
                $table->unsignedInteger('pks_votes')->default(0);
                $table->unsignedInteger('pks_party_votes')->default(0);
                $table->unsignedInteger('pks_candidate_votes')->default(0);
                $table->decimal('pks_share', 8, 6)->default(0);
                $table->unsignedSmallInteger('pks_rank')->default(99);
                $table->decimal('pks_gap_share', 8, 6)->default(0);
                $table->string('status_wilayah', 40)->default('ZONA BERAT');
                $table->unsignedSmallInteger('estimated_seats')->default(0);
                $table->json('party_rows')->nullable();
                $table->json('top_candidates')->nullable();
                $table->json('tps_rows')->nullable();
                $table->json('rw_rows')->nullable();
                $table->json('rt_rows')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->foreign('pemilu_period_id')
                    ->references('id')
                    ->on('pemilu_periods')
                    ->cascadeOnDelete();

                $table->unique(['pemilu_period_id', 'dapil', 'kecamatan', 'desa'], 'pds_period_dapil_kec_desa_unique');
                $table->index(['pemilu_period_id', 'dapil']);
                $table->index(['pemilu_period_id', 'kecamatan']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pemilu_desa_summaries');
        Schema::dropIfExists('pemilu_periods');
    }
};
