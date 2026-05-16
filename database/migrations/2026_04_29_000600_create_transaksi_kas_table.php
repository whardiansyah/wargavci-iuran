<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_kas', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->nullable();
            $table->string('kode', 20)->nullable();
            $table->string('deskripsi');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('kredit')->default(0);
            $table->unsignedBigInteger('debet')->default(0);
            $table->string('nomor_ref', 50)->nullable();
            $table->unsignedBigInteger('saldo')->default(0);
            $table->enum('jenis', ['saldo_awal', 'transaksi'])->default('transaksi');
            $table->unsignedTinyInteger('periode_bulan')->nullable();
            $table->unsignedSmallInteger('periode_tahun')->nullable();
            $table->timestamps();

            $table->index(['periode_tahun', 'periode_bulan']);
            $table->index(['tanggal', 'id']);
            $table->index('kode');
            $table->index('jenis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_kas');
    }
};
