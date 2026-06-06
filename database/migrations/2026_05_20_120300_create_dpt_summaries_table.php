<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dpt_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tps_id')->constrained('tps')->cascadeOnDelete();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();

            $table->unsignedInteger('total_dpt')->default(0);
            $table->unsignedInteger('male')->default(0);
            $table->unsignedInteger('female')->default(0);

            $table->unsignedInteger('gen_z')->default(0);
            $table->unsignedInteger('millennial')->default(0);
            $table->unsignedInteger('gen_x')->default(0);
            $table->unsignedInteger('boomer')->default(0);
            $table->unsignedInteger('age_known')->default(0);
            $table->unsignedInteger('age_unknown')->default(0);

            $table->timestamps();

            $table->index(['tps_id', 'rt', 'rw']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dpt_summaries');
    }
};
