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
        Schema::create('layanan_cetaks', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 20);
            $table->string('satuan', 10);
            $table->integer('kesetaraan_pcs');
            $table->string('url_image', 100);
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

        Schema::dropIfExists('layanan_cetaks');
    }
};
