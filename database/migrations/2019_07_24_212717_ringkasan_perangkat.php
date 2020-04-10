<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RingkasanPerangkat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('RingkasanPerangkat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('jam');
            $table->bigInteger('id_perangkat');
            $table->integer('n');
            $table->float('rerata');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('RingkasanPerangkat');
    }
}
