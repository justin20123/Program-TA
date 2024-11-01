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
        Schema::create('jenis_bahan_cetaks', function (Blueprint $table) {
            $table->id();   
            $table->string('nama', 45);
            $table->string('ukuran', 45);
            $table->string('gambar', 100);
            $table->string('deskripsi', 250);
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
    
        Schema::dropIfExists('jenis_bahan_cetaks');
    }
};
