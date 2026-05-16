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
        Schema::create('pencatatan_air', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_penghuni_id')->constrained('master_penghuni')->onDelete('cascade');
            $table->unsignedTinyInteger('periode_bulan');
            $table->unsignedSmallInteger('periode_tahun');
            $table->unsignedDecimal('meter_lalu', 15, 2);
            $table->unsignedDecimal('meter_kini', 15, 2);
            $table->unsignedBigInteger('total_tagihan');
            $table->timestamps();

            $table->unique(['master_penghuni_id', 'periode_bulan', 'periode_tahun'], 'pencatatan_air_unique_periode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencatatan_air');
    }
};
