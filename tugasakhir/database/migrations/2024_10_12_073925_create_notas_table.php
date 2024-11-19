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
            $table->integer('harga_total')->nullable();
            $table->dateTime('waktu_transaksi');
            $table->enum('status', ["proses", "sedang diantar", "menunggu diambil", "selesai", "dibatalkan", "menunggu pembayaran"]);
            $table->enum('opsi_pengambilan', ["diambil", "diantar"]);
            $table->decimal('longitude_pengambilan', 10, 6)->nullable();
            $table->decimal('latitude_pengambilan', 10, 6)->nullable();
            $table->date('tanggal_selesai')->nullable();
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
