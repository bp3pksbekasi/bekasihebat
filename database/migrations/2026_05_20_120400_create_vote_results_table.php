<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vote_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tps_id')->constrained('tps')->cascadeOnDelete();
            $table->foreignId('political_party_id')->constrained()->cascadeOnDelete();
            $table->foreignId('caleg_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('election_year')->default(2024);
            $table->unsignedInteger('suara')->default(0);
            $table->timestamps();

            $table->index(['election_year', 'tps_id']);
            $table->index(['election_year', 'political_party_id']);
            $table->index(['election_year', 'caleg_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vote_results');
    }
};
