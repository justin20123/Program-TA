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
        Schema::create('vendors_has_jenis_bahan_cetaks', function (Blueprint $table) {
            $table->unsignedBigInteger('vendors_id');
            $table->unsignedBigInteger('layanan_cetaks_id');
            $table->unsignedBigInteger('jenis_bahan_cetaks_id');
            $table->string('url_image_replace', 100)->nullable();
            $table->foreign('vendors_id')->references('id')->on('vendors');
            $table->foreign('layanan_cetaks_id')->references('id')->on('layanan_cetaks');
            $table->foreign('jenis_bahan_cetaks_id')->references('id')->on('jenis_bahan_cetaks');
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
        Schema::table('vendors_has_jenis_bahan_cetaks', function (Blueprint $table) {
            $table->dropForeign(['vendors_id']);
            $table->dropForeign(['layanan_cetaks_id']);
            $table->dropForeign(['jenis_bahan_cetaks_id']);
        });
    
        Schema::dropIfExists('vendors_has_jenis_bahan_cetaks');
    }
};
