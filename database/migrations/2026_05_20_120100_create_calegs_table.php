<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('calegs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('political_party_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dapil_id')->constrained('dapil')->cascadeOnDelete();
            $table->unsignedSmallInteger('nomor_urut');
            $table->string('nama');
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->timestamps();

            $table->index(['dapil_id', 'political_party_id', 'nomor_urut']);
            $table->unique(['dapil_id', 'political_party_id', 'nomor_urut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calegs');
    }
};
