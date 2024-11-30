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
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idpengantar')->nullable();
            $table->foreign('idpengantar')->references('id')->on('penggunas');
            $table->integer('harga_total')->nullable();
            $table->dateTime('waktu_transaksi');
            $table->enum('opsi_pengambilan', ["diambil", "diantar"]);
            $table->decimal('longitude_pengambilan', 10, 6)->nullable();
            $table->decimal('latitude_pengambilan', 10, 6)->nullable();
            $table->dateTime('waktu_menerima_pesanan')->nullable();
            $table->dateTime('waktu_diantar')->nullable();
            $table->dateTime('waktu_tunggu_diambil')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->string('ulasan', 200);
            $table->string('catatan_antar', 200)->nullable();
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
        Schema::dropIfExists('notas');
    }
};
