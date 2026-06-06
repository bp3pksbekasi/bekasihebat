<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aspirasi_reminders', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('aspirasi_id');
            $table->foreign('aspirasi_id')->references('id')->on('aspirasis')->cascadeOnDelete();
            $table->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('channel')->default('system');
            $table->text('pesan');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['target_user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aspirasi_reminders');
    }
};
