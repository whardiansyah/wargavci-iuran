<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_penghuni_id')->constrained('master_penghuni')->onDelete('cascade');
            $table->string('periode', 7);
            $table->unsignedBigInteger('jumlah_tagihan');
            $table->unsignedBigInteger('jumlah_bayar')->default(0);
            $table->bigInteger('sisa_lebih_bayar')->default(0);
            $table->date('tanggal_bayar')->nullable();
            $table->timestamps();

            $table->unique(['master_penghuni_id', 'periode'], 'pembayaran_unique_periode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
