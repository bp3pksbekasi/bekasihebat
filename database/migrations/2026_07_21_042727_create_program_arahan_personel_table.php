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
        Schema::create('program_arahan_personel', function (Blueprint $table) {
            $table->id();
            $table->uuid('program_arahan_id');
            $table->foreign('program_arahan_id')->references('id')->on('program_arahans')->cascadeOnDelete();
            $table->string('infra_type'); // korwe | korte | penggalang
            $table->uuid('infra_id'); // FK polimorfik manual
            $table->timestamps();

            $table->unique(['program_arahan_id', 'infra_type', 'infra_id'], 'program_arahan_personel_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_arahan_personel');
    }
};
