<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_sisa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_penghuni_id');
            $table->bigInteger('sisa_lebih_bayar')->default(0);
            $table->string('periode', 7);
            $table->timestamps();

            $table->foreign('master_penghuni_id')->references('id')->on('master_penghuni')->cascadeOnDelete();
            $table->unique(['master_penghuni_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_sisa');
    }
};
