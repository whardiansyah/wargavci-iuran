<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_penghuni_id')->constrained('master_penghuni')->onDelete('cascade');
            $table->string('periode', 7);
            $table->string('code', 100);
            $table->unsignedBigInteger('nilai');
            $table->enum('status_bayar', ['belum', 'sudah'])->default('belum');
            $table->timestamps();

            $table->unique(['master_penghuni_id', 'periode', 'code'], 'tagihan_unique_periode_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
