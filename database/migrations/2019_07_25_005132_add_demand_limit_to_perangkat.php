<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDemandLimitToPerangkat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('perangkat', function (Blueprint $table) {
            //
            $table->integer('demand_limit')->default(25);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('perangkat', function (Blueprint $table) {
            //
            $table->removeColumn('demand_limit');
        });
    }
}
