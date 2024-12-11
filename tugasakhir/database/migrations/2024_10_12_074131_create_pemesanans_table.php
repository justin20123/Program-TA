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
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->id();
            $table->string('penggunas_email', 30);
            $table->integer('jumlah');
            $table->integer('subtotal_pesanan')->default(0);
            $table->integer('biaya_tambahan')->default(0);
            $table->string('url_file', 100);
            $table->string('catatan', 200)->nullable();
            $table->tinyInteger('perlu_verifikasi');
            $table->unsignedBigInteger('harga_cetaks_id');
            $table->unsignedBigInteger('jenis_bahan_cetaks_id');
            $table->unsignedBigInteger('vendors_id');
            $table->unsignedBigInteger('notas_id')->nullable();
            $table->foreign('penggunas_email')->references('email')->on('penggunas');
            $table->foreign('vendors_id')->references('id')->on('vendors');
            $table->foreign('jenis_bahan_cetaks_id')->references('id')->on('jenis_bahan_cetaks');
            $table->foreign('harga_cetaks_id')->references('id')->on('harga_cetaks');
            $table->foreign('notas_id')->references('id')->on('notas');
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
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->dropForeign(['notas_id']);
            $table->dropForeign(['vendors_id']);
            $table->dropForeign(['jenis_bahan_cetaks_id']);
            $table->dropForeign(['harga_cetaks_id']);
            $table->dropForeign(['penggunas_email']);
        });
        Schema::dropIfExists('pemesanans');
    }
};
