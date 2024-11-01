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
        Schema::create('detail_cetaks', function (Blueprint $table) {
            $table->id();
            $table->string('value', 45);
            $table->unsignedBigInteger('jenis_bahan_cetaks_id');
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

        Schema::table('detail_cetaks', function (Blueprint $table) {
            $table->dropForeign(['jenis_bahan_cetaks_id']);
        });
        Schema::dropIfExists('detail_cetaks');
    }
};
