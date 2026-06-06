<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tps', function (Blueprint $table) {
            $table->id();
            $table->string('code', 80)->unique();
            $table->foreignId('dapil_id')->constrained('dapil')->cascadeOnDelete();
            $table->string('kecamatan_code', 7)->index();
            $table->string('kelurahan_code', 13)->index();
            $table->string('tps_number', 10);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->index(['dapil_id', 'kecamatan_code']);
            $table->index(['kelurahan_code', 'tps_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tps');
    }
};
