<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aspirasi_logs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('aspirasi_id');
            $table->foreign('aspirasi_id')->references('id')->on('aspirasis')->cascadeOnDelete();
            $table->string('dari_status')->nullable();
            $table->string('ke_status');
            $table->string('aksi');
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['aspirasi_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aspirasi_logs');
    }
};
