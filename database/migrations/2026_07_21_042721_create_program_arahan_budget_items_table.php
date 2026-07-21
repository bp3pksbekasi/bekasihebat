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
        Schema::create('program_arahan_budget_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('program_arahan_id');
            $table->foreign('program_arahan_id')->references('id')->on('program_arahans')->cascadeOnDelete();
            $table->string('item');
            $table->string('kategori')->nullable();
            $table->integer('qty')->default(1);
            $table->string('satuan')->default('Pcs');
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_arahan_budget_items');
    }
};
