<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('political_parties', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('nomor_urut')->unique();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('full_name')->nullable();
            $table->string('color_hex', 7)->nullable();
            $table->boolean('is_tracked')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('political_parties');
    }
};
