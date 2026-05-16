<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik', 20)->nullable()->unique();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp', 30)->nullable();
            $table->enum('status', ['aktif', 'tidak aktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('nama');
            $table->index('status');
            $table->index('jenis_kelamin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
