<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi_digitals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('judul');
            $table->string('jenis');
            $table->text('deskripsi')->nullable();
            $table->string('file_path');
            $table->string('thumbnail')->nullable();
            $table->string('topik')->nullable();
            $table->integer('distribusi_count')->default(0);
            $table->string('status')->default('draft');

            if (Schema::getColumnType('users', 'id') === 'bigint') {
                $table->unsignedBigInteger('created_by')->nullable();
            } else {
                $table->uuid('created_by')->nullable();
            }

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status']);
            $table->index(['jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi_digitals');
    }
};
