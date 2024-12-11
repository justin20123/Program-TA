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
        Schema::create('opsi_details', function (Blueprint $table) {
            $table->id();
            $table->string('opsi', 45);
            $table->string('biaya_tambahan', 45);
            $table->enum('tipe',['satuan','tambahan','jumlah']);
            $table->unsignedBigInteger('detail_cetaks_id');
            $table->foreign('detail_cetaks_id')->references('id')->on('detail_cetaks');
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
        Schema::dropIfExists('opsi_details');
    }
};
