<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabungan_umroh', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggota')->onDelete('cascade');
            $table->date('tanggal');
            $table->unsignedBigInteger('nominal');
            $table->string('cara_setor', 50);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['anggota_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabungan_umroh');
    }
};
