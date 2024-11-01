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
        Schema::create('penggunas', function (Blueprint $table) {
            $table->id();
            $table->string('email', 30)->unique();
            $table->string('password', 225);
            $table->string('nama', 30);
            $table->enum('role', ['admin', 'pemesan', 'manajer','pegawai','pengantar']); 
            $table->decimal('saldo', 10, 2);
            $table->string('nomor_telepon', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penggunas');
    }
};
