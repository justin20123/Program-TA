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
        Schema::create('pemesanans_has_opsi_details', function (Blueprint $table) {
            $table->unsignedBigInteger('pemesanans_id');
            $table->unsignedBigInteger('opsi_details_id');
            $table->foreign('pemesanans_id')->references('id')->on('pemesanans');
            $table->foreign('opsi_details_id')->references('id')->on('opsi_details');
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
        Schema::dropIfExists('pemesanans_has_opsi_details');
    }
};
