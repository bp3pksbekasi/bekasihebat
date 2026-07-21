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
        Schema::create('program_arahan_approvals', function (Blueprint $table) {
            $table->id();
            $table->uuid('program_arahan_id');
            $table->foreign('program_arahan_id')->references('id')->on('program_arahans')->cascadeOnDelete();
            $table->string('level'); // dpra | dpc | dpd
            $table->string('status')->default('pending'); // pending | approved | rejected
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->text('catatan')->nullable();
            $table->dateTime('decided_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_arahan_approvals');
    }
};
