<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_penghuni', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'tidak aktif'])->default('aktif')->after('status_rumah');
        });
    }

    public function down(): void
    {
        Schema::table('master_penghuni', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
