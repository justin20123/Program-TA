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
        Schema::create('notas_progress', function (Blueprint $table) {
            $table->unsignedBigInteger('pemesanans_id');
            $table->unsignedBigInteger('notas_id');
            $table->date('tanggal_progress')->nullable();
            $table->enum('progress',['proses','menunggu verifikasi','selesai']);
            $table->string('url_ubah_file',100)->nullable();
            $table->tinyInteger('terverifikasi')->nullable();
            $table->foreign('pemesanans_id')->references('id')->on('pemesanans');
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
        Schema::table('notas_progress', function (Blueprint $table) {
            $table->dropForeign(['pemesanans_id']);
            $table->dropForeign(['notas_id']);
        });

        Schema::dropIfExists('notas_progress');
    }
};
