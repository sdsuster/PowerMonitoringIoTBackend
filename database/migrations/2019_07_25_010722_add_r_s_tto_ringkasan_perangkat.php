<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRSTtoRingkasanPerangkat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ringkasanperangkat', function (Blueprint $table) {
            //
            $table->float('rerata_r')->default(0);
            $table->float('rerata_s')->default(0);
            $table->float('rerata_t')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ringkasanperangkat', function (Blueprint $table) {
            //
            $table->removeColumn('rerata_r');
            $table->removeColumn('rerata_s');
            $table->removeColumn('rerata_t');
        });
    }
}
