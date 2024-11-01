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
            $table->dateTime('waktu_transaksi');
            $table->enum('status', ["proses", "sedang diantar", "menunggu diambil", "selesai", "dibatalkan", "menunggu pembayaran"]);
            $table->enum('opsi_pengambilan', ["diambil", "diantar"]);
            $table->string('alamat_pengambilan', 100);
            $table->date('tanggal_selesai');
            $table->string('ulasan', 200);
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
