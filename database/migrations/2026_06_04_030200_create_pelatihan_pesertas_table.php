<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelatihan_pesertas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pelatihan_id');
            $table->foreign('pelatihan_id')->references('id')->on('pelatihans')->onDelete('cascade');
            $table->uuid('kader_id');
            $table->foreign('kader_id')->references('id')->on('kaders')->onDelete('cascade');
            $table->string('status')->default('terdaftar');
            $table->boolean('naik_jenjang')->default(false);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['pelatihan_id', 'kader_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelatihan_pesertas');
    }
};
