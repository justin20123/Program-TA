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
        Schema::create('vendors_has_penggunas', function (Blueprint $table) {
            $table->unsignedBigInteger('vendors_id');
            $table->string('penggunas_email', 30);
            $table->string('penggunas_id', 30);
            $table->foreign('vendors_id')->references('id')->on('vendors');
            $table->foreign('penggunas_email')->references('email')->on('penggunas');
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
        Schema::table('vendors_has_penggunas', function (Blueprint $table) {
            $table->dropForeign(['vendors_id']);
        });
        Schema::dropIfExists('vendors_has_penggunas');
    }
};
