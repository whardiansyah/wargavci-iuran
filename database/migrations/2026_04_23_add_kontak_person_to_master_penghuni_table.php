<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_penghuni', function (Blueprint $table) {
            $table->string('kontak_person')->nullable()->after('kepala_keluarga');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_penghuni', function (Blueprint $table) {
            $table->dropColumn('kontak_person');
        });
    }
};
