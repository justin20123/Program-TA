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
        Schema::create('harga_cetaks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_bahan_cetaks');
            $table->integer('harga_satuan');
            $table->integer('jumlah_cetak_maksimum');
            $table->integer('jumlah_cetak_minimum');
            $table->enum('status_warna', ['blackwhite', 'color']);

            $table->foreign('id_bahan_cetaks')->references('id')->on('jenis_bahan_cetaks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('harga_cetaks', function (Blueprint $table) {
            $table->dropForeign(['id_bahan_cetaks']);
        });
        Schema::dropIfExists('harga_cetaks');
    }
};
